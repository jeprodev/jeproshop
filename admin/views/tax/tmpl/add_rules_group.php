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
            <div class="panel-title" ><i class="icon-money" ></i> <?php echo JText::_('COM_JEPROSHOP_TAX_RULES_GROUP_LABEL'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_name" name="jform[name]" required="required" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_published" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('published', 'add', 1); ?></div>
                </div>
            </div>
        </div>
        <!--div class="panel" >
            <div class="panel-content well" >
                <table class="table striped-table" >
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone" >#</th>
                        <th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('grid.checkall'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_STATE_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_BEHAVIOR_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_TAX_RATE_LABEL'); ?></th>
                        <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></th>
                        <th class="nowrap" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($this->tax_rules)){
                        foreach($this->tax_rules as $index => $tax_rule){
                            $taxLink = JRoute::_('index.php?option=com_jeproshop&view=tax&task=edit_rule&tax_rule_id=' . (int)$tax_rule->tax_rule_id . '&' .JeproshopTools::getTaxToken() . '=1', true, 1);
                            $deleteTaxLink = JRoute::_('index.php?option=com_jeproshop&view=tax&task=delete_rule&tax_rule_id=' . (int)$tax_rule->tax_rule_id . '&' .JeproshopTools::getTaxToken() . '=1', true, 1);
                            ?>
                            <tr class="row_<?php echo ($index%2); ?>" >
                                <td width="1%" class="nowrap center hidden-phone"><?php echo $index +1; ?></td>
                                <td width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $tax_rule->tax_rule_id); ?></td>
                                <td width="4%" class="nowrap"><a href="<?php echo $taxLink; ?>" ><?php echo ucfirst($tax_rule->name); ?></a> </td>
                                <td width="60%" class="nowrap"></td>
                                <td width="5%" class="nowrap center"><?php echo $tax_rule->rate; ?></td>
                                <td width="5%" class="nowrap">
                                    <span class="pull-right" >
                                        <div class="btn-group-action" >
                                            <div class="btn-group pull-right" >
                                                <a href="<?php echo $taxLink; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                                <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="#" title="<?php echo JText::_('COM_JEPROSHOP_DUPLICATE_LABEL'); ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_COPY_IMAGES_TOO_LABEL'); ?>')) document.location ='<?php echo $copyImageTooLink; ?>'; else document.location = '<?php echo $duplicate_product_link; ?>'; return false; ">
                                                            <i class="icon-copy" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_DUPLICATE_LABEL'); ?>
                                                        </a>
                                                    </li><li class="divider" ></li>
                                                    <li><a href="<?php echo $deleteTaxLink; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DELETE_LABEL') . $tax->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); }" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </span>
                                </td>
                            </tr>
                        <?php }
                    }else{

                    } ?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div -->
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>