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

if(isset($this->product->product_id)){
?>
<div id="product-customization" class="panel product-tab">
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_ADD_OR_MODIFY_CUSTOMIZABLE_PROPERTIES_TITLE'); ?></div>
    <div class="panel-content well" >
        <?php if(isset($this->display_common_field) && $this->display_common_field){ ?>
        <div class="alert alert-info"><?php echo JText::_('COM_JEPROSHOP_WARNING_IF_YOU_CHANGE_THE_VALUE_OF_FIELDS_WITH_AN_ORANGE_BULLET_MESSAGE') . $this->bullet_common_field . JText::_('COM_JEPROSHOP_THE_VALUE_WILL_BE_CHANGED_FOR_ALL_OTHER_SHOPS_FOR_THIS_PRODUCT_MESSAGE'); ?></div>
        <?php } ?>
        <div class="control-group">
            <div class="control-label" >
                <label for="jform_uploadable_files" title="<?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_UPLOAD_FILE_FIELDS_TO_BE_DISPLAYED_TO_THE_USER_TITLE_DESC'); ?>" >
                    <?php echo $this->bullet_common_field . ' ' . JText::_('COM_JEPROSHOP_FILE_FIELDS_LABEL'); ?>
                </label>
            </div>
            <div class="controls">
                <input type="text" name="customization[uploadable_files]" id="jform_uploadable_files" value="<?php echo htmlentities($this->uploadable_files); ?>" />
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label for="jform_text_fields" title="<?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_TEXT_FIELDS_TO_BE_DISPLAYED_TO_THE_USER_LABEL') ?>" >
                    <?php echo $this->bullet_common_field; echo JText::_('COM_JEPROSHOP_TEXT_FIELDS_LABEL'); ?>
                </label>
            </div>
            <div class="controls">
                <input type="text" name="customization[text_fields]" id="jform_text_fields" value="<?php echo htmlentities($this->text_fields); ?>" />
            </div>
        </div>
        <?php if($this->has_file_labels){ ?>
        <hr/>
        <div class="control-group">
            <div  class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_DEFINE_THE_LABEL_OF_THE_FILE_FIELDS_LABEL'); ?></label></div>
            <div class="controls"><?php echo $this->display_file_labels; ?></div>
        </div>
        <?php }
        if($this->has_text_labels){ ?>
        <hr/>
        <div class="control-group">
            <div class="control-label"><label ><?php echo JText::_('COM_JEPROSHOP_DEFINE_THE_LABEL_OF_THE_TEXT_FIELDS_LABEL'); ?></label></div>
            <div class="controls"><?php echo $this->display_text_labels; ?></div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>