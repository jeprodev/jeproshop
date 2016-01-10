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
		<table class="table table-striped" id="taxGroupList" >
			<thead>
                <tr>
                	<th width="1%" class="nowrap center hidden-phone" >#</th>
                    <th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="3%" class="nowrap " ><?php echo JText::_('JSTATUS'); ?></th>
                    <th width="60%" class="nowrap " ><?php echo JText::_('COM_JEPROSHOP_TAX_RULES_NAME_LABEL'); ?></th>
                    <th width="5%" class="nowrap " ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                </tr>
            </thead>
            <tbody>
            <?php if(isset($this->tax_rules_groups)){
                foreach($this->tax_rules_groups as $index => $tax_rules_group){
                    $taxRulesGroupLink = JRoute::_('index.php?option=com_jeproshop&view=tax&task=edit_rules_group&tax_rules_group_id=' . (int)$tax_rules_group->tax_rules_group_id . '&' . JeproshopTools::getTaxToken() . '=1');
                    $taxRulesGroupDeleteLink = JRoute::_('index.php?option=com_jeproshop&view=tax&task=delete_rules_group&tax_rules_group_id=' . (int)$tax_rules_group->tax_rules_group_id . '&' . JeproshopTools::getTaxToken() . '=1');
                    ?>
                    <tr class="row_<?php echo ($index%2); ?>" >
                        <td width="1%" class="nowrap center hidden-phone"><?php echo $index +1; ?></td>
                        <td width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $tax_rules_group->tax_rules_group_id); ?></td>
                        <td width="3%" class="nowrap center"><i class="icon-<?php echo ($tax_rules_group->published ? '' : 'un') . 'publish'; ?>" ></i> </td>
                        <td width="60%" class="nowrap"><a href="<?php echo $taxRulesGroupLink; ?>" ><?php echo ucfirst($tax_rules_group->name); ?></a> </td>
                        <td width="5%" class="nowrap">
                        <span class="pull-right" >
                            <div class="btn-group-action" >
                                <div class="btn-group pull-right" >
                                    <a href="<?php echo $taxRulesGroupLink; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                    <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $taxRulesGroupDeleteLink; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DELETE_LABEL') . $tax->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); }" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
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
        </table>
		<input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>