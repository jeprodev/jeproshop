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

class JeproshopCartViewCart extends JViewLegacy
{
    protected $cart;
    protected $context;

	public function renderDetails($tpl =null){
        $cartModel = new JeproshopCartModelCart();
        $carts = $cartModel->getCartList();
        $this->assignRef('carts', $carts);

		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}

    public function renderView($tpl =null){
        if (!($this->loadObject(true)))
            return;

        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $customer = new JeproshopCustomerModelCustomer($this->cart->customer_id);
        $currency = new JeproshopCurrencyModelCurrency($this->cart->currency_id);
        $this->context->cart = $this->cart;
        $this->context->currency = $currency;
        $this->context->customer = $customer;
        //$this->toolbar_title = sprintf($this->l('Cart #%06d'), $this->context->cart->cart_id);
        $products = $this->cart->getProducts();
        $customized_datas = JeproshopProductModelProduct::getAllCustomizedDatas((int)$this->cart->cart_id);
        JeproshopProductModelProduct::addCustomizationPrice($products, $customized_datas);
        $summary = $this->cart->getSummaryDetails();

        /* Display order information */
        $order_id = (int)JeproshopOrderModelOrder::getOrderIdByCartId($this->cart->cart_id);
        $order = new JeproshopOrderModelOrder($order_id);
        if (JeproshopTools::isLoadedObject($order, 'order_id')){
            $tax_calculation_method = $order->getTaxCalculationMethod();
            $shop_id = (int)$order->shop_id;
        }else{
            $shop_id = (int)$this->cart->shop_id;
            $tax_calculation_method = JeproshopGroupModelGroup::getPriceDisplayMethod(JeproshopGroupModelGroup::getCurrent()->group_id);
        }

        if ($tax_calculation_method == COM_JEPROSHOP_TAX_EXCLUDED) {
            $total_products = $summary->total_products;
            $total_discounts = $summary->total_discounts_tax_exc;
            $total_wrapping = $summary->total_wrapping_tax_exc;
            $total_price = $summary->total_price_without_tax;
            $total_shipping = $summary->total_shipping_tax_exc;
        } else {
            $total_products = $summary->total_products_wt;
            $total_discounts = $summary->total_discounts;
            $total_wrapping = $summary->total_wrapping;
            $total_price = $summary->total_price;
            $total_shipping = $summary->total_shipping;
        }
        foreach ($products as $k => &$product){
            if ($tax_calculation_method == COM_JEPROSHOP_TAX_EXCLUDED){
                $product->product_price = $product->price;
                $product->product_total = $product->total;
            } else{
                $product->product_price = $product->price_wt;
                $product->product_total = $product->total_wt;
            }
            $image = array();
            $db = JFactory::getDBO();
            if (isset($product->product_attribute_id) && (int)$product->product_attribute_id) {
                $query = "SELECT " . $db->quoteName('image_id') . " FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " WHERE product_attribute_id = " . (int)$product->product_attribute_id;
                $db->setQuery($query);
                $image = $db->loadObject();
            }

            if (!isset($image->image_id)) {
                $query = "SELECT " . $db->quoteName('image_id') . " FROM " . $db->quoteName('#__jeproshop_image') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product->product_id . " AND cover = 1 ";
                $db->setQuery($query);
                $image = $db->loadObject();
            }
            $product_obj = new JeproshopProductModelProduct($product->product_id);
            $product->qty_in_stock = JeproshopStockAvailableModelStockAvailable::getQuantityAvailableByProduct($product->product_id, isset($product->product_attribute_id) ? $product->product_attribute_id : null, (int)$shop_id);

            $image_product = new JeproshopImageModelImage($image->image_id);
            $product->image = (isset($image->image_id) ? JeproshopImageManager::thumbnail(COM_JEPROSHOP_IMAGE_DIR.'products/'.$image_product->getExistingImagePath().'.jpg', 'product_mini_'.(int)$product->product_id.(isset($product->product_attribute_id) ? '_'.(int)$product->product_attribute_id : '').'.jpg', 45, 'jpg') : '--');
        }

        /*$helper = new HelperKpi();
        $helper->id = 'box-kpi-cart';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color1';
        $helper->title = $this->l('Total Cart', null, null, false);
        $helper->subtitle = sprintf($this->l('Cart #%06d', null, null, false), $cart->id);
        $helper->value = Tools::displayPrice($total_price, $currency);
        $kpi = $helper->generate(); */

        //$this->assignRef('kpi', $kpi);
        $this->assignRef('products', $products);
        $discounts = $this->cart->getCartRules();
        $this->assignRef('discounts', $discounts);
        $this->assignRef('order', $order);
        $this->assignRef('currency', $currency);
        $this->assignRef('customer', $customer);
        $customerStats = $customer->getStats();
        $this->assignRef('customer_stats', $customerStats);
        $this->assignRef('total_products', $total_products);
        $this->assignRef('total_discounts', $total_discounts);
        $this->assignRef('total_wrapping', $total_wrapping);
        $this->assignRef('total_price', $total_price);
        $this->assignRef('total_shipping', $total_shipping);
        $this->assignRef('customized_datas', $customized_datas);

        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }
	
	public function viewRules($tpl =null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	public function viewCatalogPrices($tpl = null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	public function viewMarketings($tpl =null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	private function addToolBar(){
		$is_cart = TRUE; $is_rules = FALSE;
		switch ($this->getLayout()){
			case 'rules':
				$is_cart = FALSE;   $is_rules = TRUE;
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_CART_RULES_LIST_TITLE'), 'cart-jeproshop');
				break;
			case 'marketing':
				$is_cart = FALSE;   $is_rules = TRUE;
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_CART_RULES_LIST_TITLE'), 'cart-jeproshop');
				break;
			case 'catalog_prices':
				$is_cart = FALSE;   $is_rules = TRUE;
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_CART_RULES_LIST_TITLE'), 'cart-jeproshop');
				break;
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_GROUP_TITLE'), 'cart-jeproshop');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_CARTS_LIST_TITLE'), 'cart-jeproshop');
				JToolBarHelper::addNew('add');
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers', $is_cart);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules', $is_rules);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
	}

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die
     *
     * @param boolean $opt Return an empty object if load fail
     * @return object|boolean
     */
    public function loadObject($opt = false){
        $app = JFactory::getApplication();
        $cart_id = (int)$app->input->get('cart_id');
        if ($cart_id && JeproshopTools::isUnsignedInt($cart_id)){
            if (!$this->cart)
                $this->cart = new JeproshopCartModelCart($cart_id);
            if (JeproshopTools::isLoadedObject($this->cart, 'cart_id'))
                return $this->cart;
            // throw exception
            $this->errors[] = Tools::displayError('The object cannot be loaded (or found)');
            return false;
        }
        elseif ($opt){
            if (!$this->cart)
                $this->cart = new JeproshopCartModelCart();
            return true;
        } else {
            $this->errors[] = Tools::displayError('The object cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }
}