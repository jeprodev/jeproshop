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
<div class="panel" id="jform_carrier_general_setting" >
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_GENERAL_SETTINGS_LABEL'); ?></div>
    <div class="panel-content well" >
        <div class="control-group" >
            <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_ALLOWED_CHARACTERS_LETTERS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CARRIER_NAME_LABEL'); ?></label> </div>
            <div class="controls" ><input type="text" id="jform_name" name="jform[name]" value="<?php echo $this->carrier->name; ?>" required="required" /> </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_delivery_delay" title="<?php echo JText::_('COM_JEPROSHOP_THE_ESTIMATED_DELIVERY_TIME_WILL_BE_DISPLAYED_DURING_CHECKOUT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_TRANSIT_TIME_LABEL'); ?></label> </div>
            <div class="controls" ><?php echo $this->helper->multiLanguageInputField('delay', true, $this->carrier->delay, 128); ?> </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_grade" title="<?php echo JText::_('COM_JEPROSHOP_ENTER_0_FOR_LONGEST_SHIPPING_DELAY_OR_9_FOR_THE_SHORTEST_SHIPPING_DELAY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SPEED_GRADE_LABEL'); ?></label> </div>
            <div class="controls" ><input type="text" size="1" name="jform[grade]" id="jform_grade" value="<?php echo $this->carrier->grade; ?>" /></div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_carrier_logo" title="<?php echo JText::_('COM_JEPROSHOP_CARRIER_LOGO_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CARRIER_LOGO_LABEL'); ?></label> </div>
            <div class="controls" ><input type="text" id="jform_carrier_logo" /></div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_tracking_url" title="<?php echo JText::_('COM_JEPROSHOP_DELIVERY_TRACKING_URL_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_TRACKING_URL_LABEL'); ?></label> </div>
            <div class="controls" ><input type="text" id="jform_tracking_url" name="jform[tracking_url]"  class="url" value="<?php echo $this->carrier->url; ?>" /></div>
        </div>
    </div>
</div>