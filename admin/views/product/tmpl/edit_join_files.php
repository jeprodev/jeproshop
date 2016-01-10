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
<div id="product-attachments" class="panel product-tab" >
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></div>
	<div class="panel-content well" >
    <div class="control-group" >
        <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_FILENAME_TITLE_DESC') . '<br />' . JText::_('COM_JEPROSHOP_MAXIMUM_CHARACTERS_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_FILENAME_LABEL'); ?></label></div>
        <div class="controls" ><?php echo $this->helper->multiLanguageInputField('attachment_name', 'attachments', true, $this->attachment_name); ?></div>
    </div>
    <div class="control-group" >
        <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ATTACHMENT_DESCRIPTION_LABEL'); ?></label></div>
        <div class="controls" ><?php echo $this->helper->multiLanguageTextAreaField('attachment_description', 'attachments', $this->attachment_description); ?></div>
    </div>
    <div class="control-group" >
        <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_ATTACHMENT_FILENAME_TITLE_DESC') . ' ' . $this->attachment_maximum_size . ' ' . JText::_('COM_JEPROSHOP_MB_MAX_LABEL'); ?>"><?php echo JText::_('COM_JEPROSHOP_FILE_LABEL'); ?></label></div>
        <div class="controls" ><?php echo $this->attachment_uploader; ?></div>
    </div>
    <hr />
    <div class="control-group" >
        <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_AVAILABLE_ATTACHMENTS_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ATTACHMENT_FILENAME_LABEL'); ?></label></div>
        <div class="controls" >
            <div class="available_attachments one_third">
            	<p><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_ATTACHMENTS_MESSAGE'); ?></p><br/>
            	<select multiple id="jform_select_attachment_2">
                	<?php foreach($this->attachments_2 as $attachment){ ?>
                	<option value="<?php echo $attachment->attachment_id; ?>"><?php echo $tattachment->name; ?></option>
                	<?php } ?>
            	</select>
        	</div>
        	<div class="attachments_action one_third">
            	<a href="#" id="jform_add_attachment" class="button btn btn-default btn-block" ><?php echo ucfirst(JText::_('COM_JEPROSHOP_ADD_LABEL')); ?><i class="icon-arrow-right"></i></a><br />
            	<a href="#" id="jform_remove_attachment" class="button btn btn-default btn-block"><i class="icon-arrow-left" ></i><?php echo ucfirst(JText::_('COM_JEPROSHOP_REMOVE_LABEL')); ?></a>
        	</div>
        	<div class="attachments one_third">
            	<p ><?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_FOR_THIS_PRODUCT_LABEL'); ?></p>
            	<select multiple id="jform_select_attachment_1" name="jform[attachments]" >
                	<?php foreach($this->attachments_1 as $attachment){ ?>
                	<option value="<?php echo $attachment->attachment_id; ?>" ><?php echo $attachment->name; ?></option>
                	<?php } ?>
            	</select>
        	</div>
    	</div>
    </div>
    <input type="hidden" name="jform[array_attachments]" id="jform_array_attachments" value="<?php foreach($this->attachments_1 as $attachment){ echo $attachment->attachment_id . ', '; } ?>" />
    </div>
</div>
<?php }