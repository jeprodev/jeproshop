<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeproshop
 * @link            http://jeprodev.net

 * @copyright (C)   2009 - 2011
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of,
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JeproshopUploader
{
	const DEFAULT_MAX_SIZE = 10485760;
	
	private $_accept_types;
	private $_files;
	private $_max_size;
	private $_name;
	private $_save_path;
	
	public function __construct($name = null){
		$this->setName($name);
		$this->_files = array();
	}
	
	public function setAcceptTypes($value){
		$this->_accept_types = $value;
		return $this;
	}
	
	public function getAcceptTypes(){
		return $this->_accept_types;
	}
	
	public function getFilePath($file_name = null){
		if (!isset($file_name)){
			return tempnam($this->getSavePath(), $this->getUniqueFileName());
		}
		return $this->getSavePath().$file_name;
	}
	
	public function getFiles(){
		if (!isset($this->_files)){
			$this->_files = array();
		}
		return $this->_files;
	}
	
	public function setMaxSize($value){
		$this->_max_size = intval($value);
		return $this;
	}
	
	public function getMaxSize(){
		if (!isset($this->_max_size)){
			$this->setMaxSize(self::DEFAULT_MAX_SIZE);
		}
		return $this->_max_size;
	}
	
	public function setName($value){
		$this->_name = $value;
		return $this;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setSavePath($value){
		$this->_save_path = $value;
		return $this;
	}
	
	public function getPostMaxSizeBytes() {
		$post_max_size = ini_get('post_max_size');
		$bytes         = trim($post_max_size);
		$last          = strtolower($post_max_size[strlen($post_max_size) - 1]);
	
		switch ($last){
			case 'g': $bytes *= 1024;
			case 'm': $bytes *= 1024;
			case 'k': $bytes *= 1024;
		}
	
		if ($bytes == '')
			$bytes = null;
	
		return $bytes;
	}
	
	public function getSavePath(){
		if (!isset($this->_save_path)){
			$this->setSavePath(COM_JEPROSHOP_UPLOAD_DIRECTORY);
		}
		return $this->normalizeDirectory($this->_save_path);
	}
	
	public function getUniqueFileName($prefix = 'JEPROSHOP'){
		return uniqid($prefix, true);
	}
	
	public function process($destination = null)
	{
		$upload = isset($_FILES[$this->getName()]) ? $_FILES[$this->getName()] : null;
	
		if ($upload && is_array($upload['tmp_name']))
		{
			$tmp = array();
	
			foreach ($upload['tmp_name'] as $index => $value)
			{	$tmp[$index] = array(
					'tmp_name' => $upload['tmp_name'][$index],
					'name'     => $upload['name'][$index],
					'size'     => $upload['size'][$index],
					'type'     => $upload['type'][$index],
					'error'    => $upload['error'][$index]
			);
	
			$this->files[] = $this->upload($tmp[$index], $destination);
			}
		}
		elseif ($upload)
		{
	
			$this->files[] = $this->upload($upload, $destination);
		}
	
		return $this->files;
	}
	
	public function upload($file, $destination = null)
	{
		if ($this->validate($file))
		{
			if (isset($destination) && is_dir($destination))
				$file_path = $destination;
			else
				$file_path = $this->getFilePath(isset($destination) ? $destination : $file['name']);
	
			if ($file['tmp_name'] && is_uploaded_file($file['tmp_name'] ))
				move_uploaded_file($file['tmp_name'] , $file_path);
			else
				// Non-multi part uploads (PUT method support)
				file_put_contents($file_path, fopen('php://input', 'r'));
				
			$file_size = $this->_getFileSize($file_path, true);
	
			if ($file_size === $file['size'])
			{
				$file['save_path'] = $file_path;
			}else{
				$file['size'] = $file_size;
				unlink($file_path);
				$file['error'] = JError::raiseError(500, 'Server file size is different from local file size');
			}
		}
	
		return $file;
	}
	
	protected function checkUploadError($error_code){
		$error = 0;
		switch ($error_code){
			case 1:
				$error = JError::raiseError(500, sprintf('The uploaded file exceeds %s', ini_get('post_max_size')));
				break;
			case 2:
				$error = JError::raiseError(500, sprintf('The uploaded file exceeds %s', JeproshopTools::formatBytes((int)$_POST['MAX_FILE_SIZE'])));
				break;
			case 3:
				$error = JError::raiseError(500, 'The uploaded file was only partially uploaded');
				break;
			case 4:
				$error = JError::raiseError(500, 'No file was uploaded');
				break;
			case 6:
				$error = JError::raiseError(500, 'Missing temporary folder');
				break;
			case 7:
				$error = JError::raiseError(500, 'Failed to write file to disk');
				break;
			case 8:
				$error = JError::raiseError(500, 'A PHP extension stopped the file upload');
				break;
			default;
			break;
		}
		return $error;
	}
	
	protected function validate(&$file)
	{
		$file['error'] = $this->checkUploadError($file['error']);
	
		$post_max_size = $this->getPostMaxSizeBytes();
	
		if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size))
		{
			$file['error'] = JError::raiseError(500, 'The uploaded file exceeds the post_max_size directive in php.ini');
			return false;
		}
	
		if (preg_match('/\%00/', $file['name']))
		{
			$file['error'] = JError::raiseError(500, 'Invalid file name');
			return false;
		}
	
		$types = $this->getAcceptTypes();
	
		//TODO check mime type.
		if (isset($types) && !in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $types))
		{
			$file['error'] = JError::raiseError(500, 'Filetype not allowed');
			return false;
		}
	
		if ($file['size'] > $this->getMaxSize())
		{
			$file['error'] = JError::raiseError(500, 'File is too big');
			return false;
		}
	
		return true;
	}
	
	protected function getFileSize($file_path, $clear_stat_cache = false) {
		if ($clear_stat_cache)
			clearstatcache(true, $file_path);
	
		return filesize($file_path);
	}
	
	protected function getServerVars($var){
		return (isset($_SERVER[$var]) ? $_SERVER[$var] : '');
	}
	
	protected function normalizeDirectory($directory){
		$last = $directory[strlen($directory) - 1];
	
		if (in_array($last, array('/', '\\'))) {
			$directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
			return $directory;
		}
	
		$directory .= DIRECTORY_SEPARATOR;
		return $directory;
	}
}

/** -------- UPLOADER HELPER ------ ***/
class JeproshopFileUploader extends JeproshopUploader
{
	const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/uploader';
	const DEFAULT_TEMPLATE           = 'simple';
	const DEFAULT_AJAX_TEMPLATE      = 'ajax';

	const TYPE_IMAGE                 = 'image';
	const TYPE_FILE                  = 'file';

	private   $_context;
	private   $_drop_zone;
	private   $_id;
	private   $_files;
	private   $_name;
	private   $_max_files;
	private   $_multiple;
	private   $_post_max_size;
	protected $_template;
	private   $_template_directory;
	private   $_title;
	private   $_url;
	private   $_use_ajax;

	public function setContext($value){
		$this->_context = $value;
		return $this;
	}

	public function getContext(){
		if (!isset($this->_context)){
			$this->_context = JeproshopContext::getContext();
		}
		return $this->_context;
	}

	public function setDropZone($value)
	{
		$this->_drop_zone = $value;
		return $this;
	}

	public function getDropZone(){
		if (!isset($this->_drop_zone))
			$this->setDropZone("$('#".$this->getId()."-add-button')");

		return $this->_drop_zone;
	}

	public function setId($value){
		$this->_id = (string)$value;
		return $this;
	}

	public function getId(){
		if (!isset($this->_id) || trim($this->_id) === '')
			$this->_id = $this->getName();

		return $this->_id;
	}

	public function setFiles($value)
	{
		$this->_files = $value;
		return $this;
	}

	public function getFiles(){
		if (!isset($this->_files))
			$this->_files = array();

		return $this->_files;
	}

	public function setMaxFiles($value)
	{
		$this->_max_files = isset($value) ? intval($value) : $value;
		return $this;
	}

	public function getMaxFiles()
	{
		return $this->_max_files;
	}

	public function setMultiple($value)
	{
		$this->_multiple = (bool)$value;
		return $this;
	}

	public function setName($value)
	{
		$this->_name = (string)$value;
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setPostMaxSize($value){
		$this->_post_max_size = $value;
		return $this;
	}

	public function getPostMaxSize(){
		if (!isset($this->_post_max_size))
			$this->_post_max_size = parent::getPostMaxSize();

		return $this->_post_max_size;
	}

	public function setTemplate($value)
	{
		$this->_template = $value;
		return $this;
	}

	public function getTemplate(){
		if (!isset($this->_template)){
			$this->setTemplate(self::DEFAULT_TEMPLATE);
		}
		return $this->_template;
	}

	public function setTemplateDirectory($value){
		$this->_template_directory = $value;
		return $this;
	}

	public function getTemplateDirectory(){
		if (!isset($this->_template_directory))
			$this->_template_directory = self::DEFAULT_TEMPLATE_DIRECTORY;

		return $this->normalizeDirectory($this->_template_directory);
	}

	public function getTemplateFile($template){
		if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== FALSE)
			$controllerName = strtolower($matches[0][1]);

		if ($this->getContext()->controller instanceof ModuleAdminController && file_exists($this->_normalizeDirectory(
				$this->getContext()->controller->getTemplatePath()).$this->getTemplateDirectory().$template))
			return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath())
				.$this->getTemplateDirectory().$template;
		else if ($this->getContext()->controller instanceof AdminController && isset($controllerName)
			&& file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
				.DIRECTORY_SEPARATOR.$controllerName.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template))
			return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
				.DIRECTORY_SEPARATOR.$controllerName.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template;
		else if (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
				.$this->getTemplateDirectory().$template))
			return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
					.$this->getTemplateDirectory().$template;
		else if (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
				.$this->getTemplateDirectory().$template))
			return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
				.$this->getTemplateDirectory().$template;
		else
			return $this->getTemplateDirectory().$template;
	}

	public function setTitle($value){
		$this->_title = $value;
		return $this;
	}

	public function getTitle(){
		return $this->_title;
	}

	public function setUrl($value){
		$this->_url = (string)$value;
		return $this;
	}

	public function getUrl(){
		return $this->_url;
	}

	public function setUseAjax($value){
		$this->_use_ajax = (bool)$value;
		return $this;
	}

	public function isMultiple(){
		return (isset($this->_multiple) && $this->_multiple);
	}

	public function render(){
		$admin_webpath = str_ireplace(JPATH_SITE, '', JPATH_ADMINISTRATOR);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$bo_theme = ((JeproshopTools::isLoadedObject($this->getContext()->employee, 'employee_id')
			&& $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

		/*if (!file_exists(COM_JEPROSHOP_BO_ALL_THEMES_DIR . $bo_theme . DIRECTORY_SEPARATOR .'template'))
			$bo_theme = 'default'; * /

		$this->getContext()->controller->addJs('jquery/jquery.iframe-transport.js');
		$this->getContext()->controller->addJs('jquery/jquery.fileupload.js');
		$this->getContext()->controller->addJs('jquery/jquery.fileupload-process.js');
		$this->getContext()->controller->addJs('jquery/jquery.fileupload-validate.js');
		$this->getContext()->controller->addJs('vendor/spin.js');
		$this->getContext()->controller->addJs('vendor/ladda.js');

		if ($this->useAjax() && !isset($this->_template))
			$this->setTemplate(self::DEFAULT_AJAX_TEMPLATE);

		/*$template = $this->getContext()->smarty->createTemplate(
			$this->getTemplateFile($this->getTemplate()), $this->getContext()->smarty
		); */

		/*$template = array(
			'id'            => $this->getId(),
			'name'          => $this->getName(),*/
		$url = $this->getUrl();
		$multiple = $this->isMultiple();
        $files = $this->getFiles();
			//'title'         => $this->getTitle(),*/
		$max_files = $this->getMaxFiles();
		$post_max_size = $this->getPostMaxSizeBytes();
		$drop_zone = $this->getDropZone();

		$script = '';
		$layout = $this->getTemplate();
		if($layout == 'ajax'){
			$script .= '<div class="form-group" style="display: none;"><div id="jform_' . $this->getId() . '_images_thumbnails" >';
			if(isset($files) && count($files) > 0){
				foreach($files as $file){
					if(isset($file->image) && $file->type == 'image'){
						$script .= '<div>' . $file->image; 
						if(isset($file->size)){ 
							$script .= '<p>' . JText::_('COM_JEPROSHOP_FILE_SIZE_LABEL') . ' ' . $file->size . 'kb</p>'; 
						}
						if(isset($file->delete_url)){
							$script .= '<p><a class="btn btn-default" href="' . $file->delete_url . '" ><i class="icon-trash"></i> ' . JText::_('COM_JEPROSHOP_DELETE_LABEL') . '</a></p>';
						}
						$script .= '</div>';
					}
				}
			}
			$script .= '</div></div>';
			if(isset($max_files) && count($files) >= $max_files){
				$script .= '<div class="row"><div class="alert alert-warning" >' . JText::_('COM_JEPROSHOP_YOU_REACHED_THE_LIMIT_LABEL') . ' ' . $max_files . JText::_('COM_JEPROSHOP_OF_FILES_TO_UPLOAD_PLEASE_REMOVE_CONTINUE_UPLOADING_LABEL'). '</div>	</div>';

                $javaScript = 'jQuery( document ).ready(function() { ';
				if(isset($files) && $files){
					$javaScript .= 'jQuery(\'#jform_' . $this->getId() . '_images_thumbnails\').parent().show();';
				}
				$javaScript .= '}); ';
			}else{
				$script .= '<div ><div ><input id="jform_' . $this->getId() . '" type="file" name="jform[' . $this->getName() .']" ';
				if(isset($url)){ $script .= ' data-url="' . $url . '"'; }
				if(isset($multiple) && $multiple){ $script .= ' multiple="multiple" '; }
				$script .= ' style="width:0px;height:0px;" /><button class="btn btn-default" data-style="expand-right" data-size="s" type="button" id="jform_' . $this->getId() . '_add_button" > <i class="icon-folder-open"></i> ';
				if(isset($multiple) && $multiple){
					$script .= JText::_('COM_JEPROSHOP_ADD_FILES_LABEL');
				}else{
					$script .= JText::_('COM_JEPROSHOP_ADD_FILE_LABEL');
				}
				$script .= '</button></div></div><div class="well" style="display:none" ><div id="jform_' . $this->getId() . '_files_list"></div> <button class="ladda-button btn btn-primary" data-style="expand-right" ';
				$script .= 'type="button" id="jform_' . $this->getId() . '_upload_button" style="display:none;" > <span class="ladda-label"><i class="icon-check"></i> ';
				if(isset($multiple) && $multiple){
					$script .= JText::_('COM_JEPROSHOP_UPLOAD_FILES_LABEL');
				}else{ 
					$script .= JText::_('COM_JEPROSHOP_UPLOAD_FILE_LABEL'); 
				}
				$script .= '</span> </button></div>	<div class="row" style="display:none"><div class="alert alert-success" id="jform_' . $this->getId() . '_success"></div></div><div class="row" style="display:none"> ';
				$script .= '<div class="alert alert-danger" id="jform' . $this->getId() . '_errors"></div></div>';

				$javaScript = ' function humanizeSize(bytes){ if (typeof bytes !== \'number\') { return \'\'; } if (bytes >= 1000000000) { return (bytes / 1000000000).toFixed(2) + \' GB\'; } 	if (bytes >= 1000000) { ';
                $javaScript .= ' return (bytes / 1000000).toFixed(2) + \' MB\'; } return (bytes / 1000).toFixed(2) + \' KB\'; }  jQuery(document).ready(function() { ';
				if(isset($multiple) && isset($max_files)){
					$javaScript .= 'var ' . $this->getId() . '_max_files = ' . $max_files - count($files);
				}
			
				if(isset($files) && $files){
					$javaScript .= '$(\'#jform_' . $this->getId() . '_images_thumbnails\').parent().show(); ';
				}
			
				$javaScript .= 'var ' . $this->getId() . '_upload_button = Ladda.create( document.querySelector(\'#jform_' . $this->getId() . '_upload_button\')); var ' . $this->getId() . '_total_files = 0; ' ;
                $javaScript .= 'jQuery(\'#jform_' . $this->getId() . '\').fileupload({ dataType: \'json\', async: false, autoUpload: false, singleFileUploads: true, ';
				if(isset($post_max_size)){ $javaScript .= 'maxFileSize: ' . $post_max_size . ', '; }
				if(isset($drop_zone)){$javaScript .= 'dropZone: ' . $drop_zone . ', '; }
				$javaScript .= ' start: function (e) {' . $this->getId() . '_upload_button.start(); jQuery(\'#jform_' . $this->getId() . '_upload_button\').unbind(\'click\');';  //Important as we bind it for every elements in add function
                $javaScript .= ' },	fail: function (e, data) { jQuery(\'#jform_' . $this->getId() . '_errors\').html(data.errorThrown.message).parent().show(); }, done: function (e, data) { if (data.result) { ';
                $javaScript .= ' if (typeof data.result.' . $this->getName() . ' !== \'undefined\') { for (var i=0; i < data.result.' . $this->getName() . '.length; i++) { if(data.result.' . $this->getName() . '[i] !== null) { ';
                $javaScript .= ' if (typeof data.result.' . $this->getName() . '[i].error !== \'undefined\' && data.result.' . $this->getName() . '[i].error != \'\') { ';
                $javaScript .= ' jQuery(\'#jform_' . $this->getId() . '_errors\').html(\'<strong>\'+data.result.' . $this->getName() . '[i].name+\'</strong> : \'+data.result.' . $this->getName() . '[i].error).parent().show(); } else { ';
                $javaScript .= ' jQuery(data.context).appendTo(jQuery(\'#jform_' .$this->getId() . '_success\')); jQuery(\'#jform_' . $this->getId() . '_success\').parent().show(); if (typeof data.result.' . $this->getName() . '[i].image !== \'undefined\'){ ';
                $javaScript .= ' var template = \'<div>\'; template += data.result.' . $this->getName() . '[i].image; if (typeof data.result.' . $this->getName() . '[i].delete_url !== \'undefined\'){ template += \'<p><a class="btn btn-default" href="\'+data.result.';
                $javaScript .= $this->getName() . '[i].delete_url+\'"><i class="icon-trash"></i> ' . JText::_('COM_JEPROSHOP_DELETE_LABEL') . '</a></p>; } template += \'</div>\'; jQuery(\'#jform_' . $this->getId() . '_images_thumbnails\').html($(\'#jform_';
                $javaScript .= $this->getId() . '_images_thumbnails\').html()+template); jQuery(\'#jform_' . $this->getId() . '_images_thumbnails\').parent().show(); } } } } } jQuery(data.context).find(\'button\').remove(); } }, }).on(\'fileuploadalways\', function (e, data) { ';
                $javaScript .= $this->getId() . '_total_files--; if(' . $this->getId() . '_total_files == 0){' . $this->getId() . '_upload_button.stop(); jQuery(\'#jform_' . $this->getId() . '_upload_button\').unbind(\'click\'); jQuery(\'#jform_' . $this->getId() . '_files_list\').parent().hide();';
                $javaScript .= '} }).on(\'fileuploadadd\', function(e, data) { if (typeof ' . $this->getId() . '_max_files !== \'undefined\') { if (' . $this->getId() . '_total_files >= ' . $this->getId() . '_max_files) { e.preventDefault(); ';
                $javaScript .= ' alert(\'' . JText::_('COM_JEPROSHOP_YOU_CAN_UPLOAD_A_MAXIMUM_OF_LABEL') . ' ' . $max_files . ' ' . JText::_('COM_JEPROSHOP_FILES_LABEL') . '\'); return; } } ';
                $javaScript .= ' data.context = jQuery\'<div/>\').addClass(\'form-group\').appendTo($(\'#jform_' . $this->getId() . '_files_list\')); var file_name = jQuery(\'<span/>\').append(\'<strong>\'+data.files[0].name+\'</strong> (\'+humanizeSize(data.files[0].size)+\')\').appendTo(data.context); ';
                $javaScript .= ' var button = jQuery(\'<button/>\').addClass(\'btn btn-default pull-right\').prop(\'type\', \'button\').html(\'<i class="icon-trash"></i> ' . JText::_('COM_JEPROSHOP_REMOVE_FILE_LABEL') . '\').appendTo(data.context).on(\'click\', function() {' . $this->getId() . '_total_files--; ';
                $javaScript .= ' data.files = null; var total_elements = jQuery(this).parent().siblings(\'div.row\').length; jQuery(this).parent().remove(); if (total_elements == 0) { jQuery(\'#jform_' . $this->getId() . '_files_list\').html(\'\').parent().hide(); } }); ';
                $javaScript .= ' jQuery(\'#jform_' . $this->getId() . '_files_list\').parent().show(); jQuery(\'#jform_' . $this->getId() . '_upload_button\').show().bind(\'click\', function () { if (data.files != null) data.submit(); }); ' . $this->getId() . '_total_files++; ';
                $javaScript .= ' }).on(\'fileuploadprocessalways\', function (e, data) { var index = data.index,	file = data.files[index]; if (file.error) { jQuery(\'#jform_' . $this->getId() . '_errors\').append(\'<div class="form-group"><strong>\'+file.name+\'</strong> (\'+humanizeSize(file.size)+\') : \'+file.error+\'</div>\').parent().show(); ';
                $javaScript .= ' jQuery(data.context).find(\'button\').trigger(\'click\'); } }); jQuery(\'#jform_' . $this->getImageSize() . '_add_button\').on(\'click\', function() { jQuery(\'#jform_' . $this->getId() . '_success\').html(\'\').parent().hide(); ';
                $javaScript .= ' jQuery(\'#jform_' . $this->getId() . '_errors\').html(\'\').parent().hide(); jQuery(\'#jform_' . $this->getId() . '_files_list\').parent().hide();  ' . $this->getId() . '_total_files = 0; jQuery(\'#jform_' . $this->getId() . '\').trigger(\'click\'); 	}); });';

                JFactory::getDocument()->addScriptDeclaration($javaScript);
			}
		}elseif($layout == 'simple'){
			if(isset($files) && count($files) > 0) {
                $show_thumbnail = false;
                foreach ($files as $file) {
                    if (isset($file->image) && $file->type == 'image') {
                        $show_thumbnail = true;
                    }
                }
                if ($show_thumbnail) {
                    $script .= '<div id="jform_image_thumbnails_' . $this->getId() . '" > ';
                    foreach ($files as $file) {
                        if (isset($file->image) && $file->type == 'image') {
                            $script .= '<div>' . $file->image;
                            if (isset($file->size)) {
                                $script .= '<p>' . JText::_('COM_JEPROSHOP_FILE_SIZE_LABEL') . ' ' . $file->size . ' kb</p>';
                            }
                            if (isset($file->delete_url)) {
                                $script .= '<p><a class="btn btn-default" href="' . $file->delete_url . '"><i class="icon-trash"></i> ' . JText::_('COM_JEPROSHOP_DELETE_LABEL') . '</a></p>';
                            }
                            $script .= '</div>';
                        }
                    }
                    $script .= '</div>';
                }
            }
			if(isset($max_files) && count($files) >= $max_files) {
                $script .= '<div class="row" ><div class="alert alert-warning" >' . JText::_('COM_JEPROSHOP_YOU_HAVE_REACHED_THE_LIMIT_MESSAGE') . $max_files . JText::_('COM_JEPROSHOP_OF_FILES_TO_UPLOAD_PLEASE_REMOVE_FILES_TO_CONTINUE_UPLOADING_MESSAGE') . '</div ></div >';
			}else{
			    $script .= '<input id="jform_' . $this->getId() . '" type="file" name="' . $this->getName() . '" ' . ((isset($multiple) && $multiple) ?  ' multiple="multiple"' : '') . ' class="hide" /> ';
                $script .= '<div class="dummyfile input-group" ><input id="jform_' . $this->getId() . '_name" type="text" name="filename" readonly />';
                $script .= '<span class="input-group-btn" ><button id="jform_' . $this->getId() . '_select_button" type="button" name="add_attachments" class="btn btn-default"><i class="icon-folder-open"></i> ';
                if(isset($multiple) && $multiple){ $script .= JText::_('COM_JEPROSHOP_ADD_FILES_LABEL'); }else{ $script .= JText::_('COM_JEPROSHOP_ADD_FILE_LABEL'); }
			    $script .= '</button>';
			    if((!isset($multiple) || !$multiple) && isset($files) && count($files) == 1 && isset($files[0]->download_url)) {
                    $script .= '<a href="' . $files[0]->download_url . '" class="btn btn-default"><i class="icon-cloud-download"></i> ';
                    if (isset($size)) {
                        $script .= JText::_('COM_JEPROSHOP_DOWNLOAD_CURRENT_FILE_LABEL') . ' (' . $size . 'kb)';
                    } else {
                        $script .= JText::_('COM_JEPROSHOP_DOWNLOAD_CURRENT_FILE_LABEL');
                    }
                    $script .= '</a>';
                }
                $script .= '</span></div>';

                $javaScript = '';
			    if(isset($multiple) && isset($max_files)) {
                    $javaScript .= 'var ' . $this->getId() . '_max_files = ' . $max_files - count($files) . '; ';
                }
			
			    $javaScript .= 'jQuery(document).ready(function(){ jQuery(\'#jform_' . $this->getId() . '_select_button\').click(function(e) { jQuery(\'#jform_' . $this->getId() . '\').trigger(\'click\'); }); ';
                $javaScript .= 'jQuery(\'#jform_' . $this->getId() . '_name\').click(function(e) { jQuery(\'#jform_' . $this->getId() . '\').trigger(\'click\'); }); ';
                $javaScript .= 'jQuery(\'#jform_' . $this->getId() . '_name\').on(\'dragenter\', function(e) { e.stopPropagation(); e.preventDefault(); }); ';
                $javaScript .= 'jQuery(\'#jform_' . $this->getId() . '_name\').on(\'dragover\', function(e) { e.stopPropagation(); 	e.preventDefault(); }); ';
                $javaScript .= 'jQuery(\'#jform_' . $this->getId() . '_name\').on(\'drop\', function(e) { e.preventDefault(); var files = e.originalEvent.dataTransfer.files; jQuery(\'#jform_' . $this->getId() . '\')[0].files = files; jQuery(this).val(files[0].name); }); ';
				$javaScript .= 'jQuery(\'#jform_' . $this->getId() . '\').change(function(e) { if (jQuery(this)[0].files !== undefined){ var files = jQuery(this)[0].files; var name  = \'\';  jQuery.each(files, function(index, value) { name += value.name + \', \'; }); ';
                $javaScript .= ' jQuery(\'#jform_' . $this->getId() . '_name\').val(name.slice(0, -2)); }else{ var name = jQuery(this).val().split(/[\\/]/); jQuery(\'#jform_' . $this->getId() . '_name\').val(name[name.length-1]); } }); ';
                $javaScript .= 'if (typeof ' . $this->getId() . '_max_files !== \'undefined\'){ jQuery(\'#jform_' . $this->getId() . '\').closest(\'form\').on(\'submit\', function(e) { if (jQuery(\'#jform_' . $this->getId() . '\')[0].files.length > ' . $this->getId() . '_max_files) { ';
                $javaScript .= ' e.preventDefault(); alert(' . JText::_('COM_JEPROSHOP_YOU_CAN_UPLOAD_A_MAXIMUM_OF_MESSAGE') . $max_files . JText::_('COM_JEPROSHOP_FILES_LABEL') . '); } }); } });';

                JFactory::getDocument()->addScriptDeclaration($javaScript);
			}
		}
		return $script;
	}

	public function useAjax(){
		return (isset($this->_use_ajax) && $this->_use_ajax);
	}
}

/** ----- IMAGE UPLOADER ------ ***/
class JeproshopImageUploader extends JeproshopFileUploader
{
	public function getMaxSize(){
		return (int)JeproshopSettingModelSetting::getValue('product_picture_max_size');
	}
	
	public function getSavePath(){
		return $this->normalizeDirectory(COM_JEPROSHOP_IMAGE_DIR);
	}
	
	public function getFilePath($file_name = null){
		//Force file path
		return tempnam($this->getSavePath(), $this->getUniqueFileName());
	}
	
	protected function validate(&$file){
		$file['error'] = $this->checkUploadError($file['error']);
	
		$post_max_size = $this->getPostMaxSizeBytes();
	
		if ($post_max_size && ($this->getServerVars('CONTENT_LENGTH') > $post_max_size))
		{
			$file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');
			return false;
		}
	
		if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes()))
		{
			$file['error'] = $error;
			return false;
		}
	
		if ($file['size'] > $this->getMaxSize())
		{
			$file['error'] = Tools::displayError('File is too big');
			return false;
		}	
		return true;
	}
}