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
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=invoices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-bill" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_BILLS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=returns'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-returns" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=delivery'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-delivery" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_DELIVERY_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=refund'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-refund" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_REFUND_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=status'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order-status" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=messages'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-messages" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGES_LABEL'); ?></a>
            </fieldset>
        </div>
        <table class="table striped-table" >
            <thead>
                <tr>
                    <th class="nowrap center" width="1%" >#</th>
                    <th class="nowrap" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_ORDER_REFERENCE_LABEL'); ?></th>
                    <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_STATUS_LABEL'); ?></th>
                    <th class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_DATE_ISSUED_LABEL'); ?></th>
                    <th class="nowrap" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($this->order_returns)){
                    foreach($this->order_returns as $index => $order_return){
                ?>
                <tr class="row_<?php echo $index%2; ?>" >
                    <td width="1%" class="nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                    <td width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $order_return->order_return_id); ?></td>
                    <td width="1%" class="nowrap" ></td>
                    <td class="nowrap" ></td>
                    <td class="nowrap" ></td>
                    <td class="nowrap" ></td>
                </tr>
                <?php  }
                }else{ ?>
                <tr><td colspan="6" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task" value="" />
</form>