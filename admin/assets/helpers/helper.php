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

class JeproshopHelper
{
	public function __construct() {
		$this->context = JeproshopContext::getContext();
	}
	
	public function multiLanguageInputField($fieldName, $wrapper, $required = false, $content = null, $maxLength = null, $hint = ''){
        $wrapper = $wrapper ? $wrapper : 'jform';
		if(!isset($this->languages) || !$this->languages){
			$this->languages = JeproshopLanguageModelLanguage::getLanguages();
		}
		$script = '<div class="translatable input-append" >';
		foreach ($this->languages as $language){
			$script .= '<div class="lang_' . $language->lang_id . ' input_lang" ';
			$script .= (!$language->is_default ? 'style="display:none" ' : '') . ' >';
			$script .= '<input type="text" id="jform_' . $fieldName . '_' . $language->lang_id .'" name="' . $wrapper . '[';
			$script .= $fieldName . '_' . $language->lang_id . ']" class="copy_to_friendly_url hasTooltip" ';
			$script .= 'value="' . (count($content) ? $content[$language->lang_id] : '' ). '" onKeyup="if(isArrowKey(event)) return; updateFriendlyUrl();" ';
			if($required){ $script .= 'required="required" ';}
			if($maxLength){ $script .= 'maxlength="' . (int) $maxLength . '" ';}
			$script .= '/></div><div class="btn-group-action lang-select"><div class="btn-group" >';
			$script .= '<button type="button" class="btn btn-default dropdown_toggle" tabindex="-1" data-toggle="dropdown" > ';
			$script .= $language->iso_code . '&nbsp; <i class="caret" ></i> </button><ul class="dropdown-menu" >';
			foreach($this->languages as $value){
				$script .= '<li><a href="javascript:hideOtherLanguage(' . $value->lang_id . ');" tabindex="-1" >' . $value->name . '</a></li>';
			}
			$script .= '</ul></div>';
            if($hint != ''){
                $script .= '<p class="preference_description" >' . $hint . '</p>';
            }
            $script .= '</div>';
	
		}
		$script .= '</div>';
		return $script;
	}

    public function inputAppendField($fieldName, $fieldValue, $link, $icon){
        $script = '<div class="input-append" ><input type="text" id="jform_' . $fieldName . '" name="jform[' . $fieldName . ']" value="';
        $script .= $fieldValue . '" /><a class="btn btn-primary" href="' . $link . '" ><i class="icon-' . $icon . '" ></i></a></div>';

        return $script;
    }
	
	public function radioButton($fieldName, $layout = 'add', $state = 1){
		if($layout == 'add'){
			$state_published = ' checked="checked"';
			$state_unpublished = '';
		}else{
			if($state){
				$state_published = ' checked="checked" ';
				$state_unpublished = '';
			}else{
				$state_published = '';
				$state_unpublished = ' checked="checked"';
			}
		}
		$script = '<fieldset class="btn-group radio" >';
		$script .= '<input type="radio" id="jform_' . $fieldName . '_1" name="jform[' . $fieldName . ']" value="1" ' . $state_published . ' /><label for="jform_' . $fieldName . '_1" >' . JText::_('COM_JEPROSHOP_YES_LABEL') . '</label>';
		$script .= '<input type="radio" id="jform_' . $fieldName . '_0" name="jform[' . $fieldName . ']" value="0" ' . $state_unpublished . ' /><label for="jform_' . $fieldName . '_0" >' . JText::_('COM_JEPROSHOP_NO_LABEL') . '</label>';
		$script .= '</fieldset>';
		return $script;
	}
	
	public function multiLanguageTextAreaField($fieldName, $wrapper, $content = NULL, $width = '550', $height = '100'){
        $wrapper = $wrapper ? $wrapper : 'jform';
		if(!isset($this->languages) || !$this->languages){
			$this->languages = JeproshopLanguageModelLanguage::getLanguages();
		}
		$script = '<div class="translatable" >';
		foreach($this->languages as $language){
			$script .= '<div class="lang_' . $language->lang_id . ' input_lang"' . (!$language->is_default ? 'style="display:none" ' : '') . ' >';
			$script .= '<textarea class="ckeditor" name="' . $wrapper . '[' . $fieldName . '_' . $language->lang_id . ']" id="jform_' . $fieldName . '_' . $language->lang_id . '" >';
            $script .= ($content ? $content[$language->lang_id] : '' ) . '</textarea>';
			$script .= '</div><div class="btn-group-action lang-select" ><div class="btn-group" >';
			$script .= '<button type="button" class="btn btn-default dropdown_toggle" tabindex="-1" data-toggle="dropdown" > ';
			$script .= $language->iso_code . '&nbsp; <i class="caret" ></i> </button><ul class="dropdown-menu" >';
			foreach($this->languages as $value){
				$script .= '<li><a href="jeproLang.hideOtherLanguage(' . $value->lang_id . ');" tabindex="-1" >' . $value->name . '</a></li>';
			}
			$script .= '</ul></div></div>';
			//$script .= '</div></div>';
		}
		$script .= '</div>';
		return $script;
	}
	
	public function imageFileChooser(){
		$script = '<div class="col_lg_9" ><div class="col_sm_6" >';
		$script .= '<input type="file" id="jform_name" name="jform[image]" class="hide" />';
		$script .= '<div class="input-append" ><button type="button" class="btn" ><i class="icon-file" ></i></button>';
		$script .= '<input id="jform_image_name" type="text" name="jform[filename]" readonly  class="hasTooltip" /><span class="input_group_btn">';
		$script .= '<button id="jform_image_select_button" type="button" name="jform[submit_add_attachments]" class="btn default_btn" >';
		$script .= '<i class="icon-folder-open" ></i>' . JText::_('COM_JEPROSHOP_ADD_FILE_LABEL') . '</button></span>';
		$script .= '</div></div></div>';
	
		return $script;
	}
	
	public function inputFileUploader($fieldName, $name, $files = null, $use_ajax = false, $max_files = 5, $url = ''){
        $script = '<div class="' . $fieldName . '-images-thumbnails" ' . ((count($files) <= 0 ) ? ' style="display:none" ' : '') . ' >';
        if(isset($files) && count($files)){
            foreach($files as $file){
                if(isset($file->image) && $file->type = 'image') {
                    $script .= '<div class="" ><img src="' . $file->image . '" />';
                    if(isset($file->size)){ $script .= '<p>' . JText::_('COM_JEPROSHOP_FILE_SIZE_LABEL') . ' : ' . $file->size . ' kb</p>'; }
                    if(isset($file->delete_url)) {
                        $script .= '<p><a class="btn btn-default" href="' . $file->delete_url . '" ><i class="icon-trash" ></i> ' . JText::_('COM_JEPROSHOP_DELETE_LABEL') . '</a></p>';
                    }
                    $script .= '</div>';
                }
            }
        }
        $script .= '</div>';

        $javaScript = "";
        if(isset($max_files) && count($files) >= $max_files){
            $script .= '<div class="row" ><div class="alert alert-warning" >' . JText::_('COM_JEPROSHOP_YOU_HAVE_REACHED_THE_LIMIT_LABEL') . $max_files . JText::_('COM_JEPROSHOP_OF_FILES_TO_UPLOAD_PLEASE_REMOVE_FILES_TO_CONTINUE_UPLOADING_LABEL') . '</div></div>';
        }else{
            $script .= '<div class="input-append" >';
            $javaScript  = "jQuery(document).ready(function(){ var jeproFile = new JeproshopFiles({'field_name' : '" . $fieldName . "'";
            if($use_ajax) {
                $script .= '<input type="file" id="jform_' . $fieldName . '" name="jform[' . $fieldName . ']" data-url="' . ((isset($url) && $url) ? $url : '');
                $script .= '" ' . ((isset($multiple) && $multiple) ? 'multiple="multiple" ' : '') . ' style="width:0px; height:0px" /><button class="btn btn-default" ' ;
                $script .= ' data-style="expand-right" data-size="small" type="button" id="jform_' . $fieldName . '_add_button" ><i class="icon-folder-open" ></i> ';
                $script .= ((isset($multiple) && $multiple) ? JText::_('COM_JEPROSHOP_FILES_LABEL') : JText::_('COM_JEPROSHOP_FILE_LABEL')) . '...</button><div class="well" ';
                $script .= 'style="display:none" ><div id="jform_' . $fieldName . '_files_list" ></div><button class="ladda-button btn btn-primary" data-style="expand-right" type="button"';
                $script .= ' id="jform_' . $fieldName . '_upload_button" style="display:none; " ><span class="ladda-label" ><i class="icon-check" ></i> ' . ((isset($multiple) && $multiple) ? JText::_('COM_JEPROSHOP_UPLOAD_FILES_LABEL') : JText::_('COM_JEPROSHOP_UPLOAD_FILES_LABEL'));
                $script .= '</span></button></div><div class="row" style="display: none" ><div class="alert alert-success" id="jform_' . $fieldName . '_success" ></div></div><div class="row" style="display:none" ><div class="alert alert-danger" id="jform_' . $fieldName . '_errors" ></div></div>';

                $javaScript.= "});jeproFile.ajaxUploadManager(); ";
            }else{
                $script .= '<input type="file" name="jform[' . $fieldName . ']" id="jform_' . $fieldName . '" ' . ((isset($multiple) && $multiple) ? ' multiple="multiple" ' : '') . ' value="" class="hide" />';
                $script .= '<div class="dummyfile input-append"><input id="jform_' . $fieldName . '_name" type="text" name="filename" readonly />';
                $script .= '<span class="input-group-btn"><button id="jform_' . $fieldName . '_select_button" type="button" name="submitAddAttachments" class="btn btn-default"><i class="icon-folder-open"></i> ';
                $script .= ((isset($multiple) && $multiple)?  JText::_('COM_JEPROSHOP_FILES_LABEL') : JText::_('COM_JEPROSHOP_FILE_LABEL')) . '...</button>';
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
                $javaScript.= "});jeproFile.uploadManager(); ";

            }
            $script .= '</div>';
            $javaScript .= "}) ";
        }
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/ui/jquery.ui.widget.min.js');
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/jquery.fileupload.js');
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/jquery.fileupload-process.js');
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/jquery.fileupload-validate.js');
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/ladda/spin.min.js');
        JFactory::getDocument()->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/ladda/ladda.min.js');
        JFactory::getDocument()->addScriptDeclaration($javaScript);

        return $script;
    }
}


class JeproshopCalendarHelper extends JeproshopHelper
{
    const DEFAULT_DATE_FORMAT    = 'Y-mm-dd';
    const DEFAULT_COMPARE_OPTION = 1;

    private $_actions;
    private $_compare_actions;
    private $_compare_date_from;
    private $_compare_date_to;
    private $_compare_date_option;
    private $_date_format;
    private $_date_from;
    private $_date_to;
    private $_rtl;

    public function __construct(){
        $this->base_tpl = 'calendar';
        parent::__construct();
    }

    public function setDateFrom($value){
        if (!isset($value) || $value == '')
            $value = date('Y-m-d', strtotime("-31 days"));

        if (!is_string($value))
            throw new PrestaShopException('Date must be a string');

        $this->_date_from = $value;
        return $this;
    }

    public function getDateFrom(){
        if (!isset($this->_date_from))
            $this->_date_from = date('Y-m-d', strtotime("-31 days"));

        return $this->_date_from;
    }

    public function setCompareDateFrom($value){
        $this->_compare_date_from = $value;
        return $this;
    }

    public function getCompareDateFrom(){
        return $this->_compare_date_from;
    }

    public function setCompareDateTo($value){
        $this->_compare_date_to = $value;
        return $this;
    }

    public function getCompareDateTo(){
        return $this->_compare_date_to;
    }

    public function setDateTo($value){
        if (!isset($value) || $value == '')
            $value = date('Y-m-d');

        if (!is_string($value))
            throw new JException('Date must be a string');

        $this->_date_to = $value;
        return $this;
    }

    public function setDateFormat($value){
        if (!is_string($value))
            throw new JException('Date format must be a string');

        $this->_date_format = $value;
        return $this;
    }

    public function getDateFormat(){
        if (!isset($this->_date_format))
            $this->_date_format = self::DEFAULT_DATE_FORMAT;

        return $this->_date_format;
    }

    public function getDateTo(){
        if (!isset($this->_date_to))
            $this->_date_to = date('Y-m-d');

        return $this->_date_to;
    }

    public function setRTL($value){
        $this->_rtl = (bool)$value;
        return $this;
    }

    public function isRTL(){
        if (!isset($this->_rtl))
            $this->_rtl = JeproshopContext::getContext()->language->is_rtl;

        return $this->_rtl;
    }

    public function setCompareOption($value){
        $this->_compare_date_option = (int)$value;
        return $this;
    }

    public function getCompareOption(){
        if (!isset($this->_compare_date_option))
            $this->_compare_date_option = self::DEFAULT_COMPARE_OPTION;

        return $this->_compare_date_option;
    }

    public function addAction($action){
        if (!isset($this->_actions))
            $this->_actions = array();

        $this->_actions[] = $action;

        return $this;
    }

    public function addCompareAction($action){
        if (!isset($this->_compare_actions))
            $this->_compare_actions = array();

        $this->_compare_actions[] = $action;

        return $this;
    }

    public function setActions($value) {
        if (!is_array($value) && !$value instanceof Traversable)
            throw new JException('Actions value must be an traversable array');

        $this->_actions = $value;
        return $this;
    }

    public function getActions()
    {
        if (!isset($this->_actions))
            $this->_actions = array();

        return $this->_actions;
    }

    public function setCompareActions($value)
    {
        if (!is_array($value) && !$value instanceof Traversable)
            throw new JException('Actions value must be an traversable array');

        $this->_compare_actions = $value;
        return $this;
    }

    public function getCompareActions()
    {
        if (!isset($this->_compare_actions))
            $this->_compare_actions = array();

        return $this->_compare_actions;
    }

    public function generate(){
        $context = JeproshopContext::getContext();
        $actions = $this->getActions();
        $compare_date_from = $this->getCompareDateFrom();
        $compare_date_to = $this->getCompareDateTo();
        $script = '';

        //if(1){
            $script = '<div id="jform_date_picker" class="row row-padding-top hide" ><div class="col-lg-12"><div class="date_range_picker_days"><div class="row">';
			if($this->isRtl()) {
                $script .= '<div class="col-sm-6 col-lg-4" ><div class="date_picker_2" data-date="' . $context->employee->stats_date_to . '" data-date-format="' . $this->getDateFormat() . '"></div>';
                $script .= '</div><div class="col-sm-6 col-lg-4" ><div class="date_picker_1" data-date="' . $context->employee->stats_date_from . '" data-date-format="' . $this->getDateFormat() . '" ></div></div>';
            }else {
                $script .= '<div class="col-sm-6 col-lg-4" ><div class="date_picker_1" data-date="' . $context->employee->stats_date_from . '" data-date-format="' . $this->getDateFormat() .'" ></div></div>';
				$script .= '<div class="col-sm-6 col-lg-4" ><div class="date_picker_2" data-date="' . $context->employee->stats_date_to . '" data-date-format="' . $this->getDateFormat() . '" ></div></div>';
			}

            $script .= '<div class="col-xs-12 col-sm-6 col-lg-4 pull-right" ><div id="jfom_date_picker_form" class="form-horizontal" ><div id="jform_date_range" class="form-date-group" >';
            $script .= '<div  class="form-date-heading" ><span class="title" >' . JText::_('COM_JEPROSHOP_DATE_RANGE_LABEL') . '</span>';
            if(isset($actions) && count($actions) > 0){
                if(count($actions) > 1) {
                    $script .= '<button class="btn btn-default btn-xs pull-right dropdown-toggle" data-toggle="dropdown" type="button">' . JText::_('COM_JEPROSHOP_CUSTOM_LABEL') . '<i class="icon-angle-down" ></i>';
                    $script .= '</button><ul class="dropdown-menu" >';
                    foreach ($actions as $action) {
                        $script .= '<li><a';
                        if (isset($action->class)) {
                            $script .= ' class="' . $action->class . '"';
                        }
                        $script .= ' >' . (isset($action->icon) ? '<i class="' . $action->icon . '"></i> ' : '') . $action->label . '</a></li>';
                    }
                    $script .= '</ul>';
                }else{
                    $script .= '<a' . (isset($actions[0]->href) ? ' href="' . $actions[0]->href . '"' : '') . ' class="btn btn-default btn-xs pull-right';
                    $script .= (isset($actions[0]->class) ?  $actions[0]->class : '') . '">';
                    $script .= (isset($actions[0]->icon) ? '<i class="' . $actions[0]->icon . '"></i> ' . $actions[0]->label . '</a> ' : '');
                }
            }
			$script .= '</div><div class="form-date-body"><label>' . JText::_('COM_JEPROSHOP_FROM_LABEL') . '</label><input class="date-input form-control" id="jform_date_start"';
			$script .= ' placeholder="Start" type="text" name="jform[date_from]" value="' . $context->employee->stats_date_from . '" data-date-format="' . $this->getDateFormat();
            $script .= '" tabindex="1" /><label>' . JText::_('COM_JEPROSHOP_TO_LABEL') . '</label><input class="date-input form-control" id="date-end" placeholder="End" type="text"';
            $script .= ' name="jform[date_to]" value="' . $context->employee->stats_date_to . '" data-date-format="' . $this->getDateFormat() . '" tabindex="2" /></div></div>';
            $script .= '<div id="jform_date_compare" class="form-date-group" ><div class="form-date-heading" ><span class="checkbox-title" ><label><input type="checkbox" ';
            $script .= 'id="jform_date_picker_compare" name="jform[date_picker_compare]" ' . ((isset($compare_date_from) && isset($compare_date_to)) ? ' checked="checked" ' : '') . ' tabindex="3">';
			$script .= JText::_('COM_JEPROSHOP_COMPARE_TO_LABEL') . '</label></span><select id="jform_compare_options" class="form-control fixed-width-lg pull-right" name="jform[compare_date_option]" ';
            $script .= ((is_null($compare_date_from) || is_null($compare_date_to)) ? ' disabled="disabled" ' : '') . ' ><option value="1" ' . (($this->getCompareOption() == 1) ? 'selected="selected"' : '');
            $script .= 'label="' . JText::_('COM_JEPROSHOP_PREVIOUS_PERIOD_LABEL') . '" >' . JText::_('COM_JEPROSHOP_PREVIOUS_PERIOD_LABEL') . '</option><option value="2" ';
            $script .= (($this->getCompareOption() == 2) ? ' selected="selected" ' : '') . ' label="' . JText::_('COM_JEPROSHOP_PREVIOUS_YEAR_LABEL') . '">' . JText::_('COM_JEPROSHOP_PREVIOUS_YEAR_LABEL') . '</option>';
            $script .= '<option value="3" ' . (($this->getCompareOption() == 3) ? ' selected="selected"' : '') . ' label="' . JText::_('COM_JEPROSHOP_CUSTOM_LABEL') . '">' . JText::_('COM_JEPROSHOP_CUSTOM_LABEL') . '</option>';
			$script .= '</select></div>	<div class="form-date-body" id="jform_date_body_compare"' . ((is_null($compare_date_from) || is_null($compare_date_to)) ? ' style="display: none;" ' : '') . '>';
            $script .= '<label>' . JText::_('COM_JEPROSHOP_FROM_LABEL') . '</label><input id="jform_date_start_compare" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="';
            $script .= $compare_date_from . '" data-date-format="' . $this->getDateFormat() . '" tabindex="4" /><label>' . JText::_('COM_JEPROSHOP_TO_LABEL') . '</label><input id="jform_date_end_compare" class="date-input ';
            $script .= 'form-control" type="text" placeholder="End" name="jform[compare_date_to]" value="' . $compare_date_to . '" data-date-format="' . $this->getDateFormat() . '" tabindex="5" /></div></div>';
			$script .= '<div class="form-date-actions" ><button class="btn btn-link" type="button" id="jform_date_picker_cancel" tabindex="7" ><i class="icon-remove"></i> ' . JText::_('COM_JEPROSHOP_CANCEL_LABEL');
            $script .= '</button><button class="btn btn-default pull-right" type="submit" name="submitDateRange" tabindex="6"><i class="icon-ok text-success"></i> '	. JText::_('COM_JEPROSHOP_APPLY_LABEL') . '</button></div></div></div></div></div></div></div>';
        /*}else{
            $script = '';
        }*/
echo $script;
        return $script;
    }
}