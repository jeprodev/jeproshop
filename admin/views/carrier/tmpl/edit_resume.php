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
<div class="panel" id="jform_carrier_resume_setting" >
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_RESUME_SETTINGS_LABEL'); ?></div>
    <div class="panel-content well" >
        <p id="jform_summary_meta_informations"></p>
        <p id="jform_summary_shipping_cost"></p>
        <p id="jform_summary_range"></p>

        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SUMMARY_ZONES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_ZONES_LABEL'); ?></label> </div>
            <div class="controls" ><ul id="jform_summary_zones"></ul></div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SUMMARY_GROUPS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_GROUPS_LABEL'); ?></label></div>
            <div class="controls" ><ul id="jform_summary_groups" ></ul></div>
        </div>
        <?php if(JeproshopShopModelShop::isFeaturePublished()){ ?>
        <div class="control-group" >
            <div class="control-label" ><label for="" title="<?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_TITTLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_LABEL'); ?></label> </div>
            <div class="controls" ><ul id="jform_summary_shops"></ul></div>
        </div>
        <?php } ?>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_PUBLISH_CARRIER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label> </div>
            <div class="controls" ><?php echo $this->helper->radioButton('published', 'edit', $this->carrier->published); ?></div>
        </div>
    </div>
</div>