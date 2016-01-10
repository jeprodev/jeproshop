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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" method="post" name="adminForm" id="adminForm" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-order" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=invoices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-bill" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_BILLS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=returns'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-returns" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=delivery'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-delivery" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_DELIVERY_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=refund'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-refund" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_REFUND_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=status'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order-status" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=messages'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-messages" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGES_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel" >
    		<div class="panel-content" >
    			<table class="table table-striped" id="addressList">
            		<thead>
                		<tr>
                    		<th class="nowrap center" width="1%">#</th>
                    		<th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th> 
                    		<th width="6%" class="nowrap"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_REFERENCE_LABEL'), 'o.reference', 'ASC'); ?></th>
                    		<th width="2%" class="nowrap center hidden-phone"><?php echo JText::_('COM_JEPROSHOP_NEW_CLIENT_LABEL'); ?></th>
                    		<th width="1%" class="nowrap hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_DELIVERY_LABEL'), 'o.delivery', 'ASC'); ?></th>
                    		<th width="5%" class="nowrap hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_CUSTOMER_LABEL'), 'o.customer', 'ASC'); ?></th>
                    		<th width="3%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_TOTAL_LABEL'), 'o.total', 'ASC'); ?></th>
                    		<th width="4%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_PAYMENT_LABEL'), 'o.payment', 'ASC'); ?></th>
                    		<th width="8%" class="nowrap hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_STATUS_LABEL'), 'o.status', 'ASC'); ?></th>
                    		<th width="3%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_CREATION_DATE_LABEL'), 'o.date', 'ASC'); ?></th>
                    		<th width="3%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_ACTIONS_LABEL'), 'o.action', 'ASC'); ?></th>
                    	</tr>
                    </thead>
                    <tbody>
                    	<?php if(empty($this->orders)){ ?>
                		<tr><td colspan="11" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td></tr>
                		<?php } else { 
                    		foreach($this->orders as $index => $order){ 
								$order_link = JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . (int)$order->pdf_id . '&' . JSession::getFormToken() . '=1');  
								$customer_link = JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id=' . (int)$order->customer_id . '&' . JSession::getFormToken() . '=1');
						?>
                		<tr class="row_<?php echo $index % 2; ?>" >
                    		<td width="1%" class="nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                    		<td width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $order->pdf_id); ?></td>
                    		<td width="6%" class="nowrap "><a href="<?php echo $order_link ; ?>" ><?php echo $order->reference; ?></a></td>
                    		<td width="2%" class="nowrap center hidden-phone"><?php echo ($order->new ? '<i class="icon-publish" ></i>' : '<i class="icon-unpublish" ></i>'); ?></td>
                    		<td width="4%" class="nowrap hidden-phone"><?php echo $order->country_name; ?></td>
                    		<td width="5%" class="nowrap hidden-phone"><a href="<?php echo $customer_link; ?>" ><?php echo $order->customer_name; ?></a></td>
                    		<td width="2%" class="nowrap center hidden-phone"><?php echo JeproshopTools::displayPrice($order->total_paid_tax_incl); ?></td>
                    		<td width="5%" class="nowrap center hidden-phone"><?php echo $order->payment; ?></td>
                    		<td width="1%" class="nowrap hidden-phone"><?php echo $order->order_status_name; ?></td>
                    		<td width="1%" class="nowrap center hidden-phone"><?php echo JeproshopTools::displayDate($order->date_add); ?></td>
                    		<td width="1%" class="nowrap center hidden-phone" >
                    			<div class="btn-group-action" >
                            		<div class="btn-group" >
                                		<a href="<?php echo $order_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                	</div>
                                </div>
                    		</td>
                		</tr>
                		<?php } 
                		} ?>
            		</tbody>
            		<tfoot><tr><td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
</form>