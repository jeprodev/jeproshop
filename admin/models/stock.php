<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeproshop
 * @link            http://jeprodev.net
 *
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

class JeproshopStockAvailableModelStockAvailable extends JModelLegacy 
{
	public $product_id;
	public $product_attribute_id;
	public $shop_id;
	public $shop_group_id;
	public $quantity = 0;
	public $depends_on_stock = false;
	public $out_of_stock = false;
	
	public static function getQuantityAvailableByProduct($product_id = null, $product_attribute_id = null, $shop_id = null){
		// if null, it's a product without attributes
		if ($product_attribute_id === null){ $product_attribute_id = 0; }
		 
		$db = JFactory::getDBO();
		$query = "SELECT SUM(quantity) FROM " . $db->quoteName('#__jeproshop_stock_available');
		$query .= " WHERE product_attribute_id = " . (int)$product_attribute_id;
		if($product_id !== null){
			$query .= " AND product_id = " . (int)$product_id;
		}
		$query .= JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id);
	
		$db->setQuery($query);
		$quantity = $db->loadResult();
		return ($quantity ? $quantity : 0);
	}
	
	public static function addShopRestriction($shop = NULL, $alias = NULL){
		$context = JeproshopContext::getContext();
	
		if(!empty($alias)) { $alias .= '.'; }

		/** If there is no shop id, get the context one **/
		if ($shop === null){
			if (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP){
				$shop_group = JeproshopShopModelShop::getContextShopGroup();
			}else{
				$shop_group = $context->shop->getShopGroup();
			}
	
			$shop = $context->shop;
		}elseif (is_object($shop)){
			$shop_group = $shop->getShopGroup();
		}else{
			$shop = new JeproshopShopModelShop($shop);
			$shop_group = $shop->getShopGroup();
		}
	
		/* if quantities are shared between shops of the group */
		$db = JFactory::getDBO();
		if ($shop_group->share_stock){
			$query = " AND " . $db->escape($alias). "shop_group_id = " .(int)$shop_group->shop_group_id . " AND " . $db->escape($alias) . "shop_id = 0 ";
				
		}else{
			$query = " AND " . $db->escape($alias). "shop_group_id = 0 AND " . $db->escape($alias) . "shop_id = " .(int)$shop->shop_id.' ';
		}
		return $query;
	}
	
	public static function outOfStock($product_id, $shop_id = null){
		if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }
	
		$db = JFactory::getDBO();
		$query = "SELECT out_of_stock FROM " . $db->quoteName('#__jeproshop_stock_available') . " WHERE product_id = ";
		$query .= (int)$product_id . " AND product_attribute_id = 0 " . JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id);
	
		$db->setQuery($query);
	
		return (int)$db->loadResult();
	}
	
	/**
	 * For a given product, tells if it depends on the physical (usable) stock
	 *
	 * @param int $product_id
	 * @param int $shop_id Optional : gets context if null @see Context::getContext()
	 * @return bool : depends on stock @see $depends_on_stock
	 */
	public static function dependsOnStock($product_id, $shop_id = null){
		if(!JeproshopTools::isUnsignedInt($product_id)){ return false; }
		$db = JFactory::getDBO();
	
		$query = "SELECT depends_on_stock FROM " . $db->quoteName('#__jeproshop_stock_available') . " WHERE product_id = " . (int)$product_id;
		$query .= " AND product_attribute_id = 0 " . JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id);
	
		$db->setQuery($query);
	
		return $db->loadResult();
	}


    /**
     * For a given product id, sets if stock available depends on stock
     *
     * @param int $product_id
     * @param bool|int $depends_on_stock Optional : true by default
     * @param int $shop_id Optional : gets context by default
     * @param int $product_attribute_id
     * @return bool
     */
	public static function setProductDependsOnStock($product_id, $depends_on_stock = true, $shop_id = null, $product_attribute_id = 0){
		if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }
	
		$existing_id = JeproshopStockAvailableModelStockAvailable::getStockAvailableIdByProductId((int)$product_id, (int)$product_attribute_id, $shop_id);
		$db = JFactory::getDBO();
		if ($existing_id > 0){
			$query = "UPDATE " . $db->quoteName('#__jeproshop_stock_available') . " SET " . $db->quoteName('depends_on_stock') . " = ";
			$query .= (int)$depends_on_stock . " WHERE " . $db->quoteName('stock_available_id') . " = " .(int)$existing_id;
			
			$db->setQuery($query);
			$db->query();
		}else{
			$context = JeproshopContext::getContext();
			$groupOk = false;
			
			if($shop_id === null){
				if(JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP){
					$shop_group = JeproshopShopModelShop::getContextShopGroup();
				}else{
					$shop_group = $context->shop->getShopGroup();
					$shop_id = $context->shop->shop_id;
				}
			}else{
				$shop = new JeproshopShopModelShop($shop_id);
				$shop_group = $shop->getShopGroup();
			}
			
			if($shop_group->share_stock){
				$shop_group_id = $shop_group->shop_group_id;
				$shop_id = 0;
				$groupOk = true;
			}else{
				$shop_group_id = 0;
			}
			
			$query = "INSERT INTO " . $db->quoteName('#__jeproshop_stock_available') . "(" . $db->quoteName('depends_on_stock') . ", ";
			$query .= $db->quoteName('product_id') . ", " . $db->quoteName('product_attribute_id') . ", " . $db->quoteName('shop_id');
			$query .= ", " . $db->quoteName('shop_group_id') . ") VALUES(" . (int)$depends_on_stock . ", " . (int)$product_id . ", ";
			$query .= (int)$product_attribute_id . ", " . (int)$shop_id . ", " . (int)$shop_group_id . ")";
	
			$db->setQuery($query);
			$db->query();
		}
	
		// depends on stock.. hence synchronizes
		if ($depends_on_stock){
			JeproshopStockAvailableModelStockAvailable::synchronize($product_id);
		}
	}

    /**
     * For a given product id, sets if product is available out of stocks
     *
     * @param int $product_id
     * @param bool $out_of_stock Optional false by default
     * @param int $shop_id Optional gets context by default
     * @param int $product_attribute_id
     * @return bool
     */
    public static function setProductOutOfStock($product_id, $out_of_stock = false, $shop_id = null, $product_attribute_id = 0){
        if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }

        $db = JFactory::getDBO();

        $existing_id = JeproshopStockAvailableModelStockAvailable::getStockAvailableIdByProductId((int)$product_id, (int)$product_attribute_id, $shop_id);
        if ($existing_id > 0){
            $query = "UPDATE " . $db->quoteName('#__jeproshop_stock_available') . " SET " . $db->quoteName('out_of_stock') . " = " . (int)$out_of_stock ;
            $query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id . (($product_attribute_id) ? " AND " . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id : " ");
            $query .= JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id);

            $db->setQuery($query);
            $db->query();
        }
        else
        {
            $context = JeproshopContext::getContext();
            $groupOk = false;
            // get shop group too
            if ($shop_id === null){
                if (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP)
                    $shop_group = JeproshopShopModelShop::getContextShopGroupID();
                else{
                    $shop_group = $context->shop->getShopGroup();
                    $shop_id = $context->shop->shop_id;
                }
            } else{
                $shop = new JeproshopShopModelShop($shop_id);
                $shop_group = $shop->getShopGroup();
            }

            // if quantities are shared between shops of the group
            if ($shop_group->share_stock){
                $fields = ", " . $db->quoteName('shop_group_id') . ", " . $db->quoteName('shop_d');
                $values = ", " .  (int)$shop_group->shop_group_id . ", 0";
                $groupOk = true;
            }else {
                $fields = ", " . $db->quoteName('shop_group_id');
                $values = ", 0";
            }
            // if no group specific restriction, set simple shop restriction
            if (!$groupOk) {
                $fields = ", " . $db->quoteName('shop_id');
                $values = ", " . (int)$shop_id;
            }

            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_stock_available') . "(" . $db->quoteName('out_of_stock') . ", " . $db->quoteName('product_id');
            $query .= ", " . $db->quoteName('product_attribute_id') . $fields .  ") VALUES (" . (int)$out_of_stock . ", " . (int)$product_id . ", " . $product_attribute_id . $values . ")";

            $db->setQuery($query);
            $db->query();
        }
    }
	
	public static function getStockAvailableIdByProductId($product_id, $product_attribute_id = null, $shop_id = null){
		if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }
		$db = JFactory::getDBO();
		$query = "SELECT stock_available_id FROM " . $db->quoteName('#__jeproshop_stock_available') . " WHERE ";
		$query .= $db->quoteName('product_id') . " = " . (int)$product_id;
			
		if($product_attribute_id !== null){
			$query .= " AND " . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id;
		}
		$query .= JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id);
		$db->setQuery($query);
		return (int)$db->loadResult();
	}

    /**
     * For a given id_product, synchronizes StockAvailable::quantity with Stock::usable_quantity
     *
     * @param int $product_id
     * @param int $order_shop_id
     * @return bool
     */
	public static function synchronize($product_id, $order_shop_id = null){
		if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }
		
		$db = JFactory::getDBO();
	
		// gets warehouse ids grouped by shops
		$warehouse_ids = JeproshopWarehouseModelWarehouse::getWarehousesGroupedByShops();
		if($order_shop_id !== null){
			$order_warehouses = array();
			$warehouses = JeproshopWarehouseModelWarehouse::getWarehouses(false, (int)$order_shop_id);
			foreach ($warehouses as $warehouse)
				$order_warehouses[] = $warehouse->warehouse_id;
		}
	
		// gets all product attributes ids
		$product_attribute_ids = array();
		foreach (JeproshopProductModelProduct::getProductAttributesIds($product_id) as $product_attribute_id)
			$product_attribute_ids[] = $product_attribute_id->product_attribute_id;
	
		// Allow to order the product when out of stock?
		$out_of_stock = JeproshopStockAvailableModelStockAvailable::outOfStock($product_id);
	
		$manager = JeproshopStockManagerFactory::getManager();
		// loops on $ids_warehouse to synchronize quantities
		foreach ($warehouse_ids as $shop_id => $warehouses){
			// first, checks if the product depends on stock for the given shop $id_shop
			if(JeproshopStockAvailableModelStockAvailable::dependsOnStock($product_id, $shop_id)){
				// init quantity
				$product_quantity = 0;
	
				// if it's a simple product
				if (empty($product_attribute_ids)){
					$allowed_warehouse_for_product = JeproshopWarehouseModelWarehouse::getProductWarehouseList((int)$product_id, 0, (int)$shop_id);
					$allowed_warehouse_for_product_clean = array();
					foreach ($allowed_warehouse_for_product as $warehouse){
						$allowed_warehouse_for_product_clean[] = (int)$warehouse->warehouse_id;
					}
					$allowed_warehouse_for_product_clean = array_intersect($allowed_warehouse_for_product_clean, $warehouses);
					if ($order_shop_id != null && !count(array_intersect($allowed_warehouse_for_product_clean, $order_warehouses))){
						continue;
					}
					$product_quantity = $manager->getProductRealQuantities($product_id, null, $allowed_warehouse_for_product_clean, true);
						
					/*Hook::exec('actionUpdateQuantity',
					array(
					'id_product' => $id_product,
					'id_product_attribute' => 0,
					'quantity' => $product_quantity
					)
					);*/
				}else{
					// else this product has attributes, hence loops on $ids_product_attribute
					foreach ($product_attribute_ids as $product_attribute_id){
						$allowed_warehouse_for_combination = JeproshopWarehouseModelWarehouse::getProductWarehouseList((int)$product_id, (int)$product_attribute_id, (int)$shop_id);
						$allowed_warehouse_for_combination_clean = array();
						foreach ($allowed_warehouse_for_combination as $warehouse){
							$allowed_warehouse_for_combination_clean[] = (int)$warehouse->warehouse_id;
						}
						$allowed_warehouse_for_combination_clean = array_intersect($allowed_warehouse_for_combination_clean, $warehouses);
						if ($order_shop_id != null && !count(array_intersect($allowed_warehouse_for_combination_clean, $order_warehouses))){
							continue;
						}
						$quantity = $manager->getProductRealQuantities($product_id, $product_attribute_id, $allowed_warehouse_for_combination_clean, true);
							
						$query = new DbQuery();
						$query->select('COUNT(*)');
						$query->from('stock_available');
						$query->where('id_product = '.(int)$product_id .' AND id_product_attribute = '.(int)$product_attribute_id .
								StockAvailable::addSqlShopRestriction(null, $shop_id));
							
						if ((int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query))
						{
							$query = array(
									'table' => 'stock_available',
									'data' => array('quantity' => $quantity),
									'where' => 'id_product = '.(int)$product_id .' AND id_product_attribute = '.(int)$product_attribute_id .
									StockAvailable::addSqlShopRestriction(null, $shop_id)
							);
							Db::getInstance()->update($query['table'], $query['data'], $query['where']);
						}
						else
						{
							$query = array(
									'table' => 'stock_available',
									'data' => array(
											'quantity' => $quantity,
											'depends_on_stock' => 1,
											'out_of_stock' => $out_of_stock,
											'id_product' => (int)$id_product,
											'id_product_attribute' => (int)$id_product_attribute,
									)
							);
							StockAvailable::addSqlShopParams($query['data']);
							Db::getInstance()->insert($query['table'], $query['data']);
						}
	
						$product_quantity += $quantity;
	
						Hook::exec('actionUpdateQuantity',
						array(
						'id_product' => $id_product,
						'id_product_attribute' => $id_product_attribute,
						'quantity' => $quantity
						)
						);
					}
				}
				// updates
				// if $id_product has attributes, it also updates the sum for all attributes
				$query = array(
						'table' => 'stock_available',
						'data' => array('quantity' => $product_quantity),
						'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = 0'.
						StockAvailable::addSqlShopRestriction(null, $shop_id)
				);
				Db::getInstance()->update($query['table'], $query['data'], $query['where']);
			}
		}
		// In case there are no warehouses, removes product from StockAvailable
		if (count($warehouse_ids) == 0 && JeproshopStockAvailableModelStockAvailable::dependsOnStock((int)$product_id)){
			$query = "UPDATE " . $db->quoteName('#__jeproshop_stock_available') . " SET " . $db->quoteName('quantity') . " = 0 ";
			$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id;
			
			$db->setQuery($query);
			$db->query();
		}
		JeproshopCache::clean('jeproshop_stock_available_get_quantity_available_by_product_'.(int)$product_id . '_*');
	}

    /**
     * Removes a given product from the stock available
     *
     * @param int $product_id
     * @param int $product_attribute_id Optional
     * @param mixed $shop shop id or shop object Optional
     */
    public static function removeProductFromStockAvailable($product_id, $product_attribute_id = null, $shop = null) {
        if (!JeproshopTools::isUnsignedInt($product_id)){ return false; }

        $db = JFactory::getDBO();

        if (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP) {
            if (JeproshopShopModelShop::getContextShopGroup()->share_stock == 1) {
                $product_attribute_sql = '';
                if ($product_attribute_id !== null) {
                    $product_attribute_sql = '_attribute';
                    $product_attribute_id_sql = $product_attribute_id;
                } else {
                    $product_attribute_id_sql = $product_id;
                }

                $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_product' . $product_attribute_sql . '_shop') . " WHERE " . $db->quoteName('product'. $product_attribute_sql . '_id') . " = ";
                $query .= (int)$product_attribute_id_sql . " AND " . $db->quoteName('shop_id') . " IN (" . implode(',', array_map('intval', JeproshopShopModelShop::getContextListShopIds(JeproshopShopModelShop::SHARE_STOCK))) . ")";

                $db->setQuery($query);
                $result = (int)$db->loadResult();

                if ($result) {
                    return true;
                }
            }
        }

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_stock_available') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id;
        $query .= ($product_attribute_id ? " AND " . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id : "") . JeproshopStockAvailableModelStockAvailable::addShopRestriction(null, $shop);

        $db->setQuery($query);

        $res =  $db->query();

        if ($product_attribute_id){
            if ($shop === null || !JeproshopTools::isLoadedObject($shop, 'shop_id')){
                $shop_datas = new Object();
                JeproshopStockAvailableModelStockAvailable::addSqlShopParams($shop_datas);
                $shop_id = (int)$shop_datas->shop_id;
            }else {
                $shop_id = (int)$shop->shop_id;
            }

            $stock_available = new JeproshopStockAvailableModelStockAvailable();
            $stock_available->product_id = (int)$product_id;
            $stock_available->product_attribute_id = (int)$product_id;
            $stock_available->shop_id = (int)$shop_id;
            $stock_available->postSave();
        }

        JeproshopCache::clean('jeproshop_stock_available_get-quantity_Available_by_product_'.(int)$product_id .'_*');

        return $res;
    }
}


class JeproshopStockMovementReasonModelStockMovementReason extends JModelLegacy
{
	/**
	 * Gets Stock Mvt Reasons
	 *
	 * @param int $lang_id
	 * @param int $sign Optional
	 * @return array
	 */
	public static function getStockMovementReasons($lang_id = null, $sign = null){
		if($lang_id == null){ $lang_id = JeproshopContext::getContext()->language->lang_id; }
		$db = JFactory::getDBO();
		$query = "SELECT stock_mvt_reason_lang.name, stock_mvt_reason.stock_mvt_reason_id, stock_mvt_reason.sign FROM " ;
		$query .= $db->quoteName('#__jeproshop_stock_mvt_reason') . " AS stock_mvt_reason LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_stock_mvt_reason_lang') . " AS stock_mvt_reason_lang ON(stock_mvt_reason.";
		$query .= "stock_mvt_reason_id = stock_mvt_reason_lang.stock_mvt_reason_id AND stock_mvt_reason_lang.lang_id = ";
		$query .= (int)$lang_id . ") WHERE stock_mvt_reason.deleted = 0 ";
	
		if ($sign != null){
			$query .= " AND stock_mvt_reason.sign = " .(int)$sign;
		}
		$db->setquery($query);
		return $db->loadObjectList();
	}
}

/** ---- JeproshopStockManagerFactory ---- **/
class JeproshopStockManagerFactory 
{
	/**
	 * @var $stock_manager : instance of the current StockManager.
	 */	
	protected static $stock_manager;
	
	/**
	 * Returns a StockManager
	 *
	 * @return StockManagerInterface
	 */
	public static function getManager(){
		if (!isset(JeproshopStockManagerFactory::$stock_manager)){
			$stock_manager = JeproshopStockManagerFactory::execHookStockManagerFactory();
			if (!($stock_manager instanceof StockManagerInterface)){
				$stock_manager = new JeproshopStockManager();
			}
			JeproshopStockManagerFactory::$stock_manager = $stock_manager;
		}
		return JeproshopStockManagerFactory::$stock_manager;
	}
	
	/**
	 *  Looks for a StockManager in the modules list.
	 *
	 *  @return StockManagerInterface
	 */
	public static function execHookStockManagerFactory(){
		
	}
}


/** -- JeproshopStockManager ---**/
class JeproshopStockManager implements JeproshopStockManagerInterface
{
	/**
	 * @see StockManagerInterface::isAvailable()
	 */
	public static function isAvailable(){
		// Default Manager : always available
		return true;
	}
	
	/**
	 * @see StockManagerInterface::addProduct()
	 */
	public function addProduct($product_id, $product_attribute_id = 0, JeproshopWarehouseModelWarehouse $warehouse, $quantity, $stock_mvt_reason_id, $price_tax_excluded, $is_usable = true, $supply_order_id = null){
		if (!JeproshopTools::isLoadedObject($warehouse, 'warehouse_id') || !$price_tax_excluded || !$quantity || !$product_id){
			return false;
		}
		$price_tax_excluded = (float)round($price_tax_excluded, 6);
	
		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_INC_REASON_DEFAULT');
	
		$context = Context::getContext();
	
		$mvt_params = array(
				'id_stock' => null,
				'physical_quantity' => $quantity,
				'id_stock_mvt_reason' => $id_stock_mvt_reason,
				'id_supply_order' => $id_supply_order,
				'price_te' => $price_te,
				'last_wa' => null,
				'current_wa' => null,
				'id_employee' => $context->employee->id,
				'employee_firstname' => $context->employee->firstname,
				'employee_lastname' => $context->employee->lastname,
				'sign' => 1
		);
	
		$stock_exists = false;
	
		// switch on MANAGEMENT_TYPE
		switch ($warehouse->management_type)
		{
			// case CUMP mode
			case 'WA':
	
				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);
	
				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;
	
					// for a warehouse using WA, there is one and only one stock for a given product
					$stock = $stock_collection->current();
	
					// calculates WA price
					$last_wa = $stock->price_te;
					$current_wa = $this->calculateWA($stock, $quantity, $price_te);
	
					$mvt_params['id_stock'] = $stock->id;
					$mvt_params['last_wa'] = $last_wa;
					$mvt_params['current_wa'] = $current_wa;
	
					$stock_params = array(
							'physical_quantity' => ($stock->physical_quantity + $quantity),
							'price_te' => $current_wa,
							'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
							'id_warehouse' => $warehouse->id,
					);
	
					// saves stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
				}
				else // else, the product is not in sock
				{
					$mvt_params['last_wa'] = 0;
					$mvt_params['current_wa'] = $price_te;
				}
				break;
	
				// case FIFO / LIFO mode
			case 'FIFO':
			case 'LIFO':
	
				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id, $price_te);
	
				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;
	
					// there is one and only one stock for a given product in a warehouse and at the current unit price
					$stock = $stock_collection->current();
	
					$stock_params = array(
							'physical_quantity' => ($stock->physical_quantity + $quantity),
							'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
					);
	
					// updates stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
	
					// sets mvt_params
					$mvt_params['id_stock'] = $stock->id;
	
				}
	
				break;
	
			default:
				return false;
				break;
		}
	
		if (!$stock_exists)
		{
			$stock = new Stock();
	
			$stock_params = array(
					'id_product_attribute' => $id_product_attribute,
					'id_product' => $id_product,
					'physical_quantity' => $quantity,
					'price_te' => $price_te,
					'usable_quantity' => ($is_usable ? $quantity : 0),
					'id_warehouse' => $warehouse->id
			);
	
			// saves stock in warehouse
			$stock->hydrate($stock_params);
			$stock->add();
			$mvt_params['id_stock'] = $stock->id;
		}
	
		// saves stock mvt
		$stock_mvt = new StockMvt();
		$stock_mvt->hydrate($mvt_params);
		$stock_mvt->add();
	
		return true;
	}
	
	/**
	 * @see StockManagerInterface::removeProduct()
	 */
	public function removeProduct($product_id, $product_attribute_id = null, JeproshopWarehouseModelWarehouse $warehouse, $quantity, $stock_mvt_reason_id, $is_usable = true, $order_id = null){
		$return = array();
	
		if (!Validate::isLoadedObject($warehouse) || !$quantity || !$id_product)
			return $return;
	
		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_DEC_REASON_DEFAULT');
	
		$context = Context::getContext();
	
		// Special case of a pack
		if (Pack::isPack((int)$id_product))
		{
			// Gets items
			$products_pack = Pack::getItems((int)$id_product, (int)Configuration::get('PS_LANG_DEFAULT'));
			// Foreach item
			foreach ($products_pack as $product_pack)
			{
				$pack_id_product_attribute = Product::getDefaultAttribute($product_pack->id, 1);
				if ($product_pack->advanced_stock_management == 1)
					$this->removeProduct($product_pack->id, $pack_id_product_attribute, $warehouse, $product_pack->pack_quantity * $quantity, $id_stock_mvt_reason, $is_usable, $id_order);
			}
		}
		else
		{
			// gets total quantities in stock for the current product
			$physical_quantity_in_stock = (int)$this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), false);
			$usable_quantity_in_stock = (int)$this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), true);
	
			// check quantity if we want to decrement unusable quantity
			if (!$is_usable)
				$quantity_in_stock = $physical_quantity_in_stock - $usable_quantity_in_stock;
			else
				$quantity_in_stock = $usable_quantity_in_stock;
	
			// checks if it's possible to remove the given quantity
			if ($quantity_in_stock < $quantity)
				return $return;
	
			$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);
			$stock_collection->getAll();
	
			// check if the collection is loaded
			if (count($stock_collection) <= 0)
				return $return;
	
			$stock_history_qty_available = array();
			$mvt_params = array();
			$stock_params = array();
			$quantity_to_decrement_by_stock = array();
			$global_quantity_to_decrement = $quantity;
	
			// switch on MANAGEMENT_TYPE
			switch ($warehouse->management_type)
			{
				// case CUMP mode
				case 'WA':
					// There is one and only one stock for a given product in a warehouse in this mode
					$stock = $stock_collection->current();
	
					$mvt_params = array(
							'id_stock' => $stock->id,
							'physical_quantity' => $quantity,
							'id_stock_mvt_reason' => $id_stock_mvt_reason,
							'id_order' => $id_order,
							'price_te' => $stock->price_te,
							'last_wa' => $stock->price_te,
							'current_wa' => $stock->price_te,
							'id_employee' => $context->employee->id,
							'employee_firstname' => $context->employee->firstname,
							'employee_lastname' => $context->employee->lastname,
							'sign' => -1
					);
					$stock_params = array(
							'physical_quantity' => ($stock->physical_quantity - $quantity),
							'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $quantity) : $stock->usable_quantity)
					);
	
					// saves stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
	
					// saves stock mvt
					$stock_mvt = new StockMvt();
					$stock_mvt->hydrate($mvt_params);
					$stock_mvt->save();
	
					$return[$stock->id]['quantity'] = $quantity;
					$return[$stock->id]['price_te'] = $stock->price_te;
	
					break;
	
				case 'LIFO':
				case 'FIFO':
	
					// for each stock, parse its mvts history to calculate the quantities left for each positive mvt,
					// according to the instant available quantities for this stock
					foreach ($stock_collection as $stock)
					{
						$left_quantity_to_check = $stock->physical_quantity;
						if ($left_quantity_to_check <= 0)
							continue;
	
						$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
							SELECT sm.`id_stock_mvt`, sm.`date_add`, sm.`physical_quantity`,
								IF ((sm2.`physical_quantity` is null), sm.`physical_quantity`, (sm.`physical_quantity` - SUM(sm2.`physical_quantity`))) as qty
							FROM `'._DB_PREFIX_.'stock_mvt` sm
							LEFT JOIN `'._DB_PREFIX_.'stock_mvt` sm2 ON sm2.`referer` = sm.`id_stock_mvt`
							WHERE sm.`sign` = 1
							AND sm.`id_stock` = '.(int)$stock->id.'
							GROUP BY sm.`id_stock_mvt`
							ORDER BY sm.`date_add` DESC'
						);
	
						while ($row = Db::getInstance()->nextRow($resource))
						{
							// break - in FIFO mode, we have to retreive the oldest positive mvts for which there are left quantities
							if ($warehouse->management_type == 'FIFO')
								if ($row['qty'] == 0)
									break;
	
								// converts date to timestamp
								$date = new DateTime($row['date_add']);
								$timestamp = $date->format('U');
	
								// history of the mvt
								$stock_history_qty_available[$timestamp] = array(
										'id_stock' => $stock->id,
										'id_stock_mvt' => (int)$row['id_stock_mvt'],
										'qty' => (int)$row['qty']
								);
	
								// break - in LIFO mode, checks only the necessary history to handle the global quantity for the current stock
								if ($warehouse->management_type == 'LIFO')
								{
									$left_quantity_to_check -= (int)$row['physical_quantity'];
									if ($left_quantity_to_check <= 0)
										break;
								}
						}
					}
	
					if ($warehouse->management_type == 'LIFO')
						// orders stock history by timestamp to get newest history first
						krsort($stock_history_qty_available);
					else
						// orders stock history by timestamp to get oldest history first
						ksort($stock_history_qty_available);
	
					// checks each stock to manage the real quantity to decrement for each of them
					foreach ($stock_history_qty_available as $entry)
					{
						if ($entry['qty'] >= $global_quantity_to_decrement)
						{
							$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $global_quantity_to_decrement;
							$global_quantity_to_decrement = 0;
						}
						else
						{
							$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $entry['qty'];
							$global_quantity_to_decrement -= $entry['qty'];
						}
	
						if ($global_quantity_to_decrement <= 0)
							break;
					}
	
					// for each stock, decrements it and logs the mvts
					foreach ($stock_collection as $stock)
					{
						if (array_key_exists($stock->id, $quantity_to_decrement_by_stock) && is_array($quantity_to_decrement_by_stock[$stock->id]))
						{
							$total_quantity_for_current_stock = 0;
	
							foreach ($quantity_to_decrement_by_stock[$stock->id] as $id_mvt_referrer => $qte)
							{
								$mvt_params = array(
										'id_stock' => $stock->id,
										'physical_quantity' => $qte,
										'id_stock_mvt_reason' => $id_stock_mvt_reason,
										'id_order' => $id_order,
										'price_te' => $stock->price_te,
										'sign' => -1,
										'referer' => $id_mvt_referrer,
										'id_employee' => $context->employee->id
								);
	
								// saves stock mvt
								$stock_mvt = new StockMvt();
								$stock_mvt->hydrate($mvt_params);
								$stock_mvt->save();
	
								$total_quantity_for_current_stock += $qte;
							}
	
							$stock_params = array(
									'physical_quantity' => ($stock->physical_quantity - $total_quantity_for_current_stock),
									'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $total_quantity_for_current_stock) : $stock->usable_quantity)
							);
	
							$return[$stock->id]['quantity'] = $total_quantity_for_current_stock;
							$return[$stock->id]['price_te'] = $stock->price_te;
	
							// saves stock in warehouse
							$stock->hydrate($stock_params);
							$stock->update();
						}
					}
					break;
			}
		}
	
		// if we remove a usable quantity, exec hook
		if ($is_usable)
			Hook::exec('actionProductCoverage',
					array(
							'id_product' => $id_product,
							'id_product_attribute' => $id_product_attribute,
							'warehouse' => $warehouse
					)
			);
	
		return $return;
	}

    /**
     * @see StockManagerInterface::getProductPhysicalQuantities()
     * @param int $product_id
     * @param int $product_attribute_id
     * @param null $warehouse_ids
     * @param bool $usable
     * @return int
     */
	public function getProductPhysicalQuantities($product_id, $product_attribute_id, $warehouse_ids = null, $usable = false){
        $db = JFactory::getDBO();
		if (!is_null($warehouse_ids)){
			// in case $ids_warehouse is not an array
			if (!is_array($warehouse_ids)) {
                $warehouse_ids = array($warehouse_ids);
            }
	
			// casts for security reason
			$warehouse_ids = array_map('intval', $warehouse_ids);
			if (!count($warehouse_ids)){ return 0; }
		}else {
            $warehouse_ids = array();
        }
	
		$query = "SELECT SUM(" . ($usable ? " stock.usable_quantity" : "stock.physical_quantity") . ") AS quantity FROM " . $db->quoteName('#__jeproshop_stock');
		$query .= " AS stock WHERE stock." . $db->quoteName('product_id') . " = " . (int)$product_id;

		if (0 != $product_attribute_id)
			$query .= " AND stock." . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id;
	
		if (count($warehouse_ids))
			$query .= " AND stock." . $db->quoteName('warehouse_id') . " IN(" .implode(', ', $warehouse_ids) . ")" ;

        $db->setQuery($query);
		return (int)$db->loaResult();
	}

    /**
     * @see StockManagerInterface::getProductRealQuantities()
     * @param $product_id
     * @param $product_attribute_id
     * @param null $warehouse_ids
     * @param bool $usable
     * @return int
     */
	public function getProductRealQuantities($product_id, $product_attribute_id, $warehouse_ids = null, $usable = false){
        $db = JFactory::getDBO();
		if (!is_null($warehouse_ids)){
			// in case $ids_warehouse is not an array
			if (!is_array($warehouse_ids)){ $warehouse_ids = array($warehouse_ids); }
	
			// casts for security reason
			$warehouse_ids = array_map('intval', $warehouse_ids);
		}
	
		// Gets client_orders_qty
		$query = "SELECT order_detail.product_quantity, order_detail.product_quantity_refunded FROM " . $db->quoteName('#__jeproshop_order_detail') . " AS order_detail LEFT JOIN " . $db->quoteName('#__jeproshop_orders');
        $query .= " AS ord ON (ord." . $db->quoteName('order_id') . " = order_detail." . $db->quoteName('order_id');

        $where = " WHERE order_detail." . $db->quoteName('product_id') . " = " . (int)$product_id;

		if (0 != $product_attribute_id) {
            $where .= " AND order_detail." . $db->quoteName('product_attribute_id') . " = "  . (int)$product_attribute_id;
        }
		$query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_order_history') .  " AS order_history ON (order_history." . $db->quoteName('order_id') . " = ord." . $db->quoteName('order_id') . " AND order_history.";
        $query .= $db->quoteName('order_status_id') . " = ord." . $db->quoteName('current_status') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_order_status') . " AS order_status ON(order_status."  . $db->quoteName('order_status_id');
        $query .= " = order_history." . $db->quoteName('order_status_id') . ") ";
		$where .= " AND order_status." . $db->quoteName('shipped') . " != 1 AND ord." . $db->quoteName('valid') . " = 1 OR (order_status." . $db->quoteName('order_status_id') . " != " .(int)JeproshopSettingModelSetting::getValue('order_status_error');
        $where .= " AND order_status." . $db->quoteName('order_status_id') . " != " . (int)JeproshopSettingModelSetting::getValue('order_status_canceled');
		$groupBy = " GROUP BY order_detail." . $db->quoteName('order_detail_id');
		if (count($warehouse_ids)){
			$where .= " AND order_detail.warehouse_id IN(" .implode(', ', $warehouse_ids) . ") ";
        }
        $db->setQuery($query . $where . ")". $groupBy);
		$res = $db->loadObjectList();
		$client_orders_quantity = 0;
		if (count($res)) {
            foreach ($res as $row) {
                $client_orders_quantity += ($row->product_quantity - $row->product_quantity_refunded);
            }
        }

		// Gets supply_orders_qty
		$query = "SELECT supply_order_detail." . $db->quoteName('quantity_expected') . ", supply_order_detail." . $db->quoteName('quantity_received') . " FROM " . $db->quoteName('#__jeproshop_supply_order');
        $query .= " AS supply_order LEFT JOIN " . $db->quoteName('#__jeproshop_supply_order_detail') . " AS supply_order_detail ON (supply_order_detail." . $db->quoteName('supply_order_id') . " = supply_order.";
        $query .= $db->quoteName('supply_order_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_supply_order_status') . " AS supply_order_status ON (supply_order_status." . $db->quoteName('supply_order_status_id');
		$query .= " = supply_order." . $db->quoteName('supply_order_status_id') . ") WHERE supply_order_status." . $db->quoteName('pending_receipt') . " = 1 AND supply_order_detail." . $db->quoteName('product_id');
        $query .= " = " . (int)$product_id . " AND supply_order_detail." . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id ;
        if (!is_null($warehouse_ids) && count($warehouse_ids)) {
            $query .= " AND supply_order." . $db->quoteName('warehouse_id') . " IN (" . implode(', ', $warehouse_ids) . ")" ;
        }

        $db->setQuery($query);
	    $supply_orders_quantities = $db->loadObjectList();
	
		$supply_orders_quantity = 0;
		foreach ($supply_orders_quantities as $quantity) {
            if ($quantity->quantity_expected > $quantity->quantity_received) {
                $supply_orders_quantity += ($quantity->quantity_expected - $quantity->quantity_received);
            }
        }
	
		// Gets {physical OR usable}_qty
		$quantity = $this->getProductPhysicalQuantities($product_id, $product_attribute_id, $warehouse_ids, $usable);
	
		//real qty = actual qty in stock - current client orders + current supply orders
        return ($quantity - $client_orders_quantity + $supply_orders_quantity);
	}

    /**
     * @see StockManagerInterface::transferBetweenWarehouses()
     * @param int $product_id
     * @param $product_attribute_id
     * @param int $quantity
     * @param int $warehouse_from_id
     * @param int $warehouse_to_id
     * @param bool $usable_from
     * @param bool $usable_to
     * @return bool
     */
	public function transferBetweenWarehouses($product_id, $product_attribute_id, $quantity, $warehouse_from_id, $warehouse_to_id, $usable_from = true, $usable_to = true){
		// Checks if this transfer is possible
		if ($this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($id_warehouse_from), $usable_from) < $quantity)
			return false;
	
		if ($id_warehouse_from == $id_warehouse_to && $usable_from == $usable_to)
			return false;
	
		// Checks if the given warehouses are available
		$warehouse_from = new Warehouse($id_warehouse_from);
		$warehouse_to = new Warehouse($id_warehouse_to);
		if (!Validate::isLoadedObject($warehouse_from) ||
				!Validate::isLoadedObject($warehouse_to))
					return false;
	
				// Removes from warehouse_from
				$stocks = $this->removeProduct($id_product,
						$id_product_attribute,
						$warehouse_from,
						$quantity,
						Configuration::get('PS_STOCK_MVT_TRANSFER_FROM'),
						$usable_from);
				if (!count($stocks))
					return false;
	
				// Adds in warehouse_to
				foreach ($stocks as $stock)
				{
					$price = $stock['price_te'];
	
					// convert product price to destination warehouse currency if needed
					if ($warehouse_from->id_currency != $warehouse_to->id_currency)
					{
						// First convert price to the default currency
						$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse_from->id_currency, false);
	
						// Convert the new price from default currency to needed currency
						$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse_to->id_currency, true);
					}
	
					if (!$this->addProduct($id_product,
							$id_product_attribute,
							$warehouse_to,
							$stock['quantity'],
							Configuration::get('PS_STOCK_MVT_TRANSFER_TO'),
							$price,
							$usable_to))
								return false;
				}
				return true;
	}
	
	/**
	 * @see StockManagerInterface::getProductCoverage()
	 * Here, $coverage is a number of days
	 * @return int number of days left (-1 if infinite)
	 */
	public function getProductCoverage($id_product, $id_product_attribute, $coverage, $id_warehouse = null)
	{
		if (!$id_product_attribute)
			$id_product_attribute = 0;
	
		if ($coverage == 0 || !$coverage)
			$coverage = 7; // Week by default
	
		// gets all stock_mvt for the given coverage period
		$query = '
			SELECT SUM(view.quantity) as quantity_out
			FROM
			(	SELECT sm.`physical_quantity` as quantity
				FROM `'._DB_PREFIX_.'stock_mvt` sm
				LEFT JOIN `'._DB_PREFIX_.'stock` s ON (sm.`id_stock` = s.`id_stock`)
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = s.`id_product`)
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false).'
				WHERE sm.`sign` = -1
				AND sm.`id_stock_mvt_reason` != '.Configuration::get('PS_STOCK_MVT_TRANSFER_FROM').'
				AND TO_DAYS(NOW()) - TO_DAYS(sm.`date_add`) <= '.(int)$coverage.'
				AND s.`id_product` = '.(int)$id_product.'
				AND s.`id_product_attribute` = '.(int)$id_product_attribute.
					($id_warehouse ? ' AND s.`id_warehouse` = '.(int)$id_warehouse : '').'
				GROUP BY sm.`id_stock_mvt`
			) as view';
	
		$quantity_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		if (!$quantity_out)
			return -1;
	
		$quantity_per_day = Tools::ps_round($quantity_out / $coverage);
		$physical_quantity = $this->getProductPhysicalQuantities($id_product,
				$id_product_attribute,
				($id_warehouse ? array($id_warehouse) : null),
				true);
		$time_left = ($quantity_per_day == 0) ? (-1) : Tools::ps_round($physical_quantity / $quantity_per_day);
	
		return $time_left;
	}
	
	/**
	 * For a given stock, calculates its new WA(Weighted Average) price based on the new quantities and price
	 * Formula : (physicalStock * lastCump + quantityToAdd * unitPrice) / (physicalStock + quantityToAdd)
	 *
	 * @param Stock|PrestaShopCollection $stock
	 * @param int $quantity
	 * @param float $price_te
	 * @return int WA
	 */
	protected function calculateWA(Stock $stock, $quantity, $price_te)
	{
		return (float)Tools::ps_round(((($stock->physical_quantity * $stock->price_te) + ($quantity * $price_te)) / ($stock->physical_quantity + $quantity)), 6);
	}
	
	/**
	 * For a given product, retrieves the stock collection
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse Optional
	 * @param int $price_te Optional
	 * @return PrestaShopCollection Collection of Stock
	 */
	protected function getStockCollection($id_product, $id_product_attribute, $id_warehouse = null, $price_te = null){
		$stocks = new PrestaShopCollection('Stock');
		$stocks->where('id_product', '=', $id_product);
		$stocks->where('id_product_attribute', '=', $id_product_attribute);
		if ($id_warehouse)
			$stocks->where('id_warehouse', '=', $id_warehouse);
		if ($price_te)
			$stocks->where('price_te', '=', $price_te);
	
		return $stocks;
	}
}

/** -- JeproshopStockManagerInterface ---**/
interface JeproshopStockManagerInterface 
{
	/**
	 * Checks if the StockManager is available
	 *
	 * @return StockManagerInterface
	 */
	public static function isAvailable();
	
	/**
	 * For a given product, adds a given quantity
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param JeproshopWarehouseModelWarehouse $warehouse
	 * @param int $quantity
	 * @param int $stock_movement_reason_id
	 * @param float $price_tax_excluded
	 * @param bool $is_usable
	 * @param int $supply_order_id optional
	 * @return bool
	*/
	public function addProduct($product_id, $product_attribute_id, JeproshopWarehouseModelWarehouse $warehouse, $quantity, $stock_movement_reason_id, $price_tax_excluded, $is_usable = true, $supply_order_id = null);
	
	/**
	 * For a given product, removes a given quantity
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param JeproshopWarehouseModelWarehouse $warehouse
	 * @param int $quantity
	 * @param int $stock_movement_reason_id
	 * @param bool $is_usable
	 * @param int $order_id Optional
	 * @return array - empty if an error occurred | details of removed products quantities with corresponding prices otherwise
	*/
	public function removeProduct($product_id, $product_attribute_id, JeproshopWarehouseModelWarehouse $warehouse, $quantity, $stock_movement_reason_id, $is_usable = true, $order_id = null);
	
	/**
	 * For a given product, returns its physical quantity
	 * If the given product has combinations and $id_product_attribute is null, returns the sum for all combinations
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param array|int $warehouse_ids optional
	 * @param bool $usable false default - in this case we retrieve all physical quantities, otherwise we retrieve physical quantities flagged as usable
	 * @return int
	*/
	public function getProductPhysicalQuantities($product_id, $product_attribute_id, $warehouse_ids = null, $usable = false);
	
	/**
	 * For a given product, returns its real quantity
	 * If the given product has combinations and $id_product_attribute is null, returns the sum for all combinations
	 * Real quantity : (physical_qty + supply_orders_qty - client_orders_qty)
	 * If $usable is defined, real quantity: usable_qty + supply_orders_qty - client_orders_qty
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param array|int $warehouse_ids optional
	 * @param bool $usable false by default
	 * @return int
	*/
	public function getProductRealQuantities($product_id, $product_attribute_id, $warehouse_ids = null, $usable = false);
	
	/**
	 * For a given product, transfers quantities between two warehouses
	 * By default, it manages usable quantities
	 * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 2
	 * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 1
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param int $quantity
	 * @param int $warehouse_from
	 * @param int $warehouse_to
	 * @param bool $usable_from Optional, true by default
	 * @param bool $usable_to Optional, true by default
	 * @return bool
	*/
	public function transferBetweenWarehouses($product_id, $product_attribute_id, $quantity, $warehouse_from, $warehouse_to, $usable_from = true, $usable_to = true);
	
	/**
	 * For a given product, returns the time left before being out of stock.
	 * By default, for the given product, it will use sum(quantities removed in all warehouses)
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param int $coverage
	 * @param int $warehouse_id Optional
	 * @return int time
	*/
	public function getProductCoverage($product_id, $product_attribute_id, $coverage, $warehouse_id = null);	
}