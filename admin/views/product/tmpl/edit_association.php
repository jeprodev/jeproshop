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
<div class="form_box_wrapper"  id="product_association" >
	<div id="step_association" class="panel" >
    	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_ASSOCIATION_TITLE'); ?></div>
        <div class="panel-content well" >
            <div id="jform_no_default_category" class="alert alert-info" ><?php echo JText::_('COM_JEPROSHOP_PLEASE_SELECT_DEFAULT_CATEGORY_LABEL')?></div>
            <div class="control-group" >
                <div class="control-label">
                    <?php echo $this->productMultiShopCheckbox('category_block', 'category_box'); ?>
                    <label for="jform_category_block" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_CATEGORY_BOX_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_CATEGORY_BOX_LABEL'); ?></label>
                </div>
                <div class="controls"><?php echo $this->category_tree; ?></div>
            </div>
            <div class="control-group" >
                <div class="control-label">
                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_TITLE_DESC'); ?>" >&nbsp;</label>
                </div>
                <div class="controls">
                    <a class="button btn btn-icon confirm_leave" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category&task=add') ?>">
                        <i class="add-icon" ></i> <span><?php echo JText::_('COM_JEPROSHOP_CREATE_NEW_CATEGORY_LABEL'); ?></span>
                    </a>
                </div>
            </div>
            <div class="control-group" >
                <div class="control-label">
                    <?php echo $this->productMultiShopCheckbox('default_category_id', 'default'); ?>
                    <label for="jform_default_category_id" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DEFAULT_CATEGORY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DEFAULT_CATEGORY_LABEL'); ?></label>
                </div>
                <div class="controls">
                    <select id="jform_default_category_id" name="association[default_category_id]">
                        <?php foreach($this->selected_category as $category){ ?>
                        <option value="<?php echo $category->category_id; ?>" <?php if($this->default_category_id == $category->category_id){ ?> selected="selected" <?php } ?> ><?php echo $category->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="separation"></div>
            <div class="control-group" >
                <div class="control-label">
                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_ACCESSORIES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_ACCESSORIES_LABEL'); ?></label>
                </div>
                <div class="controls">
                    <input type="hidden" name="association[input_accessories]" id="jform_input_accessories" value="<?php foreach($this->accessories as $accessory){ echo $accessory->product_id . '-'; } ?>" />
                    <input type="hidden" name="association[name_accessories]" id="jform_name_accessories" value="<?php foreach($this->accessories as $accessory){ echo $accessory->name . '�'; } ?>" />
                    <div id="jform_ajax_choose_product">
                        <input type="text" value="" id="jform_product_auto_complete_input" name="association[product_auto_complete]" />
                        <span class="input-group-addon" ><i class="icon-search" ></i></span>
                    </div>
                    <div>
                        <p style="clear: both; margin-top: 0;">
                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_BEGIN_TYPING_THE_FIRST_LETTERS_MESSAGE'); ?>
                        </p>
                        <p class="preference_description"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DO_NOT_FORGET_TO_SAVE_MESSAGE'); ?></p>
                    </div>
                    <div id="jform_div_accessories">
                        <?php foreach($this->accessories as $accessory){
                            echo $accessory->name;
                            if(!empty($accessory->reference)){ echo $accessory->reference; } ?>
                        <span class="delete_accessory" id="jform_<?php echo $accessory->product_id; ?>" style="cursor:pointer;" >
                            <img src="<?php echo JURI::base() . '/components/com_jeproshop/assets/images/delete.gif'; ?>" class="middle" alt="" />
                        </span><br />
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="control-group" >
                <div class="control-label">
                    <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_MANUFACTURER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_MANUFACTURER_LABEL'); ?></label>
                </div>
                <div class="controls">
                    <select name="association[manufacturer_id]" id="jform_manufacturer_id" >
                        <!-- option value="0"><?php echo JText::_('COM_JEPROSHOP_CHOOSE_OPTIONAL_LABEL'); ?></option -->
                        <?php foreach($this->manufacturers as $manufacturer){ ?>
                        <option value="<?php echo $manufacturer->manufacturer_id; ?>" <?php if($manufacturer->manufacturer_id == $this->product->manufacturer_id){ ?> selected="selected" <?php } ?> ><?php echo $manufacturer->name; ?></option>
                        <?php } ?>
                        <option disabled="disabled">--</option>
                    </select>&nbsp; &nbsp; &nbsp;
                    <a class="button btn btn-icon confirm_leave"  style="margin-bottom:0;" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer&task=add&' . JSession::getFormToken() . '=1'); ?>" >
                        <i class="add-icon" ></i><span><?php echo JText::_('COM_JEPROSHOP_CREATE_NEW_MANUFACTURER_LABEL'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>