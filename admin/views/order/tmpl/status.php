<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package      com_jeproshop
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
<div class="form_box" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=invoices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-bill" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_BILLS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=returns'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-returns" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=delivery'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-delivery" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_DELIVERY_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=refund'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-refund" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_REFUND_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=status'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-order-status" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=messages'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-messages" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGES_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="box_wrapper panel" >
            <div class="panel-title" ></div>
            <div class="panel-content" >
                <table  class="table table-striped" >
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap" >#</th>
                        <th width="1%" class="nowrap" ><?php echo JHtml::_('grid.checkall'); ?></th>
                        <th width="45%" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                        <th width="8%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_ICON_LABEL'); ?></th>
                        <th width="5%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_SEND_EMAIL_TO_CUSTOMER_LABEL'); ?></th>
                        <th width="5%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_DELIVERY_LABEL'); ?></th>
                        <th width="5%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_INVOICE_LABEL'); ?></th>
                        <th width="1%" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_EMAIL_TEMPLATE_LABEL'); ?></th>
                        <th width="1%" class="nowrap " ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($this->orderStatusList)){ ?>
                        <tr><td colspan="11" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td></tr>
                    <?php } else {
                    foreach($this->orderStatusList as $index => $status){
                        $order_link = JRoute::_('index.php?option=com_jeproshop&view=order&task=view_status&order_status_id=' . (int)$status->order_status_id . '&' . JeproshopTools::getOrderFormToken() . '=1');
                        $deleteLink = JRoute::_('index.php?option=com_jeproshop&view=order&task=delete_status&order_status_id=' . (int)$status->order_status_id . '&' . JeproshopTools::getOrderFormToken() . '=1');
                    ?>
                        <tr class="row_<?php echo $index % 2; ?>" >
                            <td width="1%" class="nowrap center" ><?php echo $index + 1; ?></td>
                            <td width="1%" class="nowrap center" style="padding-bottom: 8px;"><?php echo JHtml::_('grid.id', $index, $status->order_status_id); ?></td>
                            <td width="45%" class="nowrap" ><?php echo $status->name; ?></td>
                            <td width="8%" class="nowrap center" ></td>
                            <td width="1%" class="nowrap center" ><i class="icon-<?php echo $status->send_email ? 'publish' : 'unpublish'; ?>" ></i> </td>
                            <td width="1%" class="nowrap center" ><i class="icon-<?php echo $status->delivery ? 'publish' : 'unpublish'; ?>" ></i> </td>
                            <td width="1%" class="nowrap center" ><i class="icon-<?php echo $status->invoice ? 'publish' : 'unpublish'; ?>" ></i> </td>
                            <td width="1%" class="nowrap center" ><?php echo $status->template; ?></td>
                            <td width="1%" class="nowrap " >
                                <div class="btn-group pull-right" >
                                    <a href="<?php echo $order_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?></a>
                                    <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $deleteLink; ?>" ><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="clear:both; margin:25px 0" ></div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_STATUS_LIST_LABEL'); ?></div>
            <div class="panel-content " >
                <table class="table table-striped" id="return_status_list">
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap" >#</th>
                        <th width="1%" class="nowrap" ><?php echo JHtml::_('grid.checkall'); ?></th>
                        <th width="45%" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                        <th width="5%" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($this->returnStatues)){ ?>
                        <tr><td colspan="11" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td></tr>
                    <?php } else {
                    foreach($this->returnStatues as $index => $status){
                        $returnStatusLink = JRoute::_('index.php?option=com_jeproshop&view=order&task=status&order_return_status_id=' . (int)$status->order_return_status_id . '&' . JeproshopTools::getOrderFormToken() . '=1');
                        $deleteReturnStatusLink = JRoute::_('index.php?option=com_jeproshop&view=order&task=view_return_status&order_return_status_id=' . (int)$status->order_return_status_id . '&' . JeproshopTools::getOrderFormToken() . '=1');
                    ?>
                    <tr class="row_<?php echo $index % 2; ?>" >
                        <td width="1%" class="nowrap center" ><?php echo $index + 1; ?></td>
                        <td width="1%" class="nowrap center" style="padding-bottom: 8px;"><?php echo JHtml::_('grid.id', $index, $status->order_return_status_id); ?></td>
                        <td width="45%" class="nowrap" ><?php echo $status->name; ?></td>
                        <td width="1%" class="nowrap " >
                            <div class="btn-group pull-right" >
                                <a href="<?php echo $returnStatusLink; ?>" class="btn btn-micro" ><i class="icon-edit" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>