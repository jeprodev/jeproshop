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

?>
<div class="form_box_wrapper"  id="product_information" >
	<div id="step_information" class="panel" >
		<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_INFORMATION_TITLE'); ?></div>
        <div class="panel-content well" >
            <?php if(isset($this->display_common_field) && $this->display_common_field){ ?>
            <div class="warning" style="display: block"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_WARNING_LABEL'); ?></div>
            <div class="separation"></div>
            <?php } echo $this->productMultiShopCheckFields('information'); ?>
            <div id="warn_virtual_combinations" class="alert alert-info" style="display:none"><?php  echo JText::_('COM_JEPROSHOP_PRODUCT_COMBINATIONS_NOT_ALLOWED_FOR_VIRTUAL_PRODUCT'); ?></div>
            <div class="form_box_wrapper" >
                <div class="control-group">
                    <div class="control-label" ><label id="jform_product_type-lbl" for="jform_product_type" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_TYPE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TYPE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <fieldset id="jform_product_type" class="radio btn-group" >
                            <input type="radio" id="jform_product_type0" name="information[product_type]" value="<?php echo JeproshopProductModelProduct::SIMPLE_PRODUCT; ?>" <?php if($this->product->product_type == JeproshopProductModelProduct::SIMPLE_PRODUCT){ ?> checked="checked" <?php } ?> /><label for="jform_product_type0" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_SIMPLE_PRODUCT_TITLE_DESC') ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SIMPLE_PRODUCT_LABEL'); ?></label>
                            <input type="radio" id="jform_product_type1" name="information[product_type]" value="<?php echo JeproshopProductModelProduct::PACKAGE_PRODUCT; ?>" <?php if($this->product->product_type == JeproshopProductModelProduct::PACKAGE_PRODUCT){ ?> checked="checked" <?php } ?> /><label for="jform_product_type1" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PACKAGE_TITLE_DESC') ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PACKAGE_LABEL'); ?></label>
                            <input type="radio" id="jform_product_type2" name="information[product_type]" value="<?php echo JeproshopProductModelProduct::VIRTUAL_PRODUCT; ?>" <?php if($this->product->product_type == JeproshopProductModelProduct::VIRTUAL_PRODUCT){ ?> checked="checked" <?php } ?> /><label for="jform_product_type2" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_VIRTUAL_TITLE_DESC') ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VIRTUAL_LABEL'); ?></label>
                        </fieldset>
                    </div>
                </div>
            </div>
        <div class="separation"></div>        
        <div class="form_box_wrapper">
        	<div class="half_wrapper left" >
        		<div class="control-group" >
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('name', 'default'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_LABEL'); ?></label>
                    </div>
                    <div class="controls" >
                        <?php echo $this->helper->multiLanguageInputField('name', 'information', true, $this->product->name); ?>
                        <br /><p class="hint help_box" ><?php echo JText::_('COM_JEPROSHOP_FORBIDDEN_CHARACTERS_MESSAGE'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCE_LABEL'); ?></label></div>
                    <div class="controls">
                        <input type="text" name="information[reference]" id="jform_reference" value="<?php echo htmlentities($this->product->reference); ?>" required="required" />
                        <br /><p class="hint help_box"><?php echo JText::_('COM_JEPROSHOP_SPECIAL_CHARACTERS_MESSAGE'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_EAN_13_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EAN_13_LABEL'); ?></label></div>
                    <div class="controls"><input type="text" maxlength="13" name="information[ean13]" value="<?php echo htmlentities($this->product->ean13); ?>" /><p class="small"><?php echo JText::_('COM_JEPROSHOP_EUROPE_JAPAN'); ?></p></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label for="jform_upc" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_UPC_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_UPC_LABEL'); ?></label></div>
                    <div class="controls"><input type="text" id="jform_upc" maxlength="12" name="information[upc]" value="<?php echo htmlentities($this->product->upc); ?>" /><p class="small" ><?php echo JText::_('COM_JEPROSHOP_US_CANADA_LABEL'); ?></p></div>
                </div>
        	</div>
        	<div class="half_wrapper right" >
        		<div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('published', 'radio'); ?>
                        <label id="jform_published-lbl" for="jform_published" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PUBLISHED_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <fieldset id="jform_published" class="btn-group radio">
                            <input type="radio" id="jform_published_0" name="information[published]" value="1" <?php if($this->product->published == 1){ ?> checked="checked" <?php } ?> /><label for="jform_published_0"><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                            <input type="radio" id="jform_published_1" name="information[published]" value="0" <?php if($this->product->published == 0){ ?> checked="checked" <?php } ?> /><label for="jform_published_1"><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('redirect_type', 'radio'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_REDIRECT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REDIRECT_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <select name="information[redirect_type]" id="jform_redirect_type" >
                            <option value="404" <?php if($this->product->redirect_type == '404'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NO_REDIRECT_LABEL'); ?></option>
                            <option value="301" <?php if($this->product->redirect_type == '301'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PERMANENTLY_REDIRECT_LABEL'); ?></option>
                            <option value="302" <?php if($this->product->redirect_type == '302'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TEMPORARILY_REDIRECT_LABEL'); ?></option>
                        </select><br />
                        <p class="hint help_box">
                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_NO_REDIRECT_DESC'); ?><br />
                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_PERMANENTLY_REDIRECT_DESC'); ?><br />
                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_TEMPORARILY_REDIRECT_DESC'); ?>
                        </p>
                    </div>
                </div>
                <div class="control-group redirect_product_options redirect_product_options_product_choice" >
                     <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('product_redirected_id', 'radio'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_RELATED_PRODUCT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_RELATED_PRODUCT_LABEL'); ?></label>
                     </div>
                     <div class="controls" >
                        <input type="hidden" value="<?php echo $this->product->product_redirected_id; ?>" name="information[product_redirected_id]"  />
                        <input type="text" id="jform_related_product_auto_complete_input" name="information[related_product_auto_complete_input]" autocomplete="off" class="ac_input" />
						<span class="input-group-addon"><i class="icon-search"></i> </span>
						<div class="form-control-static">
							<span id="jform_related_product_name"><i class="icon-warning-sign"></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_NO_RELATED_PRODUCT_DESC'); ?></span>
							<span id="jform_related_product_remove" style="display:none">
								<a class="btn btn-default" href="#" onclick="removeRelatedProduct(); return false" id="jform_related_product_remove_link">
									<i class="icon-remove text-danger"></i>
								</a>
							</span>
						</div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('visibility', 'radio'); ?>
                        <label for="jform_visibility" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <select name="information[visibility]" id="jform_visibility" >
                            <option value="both" <?php if($this->product->visibility == 'both'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_EVERYWHERE_LABEL'); ?></option>
                            <option value="catalog" <?php if($this->product->visibility == 'catalog'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_IN_CATALOG_LABEL'); ?></option>
                            <option value="search" <?php if($this->product->visibility == 'search'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_ON_SEARCH_LABEL'); ?></option>
                            <option value="none" <?php if($this->product->visibility == 'none'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_VISIBILITY_NO_WHERE_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" id="jform_product_options" <?php if(!$this->product->published){ ?> style="" <?php } ?>>
                    <div class="control-label">
                        <?php if(isset($this->display_mutishop_checkboxes) && $this->display_multishop_checkboxes){ ?>
                        <div class="multi_shop_product_checkbox">
                                    <ul class="list_form" ><li></li></ul>
                        </div>
                        <?php } ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_OPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_OPTIONS_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <ul class="list_form">
                            <li><p class="checkbox" ><input type="checkbox" name="information[available_for_order]" id="jform_available_for_order" value="1" <?php if($this->product->available_for_order){ ?> checked="checked" <?php } ?> /><label for="jform_available_for_order" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_AVAILABLE_FOR_ORDER_LABEL'); ?></label></p></li>
                            <li><p class="checkbox" ><input type="checkbox" name="information[show_price]" id="jform_show_price" value="1" <?php if($this->product->show_price){ ?> checked="checked" <?php } ?> /><label for="jform_show_price" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHOW_PRICE_LABEL'); ?></label></p></li>
                            <li><p class="checkbox" ><input type="checkbox" name="information[online_only]" id="jform_online_only" value="1" <?php if($this->product->online_only){ ?> checked="checked" <?php } ?> /><label for="jform_online_only" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_ONLINE_ONLY_LABEL'); ?></label></p></li>
                        </ul>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('condition', 'default'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <select name="information[condition]" id="jform_condition" >
                            <option value="new" <?php if($this->product->condition == "new"){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_NEW_LABEL'); ?></option>
                            <option value="used" <?php if($this->product->condition == "used"){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_USED_LABEL'); ?></option>
                            <option value="refurbished" <?php if($this->product->condition == "refurbished"){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CONDITION_REFURBISHED_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
        	</div>
        </div> 
        <div class="separation"></div>    
        <div class="form_box_wrapper" > 
        	<div class="control-group">
                <div class="control-label">
                    <?php echo $this->productMultiShopCheckbox('short_description', 'tinymce'); ?>
                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_LABEL'); ?><br /></label>
                    <p class="product_description"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_DESCRIPTION'); ?></p>
                </div>
                <div class="controls"><?php echo $this->helper->multiLanguageTextAreaField('short_description', 'information', $this->product->short_description); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->productMultiShopCheckbox('description', 'tinymce'); ?>
                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_LABEL'); ?><br /></label>
                    <p class="product_description" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DESCRIPTION_DESCRIPTION'); ?></p>
                </div>
                <div class="controls"><?php echo $this->helper->multiLanguageTextAreaField('description', 'information', $this->product->description); ?></div>
            </div>
            <?php if($this->product_images){ ?>
            <div class="control-group">
                <div class="control-label"></div>
                <div class="controls hint clear alert alert-info" style="display: block;">                    
                    <?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_ASSOCIATION_MESSAGE'); ?>
                    <a class="addImageDescription" style="cursor:pointer" href="javascript:void(0);"><?php echo JText::_('COM_JEPROSHOP_CLICK_HERE_LABEL'); ?></a>
                </div>
            </div>
            <div id="createImageDescription" class="panel" style="display:none">
            	<div class="panel_content" >            	
            		<div class="control-group" >
            			<div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_SELECT_YOUR_IMAGE_LABEL'); ?></label></div>
            			<div class="controls" id="jform_createImageDescription" >
            				<ul class="small_image list-inline" >
            					<?php foreach($this->product_images as $key => $image){ ?>
                            	<li>
                                	<input type="radio" name="information[small_image]" id="jform_small_image_<?php echo $key; ?>" value="<?php echo $image->image_id; ?>" <?php if($key == 0){ ?> checked="checked" <?php } ?> />
                                	<label for="jform_small_image_<?php echo $key; ?>" ><img src="<?php echo $image->src; ?>" alt="<?php echo $image->legend; ?>" /></label>
                            	</li>
                            	<?php } ?>
            				</ul>
            			</div>
            		</div>
            		<div class="control-group">
            			<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_POSITION_LABEL'); ?></label></div>
            			<div class="controls" >
            				<fieldset class="radio btn-group" id="jform_left_right" >
                                <input type="radio" id="jform_left_right1" name="information[left_right]" value="left" <?php if($image->position == 'left'){ ?> checked="checked" <?php } ?> /><label for="jform_left_right1" ><?php echo JText::_('COM_JEPROSHOP_LEFT_LABEL'); ?></label>
                                <input type="radio" id="jform_left_right2" name="information[left_right]" value="right" <?php if($image->position == 'right'){ ?> checked="checked" <?php } ?>/><label for="jform_left_right2" ><?php echo JText::_('COM_JEPROSHOP_RIGHT_LABEL'); ?></label>
                            </fieldset>
            			</div>
            		</div>
            		<div class="control-group">
            			<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_IMAGE_TYPE_LABEL'); ?></label></div>
            			<div class="controls" >
            				<ul class="list_form" >
                                <?php foreach($this->imagesTypes as $key => $imageType){ ?>
                                <li>
                                   <input type="radio" name="information[image_types]" id="jform_image_types_<?php echo $key; ?>" /><label for="jform_image_types_<?php echo $key; ?>" ><?php echo $imageType->name; ?></label>
                                </li>
                                <?php } ?>
                            </ul>
            			</div>
            		</div>
            		<div class="control-group">
            			<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_IMAGE_TAG_TO_INSERT_LABEL'); ?></label></div>
            			<div class="controls" >
            				<input type="text" id="jform_result_image" name="information[result_image]" /><p class="preference_description"><?php echo JText::_('COM_JEPROSHOP_IMAGE_TAG_TO_INSERT_DESCRIPTION'); ?></p>
            			</div>
            		</div>
            	</div>
            </div>
            
            <?php } ?>
            <div class="control-group">
                <div class="control-label"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TAGS_LABEL'); ?></div>
                <div class="controls"><?php echo $this->helper->multiLanguageInputField('tag', 'information', FALSE, $this->product->tags); ?></div>
            </div>
        </div>
        </div>
	</div>
</div>