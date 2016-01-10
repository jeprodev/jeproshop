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
<div class="panel" >
	<div class="panel-title" >
		<i class="icon-user"></i>
		<?php  
			echo  ucfirst($this->customer->firstname) . ' ' . ucfirst($this->customer->lastname);
			echo ' [N° '. $this->customer->customer_id . '] - '; ?>
		<a href="mailto:<?php echo $this->customer->email; ?>" >
			<i class="icon-message" ></i><?php echo $this->customer->email; ?>
		</a>
	</div>
	<div class="panel-content well form_layout" >
		<div class="control-group" >
			<div class="control-label" ><label for="jform_civility_title" id="jform_civility_title-lbl" ><?php echo JText::_('COM_JEPROSHOP_CIVILITY_TITLE_LABEL'); ?></label></div>
			<div class="controls"><p><?php echo ucfirst($this->customer->title); ?></p></div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_customer_age" id="jform_customer_age-lbl" ><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_AGE_LABEL'); ?></label></div>
			<div class="controls">
				<p>
					<?php if(isset($this->customer->birthday) && $this->customer->birthday != '0000-00-00'){
						echo $this->customer_stats->age . ' ' .JText::_('COM_JEPROSHOP_YEAR_OLD_LABEL') . ' (' . JText::_('COM_JEPROSHOP_BIRTHDAY_LABEL') . ' : ' . $this->customer_birthday . ')'; 
					}else{ echo JText::_('COM_JEPROSHOP_UNKNOWN_LABEL'); } ?>
				</p>
			</div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_registration_date" id="jform_registration_date-lbl" ><?php echo JText::_('COM_JEPROSHOP_REGISTRATION_DATE_LABEL'); ?></label></div>
			<div class="controls"><p><?php echo $this->registration_date; ?></p></div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_last_visit" id="jform_last_visit-lbl" ><?php echo JText::_('COM_JEPROSHOP_LAST_VISITS_LABEL'); ?></label></div>
			<div class="controls" ><p><?php if($this->customer_stats->last_visit){echo $this->last_visit; }else{ echo JText::_('COM_JEPROSHOP_NEVER_LABEL'); } ?></p></div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_" id="jform_-lbl" ><?php echo JText::_('COM_JEPROSHOP_LANGUAGE_LABEL'); ?></label></div>
			<div class="controls"><p><?php echo $this->count_better_customers; ?></p></div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_shop_name" id="jform_shop_name-lbl" ><?php echo JText::_('COM_JEPROSHOP_SHOP_NAME_LABEL'); ?></label></div>
			<div class="controls"><p><?php echo $this->shop_name; ?></p></div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_LANGUAGE_LABEL'); ?></label></div>
			<div class="controls">
				<p>
					<?php if(isset($this->customerLanguage)){ 
						echo $this->customerLanguage->name;
					}else{ echo JText::_('COM_JEPROSHOP_UNKNOWN_LABEL'); }?>
									
				</p>
			</div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_" id="jform_-lbl" ><?php echo JText::_('COM_JEPROSHOP_OPTION_IN_LABEL'); ?></label></div>
			<div class="controls">
				<p>
					<?php if($this->customer->newsletter){ ?>
					<span class="label label-success"><i class="icon-check"></i><?php echo JText::_('COM_JEPROSHOP_NEWS_LETTER_LABEL'); ?></span>
					<?php } else { ?>
					<span class="label label-danger"><i class="icon-remove"></i><?php echo JText::_('COM_JEPROSHOP_NEWS_LETTER_LABEL'); ?></span>
					<?php } ?>&nbsp;
					<?php if($this->customer->optin){ ?>
					<span class="label label-success"><i class="icon-check"></i><?php echo JText::_('COM_JEPROSHOP_OPT_IN_LABEL'); ?></span>
					<?php }else{ ?>
					<span class="label label-danger"><i class="icon-remove"></i><?php echo JText::_('COM_JEPROSHOP_OPT_IN_LABEL'); ?></span>
					<?php } ?>
				</p>
			</div>
		</div>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_customer_is" id="jform_customer_is-lbl" ><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_IS_LABEL'); ?></label></div>
			<div class="controls" >
				<p>
					<?php if($this->customer->published){ ?>
					<span class="label label-success"><i class="icon-check"></i><?php echo ucfirst(JText::_('COM_JEPROSHOP_ACTIVE_LABEL')); ?></span>
					<?php }else{ ?>
					<span class="label label-danger"><i class="icon-remove"></i><?php echo ucfirst(JText::_('COM_JEPROSHOP_INACTIVE_LABEL')); ?></span>
					<?php } ?>							
				</p>
			</div>
		</div>
	</div>
</div>
<div class="panel" >
	<div class="panel-title"  ><i class="icon-order" ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_ORDERS_LABEL')); ?>&nbsp;<span class="badge badge-success" ><?php echo count($this->orders);?></span></div>
	<div class="panel-content well" >
		<?php if($this->orders && count($this->orders)){
			$count_ok = count($this->orders_ok);
			$count_ko = count($this->orders_ko); ?>
		<div class="panel_row">
			<div class="info_left">
				<i class="icon-save icon-big"></i> <?php echo JText::_('COM_JEPROSHOP_VALID_ORDERS_LABEL') . ' : '; ?>
				<span class="label label-success"><?php echo $count_ok; ?></span>
				<?php echo JText::_('COM_JEPROSHOP_FOR_A_AMOUNT_OF_LABEL') . ' ' . $this->total_ok; ?>
			</div>
			<div class="info_right">
				<i class="icon-exclamation-sign icon-big"></i> <?php echo JText::_('COM_JEPROSHOP_INVALID_ORDERS_LABEL') . ': '; ?>
				<span class="label label-danger"><?php echo $count_ko; ?></span>
			</div><div style="clear:both;" ></div>			
		</div>
		<?php if($count_ok){ ?>
		<table class="table">
			<thead>
				<tr>
					<th class="nowrap center" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_ID_LABEL')); ?></span></th>
					<th class="nowrap" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_DATE_LABEL')); ?></span></th>
					<th class="nowrap" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_PAYMENT_LABEL')); ?></span></th>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_STATUS_LABEL')); ?></span></th>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_PRODUCTS_LABEL')); ?></span></th>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_TOTAL_SPENT_LABEL')); ?></span></th>
					<th class="nowrap"><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->orders_ok AS $key => $order){ ?>
				<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
					<td class="nowrap" ><?php echo $order->order_id; ?></td>
					<td class="nowrap" ><?php echo JeproshopTools::dateFormat($order->date_add, false); ?></td>
					<td class="nowrap" ><?php echo $order->payment; ?></td>
					<td class="nowrap" ><?php echo $order->order_state; ?></td>
					<td class="nowrap center"><?php echo $order->nb_products; ?></td>
					<td class="nowrap center"><?php echo $order->total_paid_real; ?></td>
					<td class="nowrap " >
						<a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&order_id=' . $order->order_id . '&task=view&' . JeproshopTools::getOrderFormToken()); ?>">
							<i class='icon-search'></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_VIEW_LABEL')); ?>
						</a>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php } 
		if($count_ko){ ?>
		<table class="table">
			<thead>
				<tr>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_ID_LABEL')); ?></span></th>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_DATE_LABEL')); ?></span></th>
					<th class="nowrap" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_PAYMENT_LABEL')); ?></span></th>
					<th class="nowrap" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_STATUS_LABEL')); ?></span></th>
					<th class="nowrap" ><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_PRODUCTS_LABEL')); ?></span></th>
					<th class="nowrap"><span><?php echo ucfirst(JText::_('COM_JEPROSHOP_TOTAL_SPENT_LABEL')); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->orders_ko AS $key => $order){ ?>
				<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
					<td class="nowrap" ><a href="?tab=AdminOrders&amp;id_order={$order['id_order']}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}"><?php echo $order->order_id; ?></a></td>
					<td class="nowrap" ><?php echo JeproshopTools::dateFormat($order->date_add, false); ?></td>
					<td class="nowrap" ><?php echo $order->payment; ?></td>
					<td ><?php echo $order->order_state; ?></td>
					<td class="nowrap center" ><?php echo $order->nb_products; ?></td>
					<td class="nowrap center" ><?php echo JeproshopTools::displayPrice($order->total_paid_real, $this->context->currency->currency_id); ?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>	
		<?php } 
		} else {?>
		<p class="text-muted text-center">
			<?php echo ucfirst($this->customer->firstname) . ' ' . strtoupper($this->customer->lastname) . ' ' . JText::_('COM_JEPROSHOP_HAS_NOT_PLACED_ANY_ORDERS_YET_MESSAGE'); ?>
		</p>
		<?php } ?>
	</div>
</div>
<div class="panel" >
	<div class="panel-title" ><i class="icon-shopping-cart" ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_CARTS_LABEL')); ?> <span class="badge badge-success" ><?php echo count($this->carts); ?></span></div>
	<div class="panel-content well" >
		<?php if($this->carts AND count($this->carts)){ ?>
		<table class="table">
			<thead>
				<tr>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_ID_LABEL')); ?></span></th>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_DATE_LABEL')); ?></span></th>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_CARRIER_LABEL')); ?></span></th>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_TOTAL_LABEL')); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->carts AS $key => $cart){ ?>
				<tr onclick="document.location = '?tab=AdminCarts&cart_id={$cart['id_cart']|intval}&amp;viewcart&amp;token={getAdminToken tab='AdminCarts'}'">
					<td><?php echo $cart->cart_id; ?></td>
					<td>
						<a href="index.php?tab=AdminCarts&amp;id_cart={$cart['id_cart']}&amp;viewcart&amp;token={getAdminToken tab='AdminCarts'}">
							<?php echo JeproshopTools::dateFormat($cart->date_upd, false); ?>
						</a>
					</td>
					<td><?php echo $cart->name; ?></td>
					<td><?php echo $cart->total_price; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php }else{ ?>		
		<p class="text-muted text-center">
			<?php echo JText::_('COM_JEPROSHOP_NO_AVAILABLE_CART_MESSAGE'); ?>
		</p>
		<?php } ?>		
	</div>
</div>
<?php if(isset($this->products) && count($this->products)){ ?>
<div class="panel" >
	<div class="panel-title" ><i class="icon-archive" ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_PURCHASED_PRODUCTS_LABEL')); ?> <span class="badge badge-success" ><?php echo count($this->products);?></span></div>
	<div class="panel-content well" >
		<table class="table">
			<thead>
				<tr>
					<th><span class="title_box"><?php echo ucfirst(JText::_('COM_JEPROSHOP_DATE_LABEL')); ?></span></th>
					<th><span class="title_box"><?php echo ucfirst(JText::_('COM_JEPROSHOP_NAME_LABEL')); ?></span></th>
					<th><span class="title_box"><?php echo ucfirst(JText::_('COM_JEPROSHOP_QUANTITY_LABEL')); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->products AS $key => $product){ ?>
				<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$product['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
					<td><?php JeproshopTools::dateFormat($product->date_add, false); ?></td>
					<td>
						<a href="?tab=AdminOrders&amp;id_order={$product['id_order']}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">
							<?php echo $product->product_name; ?>
						</a>
					</td>
					<td><?php echo $product->product_quantity; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php }
if($this->interested_products){ ?>
<div class="panel">
	<div class="panel-title">
		<i class="icon-archive"></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_INTERESTED_PRODUCTS_LABEL')); ?> <span class="badge badge-success"><?php echo count($this->interested_products); ?></span>
	</div>
	<div class="panel-content well" >
		<table class="table">
			<thead>
				<tr>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_ID_LABEL')); ?></span></th>
					<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_NAME_LABEL')); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->interested_products as $key => $product){ ?>
					<tr onclick="document.location = '<?php echo $product->url; ?>'">
						<td><?php echo $product->product_id; ?></td>
						<td><a href="<?php echo $product->url; ?>" ><?php echo $product->name; ?></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php } ?>