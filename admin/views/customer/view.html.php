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

class JeproshopCustomerViewCustomer extends JViewLegacy
{
	protected $customer;

    protected $helper;
    
    protected $customers;
    
    protected $context;
    
    public $pagination;
    
	public function renderDetails($tpl = null){
		if($this->getLayout() !== 'modal'){
            
        }
        
        $customerModel = new JeproshopCustomerModelCustomer();
        $this->customers = $customerModel->getCustomerList();
        $this->pagination = $customerModel->getPagination();
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
	}

    public function renderAddForm($tpl = null){
        if($this->context == null){ $this->context = JeproshopContext::getContext(); }
        $groups = JeproshopGroupModelGroup::getGroups($this->context->language->lang_id, true);

        $this->assignRef('groups', $groups);
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function renderEditForm($tpl = null){
        if($this->context == null){ $this->context = JeproshopContext::getContext(); }
        $groups = JeproshopGroupModelGroup::getGroups($this->context->language->lang_id, true);

        $this->assignRef('groups', $groups);
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function renderView($tpl = null){
		if($this->getLayout() !== 'modal'){
	
		}
		if($this->context == null){ $this->context = JeproshopContext::getContext(); }
		$db = JFactory::getDBO();
		$this->setLayout('view');
		
		$this->loadObject();
		if(!JeproshopTools::isLoadedObject($this->customer, 'customer_id')){ return; }
		$this->context->customer = $this->customer;
				
		$customer_stats = $this->customer->getStats();
		$query = "SELECT SUM(total_paid_real) FROM " . $db->quoteName('#__jeproshop_orders');
		$query .= " WHERE customer_id = " . (int)$this->customer->customer_id . " AND valid = 1";
		$db->setQuery($query);
		$total_customer = $db->loadResult();
		if($total_customer){
			$query = "SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM " . $db->quoteName('#__jeproshop_orders');
			$query .= " WHERE valid = 1 AND customer_id != ".(int)$this->customer->customer_id . " GROUP BY ";
			$query .= "customer_id HAVING SUM(total_paid_real) > " . (int)$total_customer;
			$db->setQuery($query);
			$db->loadResult();
			$count_better_customers = (int)$db->loadResult('SELECT FOUND_ROWS()') + 1;
		}else{
			$count_better_customers = '-';
		}
		$orders = JeproshopOrderModelOrder::getCustomerOrders($this->customer->customer_id, true);
		$total_orders = count($orders);
		for ($i = 0; $i < $total_orders; $i++){
			$orders[$i]->total_paid_real_not_formated = $orders[$i]->total_paid_real;
			$orders[$i]->total_paid_real = JeproshopTools::displayPrice($orders[$i]->total_paid_real, new JeproshopCurrencyModelCurrency((int)$orders[$i]->currency_id));
		}
		
		$messages = JeproshopCustomerThreadModelCustomerThread::getCustomerMessages((int)$this->customer->customer_id);
		$total_messages = count($messages);
		for ($i = 0; $i < $total_messages; $i++){
			$messages[$i]->message = substr(strip_tags(html_entity_decode($messages[$i]->message, ENT_NOQUOTES, 'UTF-8')), 0, 75);
			$messages[$i]->date_add = Tools::displayDate($messages[$i]->date_add, null, true);
		}
		
		$groups = $this->customer->getGroups();
		$total_groups = count($groups);
		for ($i = 0; $i < $total_groups; $i++){
			$group = new JeproshopGroupModelGroup($groups[$i]);
			$groups[$i] = array();
			$groups[$i]['group_id'] = $group->group_id;
			$groups[$i]['name'] = $group->name[$this->context->controller->default_form_language];
		}

		$total_ok = 0;
		$orders_ok = array();
		$orders_ko = array();
		foreach ($orders as $order){
			if (!isset($order->order_state)){
				$order->order_state = JText::_('COM_JEPROSHOP_THERE_IS_NO_STATUS_DEFINED_FOR_THIS_ORDER_MESSAGE'); 
			}
			if ($order->valid){
				$orders_ok[] = $order;
				$total_ok += $order->total_paid_real_not_formated;
			}else{
				$orders_ko[] = $order;
			}
		}
		
		$products = $this->customer->getBoughtProducts();
		
		$carts = JeproshopCartModelCart::getCustomerCarts($this->customer->customer_id);
		$total_carts = count($carts);
		for ($i = 0; $i < $total_carts; $i++){
			$cart = new JeproshopCartModelCart((int)$carts[$i]->cart_id);
			$this->context->cart = $cart;
			$summary = $cart->getSummaryDetails();
			$currency = new JeproshopCurrencyModelCurrency((int)$carts[$i]->currency_id);
			$carrier = new JeproshopCarrierModelCarrier((int)$carts[$i]->carrier_id);
			$carts[$i]['id_cart'] = sprintf('%06d', $carts[$i]['id_cart']);
			$carts[$i]['date_add'] = JeproshopValidator::displayDate($carts[$i]->date_add, null, true);
			$carts[$i]['total_price'] = Tools::displayPrice($summary->total_price, $currency);
			$carts[$i]->name = $carrier->name;
		}
		
		$query = "SELECT DISTINCT cart_product.product_id, cart.cart_id, cart.shop_id, cart_product.shop_id ";
		$query .= " AS cart_product_shop_id FROM " . $db->quoteName('#__jeproshop_cart_product') . " AS cart_product";
		$query .= " JOIN " . $db->quoteName('#__jeproshop_cart') . " AS cart ON (cart.cart_id = cart_product.cart_id) ";
		$query .= "JOIN " . $db->quoteName('#__jeproshop_product') . " AS product ON (cart_product.product_id = product.";
		$query .= "product_id) WHERE cart.customer_id = " . (int)$this->customer->customer_id . " AND cart_product.product_id";
		$query .= " NOT IN ( SELECT product_id FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord JOIN ";
		$query .= $db->quoteName('#__jeproshop_order_detail') . " AS ord_detail ON (ord.order_id = ord_detail.order_id ) WHERE ";
		$query .= "ord.valid = 1 AND ord.customer_id = " . (int)$this->customer->customer_id . ")";
		
		$db->setQuery($query);
		$interested = $db->loadObjectList();
		$total_interested = count($interested);
		for ($i = 0; $i < $total_interested; $i++){
			$product = new JeproshopProductModelProduct($interested[$i]->product_id, false, 
					$this->context->controller->default_form_language, $interested[$i]->shop_id);
			if (!Validate::isLoadedObject($product, 'product_id')){ continue; }
					
			$interested[$i]->url = $this->context->controller->getProductLink(
				$product->product_id, $product->link_rewrite,
				JeproshopCategoryModelCategory::getLinkRewrite($product->default_category_id, 
				$this->context->controller->default_form_language), null, null, $interested[$i]->cp_shop_id
			);
			$interested[$i]->product_id = (int)$product->product_id;
			$interested[$i]->name = htmlentities($product->name);
		}

		$connections = $this->customer->getLastConnections();
		if (!is_array($connections))
			$connections = array();
		$total_connections = count($connections);
					
		for ($i = 0; $i < $total_connections; $i++){
			$connections[$i]->http_referer = $connections[$i]->http_referer ? preg_replace('/^www./', '', parse_url($connections[$i]->http_referer, PHP_URL_HOST)) : JText::_('COM_JEPROSHOP_DIRECT_LINK_LABEL');
		}
		$referrers = JeproshopReferrerModelReferrer::getReferrers($this->customer->customer_id);
		$total_referrers = count($referrers);
		for ($i = 0; $i < $total_referrers; $i++){
			$referrers[$i]->date_add = JeproshopTools::displayDate($referrers[$i]->date_add,null , true);
		}
		$customerLanguage = new JeproshopLanguageModelLanguage($this->customer->lang_id);
		$shop = new JeproshopShopModelShop($this->customer->shop_id);
		
		//$this->assignRef('customer', $customer);
			/*'gender' => $gender,
		/*	'gender_image' => $gender_image,
		// General information of the customer */
        $registration = JeproshopTools::displayDate($this->customer->date_add,null , true);
		$this->assignRef('registration_date', $registration);
		$this->assignRef('customer_stats', $customer_stats);
        $last_visit = JeproshopTools::displayDate($customer_stats->last_visit,null , true);
		$this->assignRef('last_visit', $last_visit);
		$this->assignRef('count_better_customers', $count_better_customers);
        $shop_feature_active = JeproshopShopModelShop::isFeaturePublished();
		$this->assignRef('shop_is_feature_active', $shop_feature_active);
		$this->assignRef('shop_name', $shop->shop_name);
        $customerBirthday = JeproshopTools::displayDate($this->customer->birthday);
		$this->assignRef('customer_birthday', $customerBirthday);
        $last_update = JeproshopTools::displayDate($this->customer->date_upd, null , true);
		$this->assignRef('last_update', $last_update);
        $customerExists = JeproshopCustomerModelCustomer::customerExists($this->customer->email);
		$this->assignRef('customer_exists', $customerExists);
		$this->assignRef('lang_id', $this->customer->lang_id);
		$this->assignRef('customerLanguage', $customerLanguage);
		// Add a Private note
        $customerNote = JeproshopTools::htmlentitiesUTF8($this->customer->note);
		$this->assignRef('customer_note', $customerNote);
		// Messages
		$this->assignRef('messages', $messages);
		// Groups
		$this->assignRef('groups', $groups);
		// Orders
		$this->assignRef('orders', $orders);
		$this->assignRef('orders_ok', $orders_ok);
		$this->assignRef('orders_ko', $orders_ko);
        $total_ok = JeproshopTools::displayPrice($total_ok, $this->context->currency->currency_id);
		$this->assignRef('total_ok', $total_ok);
		// Products
		$this->assignRef('products', $products);
		// Addresses
        $addresses = $this->customer->getAddresses($this->context->controller->default_form_language);
		$this->assignRef('addresses', $addresses);
		// Discounts
        $discounts = JeproshopCartRuleModelCartRule::getCustomerCartRules($this->context->controller->default_form_language, $this->customer->customer_id, false, false);
		$this->assignRef('discounts', $discounts);
		// Carts
		$this->assignRef('carts', $carts);
		// Interested
		$this->assignRef('interested_products', $interested);
		// Connections
		$this->assignRef('connections', $connections);
		// Referrers
		$this->assignRef('referrers', $referrers);
		
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	/**
	 * Load class object using identifier in $_GET (if possible)
	 * otherwise return an empty object, or die
	 *
	 * @param boolean $opt Return an empty object if load fail
	 * @return object|boolean
	 */
	public function loadObject($opt = false){
		/*if (!isset($this->customer) || empty($this->customer))
			return true;
		*/
		$app = JFactory::getApplication();
		$customer_id = (int)$app->input->get('customer_id');
		if ($customer_id && JeproshopTools::isUnsignedInt($customer_id)){
			if (!$this->customer)
				$this->customer = new JeproshopCustomerModelCustomer($customer_id);
			if (JeproshopTools::isLoadedObject($this->customer, 'customer_id')){
				return $this->customer;
			}
			// throw exception
			//$this->errors[] = Tools::displayError('The object cannot be loaded (or found)');
			return false;
		}elseif ($opt){
			if (!$this->customer)
				$this->customer = new JeproshopCustomerModelCustomer();
			return $this->customer;
		}else{
			$this->errors[] = Tools::displayError('The object cannot be loaded (the identifier is missing or invalid)');
			return false;
		}
	}
	
	public function viewThreads($tpl = null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}

	private function addToolBar(){
		switch ($this->getLayout()){
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_VIEW_CUSTOMER_TITLE'), 'jeproshop-order');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_CUSTOMERS_LIST_TITLE'), 'jeproshop-order');
				JToolBarHelper::addNew('add');
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers', true);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
	}

    protected function renderSubMenu($current = 'customer'){
        $script = '<div class="box_wrapper jeproshop_sub_menu_wrapper"><fieldset class="btn-group">';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=customer') . '" class="btn jeproshop_sub_menu ' . (($current == 'customer' ) ? 'btn-success' : '') . '" ><i class="icon-customer" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=address') . '" class="btn jeproshop_sub_menu ' . (($current == 'address' ) ? 'btn-success' : '') . '" ><i class="icon-address" ></i> '. ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=group') . '" class="btn jeproshop_sub_menu ' . (($current == 'group' ) ? 'btn-success' : '') . '" ><i class="icon-group" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=cart') . '" class="btn jeproshop_sub_menu ' . (($current == 'cart' ) ? 'btn-success' : '') . '" ><i class="icon-cart" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads') . '" class="btn jeproshop_sub_menu ' . (($current == 'threads' ) ? 'btn-success' : '') . '" ><i class="icon-thread" ></i> ' .  ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=contact') . '" class="btn jeproshop_sub_menu ' . (($current == 'contact' ) ? 'btn-success' : '') . '" ><i class="icon-contact" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')) . '</a>';
        $script .= '</fieldset></div>';
        return $script;
    }
}