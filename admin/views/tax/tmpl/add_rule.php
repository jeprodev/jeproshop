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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tax'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('rule_group'); ?></div>
        <div class="panel" >
            <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_YOU_ARE_ABOUT_TO_ADD_LABEL') . ' ' . JText::_('COM_JEPROSHOP_A_NEW_TAX_RULE_LABEL'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_country_id" title="<?php echo JText::_('COM_JEPROSHOP_COUNTRY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <?php $default_country_id = (int)$this->context->country->country_id;
                        $countries = JeproshopCountryModelCountry::getStaticCountries($this->context->language->lang_id); ?>
                        <select id="jform_country_id" name="jform[country_id]" >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_ALL_LABEL'); ?></option>
                            <?php foreach($countries as $country){ ?>
                                <option value="<?php echo $country->country_id; ?>" ><?php echo $country->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_state_id" title="<?php echo JText::_('COM_JEPROSHOP_STATE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STATE_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <select id="jform_state_id" name="jform[state_id]" >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_ALL_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_zipcode" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_zipcode" name="jform[zipcode]" required="required" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_behavior" title="<?php echo JText::_('COM_JEPROSHOP_BEHAVIOR_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_BEHAVIOR_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_behavior" name="jform[behavior]"  >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_THIS_TAX_ONLY_LABEL'); ?></option>
                            <option value="1" ><?php echo JText::_('COM_JEPROSHOP_COMBINE_LABEL'); ?></option>
                            <option value="2" ><?php echo JText::_('COM_JEPROSHOP_ONE_AFTER_ANOTHER_LABEL'); ?></option>
                        </select>
                        <!--p>$this->l('You must define the behavior if an address matches multiple rules:').'<br>',
                            $this->l('- This tax only: Will apply only this tax').'<br>',
                            $this->l('- Combine: Combine taxes (e.g.: 10% + 5% = 15%)').'<br>',
                            $this->l('- One after another: Apply taxes one after another (e.g.: 0 + 10% = 0 + 5% = 5.5)')</p-->
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_tax_id" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_TAX_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_tax_id" name="jform[tax_id]"  >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_NO_TAX_LABEL'); ?></option>
                            <?php foreach($this->taxes as $tax){ ?>
                            <option value="<?php echo $tax->tax_id; ?>" ><?php echo ucfirst($tax->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_description" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></label></div>
                    <div class="controls" ><textarea id="jform_description" name="jform[description]" ></textarea></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="tax_rules_group_id" value="<?php echo $this->tax_rules_group_id; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>