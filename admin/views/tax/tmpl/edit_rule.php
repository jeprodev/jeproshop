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
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('rules'); ?></div>
        <div class="panel" >
            <div class="panel-title" ></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_name" name="jform[name]" required="required" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_published" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('published', 'add', 1); ?></div>
                </div>
                <?php if(JeproshopShopModelShop::isFeaturePublished()){ ?>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_associated_shop" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->associatedShop; ?></div>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="panel" >
            <div class="panel-title" ><i class="icon-money" ></i> <?php echo JText::_('COM_JEPROSHOP_NEW_TAX_RULE_LABEL'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_country" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></div>
                    <div class="controls" >
                        <select id="jform_country" name="jform[country]" >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_ALL_COUNTRIES_LABEL'); ?></option>
                            <?php foreach ($this->countries as $country){ ?>
                            <option value="<?php echo $country->country_id; ?>" ><?php echo ucfirst($country->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_state_id" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STATES_LABEL'); ?></div>
                    <div class="controls" >
                        <select id="jform_state_id" multiple="multiple" ></select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_zip_code" title="<?php echo JText::_('COM_JEPROSHOP_YOU_CAN_DEFINE_A_RANGE_OF_ZIP_CODES_OR_SIMPLE_USE_ONE_ZIP_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_RANGE_LABEL'); ?></div>
                    <div class="controls" ><input type="text" id="jform_zip_code" name="jform[zip_code]"  value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_behavior" title="<?php echo JText::_('COM_JEPROSHOP_YOU_MUST_DEFINE_THE_BEHAVIOR_IF_AN_ADDRESS_MATCHES_MULTIPLE_RULES_TITLE_DESC') . '<br />' . JText::_('COM_JEPROSHOP_THIS_TAX_ONLY_TITLE_DESC') . '<br />' . JText::_('COM_JEPROSHOP_COMBINE_TAX_MESSAGE_TITLE_DESC') . '<br />' .JText::_('COM_JEPROSHOP_ONE_AFTER_ANOTHER_TAX_MESSAGE_TITLE_DESC') . '<br />'; ?>" ><?php echo JText::_('COM_JEPROSHOP_BEHAVIOR_LABEL'); ?></div>
                    <div class="controls" >
                        <select id="jform_behavior" name="jform[behavior]" >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_THIS_TAX_ONLY_LABEL') ?></option>
                            <option value="1" ><?php echo JText::_('COM_JEPROSHOP_COMBINE_LABEL') ?></option>
                            <option value="2" ><?php echo JText::_('COM_JEPROSHOP_ONE_AFTER_ANOTHER_LABEL') ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_tax_id" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_TAXES_LABEL'); ?></div>
                    <div class="controls" >
                        <select id="jform_tax_id" name="jform[tax_id]" >
                            <option value="0" ><?php echo JText::_('COM_JEPROSHOP_NO_TAX_LABEL'); ?></option>
                            <?php foreach($this->taxes as $tax){ ?>
                            <option value="<?php echo $tax->tax_id; ?>" ><?php echo ucfirst($tax->name); ?></option>
                            <?php }  ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_description" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></div>
                    <div class="controls" ><input type="text" id="jform_description" name="jform[description]"  /></div>
                </div>
            </div>
        </div>
        <div class="panel" >
            <div class="panel-content" >
                <table class="table table-striped" >
                    <thead>
                        <tr>
                            <th width="1%" class="nowrap center" >#</th>
                            <th width="" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></th>
                            <th width="" class="nowrap" ><?php echo ucfirst(JText::_('COM_JEPROSHOP_STATES_LABEL')); ?></th>
                            <th width="" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_LABEL'); ?></th>
                            <th width="" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_BEHAVIOR_LABEL'); ?></th>
                            <th width="" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_TAXES_LABEL'); ?></th>
                            <th width="" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></th>
                            <th width="" class="nowrap" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                        </tr>
                    </thead>
                    <tboby>
                        <?php if(isset($this->tax_rules)){
                            foreach($this->tax_rules as $index => $tax_rule){
                                ?>
                         <tr class="row_<?php echo ($index%2); ?>" >
                            <td class="nowrap" ></td>
                            <td class="nowrap" ><?php echo ucfirst($tax_rule->country_name); ?></td>
                            <td class="nowrap" ><?php echo ucfirst($tax_rule->state_name); ?></td>
                            <td class="nowrap" ><?php echo $tax_rule->zipcode;?></td>
                            <td class="nowrap" ><?php echo $tax_rule->behavior; ?></td>
                            <td class="nowrap" ><?php echo $tax_rule->rate; ?></td>
                            <td class="nowrap" ><?php echo $tax_rule->description; ?></td>
                            <td class="nowrap" ></td>
                         </tr>
                        <?php
                            }
                        }else{

                        } ?>
                    </tboby>
                </table>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>