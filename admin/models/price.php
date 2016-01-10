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

class JeproshopSpecificPriceModelSpecificPrice extends JModelLegacy
{
	public $product_id;
	
	public $specific_price_rule_id = 0;
	
	public $cart_id = 0;
	
	public $product_attribute_id;
	
	public $specific_price_id;
	
	public $shop_id;
	
	public $shop_group_id;
	
	public $currency_id;
	
	public $country_id;
	
	public $group_id;
	
	public $customer_id;
	
	public $price;
	
	public $from_quantity;
	
	public $reduction;
	
	public $reduction_type;
	
	public $from;
	
	public $to;
	
	protected static $_specific_price_cache = array();
	protected static $_cache_priorities = array();
	
	public function add(){
		$db = JFactory::getDBO();
		
		$query = "INSERT INTO " . $db->quoteName('#__jeproshop_specific_price') . "(" . $db->quoteName('specific_price_rule_id');
		$query .= ", " . $db->quoteName('product_id') . ", " . $db->quoteName('shop_id') . ", " . $db->quoteName('shop_group_id');
		$query .= ", " . $db->quoteName('currency_id') . ", " . $db->quoteName('country_id') . ", " . $db->quoteName('group_id');
		$query .= ", " . $db->quoteName('customer_id') . ", " . $db->quoteName('product_attribute_id') . ", " . $db->quoteName('price');
		$query .= ", " . $db->quoteName('from_quantity') . ", " . $db->quoteName('reduction') . ", " . $db->quoteName('reduction_type');
		$query .= ", " . $db->quoteName('from') . ", " . $db->quoteName('to') . ") VALUES (" . (int)$this->specific_price_rule_id . ", ";
		$query .= (int)$this->product_id . ", " . (int)$this->shop_id . ", " . (int)$this->shop_group_id . ", " . (int)$this->currency_id . ", ";
		$query .= (int)$this->country_id . ", " . (int)$this->group_id . ", " . (int)$this->customer_id . ", " . (int)$this->product_attribute_id;
		$query .= ", " . (float)$this->price . ", " . (int)$this->from_quantity . ", " . (float)$this->reduction . ", " . $db->quote($this->reduction_type);
		$query .= ", " . $db->quote($this->from) . ", " . $db->quote($this->to) . ")";
		
		$db->setQuery($query);
		if($db->query()){
			// Flush cache when we adding a new specific price
			JeproshopSpecificPriceModelSpecificPrice::$_specific_price_cache = array();
			JeproshopProductModelProduct::flushPriceCache();
			// Set cache of feature detachable to true
			JeproshopSettingModelSetting::updateValue('specific_price_feature_active', '1');
			return true;
		}
		return false;
	}
	
	public static function getPriority($product_id){
		if(!JeproshopSpecificPriceModelSpecificPrice::isFeaturePublished()){
			return explode(';', JeproshopSettingModelSetting::getValue('specific_price_priorities'));
		}
	
		if(!isset(JeproshopSpecificPriceModelSpecificPrice::$_cache_priorities[(int)$product_id])){
			$db = JFactory::getDBO();
	
			$query = "SELECT " . $db->quoteName('priority') . ", " . $db->quoteName('specific_price_priority_id') . " FROM ";
			$query .= $db->quoteName('#__jeproshop_specific_price_priority') ." WHERE " . $db->quoteName('product_id') . " = ";
			$query .= (int)$product_id . " ORDER BY " . $db->quoteName('specific_price_priority_id') . " DESC ";
	
			$db->setQuery($query);
			JeproshopSpecificPriceModelSpecificPrice::$_cache_priorities[(int)$product_id] = $db->loadObject();
		}
		$priorities = JeproshopSpecificPriceModelSpecificPrice::$_cache_priorities[(int)$product_id];
		if(!$priorities){
			$priority = JeproshopSettingModelSetting::getValue('specific_price_priorities');
			$priorities = 'customer_id;' . $priority;
		}else{
			$priorities = $priorities->priority;
		}
		
		return preg_split('/;/', $priorities);
	}
	
	public static function getSpecificPricesByProductId($product_id, $product_attribute_id = false, $cart_id = FALSE){
		$db = JFactory::getDBO();
	
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_specific_price') . " WHERE " . $db->quoteName('product_id');
		$query .= " = " . (int)$product_id . ($product_attribute_id ? " AND " . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id : " ");
		$query .= " AND cart_id = " . (int)$cart_id;
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public static function getSpecificPrice($product_id, $shop_id, $currency_id, $country_id, $group_id, $quantity, $product_attribute_id = null, $customer_id = 0, $cart_id = 0, $real_quantity = 0){
        if (!JeproshopSpecificPriceModelSpecificPrice::isFeaturePublished()){ return array(); }
        /*
        ** The date is not taken into account for the cache, but this is for the better because it keeps the consistency for the whole script.
        ** The price must not change between the top and the bottom of the page
        */

        $db = JFactory::getDBO();
        $key = ((int)$product_id . '_' . (int)$shop_id . '_' . (int)$currency_id . '_' . (int)$country_id . '_' . (int)$group_id . '_' . (int)$quantity . '_' . (int)$product_attribute_id . '_'.(int)$cart_id . '_' . (int)$customer_id . '_' . (int)$real_quantity);
        if (!array_key_exists($key, JeproshopSpecificPriceModelSpecificPrice::$_specific_price_cache)) {
            $now = date('Y-m-d H:i:s');
            $query = "SELECT *, " . JeproshopSpecificPriceModelSpecificPrice::getScoreQuery($product_id, $shop_id, $currency_id, $country_id, $group_id, $customer_id);
            $query .= " FROM " . $db->quoteName('#__jeproshop_specific_price') . " WHERE " . $db->quoteName('product_id') . " IN (0, " .(int)$product_id . ") AND ";
            $query .= $db->quoteName('product_attribute_id') . " IN (0, " .(int)$product_attribute_id . ") AND " . $db->quoteName('shop_id') . " IN (0, " . (int)$shop_id;
            $query .= ") AND " . $db->quoteName('currency_id') . " IN (0, " .(int)$currency_id . ") AND " . $db->quoteName('country_id') . " IN (0, " .(int)$country_id ;
            $query .= ") AND " . $db->quoteName('group_id') . " IN (0, " .(int)$group_id . ") AND " . $db->quoteName('customer_id') . " IN (0, " .(int)$customer_id . ") ";
            $query .= "AND ( (" . $db->quoteName('from') . " = '0000-00-00 00:00:00' OR '" . $now . "' >= " . $db->quoteName('from') . ") AND (" . $db->quoteName('to') ;
            $query .= " = '0000-00-00 00:00:00' OR '" . $now. "' <= " . $db->quoteName('to') . ") ) AND cart_id IN (0, ".(int)$cart_id . ") AND IF(" . $db->quoteName('from_quantity');
            $query .= " > 1, " . $db->quoteName('from_quantity') . ", 0) <= " ;
            $query .= (JeproshopSettingModelSetting::getValue('qty_discount_on_combination') || !$cart_id || !$real_quantity) ? (int)$quantity : max(1, (int)$real_quantity);
            $query .= " ORDER BY " . $db->quoteName('product_attribute_id') . " DESC, " . $db->quoteName('from_quantity') . " DESC, " . $db->quoteName('specific_price_rule_id');
            $query .= " ASC, " . $db->quoteName('score') . " DESC";

            $db->setQuery($query);
            JeproshopSpecificPriceModelSpecificPrice::$_specific_price_cache[$key] = $db->loadObject(); //Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        }
        return JeproshopSpecificPriceModelSpecificPrice::$_specific_price_cache[$key];
    }

    /**
     * score generation for quantity discount
     */
    protected static function getScoreQuery($product_id, $shop_id, $currency_id, $country_id, $group_id, $customer_id){
        $db = JFactory::getDBO();
        $now = date('Y-m-d H:i:s');
        $select = "( IF ('" .$now. "' >= " . $db->quoteName('from') . " AND '" . $now. "' <= " . $db->quoteName('to') . ", ".pow(2, 0).", 0) + ";

        $priority = JeproshopSpecificPriceModelSpecificPrice::getPriority($product_id);
        foreach (array_reverse($priority) as $k => $field){
            if (!empty($field)){
                $select .= " IF (" . $db->quote($field, true) . " = ".(int)$$field . ", " .pow(2, $k + 1).", 0) + ";
            }
        }
        return rtrim($select, ' +'). ") AS " . $db->quoteName('score');
    }

    public static function getByProductId($product_id, $product_attribute_id = false, $cart_id = false){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.
            ($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').'
			AND id_cart = '.(int)$id_cart);
    }

    public static function deleteByCartId($id_cart, $id_product = false, $id_product_attribute = false){
        return Db::getInstance()->execute('
		    DELETE FROM `'._DB_PREFIX_.'specific_price`
            WHERE id_cart='.(int)$id_cart.
            ($id_product ? ' AND id_product='.(int)$id_product.' AND id_product_attribute='.(int)$id_product_attribute : ''));
    }

    public static function deleteByProductId($product_id){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_specific_price') . " WHERE " . $db->quoteName('product_id') . " = ". (int)$product_id;
        $db->setQuery($query);
        if ($db->query()){
            // Refresh cache of feature detachable
            JeproshopSettingModelSetting::updateValue('specific_price_feature_active', JeproshopSpecificPriceRuleModelSpecificPriceRule::isCurrentlyUsed('specific_price'));
            return true;
        }
        return false;
    }

    public static function isFeaturePublished(){
		static $feature_active = NULL;
		if($feature_active === NULL){
			$feature_active = JeproshopSettingModelSetting::getValue('specific_price_feature_active');
		}
		return $feature_active;
	}
}

class JeproshopSpecificPriceRuleModelSpecificPriceRule extends JModelLegacy
{
	public $specific_price_rule_id;
	public $name;
	public $shop_id;
	public $currency_id;
	public $country_id;
	public $group_id;
	public $from_quantity;
	public $price;
	public $reduction;
	public $reduction_type;
	public $from;
	public $to;
	
	protected static $rules_application_enable = true;

	public function __construct($specific_price_rule_id = null){
		
	}

    /**
     * @param array|bool $products
     */
	public static function applyAllRules($products = false){
		if (!JeproshopSpecificPriceRuleModelSpecificPriceRule::$rules_application_enable){ return; }
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_specific_price_rule');
		
		$db->setQuery($query);
		$rules = $db->loadObjectList(); //new PrestaShopCollection('SpecificPriceRule');
		foreach ($rules as $rule){
			$rule->apply($products);
		}
	}
	
	public function apply($products = false){
		if (!JeproshopSpecificPriceRuleModelSpecificPriceRule::$rules_application_enable){ return; }
	
		$this->resetApplication($products);
		$products = $this->getAffectedProducts($products);
		foreach ($products as $product){
			JeproshopSpecificPriceRuleModelSpecificPriceRule::applyRuleToProduct((int)$this->specific_price_rule_id, (int)$product->product_id, (int)$product->product_attribute_id);
		}
	}
	
	public static function applyRuleToProduct($rule_id, $product_id, $product_attribute_id = null){
		$rule = new JeproshopSpecificPriceRuleModelSpecificPriceRule((int)$rule_id);
		if (!JeproshopValidator::isLoadedObject($rule, 'specific-price_rule_id') || !$product_id){
			return false;
		}
	
		$specific_price = new JeproshopSpecificPriceModelSpecificPrice();
		$specific_price->specific_price_rule_id = (int)$rule->specific_price_rule_id;
		$specific_price->product_id = (int)$product_id;
		$specific_price->product_attribute_id = (int)$product_attribute_id;
		$specific_price->customer_id = 0;
		$specific_price->shop_id = (int)$rule->shop_id;
		$specific_price->country_id = (int)$rule->country_id;
		$specific_price->currency_id = (int)$rule->currency_id;
		$specific_price->group_id = (int)$rule->group_id;
		$specific_price->from_quantity = (int)$rule->from_quantity;
		$specific_price->price = (float)$rule->price;
		$specific_price->reduction_type = $rule->reduction_type;
		$specific_price->reduction = ($rule->reduction_type == 'percentage' ? $rule->reduction / 100 : (float)$rule->reduction);
		$specific_price->from = $rule->from;
		$specific_price->to = $rule->to;
	
		return $specific_price->add();
	}

    /**
     * This method is allow to know if a entity is currently used
     * @since 1.5.0.1
     * @param string $table name of table linked to entity
     * @param bool $has_active_column true if the table has an active column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false){
        if ($table === null)
            $table = 'specific_rule';
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName($table .'_id') . " FROM " . $db->quoteName('#__jeproshop_' . $table);

        if ($has_active_column)
            $query .= " WHERE " . $db->quoteName('published') . " = 1";

        $db->setQuery($query);
        return (bool)$db->loadResult();
    }
}