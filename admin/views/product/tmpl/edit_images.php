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
<div class="form_box_wrapper panel"  id="product_images" >
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_IMAGES_TITLE'); ?> <span class="badge" id="jform_count_image" ><?php echo count($this->images); ?></span></div>
    <div class="panel-content well">
	<div class="control-group" >
        <div class="control-label">
            <label for="jform_" title="<?php echo JText::_('COm_JEPROSHOP_FORMAT_LABEL') . ': JPG, GIF, PNG. ' . JText::_('COM_JEPROSHOP_FILESIZE_LABEL') . ': ' . $this->max_image_size . ' ' . JText::_('COM_JEPROSHOP_MB_MAX_LABEL');  ?>" >
                <?php if(isset($this->image_id)){ echo JText::_('COM_JEPROSHOP_THIS_PRODUCT_IMAGE_LABEL'); }else{ echo JText::_('COM_JEPROSHOP_ADD_NEW_IMAGE_TO_THIS_PRODUCT_LABEL'); } ?>
            </label>
        </div>
        <div class="controls"><?php echo $this->image_uploader; ?></div>
    </div>
    <div class="control-group" >
        <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_INVALID_CHARACTERS_LABEL'); ?>"><?php echo JText::_('COM_JEPROSHOP_IMAGE_LEGEND_LABEL'); ?></label></div>
        <div class="controls">
            <?php echo $this->helper->multiLanguageInputField('legend', true, $this->product->name); ?>
        </div>
    </div>
    <div class="control-group" >
        <div class="control-label"><label ></label></div>
        <div class="controls">
        	<table class="table tableDnD" id="jform_image_table" >
                <thead>
                    <tr>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_IMAGE_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_CAPTION_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_POSITION_LABEL'); ?></th>
                        <?php if($this->shops){
                             foreach($this->shops as $shop){ ?>
                        <th class="title_box" ><?php echo $shop->shop_name; ?></th>
                        <?php }
                        } ?>                                        
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_COVER_LABEL'); ?></th><th></th>
                    </tr>
                </thead>
                <tbody id="jform_images_list"></tbody>
            </table>
            <table id="jform_image_type" style="display: none;" class="table">
                <tr id="image_id" >
                    <td>
                        <a href="<?php echo COM_JEPROSHOP_PRODUCT_IMAGE_DIR . 'image_path.jpg'; ?>" class="fancybox">
                            <img src="<?php echo COM_JEPROSHOP_PRODUCT_IMAGE_DIR . $this->iso_tiny_mce . '_default_' . $this->image_type . '.jpg'; ?>" title="legend" alt="legend" class="img_thumbnail" />
                        </a>
                    </td>
                    <td>legend</td>
                    <td id="td_image_id" class="pointer drag_handle center image_position" >
                        <div class="drag_group" >
                            <div class="positions" >image_position</div>
                        </div>
                    </td>
                    <?php if($this->shops){
                        foreach($this->shops as $shop){ ?>
                    <td ><input type="checkbox" class="image_shop" name="image_id" id="<?php echo $shop->shop_id; ?>image_id" value="<?php echo $shop->shop->shop_id; ?>" /></td>
                        <?php }
                    } ?>
                    <td class="cover" ><a href="#"><i class="icon-check-empty icon-2x cover" ></i> </a></td>
                    <td ><a href="#" class="delete_product_image pull-right btn btn-default" ><i class="icon-trash" ></i> <?php echo JText::_('COM_JEPROSHOP_DELETE_THIS_IMAGE_LABEL'); ?></a> </td>
                </tr>
            </table>
            <div style="clear:both" ></div>
            <div class="panel-footer" >
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn btn-default" ><i class="icon-cancel" ></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
                <button type="submit" name="save_image" class="btn btn-default pull-right" ><i class="process-icon-save" ></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_LABEL'); ?></button>
            </div>
        </div>
    </div>
    </div>
</div>
