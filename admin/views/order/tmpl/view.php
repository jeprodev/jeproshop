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
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-order" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=invoices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-bill" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_BILLS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=returns'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-returns" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=delivery'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-delivery" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_DELIVERY_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=refund'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-refund" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_REFUND_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=status'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order-status" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=messages'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-messages" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGES_LABEL'); ?></a>
            </fieldset>
        </div>
    	<div class="box_wrapper" >
			<div class="half_wrapper_left" >
				<div class="panel" >
					<div class="panel-title" >
						<i class="icon-credit-card"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_ORDER_LABEL')); ?> 
						<span class="badge badge-success"> <?php echo strtoupper($this->order->reference); ?></span>
						<span class="badge badge-success"> <?php echo '#' . $this->order->order_id; ?> </span>
						<span class="panel-heading-action">
							<span class="btn-group pull-right">
								<a class="btn btn-default btn-micro" href="<?php echo JRoute::_('index.php?option=com_jeproshop&task=view&order_id=' . (int)$this->previousOrder); ?>" >
									<i class="icon-backward"></i>
								</a>
								<a class="btn btn-default btn-micro" href="<?php echo JRoute::_('index.php?option=com_jeproshop&task=view&order_id=' . (int)$this->nextOrder); ?>" >
									<i class="icon-forward "></i>
								</a>
							<span>
						</span>
					</div>
					<div class="panel-content well" >
						<?php echo JHtml::_('bootstrap.startTabSet', 'order_form', array('active' =>'status')); 
						echo JHtml::_('bootstrap.addTab', 'order_form', 'status', '<i class="icon-time"></i> ' .JText::_('COM_JEPROSHOP_ORDER_STATUS_TAB_LABEL') . ' <span class="badge badge-success">' . count($this->history) . '</span>'); ?> 
						<h4 class="visible-print"><?php echo JText::_('COM_JEPROSHOP_STATUS_LABEL'); ?> <span class="badge badge-success" ><?php echo '(' . count($this->history) . ')'; ?></span></h4>
						<!-- History of status -->
						<div class="table-responsive" >
							<table class="table history-status row-margin-bottom">
								<tbody>
									<?php foreach($this->history as $key => $row){
										if($key == 0){ ?>
									<tr>
										<td style="background-color:<?php echo $row->color; ?>"><img src="<?php echo JURI::root() . 'media/com_jeproshop/images/order_status/' . $row->order_status_id . '.gif'; ?>" width="16" height="16" alt="<?php echo stripslashes($row->order_state_name); ?>" /></td>
										<td style="background-color:<?php echo $row->color; ?>; color:<?php echo $row->text_color; ?>"><?php echo stripslashes($row->order_state_name); ?></td>
										<td style="background-color:<?php echo $row->color; ?>; color:<?php echo $row->text_color; ?>"><?php if($row->employee_lastname){ echo stripslashes($row->employee_firstname) . ' ' . stripslashes($row->employee_lastname) . ') '; } ?></td>
										<td style="background-color:<?php echo $row->color; ?>; color:<?php echo $row->text_color; ?>"><?php echo JeproshopTools::dateFormat($row->date_add, true); ?></td>
									</tr>
									<?php }else{ ?>
									<tr>
										<td><img src="<?php echo JURI::root() . 'media/com_jeproshop/images/order_status/' . $row->order_state_id . '.gif'; ?>" width="16" height="16" /></td>
										<td><?php echo stripslashes($row->ostate_name); ?></td>
										<td><?php if($row->employee_lastname){ echo stripslashes($row->employee_firstname) . ' ' . stripslashes($row->employee_lastname); }else{ ?>&nbsp;<?php } ?></td>
										<td><?php echo JeproshopTools::dateFormat($row->date_add, true); ?></td>
									</tr>
									<?php } 
									} ?>
								</tbody>
							</table>
						</div>
						<!-- Change status form -->
						<!--form action="<?php echo JText::_('index.php?option=com_jeproshop&view=order'); ?>" method="post" class="form-horizontal well hidden-print" -->
							<div class="control-group" >
								<select id="jform_order_status_id" name="jform[order_status_id]" >
									<?php foreach($this->order_statues as $status){  ?>
									<option value="<?php echo $status->order_status_id; ?>" <?php if($status->order_status_id == $this->current_status){ ?> selected="selected" disabled="disabled" <?php } ?>><?php echo $status->name; ?></option>
									<?php } ?>
								</select>&nbsp;
								<button type="button" name="submit_state" class="btn btn-primary" id="jform_update_order_status" >
									<?php echo JText::_('COM_JEPROSHOP_UPDATE_STATUS_LABEL'); ?>
								</button>
								<input type="hidden" name="jform[order_id]" value="<?php echo $this->order->order_id; ?>" />&nbsp;																							
							</div>
						<!--/form -->
						<?php 
						echo JHtml::_('bootstrap.endTab'); 
						echo JHtml::_('bootstrap.addTab', 'order_form', 'documents', '<i class="icon-file-text"></i> ' . JText::_('COM_JEPROSHOP_ORDER_DOCUMENTS_TAB_LABEL') . ' <span class="badge badge-success">' . count($this->order->getDocuments()) . '</span>'); ?>
						<?php echo $this->loadTemplate('documents'). JHtml::_('bootstrap.endTab'); 
						echo JHtml::_('bootstrap.endTabSet'); ?>
					</div>
				</div>
				<div class="panel" >
					<div class="panel-title" > <i class="icon-truck"  ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_SHIPPING_TAB_LABEL')); ?></div>
					<div class="panel-content well" >{$HOOK_TAB_SHIP}
						<?php echo JHtml::_('bootstrap.startTabSet', 'shipping_form', array('active' =>'shipping')); 
						echo JHtml::_('bootstrap.addTab', 'shipping_form', 'shipping', '<i class="icon-truck"></i> ' .JText::_('COM_JEPROSHOP_SHIPPING_TAB_LABEL') . ' <span class="badge badge-success">' . count($this->order->getShipping()) . '</span>'); ?> 
						<h4 class="visible-print"><?php echo JText::_('COM_JEPROSHOP_SHIPPING_LABEL'); ?> <span class="badge badge-success">(<?php echo count($this->order->getShipping()); ?>)</span></h4>
						<!-- Shipping block -->
						<?php if(!$this->order->isVirtual()){ ?>
						<div class="form-horizontal" >
							<?php if($this->order->gift_message){ ?>
							<div class="control-group" >
								<div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_MESSAGE_LABEL'); ?></label></div>
								<div class="controls">
									<p class="form-control-static"><?php echo nl2br($this->order->gift_message); ?></p>
								</div>
							</div>
							<?php } ?>
							<div class="table-responsive">
								<table class="table table-striped" id="shipping_table">
									<thead>
										<tr>
											<th><span class="nowrap "><?php echo JText::_('COM_JEPROSHOP_DATE_LABEL'); ?></span></th>
											<th><span class="nowrap "><?php echo JText::_('COM_JEPROSHOP_TYPE_LABEL'); ?></span></th>
											<th><span class="nowrap "><?php echo JText::_('COM_JEPROSHOP_CARRIER_LABEL'); ?></span></th>
											<th><span class="nowrap center "><?php echo JText::_('COM_JEPROSHOP_WEIGHT_LABEL'); ?></span></th>
											<th><span class="nowrap center "><?php echo JText::_('COM_JEPROSHOP_SHIPPING_COST_LABEL'); ?></span></th>
											<th><span class="nowrap center"><?php echo JText::_('COM_JEPROSHOP_TRACKING_NUMBER_LABEL'); ?></span></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($this->order->getShipping() as $shipping){ ?>
										<tr>
											<td class="nowrap" ><?php echo JeproshopTools::dateFormat($shipping->date_add, true); ?></td>
											<td class="nowrap" ><?php echo $shipping->type; ?></td>
											<td class="nowrap" ><?php echo $shipping->carrier_name; ?></td>
											<td class="nowrap weight center"><?php echo $shipping->weight . ' ' . JeproshopSettingModelSetting::getValue('weight_unit'); ?></td>
											<td class="nowrap center">
												<?php if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_INCLUDED){
													echo JeproshopTools::displayPrice($shipping->shipping_cost_tax_incl, $this->currency->currency_id);
												}else{
													echo JeproshopTools::displayPrice($shipping->shipping_cost_tax_excl, $this->currency->currency_id);
												} ?>
											</td>
											<td class="nowrap actions pull-right">
												<span id="shipping_number_show"><?php if($shipping->url && $shipping->tracking_number){ ?><a target="_blank" href="{$line.url|replace:'@':$line.tracking_number}"><?php echo $shipping->tracking_number; ?></a><?php }else{ echo $shipping->tracking_number; } ?></span>
												<?php if($shipping->can_edit){ ?>
												<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;order_id=' . (int)$order->order_id); ?>">
													<span class="shipping_number_edit" style="display:none;">
														<input type="hidden" name="jform[order_carrier_id]" value="<?php echo htmlentities($shipping->order_carrier_id); ?>" />
														<input type="text" name="jform[tracking_number]" value="<?php echo htmlentities($shipping->tracking_number); ?>" />
														<button type="submit" class="btn btn-default" name="submitShippingNumber">
															<i class="icon-ok"></i> <?php echo JText::_('COM_JEPROSHOP_UPDATE_LABEL'); ?>
														</button>
													</span>
													<a href="#" class="edit_shipping_number_link btn btn-default btn-micro">
														<i class="icon-pencil"></i> <?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?>
													</a>
													<a href="#" class="cancel_shipping_number_link btn btn-default btn-micro" style="display: none;">
														<i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
													</a>
												</form>
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php if($this->carrier_module_call){ echo $this->carrier_module_call; } ?>
							<hr />
							<?php  if($this->order->recyclable){ ?>
							<span class="label label-success"><i class="icon-check"></i> <?php echo JText::_('COM_JEPROSHOP_RECYCLED_PACKAGING_LABEL'); ?></span>
							<?php }else{?>
							<span class="label label-inactive"><i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_RECYCLED_PACKAGING_LABEL'); ?></span>
							<?php } 

							if($this->order->gift){ ?>
							<span class="label label-success"><i class="icon-check"></i> <?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_LABEL'); ?></span>
							<?php }else{ ?>
							<span class="label label-inactive"><i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_LABEL'); ?></span>
							<?php } ?>
						</div>
						<?php } 
						echo JHtml::_('bootstrap.endTab'); 
						echo JHtml::_('bootstrap.addTab', 'shipping_form', 'documents', '<i class="icon-file-text"></i> ' . JText::_('COM_JEPROSHOP_ORDER_RETURNS_TAB_LABEL') . ' <span class="badge badge-success">' . count($this->order->getReturn()) . '</span>'); ?>
						<h4 class="visible-print"><?php echo JText::_('COM_JEPROSHOP_MERCHANDISE_RETURNS_LABEL'); ?> <span class="badge badge-success"><?php echo '(' . count($this->order->getReturn()) . ')'; ?></span></h4>
						<?php if(!$this->order->isVirtual()){ ?>
						<!-- Return block -->
							<?php if(count($this->order->getReturn())> 0){ ?>
							<div class="table-responsive">
								<table class="table" >
									<thead>
										<tr>
											<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_DATE_LABEL'); ?></span></th>
											<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_TYPE_LABEL'); ?></span></th>
											<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_CARRIER_LABEL'); ?></span></th>
											<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_TRACKING_NUMBER_LABEL'); ?></span></th>
										</tr>
									</thead>
								</table>
							</div>
							<?php } else { ?>
							<div class="list-empty hidden-print">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i> <?php echo JText::_('COM_JEPROSHOP_NO_MERCHANDISE_RETURNED_YET_LABEL'); ?>
								</div>
							</div>
							<?php } 
							if($this->carrier_module_call){ echo $this->carrier_module_call; } ?>
						<?php } ?>
						<?php echo JHtml::_('bootstrap.endTab'); 
						echo JHtml::_('bootstrap.endTabSet'); ?>
					</div>
				</div>
				<div class="panel" >
					<div class="panel-title" >
						<i class="icon-money"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_PAYMENT_LABEL')); ?> 
						<span class="badge badge-success" ><?php echo count($this->order->getOrderPayments()); ?></span>
					</div>
					<div class="panel-content well" >
						<?php if(count($this->order->getOrderPayments()) > 0){ ?>
						<p class="alert alert-danger" style="<?php if(round($this->orders_total_paid_tax_incl, 2) == round($this->total_paid, 2) || $this->currentState->order_state_id == 6){ ?> display: none; <?php } ?>" >
							<?php echo JText::_('COM_JEPROSHOP_WARNING_LABEL'); ?>
							<strong><?php echo JeproshopTools::displayPrice($this->total_paid, $this->currency->currency_id); ?></strong>
							<?php echo JText::_('COM_JEPROSHOP_PAID_INSTEAD_OF_LABEL'); ?>
							<strong class="total_paid"><?php echo JeproshopTools::displayPrice($this->orders_total_paid_tax_incl, $this->currency->currency_id); ?></strong>
							<?php $current_index = 0;
							foreach($this->order->getBrother() as $brother_order){ 
								if($current_index == 0){
									if(count($this->order->getBrother()) == 1){ ?>
							<br /><?php echo JText::_('COM_JEPROSHOP_THIS_WARNING_ALSO_CONCERNS_ORDER_MESSAGE'); ?>
								<?php } else{?>
							<br /><?php echo JText::_('COM_JEPROSHOP_THIS_WARNING_ALSO_CONCERNS_THE_NEXT_ORDERS_MESSAGE'); ?>
								<?php }
								} ?>							
							<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . $this->brother_order->order_id . '&' . JSession::getFormToken() . '=1'); ?>" >
								#{'%06d'|sprintf:$brother_order->order_id}
							</a>
							<?php } ?>
						</p>
						<?php } ?>
						<form id="jform_form_add_payment"  method="post" action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . $this->order->order_id . '&' . JSession::getFormToken() . '=1'); ?>" >
							<div class="table-responsive" >
								<table class="table" >
									<thead>
										<tr>
											<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_DATE_LABEL')); ?></span></th>
											<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_PAYMENT_METHOD_LABEL')); ?></span></th>
											<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_TRANSACTION_LABEL')); ?></span></th>
											<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_AMOUNT_LABEL')); ?></span></th>
											<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_INVOICE_LABEL')); ?></span></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php $payments = $this->order->getOrderPaymentCollection();
										if($payments){
											foreach($payments as $payment){ ?>
										<tr>
											<td><?php echo JeproshopTools::dateFormat($payment->date_add, true); ?></td>
											<td><?php echo $payment->payment_method; ?></td>
											<td><?php echo $payment->transaction_id; ?></td>
											<td><?php echo JeproshopTools::displayPrice($payment->amount, $payment->currency_id); ?></td>
											<td>
												<?php if($invoice = $payment->getOrderInvoice($this->order->order_id)){
													echo $invoice->getInvoiceNumberFormatted($this->current_lang_id, $this->order->shop_id);
												}else{
												} ?>
											</td>
											<td class="actions">
												<button class="btn btn-default open_payment_information">
													<i class="icon-search"></i> <?php echo JText::_('COM_JEPROSHOP_DETAILS_LABEL'); ?>											
												</button>
											</td>
										</tr>
										<tr class="payment_information" style="display: none;">
											<td colspan="5">
												<p>
													<b><?php echo JText::_('COM_JEPROSHOP_CARD_NUMBER_LABEL'); ?></b>&nbsp;
													<?php if($payment->card_number){ echo $payment->card_number; }else{  ?>
													<i><?php echo JText::_('COM_JEPROSHOP_NOT_DEFINED_LABEL'); ?></i>
													<?php } ?>
												</p>
												<p>
													<b><?php echo JText::_('COM_JEPROSHOP_CARD_BRAND_LABEL'); ?></b>&nbsp;
													<?php if($payment->card_brand){
														echo $payment->card_brand;
													}else{ ?>
													<i><?php echo JText::_('COM_JEPROSHOP_NOT_DEFINED_LABEL'); ?></i>
													<?php } ?>
												</p>
												<p>
													<b><?php echo JText::_('COM_JEPROSHOP_CARD_EXPIRATION_LABEL'); ?></b>&nbsp;
													<?php if($payment->card_expiration){
                                                        echo $payment->card_expiration;
													}else{ ?>
													<i><?php echo JText::_('COM_JEPROSHOP_NOT_DEFINED_LABEL'); ?></i>
													<?php } ?>
												</p>
												<p>
													<b><?php echo JText::_('COM_JEPROSHOP_CARD_HOLDER_LABEL'); ?></b>&nbsp;
													<?php if($payment->card_holder){ echo $payment->card_holder; 
													}else{ ?>
													<i><?php echo JText::_('COM_JEPROSHOP_NOT_DEFINED_LABEL'); ?></i>
													<?php } ?>
												</p>
											</td>
										</tr>
										<?php }
										} else { ?>
										<tr>
											<td class="list-empty hidden-print" colspan="6">
												<div class="list-empty-msg">
													<i class="icon-warning-sign list-empty-icon"></i>
													<?php echo JText::_('COM_JEPROSHOP_NO_PAYMENT_METHODS_ARE_AVAILABLE_LABEL'); ?>
												</div>
											</td>
										</tr>
										<?php } ?>
										<tr class="current-edit hidden-print">
											<td>
												<div class="input-append ">
													<input type="text" name="jform[payment_date]" id="jform_payment_date" class="datepicker input_date_box hasTooltip" value="<?php echo date('Y-m-d'); ?>" size="22" />
													<button type="button" class="btn" ><i class="icon-calendar"></i> </button>
												</div>
											</td>
											<td>
												<select name="jform[payment_method]" class="payment_method " >
													<?php foreach($this->payment_methods as $payment_method){ ?>
													<option value="<?php echo $payment_method; ?>"><?php echo $payment_method; ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<input type="text" name="jform[payment_transaction_id]" value="" class="form-control price_box" />
											</td>
											<td>
												<input type="text" name="jform[payment_amount]" value="" class="price_box pull-left" />&nbsp;
												<select name="payment_currency" class="payment_currency form-control small_box pull-left currency_sign">
													<?php foreach($this->currencies as $currency){ ?>
													<option value="<?php echo $currency->currency_id; ?>" <?php if($currency->currency_id == $this->currency->currency_id){ ?> selected="selected" <?php } ?> ><?php echo $currency->sign; ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<?php if(count($this->invoices_collection) > 0){ ?>
												<select name="jform[payment_invoice]" id="jform_payment_invoice">
													<?php foreach($this->invoices_collection as $invoice){ ?>
													<option value="<?php echo $invoice->invoice_order_id; ?>" selected="selected"><?php echo $invoice->getInvoiceNumberFormatted($this->current_lang_id, $this->order->shop_id); ?></option>
													<?php } ?>
												</select>
												<?php } ?>
											</td>
											<td class="actions">
												<button class="btn btn-primary btn-block" type="submit" name="submitAddPayment">
													&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_ADD_LABEL')); ?>&nbsp;
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</form>
						<?php if(!$this->order->valid && sizeof($this->currencies) > 1){ ?>
						<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&order_id=' . (int)$this->order->order_id . '&' . JSesion::getFormToken() . '=1'); ?>" class="form-horizontal well" method="post" >
							<div class="control-group">
								<div class="control-label" ><label class=""><?php echo JText::_('COM_JEPROSHOP_CHANGE_CURRENCY_LABEL'); ?></label></div>
								<div class="controls" >
									<select name="new_currency">
										<?php foreach($this->currencies as $currency_change){
											if($currency_change->currency_id != $this->order->currency_id){ ?>
										<option value="<?php echo $currency_change->currency_id; ?>"><?php echo $currency_change->name . ' - ' . $currency_change->sign; ?></option>
										<?php }
										}?>								
									</select>
									<p class="help-block"><?php echo JText::_('COM_JEPROSHOP_DO_NOT_FORGOT_TO_UPDATE_YOUR_EXCHANGE_RATE_BEFORE_MAKING_THIS_CHANGE_MESSAGE'); ?></p>
								</div>
							</div>
							<button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> <?php echo JText::_('COM_JEPROSHOP_CHANGE_LABEL'); ?></button>
						</form>
						<?php }?>
					</div>
				</div>
			</div>
			<div class="half_wrapper_right" >
				<?php if($this->customer->customer_id){ ?>
				<div class="panel" >
					<div class="panel-title" >
						<i class="icon-user"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_CUSTOMER_LABEL')); ?>
						<span class="badge badge-success">
							<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id=' . $this->customer->customer_id . '&' . JSession::getFormToken() . '=1'); ?>" >
								<?php if(JeproshopSettingModelSetting::getValue('enable_b2b_mode')){ echo $this->customer->company . ' - '; } 
								echo JText::_('COM_JEPROSHOP_' . strtoupper($this->customer->title) . '_LABEL'). ' ' . strtoupper($this->customer->lastname) . ' ' . $this->customer->firstname ; ?>
							</a>
						</span>
						<span class="badge badge-success"><?php echo '#' . $this->customer->customer_id; ?></span>
					</div>
					<div class="panel-content well" >
						<div class="half_wrapper left" >
							<?php if($this->customer->isGuest()){ 
								echo JText::_('COM_JEPROSHOP_THIS_ORDER_HAS_BEEN_PLACED_BY_A_GUEST_LABEL'); 
								if(!JeproshopCustomerModelCustomer::customerExists($this->customer->email)){ ?>
							<form method="post" action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=edit&customer_id=' . (int)$this->customer->customer_id . '&' .JSession::getFormToken() . '=1'); ?>" >
								<input type="hidden" name="jform[lang_id]" value="<?php echo $this->order->lang_id; ?>" />
								<input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="<?php echo JText::_('COM_JEPROSHOP_TRANSFORM_GUEST_INTO_CUSTOMER_MESSAGE'); ?>" />
								<p class="help-block"><?php echo JText::_('COM_JEPROSHOP_THIS_FEATURE_WILL_GENERATE_A_RANDOM_PASSWORD_AND_SEND_AN_EMAIL_CUSTOMER_MESSAGE'); ?></p>
							</form>
								<?php } else{ ?>
							<div class="alert alert-warning">
								<?php echo JText::_('COM_JEPROSHOP_A_REGISTERED_CUSTOMER_ACCOUNT_HAS_ALREADY_CLAIMED_THIS_EMAIL_ADDRESS_MESSAGE'); ?>
							</div>
								<?php }
								}else{ ?>
							<dl class="well list-detail" >
								<dt><?php echo JText::_('COM_JEPROSHOP_EMAIL_ADDRESS_LABEL') . ' : '; ?></dt>
								<dd><a href="mailto:<?php echo $this->customer->email; ?>" ><i class="icon-envelope-o"></i><?php echo $this->customer->email; ?></a></dd>
								<dt><?php echo JText::_('COM_JEPROSHOP_ACCOUNT_REGISTERED_LABEL'); ?></dt>
								<dd class="text-muted"><i class="icon-calendar-o"></i> <?php echo JeproshopTools::dateFormat($this->customer->date_add, true); ?></dd>
								<dt><?php echo JText::_('COM_JEPROSHOP_VALID_ORDER_PLACED_LABEL') . ' : '; ?></dt>
								<dd><span class="badge badge-success"><?php echo (int)$this->customerStats->nb_orders; ?></span></dd>
								<dt><?php echo JText::_('COM_JEPROSHOP_TOTAL_SPENT_SINCE_REGISTRATION_LABEL') . ' : '; ?></dt>
								<dd><span class="badge badge-success"> <?php echo JeproshopTools::displayPrice(JeproshopTools::roundPrice(JeproshopTools::convertPrice($this->customerStats->total_orders, $this->currency), 2), $this->currency->currency_id); ?></span></dd>
								<?php if(JeproshopSettingModelSetting::getValue('enable_b2b_mode')){ ?>
								<dt><?php echo JText::_('COM_JEPROSHOP_SIRET_LABEL') . ' : '; ?></dt>
								<dd><?php echo $this->customer->siret; ?></dd>
								<dt><?php echo JText::_('COM_JEPROSHOP_APE_LABEL') . ' : '; ?></dt>
								<dd><?php echo $this->customer->ape; ?></dd>
								<?php } ?>
							</dl>
							<?php } ?>
						</div>
						<div class="half_wrapper right" >
							<div class="form-group hidden-print">
								<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id=' . (int)$this->customer->customer_id . '&' . JSession::getFormToken() . '=1'); ?>" class="btn btn-default btn-block"><?php echo JText::_('COM_JEPROSHOP_VIEW_FULL_DETAILS_LABEL'); ?></a>
							</div>
							<div class="panel panel-sm">
								<div class="panel-title">
									<i class="icon-eye-slash"></i> <?php echo JText::_('COM_JEPROSHOP_PRIVATE_NOTE_LABEL'); ?>									
								</div>
								<div class="panel-content well" >
									<form action="ajax.php" method="post" onsubmit="saveCustomerNote(<?php echo $this->customer->customer_id; ?>); return false;" id="jform_customer_note" class="form-horizontal" >
										<div class="control-group">
											<textarea name="note" id="jform_note_content" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#jform_submit_customer_note').removeAttr('disabled') : $('#jform_submit_customer_note').attr('disabled', 'disabled')"><?php echo $this->customer->note; ?></textarea>											
										</div>
										<button type="submit" id="jform_submit_customer_note" class="btn btn-default pull-right" disabled="disabled">
											<i class="icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_LABEL'); ?>
										</button>
										<span id="jform_note_feedback"></span>
									</form>
								</div>						
							</div>
						</div>
						<div style="clear:both; " ></div>
					</div>
				</div>
				<?php } ?>
				<div class="panel" >
					<div class="panel-title" ></div>
					<div class="panel-content well" >
						<?php 
            				echo JHtml::_('bootstrap.startTabSet', 'address_form', array('active' =>'delivery_address'));
            				echo JHtml::_('bootstrap.addTab', 'address_form', 'delivery_address', JText::_('COM_JEPROSHOP_DELIVERY_ADDRESS_TAB_LABEL')); ?>
            			<div id="jform_delivery" >
            				<!-- Addresses -->
							<h4 class="visible-print"><?php echo JText::_('COM_JEPROSHOP_SHIPPING_ADDRESS_LABEL'); ?></h4>
							<?php if(!$this->order->isVirtual()){ ?>
							<!-- Shipping address -->
								<?php if($this->can_edit){ ?>
							<form class="form-horizontal hidden-print" method="post" action="<?php echo JRoute::_('index.php?view=com_jeproshop&view=order&task=view&order_id=' . (int)$this->order->order_id); ?>" >
								<div class="control-group" >
									<select name="jform[address_id]" >
										<?php foreach($this->customer_addresses as $address){ ?>
										<option value="<?php echo $address->address_id; ?>" <?php if($address->address_id == $this->order->address_delivery_id){ ?> selected="selected" <?php } ?> >
											<?php echo ucfirst($address->alias) . ' - ' . $address->address1 . ' ' . $address->postcode . $address->city;
											if(!empty($address->state)){ echo ' ' . ucfirst($address->state); }
											echo ' ' . ucfirst($address->country); ?>
										</option>
										<?php } ?>	
									</select>
									<button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> <?php echo JText::_('COM_JEPROSHOP_CHANGE_LABEL'); ?></button>									
								</div>
							</form>
								<?php } ?>
							<div class="well">
								<div class="row">
									<div class="col-sm-6">
										<?php  $address_type = (($this->delivery_address->address_id == $this->invoice_address->address_id) ? '&address_type=1' : '' ); ?>
										<a class="btn btn-default pull-right" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address&task=edit&address_id=' . (int)$this->delivery_address->address_id . '&realedit=1&order_id=' . (int)$this->order->order_id . $address_type . '&' . JeproshopTools::getAddressToken() . '=1&return='); ?>" >
											<i class="icon-pencil"></i> <?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?>
										</a>
											<?php echo JeproshopTools::displayAddressDetail($this->delivery_address, '<br />');
											if($this->delivery_address->other){ echo '<hr />' . $this->delivery_address->other . '<br />'; } ?>
									</div>
									<div class="col-sm-6 hidden-print">
										<div id="map-delivery-canvas" style="height: 190px"></div>
									</div>
								</div>
							</div>
							<?php } ?>
            			</div>
            			<?php echo JHtml::_('bootstrap.endTab'); 
            				echo JHtml::_('bootstrap.addTab', 'address_form', 'invoice_address', JText::_('COM_JEPROSHOP_INVOICE_ADDRESS_TAB_LABEL')); ?> 
            			<div id="jform_invoice_address" >
            					<!-- Invoice address -->
							<h4 class="visible-print"><?php echo JText::_('COM_JEPROSHOP_INVOICE_ADDRESS_LABEL'); ?></h4>
							<?php if($this->can_edit){ ?>
							<form class="form-horizontal hidden-print" method="post" action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . (int)$this->order->order_id); ?>">
								<div class="control-group" >
									<select name="jform[address_id]" >
										<?php foreach($this->customer_addresses as $address){ ?>
										<option value="<?php echo $address->address_id; ?>"
											<?php if($address->address_id == $this->order->address_invoice_id){ ?> selected="selected" <?php } ?> >
											<?php echo  $address->alias . ' - ' . $address->address1 . ' ' . $address->postcode . ' ' . $address->city;
											if(!empty($address->state)){ echo ' ' . $address->state; }
											echo ', ' . ucfirst($address->country); ?>
										</option>
										<?php } ?>	
									</select>									
									<button class="btn btn-default" type="submit" name="submitAddressInvoice" ><i class="icon-refresh"></i> <?php echo JText::_('COM_JEPROSHOP_CHANGE_LABEL'); ?></button>
								</div>
							</form>
							<?php } ?>
							<div class="well">
								<div class="row">
									<div class="col-sm-6">
										<?php $address_type = (($this->delivery_address->address_id == $this->invoice_address->address_id) ? '&address_type=2' : ''); ?>
										<a class="btn btn-default pull-right" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address&task=add&address_id=' . (int)$this->invoice_address->address_id . $address_type . '&realedit=1&order_id=' . (int)$this->order->order_id . '&' . JSession::getFormToken() . '=1&return='); ?>" >
											<i class="icon-pencil"></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?>											
										</a>
										<?php echo JeproshopTools::displayAddressDetail($this->invoice_address, '<br />');
										if($this->invoice_address->other){ echo '<hr />' . $this->invoice_address->other . '<br />'; } ?>
									</div>
									<div class="col-sm-6 hidden-print">
										<div id="map-invoice-canvas" style="height: 190px"></div>
									</div>
								</div>
							</div>
            			</div>
            			<?php echo JHtml::_('bootstrap.endTab'); 
            				echo JHtml::_('bootstrap.endTabSet');
            			?>
					</div>
				</div>
				<div class="panel" >
					<div class="panel-title" >
						<i class="icon-envelope"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_MESSAGES_LABEL')); ?> <span class="badge badge-success"><?php echo sizeof($this->customer_thread_message); ?></span>
					</div>
					<div class="panel-content well" id="jform_message_wrapper" >
						<?php if(sizeof($this->messages)){ ?>
						<div class="panel panel-highlighted">
							<div class="message-item">
								<?php foreach($this->messages as $message){ ?>
								<div class="message-avatar">
									<div class="avatar-md">
										<i class="icon-user icon-2x"></i>
									</div>
								</div>
								<div class="message-body">									
									<span class="message-date">&nbsp;<i class="icon-calendar"></i>
										<?php echo JeproshopTools::dateFormat($message->date_add) . ' - ' ; ?>
									</span>
									<h4 class="message-item-heading">
										<?php if($message->employee_lastname){ 
											echo $message->employee_firstname . ' ' . $message->employee_lastname; 
										}else{
											echo $message->customer_firstname . ' ' . $message->customer_lastname;
										}
										if($message->private == 1){ ?>
											<span class="badge badge-info"><?php echo JText::_('COM_JEPROSHOP_PRIVATE_LABEL'); ?></span>
										<?php } ?>
									</h4>
									<p class="message-item-text">
										<?php echo n2lbr($message->message); ?>
									</p>
								</div>
								{*if ($message['is_new_for_me'])}
								<a class="new_message" title="<?php echo JText::_('COM_JEPROSHOP_MARK_THIS_MESSAGE_AS_VIEWED_TITLE_DESC'); ?>" href="{$smarty.server.REQUEST_URI}&amp;token={$smarty.get.token}&amp;messageReaded={$message->message']}">
									<i class="icon-save"></i>
								</a>
								{/if*}
								<?php } ?>
							</div>
						</div>
						<?php } ?>
						<div id="messages" class="well hidden-print">
							<form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('<?php echo JText::_('COM_JEPROSHOP_DO_YOU_WANT_TO_SEND_THIS_MESSAGE_TO_THE_CUSTOMER_MESSAGE'); ?>');" >
								<div id="message" class="form-horizontal">
									<div class="control-group">
										<div class="control-label" ><label class="" ><?php echo JText::_('COM_JEPROSHOP_CHOOSE_A_STANDARD_MESSAGE_LABEL'); ?></label></div>
										<div class="controls">
											<select name="jform[order_message]" id="jform_order_message" onchange="orderOverwriteMessage(this, '<?php echo JText::_('COM_JEPROSHOP_DO_YOU_WANT_TO_OVERWRITE_YOUR_EXISTING_MESSAGE'); ?>" >
												<option value="0" selected="selected">--</option>
												<?php foreach($this->orderMessages as $orderMessage){ ?>
												<option value="<?php echo $orderMessage->message; ?>"><?php echo $orderMessage->name; ?></option>
												<?php } ?>
											</select>
											<p class="help-block">
												<a href="<?php JRoute::_('index.php?option=com_jeproshop&view=order&view=message'); ?>" >
													<?php echo JText::_('COM_JEPROSHOP_CONFIGURE_PREDEFINED_MESSAGES_LABEL'); ?>
													<i class="icon-external-link"></i>
												</a>
											</p>
										</div>
									</div>

									<div class="control-group">
										<div class="control-label"><label ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_TO_CUSTOMER_LABEL'); ?></label></div>
										<div class="controls" >
											<fieldset class="radio btn-group" id="jform_visibility" >
												<input type="radio" name="jform[visibility]" id="jform_visibility_on" value="1" /><label for="jform_visibility_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
												<input type="radio" name="jform[visibility]" id="jform_visibility_off" value="0" checked="checked" /><label for="jform_visibility_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
												<!--  a class="slide-button btn"></a -->
											</fieldset>
										</div>
									</div>

									<div class="control-group">
										<div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_MESSAGE_LABEL'); ?></label></div>
										<div class="controls">
											<textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
											<p id="nbchars"></p>
										</div>
									</div>
									<input type="hidden" name="order_id" value="<?php echo $this->order->order_id; ?>" />
									<input type="hidden" name="customer_id" value="<?php echo $this->order->customer_id; ?>" />
									<button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
										<?php echo JText::_('COM_JEPROSHOP_SEND_MESSAGE_LABEL'); ?>
									</button>
									<a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" >
										<?php echo JText::_('COM_JEPROSHOP_SHOW_ALL_MESSAGES_LABEL'); ?>
										<i class="icon-external-link"></i>
									</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div style="clear:both;" ></div>
		</div>
		<div class="form_box" >
			<form class="container-command-top-spacing" action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . $this->order->order_id . '&' . JeproshopTools::getOrderFormToken()); ?>" method="post" onsubmit="return orderDeleteProduct('<?php echo JText::_('COM_JEPROSHOP_THIS_PRODUCT_CANNOT_BE_RETURNED_MESSAGE'); ?>', '<?php echo JText::_('COM_JEPROSHOP_QUANTITY_TO_CANCEL_IS_GREATER_THAN_AVAILABLE_QUANTITY_MESSAGE'); ?>');" >
				<div>
					<div class="panel">
						<div class="panel-title">
							<i class="icon-shopping-cart"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_PRODUCTS_LABEL')); ?> <span class="badge badge-success" ><?php echo count($this->products); ?></span>
						</div>
						<div class="panel-content well" >
							<?php 
							if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){
								$taxMethod = JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL');
							}else{
								$taxMethod = JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL');
							}
							 ?>
							<div class="table-responsive" >
								<table class="table table-striped" id="jform_order_products">
									<thead>
										<tr>
											<th></th>
											<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_PRODUCT_LABEL'); ?></span></th>
											<th>
												<span class="title_box "><?php echo JText::_('COM_JEPROSHOP_UNIT_PRICE_LABEL'); ?></span>
												<small class="text-muted"><?php echo $taxMethod; ?></small>
											</th>
											<th class="text-center"><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></span></th>
											<?php if($this->display_warehouse){ ?><th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_WAREHOUSE_LABEL'); ?></span></th><?php } ?>
											<?php if($this->order->hasBeenPaid()){ ?><th class="text-center"><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_REFOUND_LABEL'); ?></span></th><?php } ?>
											<?php if($this->order->hasBeenDelivered() || $this->order->hasProductReturned()){ ?>
											<th class="text-center"><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_RETURNED_LABEL'); ?></span></th>
											<?php } ?>
											<?php if($this->stock_management){ ?><th class="text-center"><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_QUANTITY_LABEL'); ?></span></th><?php } ?>
											<th>
												<span class="title_box "><?php echo JText::_('COM_JEPROSHOP_TOTAL_LABEL'); ?></span>
												<small class="text-muted"><?php echo $taxMethod; ?></small>
											</th>
											<th style="display: none;" class="add_product_fields"></th>
											<th style="display: none;" class="edit_product_fields"></th>
											<th style="display: none;" class="standard_refund_fields">
												<i class="icon-minus-sign"></i>
												<?php if($this->order->hasBeenDelivered() || $this->order->hasBeenShipped()){
													echo JText::_('COM_JEPROSHOP_RETURN_LABEL');
												}elseif($this->order->hasBeenPaid()){
													echo JText::_('COM_JEPROSHOP_REFOUND_LABEL'); 
												}else{
													echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); 
												} ?>
											</th>
											<th style="display:none" class="partial_refund_fields">
												<span class="title_box "><?php echo JText::_('COM_JEPROSHOP_PARTIAL_REFUND_LABEL'); ?></span>
											</th>
											<?php if(!$this->order->hasBeenDelivered()){ ?><th></th><?php } ?>									
										</tr>
									</thead>
									<tbody>
										<?php foreach($this->products as $key => $product){ 
											if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){
												$product_price = ($product->unit_price_tax_excl + $product->ecotax);
											}else{
												$product_price = $product->unit_price_tax_incl;
											}
											/* Include customized datas partial */
											if($product->customizedDatas){ ?>
										<tr class="customized customized_<?php echo $product->order_detail_id; ?> product_line_row" >
											<td>
												<input type="hidden" class="edit_product_order_detail_id" value="<?php echo (int)$product->order_detail_id; ?>" />
												<?php if(isset($product->image) && $product->image->image_id){ echo $product->image_tag; }else{ ?>--<?php } ?>
											</td>
											<td>
												<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product&product_id=' . (int)$product->product_id . '&task=update&token=' . JeproshopTools::getProductToken()); ?> ">
													<span class="productName"><?php echo $product->product_name . ' - ' . JText::_('COM_JEPROSHOP_CUSTOMIZED_LABEL'); ?></span><br />
													<?php if($product->product_reference){ echo JText::_('COM_JEPROSHOP_REFERENCE_NUMBER_LABEL') . ' : ' . $product->product_reference . '<br />'; }
													if($product->product_supplier_reference){ echo JText::_('COM_JEPROSHOP_SUPPLIER_REFERENCE_LABEL') . ' : ' . $product->product_supplier_reference; } ?>
												</a>
											</td>
												<td>
													<span class="product_price_show"><?php echo JeproshopTools::displayPrice($product_price, $this->currency->currency_id); ?></span>
													<?php if($this->can_edit){ ?>
													<div class="product_price_edit" style="display:none;">
														<input type="hidden" name="product_order_detail_id" class="edit_product_order_detail_id" value="<?php echo $product->order_detail_id; ?>" />
														<div class="form-group">
															<div class="fixed-width-xl">
																<div class="input-group">
																	<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?></div><?php } ?>
																	<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo JeproshopTools::roundPrice($product->unit_price_tax_excl, 2); ?>" size="5" />
																	<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
																</div>
															</div>
															<br/>
															<div class="fixed-width-xl">				
																<div class="input-group">
																	<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
																	<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo JeproshopTools::roundPrice($product->unit_price_tax_incl, 2); ?>" size="5" />
																	<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
																</div>
															</div>
														</div>
													</div>
													<?php } ?>
												</td>
												<td class="product_quantity" ><?php echo $product->customizationQuantityTotal; ?></td>
												<?php if($this->display_warehouse){ ?><td>&nbsp;</td><?php }
												if($this->order->hasBeenPaid()){ ?><td class="product_quantity"><?php echo $product->customizationQuantityRefunded; ?></td><?php }
												if($this->order->hasBeenDelivered() || $this->order->hasProductReturned()){ ?><td class="product_quantity"><?php echo $product->customizationQuantityReturned; ?></td><?php }
												if($this->stock_management){ ?><td class=""><?php echo $product->current_stock; ?></td><?php } ?>
												<td class="total_product">
													<?php if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){
														echo JeproshopTools::displayPrice(JeproshopTools::roundPrice($product->product_price * $product->customizationQuantityTotal, 2), $this->currency->currency_id);
													}else{
														echo JeproshopTools::displayPrice(JeproshopTools::roundPrice($product->product_price_with_tax * $product->customizationQuantityTotal, 2), $this->currency->currency_id);
													} ?>
												</td>
												<td class="cancel_quantity standard_refund_fields current-edit" style="display:none" colspan="2" >&nbsp;</td>
												<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
												<td class="partial_refund_fields current-edit" style="text-align:left;display:none;"></td>
												<?php if($this->can_edit && !$this->order->hasBeenDelivered()){ ?>
												<td class="product_action text-right">
													<!-- {* edit/delete controls *} -->
													<div class="btn-group">
														<button type="button" class="btn btn-default edit_product_change_link" >
															<i class="icon-pencil"></i> <?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?>
														</button>
														<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
															<span class="caret"></span>
														</button>
														<ul class="dropdown-menu" role="menu">
															<li>
																<a href="#" class="delete_product_line">
																	<i class="icon-trash"></i> <?php echo JText::_('COM_JEPROSHOP_DELETE_LABEL'); ?>
																</a>
															</li>
														</ul>
													</div>
													<!-- {* Update controls *} -->
												<button type="button" class="btn btn-default submitProductChange" style="display: none;">
													<i class="icon-ok"></i> <?php echo JText::_('COM_JEPROSHOP_UPDATE_LABEL'); ?>
												</button>
												<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
													<i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
												</button>
											</td>
											<?php } ?>
										</tr>
										<?php foreach($product->customizedDatas as $customizationPerAddress){
											foreach($customizationPerAddress as $customization_id => $customization){ ?>
										<tr class="customized customized_<?php echo $product->order_detail_id; ?>" >
											<td colspan="2">
												<input type="hidden" class="edit_product_order_detail_id" value="<?php echo $product->order_detail_id; ?>" />
												<div class="form-horizontal">
													<?php foreach($customization->datas as $type => $datas){
															if($type == JeproshopProductModelProduct::CUSTOMIZE_FILE){
																foreach($datas as $data){ ?>
														<div class="control-group" >
															<div class="control-label" ><span class=""><strong><?php if($data->name){ echo $data->name; }else{ /*l s='Picture #'}{$data@iteration */} ?></strong></span></div>
															<div class="controls">
																<a href="displayImage.php?img={$data['value']}&amp;name={$order->id|intval}-file{$data@iteration}" target="_blank">
																	<img class="img-thumbnail" src="<?php echo COM_JEPROSHOP_THEME_PRODUT_PICTURE_DIR . $data->value . '_small'; ?>" alt="" />
																</a>
															</div>
														</div>
																<?php }
																}elseif($type == JeproshopProductModelProduct::CUSTOMIZE_TEXTFIELD){
																	foreach($datas as $data){ ?>
														<div class="control-group" >
															<span class="col-lg-4 control-label"><strong><?php if($data->name){ echo $data->name; }else{ /*l s='Text #%s' sprintf=$data@iteration */ } ?></strong></span>
															<div class="controls">
																<p class="form-control-static"><?php echo $data->value; ?></p>
															</div>
														</div>
																	<?php }
																}
															 } ?>
													</div>
												</td>
												<td>-</td>
												<td class="product_quantity">
													<span class="product_quantity_show<?php if((int)$customization->quantity > 1){ ?> red bold <?php } ?> "><?php echo $customization->quantity; ?></span>
													<?php if($this->can_edit){ ?>
													<span class="product_quantity_edit" style="display:none;">
														<input type="text" name="product_quantity[<?php echo (int)$customization_id; ?>]" class="edit_product_quantity" value="<?php echo htmlentities($customization->quantity); ?>" size="2" />
													</span>
													<?php } ?>
												</td>
												<?php if($this->display_warehouse){ ?><td>&nbsp;</td><?php } ?>
												<?php if($this->order->hasBeenPaid()){ ?><td><?php echo $customization->quantity_refunded; ?></td><?php } ?>
												<?php if($this->order->hasBeenDelivered()){ ?><td><?php echo $customization->quantity_returned; ?></td><?php } ?>
												<td>-</td>
												<td class="total_product">
													<?php if ($order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){
														echo JeproshopTools::displayPrice(JeproshopTools::roundPrice($product->product_price * $customization->quantity, 2), $this->currency->currency_id);
													}else{
														echo JeproshopTools::displayPrice(JeproshopTools::roundPrice($product->product_price_with_tax * $customization->quantity, 2), $this->currency->currency_id);
													} ?>
												</td>				
												<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
													<input type="hidden" name="jform[total_quantity_return]" id="jform_total_quantity_return" value="<?php echo (int)$customization->quantity_returned; ?>" />
													<input type="hidden" name="jform[total_quantity]" id="jform_total_quantity" value="<?php echo (int)$customization->quantity; ?>" />
													<input type="hidden" name="jform[product_name]" id="jform_product_name" value="<?php echo $product->product_name ?>" />
													<?php if((!$this->order->hasBeenDelivered() OR JeproshopSettingModelSetting::getValue('return_order')) AND (int)($customization->quantity_returned) < (int)($customization->quantity)){ ?>
													<input type="checkbox" name="jform[customization[<?php echo $customization_id; ?>]" id="jform_customization_id_<?php echo (int)$customization_id; ?>" value="<?php echo (int)$product->order_detail_id; ?>" onchange="setCancelQuantity(this, <?php echo (int)$customization_id; ?>, <?php echo ($customization->quantity - $product->customizationQuantityTotal - $product->product_quantity_reinjected); ?>)" <?php if(($product->product_quantity_return + $product->product_quantity_refunded) >= $product->product_quantity){ ?> disabled="disabled" <?php } ?> />
													<?php }else{ ?> -- <?php } ?>				
												</td>
												<td class="cancel_quantity standard_refund_fields current-edit" style="display:none">
													<?php if($customization->quantity_returned + $customization->quantity_refunded >= $customization->quantity){ ?>
													<input type="hidden" name="cancelCustomizationQuantity[{$customizationId|intval}]" value="0" />
													<?php }elseif(!$this->order->hasBeenDelivered() OR JeproshopSettingModelSetting::getValue('return_order')){ ?>
													<input type="text" id="jform_cancel_quantity_<?php echo $customization_id; ?>" name="jform[cancel_customization_quantity[<?php echo (int)$customization_id; ?>]" size="2" onclick="selectCheckbox(this);" value="" />0/{$customization['quantity']-$customization['quantity_refunded']}
													<?php } ?> 
												</td>
												<td class="partial_refund_fields current-edit" style="display:none; width: 250px;">
													<div class="control-group">
														<div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></label></div>
														<div class="controls">
															<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="jform_partial_refund_product_quantity[<?php echo $product->order_detail_id; ?>]]" value="<?php if(($customization->quantity - $customization->quantity_refunded) >0){ ?>1 <?php }else{ ?>0<?php } ?>" />
															<div class="input-group-addon">/ <?php echo ($customization->quantity - $customization->quantity_refunded); ?></div>
														</div>
													</div>
													<div class="control-group">
														<div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_AMOUNT_LABEL'); ?></label></div>
														<div class="controls">
															<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
															<input onchange="checkPartialRefundProductAmount(this)" type="text" name="jform[partialRefundProduct[<?php echo $product->order_detail_id; ?>]]" />
															<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('CM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
															<p class="help-block"><i class="icon-warning-sign"></i> <?php echo JText::_('COM_JEPROSHOP_MAX_LABEL') . ' ' . $product->amount_refundable; ?></p>
														</div>
													</div>

													<div class="control-group">
													<?php if(!empty($product->amount_refund) && $product->amount_refund > 0){
														echo $product->amount_refund . ' ' . JText::_('COM_JEPROSHOP_REFUND_LABEL');
													} ?>
													<input type="hidden" value="<?php echo $product->quantity_refundable; ?>" class="partial_refund_product_quantity" />
													<input type="hidden" value="<?php echo $product->amount_refundable; ?>" class="partial_refund_product_amount" />		
													</div>
											</td>
											<?php if($this->can_edit && !$this->order->hasBeenDelivered()){ ?>
											<td class="edit_product_fields" colspan="2" style="display:none"></td>
											<td class="product_action" style="text-align:right"></td>
											<?php } ?>
										</tr>
											<?php }
											} ?>

											<?php }
											/* Include product line partial */																					
											if($product->product_quantity > $product->customizationQuantityTotal){ 
											?>
										<tr class="product-line-row">
											<td><?php if(isset($product->image) && $product->image->image_id){ echo $product->image_tag; } ?></td>
											<td>
												<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product&product_id=' . (int)$product->product_id . '&task=update&' . JSession::getFormToken() . '=1'); ?>" >
													<span class="product_name"><?php echo $product->product_name; ?></span><br />
													<?php if($product->product_reference){ echo JText::_('COM_JEPROSHOP_REFERENCE_NUMBER_LABEL') . ' : ' . $product->product_reference . '<br />'; }
													if($product->product_supplier_reference){ echo JText::_('COM_JEPROSHOP_SUPPLIER_REFERENCE_LABEL') . ' : ' . $product->product_supplier_reference; } ?>
												</a>
											</td>
											<td>
												<span class="product_price_show"><?php echo JeproshopTools::displayPrice($product_price, $this->currency->currency_id); ?></span>
												<?php if($this->can_edit){ ?>
												<div class="product_price_edit" style="display:none;">
													<input type="hidden" name="jform[product_order_detail_id]" class="edit_product_order_detail_id" value="<?php echo $product->order_detail_id; ?>" />
													<div class="control-group">
														<div class="fixed-width-xl">
															<div class="input-group">
																<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' '. JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?></div><?php } ?>
																<input type="text" name="jform[product_price_tax_excl]" class="edit_product_price_tax_excl edit_product_price price_box" value="<?php echo JeproshopTools::roundPrice($product->unit_price_tax_excl, 2); ?>"/>
																<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?></div><?php } ?>
															</div>
														</div>
														<br/>
														<div class="fixed-width-xl">				
															<div class="input-group">
																<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
																<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl preice_box edit_product_price" value="<?php echo JeproshopTools::roundPrice($product->unit_price_tax_incl, 2); ?>" />
																<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
															</div>
														</div>
													</div>
												</div>
												<?php } ?>
											</td>
											<td class="nowrap product_quantity center">
												<span class="product_quantity_show<?php if((int)$product->product_quantity > 1){ ?> badge <?php } ?>"> <?php echo $product->product_quantity; ?></span>
												<?php if($this->can_edit){ ?>
												<span class="product_quantity_edit" style="display:none;">
													<input type="text" name="product_quantity" class="edit_product_quantity" value="<?php echo htmlentities($product->product_quantity) ; ?>" />
												</span>
												<?php } ?>
											</td>
											<?php if($this->display_warehouse){ ?><td><?php echo $product->warehouse_name; ?></td><?php } 
											if($this->order->hasBeenPaid()){ ?>
											<td class="productQuantity center" >
												<?php echo $product->product_quantity_refunded; 
												if(count($product->refund_history)){ ?>
												<span class="tooltip">
													<span class="tooltip_label tooltip_button">+</span>
													<span class="tooltip_content">
														<span class="title"><?php echo ucfirst(JText::_('COM_JEPROSHOP_REFOUND_HISTORY_LABEL')); ?></span>
														<?php foreach($product->refund_history as $refund){
															echo JeproshopTools::dateFormat($refund->date_add) . ' - ' . JeproshopTools::displayPrice($refund->amount_tax_incl) . '<br />';
														} ?>
													</span>
												</span>
												<?php } ?>
											</td>
											<?php } 
											if($this->order->hasBeenDelivered() || $this->order->hasProductReturned()){ ?>
											<td class="product_quantity center">
												<?php echo $product->product_quantity_return;
												if(count($product->return_history)){ ?>
												<span class="tooltip">
													<span class="tooltip_label tooltip_button">+</span>
													<span class="tooltip_content">
														<span class="title"><?php echo JText::_('COM_JEPROSHOP_RETURN_HISTORY_LABEL')?></span>
														<?php foreach($product->return_history as $return){ 
															echo $return->date_add . ' - ' . $return->product_quantity . ' - ' . $return->state . '<br />';
														} ?>
													</span>
												</span>
												<?php } ?>
											</td>
											<?php } 
											if($this->stock_management){ ?><td class="productQuantity product_stock center"><?php echo $product->current_stock; ?></td><?php } ?>
											<td class="total_product">
												<?php echo JeproshopTools::displayPrice(JeproshopTools::roundPrice($product_price, 2) * ($product->product_quantity - $product->customizationQuantityTotal), $this->currency->currency_id); ?>
											</td>
											<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
											<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
												<input type="hidden" name="jform[total_quantity_return]" id="jform_total_quantity_return" value="<?php echo $product->product_quantity_return; ?>" />
												<input type="hidden" name="jform[total_quantity]" id="jform_total_quantity" value="<?php echo $product->product_quantity; ?>" />
												<input type="hidden" name="jform[product_name]" id="jform_product_name" value="<?php echo $product->product_name; ?>" />
												<?php if((!$this->order->hasBeenDelivered() OR JeproshopSettingModelSetting::getValue('return_order')) AND (int)($product->product_quantity_return) < (int)($product->product_quantity)){ ?>
												<input type="checkbox" name="jform[order_detail_id[<?php echo $product->order_detail_id; ?>]" id="jform_order_detail_id_<?php echo $product->order_detail_id; ?>" value="<?php echo $product->order_detail_id; ?>" onchange="setCancelQuantity(this, <?php echo $product->order_detail_id; ?>, <?php echo ($product->product_quantity - $product->customizationQuantityTotal - $product->product_quantity_return); ?>)" <?php if(($product->product_quantity_return + $product->product_quantity_refunded) >= $product->product_quantity){ ?> disabled="disabled" <?php } ?> />
												<?php }else{ ?>
												--
												<?php } ?>
											</td>
											<td class="cancel_quantity standard_refund_fields current-edit" style="display:none">
												<?php if(($product->product_quantity_return + $product->product_quantity_refunded) >= $product->product_quantity){ ?>
												<input type="hidden" name="cancel_quantity[<?php echo $product->order_detail_id; ?>]" value="0" />
												<?php }elseif(!$this->order->hasBeenDelivered() OR JeproshopSettingModelSetting::getValue('PS_ORDER_RETURN')){ ?>
												<input type="text" id="jform_cancel_quantity_<?php echo $product->order_detail_id; ?>" name="jform[cancelQuantity[<?php echo $product->order_detail_id; ?>]" onclick="selectCheckbox(this);" value="" />
												<?php }

												if($product->customizationQuantityTotal){
													$productQuantity = ($product->product_quantity - $product->customizationQuantityTotal);
												}else{
													$productQuantity = $product->product_quantity;
												}

												if($this->order->hasBeenDelivered()){
													echo $product->product_quantity_refunded . '/' . $productQuantity-$product->product_quantity_refunded; 
												}elseif($this->order->hasBeenPaid()){
													echo $product->product_quantity_return . '/' . $productQuantity;
												}else{
													echo '0/' . $productQuantity;
												} ?>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none; width: 250px;">
												<div class="form-group">
													<div class="col-lg-4">
														<label class="control-label"><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL') . ' : '; ?></label>
														<div class="input-group">
															<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="jform[partialRefundProductQuantity[<?php echo $product->order_detail_id; ?>]" value="<?php if(($productQuantity - $product->product_quantity_refunded) >0){ ?>1<?php }else{ ?>0<?php } ?>" />
															<div class="input-group-addon"><?php echo '/ ' . $productQuantity - $product->product_quantity_refunded; ?></div>
														</div>
													</div>
													<div class="col-lg-8">
														<label class="control-label"><?php echo ucfirst(JText::_('COM_JEPROSHOP_AMOUNT_LABEL')); ?> : </label>
														<div class="input-group">
															<?php if($this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php } ?>
															<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[<?php echo $product->order_detail_id; ?>]" />
															<?php if(!$this->currency->format % 2){ ?><div class="input-group-addon"><?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></div><?php  } ?>
														</div>
														<p class="help-block"><i class="icon-warning-sign"></i> <?php echo JText::_('COM_JEPROSHOP_MAX_LABEL') . ' ' . $product->amount_refundable; ?></p>
													</div>
												</div>

												<div class="form-group">
													<?php if(!empty($product->amount_refund) && $product->amount_refund > 0){
														echo '(' . $product->amount_refund . ' ' . strtolower(JText::_('COM_JEPROSHOP_REFOUND_LABEL')) . ')'; 
													} ?>
													<input type="hidden" value="<?php echo $product->quantity_refundable; ?>" class="partialRefundProductQuantity" />
													<input type="hidden" value="<?php echo $product->amount_refundable; ?>" class="partialRefundProductAmount" />		
												</div>	
											</td>
											<?php if($this->can_edit && !$this->order->hasBeenDelivered()){ ?>
											<td class="product_invoice" style="display: none;">
												<?php if(sizeof($this->invoices_collection)){ ?>
												<select name="jform[product_invoice]" class="edit_product_invoice">
													<?php foreach($this->invoices_collection as $invoice){ ?>
													<option value="<?php echo $invoice->id; ?>" <?php if($invoice->id == $product->order_invoice_id){ ?> selected="selected" <?php } ?> >
														<?php echo '#_' . JeproshopSettingModelSetting::getValue('order_invoice_prefix') . '_' . $this->invoice_address->number; ?>
													</option>
													<?php } ?>
												</select>
												<?php }else{ ?> &nbsp; <?php } ?>
											</td>
											<td class="product_action text-right">
												<!--  * edit/delete controls *-->
												<div class="btn-group">
													<button type="button" class="btn btn-default edit_product_change_link">
														<i class="icon-pencil"></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?>
													</button>
													<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
														<span class="caret"></span>
													</button>
													<ul class="dropdown-menu" role="menu">
														<li>
															<a href="#" class="delete_product_line" >
																<i class="icon-trash"></i> <?php echo JText::_('COM_JEPROSHOP_DELETE_LABEL'); ?>
															</a>
														</li>
													</ul>
												</div>
												<!-- {* Update controls *} -->
												<button type="button" class="btn btn-default submitProductChange" style="display: none;">
													<i class="icon-ok"></i> <?php echo JText::_('COM_JEPROSHOP_UPDATE_LABEL'); ?>		
												</button>
												<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
													<i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
												</button>
											</td>
											<?php } ?>
										</tr>
										<?php } 
										}
										if($this->can_edit){ ?>
										<tr id="new_product" style="display:none">
											<td style="display:none;" colspan="2">
												<input type="hidden" id="add_product_product_id" name="jform[add_product[product_id]]" value="0" />

												<div class="form-group">
													<label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_LABEL'); ?></label>
													<div class="input-group">
														<input type="text" id="add_product_product_name" value=""/>
														<div class="input-group-addon">
															<i class="icon-search"></i>
														</div>
													</div>
												</div>
		
												<div id="add_product_product_attribute_area" class="form-group" style="display: none;">
													<label><?php echo JText::_('COM_JEPROSHOP_COMBINATION_LABEL'); ?></label>
													<select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
												</div>

												<div id="add_product_product_warehouse_area" class="form-group" style="display: none;">
													<label><?php echo JText::_('COM_JEPROSHOP_VALUE_LABEL'); ?><?php echo JText::_('COM_JEPROSHOP_WAREHOUSE_LABEL'); ?></label>
													<select  id="add_product_warehouse" name="add_product_warehouse"></select>
												</div>
											</td>

											<td style="display:none;">
												<div class="row">
													<div class="input-group fixed-width-xl">
														<div class="input-group-addon">
															<?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?>
															<?php echo JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?>
														</div>
														<input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" disabled="disabled" />
													</div>
												</div>
												<div class="row">
													<div class="input-group fixed-width-xl">
														<div class="input-group-addon">
															<?php echo $this->currency->sign . ' ' . JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?>
														</div>
														<input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" disabled="disabled" />
													</div>
												</div>
											</td>

											<td style="display:none;" class="productQuantity">
												<input type="number" class="form-control fixed-width-sm" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" disabled="disabled" />
											</td>
											<?php if($this->order->hasBeenPaid()){ ?><td style="display:none;" class="productQuantity"></td><?php } ?>
											<?php if($this->display_warehouse){ ?><td></td><?php } ?>
											<?php if($this->order->hasBeenDelivered()){ ?><td style="display:none;" class="productQuantity"></td><?php  } ?>
											<td style="display:none;" class="productQuantity" id="add_product_product_stock">0</td>
											<td style="display:none;" id="add_product_product_total"><?php echo JeproshopTools::displayPrice(0, $this->currency->currency_id); ?></td>
											<td style="display:none;" colspan="2">
												<?php if(sizeof($this->invoices_collection)){ ?>
												<select class="form-control" name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
													<optgroup class="existing" label="<?php echo JText::_('COM_JEPROSHOP_EXISTING_LABEL'); ?>">
														<?php foreach($this->invoices_collection as $invoice){ ?>
														<option value="<?php echo $invoice->id; ?>"><?php echo $invoice->getInvoiceNumberFormatted($current_lang_id); ?></option>
														<?php } ?>
													</optgroup>
													<optgroup label="<?php echo JText::_('COM_JEPROSHOP_NEW_LABEL'); ?>">
														<option value="0"><?php echo JText::_('COM_JEPROSHOP_CREATE_A_NEW_INVOICE_LABEL'); ?></option>
													</optgroup>
												</select>
												<?php } ?>
											</td>
											<td style="display:none;">
												<button type="button" class="btn btn-default" id="cancelAddProduct">
													<i class="icon-remove text-danger"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
												</button>
												<button type="button" class="btn btn-default" id="submitAddProduct" disabled="disabled">
													<i class="icon-ok text-success"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_LABEL'); ?>
												</button>
											</td>
										</tr>

										<tr id="new_invoice" style="display:none">
											<td colspan="10">
												<h4><?php echo JText::_('COM_JEPROSHOP_NEW_INVOICE_INFORMATION_LABEL'); ?></h4>
												<div class="form-horizontal">
													<div class="control-group">
														<div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_CARRIER_LABEL'); ?></label></div>
														<div class="controls"  >
															<p class="form-control-static"><strong><?php echo $carrier->name; ?></strong></p>
														</div>
													</div>
													<div class="control-group">
														<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_SHIPPING_COST_LABEL'); ?></label></div>
														<div class="controls">
															<p class="checkbox">
																<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
																<label><?php echo JText::_('COM_JEPROSHOP_FREE_SHIPPING_LABEL'); ?></label>
															</p>
															<p class="help-block"><?php echo JText::_('COM_JEPROSHOP_IF_YOU_DONT_SELECT_FREE_SHIPPING_THE_NORMAL_SHIPPING_COST_WILL_BE_APPLIED_LABEL'); ?></p>															
														</div>
													</div>
												</div>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php if($this->can_edit){ ?>
							<div class="row-margin-bottom row-margin-top order_action">
								<?php if(!$this->order->hasBeenDelivered()){ ?>
								<button type="button" id="jform_add_product" class="btn btn-default">
									<i class="icon-plus-sign"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_PRODUCT_LABEL'); ?>
								</button>
								<?php } ?>
								<button id="add_voucher" class="btn btn-default" type="button" >
									<i class="icon-ticket"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_NEW_DISCOUNT_LABEL'); ?>
								</button>
							</div>
							<?php } ?>	
							<div class="panel_total" >
								<div class="panel_warning">
									<div class="alert alert-warning">
										<?php echo JText::_('COM_JEPROSHOP_FOR_THIS_CUSTOMER_GROUP_PRICES_ARE_DISPLAYED_MESSAGE'); ?>
										<strong><?php echo $taxMethod; ?></strong>
										<?php if(!JeproshopSettingModelSetting::getValue('return_order')){ ?>
										<br/><strong><?php echo JText::_('COM_JEPROSHOP_MERCHANDISE_RETURNS_ARE_DISABLE_MESSAGE'); ?></strong>
										<?php } ?>
									</div>
								</div>
								<div class="total_box_wrapper" >
									<div class="panel panel-vouchers" style="<?php if(!sizeof($this->discounts)){ ?> display:none; <?php } ?>" >
										<?php if(sizeof($this->discounts) || $this->can_edit){ ?>
										<div class="table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_NAME_LABEL'); ?></span></th>
														<th><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_VALUE_LABEL'); ?></span></th>
														<?php if($this->can_edit){ ?><th></th><?php } ?>												
													</tr>
												</thead>
												<tbody>
													<?php foreach($this->discounts as $discount){ ?>
													<tr>
														<td><?php echo $discount->name; ?></td>
														<td>
															<?php if($discount->value != 0.00){ ?>
															-
															<?php }
															echo JeproshopTools::displayPrice($discount->value, $this->currency->currency_id); ?>
														</td>
														<?php if($this->can_edit){ ?>
														<td>
															<a href="{$current_index}&amp;submitDeleteVoucher&amp;order_cart_rule_id={$discount['id_order_cart_rule']}&amp;order_id={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
																<i class="icon-minus-sign"></i> <?php echo JText::_('COM_JEPROSHOP_DELETE_VOUCHER_LABEL'); ?>														
															</a>
														</td>
														<?php } ?>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										<div class="current-edit" id="jform_voucher_form" style="display:none;" >
											<div class="form-horizontal well">
												<div class="control-group">
													<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label></div>
													<div class="controls">
														<input class="form-control" type="text" name="jform[discount_name]" value="" />
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-lg-3">
														<?php echo JText::_('COM_JEPROSHOP_TYPE_LABEL'); ?>
													</label>
													<div class="col-lg-9">
														<select class="form-control" name="discount_type" id="discount_type">
															<option value="1"><?php echo JText::_('COM_JEPROSHOP_PERCENT_LABEL'); ?></option>
															<option value="2"><?php echo JText::_('COM_JEPROSHOP_AMOUNT_LABEL'); ?></option>
															<option value="3"><?php echo JText::_('COM_JEPROSHOP_FREE_SHIPPING_LABEL'); ?></option>
														</select>
													</div>
												</div>

												<div id="discount_value_field" class="form-group">
													<label class="control-label col-lg-3">
														<?php echo JText::_('COM_JEPROSHOP_VALUE_LABEL'); ?>
													</label>
													<div class="col-lg-9">
														<div class="input-group">
															<div class="input-group-addon">
																<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
																<span id="discount_percent_symbol">%</span>
															</div>
															<input class="form-control" type="text" name="discount_value"/>
														</div>
														<p class="text-muted" id="discount_value_help" style="display: none;">
															<?php echo JText::_('COM_JEPROSHOP_THIS_VALUE_MUST_INCLUDE_TAXES_LABEL'); ?>
														</p>
													</div>
												</div>

												<?php if($this->order->hasInvoice()){ ?>
												<div class="control-group">
													<div class="control-label" ><label class=" col-lg-3"><?php echo JText::_('COM_JEPROSHOP_INVOICE_LABEL'); ?></label></div>
													<div class="controls" >
														<select name="discount_invoice">
															<?php foreach($this->invoices_collection as $invoice){ ?>
															<option value="<?php echo $invoice->id; ?>" selected="selected">
																<?php echo $invoice->getInvoiceNumberFormatted($current_lang_id). ' - ' . JeproshopTools::displayPrice($invoice->total_paid_tax_incl, $this->order->currency_id); ?>
															</option>
															<?php } ?>
														</select>
													</div>
												</div>

												<div class="control-group">
													<div class="controls">
														<p class="checkbox">
															<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" /> 
															<?php echo JText::_('COM_JEPROSHOP_APPLY_ON_ALL_INVOICE_LABEL'); ?>
														</p>
														<p class="help-block">
															<?php echo JText::_('COM_JEPROSHOP_IF_YOU_CHOOSES_TO_CREATE_THIS_DISCOUNT_FOR_ALL_INVOICES_ONLY_ONE_DISCOUNT_WILL_BE_CREATED_PER_ORDER_INVOICE_LABEL'); ?>
														</p>
													</div>
												</div>
												<?php } ?>

												<div class="row">
													<div class="col-lg-9 col-lg-offset-3">
														<button class="btn btn-default" type="button" id="cancel_add_voucher">
															<i class="icon-remove text-danger"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
														</button>
														<button class="btn btn-default" type="submit" name="submitNewVoucher">
															<i class="icon-ok text-success"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_LABEL'); ?>
														</button>
													</div>
												</div>
											</div>											
										</div>
										<?php } ?>
									</div>
									<div class="panel panel_total">
										<div class="table-responsive">
											<table class="table">
												<!-- {* Assign order price *} -->
												<?php if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){
													$order_product_price = $this->order->total_products;
													$order_discount_price = $this->order->total_discounts_tax_excl;
													$order_wrapping_price = $this->order->total_wrapping_tax_excl;
													$order_shipping_price = $this->order->total_shipping_tax_excl;
												}else{
													$order_product_price = $this->order->total_products_with_tax;
													$order_discount_price = $this->order->total_discounts_tax_incl;
													$order_wrapping_price = $this->order->total_wrapping_tax_incl;
													$order_shipping_price = $this->order->total_shipping_tax_incl;
												} ?>
												<tr id="total_products">
													<td class="text-right"><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></td>
													<td class="amount text-right">
														<?php echo JeproshopTools::displayPrice($order_product_price, $this->currency->currency_id); ?>
													</td>
													<td class="partial_refund_fields current-edit" style="display:none;"></td>
												</tr>
												<tr id="total_discounts" <?php if($this->order->total_discounts_tax_incl == 0){ ?> style="display: none;" <?php } ?>>
													<td class="text-right"><?php echo JText::_('COM_JEPROSHOP_DISCOUNTS_LABEL'); ?></td>
													<td class="amount text-right">
														-<?php echo JeproshopTools::displayPrice($order_discount_price, $this->currency->currency_id); ?>
													</td>
													<td class="partial_refund_fields current-edit" style="display:none;"></td>
												</tr>
												<tr id="total_wrapping" <?php if($this->order->total_wrapping_tax_incl == 0){ ?> style="display: none;" <?php } ?> >
													<td class="text-right"><?php echo JText::_('COM_JEPROSHOP_WRAPPING_LABEL'); ?></td>
													<td class="amount text-right" >
														<?php echo JeproshopTools::displayPrice($order_wrapping_price, $this->currency->currency_id); ?>
													</td>
													<td class="partial_refund_fields current-edit" style="display:none;"></td>
												</tr>
												<tr id="total_shipping">
													<td class="text-right"><?php echo JText::_('COM_JEPROSHOP_SHIPPING_LABEL'); ?></td>
													<td class="amount text-right" >
														<?php echo JeproshopTools::displayPrice($order_shipping_price, $this->currency->currency_id); ?>
													</td>
													<td class="partial_refund_fields current-edit" style="display:none;">
														<div class="input-group">
															<div class="input-group-addon">
																<?php echo $this->currency->prefix . $this->currency->suffix; ?>
															</div>
															<input type="text" name="partialRefundShippingCost" value="0" />
														</div>
													</td>
												</tr>
												<?php if($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED){ ?>
			 									<tr id="total_taxes">
			 										<td class="text-right"><?php echo JText::_('COM_JEPROSHOP_TAXES_LABEL'); ?></td>
			 										<td class="amount text-right" ><?php echo JeproshopTools::displayPrice(($this->order->total_paid_tax_incl - $this->order->total_paid_tax_excl),  $this->currency->currency_id) ?>}</td>
			 										<td class="partial_refund_fields current-edit" style="display:none;"></td>
			 									</tr>
			 									<?php }
												$order_total_price = $this->order->total_paid_tax_incl; ?>
												<tr id="total_order">
													<td class="text-right"><strong><?php echo ucfirst(JText::_('COM_JEPROSHOP_TOTAL_LABEL')); ?></strong></td>
													<td class="amount text-right">
														<strong><?php echo JeproshopTools::displayPrice($order_total_price, $this->currency->currency_id); ?></strong>
													</td>
													<td class="partial_refund_fields current-edit" style="display:none;"></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div style="clear: both; " ></div>
							<div style="display: none;" class="standard_refund_fields form-horizontal panel">
								<div class="form-group">
									<?php if($this->order->hasBeenDelivered() && JeproshopSettingModelSetting::getValue('return_order')){ ?>
									<p class="checkbox">
										<label for="jform_reinsert_quantities">
											<input type="checkbox" id="jform_reinsert_quantities" name="jform[reinsert_quantities]" />
											<?php echo JText::_('COM_JEPROSHOP_RE_STOCK_PRODUCTS_LABEL'); ?>
										</label>
									</p>
									<?php }
									if ((!$this->order->hasBeenDelivered() && $this->order->hasBeenPaid()) || ($this->order->hasBeenDelivered() && JeproshopSettingModelSetting::getValue('return_order'))){ ?>
									<p class="checkbox">
										<label for="generateCreditSlip">
											<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
											<?php echo JText::_('COM_JEPROSHOP_GENERATE_A_CREDIT_CARD_SLIP_LABEL'); ?>
										</label>
									</p>
									<p class="checkbox">
										<label for="generateDiscount">
											<input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
											<?php echo JText::_('COM_JEPROSHOP_GENERATE_A_VOUCHER_LABEL'); ?>
										</label>
									</p>
									<p class="checkbox" id="spanShippingBack" style="display:none;">
										<label for="shippingBack">
											<input type="checkbox" id="shippingBack" name="shippingBack" />
											<?php echo JText::_('COM_JEPROSHOP_REPAY_SHIPPING_COSTS_LABEL'); ?>
										</label>
									</p>
									<?php } ?>
								</div>
								<?php if(!$this->order->hasBeenDelivered() || ($this->order->hasBeenDelivered() && JeproshopSettingModelSetting::getValue('return_order'))){ ?>
								<div class="row">
									<input type="submit" name="cancelProduct" value="<?php if($this->order->hasBeenDelivered()){ echo JText::_('COM_JEPROSHOP_RETURN_PRODUCTS_LABEL'); }elseif($this->order->hasBeenPaid()){ echo JText::_('COM_JEPROSHOP_REFUND_PRODUCTS_LABEL'); }else{ echo JText::_('COM_JEPROSHOP_CANCEL_PRODUCTS_LABEL'); } ?>" class="btn btn-default" />
								</div>
								<?php } ?>
							</div>
							<div style="display:none;" class="partial_refund_fields">
								<p class="checkbox">
									<label for="jform_reinsert_quantities_refund">
										<input type="checkbox" id="jform_reinsert_quantities_refund" name="jform[reinsert_quantities]" />
										<?php echo JText::_('COM_JEPROSHOP_RE_STOCK_PRODUCTS_LABEL'); ?>
									</label>
								</p>
								<p class="checkbox">
									<label for="generateDiscountRefund">
									 	<input type="checkbox" id="generate_discount_refund" name="jform[generate_discount_refund]" onclick="toggleShippingCost()" />
										<?php echo JText::_('COM_JEPROSHOP_GENERATE_A_VOUCHER_LABEL'); ?>
									</label>
								</p>
								<button type="submit" name="partialRefund" class="btn btn-default">
									<i class="icon-check"></i>  <?php echo JText::_('COM_JEPROSHOP_PARTIAL_REFUND_LABEL'); ?>
								</button>
							</div>
						</div>
					</div>
					<div style="display: none" >
						<input type="hidden" value="<?php echo implode(',', $this->order->getWarehouseList()); ?>" id="jform_warehouse_list" />
					</div>
					<input type="hidden" name="order_id" value="<?php echo $this->order->order_id; ?>" />
				</div>
			</form>
		</div>
    </div>	
 </div>

<script type="text/javascript" >
    jQuery(document).ready(function(){
        var order_id = parseInt(<?php echo $this->order->order_id; ?>);
        var order_status_id = parseInt(<?php echo $this->current_status; ?>);
        jQuery('#jform_update_order_status').click(function(){
            jQuery.ajax({
                type: 'POST', url: 'index.php?option=com_jeproshop&rand=' + new Date().getTime(),
                async:true, cache:false, dataType: 'html',
                data: '&view=order&use_ajax=true&task=update_status&order_id=' + order_id + 'order_status_id=' + order_status_id + '&token=',
                success: function(htmlData){ alert(htmlData); },
                error: function(XMLHttpRequest, textStatus, errorThrown){}
            });
        });

    });
</script>