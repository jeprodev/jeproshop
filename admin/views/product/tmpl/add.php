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
$themes_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $themes_dir . '/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.framework');
if($this->check_product_association_ajax){
    $class_input_ajax = 'check_product_name';
}else{
    $class_input_ajax = '';
}
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/js/ckeditor.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/product.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" method="post" name="adminForm" id="adminForm"  class="form-horizontal">
	<div class="product_edit_form" style="width: 100%;">
		<?php if(!empty($this->sideBar)){ ?>
    	<div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    	<?php } ?>
    	<div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    		<div class="form_box_wrapper jeproshop_sub_menu_wrapper">
            	<fieldset class="btn-group">
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufaturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            	</fieldset>
        	</div>  
			<div class="product_form_wrapper" >
				<?php 
            	echo JHtml::_('bootstrap.startTabSet', 'product_form', array('active' =>'information'));
            	echo JHtml::_('bootstrap.addTab', 'product_form', 'information', JText::_('COM_JEPROSHOP_PRODUCT_INFORMATION_TAB_LABEL')); ?>
            	<div id="product_information">
                	<input type="hidden" name="jform[submitted_tabs]" value="information" />	
                	<div id="step_information" class="panel">
                		<div class="panel-title"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_INFORMATION_TITLE'); ?></div>
                        <div class="panel-content well" >
                		<?php if(isset($this->display_common_field) && $this->display_common_field){ ?>
                        <div class="warning" style="display: block"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_WARNING_LABEL'); ?></div>
                        <?php } echo $this->productMultiShopCheckFields('information'); ?>
                        <div class="separation"></div>
                        <div id="warn_virtual_combinations" class="warn" style="display:none"><?php  echo JText::_('COM_JEPROSHOP_PRODUCT_COMBINATIONS_NOT_ALLOWED_FOR_VIRTUAL_PRODUCT'); ?></div>
                        <div class="control-group">
                            <div class="control-label"><label id="jform_product_type-lbl" for="jform_product_type" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_TYPE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TYPE_LABEL'); ?></label></div>
                            <div class="controls">
                            	<fieldset id="jform_product_type" class="radio btn-group" >
                                    <input type="radio" id="jform_product_type_0" name="jform[product_type]" value="<?php echo JeproshopProductModelProduct::SIMPLE_PRODUCT; ?>"  checked="checked" /><label for="jform_product_type_0" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SIMPLE_PRODUCT_LABEL'); ?></label>
                                    <input type="radio" id="jform_product_type_1" name="jform[product_type]" value="<?php echo JeproshopProductModelProduct::PACKAGE_PRODUCT; ?>"  /><label for="jform_product_type_1"  ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PACKAGE_LABEL'); ?></label>
                                    <input type="radio" id="jform_product_type_2" name="jform[product_type]" value="<?php echo JeproshopProductModelProduct::VIRTUAL_PRODUCT; ?>"  /><label for="jform_product_type_2"  ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VIRTUAL_LABEL'); ?></label>
                                 </fieldset>
                            </div>
                        </div>
                        <div class="separation"></div>
                        <div class="form_box_container" >
                        	<div class="half_wrapper left" >
                        		<div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->productMultiShopCheckbox('name', 'default'); ?>
                                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_LABEL'); ?><span class="star" style="float: right; display:none">*</span></label>
                                    </div>
                                    <div class="controls" >
                                        <?php echo $this->helper->multiLanguageInputField('name', 'jform', true); ?>
                                         <span class="hint help_box" ><?php echo JText::_('COM_JEPROSHOP_FORBIDDEN_CHARACTERS_MESSAGE'); ?></span>
                                     </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCE_LABEL'); ?></label></div>
                                    <div class="controls">
                                        <input type="text" name="jform[reference]" id="jform_reference" value="" maxlength="32" />
                                        <span class="hint help_box"><?php echo JText::_('COM_JEPROSHOP_SPECIAL_CHARACTERS_MESSAGE'); ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_EAN_13_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EAN_13_LABEL'); ?></label></div>
                                    <div class="controls"><input type="text" maxlength="13" name="jform[ean13]" value="" /><p class="small"><?php echo JText::_('COM_JEPROSHOP_EUROPE_JAPAN'); ?></p></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_UPC_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_UPC_LABEL'); ?></label></div>
                                    <div class="controls"><input type="text" maxlength="12" name="jform[upc]" value="" /><p class="small" ><?php echo JText::_('COM_JEPROSHOP_US_CANADA_LABEL'); ?></p></div>
                                </div>
                        	</div><!--  -->
                        	<div class="half_wrapper right" >
                        		<div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->productMultiShopCheckbox('published', 'radio'); ?>
                                        <label id="jform_published-lbl" for="jform_published" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PUBLISHED_LABEL'); ?></label>
                                   	</div>
                                    <div class="controls"><?php echo $this->helper->radioButton('published'); ?></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->productMultiShopCheckbox('redirect_type', 'radio'); ?>
                                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_REDIRECT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REDIRECT_LABEL'); ?></label>
                                    </div>
                                    <div class="controls">
                                        <select name="jform[redirect_type]" id="jform_redirect_type" >
                                            <option value="404" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NO_REDIRECT_LABEL'); ?></option>
                                            <option value="301" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PERMANENTLY_REDIRECT_LABEL'); ?></option>
                                            <option value="302" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TEMPORARILY_REDIRECT_LABEL'); ?></option>
                                        </select><br />
                                        <span class="hint help_box">
                                        	<?php echo JText::_('COM_JEPROSHOP_PRODUCT_NO_REDIRECT_DESC'); ?><br />
                                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_PERMANENTLY_REDIRECT_DESC'); ?><br />
                                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_TEMPORARILY_REDIRECT_DESC'); ?>
                                    	</span>
                                	</div>
                            	</div>  
                            	<div class="control-group redirect_product_options redirect_product_options_product_choice" >
                                    <div class="control-label">
                                         <?php echo $this->productMultiShopCheckbox('product_redirected_id', 'radio'); ?>
                                         <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_RELATED_PRODUCT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_RELATED_PRODUCT_LABEL'); ?></label>
                                    </div>
                                    <div class="controls" >
                                    	<input type="hidden" value="" name="jform[product_redirected_id]" id="jform_product_redirected_id"  />
                                    	<input type="text" id="jform_related_product_auto_complete_input" name="jform[related_product_auto_complete_input]" autocomplete="off" class="auto_complete_input" />
										<span class="input-group-addon"><i class="icon-search"></i></span>
										<div class="form-control-static">
											<span id="jform_related_product_name"><i class="icon-warning-sign"></i> <?php echo JText::_('COM_JEPROSHOP_NO_RELATED_PRODUCT_DESC'); ?></span>

											<span id="jform_related_product_remove" style="display:none">
												<a class="btn btn-default" href="#"  id="related_product_remove_link"> <i class="icon-remove text-danger"></i>	</a>
											</span>
										</div>
                                    </div>
                                </div>
                                <div class="control-group" >
                                    <div class="control-label" >
                                        <?php echo $this->productMultiShopCheckbox('visibility', 'radio'); ?>
                                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_LABEL'); ?></label>
                                    </div>
                                    <div class="controls" >
                                    	<select name="jform[visibility]" id="jform_visibility" >
                                            <option value="both" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_EVERYWHERE_LABEL'); ?></option>
                                            <option value="catalog" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_IN_CATALOG_LABEL'); ?></option>
                                            <option value="search" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_ON_SEARCH_LABEL'); ?></option>
                                            <option value="none"  ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_NO_WHERE_LABEL'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group" id="jform_product_options" >
                                    <div class="control-label">
                                        <?php if(isset($this->display_mutishop_checkboxes) && $this->display_multishop_checkboxes){ ?>
                                            <div class="multi_shop_product_checkbox">
                                                <ul class="list_form" >
                                                    <li></li>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_OPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_OPTIONS_LABEL'); ?></label>
                                    </div>
                                    <div class="controls">
                                        <ul class="list_form">
                                            <li><p class="checkbox" ><input type="checkbox" name="jform[available_for_order]" id="jform_" value="1" /><label for="jform_available_for_order" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_AVAILABLE_FOR_ORDER_LABEL'); ?></label></p></li>
                                            <li><p class="checkbox" ><input type="checkbox" name="jform[show_price]" id="jform_show_price" value="1" /><label for="jform_show_price" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHOW_PRICE_LABEL'); ?></label></p></li>
                                            <li><p class="checkbox" ><input type="checkbox" name="jform[online_only]" id="jform_online_only" value="1" /><label for="jform_online_only" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_ONLINE_ONLY_LABEL'); ?></label></p></li>
                                        </ul>
                                    </div>                                                
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->productMultiShopCheckbox('condition', 'default'); ?>
                                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_LABEL'); ?></label>
                                    </div>
                                    <div class="controls">
                                        <select name="jform[condition]" id="jform_condition" >
                                            <option value="new" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_NEW_LABEL'); ?></option>
                                            <option value="used" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_USED_LABEL'); ?></option>
                                            <option value="refurbished" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_REFURBISHED_LABEL'); ?></option>
                                        </select>
                                    </div>
                                </div>
                        	</div>
                        </div>
                        <div class="separation" style="clear:both; "></div>
                        <div class="form_box_container" >
                        	<div class="control-group" >
                            	<div class="control-label">
                                    <?php echo $this->productMultiShopCheckbox('short_description', 'tinymce'); ?>
                                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_LABEL'); ?><br /></label>
                                    <p class="field_description"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_DESCRIPTION'); ?></p>
                                </div>
                            	<div class="controls"><?php echo $this->helper->multiLanguageTextAreaField('short_description', ''); ?></div>
                            </div>
                            <div class="control-group">
                            	<div class="control-label">
                                	<?php echo $this->productMultiShopCheckbox('description', 'tinymce'); ?>
                                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_LABEL'); ?><br /></label>
                                    <p class="field_description" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_DESCRIPTION'); ?></p>
                                </div>
                                <div class="controls"><?php echo $this->helper->multiLanguageTextAreaField('description', ''); ?></div>
                            </div>
                            <?php if(isset($this->images) && $this->images){ ?>
                            <div class="control-group">
                                <div class="control-label"></div>
                                <div class="controls">
                                    <div style="display: block;" class="hint clear">
                                         <?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_ASSOCIATION_MESSAGE'); ?>
                                         <span class="addImageDescription" style="cursor:pointer"><?php echo JText::_('COM_JEPROSHOP_CLICK_HERE_LABEL'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="separation" style="clear:both; "></div>
                            <div class="control-group">
                				<div class="control-label"></div>
                				<div class="controls">
                            		<table id="jform_createImageDescription" >
                            			<tr><td colspan="2" height="10" ></td></tr>
                            			<tr>
                                            <td><label><?php echo JText::_('COM_JEPROSHOP_SELECT_YOUR_IMAGE_LABEL'); ?></label></td>
                                            <td>
                                                <ul class="small_image">
                                                    <?php foreach($this->images as $key => $image){ ?>
                                                    <li>
                                                        <input type="radio" name="jform[small_image]" id="jform_small_image_<?php echo $key; ?>" value="<?php echo $image->image_id; ?>" <?php if($key == 0){ ?> checked="checked" <?php } ?> />
                                                        <label for="jform_small_image_<?php echo $key; ?>"><img src="<?php echo $image->src; ?>" alt="<?php echo $image->legend; ?>" /></label>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </td>
                                        </tr>
                            			<tr>
                                            <td><label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_POSITION_LABEL'); ?></label></td>
                                            <td>
                                                <fieldset class="radio btn-group" id="jform_left_right">
                                                    <input type="radio" id="jform_left_right1" name="jform[left_right]" value="left" <?php if($image->position == 'left'){ ?> checked="checked" <?php } ?> /><label for="jform_left_right1" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label>
                                                    <input type="radio" id="jform_left_right2" name="jform[left_right]" value="right" <?php if($image->position == 'right'){ ?> checked="checked" <?php } ?>/><label for="jform_left_right2" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label>
                                                </fieldset>
                                            </td>
                                        </tr>
                            			<tr>
                                            <td><label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_TYPE_LABEL'); ?></label></td>
                                            <td>
                                               	<ul class="list_form" >
                                            	   	<?php foreach($this->imagesTypes as $key => $imageType){ ?>
                                                    <li>
                                                            	<input type="radio" name="jform[image_types]" id="jform_image_types_<?php echo $key; ?>" /><label for="jform_image_types_<?php echo $key; ?>" ><?php $imageType->name; ?></label>
                                                        	</li>
                                                        	<?php } ?>
                                                    	</ul>
                                                	</td>
                                        </tr>
                            			<tr>
                                    		<td><label><?php echo JText::_('COM_JEPROSHOP_IMAGE_TAG_TO_INSERT_LABEL'); ?></label></td>
                                    		<td>
                                        		<input type="text" id="jform_result_image" name="jform[result_image]" /><p class="preference_description"><?php echo JText::_('COM_JEPROSHOP_IMAGE_TAG_TO_INSERT_DESCRIPTION'); ?></p>
                                    		</td>
                                		</tr>
                            		</table>
                            	</div>
                            </div>
                            <?php } ?>
                            <div class="control-group">
                                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_TAGS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TAGS_LABEL'); ?></label></div>
                                <div class="controls"><?php echo $this->helper->multiLanguageInputField('tags', 'jform'); ?></div>
                            </div>
                        </div>
                	</div>
                	</div>
                </div>
            	<?php echo JHtml::_('bootstrap.endTab'); 
            	echo JHtml::_('bootstrap.addTab', 'product_form', 'price', JText::_('COM_JEPROSHOP_PRODUCT_PRICE_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div> 
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'seo', JText::_('COM_JEPROSHOP_PRODUCT_SEO_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div> 
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'associations', JText::_('COM_JEPROSHOP_PRODUCT_ASSOCIATION_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>  
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'declinations', JText::_('COM_JEPROSHOP_PRODUCT_DECLINATIONS_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>               
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'quantities', JText::_('COM_JEPROSHOP_PRODUCT_QUANTITIES_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>   
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'images', JText::_('COM_JEPROSHOP_PRODUCT_IMAGES_TAB_LABEL')) ; ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div> 
            	<?php echo  JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'characteristics', JText::_('COM_JEPROSHOP_PRODUCT_CHARACTERISTICS_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div> 
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'customization', JText::_('COM_JEPROSHOP_PRODUCT_PERSONALIZATION_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>  
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'join_files', JText::_('COM_JEPROSHOP_PRODUCT_JOIN_FILE_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>  
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'supplier', JText::_('COM_JEPROSHOP_PRODUCT_SUPPLIER_TAB_LABEL')) ; ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div>              	            
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.addTab', 'product_form', 'developer', JText::_('COM_JEPROSHOP_PRODUCT_DEVELOPER_TAB_LABEL')); ?>
            	<div><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MUST_BE_SAVED_BEFORE_YOU_FILL_THIS_FIELDS_MESSAGE')?></div> 
            	<?php echo JHtml::_('bootstrap.endTab') . JHtml::_('bootstrap.endTabSet');  ?>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<script type="text/javascript" >
    jQuery(document).ready(function(){
        var jeproProduct = new JeproshopProduct({});
        jQuery('#related_product_remove_link').click(function(){
            jeproProduct.removeRelatedProduct();
            return false;
        });
    });
</script>