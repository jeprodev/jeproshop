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
$css_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $css_dir .'/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=country'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('states'); ?></div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_YOU_ARE_ABOUT_TO_EDIT_LABEL') . ' ' . JText::_('COM_JEPROSHOP_STATE_LABEL') . ' ' ;?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_PROVIDE_THE_STATE_NAME_TO_DISPLAY_IN_ADDRESSES_AND_INVOICES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STATE_NAME_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_name" name="jform[name]" required="required" maxlength="32" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_iso_code" title="<?php echo JText::_('COM_JEPROSHOP_STATE_ISO_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ISO_CODE_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_iso_code" name="jform[iso_code]" maxlength="11" class="uppercase" required="required" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_zone" title="<?php echo JText::_('COM_JEPROSHOP_STATE_ZONE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ZONE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_zone" name="jform[zone_id]" required="required" >
                            <?php foreach($this->zones as $zone){ ?>
                            <option value="<?php echo $zone->zone_id; ?>" ><?php echo ucfirst($zone->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_country" title="<?php echo JText::_('COM_JEPROSHOP_STATE_COUNTRY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_country" name="jform[country_id]" required="required" >
                            <?php foreach($this->countries as $country){ ?>
                            <option value="<?php echo $country->country_id; ?>" ><?php echo ucfirst($country->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('published', 'add', 1); ?></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>