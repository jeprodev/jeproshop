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

class JeproshopCartModelCart extends JModelLegacy
{
	public $cart_id;

	public $shop_group_id;

	public $shop_id;

	/** @var integer Customer delivery address ID */
	public $address_delivery_id;

	/** @var integer Customer invoicing address ID */
	public $address_invoice_id;

	/** @var integer Customer currency ID */
	public $currency_id;

	/** @var integer Customer ID */
	public $customer_id;

	/** @var integer Guest ID */
	public $guest_id;

	/** @var integer Language ID */
	public $lang_id;

	/** @var boolean True if the customer wants a recycled package */
	public $recyclable = 0;

	/** @var boolean True if the customer wants a gift wrapping */
	public $gift = 0;

	/** @var string Gift message if specified */
	public $gift_message;

	/** @var boolean Mobile Theme */
	public $mobile_theme;

	/** @var string Object creation date */
	public $date_add;

	/** @var string secure_key */
	public $secure_key;

	/** @var integer Carrier ID */
	public $carrier_id = 0;

	/** @var string Object last modification date */
	public $date_upd;

	public $checkedTos = false;
	public $pictures;
	public $textFields;

	public $delivery_option;

	/** @var boolean Allow to separate order in multiple package in order to receive as soon as possible the available products */
	public $allow_separated_package = false;

	protected static $_nbProducts = array();
	protected static $_isVirtualCart = array();

	protected $_products = null;
	protected static $_totalWeight = array();
	protected $_taxCalculationMethod = COM_JEPROSHOP_TAX_EXCLUDED;
	protected static $_carriers = null;
	protected static $_taxes_rate = null;
	protected static $_attributesLists = array();

    private $pagination;

    const ONLY_PRODUCTS = 1;
    const ONLY_DISCOUNTS = 2;
    const BOTH = 3;
    const BOTH_WITHOUT_SHIPPING = 4;
    const ONLY_SHIPPING = 5;
    const ONLY_WRAPPING = 6;
    const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;
    const ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING = 8;

	public function __construct($cart_id = null){
		$db = JFactory::getDBO();

		/*$this->def = ObjectModel::getDefinition($this);
		$this->setDefinitionRetroCompatibility();

		if ($id_lang !== null)
			$this->id_lang = (Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

		if ($id_shop && $this->isMultishop())
		{
			$this->id_shop = (int)$id_shop;
			$this->get_shop_from_context = false;
		} */

		if ($this->isMultishop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		}

		if ($cart_id){
			// Load object from database if object id is present
			$cache_id = 'jeproshop_model_cart_' . (int)$cart_id.'_'.(int)$this->shop_id;
			if (!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_cart') . " AS cart WHERE cart.cart_id = " . (int)$cart_id;

				// Get lang informations
				/*if ($id_lang)
				{
					$sql->leftJoin($this->def['table'].'_lang', 'b', 'a.'.$this->def['primary'].' = b.'.$this->def['primary'].' AND b.id_lang = '.(int)$id_lang);
					if ($this->id_shop && !empty($this->def['multilang_shop']))
						$sql->where('b.id_shop = '.$this->id_shop);
				} */

				// Get shop informations
				if (JeproshopShopModelShop::isTableAssociated('cart')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_cart_shop') . " AS cart_shop ON(cart.cart_id =";
					$query .= " = cart_shop.cart_id AND cart_shop.shop_id = " . (int)$this->shop_id . ")";
				}

				$db->setQuery($query);
				$cart_data = $db->loadObject();
				if($cart_data){
					/*if (!$id_lang && isset($this->def['multilang']) && $this->def['multilang'])
					{
						$sql = 'SELECT * FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_lang`
								WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$id
								.(($this->id_shop && $this->isLangMultishop()) ? ' AND `id_shop` = '.$this->id_shop : '');
						if ($object_datas_lang = ObjectModel::$db->executeS($sql))
							foreach ($object_datas_lang as $row)
								foreach ($row as $key => $value)
								{
									if (array_key_exists($key, $this) && $key != $this->def['primary'])
									{
										if (!isset($object_datas[$key]) || !is_array($object_datas[$key]))
											$object_datas[$key] = array();
										$object_datas[$key][$row['id_lang']] = $value;
									}
								}
					} */
					JeproshopCache::store($cache_id, $cart_data);
				}
			}else{
				$cart_data = JeproshopCache::retrieve($cache_id);
			}

			if ($cart_data){
				$this->cart_id = (int)$cart_id;
				foreach ($cart_data as $key => $value){
					if (array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		/*
		if (!is_null($id_lang))
			$this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');
		*/
		if ($this->customer_id){
			if (isset(JeproshopContext::getContext()->customer) && JeproshopContext::getContext()->customer->customer_id == $this->customer_id){
				$customer = JeproshopContext::getContext()->customer;
			}else{
				$customer = new JeproshopCustomerModelCustomer((int)$this->customer_id);
			}
			if ((!$this->secure_key || $this->secure_key == '-1') && $customer->secure_key)
			{
				$this->secure_key = $customer->secure_key;
				$this->save();
			}
		}
		$this->_taxCalculationMethod = JeproshopGroupModelGroup::getPriceDisplayMethod(JeproshopGroupModelGroup::getCurrent()->group_id);
	}

	public static function getCustomerCarts($customer_id, $with_order = true){
		$db = JFactory::getDBO();

		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_cart') . " AS cart WHERE cart." . $db->quoteName('customer_id') . " = " . (int)$customer_id;
		$query .= (!$with_order ? "AND cart_id NOT IN (SELECT cart_id FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord )" : "") . " ORDER BY cart.";
		$query .= $db->quoteName('date_add') . " DESC";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public static function getOrderTotalUsingTaxCalculationMethod($cart_id){
        $context = JeproshopContext::getContext();
        $context->cart = new JeproshopCartModelCart($cart_id);
        $context->currency = new JeproshopCurrencyModelCurrency((int)$context->cart->currency_id);
        $context->customer = new JeproshopCustomerModelCustomer((int)$context->cart->customer_id);
        return JeproshopCartModelCart::getTotalCart($cart_id, true, JeproshopCartModelCart::BOTH_WITHOUT_SHIPPING);
    }

    public static function getTotalCart($cart_id, $use_tax_display = false, $type = JeproshopCartModelCart::BOTH)
    {
        $cart = new JeproshopCartModelCart($cart_id);
        if (!JeproshopTools::isLoadedObject($cart, 'cart_id'))
            die(Tools::displayError());

        $with_taxes = $use_tax_display ? $cart->_taxCalculationMethod != COM_JEPROSHOP_TAX_EXCLUDED : true;
        return JeproshopTools::displayPrice($cart->getOrderTotal($with_taxes, $type), JeproshopCurrencyModelCurrency::getCurrencyInstance((int)$cart->currency_id), false);
    }

    /**
     * This function returns the total cart amount
     *
     * Possible values for $type:
     * JeproshopCartModelCart::ONLY_PRODUCTS
     * JeproshopCartModelCart::ONLY_DISCOUNTS
     * JeproshopCartModelCart::BOTH
     * JeproshopCartModelCart::BOTH_WITHOUT_SHIPPING
     * JeproshopCartModelCart::ONLY_SHIPPING
     * JeproshopCartModelCart::ONLY_WRAPPING
     * JeproshopCartModelCart::ONLY_PRODUCTS_WITHOUT_SHIPPING
     * JeproshopCartModelCart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING
     *
     * @param boolean $withTaxes With or without taxes
     * @param integer $type Total type
     * @param null $products
     * @param null $carrier_id
     * @param boolean $use_cache Allow using cache of the method CartRule::getContextualValue
     * @return float Order total
     */
    public function getOrderTotal($withTaxes = true, $type = JeproshopCartModelCart::BOTH, $products = null, $carrier_id = null, $use_cache = true)
    {
        if (!$this->cart_id){ return 0; }

        $type = (int)$type;
        $array_type = array(
            JeproshopCartModelCart::ONLY_PRODUCTS, JeproshopCartModelCart::ONLY_DISCOUNTS,
            JeproshopCartModelCart::BOTH, JeproshopCartModelCart::BOTH_WITHOUT_SHIPPING,
            JeproshopCartModelCart::ONLY_SHIPPING, JeproshopCartModelCart::ONLY_WRAPPING,
            JeproshopCartModelCart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            JeproshopCartModelCart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = JeproshopContext::getContext()->cloneContext();
        $virtual_context->cart = $this;

        if (!in_array($type, $array_type))
            die(Tools::displayError());

        $with_shipping = in_array($type, array(JeproshopCartModelCart::BOTH, JeproshopCartModelCart::ONLY_SHIPPING));

        // if cart rules are not used
        if ($type == JeproshopCartModelCart::ONLY_DISCOUNTS && !JeproshopCartRuleModelCartRule::isFeaturePublished())
            return 0;

        // no shipping cost if is a cart with only virtual products
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == JeproshopCartModelCart::ONLY_SHIPPING)
            return 0;

        if ($virtual && $type == JeproshopCartModelCart::BOTH)
            $type = JeproshopCartModelCart::BOTH_WITHOUT_SHIPPING;

        if ($with_shipping || $type == JeproshopCartModelCart::ONLY_DISCOUNTS){
            if (is_null($products) && is_null($carrier_id)) {
                $shipping_fees = $this->getTotalShippingCost(null, (boolean)$withTaxes);
            }else {
                $shipping_fees = $this->getPackageShippingCost($carrier_id, (bool)$withTaxes, null, $products);
            }
        }else {
            $shipping_fees = 0;
        }

        if ($type == JeproshopCartModelCart::ONLY_SHIPPING) {
            return $shipping_fees;
        }

        if ($type == JeproshopCartModelCart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = JeproshopCartModelCart::ONLY_PRODUCTS;
        }

        $param_product = true;
        if (is_null($products)){
            $param_product = false;
            $products = $this->getProducts();
        }

        if ($type == JeproshopCartModelCart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING){
            foreach ($products as $key => $product) {
                if ($product->is_virtual)
                    unset($products[$key]);
            }

            $type = JeproshopCartModelCart::ONLY_PRODUCTS;
        }

        $order_total = 0;
        if (JeproshopTaxModelTax::taxExcludedOption()) {
            $with_taxes = false;
        }

        foreach ($products as $product) {
            // products refer to the cart details
            if ($virtual_context->shop->shop_id != $product->shop_id) {
                $virtual_context->shop = new JeproshopShopModelShop((int)$product->shop_id);
            }
            if (JeproshopSettingModelSetting::getValue('PS_TAX_ADDRESS_TYPE') == 'address_invoice_id')
                $address_id = (int)$this->address_invoice_id;
            else
                $address_id = (int)$product->address_delivery_id; // Get delivery address of the product from the cart
            if (!JeproshopAddressModelAddress::addressExists($address_id))
                $address_id = null;

            $null = null;
            if ($this->_taxCalculationMethod == COM_JEPROSHOP_TAX_EXCLUDED)
            {
                // Here taxes are computed only once the quantity has been applied to the product price
                $price = JeproshopProductModelProduct::getStaticPrice(
                    (int)$product->product_id, false, (int)$product->product_attribute_id, 2, null, false, true, $product->cart_quantity, false,
                    (int)$this->customer_id ? (int)$this->customer_id : null, (int)$this->cart_id, $address_id, $null, true, true, $virtual_context
                );

                $total_ecotax = $product->ecotax * (int)$product->cart_quantity;
                $total_price = $price * (int)$product->cart_quantity;

                if ($withTaxes) {
                    $product_tax_rate = (float)JeproshopTaxModelTax::getProductTaxRate((int)$product->product_id, (int)$address_id, $virtual_context);
                    $product_eco_tax_rate = JeproshopTaxModelTax::getProductEcotaxRate((int)$address_id);

                    $total_price = ($total_price - $total_ecotax) * (1 + $product_tax_rate / 100);
                    $total_ecotax = $total_ecotax * (1 + $product_eco_tax_rate / 100);
                    $total_price = JeproshopTools::roundPrice($total_price + $total_ecotax, 2);
                }
            } else {
                if ($withTaxes)
                    $price = JeproshopProductModelProduct::getStaticPrice(
                        (int)$product->product_id, true, (int)$product->product_attribute_id, 2, null, false, true, $product->cart_quantity, false,
                        ((int)$this->customer_id ? (int)$this->customer_id : null),  (int)$this->cart_id, ((int)$address_id ? (int)$address_id : null),
                        $null, true, true, $virtual_context
                    );
                else
                    $price = JeproshopProductModelProduct::getStaticPrice(
                        (int)$product->product_id, false, (int)$product->product_attribute_id, 2, null, false, true, $product->cart_quantity, false,
                        ((int)$this->customer_id ? (int)$this->customer_id : null), (int)$this->cart_id, ((int)$address_id ? (int)$address_id : null),
                        $null,  true, true, $virtual_context
                    );

                $total_price = JeproshopTools::roundPrice($price * (int)$product->cart_quantity, 2);
            }
            $order_total += $total_price;
        }

        $order_total_products = $order_total;

        if ($type == JeproshopCartModelCart::ONLY_DISCOUNTS)
            $order_total = 0;

        // Wrapping Fees
        $wrapping_fees = 0;
        if ($this->gift)
            $wrapping_fees = JeproshopTools::convertPrice(JeproshopTools::roundPrice($this->getGiftWrappingPrice($withTaxes), 2), JeproshopCurrencyModelCurrency::getCurrencyInstance((int)$this->currency_id));
        if ($type == JeproshopCartModelCart::ONLY_WRAPPING)
            return $wrapping_fees;

        $order_total_discount = 0;
        if (!in_array($type, array(JeproshopCartModelCart::ONLY_SHIPPING, JeproshopCartModelCart::ONLY_PRODUCTS)) && JeproshopCartRuleModelCartRule::isFeaturePublished())
        {
            // First, retrieve the cart rules associated to this "getOrderTotal"
            if ($with_shipping || $type == JeproshopCartModelCart::ONLY_DISCOUNTS)
                $cart_rules = $this->getCartRules(JeproshopCartRuleModelCartRule::FILTER_ACTION_ALL);
            else
            {
                $cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
                // Cart Rules array are merged manually in order to avoid doubles
                foreach ($this->getCartRules(JeproshopCartRuleModelCartRule::FILTER_ACTION_GIFT) as $tmp_cart_rule){
                    $flag = false;
                    foreach ($cart_rules as $cart_rule)
                        if ($tmp_cart_rule->cart_rule_id == $cart_rule->cart_rule_id) {
                            $flag = true;
                        }
                    if (!$flag)
                        $cart_rules[] = $tmp_cart_rule;
                }
            }

            $id_address_delivery = 0;
            if (isset($products[0]))
                $id_address_delivery = (is_null($products) ? $this->address_delivery_id : $products[0]->address_delivery_id);
            $package = array('id_carrier' => $carrier_id, 'id_address' => $id_address_delivery, 'products' => $products);

            // Then, calculate the contextual value for each one
            foreach ($cart_rules as $cart_rule)
            {
                // If the cart rule offers free shipping, add the shipping cost
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping)
                    $order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_SHIPPING, ($param_product ? $package : null), $use_cache), 2);

                // If the cart rule is a free gift, then add the free gift value only if the gift is in this package
                if ((int)$cart_rule['obj']->gift_product)
                {
                    $in_order = false;
                    if (is_null($products))
                        $in_order = true;
                    else
                        foreach ($products as $product)
                            if ($cart_rule['obj']->gift_product == $product['id_product'] && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute'])
                                $in_order = true;

                    if ($in_order)
                        $order_total_discount += $cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_GIFT, $package, $use_cache);
                }

                // If the cart rule offers a reduction, the amount is prorated (with the products in the package)
                if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0)
                    $order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_REDUCTION, $package, $use_cache), 2);
            }
            $order_total_discount = min(JeproshopTools::roundPrice($order_total_discount, 2), $wrapping_fees + $order_total_products + $shipping_fees);
            $order_total -= $order_total_discount;
        }

        if ($type == JeproshopCartModelCart::BOTH) {
            $order_total += $shipping_fees + $wrapping_fees;
        }

        if ($order_total < 0 && $type != JeproshopCartModelCart::ONLY_DISCOUNTS) {
            return 0;
        }

        if ($type == JeproshopCartModelCart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }

        return JeproshopTools::roundPrice((float)$order_total, 2);
    }

    /**
     * Return cart products
     *
     * @result array Products
     * @param bool $refresh
     * @param bool $product_id
     * @param null $country_id
     * @return array|null
     */
    public function getProducts($refresh = false, $product_id = false, $country_id = null){
        if (!$this->cart_id)
            return array();
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->_products !== null && !$refresh){
            // Return product row with specified ID if it exists
            if (is_int($product_id)){
                foreach ($this->_products as $product)
                    if ($product->product_id == $product_id)
                        return array($product);
                return array();
            }
            return $this->_products;
        }

        $db = JFactory::getDBO();
        $select = $leftJoin = "";

        if (JeproshopCustomization::isFeaturePublished()) {
            $select .= ('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $leftJoin .= " LEFT JOIN " . $db->quoteName('#__jeproshop_customization') . " AS customization " ; /*
                'p.`id_product` = cu.`id_product` AND cart_product.`id_product_attribute` = cu.`id_product_attribute` AND cu.`id_cart` = '.(int)$this->id); */
        }
        else
            $select .= 'NULL AS customization_quantity, NULL AS id_customization';

        if (JeproshopCombinationModelCombination::isFeaturePublished()){
            $select .= '
				product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
				IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
				(p.`weight`+ pa.`weight`) weight_attribute,
				IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
				IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
				pai.`id_image` as pai_id_image, il.`legend` as pai_legend,
				IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity
			';

            /*$sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cart_product.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cart_product.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
            $sql->leftJoin('product_attribute_image', 'pai', 'pai.`id_product_attribute` = pa.`id_product_attribute`');
            $sql->leftJoin('image_lang', 'il', 'il.`id_image` = pai.`id_image` AND il.`id_lang` = '.(int)$this->id_lang); */
        }
        else
            $select .= (
                'p.`reference` AS reference, p.`ean13`,
				p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity'
            );

        $query = "SELECT cart_product." . $db->quoteName('product_attribute_id') . ", cart_product." . $db->quoteName('product_id') . ", cart_product.";
        $query .= $db->quoteName('quantity') . " AS cart_quantity, cart_product." . $db->quoteName('shop_id') . ", product_lang." .$db->quoteName('name');
        $query .= ", product." . $db->quoteName('is_virtual') . ", product_lang." . $db->quoteName('short_description') . ", product_lang." . $db->quoteName('available_now');
        $query .= ", product_lang." . $db->quoteName('available_later') . ", product_shop." . $db->quoteName('default_category_id') . ", product.";
        $query .= $db->quoteName('supplier_id') . ", product." . $db->quoteName('manufacturer_id') . ", product_shop." . $db->quoteName('on_sale') .", product_shop.";
        $query .= $db->quoteName('ecotax') . ", product_shop." . $db->quoteName('additional_shipping_cost') . ", product_shop." . $db->quoteName('available_for_order');
        $query .= ", product_shop." . $db->quoteName('price') . ", product_shop." . $db->quoteName('published') . ", product_shop." . $db->quoteName('unity');
        $query .= ", product_shop." . $db->quoteName('unit_price_ratio') . ", stock." . $db->quoteName('quantity') . " AS quantity_available, product." . $db->quoteName('width');;
        $query .= ", product." . $db->quoteName('height') . ", product." . $db->quoteName('depth') . ", stock." . $db->quoteName('out_of_stock') . ", product.";
        $query .= $db->quoteName('weight') . ", product." . $db->quoteName('date_add') . ", product." . $db->quoteName('date_upd') . ", IFNULL(stock.quantity, 0) as quantity, ";
        $query .= "product_lang." . $db->quoteName('link_rewrite') . ", category_lang." . $db->quoteName('link_rewrite') . " AS category, CONCAT(LPAD(cart_product.";
        $query .= $db->quoteName('product_id') . ", 10, 0), LPAD(IFNULL(cart_product." . $db->quoteName('product_attribute_id') . ", 0), 10, 0), IFNULL(cart_product.";
        $query .= $db->quoteName('address_delivery_id') . ", 0)) AS unique_id, cart_product.address_delivery_id, product_shop." . $db->quoteName('wholesale_price');
        $query .= ", product_shop.advanced_stock_management, product_supplier.product_supplier_reference supplier_reference, IFNULL(specific_price." . $db->quoteName('reduction_type');
        $query .= ", 0) AS reduction_type FROM " . $db->quoteName('#__jeproshop_cart_product') . " AS cart_product LEFT JOIN " . $db->quoteName('#__jeproshop_product') . " AS product ";
        $query .= " ON (product." . $db->quoteName('product_id') . " = cart_product." . $db->quoteName('product_id') . ") INNER JOIN " . $db->quoteName('#__jeproshop_product_shop') ;
        $query .= " AS product_shop ON (product_shop." . $db->quoteName('shop_id') . " = cart_product." . $db->quoteName('shop_id') . " AND product_shop." . $db->quoteName('product_id');
        $query .= " = product." . $db->quoteName('product_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON (product." . $db->quoteName('product_id');
        $query .= " = product_lang." . $db->quoteName('product_id') . "	AND product_lang." . $db->quoteName('lang_id') . " = " . (int)$this->lang_id ;
        $query .= JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang', 'cart_product.shop_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_category_lang');
        $query .= " AS category_lang ON(product_shop." . $db->quoteName('default_category_id') . " = category_lang." . $db->quoteName('category_id') . " AND category_lang." . $db->quoteName('lang_id');
        $query .= " = " . (int)$this->lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('category_lang', 'cart_product.shop_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_product_supplier');
        $query .= " AS product_supplier ON (product_supplier." . $db->quoteName('product_id') . " = cart_product." . $db->quoteName('product_id') . " AND product_supplier.";
        $query .= $db->quoteName('product_attribute_id') . " = cart_product." . $db->quoteName('product_attribute_id') . " AND product_supplier." . $db->quoteName('supplier_id') ;
        $query .= " = product." . $db->quoteName('supplier_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_specific_price') . " AS specific_price ON (specific_price.";
        $query .= $db->quoteName('product_id') . " = cart_product." . $db->quoteName('product_id') . ") " . JeproshopProductModelProduct::sqlStock('cart_product'); // AND 'sp.`id_shop` = cart_product.`id_shop`

        // @todo test if everything is ok, then refactorise call of this method
        //$sql->join(Product::sqlStock('cart_product', 'cart_product'));

        $query .= " WHERE cart_product." . $db->quoteName('cart_id') . " = " .(int)$this->cart_id;
        if ($product_id)
            $query .= " AND cart_product." . $db->quoteName('product_id') . " = " .(int)$product_id;
        $query .= " AND product." . $db->quoteName('product_id') . " IS NOT NULL GROUP BY unique_id ORDER BY cart_product." . $db->quoteName('date_add');
        $query .= ", product." . $db->quoteName('product_id') . ", cart_product." . $db->quoteName('product_attribute_id') . " ASC";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = array();
        $product_attribute_ids = array();
        if ($result)
            foreach ($result as $row){
                $products_ids[] = $row->product_id;
                $product_attribute_ids[] = $row->product_attribute_id;
            }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        JeproshopProductModelProduct::cacheProductsFeatures($products_ids);
        JeproshopCartModelCart::cacheSomeAttributesLists($product_attribute_ids, $this->lang_id);

        $this->_products = array();
        if (empty($result))
            return array();

        $cart_shop_context = JeproshopContext::getContext()->cloneContext();
        foreach ($result as &$row){
            if (isset($row->ecotax_attr) && $row->ecotax_attr > 0){
                $row->ecotax = (float)$row->ecotax_attr;
            }
            $row->stock_quantity = (int)$row->quantity;
            // for compatibility with 1.2 themes
            $row->quantity = (int)$row->cart_quantity;

            if (isset($row->product_attribute_id) && (int)$row->product_attribute_id && isset($row->weight_attribute)) {
                $row->weight = (float)$row->weight_attribute;
            }

            if (JeproshopSettingModelSetting::getValue('tax_address_type') == 'address_invoice_id')
                $address_id = (int)$this->address_invoice_id;
            else
                $address_id = (int)$row->address_delivery_id;
            if (!JeproshopAddressModelAddress::addressExists($address_id))
                $address_id = null;

            if ($cart_shop_context->shop->shop_id != $row->shop_id)
                $cart_shop_context->shop = new JeproshopShopModelShop((int)$row->shop_id);

            $specific_price_output = null;
            $null = null;

            if ($this->_taxCalculationMethod == COM_JEPROSHOP_TAX_EXCLUDED)
            {
                $row->price = JeproshopProductModelProduct::getStaticPrice(
                    (int)$row->product_id, false, isset($row->product_attribute_id) ? (int)$row->product_attribute_id : null, 2, null, false, true,
                    (int)$row->cart_quantity, false, ((int)$this->customer_id ? (int)$this->customer_id : null), (int)$this->cart_id,
                    ((int)$address_id ? (int)$address_id : null), $specific_price_output, true, true, $cart_shop_context
                ); // Here taxes are computed only once the quantity has been applied to the product price

                $row->price_wt = JeproshopProductModelProduct::getStaticPrice(
                    (int)$row->product_id, true, isset($row->product_attribute_id) ? (int)$row->product_attribute_id : null, 2, null, false, true,
                    (int)$row->cart_quantity, false, ((int)$this->customer_id ? (int)$this->customer_id : null), (int)$this->cart_id,
                    ((int)$address_id ? (int)$address_id : null), $null, true, true, $cart_shop_context
                );

                $tax_rate = JeproshopTaxModelTax::getProductTaxRate((int)$row->product_id, (int)$address_id);

                $row->total_wt = JeproshopTools::roundPrice($row->price * (float)$row->cart_quantity * (1 + (float)$tax_rate / 100), 2);
                $row->total = $row->price * (int)$row->cart_quantity;
            } else {
                $row->price = JeproshopProductModelProduct::getStaticPrice(
                    (int)$row->product_id, false, (int)$row->product_attribute_id, 2, null, false, true, $row->cart_quantity, false,
                    ((int)$this->customer_id ? (int)$this->customer_id : null), (int)$this->cart_id, ((int)$address_id ? (int)$address_id : null),
                    $specific_price_output, true, true, $cart_shop_context
                );

                $row->price_wt = JeproshopProductModelProduct::getStaticPrice(
                    (int)$row->product_id, true,  (int)$row->product_attribute_id, 2, null,  false, true, $row->cart_quantity,  false,
                    ((int)$this->customer_id ? (int)$this->customer_id : null), (int)$this->cart_id, ((int)$address_id ? (int)$address_id : null),
                    $null, true,  true, $cart_shop_context
                );

                // In case when you use QuantityDiscount, getPriceStatic() can be return more of 2 decimals
                $row->price_wt = JeproshopTools::roundPrice($row->price_wt, 2);
                $row->total_wt = $row->price_wt * (int)$row->cart_quantity;
                $row->total = JeproshopTools::roundPrice($row->price * (int)$row->cart_quantity, 2);
                $row->description_short = JeproshopTools::nl2br($row->short_description);
            }

            if (!isset($row->product_attribute_id_image_id) || $row->product_attribute_id_image_id == 0){
                $cache_id = 'jeproshop_cart_get_products_product_attribute_id_image_id_'.(int)$row->product_id .'_'.(int)$this->lang_id.'_'.(int)$row->shop_id;
                if (!JeproshopCache::isStored($cache_id)) {
                    $db = JFactory::getDBO();

                    $query = "SELECT image_shop." . $db->quoteName('image_id') . " AS image_id, image_lang." . $db->quoteName('legend') .  " FROM ";
                    $query .= $db->quoteName('#__jeproshop_image') . " AS image JOIN " . $db->quoteName('#__jeproshop_image_shop') . " AS image_shop ON (";
                    $query .= " image.image_id = image_shop.image_id AND image_shop.cover=1 AND image_shop.shop_id = " .(int)$row->shop_id . ") LEFT JOIN ";
                    $query .= $db->quoteName('#__jeproshop_image_lang') . " AS image_lang ON (image_shop." . $db->quoteName('image_id') . " = image_lang.";
                    $query .= $db->quoteName('image_id') . " AND image_lang." . $db->quoteName('lang_id') . " = " .(int)$this->lang_id . ") WHERE image.";
                    $query .= $db->quoteName('product_id') . " = " .(int)$row->product_id . " AND image_shop." . $db->quoteName('cover') . " = 1";

                    $db->setQuery($query);
                    $row2 = $db->loadObject();
                    JeproshopCache::store($cache_id, $row2);
                }
                $row2 = JeproshopCache::retrieve($cache_id);
                if (!$row2) {
                    $row2 = new JObject();
                    $row2->image_id = false;
                    $row2->legend = false;
                }else
                    $row = array_merge($row, $row2);
            } else {
                $row->image_id = $row->product_attribute_id_image_id;
                $row->legend = $row->product_attribute_legend;
            }

            $row->reduction_applies = ($specific_price_output && (float)$specific_price_output->reduction);
            $row->quantity_discount_applies = ($specific_price_output && $row->cart_quantity >= (int)$specific_price_output->from_quantity);
            $row->image_id = JeproshopProductModelProduct::defineProductImage($row, $this->lang_id);
            $row->allow_out_of_sp = JeproshopProductModelProduct::isAvailableWhenOutOfStock($row->out_of_stock);
            $row->features = JeproshopProductModelProduct::getStaticFeatures((int)$row->product_id);

            if (array_key_exists($row->product_attribute_id .'_'.$this->lang_id, self::$_attributesLists)) {
                $row = array_merge($row, self::$_attributesLists[$row->product_attribute_id . '_' . $this->lang_id]);
            }
            $row = JeproshopProductModelProduct::getTaxesInformations($row, $cart_shop_context);

            $this->_products[] = $row;
        }

        return $this->_products;
    }



    public static function cacheSomeAttributesLists($product_attribute_list, $lang_id) {
        if (!JeproshopCombinationModelCombination::isFeaturePublished()){ return; }

        $product_attribute_implode = array();

        foreach ($product_attribute_list as $product_attribute_id)
            if ((int)$product_attribute_id && !array_key_exists($product_attribute_id.'_'.$lang_id, self::$_attributesLists)){
                $product_attribute_implode[] = (int)$product_attribute_id;
                $attribute = new JObject();
                $attribute->attributes = '';
                $attribute->attributes_small = '';
                self::$_attributesLists[(int)$product_attribute_id.'_'.$lang_id] = $attribute;
            }

        if (!count($product_attribute_implode))   return;

        $db = JFactory::getDBO();

        $query = "SELECT product_attribute_combination." . $db->quoteName('product_attribute') . ", attribute_group_lang." . $db->quoteName('public_name');
        $query .= " AS public_group_name, attribute_lang." . $db->quoteName('name') . " AS attribute_name FROM " . $db->quoteName('#__jeproshop_product_attribute_combination');
        $query .= " AS product_attribute_combination LEFT JOIN " . $db->quoteName('#__jeproshop_attribute') . " AS attribute ON (attribute." . $db->quoteName('attribute_id');
        $query .= " = product_attribute_combination." . $db->quoteName('attribute_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group ";
        $query .= " ON attribute_group." . $db->quoteName('attribute_group_id') . " = attribute." . $db->quoteName('attribute_group_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_lang');
        $query .= " AS attribute_lang ON ( attribute." . $db->quoteName('attribute_id') . " = attribute_lang." . $db->quoteName('attribute_id') . " AND attribute_lang.";
        $query .= $db->quoteName('lang_id') . " = " .(int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang') . " AS attribute_group_lang ON ( ";
        $query .= "attribute_group." . $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id') . " AND attribute_group_lang.";
        $query .= $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE product_attribute_combination." . $db->quoteName('product_attribute_id') . " IN (" ;
        $query .= implode(',', $product_attribute_implode) . ") ORDER BY attribute_group_lang." . $db->quoteName('public_name') . " ASC ";

        $db->setQuery($query);

        $result = $db->loadObjectList();

        foreach ($result as $row) {
            self::$_attributesLists[$row->product_attribute_id .'_'.$lang_id]->attributes .= $row->public_group_name .' : '.$row->attribute_name.', ';
            self::$_attributesLists[$row->product_attribute_id .'_'.$lang_id]->attributes_small .= $row->attribute_name . ', ';
        }

        foreach ($product_attribute_implode as $product_attribute_id) {
            self::$_attributesLists[$product_attribute_id.'-'.$lang_id]->attributes = rtrim(
                self::$_attributesLists[$product_attribute_id.'_'.$lang_id]->attributes,
                ', '
            );

            self::$_attributesLists[$product_attribute_id.'-'.$lang_id]->attributes_small = rtrim(
                self::$_attributesLists[$product_attribute_id.'_'.$lang_id]->attributes_small,
                ', '
            );
        }
    }

    /**
     * Check if cart contains only virtual products
     * @return bool true if is a virtual cart or false
     */
    public function isVirtualCart(){
        if (!JeproshopProductDownloadModelProductDownload::isFeaturePublished()){ return false; }

        if (!isset(self::$_isVirtualCart[$this->cart_id])){
            $products = $this->getProducts();
            if (!count($products))
                return false;

            $is_virtual = 1;
            foreach ($products as $product){
                if (empty($product->is_virtual))
                    $is_virtual = 0;
            }
            self::$_isVirtualCart[$this->cart_id] = (int)$is_virtual;
        }

        return self::$_isVirtualCart[$this->cart_id];
    }


    public static function replaceZeroByShopName($echo){
        return ($echo == '0' ? JeproshopSettingModelSetting::getValue('shop_name') : $echo);
    }

    public function getCartList(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $context = JeproshopContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitStart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'address_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC' , 'string');

        $use_limit = true;
        if($limit === false){
            $use_limit = false;
        }

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS cart." . $db->quoteName('cart_id') . " AS total, cart." . $db->quoteName('cart_id') . ", cart." . $db->quoteName('date_add') . " AS date_add, CONCAT(LEFT(customer.";
            $query .= $db->quoteName('firstname') . ", 1), '. ', customer." . $db->quoteName('lastname') . ") AS customer_name, carrier." . $db->quoteName('name');
            $query .= " AS carrier_name, IF (IFNULL(ord.order_id, '" . JText::_('COM_JEPROSHOP_NOT_ORDERED_LABEL') . "') = '" . JText::_('COM_JEPROSHOP_NOT_ORDERED_LABEL');
            $query .= "', IF(TIME_TO_SEC(TIMEDIFF(NOW(), cart." . $db->quoteName('date_add') . ")) > 86400, '" . JText::_('COM_JEPROSHOP_ABANDONED_CART_LABEL') . "', '";
            $query .= JText::_('COM_JEPROSHOP_NOT_ORDERED_LABEL') . "'), ord." . $db->quoteName('order_id') . ") AS order_id, IF(ord." . $db->quoteName('order_id') . ", 1";
            $query .= ", 0) AS badge_success, IF(ord." . $db->quoteName('order_id') . ", 0, 1) badge_danger, IF(connection." . $db->quoteName('guest_id') . ", 1, 0) AS guest_id ";
            $query .= " FROM " . $db->quoteName('#__jeproshop_cart') . " AS cart LEFT JOIN " . $db->quoteName('#__jeproshop_customer') . " AS customer ON (customer.";
            $query .= $db->quoteName('customer_id') . " = cart." . $db->quoteName('customer_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_currency') . " AS currency";
            $query .= " ON (currency." . $db->quoteName('currency_id') . " = cart." . $db->quoteName('currency_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_carrier');
            $query .= " AS carrier ON(carrier." . $db->quoteName('carrier_id') . " = cart." . $db->quoteName('carrier_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_orders');
            $query .= " AS ord ON (ord." . $db->quoteName('cart_id') . " = cart." . $db->quoteName('cart_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_connection');
            $query .= " AS connection ON (cart." . $db->quoteName('guest_id') . " = connection." . $db->quoteName('guest_id') . " AND TIME_TO_SEC(TIMEDIFF(NOW(), connection.";
            $query .= $db->quoteName('date_add') . ")) < 1800)";

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit == true) ? " LIMIT " . (int)$limitStart . ", " . (int)$limit : " ");

            $db->setQuery($query);
            $carts = $db->loadObjectList();

            if($use_limit == true){
                $limitStart = (int)$limitStart -(int)$limit;
                if($limitStart < 0){ break; }
            }else{ break; }
        }while(empty($carts));

        $this->pagination = new JPagination($total, $limitStart, $limit);
        return $carts;
    }

	public function isMultiShop(){
		return (JeproshopShopModelShop::isTableAssociated('cart') || !empty($this->multiLangShop));
	}

    /**
     * Return useful informations for cart
     *
     * @param null $lang_id
     * @param bool $refresh
     * @return array Cart details
     */
    public function getSummaryDetails($lang_id = null, $refresh = false){
        $context = JeproshopContext::getContext();
        $app = JFactory::getApplication();
        if (!$lang_id)
            $lang_id = $context->language->lang_id;

        $delivery = new JeproshopAddressModelAddress((int)$this->address_delivery_id);
        $invoice = new JeproshopAddressModelAddress((int)$this->address_invoice_id);

        // New layout system with personalization fields
        $formatted_addresses = array(
            'delivery' => JeproshopAddressFormatModelAddressFormat::getFormattedLayoutData($delivery),
            'invoice' => JeproshopAddressFormatModelAddressFormat::getFormattedLayoutData($invoice)
        );

        $base_total_tax_inc = $this->getOrderTotal(true);
        $base_total_tax_exc = $this->getOrderTotal(false);

        $total_tax = $base_total_tax_inc - $base_total_tax_exc;

        if ($total_tax < 0)
            $total_tax = 0;

        $currency = new JeproshopCurrencyModelCurrency($this->currency_id);

        $products = $this->getProducts($refresh);
        $gift_products = array();
        $cart_rules = $this->getCartRules();
        $total_shipping = $this->getTotalShippingCost();
        $total_shipping_tax_exc = $this->getTotalShippingCost(null, false);
        $total_products_wt = $this->getOrderTotal(true, JeproshopCartModelCart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, JeproshopCartModelCart::ONLY_PRODUCTS);
        $total_discounts = $this->getOrderTotal(true, JeproshopCartModelCart::ONLY_DISCOUNTS);
        $total_discounts_tax_exc = $this->getOrderTotal(false, JeproshopCartModelCart::ONLY_DISCOUNTS);

        // The cart content is altered for display
        foreach ($cart_rules as &$cart_rule){
            // If the cart rule is automatic (without any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
            if ($cart_rule->free_shipping && (empty($cart_rule->code) || preg_match('/^'. JeproshopCartRuleModelCartRule::JEPROSHOP_BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule->code))){
                $cart_rule->value_real -= $total_shipping;
                $cart_rule->value_tax_exc -= $total_shipping_tax_exc;
                $cart_rule->value_real = JeproshopValidator::roundPrice($cart_rule->value_real, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                $cart_rule->value_tax_exc = JeproshopValidator::roundPrice($cart_rule->value_tax_exc, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                if ($total_discounts > $cart_rule->value_real)
                    $total_discounts -= $total_shipping;
                if ($total_discounts_tax_exc > $cart_rule->value_tax_exc)
                    $total_discounts_tax_exc -= $total_shipping_tax_exc;

                // Update total shipping
                $total_shipping = 0;
                $total_shipping_tax_exc = 0;
            }

            if ($cart_rule->gift_product) {
                foreach ($products as $key => &$product) {
                    if (empty($product->gift) && $product->product_id == $cart_rule->gift_product && $product->product_attribute_id == $cart_rule->gift_product_attribute) {
                        // Update total products
                        $total_products_wt = JeproshopValidator::roundPrice($total_products_wt - $product->price_wt, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                        $total_products = JeproshopValidator::roundPrice($total_products - $product->price, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);

                        // Update total discounts
                        $total_discounts = JeproshopValidator::roundPrice($total_discounts - $product->price_wt, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                        $total_discounts_tax_exc = JeproshopValidator::roundPrice($total_discounts_tax_exc - $product->price, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);

                        // Update cart rule value
                        $cart_rule->value_real = JeproshopValidator::roundPrice($cart_rule->value_real - $product->price_wt, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                        $cart_rule->value_tax_exc = JeproshopValidator::roundPrice($cart_rule->value_tax_exc - $product->price, (int)$context->currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);

                        // Update product quantity
                        $product->total_wt = JeproshopValidator::roundPrice($product->total_wt - $product->price_wt, (int)$currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                        $product->total = JeproshopValidator::roundPrice($product->total - $product->price, (int)$currency->decimals * COM_JEPROSHOP_PRICE_DISPLAY_PRECISION);
                        $product->cart_quantity--;

                        if (!$product->cart_quantity)
                            unset($products[$key]);

                        // Add a new product line
                        $gift_product = $product;
                        $gift_product->cart_quantity = 1;
                        $gift_product->price = 0;
                        $gift_product->price_wt = 0;
                        $gift_product->total_wt = 0;
                        $gift_product->total = 0;
                        $gift_product->gift = true;
                        $gift_products[] = $gift_product;

                        break; // One gift product per cart rule
                    }
                }
            }
        }

        foreach ($cart_rules as $key => &$cart_rule)
            if ($cart_rule->value_real == 0)
                unset($cart_rules[$key]);

        $summary = new JObject();

        $summary->set('delivery', $delivery);
        $summary->set('delivery_state', JeproshopStateModelState::getNameById($delivery->state_id));
        $summary->set('invoice', $invoice);
        $summary->set('invoice_state', JeproshopStateModelState::getNameById($invoice->state_id));
        $summary->set('formattedAddresses', $formatted_addresses);
        $summary->set('products', array_values($products));
        $summary->set('gift_products', $gift_products);
        $summary->set('discounts', array_values($cart_rules));
        $summary->set('is_virtual_cart', (int)$this->isVirtualCart());
        $summary->set('total_discounts', $total_discounts);
        $summary->set('total_discounts_tax_exc', $total_discounts_tax_exc);
        $summary->set('total_wrapping', $this->getOrderTotal(true, JeproshopCartModelCart::ONLY_WRAPPING));
        $summary->set('total_wrapping_tax_exc', $this->getOrderTotal(false, JeproshopCartModelCart::ONLY_WRAPPING));
        $summary->set('total_shipping', $total_shipping);
        $summary->set('total_shipping_tax_exc', $total_shipping_tax_exc);
        $summary->set('total_products_wt', $total_products_wt);
        $summary->set('total_products', $total_products);
        $summary->set('total_price', $base_total_tax_inc);
        $summary->set('total_tax', $total_tax);
        $summary->set('total_price_without_tax', $base_total_tax_exc);
        $summary->set('is_multi_address_delivery', $this->isMultiAddressDelivery() || ((int)$app->input->get('multi-shipping') == 1));
        $summary->set('free_ship', $total_shipping ? 0 : 1);
        $summary->set('carrier', new JeproshopCarrierModelCarrier($this->carrier_id, $lang_id));

        return $summary;
    }

    /**
     * Return shipping total for the cart
     *
     * @param array $delivery_option Array of the delivery option for each address
     * @param bool $use_tax
     * @param JeproshopCountryModelCountry $default_country
     * @return float Shipping total
     */
    public function getTotalShippingCost($delivery_option = null, $use_tax = true, JeproshopCountryModelCountry $default_country = null){
        if(isset(JeproshopContext::getContext()->cookie->country_id)){
            $default_country = new JeproshopCountryModelCountry(JeproshopContext::getContext()->cookie->country_id);
        }
        if (is_null($delivery_option)){
            $delivery_option = $this->getDeliveryOption($default_country, false, false);
        }
        $total_shipping = 0;
        $delivery_option_list = $this->getDeliveryOptionList($default_country);
        foreach ($delivery_option as $address_id => $key){
            if (!isset($delivery_option_list[$address_id]) || !isset($delivery_option_list[$address_id][$key]))
                continue;
            if ($use_tax)
                $total_shipping += $delivery_option_list[$address_id][$key]->total_price_with_tax;
            else
                $total_shipping += $delivery_option_list[$address_id][$key]->total_price_without_tax;
        }

        return $total_shipping;
    }

    public function getDeliveryOptionList(JeproshopCountryModelCountry $default_country = null, $flush = false){
        static $cache = null;
        if ($cache !== null && !$flush)
            return $cache;

        $delivery_option_list = array();
        $carriers_price = array();
        $carrier_collection = array();
        $package_list = $this->getPackageList();

        // Foreach addresses
        foreach ($package_list as $address_id => $packages) {
            // Initialize vars
            $delivery_option_list[$address_id] = array();
            $carriers_price[$address_id] = array();
            $common_carriers = null;
            $best_price_carriers = array();
            $best_grade_carriers = array();
            $carriers_instance = array();

            // Get country
            if ($address_id){
                $address = new JeproshopAddressModelAddress($address_id);
                $country = new JeproshopCountryModelCountry($address->country_id);
            }
            else
                $country = $default_country;

            // Foreach packages, get the carriers with best price, best position and best grade
            foreach ($packages as $package_id => $package){
                // No carriers available
                if (count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0){
                    $cache = array();
                    return $cache;
                }

                $carriers_price[$address_id][$package_id] = array();

                // Get all common carriers for each packages to the same address
                if (is_null($common_carriers))
                    $common_carriers = $package['carrier_list'];
                else
                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);

                $best_price = null;
                $best_price_carrier = null;
                $best_grade = null;
                $best_grade_carrier = null;

                // Foreach carriers of the package, calculate his price, check if it the best price, position and grade
                foreach ($package['carrier_list'] as $carrier_id){
                    if (!isset($carriers_instance[$carrier_id]))
                        $carriers_instance[$carrier_id] = new JeproshopCarrierModelCarrier($carrier_id);

                    $price_with_tax = $this->getPackageShippingCost($carrier_id, true, $country, $package['product_list']);
                    $price_without_tax = $this->getPackageShippingCost($carrier_id, false, $country, $package['product_list']);
                    if (is_null($best_price) || $price_with_tax < $best_price){
                        $best_price = $price_with_tax;
                        $best_price_carrier = $carrier_id;
                    }
                    $carriers_price[$address_id][$package_id][$carrier_id] = array(
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax);

                    $grade = $carriers_instance[$carrier_id]->grade;
                    if (is_null($best_grade) || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $carrier_id;
                    }
                }

                $best_price_carriers[$package_id] = $best_price_carrier;
                $best_grade_carriers[$package_id] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = array();
            $key = '';

            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $package_id => $carrier_id){
                $key .= $carrier_id . ',';
                if (!isset($best_price_carrier[$carrier_id]))
                    $best_price_carrier[$carrier_id] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                $best_price_carrier[$carrier_id]['price_with_tax'] += $carriers_price[$address_id][$package_id][$carrier_id]['with_tax'];
                $best_price_carrier[$carrier_id]['price_without_tax'] += $carriers_price[$address_id][$package_id][$carrier_id]['without_tax'];
                $best_price_carrier[$carrier_id]['package_list'][] = $package_id;
                $best_price_carrier[$carrier_id]['product_list'] = array_merge($best_price_carrier[$carrier_id]['product_list'], $packages[$package_id]['product_list']);
                $best_price_carrier[$carrier_id]['instance'] = $carriers_instance[$carrier_id];
            }

            // Add the delivery option with best price as best price
            $delivery_option_list[$address_id][$key] = array(
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1)
            );

            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = array();
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $package_id => $carrier_id) {
                $key .= $carrier_id.',';
                if (!isset($best_grade_carrier[$carrier_id]))
                    $best_grade_carrier[$carrier_id] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                $best_grade_carrier[$carrier_id]['price_with_tax'] += $carriers_price[$address_id][$package_id][$carrier_id]['with_tax'];
                $best_grade_carrier[$carrier_id]['price_without_tax'] += $carriers_price[$address_id][$package_id][$carrier_id]['without_tax'];
                $best_grade_carrier[$carrier_id]['package_list'][] = $package_id;
                $best_grade_carrier[$carrier_id]['product_list'] = array_merge($best_grade_carrier[$carrier_id]['product_list'], $packages[$package_id]['product_list']);
                $best_grade_carrier[$carrier_id]['instance'] = $carriers_instance[$carrier_id];
            }

            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$address_id][$key]))
                $delivery_option_list[$address_id][$key] = array(
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1)
                );
            $delivery_option_list[$address_id][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $carrier_id){
                $key = '';
                $package_list = array();
                $product_list = array();
                $price_with_tax = 0;
                $price_without_tax = 0;

                foreach ($packages as $package_id => $package){
                    $key .= $carrier_id.',';
                    $price_with_tax += $carriers_price[$address_id][$package_id][$carrier_id]['with_tax'];
                    $price_without_tax += $carriers_price[$address_id][$package_id][$carrier_id]['without_tax'];
                    $package_list[] = $package_id;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$address_id][$key]))
                    $delivery_option_list[$address_id][$key] = array(
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => array(
                            $carrier_id => array(
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$carrier_id],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            )
                        )
                    );
                else
                    $delivery_option_list[$address_id][$key]['unique_carrier'] = (count($delivery_option_list[$address_id][$key]['carrier_list']) <= 1);
            }
        }

        $cart_rules = JeproshopCartRuleModelCartRule::getCustomerCartRules(JeproshopContext::getContext()->cookie->lang_id, JeproshopContext::getContext()->cookie->customer_id, true, true, false, $this);

        $free_carriers_rules = array();
        foreach ($cart_rules as $cart_rule){
            if ($cart_rule->free_shipping && $cart_rule->carrier_restriction){
                $cartRule = new JeproshopCartRuleModelCartRule((int)$cart_rule->cart_rule_id);
                if (JeproshopValidator::isLoadedObject($cartRule, 'cart_rule_id')){
                    $carriers = $cart_rule->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])){
                        foreach($carriers['selected'] as $carrier){
                            if (isset($carrier->carrier_id) && $carrier->carrier_id){
                                $free_carriers_rules[] = (int)$carrier->carrier_id;
                            }
                        }
                    }
                }
            }
        }

        // For each delivery options :
        //    - Set the carrier list
        //    - Calculate the price
        //    - Calculate the average position
        foreach ($delivery_option_list as $address_id => $delivery_option)
            foreach ($delivery_option as $key => $value)
            {
                $total_price_with_tax = 0;
                $total_price_without_tax = 0;
                $position = 0;
                foreach ($value['carrier_list'] as $carrier_id => $data)
                {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($carrier_id, $free_carriers_rules)) ? 0 : $total_price_without_tax ;

                    if (!isset($carrier_collection[$carrier_id]))
                        $carrier_collection[$carrier_id] = new Carrier($carrier_id);
                    $delivery_option_list[$address_id][$key]['carrier_list'][$carrier_id]['instance'] = $carrier_collection[$carrier_id];

                    if (file_exists(_PS_SHIP_IMG_DIR_.$carrier_id.'.jpg'))
                        $delivery_option_list[$address_id][$key]['carrier_list'][$carrier_id]['logo'] = _THEME_SHIP_DIR_.$carrier_id.'.jpg';
                    else
                        $delivery_option_list[$address_id][$key]['carrier_list'][$carrier_id]['logo'] = false;

                    $position += $carrier_collection[$carrier_id]->position;
                }
                $delivery_option_list[$address_id][$key]['total_price_with_tax'] = $total_price_with_tax;
                $delivery_option_list[$address_id][$key]['total_price_without_tax'] = $total_price_without_tax;
                $delivery_option_list[$address_id][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
                $delivery_option_list[$address_id][$key]['position'] = $position / count($value['carrier_list']);
            }

        // Sort delivery option list
        foreach ($delivery_option_list as &$array)
            uasort ($array, array('Cart', 'sortDeliveryOptionList'));

        $cache = $delivery_option_list;
        return $delivery_option_list;
    }


    /**
     * Get the delivery option selected, or if no delivery option was selected, the cheapest option for each address
     * @param null $default_country
     * @param bool $doNotAutoSelectOptions
     * @param bool $use_cache
     * @return array delivery option
     */
    public function getDeliveryOption($default_country = null, $doNotAutoSelectOptions = false, $use_cache = true){
        static $cache = array();
        $cache_id = (int)(is_object($default_country) ? $default_country->country_id : 0).'_'.(int)$doNotAutoSelectOptions;
        if (isset($cache[$cache_id]) && $use_cache){
            return $cache[$cache_id];
        }
        $delivery_option_list = $this->getDeliveryOptionList($default_country);

        // The delivery option was selected
        if (isset($this->delivery_option) && $this->delivery_option != '') {
            $delivery_option = Tools::unSerialize($this->delivery_option);
            $validated = true;
            foreach ($delivery_option as $address_id => $key) {
                if (!isset($delivery_option_list[$address_id][$key])) {
                    $validated = false;
                    break;
                }
            }

            if ($validated){
                $cache[$cache_id] = $delivery_option;
                return $delivery_option;
            }
        }

        if ($doNotAutoSelectOptions){ return false; }

        // No delivery option selected or delivery option selected is not valid, get the better for all options
        $delivery_option = array();
        foreach ($delivery_option_list as $address_id => $options)
        {
            foreach ($options as $key => $option) {
                if (JeproshopSettingModelSeting::getValue('default_carrier') == -1 && $option['is_best_price']) {
                    $delivery_option[$address_id] = $key;
                    break;
                } elseif (JeproshopSettingModelSeting::getValue('default_carrier') == -2 && $option['is_best_grade']) {
                    $delivery_option[$address_id] = $key;
                    break;
                } elseif ($option['unique_carrier'] && in_array(JeproshopSettingModelSeting::getValue('default_carrier'), array_keys($option['carrier_list']))) {
                    $delivery_option[$address_id] = $key;
                    break;
                }
            }

            reset($options);
            if (!isset($delivery_option[$address_id]))
                $delivery_option[$address_id] = key($options);
        }

        $cache[$cache_id] = $delivery_option;

        return $delivery_option;
    }

    public function getPackageList($flush = false){
        static $cache = array();
        if (isset($cache[(int)$this->cart_id.'_'.(int)$this->address_delivery_id]) && $cache[(int)$this->cart_id.'_'.(int)$this->address_delivery_id] !== false && !$flush)
            return $cache[(int)$this->cart_id.'_'.(int)$this->address_delivery_id];

        $product_list = $this->getProducts();
        // Step 1 : Get product informations (warehouse_list and carrier_list), count warehouse
        // Determine the best warehouse to determine the packages
        // For that we count the number of time we can use a warehouse for a specific delivery address
        $warehouse_count_by_address = array();
        $warehouse_carrier_list = array();

        $stock_management_active = JeproshopSettingModelSetting::getValue('advanced_stock_management');

        foreach ($product_list as &$product){
            if ((int)$product->address_delivery_id == 0){
                $product->address_delivery_id = (int)$this->address_delivery_id;
            }

            if (!isset($warehouse_count_by_address[$product->address_delivery_id])){
                $warehouse_count_by_address[$product->address_delivery_id] = array();
            }

            $product->warehouse_list = array();

            if ($stock_management_active &&
                ((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement((int)$product->product_id)))
            {
                $warehouse_list = Warehouse::getProductWarehouseList($product->product_id, $product->roduct_attribute_id, $this->shop_id);
                if (count($warehouse_list) == 0)
                    $warehouse_list = Warehouse::getProductWarehouseList($product->product_id, $product->roduct_attribute_id);
                // Does the product is in stock ?
                // If yes, get only warehouse where the product is in stock

                $warehouse_in_stock = array();
                $manager = StockManagerFactory::getManager();

                foreach ($warehouse_list as $key => $warehouse)
                {
                    $product_real_quantities = $manager->getProductRealQuantities(
                        $product->product_id,
                        $product->product_attribute_id,
                        array($warehouse->warehouse_id),
                        true
                    );

                    if ($product_real_quantities > 0 || Pack::isPack((int)$product->product_id))
                        $warehouse_in_stock[] = $warehouse;
                }

                if (!empty($warehouse_in_stock)){
                    $warehouse_list = $warehouse_in_stock;
                    $product->in_stock = true;
                }
                else
                    $product->in_stock = false;
            }
            else
            {
                //simulate default warehouse
                $warehouse_list = array(0);
                $product->in_stock = StockAvailable::getQuantityAvailableByProduct($product->product_id, $product->product_attribute_id) > 0;
            }

            foreach ($warehouse_list as $warehouse)
            {
                if (!isset($warehouse_carrier_list[$warehouse->warehouse_id]))
                {
                    $warehouse_object = new JeproshopWarehouseModelWarehouse($warehouse->warehouse_id);
                    $warehouse_carrier_list[$warehouse->warehouse_id] = $warehouse_object->getCarriers();
                }

                $product->warehouse_list[] = $warehouse->warehouse_id;
                if (!isset($warehouse_count_by_address[$product->address_delivery_id][$warehouse->warehouse_id]))
                    $warehouse_count_by_address[$product->address_delivery_id][$warehouse->warehouse_id] = 0;

                $warehouse_count_by_address[$product->address_delivery_id][$warehouse->warehouse_id]++;
            }
        }
        unset($product);

        arsort($warehouse_count_by_address);

        // Step 2 : Group product by warehouse
        $grouped_by_warehouse = array();
        foreach ($product_list as &$product)
        {
            if (!isset($grouped_by_warehouse[$product->address_delivery_id]))
                $grouped_by_warehouse[$product->address_delivery_id] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );

            $product->carrier_list = array();
            $warehouse_id = 0;
            foreach ($warehouse_count_by_address[$product->address_delivery_id] as $war_id => $val)
            {
                if (in_array((int)$war_id, $product->warehouse_list))
                {
                    $product->carrier_list = array_merge($product->carrier_list, Carrier::getAvailableCarrierList(new Product($product->product_id), $war_id, $product->address_delivery_id, null, $this));
                    if (!$warehouse_id)
                        $warehouse_id = (int)$war_id;
                }
            }

            if (!isset($grouped_by_warehouse[$product->address_delivery_id]['in_stock'][$warehouse_id])) {
                $grouped_by_warehouse[$product->address_delivery_id]['in_stock'][$warehouse_id] = array();
                $grouped_by_warehouse[$product->address_delivery_id]['out_of_stock'][$warehouse_id] = array();
            }

            if (!$this->allow_separated_package)
                $key = 'in_stock';
            else
                $key = $product->in_stock ? 'in_stock' : 'out_of_stock';

            if (empty($product->carrier_list))
                $product->carrier_list = array(0);

            $grouped_by_warehouse[$product->address_delivery_id][$key][$warehouse_id][] = $product;
        }
        unset($product);

        // Step 3 : grouped product from grouped_by_warehouse by available carriers
        $grouped_by_carriers = array();
        foreach ($grouped_by_warehouse as $address_delivery_id => $products_in_stock_list)
        {
            if (!isset($grouped_by_carriers[$address_delivery_id]))
                $grouped_by_carriers[$address_delivery_id] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );
            foreach ($products_in_stock_list as $key => $warehouse_list)
            {
                if (!isset($grouped_by_carriers[$address_delivery_id][$key]))
                    $grouped_by_carriers[$address_delivery_id][$key] = array();
                foreach ($warehouse_list as $warehouse_id => $product_list)
                {
                    if (!isset($grouped_by_carriers[$address_delivery_id][$key][$warehouse_id]))
                        $grouped_by_carriers[$address_delivery_id][$key][$warehouse_id] = array();
                    foreach ($product_list as $product)
                    {
                        $package_carriers_key = implode(',', $product->carrier_list);

                        if (!isset($grouped_by_carriers[$address_delivery_id][$key][$warehouse_id][$package_carriers_key]))
                            $grouped_by_carriers[$address_delivery_id][$key][$warehouse_id][$package_carriers_key] = array(
                                'product_list' => array(),
                                'carrier_list' => $product->carrier_list,
                                'warehouse_list' => $product->warehouse_list
                            );

                        $grouped_by_carriers[$address_delivery_id][$key][$warehouse_id][$package_carriers_key]['product_list'][] = $product;
                    }
                }
            }
        }

        $package_list = array();
        // Step 4 : merge product from grouped_by_carriers into $package to minimize the number of package
        foreach ($grouped_by_carriers as $address_delivery_id => $products_in_stock_list){
            if (!isset($package_list[$address_delivery_id]))
                $package_list[$address_delivery_id] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );

            foreach ($products_in_stock_list as $key => $warehouse_list)
            {
                if (!isset($package_list[$address_delivery_id][$key]))
                    $package_list[$address_delivery_id][$key] = array();
                // Count occurrence of each carriers to minimize the number of packages
                $carrier_count = array();
                foreach ($warehouse_list as $warehouse_id => $products_grouped_by_carriers)
                {
                    foreach ($products_grouped_by_carriers as $data)
                    {
                        foreach ($data['carrier_list'] as $carrier_id)
                        {
                            if (!isset($carrier_count[$carrier_id]))
                                $carrier_count[$carrier_id] = 0;
                            $carrier_count[$carrier_id]++;
                        }
                    }
                }
                arsort($carrier_count);
                foreach ($warehouse_list as $warehouse_id => $products_grouped_by_carriers)
                {
                    if (!isset($package_list[$address_delivery_id][$key][$warehouse_id]))
                        $package_list[$address_delivery_id][$key][$warehouse_id] = array();
                    foreach ($products_grouped_by_carriers as $data)
                    {
                        foreach ($carrier_count as $carrier_id => $rate)
                        {
                            if (in_array($carrier_id, $data['carrier_list']))
                            {
                                if (!isset($package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id]))
                                    $package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id] = array(
                                        'carrier_list' => $data['carrier_list'],
                                        'warehouse_list' => $data['warehouse_list'],
                                        'product_list' => array(),
                                    );
                                $package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id]['carrier_list'] =
                                    array_intersect($package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id]['carrier_list'], $data['carrier_list']);
                                $package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id]['product_list'] =
                                    array_merge($package_list[$address_delivery_id][$key][$warehouse_id][$carrier_id]['product_list'], $data['product_list']);

                                break;
                            }
                        }
                    }
                }
            }
        }

        // Step 5 : Reduce depth of $package_list
        $final_package_list = array();
        foreach ($package_list as $address_delivery_id => $products_in_stock_list){
            if (!isset($final_package_list[$address_delivery_id])){
                $final_package_list[$address_delivery_id] = array();
            }

            foreach ($products_in_stock_list as $key => $warehouse_list){
                foreach ($warehouse_list as $warehouse_id => $products_grouped_by_carriers){
                    foreach ($products_grouped_by_carriers as $data){
                        $final_package_list[$address_delivery_id][] = array(
                            'product_list' => $data['product_list'],
                            'carrier_list' => $data['carrier_list'],
                            'warehouse_list' => $data['warehouse_list'],
                            'warehouse_id' => $warehouse_id,
                        );
                    }
                }
            }
        }
        $cache[(int)$this->cart_id] = $final_package_list;
        return $final_package_list;
    }

    public function getCartRules($filter = JeproshopCartRuleModelCartRule::JEPROSHOP_FILTER_ACTION_ALL) {
        // If the cart has not been saved, then there can't be any cart rule applied
        if (!JeproshopCartRuleModelCartRule::isFeaturePublished() || !$this->cart_id){ return array(); }


        $cache_key = 'jeproshop_cart_getCartRules_'. $this->cart_id . '_' . $filter;
        if (!JeproshopCache::isStored($cache_key)){
            $db = JFactory::getDBO();

            $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_cart_cart_rule') . " AS cd LEFT JOIN " . $db->quoteName('#__jeproshop_cart_rule');
            $query .= " AS cart_rule ON cd." . $db->quoteName('cart_rule_id') . " = cart_rule." . $db->quoteName('cart_rule_id') . " LEFT JOIN ";
            $query .= $db->quoteName('#__jeproshop_cart_rule_lang') . " AS cart_rule_lang ON ( cd." . $db->quoteName('cart_rule_id') . " = cart_rule_lang.";
            $query .= $db->quoteName('cart_rule_id') . " AND cart_rule_lang.lang_id = " .(int)$this->lang_id . ") WHERE " . $db->quoteName('cart_id') . " = " .(int)$this->cart_id;
            $query .= ($filter == JeproshopCartRuleModelCartRule::JEPROSHOP_FILTER_ACTION_SHIPPING ? " AND free_shipping = 1" : ""). ($filter == JeproshopCartRuleModelCartRule::JEPROSHOP_FILTER_ACTION_GIFT ? " AND gift_product != 0" : "");
            $query .= ($filter == JeproshopCartRuleModelCartRule::JEPROSHOP_FILTER_ACTION_REDUCTION ? " AND (reduction_percent != 0 OR reduction_amount != 0)"  : "") . " ORDER by cart_rule.priority ASC ";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            JeproshopCache::store($cache_key, $result);
        }
        $result = JeproshopCache::retrieve($cache_key);

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = JeproshopContext::getContext()->cloneContext();
        $virtual_context->cart = $this;

        foreach ($result as &$row){
            $row->obj = new JeproshopCartRuleModelCartRule($row->cart_rule_id, (int)$this->lang_id);
            $row->value_real = $row->obj->getContextualValue(true, $virtual_context, $filter);
            $row->value_tax_exc = $row->obj->getContextualValue(false, $virtual_context, $filter);

            // Retro compatibility < 1.5.0.2
            $row->discount_id = $row->cart_rule_id;
            $row->description = $row->name;
        }

        return $result;
    }

    /**
     * Does the cart use multiple address
     * @return boolean
     */
    public function isMultiAddressDelivery(){
        static $cache = null;

        if (is_null($cache)) {
            $db = JFactory::getDBO();

            $query = "SELECT count(distinct address_delivery_id) FROM " . $db->quoteName('#__jeproshop_cart_product');
            $query .= " AS cart_product WHERE cart_product.cart_id = " . (int)$this->cart_id;

            $db->setQuery($query);
            $cache = (bool)($db->loadResult() > 1);
        }
        return $cache;
    }

    public function add($autodate = true, $null_values = false)
    {
        $return = parent::add($autodate);
        Hook::exec('actionCartSave');

        return $return;
    }

    public function update($null_values = false)
    {
        if (isset(self::$_nbProducts[$this->id]))
            unset(self::$_nbProducts[$this->id]);

        if (isset(self::$_totalWeight[$this->id]))
            unset(self::$_totalWeight[$this->id]);

        $this->_products = null;
        $return = parent::update();
        Hook::exec('actionCartSave');

        return $return;
    }

    /**
     * Update the address id of the cart
     *
     * @param int $id_address Current address id to change
     * @param int $id_address_new New address id
     */
    public function updateAddressId($id_address, $id_address_new)
    {
        $to_update = false;
        if (!isset($this->id_address_invoice) || $this->id_address_invoice == $id_address)
        {
            $to_update = true;
            $this->id_address_invoice = $id_address_new;
        }
        if (!isset($this->id_address_delivery) || $this->id_address_delivery == $id_address)
        {
            $to_update = true;
            $this->id_address_delivery = $id_address_new;
        }
        if ($to_update)
            $this->update();

        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
		SET `id_address_delivery` = '.(int)$id_address_new.'
		WHERE  `id_cart` = '.(int)$this->id.'
			AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
			SET `id_address_delivery` = '.(int)$id_address_new.'
			WHERE  `id_cart` = '.(int)$this->id.'
				AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);
    }

    public function delete()
    {
        if ($this->OrderExists()) //NOT delete a cart which is associated with an order
            return false;

        $uploaded_files = Db::getInstance()->executeS('
			SELECT cd.`value`
			FROM `'._DB_PREFIX_.'customized_data` cd
			INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
			WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$this->id
        );

        foreach ($uploaded_files as $must_unlink)
        {
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value'].'_small');
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value']);
        }

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customized_data`
			WHERE `id_customization` IN (
				SELECT `id_customization`
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_cart`='.(int)$this->id.'
			)'
        );

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$this->id
        );

        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart` = '.(int)$this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id))
            return false;

        return parent::delete();
    }

    public static function getTaxesAverageUsed($id_cart)
    {
        $cart = new Cart((int)$id_cart);
        if (!Validate::isLoadedObject($cart))
            die(Tools::displayError());

        if (!Configuration::get('PS_TAX'))
            return 0;

        $products = $cart->getProducts();
        $total_products_moy = 0;
        $ratio_tax = 0;

        if (!count($products))
            return 0;

        foreach ($products as $product) // products refer to the cart details
        {
            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
                $address_id = (int)$cart->id_address_invoice;
            else
                $address_id = (int)$product['id_address_delivery']; // Get delivery address of the product from the cart
            if (!Address::addressExists($address_id))
                $address_id = null;

            $total_products_moy += $product['total_wt'];
            $ratio_tax += $product['total_wt'] * Tax::getProductTaxRate(
                    (int)$product['id_product'],
                    (int)$address_id
                );
        }

        if ($total_products_moy > 0)
            return $ratio_tax / $total_products_moy;

        return 0;
    }

    /**
     * @deprecated 1.5.0, use Cart->getCartRules()
     */
    public function getDiscounts($lite = false, $refresh = false)
    {
        Tools::displayAsDeprecated();
        return $this->getCartRules();
    }


    public function getDiscountsCustomer($id_cart_rule)
    {
        if (!CartRule::isFeatureActive())
            return 0;
        $cache_id = 'Cart::getDiscountsCustomer_'.(int)$this->id.'-'.(int)$id_cart_rule;
        if (!Cache::isStored($cache_id))
        {
            $result = (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'cart_cart_rule`
				WHERE `id_cart_rule` = '.(int)$id_cart_rule.' AND `id_cart` = '.(int)$this->id);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }


    /**
     * @deprecated 1.5.0, use Cart->addCartRule()
     */
    public function addDiscount($id_cart_rule)
    {
        Tools::displayAsDeprecated();
        return $this->addCartRule($id_cart_rule);
    }

    public function addCartRule($id_cart_rule)
    {
        // You can't add a cart rule that does not exist
        $cartRule = new CartRule($id_cart_rule, Context::getContext()->language->id);

        if (!Validate::isLoadedObject($cartRule))
            return false;

        if (Db::getInstance()->getValue('SELECT id_cart_rule FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart_rule = '.(int)$id_cart_rule.' AND id_cart = '.(int)$this->id))
            return false;

        // Add the cart rule to the cart
        if (!Db::getInstance()->insert('cart_cart_rule', array(
            'id_cart_rule' => (int)$id_cart_rule,
            'id_cart' => (int)$this->id
        )))
            return false;

        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT);

        if ((int)$cartRule->gift_product)
            $this->updateQty(1, $cartRule->gift_product, $cartRule->gift_product_attribute, false, 'up', 0, null, false);

        return true;
    }

    public function containsProduct($id_product, $id_product_attribute = 0, $id_customization = 0, $id_address_delivery = 0)
    {
        $sql = 'SELECT cp.`quantity` FROM `'._DB_PREFIX_.'cart_product` cp';

        if ($id_customization)
            $sql .= '
				LEFT JOIN `'._DB_PREFIX_.'customization` c ON (
					c.`id_product` = cp.`id_product`
					AND c.`id_product_attribute` = cp.`id_product_attribute`
				)';

        $sql .= '
			WHERE cp.`id_product` = '.(int)$id_product.'
			AND cp.`id_product_attribute` = '.(int)$id_product_attribute.'
			AND cp.`id_cart` = '.(int)$this->id;
        if (Configuration::get('PS_ALLOW_MULTI_SHIPPING') && $this->isMultiAddressDelivery())
            $sql .= ' AND cp.`id_address_delivery` = '.(int)$id_address_delivery;

        if ($id_customization)
            $sql .= ' AND c.`id_customization` = '.(int)$id_customization;

        return Db::getInstance()->getRow($sql);
    }


    /*
    ** Customization management
    */
    protected function _updateCustomizationQuantity($quantity, $id_customization, $id_product, $id_product_attribute, $id_address_delivery, $operator = 'up')
    {
        // Link customization to product combination when it is first added to cart
        if (empty($id_customization))
        {
            $customization = $this->getProductCustomization($id_product, null, true);
            foreach ($customization as $field)
            {
                if ($field['quantity'] == 0)
                {
                    Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customization`
					SET `quantity` = '.(int)$quantity.',
						`id_product_attribute` = '.(int)$id_product_attribute.',
						`id_address_delivery` = '.(int)$id_address_delivery.',
						`in_cart` = 1
					WHERE `id_customization` = '.(int)$field['id_customization']);
                }
            }
        }

        /* Deletion */
        if (!empty($id_customization) && (int)$quantity < 1)
            return $this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute);

        /* Quantity update */
        if (!empty($id_customization))
        {
            $result = Db::getInstance()->getRow('SELECT `quantity` FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
            if ($result && Db::getInstance()->NumRows())
            {
                if ($operator == 'down' && (int)$result['quantity'] - (int)$quantity < 1)
                    return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);

                return Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customization`
					SET
						`quantity` = `quantity` '.($operator == 'up' ? '+ ' : '- ').(int)$quantity.',
						`id_address_delivery` = '.(int)$id_address_delivery.'
					WHERE `id_customization` = '.(int)$id_customization);
            }
            else
                Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customization`
					SET `id_address_delivery` = '.(int)$id_address_delivery.'
					WHERE `id_customization` = '.(int)$id_customization);
        }
        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update(true);
        return true;
    }

    /**
     * Add customization item to database
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $index
     * @param int $type
     * @param string $field
     * @param int $quantity
     * @return boolean success
     */
    public function _addCustomization($id_product, $id_product_attribute, $index, $type, $field, $quantity)
    {
        $exising_customization = Db::getInstance()->executeS('
			SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
			ON cu.`id_customization` = cd.`id_customization`
			WHERE cu.id_cart = '.(int)$this->id.'
			AND cu.id_product = '.(int)$id_product.'
			AND in_cart = 0'
        );

        if ($exising_customization)
        {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization)
            {
                if ($customization['type'] == $type && $customization['index'] == $index)
                {
                    Db::getInstance()->execute('
						DELETE FROM `'._DB_PREFIX_.'customized_data`
						WHERE id_customization = '.(int)$customization['id_customization'].'
						AND type = '.(int)$customization['type'].'
						AND `index` = '.(int)$customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE)
                    {
                        @unlink(_PS_UPLOAD_DIR_.$customization['value']);
                        @unlink(_PS_UPLOAD_DIR_.$customization['value'].'_small');
                    }
                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        }
        else
        {
            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
				VALUES ('.(int)$this->id.', '.(int)$id_product.', '.(int)$id_product_attribute.', '.(int)$quantity.')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }

        $query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
			VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.pSQL($field).'\')';

        if (!Db::getInstance()->execute($query))
            return false;
        return true;
    }

    /**
     * Check if order has already been placed
     *
     * @return boolean result
     */
    public function orderExists()
    {
        $cache_id = 'Cart::orderExists_'.(int)$this->id;
        if (!Cache::isStored($cache_id))
        {
            $result = (bool)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int)$this->id);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * @deprecated 1.5.0, use Cart->removeCartRule()
     */
    public function deleteDiscount($id_cart_rule)
    {
        Tools::displayAsDeprecated();
        return $this->removeCartRule($id_cart_rule);
    }

    public function removeCartRule($id_cart_rule)
    {
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT);

        $result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'cart_cart_rule`
		WHERE `id_cart_rule` = '.(int)$id_cart_rule.'
		AND `id_cart` = '.(int)$this->id.'
		LIMIT 1');

        $cart_rule = new CartRule($id_cart_rule, Configuration::get('PS_LANG_DEFAULT'));
        if ((int)$cart_rule->gift_product)
            $this->updateQty(1, $cart_rule->gift_product, $cart_rule->gift_product_attribute, null, 'down', 0, null, false);

        return $result;
    }

    /**
     * Delete a product from the cart
     *
     * @param integer $id_product Product ID
     * @param integer $id_product_attribute Attribute ID if needed
     * @param integer $id_customization Customization id
     * @return boolean result
     */
    public function deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0)
    {
        if (isset(self::$_nbProducts[$this->id]))
            unset(self::$_nbProducts[$this->id]);

        if (isset(self::$_totalWeight[$this->id]))
            unset(self::$_totalWeight[$this->id]);

        if ((int)$id_customization)
        {
            $product_total_quantity = (int)Db::getInstance()->getValue(
                'SELECT `quantity`
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)$id_product.'
				AND `id_cart` = '.(int)$this->id.'
				AND `id_product_attribute` = '.(int)$id_product_attribute);

            $customization_quantity = (int)Db::getInstance()->getValue('
			SELECT `quantity`
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$this->id.'
			AND `id_product` = '.(int)$id_product.'
			AND `id_product_attribute` = '.(int)$id_product_attribute.'
			'.((int)$id_address_delivery ? 'AND `id_address_delivery` = '.(int)$id_address_delivery : ''));

            if (!$this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute, (int)$id_address_delivery))
                return false;

            // refresh cache of self::_products
            $this->_products = $this->getProducts(true);
            return ($customization_quantity == $product_total_quantity && $this->deleteProduct((int)$id_product, (int)$id_product_attribute, null, (int)$id_address_delivery));
        }

        /* Get customization quantity */
        $result = Db::getInstance()->getRow('
			SELECT SUM(`quantity`) AS \'quantity\'
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$this->id.'
			AND `id_product` = '.(int)$id_product.'
			AND `id_product_attribute` = '.(int)$id_product_attribute);

        if ($result === false)
            return false;

        /* If the product still possesses customization it does not have to be deleted */
        if (Db::getInstance()->NumRows() && (int)$result['quantity'])
            return Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cart_product`
				SET `quantity` = '.(int)$result['quantity'].'
				WHERE `id_cart` = '.(int)$this->id.'
				AND `id_product` = '.(int)$id_product.
                ($id_product_attribute != null ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '')
            );

        /* Product deletion */
        $result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'cart_product`
		WHERE `id_product` = '.(int)$id_product.'
		'.(!is_null($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
		AND `id_cart` = '.(int)$this->id.'
		'.((int)$id_address_delivery ? 'AND `id_address_delivery` = '.(int)$id_address_delivery : ''));

        if ($result)
        {
            $return = $this->update(true);
            // refresh cache of self::_products
            $this->_products = $this->getProducts(true);
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();

            return $return;
        }

        return false;
    }

    /**
     * Delete a customization from the cart. If customization is a Picture,
     * then the image is also deleted
     *
     * @param integer $id_customization
     * @return boolean result
     */
    protected function deleteCustomization($id_customization, $id_product, $id_product_attribute, $id_address_delivery = 0)
    {
        $result = true;
        $customization = Db::getInstance()->getRow('SELECT *
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_customization` = '.(int)$id_customization);

        if ($customization)
        {
            $cust_data = Db::getInstance()->getRow('SELECT *
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)$id_customization);

            // Delete customization picture if necessary
            if (isset($cust_data['type']) && $cust_data['type'] == 0)
                $result &= (@unlink(_PS_UPLOAD_DIR_.$cust_data['value']) && @unlink(_PS_UPLOAD_DIR_.$cust_data['value'].'_small'));

            $result &= Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)$id_customization
            );

            if ($result)
                $result &= Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` - '.(int)$customization['quantity'].'
					WHERE `id_cart` = '.(int)$this->id.'
					AND `id_product` = '.(int)$id_product.
                    ((int)$id_product_attribute ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
					AND `id_address_delivery` = '.(int)$id_address_delivery
                );

            if (!$result)
                return false;

            return Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'customization`
				WHERE `id_customization` = '.(int)$id_customization
            );
        }

        return true;
    }

    /**
     * Get the gift wrapping price
     * @param boolean $with_taxes With or without taxes
     * @return float wrapping price
     */
    public function getGiftWrappingPrice($with_taxes = true, $id_address = null)
    {
        static $address = null;

        $wrapping_fees = (float)Configuration::get('PS_GIFT_WRAPPING_PRICE');
        if ($with_taxes && $wrapping_fees > 0)
        {
            if ($address === null)
            {
                if ($id_address === null)
                    $id_address = (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                try {
                    $address = Address::initialize($id_address);
                } catch (Exception $e) {
                    $address = new Address();
                    $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                }
            }

            $tax_manager = TaxManagerFactory::getManager($address, (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
            $tax_calculator = $tax_manager->getTaxCalculator();
            $wrapping_fees = $tax_calculator->addTaxes($wrapping_fees);
        }

        return $wrapping_fees;
    }

    /**
     * Get the number of packages
     *
     * @return int number of packages
     */
    public function getNbOfPackages()
    {
        static $nb_packages = 0;

        if (!$nb_packages)
            foreach ($this->getPackageList() as $by_address)
                $nb_packages += count($by_address);

        return $nb_packages;
    }

    /**
     * Get products grouped by package and by addresses to be sent individualy (one package = one shipping cost).
     *
     * @return array array(
     *                   0 => array( // First address
     *                       0 => array(  // First package
     *                           'product_list' => array(...),
     *                           'carrier_list' => array(...),
     *                           'id_warehouse' => array(...),
     *                       ),
     *                   ),
     *               );
     * @todo Add avaibility check
     */


    public function getPackageIdWarehouse($package, $id_carrier = null)
    {
        if ($id_carrier === null)
            if (isset($package['id_carrier']))
                $id_carrier = (int)$package['id_carrier'];

        if ($id_carrier == null)
            return $package['id_warehouse'];

        foreach ($package['warehouse_list'] as $id_warehouse)
        {
            $warehouse = new Warehouse((int)$id_warehouse);
            $available_warehouse_carriers = $warehouse->getCarriers();
            if (in_array($id_carrier, $available_warehouse_carriers))
                return (int)$id_warehouse;
        }
        return 0;
    }

    /**
     * Get all deliveries options available for the current cart
     * @param Country $default_country
     * @param boolean $flush Force flushing cache
     *
     * @return array array(
     *                   0 => array( // First address
     *                       '12,' => array(  // First delivery option available for this address
     *                           carrier_list => array(
     *                               12 => array( // First carrier for this option
     *                                   'instance' => Carrier Object,
     *                                   'logo' => <url to the carriers logo>,
     *                                   'price_with_tax' => 12.4,
     *                                   'price_without_tax' => 12.4,
     *                                   'package_list' => array(
     *                                       1,
     *                                       3,
     *                                   ),
     *                               ),
     *                           ),
     *                           is_best_grade => true, // Does this option have the biggest grade (quick shipping) for this shipping address
     *                           is_best_price => true, // Does this option have the lower price for this shipping address
     *                           unique_carrier => true, // Does this option use a unique carrier
     *                           total_price_with_tax => 12.5,
     *                           total_price_without_tax => 12.5,
     *                           position => 5, // Average of the carrier position
     *                       ),
     *                   ),
     *               );
     *               If there are no carriers available for an address, return an empty  array
     */


    /**
     *
     * Sort list of option delivery by parameters define in the BO
     * @param $option1
     * @param $option2
     * @return int -1 if $option 1 must be placed before and 1 if the $option1 must be placed after the $option2
     */
    public static function sortDeliveryOptionList($option1, $option2)
    {
        static $order_by_price = null;
        static $order_way = null;
        if (is_null($order_by_price))
            $order_by_price = !Configuration::get('PS_CARRIER_DEFAULT_SORT');
        if (is_null($order_way))
            $order_way = Configuration::get('PS_CARRIER_DEFAULT_ORDER');

        if ($order_by_price)
            if ($order_way)
                return ($option1['total_price_with_tax'] < $option2['total_price_with_tax']) * 2 - 1; // return -1 or 1
            else
                return ($option1['total_price_with_tax'] >= $option2['total_price_with_tax']) * 2 - 1; // return -1 or 1
        else
            if ($order_way)
                return ($option1['position'] < $option2['position']) * 2 - 1; // return -1 or 1
            else
                return ($option1['position'] >= $option2['position']) * 2 - 1; // return -1 or 1
    }

    public function carrierIsSelected($id_carrier, $id_address)
    {
        $delivery_option = $this->getDeliveryOption();
        $delivery_option_list = $this->getDeliveryOptionList();

        if (!isset($delivery_option[$id_address]))
            return false;

        if (!isset($delivery_option_list[$id_address][$delivery_option[$id_address]]))
            return false;

        if (!in_array($id_carrier, array_keys($delivery_option_list[$id_address][$delivery_option[$id_address]]['carrier_list'])))
            return false;

        return true;
    }


    /**
     * Return shipping total of a specific carriers for the cart
     *
     * @param int $id_carrier
     * @param array $delivery_option Array of the delivery option for each address
     * @param booleal $useTax
     * @param Country $default_country
     * @return float Shipping total
     */
    public function getCarrierCost($id_carrier, $useTax = true, Country $default_country = null, $delivery_option = null)
    {
        if (is_null($delivery_option))
            $delivery_option = $this->getDeliveryOption($default_country);

        $total_shipping = 0;
        $delivery_option_list = $this->getDeliveryOptionList();


        foreach ($delivery_option as $id_address => $key)
        {
            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key]))
                continue;
            if (isset($delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]))
            {
                if ($useTax)
                    $total_shipping += $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['price_with_tax'];
                else
                    $total_shipping += $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['price_without_tax'];
            }
        }

        return $total_shipping;
    }


    /**
     * @deprecated 1.5.0, use Cart->getPackageShippingCost()
     */
    public function getOrderShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null)
    {
        Tools::displayAsDeprecated();
        return $this->getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list);
    }

    /**
     * @deprecated 1.5.0
     */
    public function checkDiscountValidity($obj, $discounts, $order_total, $products, $check_cart_discount = false)
    {
        Tools::displayAsDeprecated();
        $context = Context::getContext()->cloneContext();
        $context->cart = $this;

        return $obj->checkValidity($context);
    }

    public static function lastNoneOrderedCart($id_customer)
    {
        $sql = 'SELECT c.`id_cart`
				FROM '._DB_PREFIX_.'cart c
				WHERE c.`id_cart` NOT IN (SELECT o.`id_cart` FROM '._DB_PREFIX_.'orders o WHERE o.`id_customer` = '.(int)$id_customer.')
				AND c.`id_customer` = '.(int)$id_customer.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'c').'
				ORDER BY c.`date_upd` DESC';

        if (!$id_cart = Db::getInstance()->getValue($sql))
            return false;

        return (int)$id_cart;
    }


    /**
     * Build cart object from provided id_order
     *
     * @param int $id_order
     * @return Cart|bool
     */
    public static function getCartByOrderId($id_order)
    {
        if ($id_cart = Cart::getCartIdByOrderId($id_order))
            return new Cart((int)$id_cart);

        return false;
    }

    public static function getCartIdByOrderId($id_order)
    {
        $result = Db::getInstance()->getRow('SELECT `id_cart` FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.(int)$id_order);
        if (!$result || empty($result) || !array_key_exists('id_cart', $result))
            return false;
        return $result['id_cart'];
    }

    /**
     * Add customer's text
     *
     * @params int $id_product
     * @params int $index
     * @params int $type
     * @params string $textValue
     *
     * @return bool Always true
     */
    public function addTextFieldToProduct($id_product, $index, $type, $text_value)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $text_value, 0);
    }

    /**
     * Add customer's pictures
     *
     * @return bool Always true
     */
    public function addPictureToProduct($id_product, $index, $type, $file)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $file, 0);
    }

    public function deletePictureToProduct($id_product, $index)
    {
        Tools::displayAsDeprecated();
        return $this->deleteCustomizationToProduct($id_product, 0);
    }

    /**
     * Remove a customer's customization
     *
     * @param int $id_product
     * @param int $index
     * @return bool
     */
    public function deleteCustomizationToProduct($id_product, $index)
    {
        $result = true;

        $cust_data = Db::getInstance()->getRow('
			SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
			ON cu.`id_customization` = cd.`id_customization`
			WHERE cu.`id_cart` = '.(int)$this->id.'
			AND cu.`id_product` = '.(int)$id_product.'
			AND `index` = '.(int)$index.'
			AND `in_cart` = 0'
        );

        // Delete customization picture if necessary
        if ($cust_data['type'] == 0)
            $result &= (@unlink(_PS_UPLOAD_DIR_.$cust_data['value']) && @unlink(_PS_UPLOAD_DIR_.$cust_data['value'].'_small'));

        $result &= Db::getInstance()->execute('DELETE
			FROM `'._DB_PREFIX_.'customized_data`
			WHERE `id_customization` = '.(int)$cust_data['id_customization'].'
			AND `index` = '.(int)$index
        );
        return $result;
    }

    /**
     * Return custom pictures in this cart for a specified product
     *
     * @param int $id_product
     * @param int $type only return customization of this type
     * @param bool $not_in_cart only return customizations that are not in cart already
     * @return array result rows
     */
    public function getProductCustomization($id_product, $type = null, $not_in_cart = false)
    {
        if (!Customization::isFeatureActive())
            return array();

        $result = Db::getInstance()->executeS('
			SELECT cu.id_customization, cd.index, cd.value, cd.type, cu.in_cart, cu.quantity
			FROM `'._DB_PREFIX_.'customization` cu
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (cu.`id_customization` = cd.`id_customization`)
			WHERE cu.id_cart = '.(int)$this->id.'
			AND cu.id_product = '.(int)$id_product.
            ($type === Product::CUSTOMIZE_FILE ? ' AND type = '.(int)Product::CUSTOMIZE_FILE : '').
            ($type === Product::CUSTOMIZE_TEXTFIELD ? ' AND type = '.(int)Product::CUSTOMIZE_TEXTFIELD : '').
            ($not_in_cart ? ' AND in_cart = 0' : '')
        );
        return $result;
    }

    public function duplicate()
    {
        if (!Validate::isLoadedObject($this))
            return false;

        $cart = new Cart($this->id);
        $cart->id = null;
        $cart->id_shop = $this->id_shop;
        $cart->id_shop_group = $this->id_shop_group;

        if (!Customer::customerHasAddress((int)$cart->id_customer, (int)$cart->id_address_delivery))
            $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$cart->id_customer);

        if (!Customer::customerHasAddress((int)$cart->id_customer, (int)$cart->id_address_invoice))
            $cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$cart->id_customer);

        $cart->add();

        if (!Validate::isLoadedObject($cart))
            return false;

        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id);

        $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $cart->id_address_delivery : 0;

        foreach ($products as $product)
        {
            if ($id_address_delivery)
            {
                if (Customer::customerHasAddress((int)$cart->id_customer, $product['id_address_delivery']))
                    $id_address_delivery = $product['id_address_delivery'];
            }

            $success &= $cart->updateQty(
                $product['quantity'],
                (int)$product['id_product'],
                (int)$product['id_product_attribute'],
                null,
                'up',
                (int)$id_address_delivery,
                new Shop($cart->id_shop)
            );
        }

        // Customized products
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'customization c
			LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
			WHERE c.id_cart = '.(int)$this->id
        );

        // Get datas from customization table
        $customs_by_id = array();
        foreach ($customs as $custom)
        {
            if (!isset($customs_by_id[$custom['id_customization']]))
                $customs_by_id[$custom['id_customization']] = array(
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                    'quantity' => $custom['quantity']
                );
        }

        // Insert new customizations
        $custom_ids = array();
        foreach ($customs_by_id as $customization_id => $val)
        {
            Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'customization` (id_cart, id_product_attribute, id_product, `id_address_delivery`, quantity, `quantity_refunded`, `quantity_returned`, `in_cart`)
				VALUES('.(int)$cart->id.', '.(int)$val['id_product_attribute'].', '.(int)$val['id_product'].', '.(int)$id_address_delivery.', '.(int)$val['quantity'].', 0, 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        // Insert customized_data
        if (count($customs))
        {
            $first = true;
            $sql_custom_data = 'INSERT INTO '._DB_PREFIX_.'customized_data (`id_customization`, `type`, `index`, `value`) VALUES ';
            foreach ($customs as $custom)
            {
                if (!$first)
                    $sql_custom_data .= ',';
                else
                    $first = false;

                $sql_custom_data .= '('.(int)$custom_ids[$custom['id_customization']].', '.(int)$custom['type'].', '.
                    (int)$custom['index'].', \''.pSQL($custom['value']).'\')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        return array('cart' => $cart, 'success' => $success);
    }

    public function getWsCartRows()
    {
        return Db::getInstance()->executeS('
			SELECT id_product, id_product_attribute, quantity, id_address_delivery
			FROM `'._DB_PREFIX_.'cart_product`
			WHERE id_cart = '.(int)$this->id.' AND id_shop = '.(int)Context::getContext()->shop->id
        );
    }

    public function setWsCartRows($values)
    {
        if ($this->deleteAssociations())
        {
            $query = 'INSERT INTO `'._DB_PREFIX_.'cart_product`(`id_cart`, `id_product`, `id_product_attribute`, `id_address_delivery`, `quantity`, `date_add`, `id_shop`) VALUES ';

            foreach ($values as $value)
                $query .= '('.(int)$this->id.', '.(int)$value['id_product'].', '.
                    (isset($value['id_product_attribute']) ? (int)$value['id_product_attribute'] : 'NULL').', '.
                    (isset($value['id_address_delivery']) ? (int)$value['id_address_delivery'] : 0).', '.
                    (int)$value['quantity'].', NOW(), '.(int)Context::getContext()->shop->id.'),';

            Db::getInstance()->execute(rtrim($query, ','));
        }

        return true;
    }

    public function setProductAddressDelivery($id_product, $id_product_attribute, $old_id_address_delivery, $new_id_address_delivery)
    {
        // Check address is linked with the customer
        if (!Customer::customerHasAddress(Context::getContext()->customer->id, $new_id_address_delivery))
            return false;

        if ($new_id_address_delivery == $old_id_address_delivery)
            return false;

        // Checking if the product with the old address delivery exists
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('cart_product', 'cp');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$old_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        if ($result == 0)
            return false;

        // Checking if there is no others similar products with this new address delivery
        $sql = new DbQuery();
        $sql->select('sum(quantity) as qty');
        $sql->from('cart_product', 'cp');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$new_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        // Removing similar products with this new address delivery
        $sql = 'DELETE FROM '._DB_PREFIX_.'cart_product
			WHERE id_product = '.(int)$id_product.'
			AND id_product_attribute = '.(int)$id_product_attribute.'
			AND id_address_delivery = '.(int)$new_id_address_delivery.'
			AND id_cart = '.(int)$this->id.'
			LIMIT 1';
        Db::getInstance()->execute($sql);

        // Changing the address
        $sql = 'UPDATE '._DB_PREFIX_.'cart_product
			SET `id_address_delivery` = '.(int)$new_id_address_delivery.',
			`quantity` = `quantity` + '.(int)$result['sum'].'
			WHERE id_product = '.(int)$id_product.'
			AND id_product_attribute = '.(int)$id_product_attribute.'
			AND id_address_delivery = '.(int)$old_id_address_delivery.'
			AND id_cart = '.(int)$this->id.'
			LIMIT 1';
        Db::getInstance()->execute($sql);

        // Changing the address of the customizations
        $sql = 'UPDATE '._DB_PREFIX_.'customization
			SET `id_address_delivery` = '.(int)$new_id_address_delivery.'
			WHERE id_product = '.(int)$id_product.'
			AND id_product_attribute = '.(int)$id_product_attribute.'
			AND id_address_delivery = '.(int)$old_id_address_delivery.'
			AND id_cart = '.(int)$this->id;
        Db::getInstance()->execute($sql);

        return true;
    }

    public function duplicateProduct($id_product, $id_product_attribute, $id_address_delivery,
                                     $new_id_address_delivery, $quantity = 1, $keep_quantity = false)
    {
        // Check address is linked with the customer
        if (!Customer::customerHasAddress(Context::getContext()->customer->id, $new_id_address_delivery))
            return false;

        // Checking the product do not exist with the new address
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('cart_product', 'c');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$new_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        if ($result > 0)
            return false;

        // Duplicating cart_product line
        $sql = 'INSERT INTO '._DB_PREFIX_.'cart_product
			(`id_cart`, `id_product`, `id_shop`, `id_product_attribute`, `quantity`, `date_add`, `id_address_delivery`)
			values(
				'.(int)$this->id.',
				'.(int)$id_product.',
				'.(int)$this->id_shop.',
				'.(int)$id_product_attribute.',
				'.(int)$quantity.',
				NOW(),
				'.(int)$new_id_address_delivery.')';

        Db::getInstance()->execute($sql);

        if (!$keep_quantity)
        {
            $sql = new DbQuery();
            $sql->select('quantity');
            $sql->from('cart_product', 'c');
            $sql->where('id_product = '.(int)$id_product);
            $sql->where('id_product_attribute = '.(int)$id_product_attribute);
            $sql->where('id_address_delivery = '.(int)$id_address_delivery);
            $sql->where('id_cart = '.(int)$this->id);
            $duplicatedQuantity = Db::getInstance()->getValue($sql);

            if ($duplicatedQuantity > $quantity)
            {
                $sql = 'UPDATE '._DB_PREFIX_.'cart_product
					SET `quantity` = `quantity` - '.(int)$quantity.'
					WHERE id_cart = '.(int)$this->id.'
					AND id_product = '.(int)$id_product.'
					AND id_shop = '.(int)$this->id_shop.'
					AND id_product_attribute = '.(int)$id_product_attribute.'
					AND id_address_delivery = '.(int)$id_address_delivery;
                Db::getInstance()->execute($sql);
            }
        }

        // Checking if there is customizations
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization', 'c');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $results = Db::getInstance()->executeS($sql);

        foreach ($results as $customization)
        {

            // Duplicate customization
            $sql = 'INSERT INTO '._DB_PREFIX_.'customization
				(`id_product_attribute`, `id_address_delivery`, `id_cart`, `id_product`, `quantity`, `in_cart`)
				VALUES (
					'.$customization['id_product_attribute'].',
					'.$new_id_address_delivery.',
					'.$customization['id_cart'].',
					'.$customization['id_product'].',
					'.$quantity.',
					'.$customization['in_cart'].')';
            Db::getInstance()->execute($sql);

            $sql = 'INSERT INTO '._DB_PREFIX_.'customized_data(`id_customization`, `type`, `index`, `value`)
				(
					SELECT '.(int)Db::getInstance()->Insert_ID().' `id_customization`, `type`, `index`, `value`
					FROM customized_data
					WHERE id_customization = '.$customization['id_customization'].'
				)';
            Db::getInstance()->execute($sql);
        }

        $customization_count = count($results);
        if ($customization_count > 0)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'cart_product
				SET `quantity` = `quantity` = '.(int)$customization_count * $quantity.'
				WHERE id_cart = '.(int)$this->id.'
				AND id_product = '.(int)$id_product.'
				AND id_shop = '.(int)$this->id_shop.'
				AND id_product_attribute = '.(int)$id_product_attribute.'
				AND id_address_delivery = '.(int)$new_id_address_delivery;
            Db::getInstance()->execute($sql);
        }

        return true;
    }



    /**
     * Set an address to all products on the cart without address delivery
     */
    public function autosetProductAddress()
    {
        $id_address_delivery = 0;
        // Get the main address of the customer
        if ((int)$this->id_address_delivery > 0)
            $id_address_delivery = (int)$this->id_address_delivery;
        else
            $id_address_delivery = (int)Address::getFirstCustomerAddressId(Context::getContext()->customer->id);

        if (!$id_address_delivery)
            return;

        // Update
        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
			SET `id_address_delivery` = '.(int)$id_address_delivery.'
			WHERE `id_cart` = '.(int)$this->id.'
				AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)
				AND `id_shop` = '.(int)$this->id_shop;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
			SET `id_address_delivery` = '.(int)$id_address_delivery.'
			WHERE `id_cart` = '.(int)$this->id.'
				AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)';

        Db::getInstance()->execute($sql);
    }

    public function deleteAssociations()
    {
        return (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_cart` = '.(int)$this->id) !== false);
    }

    /**
     * isGuestCartByCartId
     *
     * @param int $id_cart
     * @return bool true if cart has been made by a guest customer
     */
    public static function isGuestCartByCartId($id_cart)
    {
        if (!(int)$id_cart)
            return false;
        return (bool)Db::getInstance()->getValue('
			SELECT `is_guest`
			FROM `'._DB_PREFIX_.'customer` cu
			LEFT JOIN `'._DB_PREFIX_.'cart` ca ON (ca.`id_customer` = cu.`id_customer`)
			WHERE ca.`id_cart` = '.(int)$id_cart);
    }
}


/*** ------- CART RULE -------- *****/
class JeproshopCartRuleModelCartRule extends JModelLegacy
{
	/* Filters used when retrieving the cart rules applied to a cart of when calculating the value of a reduction */
	const JEPROSHOP_FILTER_ACTION_ALL = 1;
	const JEPROSHOP_FILTER_ACTION_SHIPPING = 2;
	const JEPROSHOP_FILTER_ACTION_REDUCTION = 3;
	const JEPROSHOP_FILTER_ACTION_GIFT = 4;
	const JEPROSHOP_FILTER_ACTION_ALL_NO_CAP = 5;

	const BO_ORDER_CODE_PREFIX = 'BO_ORDER_';

	/* This variable controls that a free gift is offered only once, even when multi-shipping is activated and the same product is delivered in both addresses */
	protected static $only_one_gift = array();

	public $cart_rule_id;
	public $name;
	public $customer_id;
	public $date_from;
	public $date_to;
	public $description;
	public $quantity = 1;
	public $quantity_per_user = 1;
	public $priority = 1;
	public $partial_use = 1;
	public $code;
	public $minimum_amount;
	public $minimum_amount_tax;
	public $minimum_amount_currency;
	public $minimum_amount_shipping;
	public $country_restriction;
	public $carrier_restriction;
	public $group_restriction;
	public $cart_rule_restriction;
	public $product_restriction;
	public $shop_restriction;
	public $free_shipping;
	public $reduction_percent;
	public $reduction_amount;
	public $reduction_tax;
	public $reduction_currency;
	public $reduction_product;
	public $gift_product;
	public $gift_product_attribute;
	public $highlight;
	public $published = 1;
	public $date_add;
	public $date_upd;

	/**
	 * @static
	 * @param $lang_id
	 * @param $customer_id
	 * @param bool $published
	 * @param bool $includeGeneric
	 * @param bool $inStock
	 * @param JeproshopCartModelCart|null $cart
	 * @return array
	 */
	public static function getCustomerCartRules($lang_id, $customer_id, $published = false, $includeGeneric = true, $inStock = false, JeproshopCartModelCart $cart = null){
		if (!JeproshopCartRuleModelCartRule::isFeaturePublished()){
			return array();
		}

		$db = JFactory::getDBO();

		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_cart_rule') . " AS cart_rule LEFT JOIN " . $db->quoteName('#__jeproshop_cart_rule_lang');
		$query .= " AS cart_rule_lang ON (cart_rule." .  $db->quoteName('cart_rule_id') . " = cart_rule_lang." . $db->quoteName('cart_rule_id') . " AND ";
		$query .= "cart_rule_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE ( cart_rule." . $db->quoteName('customer_id') . " = ";
		$query .= (int)$customer_id . " OR cart_rule.group_restriction = 1 " . ($includeGeneric ?  "OR cart_rule." . $db->quoteName('customer_id') . " = 0" : "");
		$query .= ") AND cart_rule.date_from < \"" . date('Y-m-d H:i:s') . "\" AND cart_rule.date_to > \"" . date('Y-m-d H:i:s') . "\"";
		$query .= ($published ? " AND cart_rule" . $db->quoteName('published') . " = 1" : ""). ($inStock ? " AND cart_rule." . $db->quoteName('quantity') . " > 0" :  "");

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Remove cart rule that does not match the customer groups
		$customerGroups = JeproshopCustomerModelCustomer::getGroupsStatic($customer_id);
		foreach ($result as $key => $cart_rule){
			if ($cart_rule->group_restriction){
				$query = "SELECT " . $db->quoteName('group_id') . " FROM " . $db->quoteName('#__jeproshop_cart_rule_group') . " WHERE " . $db->quoteName('cart_rule_id') . " = " . (int)$cart_rule->cart_rule_id;
				$db->setQuery($query);
				$cartRuleGroups = $db->loadObjectList();
				foreach ($cartRuleGroups as $cartRuleGroup){
					if (in_array($cartRuleGroup->group_id, $customerGroups)){
						continue 2;
					}
				}
				unset($result[$key]);
			}
		}

		foreach ($result as &$cart_rule){
			if ($cart_rule->quantity_per_user){
				$quantity_used = JeproshopOrderModelOrder::getCustomerDiscounts((int)$customer_id, (int)$cart_rule->cart_rule_id);
				if (isset($cart) && isset($cart->cart_id)){
					$quantity_used += $cart->getDiscountsCustomer((int)$cart_rule->cart_rule_id);
				}
				$cart_rule->quantity_for_user = $cart_rule->quantity_per_user - $quantity_used;
			}else{
				$cart_rule->quantity_for_user = 0;
			}
		}
		unset($cart_rule);

		foreach ($result as $cart_rule){
			if ($cart_rule->shop_restriction){
				$query = "SELECT shop_id FROM " . $db->quoteName('#__jeproshop_cart_rule_shop') . " WHERE cart_rule_id = " . (int)$cart_rule->cart_rule_id;
				$db->setQuery($query);
				$cartRuleShops = $db->loadObjectList();
				foreach ($cartRuleShops as $cartRuleShop){
					if (JeproshopShopModelShop::isFeatureActive() && ($cartRuleShop->shop_id == JeproshopContext::getShopContext()->shop->shop_id)){
						continue 2;
					}
				}
				unset($result[$key]);
			}
		}

		// RetroCompatibility with 1.4 discounts
		foreach ($result as &$cart_rule){
			$cart_rule->value = 0;
			$cart_rule->minimal = JeproshopTools::convertPriceFull($cart_rule->minimum_amount, new JeproshopCurrencyModelCurrency($cart_rule->minimum_amount_currency), JeproshopContext::getContext()->currency);
			$cart_rule->cumulable = !$cart_rule->cart_rule_restriction;
			$cart_rule->discount_type_id = false;
			if ($cart_rule->free_shipping){
				$cart_rule->discount_type_id = Discount::FREE_SHIPPING;
			}elseif ($cart_rule->reduction_percent > 0){
				$cart_rule->discount_type_id = Discount::PERCENT;
				$cart_rule->value = $cart_rule->reduction_percent;
			}elseif ($cart_rule->reduction_amount > 0){
				$cart_rule->discount_type_id = Discount::AMOUNT;
				$cart_rule->value = $cart_rule->reduction_amount;
			}
		}
		return $result;
	}

	/**
	 * @static
	 * @return bool
	 */
	public static function isFeaturePublished(){
		static $is_feature_active = null;
		if ($is_feature_active === null){
			$is_feature_active = (bool)JeproshopSettingModelSetting::getValue('cart_rule_feature_active');
		}
		return $is_feature_active;
	}

    /**
     * When an entity associated to a product rule (product, category, attribute, supplier, manufacturer...)
     * is deleted, the product rules must be updated
     *
     * @param $type
     * @param $list
     * @return bool
     */
    public static function cleanProductRuleIntegrity($type, $list){
        // Type must be available in the 'type' enum of the table cart_rule_product_rule
        if (!in_array($type, array('products', 'categories', 'attributes', 'manufacturers', 'suppliers'))) {
            return false;
        }

        // This check must not be removed because this var is used a few lines below
        $list = (is_array($list) ? implode(',', array_map('intval', $list)) : (int)$list);
        if (!preg_match('/^[0-9,]+$/', $list)) {
            return false;
        }

        $db = JFactory::getDBO();

        // Delete associated restrictions on cart rules
        $query = "DELETE " . $db->quoteName('cart_rule_product_rule_value') . " FROM " . $db->quoteName('#__jeproshop_cart_rule_product_rule') . " AS cart_rule_product_rule LEFT JOIN " . $db->quoteName('#__jeproshop_cart_rule_product_rule_value') . " AS cart_rule_product_rule_value ON (cart_rule_product_rule.";
        $query .= $db->quoteName('product_rule_id') . " = cart_rule_product_rule_value." . $db->quoteName('product_rule_id') . " ) WHERE cart_rule_product_rule." . $db->quoteName('type') . " = " . $db->quote($type) . " AND cart_rule_product_rule_value." . $db->quoteName('item_id') . " IN (" . $list . ")";

        $db->setQuery($query);
        $db->query();
         // $list is checked a few lines above

        // Delete the product rules that does not have any values
        if ($db->getAffectedRows() > 0) {
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart_rule_product_rule') . " WHERE " . $db->quoteName('product_rule_id') . " NOT IN (SELECT " . $db->quoteName('product_rule_id') . " FROM " . $db->quoteName('#__jeproshop_cart_rule_product_rule_value') . ") ";

            $db->setQuery($query);
            $db->query();
        }

        // If the product rules were the only conditions of a product rule group, delete the product rule group
        if ($db->getAffectedRows() > 0) {
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart_rule_product_rule_group') . " WHERE " . $db->quoteName('product_rule_group_id') . " NOT IN (SELECT " . $db->quoteName('product_rule_group_id') . " FROM " . $db->quoteName('#__jeproshop_cart_rule_product_rule') . ")";

            $db->setQuery($query);
            $db->query();
        }

        // If the product rule group were the only restrictions of a cart rule, update de cart rule restriction cache
        if ($db->getAffectedRows() > 0) {
            $query = "UPDATE " . $db->quoteName('#__jeproshop_cart_rule') . " AS cart_rule LEFT JOIN " . $db->quoteName('#__jeproshop_cart_rule_product_rule_group') . " AS cart_rule_product_rule_group ON cart_rule." . $db->quoteName('cart_rule_id');
            $query .= " = cart_rule_product_rule_group." . $db->quoteName('cart_rule_id') . ") SET product_restriction = IF (cart_rule_product_rule_group." . $db->quoteName('product-rule_group_id') . " IS NULL, 0, 1) ";

            $db->setQuery($query);
            $db->query();
        }

        return true;
    }

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values))
            return false;

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
        return true;
    }

    public function update($null_values = false)
    {
        Cache::clean('getContextualValue_'.$this->id.'_*');
        return parent::update($null_values);
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', CartRule::isCurrentlyUsed($this->def['table'], true));

        $r = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_combination` WHERE `id_cart_rule_1` = '.(int)$this->id.' OR `id_cart_rule_2` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_group` WHERE `id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_product_rule_group` NOT IN (SELECT `id_product_rule_group` FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`)');
        $r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');

        return $r;
    }

    /**
     * Copy conditions from one cart rule to an other
     *
     * @static
     * @param int $id_cart_rule_source
     * @param int $id_cart_rule_destination
     */
    public static function copyConditions($id_cart_rule_source, $id_cart_rule_destination)
    {
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
		(SELECT '.(int)$id_cart_rule_destination.', id_shop FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
		(SELECT '.(int)$id_cart_rule_destination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
		(SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
		(SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
		(SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
		WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');

        // Todo : should be changed soon, be must be copied too
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');

        // Copy products/category filters
        $products_rules_group_source = Db::getInstance()->ExecuteS('
		SELECT id_product_rule_group,quantity FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
		WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.' ');

        foreach ($products_rules_group_source as $product_rule_group_source)
        {
            Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
			VALUES ('.(int)$id_cart_rule_destination.','.(int)$product_rule_group_source['quantity'].')');
            $id_product_rule_group_destination = Db::getInstance()->Insert_ID();

            $products_rules_source = Db::getInstance()->ExecuteS('
			SELECT id_product_rule,type FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `id_product_rule_group` = '.(int)$product_rule_group_source['id_product_rule_group'].' ');

            foreach ($products_rules_source as $product_rule_source)
            {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
				VALUES ('.(int)$id_product_rule_group_destination.',"'.pSQL($product_rule_source['type']).'")');
                $id_product_rule_destination = Db::getInstance()->Insert_ID();

                $products_rules_values_source = Db::getInstance()->ExecuteS('
				SELECT id_item FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`
				WHERE `id_product_rule` = '.(int)$product_rule_source['id_product_rule'].' ');

                foreach ($products_rules_values_source as $product_rule_value_source)
                    Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`)
					VALUES ('.(int)$id_product_rule_destination.','.(int)$product_rule_value_source['id_item'].')');
            }
        }
    }

    /**
     * Retrieves the id associated to the given code
     *
     * @static
     * @param string $code
     * @return int|bool
     */
    public static function getIdByCode($code)
    {
        if (!Validate::isCleanHtml($code))
            return false;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');
    }



    /**
     * @param $id_customer
     * @return bool
     */
    public function usedByCustomer($id_customer)
    {
        return (bool)Db::getInstance()->getValue('
		SELECT id_cart_rule
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
		WHERE ocr.`id_cart_rule` = '.(int)$this->id.'
		AND o.`id_customer` = '.(int)$id_customer);
    }

    /**
     * @static
     * @param $name
     * @return bool
     */
    public static function cartRuleExists($name)
    {
        if (!CartRule::isFeatureActive())
            return false;

        return (bool)Db::getInstance()->getValue('
		SELECT `id_cart_rule`
		FROM `'._DB_PREFIX_.'cart_rule`
		WHERE `code` = \''.pSQL($name).'\'');
    }

    /**
     * @static
     * @param $id_customer
     * @return bool
     */
    public static function deleteByIdCustomer($id_customer)
    {
        $return = true;
        $cart_rules = new PrestaShopCollection('CartRule');
        $cart_rules->where('id_customer', '=', $id_customer);
        foreach ($cart_rules as $cart_rule)
            $return &= $cart_rule->delete();
        return $return;
    }

    /**
     * @return array
     */
    public function getProductRuleGroups()
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0)
            return array();

        $productRuleGroups = array();
        $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'cart_rule_product_rule_group WHERE id_cart_rule = '.(int)$this->id);
        foreach ($result as $row)
        {
            if (!isset($productRuleGroups[$row['id_product_rule_group']]))
                $productRuleGroups[$row['id_product_rule_group']] = array('id_product_rule_group' => $row['id_product_rule_group'], 'quantity' => $row['quantity']);
            $productRuleGroups[$row['id_product_rule_group']]['product_rules'] = $this->getProductRules($row['id_product_rule_group']);
        }
        return $productRuleGroups;
    }

    /**
     * @param $id_product_rule_group
     * @return array ('type' => ? , 'values' => ?)
     */
    public function getProductRules($id_product_rule_group)
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0)
            return array();

        $productRules = array();
        $results = Db::getInstance()->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule pr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value prv ON pr.id_product_rule = prv.id_product_rule
		WHERE pr.id_product_rule_group = '.(int)$id_product_rule_group);
        foreach ($results as $row)
        {
            if (!isset($productRules[$row['id_product_rule']]))
                $productRules[$row['id_product_rule']] = array('type' => $row['type'], 'values' => array());
            $productRules[$row['id_product_rule']]['values'][] = $row['id_item'];
        }
        return $productRules;
    }

    /**
     * Check if this cart rule can be applied
     *
     * @param Context $context
     * @param bool $alreadyInCart Check if the voucher is already on the cart
     * @param bool $display_error Display error
     * @return bool|mixed|string
     */
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true)
    {
        if (!CartRule::isFeatureActive())
            return false;

        if (!$this->active)
            return (!$display_error) ? false : Tools::displayError('This voucher is disabled');
        if (!$this->quantity)
            return (!$display_error) ? false : Tools::displayError('This voucher has already been used');
        if (strtotime($this->date_from) > time())
            return (!$display_error) ? false : Tools::displayError('This voucher is not valid yet');
        if (strtotime($this->date_to) < time())
            return (!$display_error) ? false : Tools::displayError('This voucher has expired');

        if ($context->cart->id_customer)
        {
            $quantityUsed = Db::getInstance()->getValue('
			SELECT count(*)
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule od ON o.id_order = od.id_order
			WHERE o.id_customer = '.$context->cart->id_customer.'
			AND od.id_cart_rule = '.(int)$this->id.'
			AND '.(int)Configuration::get('PS_OS_ERROR').' != o.current_state
			');
            if ($quantityUsed + 1 > $this->quantity_per_user)
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher anymore (usage limit reached)');
        }

        // Get an intersection of the customer groups and the cart rule groups (if the customer is not logged in, the default group is 1)
        if ($this->group_restriction)
        {
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crg.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_group crg
			WHERE crg.id_cart_rule = '.(int)$this->id.'
			AND crg.id_group '.($context->cart->id_customer ? 'IN (SELECT cg.id_group FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = '.(int)$context->cart->id_customer.')' : '= 1'));
            if (!$id_cart_rule)
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
        }

        // Check if the customer delivery address is usable with the cart rule
        if ($this->country_restriction)
        {
            if (!$context->cart->id_address_delivery)
                return (!$display_error) ? false : Tools::displayError('You must choose a delivery address before applying this voucher to your order');
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_country crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_country = (SELECT a.id_country FROM '._DB_PREFIX_.'address a WHERE a.id_address = '.(int)$context->cart->id_address_delivery.' LIMIT 1)');
            if (!$id_cart_rule)
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in your country of delivery');
        }

        // Check if the carrier chosen by the customer is usable with the cart rule
        if ($this->carrier_restriction)
        {
            if (!$context->cart->id_carrier)
                return (!$display_error) ? false : Tools::displayError('You must choose a carrier before applying this voucher to your order');
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_carrier crc
			INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND c.id_carrier = '.(int)$context->cart->id_carrier);
            if (!$id_cart_rule)
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with this carrier');
        }

        // Check if the cart rules appliy to the shop browsed by the customer
        if ($this->shop_restriction && $context->shop->id && Shop::isFeatureActive())
        {
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crs.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_shop crs
			WHERE crs.id_cart_rule = '.(int)$this->id.'
			AND crs.id_shop = '.(int)$context->shop->id);
            if (!$id_cart_rule)
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
        }

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction)
        {
            $r = $this->checkProductRestrictions($context, false, $display_error, $alreadyInCart);
            if ($r !== false && $display_error)
                return $r;
            elseif (!$r && !$display_error)
                return false;
        }

        // Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
        if ($this->id_customer && $context->cart->id_customer != $this->id_customer)
        {
            if (!Context::getContext()->customer->isLogged())
                return (!$display_error) ? false : (Tools::displayError('You cannot use this voucher').' - '.Tools::displayError('Please log in'));
            return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
        }

        if ($this->minimum_amount)
        {
            // Minimum amount is converted to the contextual currency
            $minimum_amount = $this->minimum_amount;
            if ($this->minimum_amount_currency != Context::getContext()->currency->id)
                $minimum_amount = Tools::convertPriceFull($minimum_amount , new Currency($this->minimum_amount_currency), Context::getContext()->currency);

            $cartTotal = $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_PRODUCTS);
            if ($this->minimum_amount_shipping)
                $cartTotal += $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_SHIPPING);
            $products = $context->cart->getProducts();
            $cart_rules = $context->cart->getCartRules();

            foreach ($cart_rules as &$cart_rule)
                if ($cart_rule['gift_product'])
                    foreach ($products as $key => &$product)
                        if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute'])
                            $cartTotal = Tools::ps_round($cartTotal - $product[$this->minimum_amount_tax ? 'price_wt' : 'price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);

            if ($cartTotal < $minimum_amount)
                return (!$display_error) ? false : Tools::displayError('You have not reached the minimum amount required to use this voucher');
        }

        /* This loop checks:
            - if the voucher is already in the cart
            - if a non compatible voucher is in the cart
            - if there are products in the cart (gifts excluded)
            Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
        */
        $nb_products = Cart::getNbProducts($context->cart->id);
        $otherCartRules = $context->cart->getCartRules();
        if (count($otherCartRules))
            foreach ($otherCartRules as $otherCartRule)
            {
                if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart)
                    return (!$display_error) ? false : Tools::displayError('This voucher is already in your cart');
                if ($otherCartRule['gift_product'])
                    --$nb_products;

                if ($this->cart_rule_restriction && $otherCartRule['cart_rule_restriction'] && $otherCartRule['id_cart_rule'] != $this->id)
                {
                    $combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM '._DB_PREFIX_.'cart_rule_combination
					WHERE (id_cart_rule_1 = '.(int)$this->id.' AND id_cart_rule_2 = '.(int)$otherCartRule['id_cart_rule'].')
					OR (id_cart_rule_2 = '.(int)$this->id.' AND id_cart_rule_1 = '.(int)$otherCartRule['id_cart_rule'].')');
                    if (!$combinable)
                    {
                        $cart_rule = new CartRule((int)$otherCartRule['id_cart_rule'], $context->cart->id_lang);
                        // The cart rules are not combinable and the cart rule currently in the cart has priority over the one tested
                        if ($cart_rule->priority <= $this->priority)
                            return (!$display_error) ? false : Tools::displayError('This voucher is not combinable with an other voucher already in your cart:').' '.$cart_rule->name;
                        // But if the cart rule that is tested has priority over the one in the cart, we remove the one in the cart and keep this new one
                        else
                            $context->cart->removeCartRule($cart_rule->id);
                    }
                }
            }

        if (!$nb_products)
            return (!$display_error) ? false : Tools::displayError('Cart is empty');

        if (!$display_error)
            return true;
    }

    protected function checkProductRestrictions(Context $context, $return_products = false, $display_error = true, $alreadyInCart = false)
    {
        $selectedProducts = array();

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction)
        {
            $productRuleGroups = $this->getProductRuleGroups();
            foreach ($productRuleGroups as $id_product_rule_group => $productRuleGroup)
            {
                $eligibleProductsList = array();
                if (isset($context->cart) && is_object($context->cart) && is_array($products = $context->cart->getProducts()))
                    foreach ($products as $product)
                        $eligibleProductsList[] = (int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
                if (!count($eligibleProductsList))
                    return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in an empty cart');

                $productRules = $this->getProductRules($id_product_rule_group);
                foreach ($productRules as $productRule)
                {
                    switch ($productRule['type'])
                    {
                        case 'attributes':
                            $cartAttributes = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, pac.`id_attribute`, cp.`id_product_attribute`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')
							AND cp.id_product_attribute > 0');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartAttributes as $cartAttribute)
                                if (in_array($cartAttribute['id_attribute'], $productRule['values']))
                                {
                                    $countMatchingProducts += $cartAttribute['quantity'];
                                    if ($alreadyInCart && $this->gift_product == $cartAttribute['id_product'] && $this->gift_product_attribute == $cartAttribute['id_product_attribute'])
                                        --$countMatchingProducts;
                                    $matchingProductsList[] = $cartAttribute['id_product'].'-'.$cartAttribute['id_product_attribute'];
                                }
                            if ($countMatchingProducts < $productRuleGroup['quantity'])
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            $eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
                            break;
                        case 'products':
                            $cartProducts = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`
							FROM `'._DB_PREFIX_.'cart_product` cp
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartProducts as $cartProduct)
                                if (in_array($cartProduct['id_product'], $productRule['values']))
                                {
                                    $countMatchingProducts += $cartProduct['quantity'];
                                    if ($alreadyInCart && $this->gift_product == $cartProduct['id_product'])
                                        --$countMatchingProducts;
                                    $matchingProductsList[] = $cartProduct['id_product'].'-0';
                                }
                            if ($countMatchingProducts < $productRuleGroup['quantity'])
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            $eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
                            break;
                        case 'categories':
                            $cartCategories = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, cp.`id_product_attribute`, catp.`id_category`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'category_product` catp ON cp.id_product = catp.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')
							AND cp.`id_product` <> '.(int)$this->gift_product);
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartCategories as $cartCategory)
                                if (in_array($cartCategory['id_category'], $productRule['values'])
                                    // We also check that the product is not already in the matching product list, because there are doubles in the query results (when the product is in multiple categories)
                                    && !in_array($cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'], $matchingProductsList))
                                {
                                    $countMatchingProducts += $cartCategory['quantity'];
                                    $matchingProductsList[] = $cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'];
                                }
                            if ($countMatchingProducts < $productRuleGroup['quantity'])
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            // Attribute id is not important for this filter in the global list, so the ids are replaced by 0
                            foreach ($matchingProductsList as &$matchingProduct)
                                $matchingProduct = preg_replace('/^([0-9]+)-[0-9]+$/', '$1-0', $matchingProduct);
                            $eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
                            break;
                        case 'manufacturers':
                            $cartManufacturers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_manufacturer`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartManufacturers as $cartManufacturer)
                                if (in_array($cartManufacturer['id_manufacturer'], $productRule['values']))
                                {
                                    $countMatchingProducts += $cartManufacturer['quantity'];
                                    $matchingProductsList[] = $cartManufacturer['id_product'].'-0';
                                }
                            if ($countMatchingProducts < $productRuleGroup['quantity'])
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            $eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
                            break;
                        case 'suppliers':
                            $cartSuppliers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_supplier`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartSuppliers as $cartSupplier)
                                if (in_array($cartSupplier['id_supplier'], $productRule['values']))
                                {
                                    $countMatchingProducts += $cartSupplier['quantity'];
                                    $matchingProductsList[] = $cartSupplier['id_product'].'-0';
                                }
                            if ($countMatchingProducts < $productRuleGroup['quantity'])
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            $eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
                            break;
                    }

                    if (!count($eligibleProductsList))
                        return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                }
                $selectedProducts = array_merge($selectedProducts, $eligibleProductsList);
            }
        }

        if ($return_products)
            return $selectedProducts;
        return (!$display_error) ? true : false;
    }

    protected static function array_uintersect($array1, $array2)
    {
        $intersection = array();
        foreach ($array1 as $value1)
            foreach ($array2 as $value2)
                if (CartRule::array_uintersect_compare($value1, $value2) == 0)
                {
                    $intersection[] = $value1;
                    break 1;
                }
        return $intersection;
    }

    protected static function array_uintersect_compare($a, $b)
    {
        if ($a == $b)
            return 0;

        $asplit = explode('-', $a);
        $bsplit = explode('-', $b);
        if ($asplit[0] == $bsplit[0] && (!(int)$asplit[1] || !(int)$bsplit[1]))
            return 0;

        return 1;
    }

    /**
     * The reduction value is POSITIVE
     *
     * @param bool $use_tax
     * @param Context $context
     * @param boolean $use_cache Allow using cache to avoid multiple free gift using multishipping
     * @return float|int|string
     */
    public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
    {
        if (!CartRule::isFeatureActive())
            return 0;
        if (!$context)
            $context = Context::getContext();
        if (!$filter)
            $filter = CartRule::FILTER_ACTION_ALL;

        $all_products = $context->cart->getProducts();
        $package_products = (is_null($package) ? $all_products : $package['products']);

        $reduction_value = 0;

        $cache_id = 'getContextualValue_'.(int)$this->id.'_'.(int)$use_tax.'_'.(int)$context->cart->id.'_'.(int)$filter;
        foreach ($package_products as $product)
            $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'];

        if (Cache::isStored($cache_id))
            return Cache::retrieve($cache_id);

        // Free shipping on selected carriers
        if ($this->free_shipping && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING)))
        {
            if (!$this->carrier_restriction)
                $reduction_value += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
            else
            {
                $data = Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, c.id_carrier
					FROM '._DB_PREFIX_.'cart_rule_carrier crc
					INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
					WHERE crc.id_cart_rule = '.(int)$this->id.'
					AND c.id_carrier = '.(int)$context->cart->id_carrier);

                if ($data)
                    foreach ($data as $cart_rule)
                        $reduction_value += $context->cart->getCarrierCost((int)$cart_rule['id_carrier'], $use_tax, $context->country);
            }
        }

        if (in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION)))
        {
            // Discount (%) on the whole order
            if ($this->reduction_percent && $this->reduction_product == 0)
            {
                // Do not give a reduction on free products!
                $order_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cart_rule)
                    $order_total -= Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), 2);

                $reduction_value += $order_total * $this->reduction_percent / 100;
            }

            // Discount (%) on a specific product
            if ($this->reduction_percent && $this->reduction_product > 0)
            {
                foreach ($package_products as $product)
                    if ($product['id_product'] == $this->reduction_product)
                        $reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
            }

            // Discount (%) on the cheapest product
            if ($this->reduction_percent && $this->reduction_product == -1)
            {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product)
                {
                    $price = ($use_tax ? $product['price_wt'] : $product['price']);
                    if ($price > 0 && ($minPrice === false || $minPrice > $price))
                    {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'].'-'.$product['id_product_attribute'];
                    }
                }

                // Check if the cheapest product is in the package
                $in_package = false;
                foreach ($package_products as $product)
                    if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapest_product || $product['id_product'].'-0' == $cheapest_product)
                        $in_package = true;
                if ($in_package)
                    $reduction_value += $minPrice * $this->reduction_percent / 100;
            }

            // Discount (%) on the selection of products
            if ($this->reduction_percent && $this->reduction_product == -2)
            {
                $selected_products_reduction = 0;
                $selected_products = $this->checkProductRestrictions($context, true);
                if (is_array($selected_products))
                    foreach ($package_products as $product)
                        if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products)
                            || in_array($product['id_product'].'-0', $selected_products))
                        {
                            $price = ($use_tax ? $product['price_wt'] : $product['price']);
                            $selected_products_reduction += $price * $product['cart_quantity'];
                        }
                $reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
            }

            // Discount (¤)
            if ($this->reduction_amount)
            {
                $prorata = 1;
                if (!is_null($package) && count($all_products))
                {
                    $total_products = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                    if ($total_products)
                        $prorata = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package['products']) / $total_products;
                }

                $reduction_amount = $this->reduction_amount;
                // If we need to convert the voucher value to the cart currency
                if ($this->reduction_currency != $context->currency->id)
                {
                    $voucherCurrency = new Currency($this->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0)
                        $reduction_amount = 0;
                    else
                        $reduction_amount /= $voucherCurrency->conversion_rate;

                    // Then we convert the voucher value in the default currency into the cart currency
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount);
                }

                // If it has the same tax application that you need, then it's the right value, whatever the product!
                if ($this->reduction_tax == $use_tax)
                {
                    // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP)
                    {
                        $cart_amount = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                        $reduction_amount = min($reduction_amount, $cart_amount);
                    }
                    $reduction_value += $prorata * $reduction_amount;
                }
                else
                {
                    if ($this->reduction_product > 0)
                    {
                        foreach ($context->cart->getProducts() as $product)
                            if ($product['id_product'] == $this->reduction_product)
                            {
                                $product_price_ti = $product['price_wt'];
                                $product_price_te = $product['price'];
                                $product_vat_amount = $product_price_ti - $product_price_te;

                                if ($product_vat_amount == 0 || $product_price_te == 0)
                                    $product_vat_rate = 0;
                                else
                                    $product_vat_rate = $product_vat_amount / $product_price_te;

                                if ($this->reduction_tax && !$use_tax)
                                    $reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
                                elseif (!$this->reduction_tax && $use_tax)
                                    $reduction_value += $prorata * $reduction_amount * (1 + $product_vat_rate);
                            }
                    }
                    // Discount (¤) on the whole order
                    elseif ($this->reduction_product == 0)
                    {
                        $cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                        $cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

                        // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP)
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);

                        $cart_vat_amount = $cart_amount_ti - $cart_amount_te;

                        if ($cart_vat_amount == 0 || $cart_amount_te == 0)
                            $cart_average_vat_rate = 0;
                        else
                            $cart_average_vat_rate = Tools::ps_round($cart_vat_amount / $cart_amount_te, 3);

                        if ($this->reduction_tax && !$use_tax)
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        elseif (!$this->reduction_tax && $use_tax)
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                    */
                }
            }
        }

        // Free gift
        if ((int)$this->gift_product && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT)))
        {
            $id_address = (is_null($package) ? 0 : $package['id_address']);
            foreach ($package_products as $product)
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute))
                {
                    // The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
                    if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $id_address
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
                        || $id_address == 0
                        || !$use_cache)
                    {
                        $reduction_value += ($use_tax ? $product['price_wt'] : $product['price']);
                        if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0))
                            CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $id_address;
                        break;
                    }
                }
        }

        Cache::store($cache_id, $reduction_value);
        return $reduction_value;
    }

    /**
     * Make sure caches are empty
     * Must be called before calling multiple time getContextualValue()
     */
    public static function cleanObjectCache(){
        self::$only_one_gift = array();
    }

    protected function getCartRuleCombinations()
    {
        $array = array();
        $array['selected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		WHERE cr.id_cart_rule != '.(int)$this->id.'
		AND (
			cr.cart_rule_restriction = 0
			OR cr.id_cart_rule IN (
				SELECT IF(id_cart_rule_1 = '.(int)$this->id.', id_cart_rule_2, id_cart_rule_1)
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE '.(int)$this->id.' = id_cart_rule_1
				OR '.(int)$this->id.' = id_cart_rule_2
			)
		)');

        $array['unselected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		INNER JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc1 ON (cr.id_cart_rule = crc1.id_cart_rule_1 AND crc1.id_cart_rule_2 = '.(int)$this->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc2 ON (cr.id_cart_rule = crc2.id_cart_rule_2 AND crc2.id_cart_rule_1 = '.(int)$this->id.')
		WHERE cr.cart_rule_restriction = 1
		AND cr.id_cart_rule != '.(int)$this->id.'
		AND crc1.id_cart_rule_1 IS NULL
		AND crc2.id_cart_rule_1 IS NULL');
        return $array;
    }

    public function getAssociatedRestrictions($type, $active_only, $i18n)
    {
        $array = array('selected' => array(), 'unselected' => array());

        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop')))
            return false;

        $shop_list = '';
        if ($type == 'shop')
        {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (count($shops))
                $shop_list = ' AND t.id_shop IN ('.implode(array_map('intval', $shops), ',').') ';
        }

        if (!Validate::isLoadedObject($this) OR $this->{$type.'_restriction'} == 0)
        {
            $array['selected'] = Db::getInstance()->executeS('
			SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
			FROM `'._DB_PREFIX_.$type.'` t
			'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
			WHERE 1
			'.($active_only ? 'AND t.active = 1' : '').'
			'.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
			'.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id : '').
                $shop_list.
                ' ORDER BY name ASC');
        }
        else
        {
            if ($type == 'cart_rule')
                $array = $this->getCartRuleCombinations();
            else
            {
                $resource = Db::getInstance()->query('
				SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
				FROM `'._DB_PREFIX_.$type.'` t
				'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
				LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.($type == 'carrier' ? 'reference' : $type).' = crt.id_'.$type.'
				WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
                    $shop_list
                    .(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').
                    ' ORDER BY name ASC',
                    false);
                while ($row = Db::getInstance()->nextRow($resource))
                    $array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
            }
        }
        return $array;
    }

    public static function autoRemoveFromCart($context = null)
    {
        if (!$context)
            $context = Context::getContext();
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart))
            return array();

        static $errors = array();
        foreach ($context->cart->getCartRules() as $cart_rule)
        {
            if ($error = $cart_rule['obj']->checkValidity($context, true))
            {
                $context->cart->removeCartRule($cart_rule['obj']->id);
                $context->cart->update();
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * @static
     * @param Context|null $context
     * @return mixed
     */
    public static function autoAddToCart(Context $context = null)
    {
        if ($context === null)
            $context = Context::getContext();
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart))
            return;

        $sql = '
		SELECT cr.*
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule
		'.(!$context->customer->id && Group::isFeatureActive() ? ' LEFT JOIN '._DB_PREFIX_.'cart_rule_group crg ON cr.id_cart_rule = crg.id_cart_rule' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
		'.($context->cart->id_carrier ? 'LEFT JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crca.id_carrier AND c.deleted = 0)' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
		WHERE cr.active = 1
		AND cr.code = ""
		AND cr.quantity > 0
		AND cr.date_from < "'.date('Y-m-d H:i:s').'"
		AND cr.date_to > "'.date('Y-m-d H:i:s').'"
		AND (
			cr.id_customer = 0
			'.($context->customer->id ? 'OR cr.id_customer = '.(int)$context->cart->id_customer : '').'
		)
		AND (
			cr.`carrier_restriction` = 0
			'.($context->cart->id_carrier ? 'OR c.id_carrier = '.(int)$context->cart->id_carrier : '').'
		)
		AND (
			cr.`shop_restriction` = 0
			'.((Shop::isFeatureActive() && $context->shop->id) ? 'OR crs.id_shop = '.(int)$context->shop->id : '').'
		)
		AND (
			cr.`group_restriction` = 0
			'.($context->customer->id ? 'OR 0 < (
				SELECT cg.`id_group`
				FROM `'._DB_PREFIX_.'customer_group` cg
				INNER JOIN `'._DB_PREFIX_.'cart_rule_group` crg ON cg.id_group = crg.id_group
				WHERE cr.`id_cart_rule` = crg.`id_cart_rule`
				AND cg.`id_customer` = '.(int)$context->customer->id.'
				LIMIT 1
			)' : (Group::isFeatureActive() ? 'OR crg.`id_group` = '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP') : '')).'
		)
		AND (
			cr.`reduction_product` <= 0
			OR cr.`reduction_product` IN (
				SELECT `id_product`
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_cart` = '.(int)$context->cart->id.'
			)
		)
		AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int)$context->cart->id.')
		ORDER BY priority';
        $result = Db::getInstance()->executeS($sql);
        if ($result)
        {
            $cart_rules = ObjectModel::hydrateCollection('CartRule', $result);
            if ($cart_rules)
                foreach ($cart_rules as $cart_rule)
                    if ($cart_rule->checkValidity($context, false, false))
                        $context->cart->addCartRule($cart_rule->id);
        }
    }

    /**
     * @static
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getCartsRuleByCode($name, $id_lang, $extended = false)
    {
        return Db::getInstance()->executeS('
			SELECT cr.*, crl.*
			FROM '._DB_PREFIX_.'cart_rule cr
			LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
			WHERE code LIKE \'%'.pSQL($name).'%\''
            .($extended ? ' OR name LIKE \'%'.pSQL($name).'%\'' : ''));
    }
}