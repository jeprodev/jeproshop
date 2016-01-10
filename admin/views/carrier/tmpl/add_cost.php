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
<div class="panel" id="jform_carrier_cost_setting" >
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_COST_SETTINGS_LABEL'); ?></div>
    <div class="panel-content well" >
        <div class="control-group" >
            <div class="control-label" ><label for="jform_shipping_handling" title="<?php echo JText::_('COM_JEPROSHOP_ADD_HANDLING_COSTS_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ADD_HANDLING_COSTS_LABEL'); ?></label></div>
            <div class="controls" >
                <fieldset class="radio btn-group" >
                    <input type="radio" id="jform_shipping_handling_1" name="jform[shipping_handling]"  checked="checked"  value="1" /><label for="jform_shipping_handling_1" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                    <input type="radio" id="jform_shipping_handling_0" name="jform[shipping_handling]"  value="0" /><label for="jform_shipping_handling_0" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                </fieldset>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_is_free" title="<?php echo JText::_('COM_JEPROSHOP_FREE_SHIPPING_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_FREE_SHIPPING_LABEL'); ?></label></div>
            <div class="controls" >
                <fieldset class="radio btn-group" >
                    <input type="radio" id="jform_is_free_1" name="jform[is_free]"  checked="checked"  value="1" /><label for="jform_is_free_1" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                    <input type="radio" id="jform_is_free_0" name="jform[is_free]"  value="0" /><label for="jform_is_free_0" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                </fieldset>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_shipping_method" title="<?php echo JText::_('COM_JEPROSHOP_SHIPPING_METHOD_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_SHIPPING_METHOD_LABEL'); ?></label></div>
            <div class="controls" >
                <fieldset class="btn-group radio" >
                    <input type="radio" id="jform_shipping_method_1" name="jform[shipping_method]" value="<?php echo JeproshopCarrierModelCarrier::PRICE_SHIPPING_METHOD; ?>" checked="checked"  /><label for="jform_shipping_method_1" ><?php echo JText::_('COM_JEPROSHOP_ACCORDING_TO_TOTAL_PRICE_LABEL'); ?></label>
                    <input type="radio" id="jform_shipping_method_0" name="jform[shipping_method]" value="<?php echo JeproshopCarrierModelCarrier::WEIGHT_SHIPPING_METHOD; ?>"  /><label for="jform_shipping_method_0" ><?php echo JText::_('COM_JEPROSHOP_ACCORDING_TO_TOTAL_WEIGHT_LABEL'); ?></label>
                </fieldset>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_tax_rules_group_id" title="<?php echo JText::_('COM_JEPROSHOP_TAX_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_TAX_LABEL'); ?></label></div>
            <div class="controls" >
                <select id="jform_tax_rules_group_id" name="jform[tax_rules_group_id]" >
                    <option value="0" ><?php echo JText::_('COM_JEPROSHOP_NO_TAX_LABEL'); ?></option>
                    <?php foreach($this->tax_rules_groups as $tax_rules_group){ ?>
                    <option value="<?php echo $tax_rules_group->tax_rules_group_id; ?>"  <?php if($tax_rules_group->tax_rules_group_id == $this->carrier->tax_rules_group_id){ ?>selected="selected" <?php } ?> ><?php echo $tax_rules_group->name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_range_behavior" title="<?php echo JText::_('COM_JEPROSHOP_OUT_OF_RANGE_BEHAVIOR_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_OUT_OF_RANGE_BEHAVIOR_LABEL'); ?></label></div>
            <div class="controls" >
                <select id="jform_range_behavior" name="jform[range_behavior]" >
                    <option value="0" selected="selected" ><?php echo JText::_('COM_JEPROSHOP_APPLY_THE_COST_OF_HIGHEST_DEFINED_RANGE_LABEL'); ?></option>
                    <option value="1" ><?php echo JText::_('COM_JEPROSHOP_DISABLE_CARRIER_LABEL'); ?></option>
                </select>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label for="jform_zones" title="<?php echo JText::_('COM_JEPROSHOP_SUPPORTED_ZONES_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_SUPPORTED_ZONES_LABEL'); ?></label></div>
            <div class="controls" >
                <table class="table" style="width: 500px; ">
                    <tbody>
                <?php foreach($this->zones as $zone){ ?>
                    <tr style="border-top: none";>
                        <td class="nowrap" width="1%" ><input type="checkbox" value="1"  id="jform_zones_<?php echo $zone->zone_id; ?>" name="jform[zone_<?php echo $zone->zone_id; ?>]" /></td>
                        <td class="nowrap" ><?php echo ucfirst($zone->name); ?></td>
                    </tr>
                <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>