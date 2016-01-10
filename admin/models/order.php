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

class JeproshopOrderModelOrder extends JModelLegacy
{
    public $order_id; 
    
    public $address_delivery_id;
    public $address_delivery;
    
    public $address_invoice_id;
    public $address_invoice;
    
    public $shop_group_id;
    
    public $shop_id;
    
    public $cart_id;
    
    public $currency_id;
    
    public $lang_id;
    
    public $customer_id;
    
    public $carrier_id;
    
    public $current_status;
    
    public $secure_key;
    
    public $payment;
    
    public $conversion_rate;
    
    public $recyclable = 1;
    
    public $gift = 0;
    
    public $gift_message;
    
    public $mobile_theme;
    
    public $shipping_number;
    
    public $total_discounts;
    
    public $total_discounts_tax_incl;
    public $total_discounts_tax_excl;
    
    public $total_paid;
    public $total_paid_tax_incl;
    public $total_paid_tax_excl;
    
    public $total_paid_real;
    
    public $total_products;
    
    public $total_products_with_tax;
    
    public $total_shipping;
    public $total_shipping_tax_excl;
    public $total_shipping_tax_incl;
    
    public $carrier_tax_rate;
    
    public $total_wrapping;
    public $total_wrapping_tax_incl;
    public $total_wrapping_tax_excl;
    
    public $invoice_number;
    public $invoice_date;
    
    public $delivery_number;
    public $delivery_date;
    
    public $valid;
    
    public $date_add;
    public $date_upd;
    
    public $reference;
    
    public $multishop_context = -1;
    public $multishop_context_group = true;
    
    protected $context;

    protected $_taxCalculationMethod = COM_JEPROSHOP_TAX_EXCLUDED;
    protected static $_historyCache = array();

    private $pagination;
    
    public function __construct($order_id = null, $lang_id = null){
    	if($lang_id !== NULL){
    		$this->lang_id = JeproshopLanguageModelLanguage::getLanguage($lang_id) !== FALSE ? (int)$lang_id : (int)JeproshopSettingModelSetting::getValue('default_lang');
    	}
    	
    	if($order_id){
    		$cache_id = 'jeproshop_order_model_' . $order_id . '_' . $lang_id . ( $this->shop_id ? '_' . $this->shop_id : '');
    		if(!JeproshopCache::isStored($cache_id)){
    			$db = JFactory::getDBO();
    	
    			$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord ";
    			$where = "";
    			/** get language information **/
    			if($lang_id){
    				$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_order_lang') . " AS order_lang ";
    				$query .= " ON (ord.order_id = order_lang.order_id AND order_lang.lang_id = " . (int)$lang_id . ") ";
    				if($this->shop_id && !(empty($this->multiLangShop))){
    					$where = " AND order_lang.shop_id = " . $this->shop_id;
    				}
    			}
    	
    			/** Get shop informations **/
    			if(JeproshopShopModelShop::isTableAssociated('order')){
    				$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_order_shop') . " AS order_shop ON (";
    				$query .= "ord.order_id = order_shop.order_id AND order_shop.shop_id = " . (int)  $this->shop_id . ")";
    			}
    			$query .= " WHERE ord.order_id = " . (int)$order_id . $where;
    	
    			$db->setQuery($query);
    			$order_data = $db->loadObject();
    	
    			if($order_data){    				
    				JeproshopCache::store($cache_id, $order_data);
    			}
    		}else{
    			$order_data = JeproshopCache::retrieve($cache_id);
    		}
    	
    		if($order_data){
    			$order_data->order_id = $order_id;
    			foreach($order_data as $key => $value){
    				if(property_exists($key, $this)){
    					$this->{$key} = $value;
    				}
    			}
    		}
    	}
    	$this->_taxCalculationMethod = JeproshopGroupModelGroup::getDefaultPriceDisplayMethod();
    }

    /**
     * Get order products
     *
     * @param bool $products
     * @param bool $selectedProducts
     * @param bool $selectedQty
     * @return array Products with price, quantity (with tax and without)
     */
    public function getProducts($products = false, $selectedProducts = false, $selectedQty = false){
    	if (!$products){
    		$products = $this->getProductsDetail();
    	}
    	$customized_datas = JeproshopProductModelProduct::getAllCustomizedDatas($this->cart_id);
    
    	$resultArray = array();
    	foreach ($products as $row){
    		// Change qty if selected
    		if ($selectedQty){
    			$row->product_quantity = 0;
    			foreach ($selectedProducts as $key => $product_id){
    				if ($row->order_detail_id == $product_id){
    					$row->product_quantity = (int)($selectedQty[$key]);
    				}
    			}
    			if (!$row->product_quantity){ continue; }
    		}
    
    		$this->setProductImageInformations($row);
    		$this->setProductCurrentStock($row);
    
    		// Backward compatibility 1.4 -> 1.5
    		$this->setProductPrices($row);
    
    		$this->setProductCustomizedDatas($row, $customized_datas);
    
    		// Add information for virtual product
    		if ($row->download_hash && !empty($row->download_hash))	{
    			$row->filename = JeproshopProductDownloadModelProductDownload::getFilenameFromProductId((int)$row->product_id);
    			// Get the display filename
    			$row->display_filename = JeproshopProductDownloadModelProductDownload::getFilenameFromFilename($row->filename);
    		}
    			
    		$row->address_delivery_id = $this->address_delivery_id;
    			
    		/* Stock product */
    		$resultArray[(int)$row->order_detail_id] = $row;
    	}
    
    	if ($customized_datas)
    		JeproshopProductModelProduct::addCustomizationPrice($resultArray, $customized_datas);
    
    	return $resultArray;
    }

    /**
     * Get order history
     *
     * @param integer $lang_id Language id
     * @param bool|int $order_status_id Filter a specific order status
     * @param bool|int $no_hidden Filter no hidden status
     * @param integer $filters Flag to use specific field filter
     * @return array History entries ordered by date DESC
     */
    public function getHistory($lang_id, $order_status_id = false, $no_hidden = false, $filters = 0){
    	if (!$order_status_id){ $order_status_id = 0; }
    
    	$logable = false;
    	$delivery = false;
    	$paid = false;
    	$shipped = false;
    	if ($filters > 0){
    		if ($filters & JeproshopOrderStatusModelOrderStatus::FLAG_NO_HIDDEN){ $no_hidden = true; }
    		
    		if ($filters & JeproshopOrderStatusModelOrderStatus::FLAG_DELIVERY){ $delivery = true; }
    		
    		if ($filters & JeproshopOrderStatusModelOrderStatus::FLAG_LOGABLE){ $logable = true; }
    		
    		if ($filters & JeproshopOrderStatusModelOrderStatus::FLAG_PAID){ $paid = true;}
    		
    		if ($filters & JeproshopOrderStatusModelOrderStatus::FLAG_SHIPPED){ $shipped = true; }
    	}
    
    	if (!isset(self::$_historyCache[$this->order_id.'_'.$order_status_id .'_'.$filters]) || $no_hidden){
    		$db = JFactory::getDBO();
    		$lang_id = $lang_id ? (int)($lang_id) : 'o.`id_lang`';
    		
    		$query = "SELECT order_status.*, order_history.*, employee." . $db->quoteName('username') . " AS employee_firstname,";
    		$query .= " employee." .$db->quoteName('name') . " AS employee_lastname, order_status_lang." . $db->quoteName('name');
    		$query .= " AS order_status_name FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord LEFT JOIN ";
    		$query .= $db->quoteName('#__jeproshop_order_history') . " AS order_history ON ord." . $db->quoteName('order_id');
    		$query .= " = order_history." . $db->quoteName('order_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_order_status');
    		$query .= " AS order_status ON order_status." . $db->quoteName('order_status_id') . " = order_history." . $db->quoteName('order_status_id');
    		$query .= "	LEFT JOIN " . $db->quoteName('#__jeproshop_order_status_lang') . " AS order_status_lang ON (order_status.";
    		$query .= $db->quoteName('order_status_id') . " = order_status_lang." . $db->quoteName('order_status_id') . " AND order_status_lang.";
    		$query .= $db->quoteName('lang_id') . " = " . (int)($lang_id) . ") LEFT JOIN " . $db->quoteName('#__users') . " AS employee ON";
    		$query .= " employee." . $db->quoteName('id') . " = order_history." . $db->quoteName('employee_id') . " WHERE order_history.order_id = ";
    		$query .= (int)($this->order_id) . ($no_hidden ? " AND order_status.hidden = 0" : "") . ($logable ? " AND order_status.logable = 1" : "");
    		$query .= ($delivery ? " AND order_status.delivery = 1" : "") . ($paid ? " AND order_status.paid = 1" : "") . ($shipped ? " AND order_status.shipped = 1" : "");
    		$query .= ((int)($order_status_id) ? " AND order_history." . $db->quoteName('order_status_id') . " = " . (int)($order_status_id) : "");
    		$query .= " ORDER BY order_history.date_add DESC, order_history.order_history_id DESC";
    		
    		$db->setQuery($query);
    		$result = $db->loadObjectList();
    		if ($no_hidden)
    			return $result;
    		self::$_historyCache[$this->order_id.'_'.$order_status_id .'_'.$filters] = $result;
    	}
    	return self::$_historyCache[$this->order_id.'_'.$order_status_id.'_'.$filters];
    }
    
    /**
     *
     * Has invoice return true if this order has already an invoice
     * @return bool
     */
    public function hasInvoice(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT " . $db->quoteName('order_invoice_id') . "	FROM " . $db->quoteName('#__jeproshop_order_invoice');
    	$query .= "	WHERE " . $db->quoteName('order_id') . " =  " .(int)$this->order_id . "	AND " . $db->quoteName('number') . " > 0";
    	
    	$db->setQuery($query);
    	$result = $db->loadResult();
    	
    	return ( $result ? $result : false);
    }
    
    /**
     *
     * Has Delivery return true if this order has already a delivery slip
     * @return bool
     */
    public function hasDelivery(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT " . $db->quoteName('order_invoice_id') . "	FROM " . $db->quoteName('#__jeproshop_order_invoice') . " WHERE ";
    	$query .= $db->quoteName('order_id') . " =  " . (int)$this->order_id . " AND " . $db->quoteName('delivery_number') . " > 0";
    	
    	$db->setQuery($query);
    	$result = $db->loadResult();
    	
    	return ( $result ? $result : false);
    }
    
    public function getCartRules(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_cart_rule') . " AS order_cart_rule WHERE order_cart_rule.";
    	$query .= $db->quoteName('order_id') . " = " . (int)$this->order_id;
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     * Get the sum of total_paid_tax_incl of the orders with similar reference
     *
     * @since 1.5.0.1
     * @return float
     */
    public function getOrdersTotalPaid(){
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT SUM(total_paid_tax_incl) FROM " . $db->quoteName('#__jeproshop_orders') . " WHERE ";
    	$query .= $db->quoteName('reference') . " = " . $db->quote($this->reference) . " AND " . $db->quoteName('cart_id');
    	$query .= " = " . (int)$this->cart_id;
    	
    	$db->setQuery($query);
    	return $db->loadResult();
    }
    
    /**
     * Get total paid
     *
     * @since 1.5.0.1
     * @param JeproshopCurrencyModelCurrency $currency currency used for the total paid of the current order
     * @return float amount in the $currency
     */
    public function getTotalPaid($currency = null)  {
    	if (!$currency){
    		$currency = new JeproshopCurrencyModelCurrency($this->currency_id);
    	}
    	
    	$total = 0;
    	// Retrieve all payments
    	$payments = $this->getOrderPaymentCollection();
    	foreach ($payments as $payment){
    		if ($payment->currency_id == $currency->currency_id){
    			$total += $payment->amount;
    		}else{
    			$amount = JeproshopTools::convertPrice($payment->amount, $payment->currency_id, false);
    			if ($currency->currency_id == JeproshopSettingModelSetting::getValue('default_currency', null, null, $this->shop_id)){
    				$total += $amount;
    			}else{
    				$total += JeproshopTools::convertPrice($amount, $currency->currency_id, true);
    			}
    		}
    	}    
    	return JeproshopTools::roundPrice($total, 2);
    }
    
    /**
     * This method return the ID of the previous order
     * @since 1.5.0.1
     * @return int
     */
    public function getPreviousOrderId(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT order_id FROM " . $db->quoteName('#__jeproshop_orders') . " WHERE order_id < "; 
    	$query .= (int)$this->order_id . " ORDER BY order_id DESC";
    	 
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     * This method return the ID of the next order
     * @since 1.5.0.1
     * @return int
     */
    public function getNextOrderId(){
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT order_id FROM " . $db->quoteName('#__jeproshop_orders') . " WHERE order_id > ";
    	$query .= (int)$this->order_id . " ORDER BY order_id ASC";
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }

    /**
     * This method allows to get all Order Payment for the current order
     * @since 1.5.0.1
     * @return Collection Collection of OrderPayment
     */
    public function getOrderPaymentCollection(){
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_payment') . " WHERE order_reference = ";
    	$query .= $db->quote($this->reference);
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    public function getProductsDetail(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_detail') . " AS order_detail LEFT JOIN ";
    	$query .= $db->quoteName('#__jeproshop_product') . " AS product ON (product.product_id = order_detail.";
    	$query .= "product_id) LEFT JOIN " . $db->quoteName('#__jeproshop_product_shop') . " AS product_shop ON (";
    	$query .= " product_shop.product_id = product.product_id AND product_shop.shop_id = order_detail.shop_id) ";
		$query .= "WHERE order_detail." . $db->quoteName('order_id') . " = " .(int)($this->order_id);
		
		$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     *
     * This method allow to add image information on a product detail
     * @param array &$product
     */
    protected function setProductImageInformations(&$product){
    	$db = JFactory::getDBO();
    	
    	if (isset($product->product_attribute_id) && $product->product_attribute_id){
    		$query = "SELECT image_shop.image_id FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " AS product_attribte_image";
    		$query .= JeproshopShopModelShop::addSqlAssociation('image', 'pai', true) . " WHERE product_attribute_id = " . (int)$product->product_attribute_id;
    		
    		$db->setQuery($query);
    		$image_id = $db->loadResult();
    	}
    	
    	if (!isset($image_id) || !$image_id){
    		$query = "SELECT image_shop.image_id FROM " . $db->quoteName('#__jeproshop_image') . " AS image";
    		$query .= JeproshopShopModelShop::addSqlAssociation('image', true, 'image_shop.cover=1') . " WHERE";
    		$query .= " product_id = " . (int)($product->product_id);
    		
    		$db->setQuery($query);
    		$image_id = $db->loadResult();
    	}
    	
    	$product->image = null;
    	$product->image_size = null;
    
    	if ($image_id){
    		$product->image = new JeproshopImageModelImage($image_id);
    	}
    }
    
    /**
     *
     * This method allow to add stock information on a product detail
     *
     * If advanced stock management is active, get physical stock of this product in the warehouse associated to the product for the current order
     * Else get the available quantity of the product in function of the shop associated to the order
     *
     * @param array &$product
     */
    protected function setProductCurrentStock(&$product){
    	if(JeproshopSettingModelSetting::getValue('advanced_stock_management') && (int)$product->advanced_stock_management == 1	&& (int)$product->warehouse_id > 0){
    		$product->current_stock = JeproshopStockManagerFactory::getManager()->getProductPhysicalQuantities($product->product_id, $product->product_attribute_id, (int)$product->warehouse_id, true);
    	}else{
    		$product->current_stock = JeproshopStockAvailableModelStockAvailable::getQuantityAvailableByProduct($product->product_id, $product->product_attribute_id, (int)$this->shop_id);
    	}
    }

    /**
     * Marked as deprecated but should not throw any "deprecated" message
     * This function is used in order to keep front office backward compatibility 14 -> 1.5
     * (Order History)
     *
     * @param $row
     */
    public function setProductPrices($row){
    	$tax_calculator = JeproshopOrderDetailModelOrderDetail::getStaticTaxCalculator((int)$row->order_detail_id);
    	$row->tax_calculator = $tax_calculator;
    	$row->tax_rate = $tax_calculator->getTotalRate();
    
    	$row->product_price = JeproshopTools::roundPrice($row->unit_price_tax_excl, 2);
    	$row->product_price_with_tax = JeproshopTools::roundPrice($row->unit_price_tax_incl, 2);
    
    	$group_reduction = 1;
    	if ($row->group_reduction > 0){
    		$group_reduction = 1 - $row->group_reduction / 100;
    	}
    	$row->product_price_with_tax_but_ecotax = $row->product_price_with_tax - $row->ecotax;
    
    	$row->total_with_tax = $row->total_price_tax_incl;
    	$row->total_price = $row->total_price_tax_excl;
    }
    
    protected function setProductCustomizedDatas(&$product, $customized_datas){
    	$product->customizedDatas = null;
    	if (isset($customized_datas[$product->product_id][$product->product_attribute_id])){
    		$product->customizedDatas = $customized_datas[$product->product_id][$product->product_attribute_id];
    	}else{
    		$product->customizationQuantityTotal = 0;
    	}
    }    

    public function getTaxCalculationMethod(){
    	return (int)($this->_taxCalculationMethod);
    }
    
    /**
     * Get the an order detail list of the current order
     * @return array
     */
    public function getOrderDetailList(){
    	return JeproshopOrderDetailModelOrderDetail::getOderDetails($this->order_id);
    }

    /**
     * Get customer orders
     *
     * @param integer $customer_id Customer id
     * @param boolean $showHiddenStatus Display or not hidden order statuses
     * @param JeproshopContext $context
     * @return array Customer orders
     */
    public static function getCustomerOrders($customer_id, $showHiddenStatus = false, JeproshopContext $context = null){
    	if (!$context){ $context = JeproshopContext::getContext(); }
    	$db = JFactory::getDBO();
    
    	$query = "SELECT ord.*, (SELECT SUM(order_detail." . $db->quoteName('product_quantity') . ") FROM " . $db->quoteName('#__jeproshop_order_detail');
    	$query .= " AS order_detail WHERE order_detail." . $db->quoteName('order_id') . " = ord." . $db->quoteName('order_id') . ") nb_products FROM ";
    	$query .= $db->quoteName('#__jeproshop_orders') . " AS ord WHERE ord." . $db->quoteName('customer_id') . " = " .(int)$customer_id . " GROUP BY ord.";
    	$query .= $db->quoteName('order_id') . " ORDER BY ord." . $db->quoteName('date_add') . " DESC";
    	
    	$db->setQuery($query);
    	$res = $db->loadObjectList();
    	if (!$res)
    		return array();
    
    	foreach($res as $key => $val){
    		$query = "SELECT order_status." . $db->quoteName('order_status_id') . ", order_status_lang." . $db->quoteName('name') . " AS order_status, order_status.";
    		$query .= $db->quoteName('invoice') . ", order_status." . $db->quoteName('color') . " as order_status_color FROM ";
    		$query .= $db->quoteName('#__jeproshop_order_history') . " AS order_history LEFT JOIN " . $db->quoteName('#__jeproshop_order_status') . " AS order_status ";
    		$query .= "ON (order_status." . $db->quoteName('order_status_id') . " = order_history." . $db->quoteName('order_status_id') . ") INNER JOIN ";
    		$query .= $db->quoteName('#__jeproshop_order_status_lang') . " AS order_status_lang ON (order_status." . $db->quoteName('order_status_id') . " = order_status_lang.";
    		$query .= $db->quoteName('order_status_id') . " AND order_status_lang." . $db->quoteName('lang_id') . " = " . (int)$context->language->lang_id . ") WHERE order_history.";
    		$query .= $db->quoteName('order_id') . " = " . (int)($val->order_id).(!$showHiddenStatus ? " AND order_status." . $db->quoteName('hidden') . " != 1" : "");
    		$query .= " ORDER BY order_history." . $db->quoteName('date_add') . " DESC, order_history." . $db->quoteName('order_history_id') . " DESC LIMIT 1";

    		$db->setQuery($query);
    		$res2 = $db->loadObjectList();
    
    		if ($res2){
    			$res[$key] = array_merge($res[$key], $res2[0]);
    		}
    	} 
    	return $res;
    }
    
    /**
     * Check if order contains (only) virtual products
     *
     * @param boolean $strict If false return true if there are at least one product virtual
     * @return boolean true if is a virtual order or false
     *
     */
    public function isVirtual($strict = true) {
    	$products = $this->getProducts();
    	if (count($products) < 1){	return false; }
    	$virtual = true;
    	foreach ($products as $product){
    		$pd = JeproshopProductDownloadModelProductDownload::getIdFromProductId((int)($product->product_id));
    		if ($pd && JeproshopTools::isUnsignedInt($pd) && $product->download_hash && $product->display_filename != ''){
    			if ($strict === false){ return true; }
    		}
    		else
    			$virtual &= false;
    	}
    	return $virtual;
    }
    
    /**
     * @since 1.5.0.4
     * @return JeproshopOrderStatusModelOrderStatus or null if Order haven't a state
     */
    public function getCurrentOrderStatus(){
    	if ($this->current_status){
    		return new JeproshopOrderStatusModelOrderStatus($this->current_status);
    	}
    	return null;
    }
    
    /**
     * Get warehouse associated to the order
     *
     * return array List of warehouse
     */
    public function getWarehouseList(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT warehouse_id FROM " . $db->quoteName('#__jeproshop_order_detail') . " WHERE " . $db->quoteName('order_id');
    	$query .= " = " . (int)$this->order_id . " GROUP BY warehouse_id ";
    	
    	$db->setQuery($query);
    	$results = $db->loadObjectList();
    	if (!$results){ return array(); }
    
    	$warehouse_list = array();
    	foreach ($results as $row){
    		$warehouse_list[] = $row->warehouse_id;
    	}
    	return $warehouse_list;
    }
    
    public function hasBeenPaid(){
    	return count($this->getHistory((int)($this->lang_id), false, false, JeproshopOrderStatusModelOrderStatus::FLAG_PAID));
    }
    
    public function hasBeenShipped() {
    	return count($this->getHistory((int)($this->lang_id), false, false, JeproshopOrderStatusModelOrderStatus::FLAG_SHIPPED));
    }
    
    public function hasBeenDelivered(){
    	return count($this->getHistory((int)($this->lang_id), false, false, JeproshopOrderStatusModelOrderStatus::FLAG_DELIVERY));
    }
    
    /**
     * Has products returned by the merchant or by the customer?
     */
    public function hasProductReturned(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT IFNULL(SUM(order_return_detail.product_quantity), SUM(product_quantity_return)) FROM ";
    	$query .= $db->quoteName('#__jeproshop_orders') . " AS ord INNER JOIN " . $db->quoteName('#__jeproshop_order_detail');
    	$query .= " AS order_detail ON order_detail.order_id = ord.order_id LEFT JOIN " . $db->quoteName('#__jeproshop_order_return_detail');
    	$query .= " AS order_return_detail ON order_return_detail.order_detail_id = order_detail.order_detail_id WHERE ord.order_id = ";
    	$query .= (int)$this->order_id;
    	
    	$db->setQuery($query);
    	return $db->loadResult();
    }
    
    /**
     * Get a collection of order payments
     *
     * @since 1.5.0.13
     */
    public function getOrderPayments() {
    	return JeproshopOrderPaymentModelOrderPayment::getByOrderReference($this->reference);
    }
    
    public function getOrderList(JeproshopContext $context = NULL){
    	jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		if(!$context){ $context = JeproshopContext::getContext(); }
    	
    	$select = " ord.currency_id, ord.order_id AS pdf_id, CONCAT(LEFT(customer." . $db->quoteName('firstname') . ", 1), '. ',";
    	$select .= " customer." . $db->quoteName('lastname') . ") AS " . $db->quoteName('customer_name') . ", order_status_lang.";
    	$select .= $db->quoteName('name') . " AS " . $db->quoteName('order_status_name') . ", order_status." . $db->quoteName('color');
    	$select .= ", IF((SELECT COUNT(orders.order_id) FROM " . $db->quoteName('#__jeproshop_orders') . " AS orders WHERE orders.customer_id ";
    	$select.= "= ord.customer_id) > 1, 0, 1) AS new, country_lang.name as country_name, IF(ord.valid, 1, 0) badge_success ";
    	
    	$join = " LEFT JOIN " . $db->quoteName('#__jeproshop_customer') . " AS customer ON (customer." . $db->quoteName('customer_id');
    	$join .= " = ord." .  $db->quoteName('customer_id') . ") INNER JOIN " . $db->quoteName('#__jeproshop_address') . " AS address ";
    	$join .= " ON address.address_id = ord.address_delivery_id INNER JOIN " . $db->quoteName('#__jeproshop_country') . " AS country ";
    	$join .= " ON address.country_id = country.country_id INNER JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang";
    	$join .= " ON (country." . $db->quoteName('country_id'). " = country_lang." . $db->quoteName('country_id') . " AND country_lang.";
    	$join .= $db->quoteName('lang_id') . " = " .(int)$context->language->lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_order_status');
    	$join .= " AS order_status ON (order_status." . $db->quoteName('order_status_id') . " = ord." . $db->quoteName('current_status') . ") LEFT JOIN ";
    	$join .= $db->quoteName('#__jeproshop_order_status_lang') . " order_status_lang ON (order_status." . $db->quoteName('order_status_id') . " = ";
    	$join .= " order_status_lang." . $db->quoteName('order_status_id') . " AND order_status_lang." . $db->quoteName('lang_id') . " = ";
    	$join .= (int)$context->language->lang_id. ") ";    	
    	
    	
    	$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
    	$limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
    	$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'order_id', 'string');
    	$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'DESC', 'string');
    	$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
    	$deleted = false;
    	/* Manage default params values */

    	$use_limit = true;
    	if ($limit === false)
    		$use_limit = false;
    	
    	$select_shop = ", shop.shop_name AS shop_name ";
    	$join_shop = " LEFT JOIN " . $db->quoteName('#__jeproshop_shop') . " AS shop ON ord.shop_id = shop.shop_id ";
    	$where_shop = JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_ORDER, 'ord', 'shop');

        if(JeproshopCountryModelCountry::isCurrentlyUsed()){
            $query = "SELECT DISTINCT country.country_id, country_lang." . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord ";
            $query .= " INNER JOIN " . $db->quoteName('#__jeproshop_address') . " AS address ON address.address_id = ord.address_delivery_id INNER JOIN " . $db->quoteName('#__jeproshop_country');
            $query .= " AS country ON address.country_id = country.country_id INNER JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id');
            $query.= " = country_lang." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") ORDER BY country_lang.name ASC";

            $db->setQuery($query);
            $result = $db->loadObjectList();

            $shopLinkType = 'shop';
        }
    	
    	$where = "";
    	if($this->multishop_context && JeproshopShopModelShop::isTableAssociated('order')){
    		if(JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_ALL || !$context->employee->isSuperAdmin()){
    			if(JeproshopShopModelShop::isFeaturePublished() && $test_join && JeproshopShopModelShop::isTableAssociated('order')){
    				$where .= " AND ord.order_id IN (SELECT order_shop.shop_id FROM " . $db->quoteName('#__jeproshop_order_shop') . " AS order_shop WHERE order_shop.shop_id IN (" . implode(', ', JeproshopShopModelShop::getContextListShopIds()) . "))";
    			}
    		}
    	}
    	
    	$lang_join = "";
    	/*if($lang_id){
    		$lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_order');
    	} */   	    	
    	
    	do{
    		$query = "SELECT SQL_CALC_FOUND_ROWS ord." . $db->quoteName('order_id') . " AS pdf_id, ord." .  $db->quoteName('reference'). ", ord." . $db->quoteName('customer_id');
    		if(JeproshopSettingModelSetting::getValue('enable_b2b_mode')){ $query .= ", customer." .  $db->quoteName('company'); }
    		$query .= ", " . $db->quoteName('total_paid_tax_incl') . ", " .  $db->quoteName('payment') . ", ord." . $db->quoteName('date_add') . " AS date_add, ord.";
    		$query .= $db->quoteName('currency_id') . ", order_status." . $db->quoteName('order_status_id') . ", " . $select . $select_shop;
    		$query .= " FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord ". $lang_join . $join . $join_shop . " WHERE 1 " . $where ;
    		$query .= ($deleted ? " AND ord." . $db->quoteName('deleted') . " = 0 " : "") . (isset($filter) ? $filter : "") . $where_shop;
    		$query .= (isset($group) ? $group : "") . " ORDER BY " . ((str_replace('`', '', $order_by) == 'order_id') ? "ord." : "");
    		$query .= $order_by . " " . $order_way ; 
    		
    		$db->setQuery($query);
    		$total = count($db->loadObjectList());
    			
    		$query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
    		
    		$db->setQuery($query);
    		$orders = $db->loadObjectList();
    			
    		if($use_limit == true){
    			$limit_start = (int)$limit_start -(int)$limit;
    			if($limit_start < 0){ break; }
    		}else{ break; }
    	}while(empty($orders));

        $this->pagination = new JPagination($total, $limit_start, $limit);
    	return  $orders;
    }

    public function getPagination(){ return $this->pagination; }
    
    /**
     * Returns the correct product taxes breakdown.
     *
     * Get all documents linked to the current order
     *
     * @since 1.5.0.1
     * @return array
     */
    public function getDocuments(){
    	$invoices = $this->getInvoicesCollection();
    	foreach($invoices as $key => $invoice){
    		if (!$invoice->number){ unset($invoices[$key]); }
    	}
    	$delivery_slips = $this->getDeliverySlipsCollection();
    	// @TODO review
    	foreach ($delivery_slips as $key => $delivery){
    		$delivery->is_delivery = true;
    		$delivery->date_add = $delivery->delivery_date;
    		if (!$delivery->delivery_number){
    			unset($delivery_slips[$key]);
    		}
    	}
    	$order_slips = $this->getOrderSlipsCollection();
    
    	$documents = array_merge($invoices, $order_slips, $delivery_slips);
    	usort($documents, array('JeproshopOrderModelOrder', 'sortDocuments'));
    
    	return $documents;
    }

    /**
     * Get an order by its cart id
     *
     * @param integer $cart_id JeproshopCartModelCart id
     * @return array Order details
     */
    public static function getOrderIdByCartId($cart_id){
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('order_id') . " FROM " . $db->quoteName('#__jeproshop_orders') . " WHERE " . $db->quoteName('cart_id') . " = ";
        $query .= (int)($cart_id) . JeproshopShopModelShop::addSqlRestriction();

        $db->setQuery($query);
        $result = $db->loadOject();

        return isset($result['id_order']) ? $result['id_order'] : false;
    }
    
    /**
     * @return array return all shipping method for the current order
     * state_name sql var is now deprecated - use order_status_name for the state name and carrier_name for the carrier_name
     */
    public function getShipping() {
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT DISTINCT order_carrier." . $db->quoteName('order_invoice_id') . ", order_carrier." . $db->quoteName('weight');
    	$query .= ", order_carrier." . $db->quoteName('shipping_cost_tax_excl') . ", order_carrier." . $db->quoteName('shipping_cost_tax_incl');
    	$query .= ", carrier." . $db->quoteName('url') . ", order_carrier." . $db->quoteName('carrier_id') . ", carrier." . $db->quoteName('name');
    	$query .= " AS carrier_name, order_carrier." . $db->quoteName('date_add') . ", \"Delivery\" AS " . $db->quoteName('type') . ", \"true\" AS";
    	$query .= " can_edit, order_carrier." . $db->quoteName('tracking_number') . ", order_carrier." . $db->quoteName('order_carrier_id');
    	$query .= ", order_status_lang." . $db->quoteName('name') . " AS order_status_name, carrier." . $db->quoteName('name') . " AS state_name ";
    	$query .= " FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord LEFT JOIN " . $db->quoteName('#__jeproshop_order_history');
    	$query .= " AS order_history ON (ord." . $db->quoteName('order_id') . " = order_history." . $db->quoteName('order_id') . ") LEFT JOIN ";
    	$query .= $db->quoteName('#__jeproshop_order_carrier') . " AS order_carrier ON (ord." . $db->quoteName('order_id') . " = order_carrier.";
    	$query .= $db->quoteName('order_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_carrier') . " AS carrier ON (order_carrier.";
    	$query .= $db->quoteName('carrier_id') . " = carrier." . $db->quoteName('carrier_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_order_status_lang');
    	$query .= " AS order_status_lang ON (order_history." . $db->quoteName('order_status_id') . " = order_status_lang." . $db->quoteName('order_status_id');
    	$query .= " AND order_status_lang." . $db->quoteName('lang_id') . " = " . (int)JeproshopContext::getContext()->language->lang_id . ") WHERE ord.";
    	$query .= $db->quoteName('order_id') . " = " .(int)$this->order_id . " GROUP BY carrier.carrier_id ";
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    public static function sortDocuments($a, $b){
    	if ($a->date_add == $b->date_add){	return 0; }
    	return ($a->date_add < $b->date_add) ? -1 : 1;
    }
    
    public function getReturn(){
    	return JeproshopOrderReturnModelOrderReturn::getOrdersReturn($this->customer_id, $this->order_id);
    }
    
    
    /**
     *
     * Get all order_slips for the current order
     * @since 1.5.0.2
     * @return Array Collection of OrderSlip
     */
    public function getOrderSlipsCollection(){
    	$db = JFactory::getDBO();
    	
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_slip') . " WHERE order_id = " . (int)$this->order_id;
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     *
     * Get all invoices for the current order
     * @since 1.5.0.1
     * @return Array Collection of OrderInvoice
     */
    public function getInvoicesCollection(){
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_invoice') . " WHERE order_id = " . (int)$this->order_id;
    	 
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     *
     * Get all delivery slips for the current order
     * @since 1.5.0.2
     * @return Array Collection of OrderInvoice
     */
    public function getDeliverySlipsCollection(){
    	$db = JFactory::getDBO();
    	 
    	$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_invoice') . " WHERE order_id = " . (int)$this->order_id;
    	$query .= " AND delivery_number != 0"; 
    	
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
    
    /**
     * Get all not paid invoices for the current order
     * @since 1.5.0.2
     * @return Array Collection of Order invoice not paid
     */
    public function getNotPaidInvoicesCollection(){
    	$invoices = $this->getInvoicesCollection();
    	foreach ($invoices as $key => $invoice)
    		if ($invoice->isPaid())
    			unset($invoices[$key]);
    		return $invoices;
    }

    public function add($autodate = true, $null_values = true)
    {
        if (parent::add($autodate, $null_values))
            return SpecificPrice::deleteByIdCart($this->id_cart);
        return false;
    }


    /* Does NOT delete a product but "cancel" it (which means return/refund/delete it depending of the case) */
    public function deleteProduct($order, $orderDetail, $quantity){
        if (!(int)($this->getCurrentState()) || !validate::isLoadedObject($orderDetail))
            return false;

        if ($this->hasBeenDelivered())
        {
            if (!Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop))
                throw new PrestaShopException('PS_ORDER_RETURN is not defined in table configuration');
            $orderDetail->product_quantity_return += (int)($quantity);
            return $orderDetail->update();
        }elseif ($this->hasBeenPaid()){
            $orderDetail->product_quantity_refunded += (int)($quantity);
            return $orderDetail->update();
        }
        return $this->_deleteProduct($orderDetail, (int)$quantity);
    }

    /**
     * This function return products of the orders
     * It's similar to Order::getProducts but witrh similar outputs of Cart::getProducts
     *
     * @return array
     */
    public function getCartProducts()
    {
        $product_id_list = array();
        $products = $this->getProducts();
        foreach ($products as &$product)
        {
            $product['id_product_attribute'] = $product['product_attribute_id'];
            $product['cart_quantity'] = $product['product_quantity'];
            $product_id_list[] = $this->id_address_delivery.'_'
                .$product['product_id'].'_'
                .$product['product_attribute_id'].'_'
                .(isset($product['id_customization']) ? $product['id_customization'] : '0');
        }
        unset($product);

        $product_list = array();
        foreach ($products as $product)
        {
            $key = $this->id_address_delivery.'_'
                .$product['id_product'].'_'
                .(isset($product['id_product_attribute']) ? $product['id_product_attribute'] : '0').'_'
                .(isset($product['id_customization']) ? $product['id_customization'] : '0');

            if (in_array($key, $product_id_list))
                $product_list[] = $product;
        }
        return $product_list;
    }

    /* DOES delete the product */
    protected function _deleteProduct($orderDetail, $quantity)
    {
        $product_price_tax_excl = $orderDetail->unit_price_tax_excl * $quantity;
        $product_price_tax_incl = $orderDetail->unit_price_tax_incl * $quantity;

        /* Update cart */
        $cart = new Cart($this->id_cart);
        $cart->updateQty($quantity, $orderDetail->product_id, $orderDetail->product_attribute_id, false, 'down'); // customization are deleted in deleteCustomization
        $cart->update();

        /* Update order */
        $shipping_diff_tax_incl = $this->total_shipping_tax_incl - $cart->getPackageShippingCost($this->id_carrier, true, null, $this->getCartProducts());
        $shipping_diff_tax_excl = $this->total_shipping_tax_excl - $cart->getPackageShippingCost($this->id_carrier, false, null, $this->getCartProducts());
        $this->total_shipping -= $shipping_diff_tax_incl;
        $this->total_shipping_tax_excl -= $shipping_diff_tax_excl;
        $this->total_shipping_tax_incl -= $shipping_diff_tax_incl;
        $this->total_products -= $product_price_tax_excl;
        $this->total_products_wt -= $product_price_tax_incl;
        $this->total_paid -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_incl -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_excl -= $product_price_tax_excl + $shipping_diff_tax_excl;
        $this->total_paid_real -= $product_price_tax_incl + $shipping_diff_tax_incl;

        $fields = array(
            'total_shipping',
            'total_shipping_tax_excl',
            'total_shipping_tax_incl',
            'total_products',
            'total_products_wt',
            'total_paid',
            'total_paid_tax_incl',
            'total_paid_tax_excl',
            'total_paid_real'
        );

        /* Prevent from floating precision issues (total_products has only 2 decimals) */
        foreach ($fields as $field)
            if ($this->{$field} < 0)
                $this->{$field} = 0;

        /* Prevent from floating precision issues */
        foreach ($fields as $field)
            $this->{$field} = number_format($this->{$field}, 2, '.', '');

        /* Update order detail */
        $orderDetail->product_quantity -= (int)$quantity;
        if ($orderDetail->product_quantity == 0)
        {
            if (!$orderDetail->delete())
                return false;
            if (count($this->getProductsDetail()) == 0)
            {
                $history = new OrderHistory();
                $history->id_order = (int)($this->id);
                $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $this);
                if (!$history->addWithemail())
                    return false;
            }
            return $this->update();
        }
        else
        {
            $orderDetail->total_price_tax_incl -= $product_price_tax_incl;
            $orderDetail->total_price_tax_excl -= $product_price_tax_excl;
            $orderDetail->total_shipping_price_tax_incl -= $shipping_diff_tax_incl;
            $orderDetail->total_shipping_price_tax_excl -= $shipping_diff_tax_excl;
        }
        return $orderDetail->update() && $this->update();
    }

    public function deleteCustomization($id_customization, $quantity, $orderDetail)
    {
        if (!(int)($this->getCurrentState()))
            return false;

        if ($this->hasBeenDelivered())
            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_returned` = `quantity_returned` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
        elseif ($this->hasBeenPaid())
            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_refunded` = `quantity_refunded` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
        if (!Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity` = `quantity` - '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id)))
            return false;
        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `quantity` = 0'))
            return false;
        return $this->_deleteProduct($orderDetail, (int)$quantity);
    }

    public function getFirstMessage(){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `message`
			FROM `'._DB_PREFIX_.'message`
			WHERE `id_order` = '.(int)$this->id.'
			ORDER BY `id_message`
		');
    }

    public static function getIdOrderProduct($id_customer, $id_product)
    {
        return (int)Db::getInstance()->getValue('
			SELECT o.id_order
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_detail od
				ON o.id_order = od.id_order
			WHERE o.id_customer = '.(int)$id_customer.'
				AND od.product_id = '.(int)$id_product.'
			ORDER BY o.date_add DESC
		');
    }

    public function getTaxesAverageUsed()
    {
        return Cart::getTaxesAverageUsed((int)($this->id_cart));
    }

    /**
     * Count virtual products in order
     *
     * @return int number of virtual products
     */
    public function getVirtualProducts()
    {
        $sql = '
			SELECT `product_id`, `product_attribute_id`, `download_hash`, `download_deadline`
			FROM `'._DB_PREFIX_.'order_detail` od
			WHERE od.`id_order` = '.(int)($this->id).'
				AND `download_hash` <> \'\'';
        return Db::getInstance()->executeS($sql);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public function getDiscounts($details = false)
    {
        Tools::displayAsDeprecated();
        return Order::getCartRules();
    }


    public static function getDiscountsCustomer($id_customer, $id_cart_rule)
    {
        $cache_id = 'Order::getDiscountsCustomer_'.(int)$id_customer.'-'.(int)$id_cart_rule;
        if (!Cache::isStored($cache_id))
        {
            $result = (int)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule ocr ON (ocr.id_order = o.id_order)
			WHERE o.id_customer = '.(int)$id_customer.'
			AND ocr.id_cart_rule = '.(int)$id_cart_rule);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get current order status (eg. Awaiting payment, Delivered...)
     *
     * @return int Order status id
     */
    public function getCurrentState()
    {
        return $this->current_state;
    }

    /**
     * Get current order status name (eg. Awaiting payment, Delivered...)
     *
     * @return array Order status details
     */
    public function getCurrentStateFull($id_lang)
    {
        return Db::getInstance()->getRow('
			SELECT os.`id_order_state`, osl.`name`, os.`logable`, os.`shipped`
			FROM `'._DB_PREFIX_.'order_state` os
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = os.`id_order_state`)
			WHERE osl.`id_lang` = '.(int)$id_lang.' AND os.`id_order_state` = '.(int)$this->current_state);
    }

    public function isInPreparation()
    {
        return count($this->getHistory((int)($this->id_lang), Configuration::get('PS_OS_PREPARATION')));
    }

    /**
     * Checks if the current order status is paid and shipped
     *
     * @return bool
     */
    public function isPaidAndShipped()
    {
        $order_state = $this->getCurrentOrderState();
        if ($order_state && $order_state->paid && $order_state->shipped)
            return true;
        return false;
    }

    public static function getOrdersIdByDate($date_from, $date_to, $id_customer = null, $type = null)
    {
        $sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE DATE_ADD(date_upd, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND date_upd >= \''.pSQL($date_from).'\'
					'.Shop::addSqlRestriction()
            .($type ? ' AND `'.bqSQL($type).'_number` != 0' : '')
            .($id_customer ? ' AND id_customer = '.(int)$id_customer : '');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = array();
        foreach ($result as $order)
            $orders[] = (int)($order['id_order']);
        return $orders;
    }

    public static function getOrdersWithInformations($limit = null, Context $context = null)
    {
        if (!$context)
            $context = Context::getContext();

        $sql = 'SELECT *, (
					SELECT osl.`name`
					FROM `'._DB_PREFIX_.'order_state_lang` osl
					WHERE osl.`id_order_state` = o.`current_state`
					AND osl.`id_lang` = '.(int)$context->language->id.'
					LIMIT 1
				) AS `state_name`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
				WHERE 1
					'.Shop::addSqlRestriction(false, 'o').'
				ORDER BY o.`date_add` DESC
				'.((int)$limit ? 'LIMIT 0, '.(int)$limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * @deprecated since 1.5.0.2
     *
     * @static
     * @param $date_from
     * @param $date_to
     * @param $id_customer
     * @param $type
     *
     * @return array
     */
    public static function getOrdersIdInvoiceByDate($date_from, $date_to, $id_customer = null, $type = null)
    {
        Tools::displayAsDeprecated();
        $sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND invoice_date >= \''.pSQL($date_from).'\'
					'.Shop::addSqlRestriction()
            .($type ? ' AND `'.bqSQL($type).'_number` != 0' : '')
            .($id_customer ? ' AND id_customer = '.(int)($id_customer) : '').
            ' ORDER BY invoice_date ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = array();
        foreach ($result as $order)
            $orders[] = (int)$order['id_order'];
        return $orders;
    }

    /**
     * @deprecated 1.5.0.3
     *
     * @static
     * @param $id_order_state
     * @return array
     */
    public static function getOrderIdsByStatus($id_order_state)
    {
        Tools::displayAsDeprecated();
        $sql = 'SELECT id_order
				FROM '._DB_PREFIX_.'orders o
				WHERE o.`current_state` = '.(int)$id_order_state.'
				'.Shop::addSqlRestriction(false, 'o').'
				ORDER BY invoice_date ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = array();
        foreach ($result as $order)
            $orders[] = (int)($order['id_order']);
        return $orders;
    }

    /**
     * Get order customer
     *
     * @return Customer $customer
     */
    public function getCustomer()
    {
        static $customer = null;
        if (is_null($customer))
            $customer = new Customer((int)$this->id_customer);

        return $customer;
    }

    /**
     * Get customer orders number
     *
     * @param integer $id_customer Customer id
     * @return array Customer orders number
     */
    public static function getCustomerNbOrders($id_customer)
    {
        $sql = 'SELECT COUNT(`id_order`) AS nb
				FROM `'._DB_PREFIX_.'orders`
				WHERE `id_customer` = '.(int)$id_customer
            .Shop::addSqlRestriction();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return isset($result['nb']) ? $result['nb'] : 0;
    }



    /**
     * @deprecated 1.5.0.1
     * @see Order::addCartRule()
     * @param int $id_cart_rule
     * @param string $name
     * @param float $value
     * @return bool
     */
    public function addDiscount($id_cart_rule, $name, $value)
    {
        Tools::displayAsDeprecated();
        return Order::addCartRule($id_cart_rule, $name, array('tax_incl' => $value, 'tax_excl' => '0.00'));
    }

    /**
     * @since 1.5.0.1
     * @param int $id_cart_rule
     * @param string $name
     * @param array $values
     * @param int $id_order_invoice
     * @return bool
     */
    public function addCartRule($id_cart_rule, $name, $values, $id_order_invoice = 0, $free_shipping = null)
    {
        $order_cart_rule = new OrderCartRule();
        $order_cart_rule->id_order = $this->id;
        $order_cart_rule->id_cart_rule = $id_cart_rule;
        $order_cart_rule->id_order_invoice = $id_order_invoice;
        $order_cart_rule->name = $name;
        $order_cart_rule->value = $values['tax_incl'];
        $order_cart_rule->value_tax_excl = $values['tax_excl'];
        if ($free_shipping === null)
        {
            $cart_rule = new CartRule($id_cart_rule);
            $free_shipping = $cart_rule->free_shipping;
        }
        $order_cart_rule->free_shipping = (int)$free_shipping;
        $order_cart_rule->add();
    }

    public function getNumberOfDays()
    {
        $nbReturnDays = (int)(Configuration::get('PS_ORDER_RETURN_NB_DAYS', null, null, $this->id_shop));
        if (!$nbReturnDays)
            return true;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT TO_DAYS(NOW()) - TO_DAYS(`delivery_date`)  AS days FROM `'._DB_PREFIX_.'orders`
		WHERE `id_order` = '.(int)($this->id));
        if ($result['days'] <= $nbReturnDays)
            return true;
        return false;
    }

    /**
     * Can this order be returned by the client?
     *
     * @return bool
     */
    public function isReturnable()
    {
        if (Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop) && $this->isPaidAndShipped())
            return $this->getNumberOfDays();

        return false;
    }

    public static function getLastInvoiceNumber()
    {
        return Db::getInstance()->getValue('
			SELECT MAX(`number`)
			FROM `'._DB_PREFIX_.'order_invoice`
		');
    }

    public static function setLastInvoiceNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id)
            return false;

        $number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $id_shop);
        // If invoice start number has been set, you clean the value of this configuration
        if ($number)
            Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $id_shop);

        $sql = 'UPDATE `'._DB_PREFIX_.'order_invoice` SET number =';

        if ($number)
            $sql .= (int)$number;
        else
            $sql .= '(SELECT new_number FROM (SELECT (MAX(`number`) + 1) AS new_number
			FROM `'._DB_PREFIX_.'order_invoice`) AS result)';

        $sql .=' WHERE `id_order_invoice` = '.(int)$order_invoice_id;

        return Db::getInstance()->execute($sql);
    }

    public function getInvoiceNumber($order_invoice_id)
    {
        if (!$order_invoice_id)
            return false;

        return Db::getInstance()->getValue('
			SELECT `number`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE `id_order_invoice` = '.(int)$order_invoice_id
        );
    }

    /**
     * This method allows to generate first invoice of the current order
     */
    public function setInvoice($use_existing_payment = false)
    {
        if (Configuration::get('PS_INVOICE') && !$this->hasInvoice())
        {
            if ($id = (int)$this->hasDelivery())
                $order_invoice = new OrderInvoice($id);
            else
                $order_invoice = new OrderInvoice();
            $order_invoice->id_order = $this->id;
            if (!$id)
                $order_invoice->number = 0;
            $address = new Address((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $carrier = new Carrier((int)$this->id_carrier);
            $tax_calculator = $carrier->getTaxCalculator($address);
            $order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
            $order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
            $order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
            $order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
            $order_invoice->total_products = $this->total_products;
            $order_invoice->total_products_wt = $this->total_products_wt;
            $order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
            $order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
            $order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
            $order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
            $order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;

            // Save Order invoice

            $order_invoice->save();
            $this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);

            $order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

            // Update order_carrier
            $id_order_carrier = Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$order_invoice->id_order.'
				AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');

            if ($id_order_carrier)
            {
                $order_carrier = new OrderCarrier($id_order_carrier);
                $order_carrier->id_order_invoice = (int)$order_invoice->id;
                $order_carrier->update();
            }

            // Update order detail
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_detail`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

            // Update order payment
            if ($use_existing_payment)
            {
                $id_order_payments = Db::getInstance()->executeS('
					SELECT DISTINCT op.id_order_payment
					FROM `'._DB_PREFIX_.'order_payment` op
					INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
					LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)
					WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

                if (count($id_order_payments))
                {
                    foreach ($id_order_payments as $order_payment)
                        Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
							SET
								`id_order_invoice` = '.(int)$order_invoice->id.',
								`id_order_payment` = '.(int)$order_payment['id_order_payment'].',
								`id_order` = '.(int)$order_invoice->id_order);
                    // Clear cache
                    Cache::clean('order_invoice_paid_*');
                }
            }

            // Update order cart rule
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_cart_rule`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

            // Keep it for backward compatibility, to remove on 1.6 version
            $this->invoice_date = $order_invoice->date_add;
            $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
            $this->update();
        }
    }

    /**
     * This method allows to generate first delivery slip of the current order
     */
    public function setDeliverySlip()
    {
        if (!$this->hasInvoice())
        {
            $order_invoice = new OrderInvoice();
            $order_invoice->id_order = $this->id;
            $order_invoice->number = 0;
            $order_invoice->add();
            $this->delivery_date = $order_invoice->date_add;
            $this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
            $this->update();
        }
    }

    public function setDeliveryNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id)
            return false;

        $id_shop = shop::getTotalShops() > 1 ? $id_shop : null;

        $number = Configuration::get('PS_DELIVERY_NUMBER', null, null, $id_shop);
        // If delivery slip start number has been set, you clean the value of this configuration
        if ($number)
            Configuration::updateValue('PS_DELIVERY_NUMBER', false, false, null, $id_shop);

        $sql = 'UPDATE `'._DB_PREFIX_.'order_invoice` SET delivery_number =';

        if ($number)
            $sql .= (int)$number;
        else
            $sql .= '(SELECT new_number FROM (SELECT (MAX(`delivery_number`) + 1) AS new_number
			FROM `'._DB_PREFIX_.'order_invoice`) AS result)';

        $sql .=' WHERE `id_order_invoice` = '.(int)$order_invoice_id;

        return Db::getInstance()->execute($sql);
    }

    public function getDeliveryNumber($order_invoice_id)
    {
        if (!$order_invoice_id)
            return false;

        return Db::getInstance()->getValue('
			SELECT `delivery_number`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE `id_order_invoice` = '.(int)$order_invoice_id
        );
    }

    public function setDelivery()
    {
        // Get all invoice
        $order_invoice_collection = $this->getInvoicesCollection();
        foreach ($order_invoice_collection as $order_invoice)
        {
            if ($order_invoice->delivery_number)
                continue;

            // Set delivery number on invoice
            $order_invoice->delivery_number = 0;
            $order_invoice->delivery_date = date('Y-m-d H:i:s');
            // Update Order Invoice
            $order_invoice->update();
            $this->setDeliveryNumber($order_invoice->id, $this->id_shop);
            $this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
        }

        // Keep it for backward compatibility, to remove on 1.6 version
        // Set delivery date
        $this->delivery_date = date('Y-m-d H:i:s');
        // Update object
        $this->update();
    }

    public static function getByDelivery($id_delivery)
    {
        $sql = 'SELECT id_order
				FROM `'._DB_PREFIX_.'orders`
				WHERE `delivery_number` = '.(int)($id_delivery).'
				'.Shop::addSqlRestriction();
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return new Order((int)($res['id_order']));
    }

    /**
     * Get a collection of orders using reference
     *
     * @since 1.5.0.14
     *
     * @param string $reference
     * @return PrestaShopCollection Collection of Order
     */
    public static function getByReference($reference)
    {
        $orders = new PrestaShopCollection('Order');
        $orders->where('reference', '=', $reference);
        return $orders;
    }

    public function getTotalWeight()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(product_weight * product_quantity)
		FROM '._DB_PREFIX_.'order_detail
		WHERE id_order = '.(int)($this->id));
        return (float)($result);
    }

    /**
     *
     * @param int $id_invoice
     * @deprecated 1.5.0.1
     */
    public static function getInvoice($id_invoice)
    {
        Tools::displayAsDeprecated();
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `invoice_number`, `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE invoice_number = '.(int)($id_invoice));
    }

    public function isAssociatedAtGuest($email)
    {
        if (!$email)
            return false;
        $sql = 'SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
				WHERE o.`id_order` = '.(int)$this->id.'
					AND c.`email` = \''.pSQL($email).'\'
					AND c.`is_guest` = 1
					'.Shop::addSqlRestriction(false, 'c');
        return (bool)Db::getInstance()->getValue($sql);
    }

    /**
     * @param int $id_order
     * @param int $id_customer optionnal
     * @return int id_cart
     */
    public static function getCartIdStatic($id_order, $id_customer = 0)
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_cart`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$id_order.'
			'.($id_customer ? 'AND `id_customer` = '.(int)$id_customer : ''));
    }

    public function getWsOrderRows()
    {
        $query = '
			SELECT
			`id_order_detail` as `id`,
			`product_id`,
			`product_price`,
			`id_order`,
			`product_attribute_id`,
			`product_quantity`,
			`product_name`,
			`product_reference`,
			`product_ean13`,
			`product_upc`,
			`unit_price_tax_incl`,
			`unit_price_tax_excl`
			FROM `'._DB_PREFIX_.'order_detail`
			WHERE id_order = '.(int)$this->id;
        $result = Db::getInstance()->executeS($query);
        return $result;
    }

    /** Set current order status
     * @param int $id_order_state
     * @param int $id_employee (/!\ not optional except for Webservice.
     */
    public function setCurrentStatus($id_order_state, $id_employee = 0)
    {
        if (empty($id_order_state))
            return false;
        $history = new OrderHistory();
        $history->id_order = (int)$this->id;
        $history->id_employee = (int)$id_employee;
        $history->changeIdOrderState((int)$id_order_state, $this);
        $res = Db::getInstance()->getRow('
			SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$this->id);
        $this->invoice_date = $res['invoice_date'];
        $this->invoice_number = $res['invoice_number'];
        $this->delivery_date = $res['delivery_date'];
        $this->delivery_number = $res['delivery_number'];
        $this->update();

        $history->addWithemail();
    }

    public function addWs($autodate = true, $nullValues = false)
    {
        $paymentModule = Module::getInstanceByName($this->module);
        $customer = new Customer($this->id_customer);
        $paymentModule->validateOrder($this->id_cart, Configuration::get('PS_OS_WS_PAYMENT'), $this->total_paid, $this->payment, null, array(), null, false, $customer->secure_key);
        $this->id = $paymentModule->currentOrder;
        return true;
    }

    public function deleteAssociations()
    {
        return (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'order_detail`
				WHERE `id_order` = '.(int)($this->id)) !== false);
    }
    /**
     * Generate a unique reference for orders generated with the same cart id
     * This references, is useful for check payment
     *
     * @return String
     */
    public static function generateReference()
    {
        return strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
    }

    public function orderContainProduct($id_product)
    {
        $product_list = $this->getOrderDetailList();
        foreach ($product_list as $product)
            if ($product['product_id'] == (int)$id_product)
                return true;
        return false;
    }
    /**
     * This method returns true if at least one order details uses the
     * One After Another tax computation method.
     *
     * @since 1.5.0.1
     * @return boolean
     */
    public function useOneAfterAnotherTaxComputationMethod()
    {
        // if one of the order details use the tax computation method the display will be different
        return Db::getInstance()->getValue('
		SELECT od.`tax_computation_method`
		FROM `'._DB_PREFIX_.'order_detail_tax` odt
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
		WHERE od.`id_order` = '.(int)$this->id.'
		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
        );
    }

    /**
     *
     * This method allows to add a payment to the current order
     * @since 1.5.0.1
     * @param float $amount_paid
     * @param string $payment_method
     * @param string $payment_transaction_id
     * @param Currency $currency
     * @param string $date
     * @param OrderInvoice $order_invoice
     * @return bool
     */
    public function addOrderPayment($amount_paid, $payment_method = null, $payment_transaction_id = null, $currency = null, $date = null, $order_invoice = null)
    {
        $order_payment = new OrderPayment();
        $order_payment->order_reference = $this->reference;
        $order_payment->id_currency = ($currency ? $currency->id : $this->id_currency);
        // we kept the currency rate for historization reasons
        $order_payment->conversion_rate = ($currency ? $currency->conversion_rate : 1);
        // if payment_method is define, we used this
        $order_payment->payment_method = ($payment_method ? $payment_method : $this->payment);
        $order_payment->transaction_id = $payment_transaction_id;
        $order_payment->amount = $amount_paid;
        $order_payment->date_add = ($date ? $date : null);

        // Update total_paid_real value for backward compatibility reasons
        if ($order_payment->id_currency == $this->id_currency)
            $this->total_paid_real += $order_payment->amount;
        else
            $this->total_paid_real += Tools::ps_round(Tools::convertPrice($order_payment->amount, $order_payment->id_currency, false), 2);

        // We put autodate parameter of add method to true if date_add field is null
        $res = $order_payment->add(is_null($order_payment->date_add)) && $this->update();

        if (!$res)
            return false;

        if (!is_null($order_invoice))
        {
            $res = Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`)
			VALUES('.(int)$order_invoice->id.', '.(int)$order_payment->id.', '.(int)$this->id.')');

            // Clear cache
            Cache::clean('order_invoice_paid_*');
        }

        return $res;
    }


    /**
     *
     * This method allows to change the shipping cost of the current order
     * @since 1.5.0.1
     * @param float $amount
     * @return bool
     */
    public function updateShippingCost($amount)
    {
        $difference = $amount - $this->total_shipping;
        // if the current amount is same as the new, we return true
        if ($difference == 0)
            return true;

        // update the total_shipping value
        $this->total_shipping = $amount;
        // update the total of this order
        $this->total_paid += $difference;

        // update database
        return $this->update();
    }

    /**
     * Returns the correct product taxes breakdown.
     *
     * @since 1.5.0.1
     * @return array
     */
    public function getProductTaxesBreakdown()
    {
        $tmp_tax_infos = array();
        if ($this->useOneAfterAnotherTaxComputationMethod())
        {
            // sum by taxes
            $taxes_by_tax = Db::getInstance()->executeS('
			SELECT odt.`id_order_detail`, t.`name`, t.`rate`, SUM(`total_amount`) AS `total_amount`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id.'
			GROUP BY odt.`id_tax`
			');

            // format response
            $tmp_tax_infos = array();
            foreach ($taxes_infos as $tax_infos)
            {
                $tmp_tax_infos[$tax_infos['rate']]['total_amount'] = $tax_infos['tax_amount'];
                $tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
            }
        }
        else
        {
            // sum by order details in order to retrieve real taxes rate
            $taxes_infos = Db::getInstance()->executeS('
			SELECT odt.`id_order_detail`, t.`rate` AS `name`, SUM(od.`total_price_tax_excl`) AS total_price_tax_excl, SUM(t.`rate`) AS rate, SUM(`total_amount`) AS `total_amount`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id.'
			GROUP BY odt.`id_order_detail`
			');

            // sum by taxes
            $tmp_tax_infos = array();
            foreach ($taxes_infos as $tax_infos)
            {
                if (!isset($tmp_tax_infos[$tax_infos['rate']]))
                    $tmp_tax_infos[$tax_infos['rate']] = array('total_amount' => 0,
                        'name' => 0,
                        'total_price_tax_excl' => 0);

                $tmp_tax_infos[$tax_infos['rate']]['total_amount'] += $tax_infos['total_amount'];
                $tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
                $tmp_tax_infos[$tax_infos['rate']]['total_price_tax_excl'] += $tax_infos['total_price_tax_excl'];
            }
        }

        return $tmp_tax_infos;
    }

    /**
     * Returns the shipping taxes breakdown
     *
     * @since 1.5.0.1
     * @return array
     */
    public function getShippingTaxesBreakdown()
    {
        $taxes_breakdown = array();

        $shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

        if ($shipping_tax_amount > 0)
            $taxes_breakdown[] = array(
                'rate' => $this->carrier_tax_rate,
                'total_amount' => $shipping_tax_amount
            );

        return $taxes_breakdown;
    }

    /**
     * Returns the wrapping taxes breakdown
     * @todo

     * @since 1.5.0.1
     * @return array
     */
    public function getWrappingTaxesBreakdown()
    {
        $taxes_breakdown = array();
        return $taxes_breakdown;
    }

    /**
     * Returns the ecotax taxes breakdown
     *
     * @since 1.5.0.1
     * @return array
     */
    public function getEcoTaxTaxesBreakdown()
    {
        return Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate`, SUM(`ecotax`) as `ecotax_tax_excl`, SUM(`ecotax`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int)$this->id
        );
    }


    /**
     * @since 1.5.0.4
     * @return OrderState or null if Order haven't a state
     */
    public function getCurrentOrderState()
    {
        if ($this->current_state)
            return new OrderState($this->current_state);
        return null;
    }

    /**
     * @see ObjectModel::getWebserviceObjectList()
     */
    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $sql_filter .= Shop::addSqlRestriction(Shop::SHARE_ORDER, 'main');
        return parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
    }

    /**
     * Get all other orders with the same reference
     *
     * @since 1.5.0.13
     */
    public function getBrother()
    {
        $collection = new PrestaShopCollection('order');
        $collection->where('reference', '=', $this->reference);
        $collection->where('id_order', '<>', $this->id);
        return $collection;
    }

    /**
     * Return a unique reference like : GWJTHMZUN#2
     *
     * With multi-shipping, order reference are the same for all orders made with the same cart
     * in this case this method suffix the order reference by a # and the order number
     *
     * @param $order_id
     * @return
     */
    public static function getUniqueReferenceOf($order_id){
        $order = new JeproshopOrderModelOrder($id_order);
        return $order->getUniqueReference();
    }

    /**
     * Return id of carrier
     *
     * Get id of the carrier used in order
     *
     * @since 1.5.5.0
     */
    public function getIdOrderCarrier()
    {
        return (int)Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$this->id);
    }

    public function getWsShippingNumber()
    {
        $id_order_carrier = Db::getInstance()->getValue('
			SELECT `id_order_carrier`
			FROM `'._DB_PREFIX_.'order_carrier`
			WHERE `id_order` = '.(int)$this->id);
        if ($id_order_carrier)
        {
            $order_carrier = new OrderCarrier($id_order_carrier);
            return $order_carrier->tracking_number;
        }
        return $this->shipping_number;
    }

    public function setWebServiceShippingNumber($shipping_number)
    {
        $id_order_carrier = Db::getInstance()->getValue('
			SELECT `id_order_carrier`
			FROM `'._DB_PREFIX_.'order_carrier`
			WHERE `id_order` = '.(int)$this->id);
        if ($id_order_carrier)
        {
            $order_carrier = new JeproshopOrderCarrierModelOrderCarrier($order_carrier_id);
            $order_carrier->tracking_number = $shipping_number;
            $order_carrier->update();
        }
        else
            $this->shipping_number = $shipping_number;
        return true;
    }
}


class JeproshopOrderDetailModelOrderDetail extends JModelLegacy
{
	/** @var integer */
	public $order_detail_id;
	
	/** @var integer */
	public $order_id;
	
	/** @var integer */
	public $order_invoice_id;
	
	/** @var integer */
	public $product_id;
	
	/** @var integer */
	public $shop_id;
	
	/** @var integer */
	public $product_attribute_id;
	
	/** @var string */
	public $product_name;
	
	/** @var integer */
	public $product_quantity;
	
	/** @var integer */
	public $product_quantity_in_stock;
	
	/** @var integer */
	public $product_quantity_return;
	
	/** @var integer */
	public $product_quantity_refunded;
	
	/** @var integer */
	public $product_quantity_reinserted;
	
	/** @var float */
	public $product_price;
	
	/** @var float */
	public $original_product_price;
	
	/** @var float */
	public $unit_price_tax_incl;
	
	/** @var float */
	public $unit_price_tax_excl;
	
	/** @var float */
	public $total_price_tax_incl;
	
	/** @var float */
	public $total_price_tax_excl;
	
	/** @var float */
	public $reduction_percent;
	
	/** @var float */
	public $reduction_amount;
	
	/** @var float */
	public $reduction_amount_tax_excl;
	
	/** @var float */
	public $reduction_amount_tax_incl;
	
	/** @var float */
	public $group_reduction;
	
	/** @var float */
	public $product_quantity_discount;
	
	/** @var string */
	public $product_ean13;
	
	/** @var string */
	public $product_upc;
	
	/** @var string */
	public $product_reference;
	
	/** @var string */
	public $product_supplier_reference;
	
	/** @var float */
	public $product_weight;
	
	/** @var float */
	public $ecotax;
	
	/** @var float */
	public $ecotax_tax_rate;
	
	/** @var integer */
	public $discount_quantity_applied;
	
	/** @var string */
	public $download_hash;
	
	/** @var integer */
	public $download_nb;
	
	/** @var date */
	public $download_deadline;
	
	/** @var string $tax_name **/
	public $tax_name;
	
	/** @var float $tax_rate **/
	public $tax_rate;
	
	/** @var float $tax_computation_method **/
	public $tax_computation_method;
	
	/** @var int Id warehouse */
	public $warehouse_id;
	
	/** @var float additional shipping price tax excl */
	public $total_shipping_price_tax_excl;
	
	/** @var float additional shipping price tax incl */
	public $total_shipping_price_tax_incl;
	
	/** @var float */
	public $purchase_supplier_price;
	
	private  $pagination = null;

    public function __construct($order_detail_id = null, $lang_id = null, $context = null)
    {
        $this->context = $context;
        $id_shop = null;
        if ($this->context != null && isset($this->context->shop))
            $id_shop = $this->context->shop->id;
        parent::__construct($id, $id_lang, $id_shop);

        if ($context == null)
            $context = Context::getContext();
        $this->context = $context->cloneContext();
    }


    /**
	 * Returns the tax calculator associated to this order detail.
	 * @since 1.5.0.1
	 * @return JeproshopTaxCalculator
	 */
	public function getTaxCalculator(){
		return JeproshopOrderDetailModelOrderDetail::getStaticTaxCalculator($this->order_detail_id);
	}
	
	/**
	 * Return the tax calculator associated to this order_detail
     *
	 * @param int $order_detail_id
	 * @return JeproshopTaxCalculator
	 */
	public static function getStaticTaxCalculator($order_detail_id){
		$db = JFactory::getDBO();
		
		$query = "SELECT order_detail_tax.*, order_detail." .$db->quoteName('tax_computation_method') . " FROM ";
		$query .= $db->quoteName('#__jeproshop_order_detail_tax') . " AS order_detail_tax LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_order_detail') . " AS  order_detail ON (order_detail.";
		$query .= $db->quoteName('order_detail_id') . " = order_detail_tax." . $db->quoteName('order_detail_id');
		$query .= ") WHERE order_detail." . $db->quoteName('order_detail_id') . " = " .(int)$order_detail_id;
	
		$computation_method = 1;
		$taxes = array();
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if ($results){
			foreach ($results as $result){
				$taxes[] = new JeproshopTaxModelTax((int)$result->tax_id);
			}
			$computation_method = $result->tax_computation_method;
		}	
		return new JeproshopTaxCalculator($taxes, $computation_method);
	}
	
	public static function getOderDetails($order_id){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_detail') . " WHERE " . $db->quoteName('order_id') . " = " . (int)$order_id;
		
		$db->setQuery($query);		 
		return  $db->loadObjectList();
	}

    public function delete()
    {
        if(!$res = parent::delete())
            return false;

        Db::getInstance()->delete('order_detail_tax', 'id_order_detail='.(int)$this->id);

        return $res;
    }

    protected function setContext($id_shop)
    {
        if ($this->context->shop->id != $id_shop)
            $this->context->shop = new Shop((int)$id_shop);
    }

    public static function getDownloadFromHash($hash)
    {
        if ($hash == '') return false;
        $sql = 'SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (od.`product_id`=pd.`id_product`)
		WHERE od.`download_hash` = \''.pSQL(strval($hash)).'\'
		AND pd.`active` = 1';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function incrementDownload($id_order_detail, $increment = 1)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'order_detail`
			SET `download_nb` = `download_nb` + '.(int)$increment.'
			WHERE `id_order_detail`= '.(int)$id_order_detail.'
			LIMIT 1';
        return Db::getInstance()->execute($sql);
    }

    /**
     * Return the tax calculator associated to this order_detail
     * @since 1.5.0.1
     * @param int $id_order_detail
     * @return TaxCalculator
     */
    public static function getTaxCalculatorStatic($id_order_detail)
    {
        $sql = 'SELECT t.*, d.`tax_computation_method`
				FROM `'._DB_PREFIX_.'order_detail_tax` t
				LEFT JOIN `'._DB_PREFIX_.'order_detail` d ON (d.`id_order_detail` = t.`id_order_detail`)
				WHERE d.`id_order_detail` = '.(int)$id_order_detail;

        $computation_method = 1;
        $taxes = array();
        if ($results = Db::getInstance()->executeS($sql))
        {
            foreach ($results as $result)
                $taxes[] = new Tax((int)$result['id_tax']);

            $computation_method = $result['tax_computation_method'];
        }

        return new TaxCalculator($taxes, $computation_method);
    }

    /**
     * Save the tax calculator
     * @since 1.5.0.1
     * @return boolean
     */
    public function saveTaxCalculator(Order $order, $replace = false)
    {
        // Nothing to save
        if ($this->tax_calculator == null)
            return true;

        if (!($this->tax_calculator instanceOf TaxCalculator))
            return false;

        if (count($this->tax_calculator->taxes) == 0)
            return true;

        if ($order->total_products <= 0)
            return true;

        $ratio = $this->unit_price_tax_excl / $order->total_products;
        $order_reduction_amount = $order->total_discounts_tax_excl * $ratio;
        $discounted_price_tax_excl = $this->unit_price_tax_excl - $order_reduction_amount;

        $values = '';
        foreach ($this->tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount)
        {
            $unit_amount = (float)Tools::ps_round($amount, 2);
            $total_amount = $unit_amount * $this->product_quantity;
            $values .= '('.(int)$this->id.','.(float)$id_tax.','.$unit_amount.','.(float)$total_amount.'),';
        }

        if ($replace)
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$this->id);

        $values = rtrim($values, ',');
        $sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;

        return Db::getInstance()->execute($sql);
    }

    public function updateTaxAmount($order)
    {
        $this->setContext((int)$this->id_shop);
        $address = new Address((int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $tax_manager = TaxManagerFactory::getManager($address, (int)Product::getIdTaxRulesGroupByIdProduct((int)$this->product_id, $this->context));
        $this->tax_calculator = $tax_manager->getTaxCalculator();

        return $this->saveTaxCalculator($order, true);
    }

    /**
     * Get a detailed order list of an id_order
     * @param int $id_order
     * @return array
     */
    public static function getList($id_order)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = '.(int)$id_order);
    }

    /*
     * Set virtual product information
     * @param array $product
     */
    protected function setVirtualProductInformation($product)
    {
        // Add some informations for virtual products
        $this->download_deadline = '0000-00-00 00:00:00';
        $this->download_hash = null;

        if ($id_product_download = ProductDownload::getIdFromIdProduct((int)($product['id_product'])))
        {
            $productDownload = new ProductDownload((int)($id_product_download));
            $this->download_deadline = $productDownload->getDeadLine();
            $this->download_hash = $productDownload->getHash();

            unset($productDownload);
        }
    }

    /**
     * Check the order status
     * @param array $product
     * @param int $id_order_state
     */
    protected function checkProductStock($product, $id_order_state)
    {
        if ($id_order_state != Configuration::get('PS_OS_CANCELED') && $id_order_state != Configuration::get('PS_OS_ERROR'))
        {
            $update_quantity = true;
            if (!StockAvailable::dependsOnStock($product['id_product']))
                $update_quantity = StockAvailable::updateQuantity($product['id_product'], $product['id_product_attribute'], -(int)$product['cart_quantity']);

            if ($update_quantity)
                $product['stock_quantity'] -= $product['cart_quantity'];

            if ($product['stock_quantity'] < 0 && Configuration::get('PS_STOCK_MANAGEMENT'))
                $this->outOfStock = true;
            Product::updateDefaultAttribute($product['id_product']);
        }
    }

    /**
     * Apply tax to the product
     * @param object $order
     * @param array $product
     */
    protected function setProductTax(Order $order, $product)
    {
        $this->ecotax = Tools::convertPrice(floatval($product['ecotax']), intval($order->id_currency));

        // Exclude VAT
        if (!Tax::excludeTaxeOption())
        {
            $this->setContext((int)$product['id_shop']);
            $id_tax_rules = (int)Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context);

            $tax_manager = TaxManagerFactory::getManager($this->vat_address, $id_tax_rules);
            $this->tax_calculator = $tax_manager->getTaxCalculator();
            $this->tax_computation_method = (int)$this->tax_calculator->computation_method;
        }

        $this->ecotax_tax_rate = 0;
        if (!empty($product['ecotax']))
            $this->ecotax_tax_rate = Tax::getProductEcotaxRate($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});


    }

    /**
     * Set specific price of the product
     * @param object $order
     */
    protected function setSpecificPrice(Order $order, $product = null)
    {
        $this->reduction_amount = 0.00;
        $this->reduction_percent = 0.00;
        $this->reduction_amount_tax_incl = 0.00;
        $this->reduction_amount_tax_excl = 0.00;

        if ($this->specificPrice)
            switch ($this->specificPrice['reduction_type'])
            {
                case 'percentage':
                    $this->reduction_percent = (float)$this->specificPrice['reduction'] * 100;
                    break;

                case 'amount':
                    $price = Tools::convertPrice($this->specificPrice['reduction'], $order->id_currency);
                    $this->reduction_amount = (float)(!$this->specificPrice['id_currency'] ?
                        $price : $this->specificPrice['reduction']);
                    if ($product !== null)
                        $this->setContext((int)$product['id_shop']);
                    $id_tax_rules = (int)Product::getIdTaxRulesGroupByIdProduct((int)$this->specificPrice['id_product'], $this->context);
                    $tax_manager = TaxManagerFactory::getManager($this->vat_address, $id_tax_rules);
                    $this->tax_calculator = $tax_manager->getTaxCalculator();

                    $this->reduction_amount_tax_incl = $this->reduction_amount;
                    $this->reduction_amount_tax_excl = Tools::ps_round($this->tax_calculator->removeTaxes($this->reduction_amount_tax_incl), 2);
                    break;
            }
    }

    /**
     * Set detailed product price to the order detail
     * @param object $order
     * @param object $cart
     * @param array $product
     */
    protected function setDetailProductPrice(Order $order, Cart $cart, $product)
    {
        $this->setContext((int)$product['id_shop']);
        Product::getPriceStatic((int)$product['id_product'], true, (int)$product['id_product_attribute'], 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, $this->context);
        $this->specificPrice = $specific_price;

        $this->original_product_price = Product::getPriceStatic($product['id_product'], false, (int)$product['id_product_attribute'], 6, null, false, false, 1, false, null, null, null, $null, true, true, $this->context);
        $this->product_price = $this->original_product_price;
        $this->unit_price_tax_incl = (float)$product['price_wt'];
        $this->unit_price_tax_excl = (float)$product['price'];
        $this->total_price_tax_incl = (float)$product['total_wt'];
        $this->total_price_tax_excl = (float)$product['total'];

        $this->purchase_supplier_price = (float)$product['wholesale_price'];
        if ($product['id_supplier'] > 0)
            $this->purchase_supplier_price = (float)ProductSupplier::getProductPrice((int)$product['id_supplier'], $product['id_product'], $product['id_product_attribute'], true);

        $this->setSpecificPrice($order, $product);

        $this->group_reduction = (float)(Group::getReduction((int)($order->id_customer)));

        $shop_id = $this->context->shop->id;

        $quantityDiscount = SpecificPrice::getQuantityDiscount((int)$product['id_product'], $shop_id,
            (int)$cart->id_currency, (int)$this->vat_address->id_country,
            (int)$this->customer->id_default_group, (int)$product['cart_quantity'], false, null, null, $null, true, true, $this->context);

        $unitPrice = Product::getPriceStatic((int)$product['id_product'], true,
            ($product['id_product_attribute'] ? intval($product['id_product_attribute']) : null),
            2, null, false, true, 1, false, (int)$order->id_customer, null, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $null, true, true, $this->context);
        $this->product_quantity_discount = 0.00;
        if ($quantityDiscount)
        {
            $this->product_quantity_discount = $unitPrice;
            if (Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC)
                $this->product_quantity_discount = Tools::ps_round($unitPrice, 2);

            if (isset($this->tax_calculator))
                $this->product_quantity_discount -= $this->tax_calculator->addTaxes($quantityDiscount['price']);
        }

        $this->discount_quantity_applied = (($this->specificPrice && $this->specificPrice['from_quantity'] > 1) ? 1 : 0);
    }

    /**
     * Create an order detail liable to an id_order
     * @param object $order
     * @param object $cart
     * @param array $product
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
     */
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
    {
        if ($use_taxes)
            $this->tax_calculator = new TaxCalculator();

        $this->id = null;

        $this->product_id = (int)($product['id_product']);
        $this->product_attribute_id = (int)($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : null);
        $this->product_name = $product['name'].
            ((isset($product['attributes']) && $product['attributes'] != null) ?
                ' - '.$product['attributes'] : '');

        $this->product_quantity = (int)($product['cart_quantity']);
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = (float)($product['id_product_attribute'] ? $product['weight_attribute'] : $product['weight']);
        $this->id_warehouse = $id_warehouse;

        $productQuantity = (int)(Product::getQuantity($this->product_id, $this->product_attribute_id));
        $this->product_quantity_in_stock = ($productQuantity - (int)($product['cart_quantity']) < 0) ?
            $productQuantity : (int)($product['cart_quantity']);

        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);

        if ($use_taxes)
            $this->setProductTax($order, $product);
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);

        // Set order invoice id
        $this->id_order_invoice = (int)$id_order_invoice;

        // Set shop id
        $this->id_shop = (int)$product['id_shop'];

        // Add new entry to the table
        $this->save();

        if ($use_taxes)
            $this->saveTaxCalculator($order);
        unset($this->tax_calculator);
    }

    /**
     * Create a list of order detail for a specified id_order using cart
     * @param object $order
     * @param object $cart
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
     */
    public function createList(Order $order, Cart $cart, $id_order_state, $product_list, $id_order_invoice = 0, $use_taxes = true, $id_warehouse = 0)
    {
        $this->vat_address = new Address((int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->customer = new Customer((int)($order->id_customer));

        $this->id_order = $order->id;
        $this->outOfStock = false;

        foreach ($product_list as $product)
            $this->create($order, $cart, $product, $id_order_state, $id_order_invoice, $use_taxes, $id_warehouse);

        unset($this->vat_address);
        unset($products);
        unset($this->customer);
    }

    /**
     * Get the state of the current stock product
     * @return array
     */
    public function getStockState()
    {
        return $this->outOfStock;
    }

    /**
     * Set the additional shipping information
     *
     * @param Order $order
     * @param $product
     */
    public function setShippingCost(Order $order, $product)
    {
        $tax_rate = 0;

        $carrier = OrderInvoice::getCarrier((int)$this->id_order_invoice);
        if (isset($carrier) && Validate::isLoadedObject($carrier))
            $tax_rate = $carrier->getTaxesRate(new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

        $this->total_shipping_price_tax_excl = (float)$product['additional_shipping_cost'];
        $this->total_shipping_price_tax_incl = (float)($this->total_shipping_price_tax_excl * (1 + ($tax_rate / 100)));
        $this->total_shipping_price_tax_incl = Tools::ps_round($this->total_shipping_price_tax_incl, 2);
    }

    public function getWsTaxes()
    {
        $query = new DbQuery();
        $query->select('id_tax as id');
        $query->from('order_detail_tax', 'tax');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (tax.`id_order_detail` = od.`id_order_detail`)');
        $query->where('od.`id_order_detail` = '.(int)$this->id_order_detail);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getCrossSells($id_product, $id_lang, $limit = 12)
    {
        if (!$id_product || !$id_lang)
            return;

        $front = true;
        if (!in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront')))
            $front = false;

        $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT o.id_order
		FROM '._DB_PREFIX_.'orders o
		LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order = o.id_order)
		WHERE o.valid = 1 AND od.product_id = '.(int)$id_product);

        if (sizeof($orders))
        {
            $list = '';
            foreach ($orders AS $order)
                $list .= (int)$order['id_order'].',';
            $list = rtrim($list, ',');

            $orderProducts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT od.product_id, p.id_product, pl.name, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price, cl.link_rewrite category, p.ean13, p.out_of_stock, p.id_category_default
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id'.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
				WHERE od.id_order IN ('.$list.')
					AND pl.id_lang = '.(int)$id_lang.'
					AND cl.id_lang = '.(int)$id_lang.'
					AND od.product_id != '.(int)$id_product.'
					AND i.cover = 1
					AND product_shop.active = 1'
                .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				ORDER BY RAND()
				LIMIT '.(int)$limit.'
			');

            $taxCalc = Product::getTaxCalculationMethod();
            if (is_array($orderProducts))
            {
                foreach ($orderProducts AS &$orderProduct)
                {
                    $orderProduct['image'] = Context::getContext()->link->getImageLink($orderProduct['link_rewrite'], (int)$orderProduct['product_id'].'-'.(int)$orderProduct['id_image'], ImageType::getFormatedName('medium'));
                    $orderProduct['link'] = Context::getContext()->link->getProductLink((int)$orderProduct['product_id'], $orderProduct['link_rewrite'], $orderProduct['category'], $orderProduct['ean13']);
                    if ($taxCalc == 0 OR $taxCalc == 2)
                        $orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], true, NULL);
                    elseif ($taxCalc == 1)
                        $orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], false, NULL);
                }
                return Product::getProductsProperties($id_lang, $orderProducts);
            }
        }
    }
	
}

class JeproshopOrderReturnModelOrderReturn extends JModelLegacy
{
	/** @var integer */
	public $order_return_id;
	
	/** @var integer */
	public $customer_id;
	
	/** @var integer */
	public $order_id;
	
	/** @var integer */
	public $state_id;
	
	/** @var string message content */
	public $question;
	
	/** @var string Object creation date */
	public $date_add;
	
	/** @var string Object last modification date */
	public $date_upd;
	
	/**
	 * Get return details for one product line
     *
	 * @param $order_detail_id
	 */
	public static function getProductReturnDetail($order_detail_id){
		$db = JFactory::getDBO();
		$query = "SELECT product_quantity, date_add, order_return_status_lang.name as status FROM " . $db->quoteName('#__jeproshop_order_return_detail');
		$query .= " AS order_return_detail LEFT JOIN " . $db->quoteName('#__jeproshop_order_return') . " AS order_return ON order_return.";
		$query .= "order_return_id = order_return_detail.order_return_id LEFT JOIN " . $db->quoteName('#__jeproshop_order_return_status_lang');
		$query .= " AS order_return_status_lang ON order_return_status_lang.order_return_status_id = order_return.state AND ";
		$query .= "order_return_status_lang.lang_id = " . (int)JeproshopContext::getContext()->language->lang_id;
		$query .= "	WHERE order_return_detail." . $db->quoteName('order_detail_id') . " = ".(int)$order_detail_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getOrdersReturn($customer_id, $order_id = false, $no_denied = false, JeproshopContext $context = null){
		if (!$context){	$context = JeproshopContext::getContext(); }
		
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_return') . " WHERE " . $db->quoteName('customer_id');
		$query .= " = " . (int)($customer_id) . ($order_id ? " AND " .$db->quoteName('order_id') . " = ".(int)($order_id) : "");
		$query .= ($no_denied ? " AND " . $db->quoteName('state') . " != 4" : ""). " ORDER BY " . $db->quoteName('date_add') . " DESC ";
		
		$db->setQuery($query);
		$data = $db->loadObjectList();
		
		foreach ($data as $k => $or){
			$state = new JeproshopOrderReturnStateModelOrderReturnState($or->state);
			$data[$k]->state_name = $state->name[$context->language->lang_id];
			$data[$k]->type = 'Return';
			$data[$k]->tracking_number = $or->order_return_id;
			$data[$k]->can_edit = false;
			$data[$k]->reference = JeproshopOrderModelOrder::getUniqReferenceOf($or->order_id);
		}
		return $data;
	}

    public function addReturnDetail($orderDetailList, $productQtyList, $customizationIds, $customizationQtyInput){
        $db = JFactory::getDBO();
        /** Classic product return **/
        if ($orderDetailList) {
            foreach ($orderDetailList as $key => $orderDetail){
                if ($qty = (int)$productQtyList[$key]){
                    $query = "INSERT INTO " . $db->quoteName('#__jeproshop_order_return_detail') . "(" . $db->quoteName('order_return_id') . ", " . $db->quoteName('order_detail_id') . ", " . $db->quoteName('product_quantity') . ", ";
                    $query .= $db->quoteName('customization_id') . ") VALUES (" . (int)$this->order_return_id . ", " . (int)$orderDetail . ", " . (int)$qty . ", 0)" ;

                    $db->setQuery($query);
                    $db->query();
                }
            }
        }

        /** Customized product return **/
        if ($customizationIds) {
            foreach ($customizationIds as $orderDetailId => $customizations) {
                foreach ($customizations as $customizationId) {
                    if ($quantity = (int)$customizationQtyInput[(int)$customizationId]) {
                        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_order_return_detail') . "(" . $db->quoteName('order_return_id') . ", " . $db->quoteName('order_detail_id') . ", " . $db->quoteName('product_quantity') . ", ";
                        $query .= $db->quoteName('customization_id') . ") VALUES (" . (int)$this->order_return_id . ", " . (int)$orderDetailId . ", " . (int)$quantity . ", " . (int)$customizationId . ")";

                        $db->setQuery($query);
                        $db->query();
                    }
                }
            }
        }
    }

    public function checkEnoughProduct($orderDetailList, $productQtyList, $customizationIds, $customizationQtyInput){
        $order = new JeproshopOrderModelOrder((int)($this->order_id));
        if (!JeproshopValidate::isLoadedObject($order, 'order_id')) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();
        /* Products already returned */
        $order_returns = JeproshopOrderReturnModelOrderReturn::getOrdersReturn($order->customer_id, $order->order_id, true);
        foreach ($order_returns as $order_return){
            $order_return_products = JeproshopOrderReturnModelOrderReturn::getOrdersReturnProducts($order_return->order_return_id, $order);
            foreach ($order_return_products AS $key => $orp) {
                $products[$key]->product_quantity -= (int)($orp->product_quantity);
            }
        }

        /* Quantity check */
        if ($orderDetailList) {
            foreach (array_keys($orderDetailList) as $key) {
                if ($qty = (int)($productQtyList[$key])) {
                    if ($products[$key]->product_quantity - $qty < 0) {
                        return false;
                    }
                }
            }
        }
        /* Customization quantity check */
        if ($customizationIds){
            $orderedCustomizations = JeproshopCustomization::getOrderedCustomizations((int)($order->cart_id));
            foreach ($customizationIds as $customizations) {
                foreach ($customizations as $customizationId) {
                    $customizationId = (int)$customizationId;
                    if (!isset($orderedCustomizations[$customizationId]))
                        return false;
                    $quantity = (isset($customizationQtyInput[$customizationId]) ? (int)($customizationQtyInput[$customizationId]) : 0);
                    if ((int)($orderedCustomizations[$customizationId]->quantity) - $quantity < 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function countProduct(){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(" . $db->quoteName('order_return_id') . ") AS total FROM " . $db->quoteName('#__jeproshop_order_return_detail') . " WHERE ";
        $query .= $db->quoteName('order_return_id') . " = " .(int)($this->order_return_id);

        $db->setQuery($query);
        $data = $db->loadObject();
        if (!$data)
            return false;
        return (int)($data->total);
    }

    public static function getOrdersReturnDetail($order_return_id){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_return_detail') . " WHERE " . $db->quoteName('order_return_id') . " = " .(int)($order_return_id);

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getOrdersReturnProducts($orderReturnId, $order){
        $productsRet = JeproshopOrderReturnModelOrderReturn::getOrdersReturnDetail($orderReturnId);
        $products = $order->getProducts();
        $tmp = array();
        foreach ($productsRet as $return_detail){
            $tmp[$return_detail->order_detail_id]['quantity'] = isset($tmp[$return_detail['id_order_detail']]['quantity']) ? $tmp[$return_detail['id_order_detail']]['quantity'] + (int)($return_detail['product_quantity']) : (int)($return_detail['product_quantity']);
            $tmp[$return_detail->order_detail_id]['customizations'] = (int)($return_detail['id_customization']);
        }
        $resTab = array();
        foreach ($products as $key => $product) {
            if (isset($tmp[$product->order_detail_id])) {
                $resTab[$key] = $product;
                $resTab[$key]['product_quantity'] = $tmp[$product->order_detail_id]['quantity'];
                $resTab[$key]['customizations'] = $tmp[$product->order_detail_id]['customizations'];
            }
        }
        return $resTab;
    }

    public static function getReturnedCustomizedProducts($order_id) {
        $returns = JeproshopCustomization::getReturnedCustomizations($order_id);
        $order = new JeproshopOrderModelOrder((int)($order_id));
        if (!JeproshopValidate::isLoadedObject($order, 'order_id')) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();

        foreach ($returns as &$return){
            $return->product_id = (int)($products[(int)($return['id_order_detail'])]['product_id']);
            $return->product_attribute_id = (int)($products[(int)($return['id_order_detail'])]['product_attribute_id']);
            $return->name = $products[(int)($return['id_order_detail'])]['product_name'];
            $return->reference = $products[(int)($return['id_order_detail'])]['product_reference'];
            $return->address_delivery_id = $products[(int)($return['id_order_detail'])]['id_address_delivery'];
        }
        return $returns;
    }

    public static function deleteOrderReturnDetail($id_order_return, $id_order_detail, $id_customization = 0)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_return_detail` WHERE `id_order_detail` = '.(int)($id_order_detail).' AND `id_order_return` = '.(int)($id_order_return).' AND `id_customization` = '.(int)($id_customization));
    }

    /**
     *
     * Add returned quantity to products list
     * @param array $products
     * @param int $id_order
     */
    public static function addReturnedQuantity(&$products, $id_order)
    {
        $details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT od.id_order_detail, GREATEST(od.product_quantity_return, IFNULL(SUM(ord.product_quantity),0)) as qty_returned
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'order_return_detail ord
			ON ord.id_order_detail = od.id_order_detail
			WHERE od.id_order = '.(int)$id_order.'
			GROUP BY od.id_order_detail'
        );
        if (!$details)
            return;

        $detail_list = array();
        foreach ($details as $detail)
            $detail_list[$detail['id_order_detail']] = $detail;

        foreach ($products as &$product)
            if (isset($detail_list[$product['id_order_detail']]['qty_returned']))
                $product['qty_returned'] = $detail_list[$product['id_order_detail']]['qty_returned'];
    }
}

class JeproshopOrderSlipModelOrderSlip extends JModelLegacy
{
    public $order_slip_id;
    public $customer_id;
    public $order_id;
    public $conversion_rate;

    /** @var integer */
    public $amount;

    /** @var integer */
    public $shipping_cost;

    /** @var integer */
    public $shipping_cost_amount;

    /** @var integer */
    public $partial;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

	/**
	 * Get resume of all refund for one product line
     *
     * @param $order_detail_id
	 */
	public static function getProductSlipResume($order_detail_id){
		$db = JFactory::getDBO();
		$query = "SELECT SUM(product_quantity) product_quantity, SUM(amount_tax_excl) amount_tax_excl, SUM(amount_tax_incl) ";
		$query .= "amount_tax_incl FROM " . $db->quoteName('#__jeproshop_order_slip_detail') . " WHERE "; 
		$query .= $db->quoteName('order_detail_id') . " = " . (int)$order_detail_id;
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Get refund details for one product line
     *
	 * @param $order_detail_id
	 */
	public static function getProductSlipDetail($order_detail_id){
		$db = JFactory::getDBO();
		
		$query = "SELECT product_quantity, amount_tax_excl, amount_tax_incl, date_add FROM " . $db->quoteName('#__jeproshop_order_slip_detail');
		$query .= " AS order_slip_detail LEFT JOIN " . $db->quoteName('#__jeproshop_order_slip') . " AS order_slip ON order_slip.order_slip_id";
		$query .= "= order_slip_detail.order_slip_id WHERE order_slip_detail." . $db->quoteName('order_detail_id') . " = " .(int)$order_detail_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function addSlipDetail($orderDetailList, $productQtyList)
    {
        foreach ($orderDetailList as $key => $id_order_detail)
        {
            if ($qty = (int)($productQtyList[$key]))
            {
                $order_detail = new OrderDetail((int)$id_order_detail);

                if (Validate::isLoadedObject($order_detail))
                    Db::getInstance()->insert('order_slip_detail', array(
                        'id_order_slip' => (int)$this->id,
                        'id_order_detail' => (int)$id_order_detail,
                        'product_quantity' => $qty,
                        'amount_tax_excl' => $order_detail->unit_price_tax_excl * $qty,
                        'amount_tax_incl' => $order_detail->unit_price_tax_incl * $qty
                    ));
            }
        }
    }

    public static function getOrdersSlip($customer_id, $order_id = false) {
        $db = JFactory::getDBO();
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_slip') . " WHERE " . $db->quoteName('customer_id') . " = " .(int)($customer_id);
        $query .= ($order_id ? " AND " . $db->quoteName('order_id') . " = " .(int)($order_id) : ""). " ORDER BY " . $db->quoteName('date_add') . " DESC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getOrdersSlipDetail($id_order_slip = false, $id_order_detail = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            ($id_order_detail ? 'SELECT SUM(`product_quantity`) AS `total`' : 'SELECT *').
            'FROM `'._DB_PREFIX_.'order_slip_detail`'
            .($id_order_slip ? ' WHERE `id_order_slip` = '.(int)($id_order_slip) : '')
            .($id_order_detail ? ' WHERE `id_order_detail` = '.(int)($id_order_detail) : ''));
    }

    public static function getOrdersSlipProducts($orderSlipId, $order){
        $cart_rules = $order->getCartRules(true);
        $productsRet = JeproshopOrderSlipModelOrderSlip::getOrdersSlipDetail($orderSlipId);
        $order_details = $order->getProductsDetail();

        $slip_quantity = array();
        foreach ($productsRet as $slip_detail)
            $slip_quantity[$slip_detail['id_order_detail']] = $slip_detail['product_quantity'];
        $products = array();
        foreach ($order_details as $key => $product)
            if (isset($slip_quantity[$product['id_order_detail']]))
            {
                $products[$key] = $product;
                $products[$key]['product_quantity'] = $slip_quantity[$product['id_order_detail']];
            }
        return $order->getProducts($products);
    }

    public function getProducts() {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *, osd.product_quantity
		FROM `'._DB_PREFIX_.'order_slip_detail` osd
		INNER JOIN `'._DB_PREFIX_.'order_detail` od ON osd.id_order_detail = od.id_order_detail
		WHERE osd.`id_order_slip` = '.(int)$this->id);

        $order = new Order($this->id_order);
        $products = array();
        foreach ($result as $row)
        {
            $order->setProductPrices($row);
            $products[] = $row;
        }
        return $products;
    }

    public static function getSlipsIdByDate($dateFrom, $dateTo){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_order_slip`
		FROM `'._DB_PREFIX_.'order_slip` os
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = os.`id_order`)
		WHERE os.`date_add` BETWEEN \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\'
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
		ORDER BY os.`date_add` ASC');

        $slips = array();
        foreach ($result as $slip)
            $slips[] = (int)$slip['id_order_slip'];
        return $slips;
    }

    public static function createOrderSlip($order, $productList, $qtyList, $shipping_cost = false){
        $currency = new JeproshopCurrencyModelCurrency($order->currency_id);
        $orderSlip = new JeproshopOrderSlipModelOrderSlip();
        $orderSlip->customer_id = (int)($order->customer_id);
        $orderSlip->order_id = (int)($order->order_id);
        $orderSlip->shipping_cost = (int)$shipping_cost;
        if ($orderSlip->shipping_cost)
            $orderSlip->shipping_cost_amount = $order->total_shipping_tax_incl;
        $orderSlip->conversion_rate = $currency->conversion_rate;
        $orderSlip->partial = 0;

        $orderSlip->amount = $orderSlip->shipping_cost_amount;
        foreach ($productList as $order_detail_id){
            $order_detail = new JeproshopOrderDetailModelOrderDetail((int)$order_detail_id);
            $orderSlip->amount += $order_detail->unit_price_tax_incl * $qtyList[(int)$order_detail_id];
        }

        if (!$orderSlip->add())
            return false;

        $orderSlip->addSlipDetail($productList, $qtyList);
        return true;
    }

    public static function createPartialOrderSlip($order, $amount, $shipping_cost_amount, $order_detail_list){
        $currency = new JeproshopCurrencyModelCurrency($order->currency_id);
        $orderSlip = new JeproshopOrderSlipModelOrderSlip();
        $orderSlip->customer_id = (int)($order->customer_id);
        $orderSlip->order_id = (int)($order->order_id);
        $orderSlip->amount = (float)($amount);
        $orderSlip->shipping_cost = false;
        $orderSlip->shipping_cost_amount = (float)($shipping_cost_amount);
        $orderSlip->conversion_rate = $currency->conversion_rate;
        $orderSlip->partial = 1;
        if (!$orderSlip->add())
            return false;

        $orderSlip->addPartialSlipDetail($order_detail_list);
        return true;
    }

    public function addPartialSlipDetail($order_detail_list){
        foreach ($order_detail_list as $id_order_detail => $tab)
        {
            $order_detail = new OrderDetail($id_order_detail);
            $order_slip_resume = self::getProductSlipResume($id_order_detail);

            if ($tab['amount'] + $order_slip_resume['amount_tax_incl'] > $order_detail->total_price_tax_incl)
                $tab['amount'] = $order_detail->total_price_tax_incl - $order_slip_resume['amount_tax_incl'];

            if ($tab['amount'] == 0)
                continue;

            if ($tab['quantity'] + $order_slip_resume['product_quantity'] > $order_detail->product_quantity)
                $tab['quantity'] = $order_detail->product_quantity - $order_slip_resume['product_quantity'];

            $tab['amount_tax_excl'] = $tab['amount_tax_incl'] = $tab['amount'];
            $id_tax = (int)Db::getInstance()->getValue('SELECT `id_tax` FROM `'._DB_PREFIX_.'order_detail_tax` WHERE `id_order_detail` = '.(int)$id_order_detail);
            if ($id_tax > 0)
            {
                $rate = (float)Db::getInstance()->getValue('SELECT `rate` FROM `'._DB_PREFIX_.'tax` WHERE `id_tax` = '.(int)$id_tax);
                if ($rate > 0)
                {
                    $rate = 1 + ($rate / 100);
                    $tab['amount_tax_excl'] = $tab['amount_tax_excl'] / $rate;
                }
            }

            if ($tab['quantity'] > 0 && $tab['quantity'] > $order_detail->product_quantity_refunded)
            {
                $order_detail->product_quantity_refunded = $tab['quantity'];
                $order_detail->save();
            }

            $insertOrderSlip = array(
                'id_order_slip' => (int)($this->id),
                'id_order_detail' => (int)($id_order_detail),
                'product_quantity' => (int)($tab['quantity']),
                'amount_tax_excl' => (float)($tab['amount_tax_excl']),
                'amount_tax_incl' => (float)($tab['amount_tax_incl']),
            );

            Db::getInstance()->insert('order_slip_detail', $insertOrderSlip);
        }
    }

    public function getEcoTaxTaxesBreakdown(){
        $ecotax_detail = array();
        foreach ($this->getOrdersSlipDetail((int)$this->id) as $order_slip_details)
        {
            $row = Db::getInstance()->getRow('
					SELECT `ecotax_tax_rate` as `rate`, `ecotax` as `ecotax_tax_excl`, `ecotax` as `ecotax_tax_incl`, `product_quantity`
					FROM `'._DB_PREFIX_.'order_detail`
					WHERE `id_order_detail` = '.(int)$order_slip_details['id_order_detail']
            );

            if (!isset($ecotax_detail[$row['rate']]))
                $ecotax_detail[$row['rate']] = array('ecotax_tax_incl' => 0, 'ecotax_tax_excl' => 0, 'rate' => $row['rate']);

            $ecotax_detail[$row->rate]['ecotax_tax_incl'] += Tools::ps_round(($row['ecotax_tax_excl'] * $order_slip_details['product_quantity']) + ($row['ecotax_tax_excl'] * $order_slip_details['product_quantity'] * $row['rate'] / 100), 2);
            $ecotax_detail[$row->rate]['ecotax_tax_excl'] += Tools::ps_round($row['ecotax_tax_excl'] * $order_slip_details['product_quantity'], 2);
        }

        return $ecotax_detail;
    }

    public function getWsOrderSlipDetails(){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('order_slip_id') . " AS id, " . $db->quoteName('order_detail_id') . ", product_quantity, amount_tax_excl, amount_tax_incl FROM ";
        $query .= $db->quoteName('#__jeproshop_order_slip_detail') . " WHERE order_slip_id = ".(int)$this->order_slip_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function setWsOrderSlipDetails($values){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_order_slip_detail') . " WHERE " . $db->quoteName('order_slip_id') . " = " .(int)$this->order_slip_id;
        $db->setQuery($query);
        if ($db->query()){
            $query = "INSERT INTO " . $db->quoteName('#jeproshop_order_slip_detail') . "(" . $db->quoteName('order_slip_id') . ", " . $db->quoteName('order_detail_id') . ", " . $db->quoteName('product_quantity') . ", ";
            $query .= $db->quoteName('amount_tax_excl') . ", " . $db->quoteName('amount_tax_incl') . ") VALUES ";

            foreach ($values as $value) {
                $query .= "(" . (int)$this->order_slip_id . ", " . (int)$value->order_detail_id . ", " . (int)$value->product_quantity . ", " .
                    (isset($value->amount_tax_excl) ? (float)$value->amount_tax_excl : 'NULL') . ", " .
                    (isset($value->amount_tax_incl) ? (float)$value->amount_tax_incl : 'NULL') . "),";
            }
            $query = rtrim($query, ',');
            $db->setQuery($query);
            $db->query();
        }
        return true;
    }
}



class JeproshopOrderMessageModelOrderMessage extends JModelLegacy
{
	/** @var string name name */
	public $name;
	
	/** @var string message content */
	public $message;
	
	/** @var string Object creation date */
	public $date_add;
	
	public static function getOrderMessages($lang_id){
		$db = JFactory::getDBO();
		
		$query = "SELECT order_message.order_message_id, order_message_lang.name, order_message_lang.message FROM ";
		$query .= $db->quoteName('#__jeproshop_order_message') . " AS order_message LEFT JOIN " . $db->quoteName('#__jeproshop_order_message_lang');
		$query .= " AS order_message_lang ON (order_message_lang.order_message_id = order_message.order_message_id )";
		$query .= " WHERE order_message_lang.lang_id = " . (int)$lang_id . " ORDER BY name ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList(); 
	}
}


class JeproshopOrderStatusModelOrderStatus extends JModelLegacy
{
	/** @var string Name */
	public $name;
	
	/** @var string Template name if there is any e-mail to send */
	public $template;
	
	/** @var boolean Send an e-mail to customer ? */
	public $send_email;
	
	public $module_name;
	
	/** @var boolean Allow customer to view and download invoice when order is at this state */
	public $invoice;
	
	/** @var string Display state in the specified color */
	public $color;
	
	public $unremovable;
	
	/** @var boolean Log authorization */
	public $logable;
	
	/** @var boolean Delivery */
	public $delivery;
	
	/** @var boolean Hidden */
	public $hidden;
	
	/** @var boolean Shipped */
	public $shipped;
	
	/** @var boolean Paid */
	public $paid;
	
	/** @var boolean True if carrier has been deleted (staying in database as deleted) */
	public $deleted = 0;
	
	const FLAG_NO_HIDDEN	= 1;  /* 00001 */
	const FLAG_LOGABLE		= 2;  /* 00010 */
	const FLAG_DELIVERY		= 4;  /* 00100 */
	const FLAG_SHIPPED		= 8;  /* 01000 */
	const FLAG_PAID			= 16; /* 10000 */

    private $pagination;
	
	/**
	 * Get all available order statuses
	 *
	 * @param integer $lang_id Language id for status name
	 * @return array Order statues
	 */
	public static function getOrderStatus($lang_id){
		$cache_id = 'jeproshop_order_status_get_order_status_'.(int)$lang_id;
		if (!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
			
			$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_status') . " AS order_status LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_order_status_lang') . " AS order_status_lang ON (order_status.";
			$query .= $db->quoteName('order_status_id') . " = order_status_lang." . $db->quoteName('order_status_id');
			$query .= " AND order_status_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE deleted = 0 ";
			$query .= " ORDER BY " . $db->quoteName('name') . " ASC";
			
			$db->setQuery($query);
			$result = $db->loadObjectList();
			
			JeproshopCache::store($cache_id, $result);
		}
		return JeproshopCache::retrieve($cache_id);
	}

    /**
     * Check if we can make a invoice when order is in this state
     *
     * @param integer $order_status_id State ID
     * @return boolean availability
     */
    public static function invoiceAvailable($order_status_id){
        $result = false;
        if (Configuration::get('PS_INVOICE')) {
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('invoice') . " FROM " . $db->quoteName('#__jeproshop_order_status') . " WHERE " . $db->quoteName('order_status_id') . " = " . (int)$order_status_id;

            $db->setQuery($query);
            $result = $db->loadResult();
        }
        return (bool)$result;
    }

    public function isRemovable(){
        return !($this->unremovable);
    }

    public function getOrderStatusList(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $context = JeproshopContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitStart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'order_status_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC' , 'string');

        $select = $select_shop = $where_shop = $join_shop = "";

        $lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_order_status_lang') . " AS order_status_lang ON(order_status_lang." . $db->quoteName('order_status_id');
        $lang_join .= " = order_status." . $db->quoteName('order_status_id') . " AND order_status_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
        /*if($shop_lang_id){
            if(!JeproshopShopModelShop::isFeaturePublished()){
                $lang_join .= " AND order_status_lang." . $db->quoteName('shop_id') . " = 1";
            }elseif(JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP){
                $lang_join .= " AND order_status_lang." . $db->quoteName('shop_id') . " = " . (int)$shop_lang_id;
            }else{
                $lang_join .= " AND order_status_lang." . $db->quoteName('shop_id') . " = order_status.default_shop_id";
            }
        }
        $lang_join .= ")"; */

        $having_clause = "";

        $where = "";
        if(JeproshopShopModelShop::isTableAssociated('order_status')){
            if(JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_ALL || !$context->employee->isSuperAdmin()){
                $test_join = "";
                if(JeproshopShopModelShop::isFeaturePublished() && $test_join && JeproshopShopModelShop::isTableAssociated('order_status')){
                    $where .= " AND order_status." . $db->quoteName('order_status_id') . " IN (SELECT orders_status_shop." . $db->quoteName('order_status_id');
                    $where .= " FROM " . $db->quoteName('#__jeproshop_order_status_shop') . " AS order_status_shop WHERE order_status_shop.shop_id IN (";
                    $where .= implode(', ', JeproshopShopModelShop::getContextListShopIds()) . ") )";
                }
            }
        }

        $use_limit = true;
        if($limit === false){
            $use_limit = false;
        }
        $deleted = $group = $filter = $join = "";
        //", order_status." . $db->quoteName('logo') .
        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS order_status." . $db->quoteName('order_status_id') . ", order_status_lang." . $db->quoteName('name');
            $query .= ", order_status." . $db->quoteName('send_email') . ", order_status.";
            $query .= $db->quoteName('delivery') . ", order_status." . $db->quoteName('invoice') . ", order_status_lang." . $db->quoteName('template');
            $query .= $select . $select_shop . " FROM " . $db->quoteName('#__jeproshop_order_status'). " AS order_status " . $lang_join . $join;
            $query .= $join_shop . " WHERE 1 " . $where . $deleted . $filter . $where_shop . $group . $having_clause . " ORDER BY ";
            $query .= ((str_replace('`', '', $order_by) == 'order_status_id') ? "order_status." : "") . $order_by . " " . $order_way;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= ($use_limit ? " LIMIT " . (int)$limitStart . ", " . (int)$limit : "");

            $db->setQuery($query);
            $orderStatus = $db->loadObjectList();

            if($orderStatus == false){ break; }
            if($use_limit){
                $limitStart = (int)$limitStart - (int)$limit;
                if($limitStart < 0){ break; }
            }else{ break; }

        }while(empty($orderStatus));

        $this->pagination = new JPagination($total, $limitStart, $limit);
        return  $orderStatus;
    }

    public function getOrderReturnStatusList(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $context = JeproshopContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitStart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'order_return_status_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC' , 'string');

        $use_limit = true;
        if($limit === false){
            $use_limit = false;
        }

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS return_status." . $db->quoteName('order_return_status_id') . ", return_status_lang." . $db->quoteName('name');
            $query .= ", return_status." . $db->quoteName('color') . " FROM " . $db->quoteName('#__jeproshop_order_return_status') . " AS return_status LEFT JOIN ";
            $query .= $db->quoteName('#__jeproshop_order_return_status_lang') . " AS return_status_lang ON (return_status." . $db->quoteName('order_return_status_id');
            $query .= " = return_status_lang." . $db->quoteName('order_return_status_id') . " AND return_status_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id;
            $query .= ") WHERE 1 ORDER BY " . ((str_replace('`', '', $order_by) == 'order_return_status_id') ? "return_status." : "" ). $order_by . " " . $order_way;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= ($use_limit ? " LIMIT " . (int)$limitStart . ", " . (int)$limit : "");
            $db->setQuery($query);

            $returnStatus = $db->loadObjectList();

            if($returnStatus == false){ break; }
            if($use_limit){
                $limitStart = (int)$limitStart - (int)$limit;
                if($limitStart < 0){ break; }
            }else{ break; }

        }while(empty($returnStatus));

        $this->pagination = new JPagination($total, $limitStart, $limit);
        return  $returnStatus;
    }

    public function getPagination(){ return $this->pagination; }
}


class JeproshopOrderPaymentModelOrderPayment extends JModelLegacy
{
    public $order_payment_id;
	public $order_reference;
	public $currency_id;
	public $amount;
	public $payment_method;
	public $conversion_rate;
	public $transaction_id;
	public $card_number;
	public $card_brand;
	public $card_expiration;
	public $card_holder;
	public $date_add;
	
	/**
	 * Get the detailed payment of an order
	 * @param int $order_reference
	 * @return array
	 * @since 1.5.0.13
	 */
	public static function getByOrderReference($order_reference){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_payment') . " WHERE ";
		$query .= $db->quoteName('order_reference') . " = " . $db->quote($order_reference);
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues))
        {
            Hook::exec('actionPaymentCCAdd', array('paymentCC' => $this));
            return true;
        }
        return false;
    }

    /**
     * Get the detailed payment of an order
     * @param int $order_id
     * @return array
     */
    public static function getByOrderId($order_id){
        Tools::displayAsDeprecated();
        $order = new JeproshopOrderModeOrder($order_id);
        return JeproshopOrderPaymentModelOrderPayment::getByOrderReference($order->reference);
    }

    /**
     * Get Order Payments By Invoice ID
     * @static
     * @param int $invoice_id Invoice ID
     * @return PrestaShopCollection Collection of OrderPayment
     */
    public static function getByInvoiceId($invoice_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('order_payment_id') . " FROM " . $db->quoteName('#__jeproshop_order_invoice_payment') . " WHERE " . $db->quoteName('order_invoice_id') . " = " . (int)$invoice_id;

        $db->setQuery($query);
        $payments = $db->loadObjectList();
        if (!$payments)
            return array();

        $payment_list = array();
        foreach ($payments as $payment) {
            $payment_list[] = $payment->order_payment_id;
        }

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_order_payment') . " WHERE " . $db->quoteName('order_payment_id') . " IN( " . implode($payment_list) . ") ";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Return order invoice object linked to the payment
     *
     * @param int $order_id Order Id
     *
     * @return bool|JeproshopOrderInvoiceModelOrderInvoice
     */
    public function getOrderInvoice($order_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('order_invoice_id') . " FROM " . $db->quoteName('#__jeproshop_order_invoice_payment') . " WHERE " . $db->quoteName('order_payment_id') ." = " .(int)$this->order_payment_id;
        $query .= " AND " . $db->quoteName('order_id') . " = " .(int)$order_id;

        $db->setQuery($query);
        $res = $db->loadResult();

        if (!$res)
            return false;

        return new JeproshopOrderInvoiceModelOrderInvoice((int)$res);
    }
}