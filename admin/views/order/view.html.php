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

class JeproshopOrderViewOrder extends JViewLegacy
{
	public $context = null;

    public function renderDetails($tpl = null){
        $orderModel = new JeproshopOrderModelOrder();
        $orders = $orderModel->getOrderList();

        $this->assignRef('orders', $orders);
        $pagination = $orderModel->getPagination();
        $this->assignRef('pagination', $pagination);

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

	public function display($tpl = null){
		if(!isset($this->context) || $this->context == null){ $this->context = JeproshopContext::getContext(); } 
		
		//$orderModel = new JeproshopOrderModelOrder();
		//$this->orders = $orderModel->getOrderList();

	}

    public function renderInvoicesList($tpl = null){
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }
	public function renderDeliveriesList($tpl = null){
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }
	public function renderMessagesList($tpl = null){
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderRefundsList($tpl = null){
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderReturnsList($tpl = null){
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

	public function renderView($tpl = NULL){
		if(!isset($this->context) || $this->context == null){ $this->context = JeproshopContext::getContext(); }
		
		$app = JFactory::getApplication();
		
		$order = new JeproshopOrderModelOrder($app->input->get('order_id'));
		if (!JeproshopTools::isLoadedObject($order, 'order_id')){
			JError::raiseError(500, JText::_('COM_JEPROSHOP_THE_ORDER_CANNOT_BE_FOUND_WITHIN_YOUR_DATA_BASE_MESSAGE'));
		}
		
		$customer = new JeproshopCustomerModelCustomer($order->customer_id);
		$carrier = new JeproshopCarrierModelCarrier($order->carrier_id);
		$products = $this->getProducts($order);
		$currency = new JeproshopCurrencyModelCurrency((int)$order->currency_id);
		// Carrier module call
		$carrier_module_call = null;
		if ($carrier->is_module){
			/*$module = Module::getInstanceByName($carrier->external_module_name);
			if (method_exists($module, 'displayInfoByCart'))
				$carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart); */
		}
		
		// Retrieve addresses information
		$addressInvoice = new JeproshopAddressModelAddress($order->address_invoice_id, $this->context->language->lang_id);
		if (JeproshopTools::isLoadedObject($addressInvoice, 'address_id') && $addressInvoice->state_id){
			$invoiceState = new JeproshopStateModelState((int)$addressInvoice->state_id);
		}
		
		if ($order->address_invoice_id == $order->address_delivery_id){
			$addressDelivery = $addressInvoice;
			if (isset($invoiceState)){ $deliveryState = $invoiceState; } 
		}else{
			$addressDelivery = new JeproshopAddressModelAddress($order->address_delivery_id, $this->context->language->lang_id);
			if(JeproshopTools::isLoadedObject($addressDelivery, 'address_id') && $addressDelivery->state_id){
				$deliveryState = new JeproshopStateModelState((int)($addressDelivery->state_id));
			}
		}

		$title = JText::_('COM_JEPROSHOP_ORDER_LABEL') . ' '; //todo learn how to display

		//$toolbar_title = sprintf($this->l('Order #%1$d (%2$s) - %3$s %4$s'), $order->order_id, $order->reference, $customer->firstname, $customer->lastname);
		if (JeproshopShopModelShop::isFeaturePublished()){
			$shop = new JeproshopShopModelShop((int)$order->shop_id);
			//$this->toolbar_title .= ' - '.sprintf($this->l('Shop: %s'), $shop->name);
		}
        JToolBarHelper::title($title);
		// gets warehouses to ship products, if and only if advanced stock management is activated
		$warehouse_list = null;
		
		$order_details = $order->getOrderDetailList();
		foreach ($order_details as $order_detail){
			$product = new JeproshopProductModelProduct($order_detail->product_id );		
			if (JeproshopSettingModelSetting::getValue('advanced_stock_management') && $product->advanced_stock_management){
				$warehouses = JeproshopWarehouseModelWarehouse::getWarehousesByProductId($order_detail->product_id, $order_detail->product_attribute_id);
				foreach ($warehouses as $warehouse){
					if (!isset($warehouse_list[$warehouse->warehouse_id])){ $warehouse_list[$warehouse->warehouse_id] = $warehouse; }
				}
			}
		}
		
		$payment_methods = array();
		/*foreach (PaymentModule::getInstalledPaymentModules() as $payment)
		{
			$module = Module::getInstanceByName($payment['name']);
			if (Validate::isLoadedObject($module) && $module->active)
				$payment_methods[] = $module->displayName;
		}*/
		
		// display warning if there are products out of stock
		$display_out_of_stock_warning = false;
		$current_order_status = $order->getCurrentOrderStatus();
		if(JeproshopSettingModelSetting::getValue('stock_management') && (!JeproshopTools::isLoadedObject($current_order_status, 'order_id') || ($current_order_status->delivery != 1 && $current_order_status->shipped != 1))){
			$display_out_of_stock_warning = true;
		}
		// products current stock (from stock_available)
		foreach ($products as &$product){
			$product->current_stock = JeproshopStockAvailableModelStockAvailable::getQuantityAvailableByProduct($product->product_id, $product->product_attribute_id, $product->shop_id);
				
			$resume = JeproshopOrderSlipModelOrderSlip::getProductSlipResume($product->order_detail_id);
			$product->quantity_refundable = $product->product_quantity - $resume->product_quantity;
			$product->amount_refundable = $product->total_price_tax_incl - $resume->amount_tax_incl;
			$product->amount_refund = JeproshopTools::displayPrice($resume->amount_tax_incl, $currency);
			$product->refund_history = JeproshopOrderSlipModelOrderSlip::getProductSlipDetail($product->order_detail_id);
			$product->return_history = JeproshopOrderReturnModelOrderReturn::getProductReturnDetail($product->order_detail_id);
				
			// if the current stock requires a warning
			if ($product->current_stock == 0 && $display_out_of_stock_warning)
				JError::raiseWarning(500, JText::_('COM_JEPROSHOP_THIS_PRODUCT_IS_OUT_OF_STOCK_LABEL'). ' : '.$product->product_name);
			if ($product->warehouse_id != 0){
				$warehouse = new JeproshopWarehouseModelWarehouse((int)$product->warehouse_id);
				$product->warehouse_name = $warehouse->name;
			}else{
				$product->warehouse_name = '--';
			}
		}

		//$gender = new Gender((int)$customer->id_gender, $this->context->language->id);
		
		$history = $order->getHistory($this->context->language->lang_id);
		
		foreach ($history as &$order_state){
			$order_state->text_color = JeproshopTools::getBrightness($order_state->color) < 128 ? 'white' : 'black';
		}

        $this->setLayout('view');
		$this->assignRef('order', $order);
		$cart = new JeproshopCartModelCart($order->cart_id);
		$this->assignRef('cart', $cart);
		$this->assignRef('customer', $customer);
        $customer_addresses = $customer->getAddresses($this->context->language->lang_id);
		$this->assignRef('customer_addresses', $customer_addresses);

		$this->assignRef('delivery_address', $addressDelivery);
		$this->assignRef('deliveryState', isset($deliveryState) ? $deliveryState : null);
		$this->assignRef('invoice_address', $addressInvoice);
		$this->assignRef('invoiceState', isset($invoiceState) ? $invoiceState : null);
        $customerStats = $customer->getStats();
		$this->assignRef('customerStats', $customerStats);
		$this->assignRef('products', $products);
        $discounts = $order->getCartRules();
		$this->assignRef('discounts',$discounts);
        $orderTotalPaid = $order->getOrdersTotalPaid();
		$this->assignRef('orders_total_paid_tax_incl', $orderTotalPaid); // Get the sum of total_paid_tax_incl of the order with similar reference
        $totalPaid = $order->getTotalPaid();
		$this->assignRef('total_paid', $totalPaid);
        $returns =  JeproshopOrderReturnModelOrderReturn::getOrdersReturn($order->customer_id, $order->order_id);
		$this->assignRef('returns',$returns);
        $customerThreads =  JeproshopCustomerThreadModelCustomerThread::getCustomerMessages($order->customer_id);
		$this->assignRef('customer_thread_message', $customerThreads);
        $orderMessages = JeproshopOrderMessageModelOrderMessage::getOrderMessages($order->lang_id);
		$this->assignRef('order_messages', $orderMessages);
        $messages = JeproshopMessageModelMessage::getMessagesByOrderId($order->order_id, true);
		$this->assignRef('messages', $messages);
        $carrier = new JeproshopCarrierModelCarrier($order->carrier_id);
		$this->assignRef('carrier', $carrier);
		$this->assignRef('history', $history);
        $statues = JeproshopOrderStatusModelOrderStatus::getOrderStatus($this->context->language->lang_id);
		$this->assignRef('order_statues', $statues);
		$this->assignRef('warehouse_list', $warehouse_list);
        $sources = JeproshopConnectionSourceModelConnectionSource::getOrderSources($order->order_id);
		$this->assignRef('sources', $sources);
        $orderStatus = $order->getCurrentOrderStatus();
		$this->assignRef('current_status', $orderStatus);
		$this->assignRef('currency', new JeproshopCurrencyModelCurrency($order->currency_id));
        $currencies = JeproshopCurrencyModelCurrency::getCurrenciesByShopId($order->shop_id);
		$this->assignRef('currencies', $currencies);
        $previousOrder = $order->getPreviousOrderId();
		$this->assignRef('previousOrder', $previousOrder);
        $nextOrder = $order->getNextOrderId();
		$this->assignRef('nextOrder', $nextOrder);
		//$this->assignRef('current_index', self::$currentIndex);
		$this->assignRef('carrier_module_call', $carrier_module_call);
		$this->assignRef('iso_code_lang', $this->context->language->iso_code);
		$this->assignRef('lang_id', $this->context->language->lang_id);
		$can_edit = true;
		$this->assignRef('can_edit', $can_edit); //($this->tabAccess['edit'] == 1));
		$this->assignRef('current_id_lang', $this->context->language->lang_id);
        $invoiceCollection = $order->getInvoicesCollection();
		$this->assignRef('invoices_collection', $invoiceCollection);
        $unPaid = $order->getNotPaidInvoicesCollection();
		$this->assignRef('not_paid_invoices_collection', $unPaid);
		$this->assignRef('payment_methods', $payment_methods);
        $invoiceAllowed = JeproshopSettingModelSetting::getValue('invoice_allowed');
		$this->assignRef('invoice_management_active', $invoiceAllowed);
		$display_warehouse = (int)JeproshopSettingModelSetting::getValue('advanced_stock_management');
		$this->assignRef('display_warehouse', $display_warehouse);
        $stockManagement = JeproshopSettingModelSetting::getValue('stock_management');
		$this->assignRef('stock_management', $stockManagement);
		/*$this->assignRef('HOOK_CONTENT_ORDER', Hook::exec('displayAdminOrderContentOrder', array(
				'order' => $order,
				'products' => $products,
				'customer' => $customer)
				),
		$this->assignRef('HOOK_CONTENT_SHIP' => Hook::exec('displayAdminOrderContentShip', array(
				'order' => $order,
				'products' => $products,
				'customer' => $customer)
				),
		$this->assignRef('HOOK_TAB_ORDER' => Hook::exec('displayAdminOrderTabOrder', array(
				'order' => $order,
				'products' => $products,
				'customer' => $customer)
				
		$this->assignRef('HOOK_TAB_SHIP' => Hook::exec('displayAdminOrderTabShip', array(
		$this->assignRef('order' => $order,
		$this->assignRef('products' => $products,
		$this->assignRef('customer' => $customer) */

		$this->addToolBar();
		$this->sideBar = JHtmlSideBar::render();
		
		parent::display($tpl);
	}
	
	protected function getProducts($order){
		$products = $order->getProducts();
	
		foreach ($products as &$product){
			if ($product->image != null){
				$name = 'product_mini_'. (int)$product->product_id .(isset($product->product_attribute_id) ? '_'.(int)$product->product_attribute_id : '').'.jpg';
				// generate image cache, only for back office
				$product->image_tag = JeproshopImageManager::thumbnail(COM_JEPROSHOP_IMAGE_DIR .'products/'.$product->image->getExistingImagePath().'.jpg', $name, 45, 'jpg');
				if (file_exists(COM_JEPROSHOP_IMAGE_DIR . $name)){
					$product->image_size = getimagesize(COM_JEPROSHOP_IMAGE_DIR . $name);
				}else{ $product->image_size = false; }
			}
		}	
		return $products;
	}

    public function renderAddForm($tpl = null){ //print_r(JeproshopCustomerModelCustomer::searchByName('je'));
        $context = JeproshopContext::getContext();
        if ($context->shop->getShopContext() != JeproshopShopModelShop::CONTEXT_SHOP && JeproshopShopModelShop::isFeaturePublished()) {
            $context->controller->has_errors = true;
            $this->l('You have to select a shop before creating new orders.');
        }
        $app = JFactory::getApplication();
        $cart_id = (int)$app->input->get('cart_id');
        $cart = new JeproshopCartModelCart((int)$cart_id);
        if ($cart_id && !JeproshopTools::isLoadedObject($cart, 'cart_id')) {
            $context->controller->has_errors = true;
            $this->l('This cart does not exists');
        }

        if ($cart_id && JeproshopTools::isLoadedObject($cart, 'cart_id') && !$cart->customer_id) {
            $context->controller->has_errors = true;
            $this->l('The cart must have a customer');
        }
        if ($context->controller->has_errors)
            return false;

        /*parent::renderForm();
        unset($this->toolbar_btn['save']);
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
*/
        $defaults_order_statues = array('cheque' => (int)JeproshopSettingModelSetting::getValue('order_status_cheque'),
            'bank_wire' => (int)JeproshopSettingModelSetting::getValue('order_status_bank_wire'),
            'cash_on_delivery' => (int)JeproshopSettingModelSetting::getValue('order_status_preparation'),
            'other' => (int)JeproshopSettingModelSetting::getValue('order_status_payment'));
        $payment_modules = array();
      /*  foreach (PaymentModule::getInstalledPaymentModules() as $p_module)
            $payment_modules[] = Module::getInstanceById((int)$p_module['id_module']);
*/

        $recyclable_pack = (int)JeproshopSettingModelSetting::getValue('offer_recycled_wrapping');
        $this->assignRef('recyclable_pack', $recyclable_pack);
        $gift_wrapping = (int)JeproshopSettingModelSetting::getValue('offer_gift_wrapping');
        $this->assignRef('gift_wrapping', $gift_wrapping);
        $this->assignRef('cart',  $cart);
        $this->assignRef('cart_id', $cart_id);
        $currencies = JeproshopCurrencyModelCurrency::getCurrenciesByShopId(JeproshopContext::getContext()->shop->shop_id);
        $this->assignRef('currencies', $currencies);
        $languages = JeproshopLanguageModelLanguage::getLanguages(true, JeproshopContext::getContext()->shop->shop_id);
        $this->assignRef('languages', $languages);
        $this->assignRef('payment_modules', $payment_modules);
        $order_statues = JeproshopOrderStatusModelOrderStatus::getOrderStatus((int)JeproshopContext::getContext()->language->lang_id);
        $this->assignRef('order_statues', $order_statues);
        $this->assignRef('defaults_order_statues', $defaults_order_statues);
        /*    'show_toolbar' => $this->show_toolbar,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'title' => array($this->l('Orders'), $this->l('Create order'))
        ));* /
        $this->content .= $this->createTemplate('form.tpl')->fetch(); */
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderStatusList($tpl = null){
        $orderStatusModel = new JeproshopOrderStatusModelOrderStatus();
        $orderStatusList = $orderStatusModel->getOrderStatusList();
        $orderPagination = $orderStatusModel->getPagination();
        $this->assignRef('orderStatusList', $orderStatusList);
        $this->assignRef('statues_pagination', $orderPagination);

        $returnStatues = $orderStatusModel->getOrderReturnStatusList();
        $this->assignRef('returnStatues', $returnStatues);


        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

	private function addToolBar(){
		switch ($this->getLayout()){
			case 'order':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_VIEW_ORDER_TITLE'), 'jeproshop-order');
				/*JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel'); */
				break;
            case 'add' :
                break;
            case 'view' :
                break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ORDERS_LIST_TITLE'), 'jeproshop-order');
				JToolBarHelper::addNew('add', JText::_('COM_JEPROSHOP_ADD_NEW_ORDER_LABEL'));
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders', true);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
	}
}
