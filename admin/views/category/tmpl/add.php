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

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$css_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $css_dir .'/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/js/ckeditor.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal" >   
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tracking" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="form_box_wrapper well" id="add_category">
        	<div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_NAME_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', true); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_PUBLISHED_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->radioButton('published'); ?></div>
            </div>
            <div class="control-group">
            	<div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_PARENT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_PARENT_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->categories_tree; ?></div>
            </div>                
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_DESCRIPTION_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageTextAreaField('description', NULL); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_IMAGE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_IMAGE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->imageFileChooser(); ?></div>
             </div>
             <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_META_TITLE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_TITLE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_title', true); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_META_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_DESCRIPTION_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_description', true); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_META_KEYWORDS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_KEYWORDS_LABEL'); ?></label></div>
            	<div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_keywords', true); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_LINK_REWRITE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LINK_REWRITE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('link_rewrite', true); ?></div>
            </div>
            <?php if(JeproshopSettingModelSetting::getValue('multishop_feature_active')){ ?>
            <div class="control-group" >
                <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_IS_ROOT_CATEGORY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_IS_ROOT_CATEGORY_LABEL'); ?></label> </div>
                <div class="controls" ><?php echo $this->helper->radioButton('is_root_category', 'add', null); ?></div>
            </div>
            <?php } ?>
            <?php if(JeproshopShopModelShop::isFeaturePublished()){ ?>
            <div class="control-group" >
                <div class="control-label" ><label for="jform_associated_shop" title="<?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_LABEL'); ?></div>
                <div class="controls" ><?php echo $this->associated_shop; ?></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_CATEGORY_ALLOWED_GROUP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_ALLOWED_GROUP_LABEL'); ?></label></div>
                <div class="controls" >
                	<table class="table table-bordered table-striped">
                    	<thead>
                        	<tr>
                            	<th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                                <th class="nowrap center" width="1%"><?php echo JText::_('JID'); ?></th>
                                <th class="nowrap " ><?php echo JText::_('COM_JEPROSHOP_GROUP_NAME_LABEL'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if(empty($this->groups)){ ?>
                            <tr><td colspan="3" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                            <?php } else {     
                            	foreach($this->groups as $index => $group){ 
                                	//$group_link = JRoute::_('index.php?option=com_jeproshop&view=feature&task=edit&feature_id=' . $feature->feature_id .'&' . JSession::getFormToken() .'=1');
                                ?>
                            <tr class="row_<?php echo $index % 2; ?>" >
                            	<td class="nowrap center" width="1%" ><?php echo JHtml::_('grid.id', $index, $group->group_id); ?></td>
                                <td class="nowrap center" width="1%" ><?php echo $group->group_id; ?></td>
                                <td class="nowrap " width="40%" ><?php echo $group->name; ?></td>
                            </tr>
                            <?php }
                                } ?>
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
            <div class="control-group" >
                <div class="control-label"></div>
                <div class="controls">
                    <div class="alert alert-info">
                    	<h4><?php $allowed_groups = isset($this->allowed_groups) ? count($this->allowed_groups) : 0;
                            echo JText::_('COM_JEPROSHOP_YOU_NOW_HAVE_MESSAGE') . ' ' . ((int)($allowed_groups) + 3) . ' ' . JText::_('COM_JEPROSHOP_ALLOWED_GROUP_MESSAGE'); ?></h4><br />
                    	<p>
                        <?php echo $this->unidentified_group_information .'<br />'. $this->guest_group_information .'<br />'. $this->default_group_information .'<br />';
                        if(isset($this->allowed_groups)) {
                            foreach ($this->allowed_groups as $ag) { ?>
                                <b><?php echo $ag->name; ?></b> - <?php echo $ag->description; ?><br/>
                            <?php }
                        }?>
                        </p>
                    </div>

                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div> 
</form>

<script type="text/javascript" >
    jQuery(document).ready(function(){
        /*('#jform_categories_tree').tree("collapseAll");
        
        $('#jform_categories_tree').find(":input").each(function(){
            if($.inArray($(this).val(), selected_categories) !== -1){
                $(this).prop("checked", true);
                $(this).parent().addClass("tree_selected");
                $(this).parents('ul.tree').each(function(){
                    $(this).show();
                    $(this).prev().find('.icon_folder_close').removeClass('.icon_folder_close').addClass('.icon_folder_open');
                });
            }
        });
        
        $('#jform_image_select_button').click(function(){
            $('#jform_image').trigger('click');
        });
        
        $('#jform_image_name').click(function(event){ $('#jform_image').trigger('click');});
        $('#jform_image_name').on('dragenter', function(e){ e.stopPropagation(); e.preventDefault(); });
        $('#jform_image_name').on('dragover', function(e){ e.stopPropagation(); e.preventDefault(); });
        $('#jform_image_name').on('drop', function(e){ 
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            $('#jform_image')[0].files = files;
            $(this).val(files[0].name);
        });
        
        $('#jform_image').change(function(e){
            if($(this)[0].files !== 'undefined'){
                var files = $(this)[0].files;
                var name = '';
                $.each(files, function(index, value){  name += value.name + ', '; }
                $('#jform_image_name').val(name.slice(0, -2));
            }else {
                //Internet Explorer 9 Compatibility
                var name = $(this).val().split(/[\\/]/);
                $('#jform_image_name').val(name[name.length - 1]);
            }
        });
        
        if (typeof image_max_files !== 'undefined'){
            $('#jform_image').closest('form').on('submit', function(e) {
                if ($('#jform_image')[0].files.length > image_max_files) {
                    e.preventDefault();
                    alert('<?php echo JText::_('COM_JEPROSHOP_CAN_UPLOAD_A_MAXIMUM_MESSAGE'); ?>');
                }
            });
	}
        
        function tagifyField(field_id){
            $('#' + field_id).tagify({delimiters:[13, 44], addTagPrompt: '<?php echo JText::_('COM_JEPROSHOP_ADD_TAG_LBEL'); ?>'});
            $('#adminForm').submit(function(){
                $(this).find('#' + field_id).val($('#' + field_id).tagify('serialize'));
            });
        } */
    });    
</script>