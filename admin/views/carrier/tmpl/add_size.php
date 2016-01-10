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
<div class="panel" id="jform_carrier_size_setting" >
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_SIZE_SETTINGS_LABEL'); ?></div>
    <div class="panel-content well" >
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_MAX_WIDTH_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MAX_WIDTH_LABEL'); ?></label></div>
            <div class="controls" >
                <div class="input-append" >
                    <input type="text" id="jform_max_width" name="jform[max_width]" value="" class="input-small" />
                    <a class="btn btn-primary" href="#" ><label ><?php echo JeproshopSettingModelSetting::getValue('dimension_unit'); ?></label></a>
                </div>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_MAX_HEIGHT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MAX_HEIGHT_LABEL'); ?></label></div>
            <div class="controls" >
                <div class="input-append" >
                    <input type="text" id="jform_max_height" name="jform[max_height]" value="" class="input-small" />
                    <a class="btn btn-primary" href="#" ><label ><?php echo JeproshopSettingModelSetting::getValue('dimension_unit'); ?></label></a>
                </div>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_MAX_DEPTH_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MAX_DEPTH_LABEL'); ?></label></div>
            <div class="controls" >
                <div class="input-append" >
                    <input type="text" id="jform_max_depth" name="jform[max_depth]" value="" class="input-small" />
                    <a class="btn btn-primary" href="#" ><label ><?php echo JeproshopSettingModelSetting::getValue('dimension_unit'); ?></label></a>
                </div>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_MAX_WEIGHT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MAX_WEIGHT_LABEL'); ?></label></div>
            <div class="controls" >
                <div class="input-append" >
                    <input type="text" id="jform_max_weight" name="jform[max_weight]" class="input-small" value="" />
                    <a class="btn btn-primary" href="" ><label ><?php echo JeproshopSettingModelSetting::getValue('weight_unit'); ?></label></a>
                </div>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_MARK_THE_GROUPS_THAT_ALLOWED_ACCESS_TO_THIS_CARRIER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_GROUPS_LABEL'); ?></label></div>
            <div class="controls" >
                <table class="table table-bordered" style="width:500px;">
                    <thead>
                        <tr>
                            <th class="nowrap" width="1%" ><?php echo JHtml::_('grid.checkall'); ?></th>
                            <th class="nowrap" width="2%" ><?php echo JText::_('COM_JEPROSHOP_ID_LABEL'); ?></th>
                            <th class="nowrap" width="50%" ><?php echo JText::_('COM_JEPROSHOP_GROUP_NAME_LABEL'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($this->groups) && count($this->groups) > 0) {
                            foreach ($this->groups as $group) { ?>
                                <tr class="">
                                    <td class="nowrap" width=1"%"><?php ?></td>
                                    <td class="nowrap" width="2%"><?php echo (int)$group->group_id; ?></td>
                                    <td class="nowrap" width="50%"><?php echo ucfirst($group->name); ?></td>
                                </tr>
                            <?php }
                        }else{ ?>
                            <tr>
                                <td colspan="3" ><<?php echo JText::_('COM_JEPROSHOP_NO_MATCHING_MESSAGE'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>