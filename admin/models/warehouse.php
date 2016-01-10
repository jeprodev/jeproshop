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

class JeproshopWarehouseModelWarehouse extends JModelLegacy
{
	/** @var int identifier of the warehouse */
	public $warehouse_id;
	
	/** @var int Id of the address associated to the warehouse */
	public $address_id;
	
	/** @var string Reference of the warehouse */
	public $reference;
	
	/** @var string Name of the warehouse */
	public $name;
	
	/** @var int Id of the employee who manages the warehouse */
	public $employee_id;
	
	/** @var int Id of the valuation currency of the warehouse */
	public $currency_id;
	
	/** @var bool True if warehouse has been deleted (hence, no deletion in DB) */
	public $deleted = 0;
	
	/**
	 * Describes the way a Warehouse is managed
	 * @var enum WA|LIFO|FIFO
	 */
	public $management_type;
	
	public function __construct($warehouse_id = null, $lang_id = null){
	
	}
	
	/**
	 * For a given {product, product attribute} gets warehouse list
	 *
	 * @param int $product_id ID of the product
	 * @param int $product_attribute_id Optional, uses 0 if this product does not have attributes
	 * @param int $shop_id Optional, ID of the shop. Uses the context shop id (@see JeproshopContext::shop)
	 * @return array Warehouses (ID, reference/name concatenated)
	 */
	public static function getProductWarehouseList($product_id, $product_attribute_id = 0, $shop_id = null){
		$db = JFactory::getDBO();
		// if it's a pack, returns warehouses if and only if some products use the advanced stock management
		if (JeproshopProductPack::isPack($product_id)){
			$warehouses = JeproshopWarehouseModelWarehouse::getPackWarehouses($product_id);
			/*$res = array();
			foreach ($warehouses as $warehouse)
				$res[]['id_warehouse'] = $warehouse; */
			return $warehouses;
		}
		
		$share_stock = false;
		if ($shop_id === null){
			if(JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP){
				$shop_group = JeproshopShopModelShop::getContextShopGroup();
			}else{
				$shop_group = JeproshopContext::getContext()->shop->getShopGroup();
				$shop_id = (int)JeproshopContext::getContext()->shop->shop_id;
			}
			$share_stock = $shop_group->share_stock;
		}
		else
		{
			$shop_group = JeproshopShopModelShop::getGroupFromShop($shop_id);
			$share_stock = $shop_group->share_stock;
		}
	
		if ($share_stock){
			$shop_ids = JeproshopShopModelShop::getShops(true, (int)$shop_group->shop_group_id, true);
		}else{
			$shop_ids = array((int)$shop_id);
		}
		
		$query = "SELECT warehouse_product_location.warehouse_id, CONCAT(warehouse.reference, ' - ', warehouse.name)";
		$query .= " AS name FROM " . $db->QuoteName('#__jeproshop_warehouse_product_location') . " AS warehouse_product_location";
		$query .= " INNER JOIN " .$db->quoteName('#__jeproshop_warehouse_shop') . " AS warehouse_shop ON(warehouse_shop.";
		$query .= "warehouse_id = warehouse_product_location.warehouse_id AND shop_id IN (" . implode(',', array_map('intval', $shop_ids));
		$query .= " )) INNER JOIN " . $db->quoteName('#__jeproshop_warehouse') . " AS warehouse ON (warehouse.warehouse_id = warehouse_shop.";
		$query .= "warehouse_id ) WHERE product_id = " . (int)$product_id . " AND product_attribute_id = " . (int)$product_attribute_id;
		$query .= " AND warehouse.deleted = 0 GROUP BY warehouse_product_location.warehouse_id";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Gets warehouses grouped by shops
	 *
	 * @return array (of array) Warehouses ID are grouped by shops ID
	 */
	public static function getWarehousesGroupedByShops(){
		$warehouse_ids = array();
		$db = JFactory::getDBO();
		$query = "SELECT warehouse_id, shop_id FROM ". $db->quoteName('#__jeproshop_warehouse_shop');
		$query .= " ORDER BY " . $db->quoteName('shop_id');
		
		$db->setQuery($query);
		$warehouses = $db->loadObjectList();
	
		// queries to get warehouse ids grouped by shops
		foreach ($warehouses as $row){
			$warehouse_ids[$row->shop_id][] = $row->warehouse_id;
		}
		return $warehouse_ids;
	}
	
	/**
	 * For a given product, returns the warehouses it is stored in
	 *
	 * @param int $id_product Product Id
	 * @param int $id_product_attribute Optional, Product Attribute Id - 0 by default (no attribues)
	 * @return array Warehouses Ids and names
	 */
	public static function getWarehousesByProductId($product_id, $product_attribute_id = 0){
		if (!$product_id && !$product_attribute_id){ return array(); }
		$db = JFactory::getDBO();
		
		$query = "SELECT DISTINCT warehouse.warehouse_id, CONCAT(warehouse.reference, ' - ', warehouse.name) as name FROM " . $db->quoteName('#__jeproshop_warehouse');
		$query .= " AS warehouse LEFT JOIN " . $db->quoteName('#__jeproshop_stock') . " AS stock ON (stock.warehouse_id = warehouse.warehouse_id) WHERE 1 ";
		$query .= ($product_id ? " AND stock.product_id = " . (int)$product_id : "") . ($product_attribute_id ? " AND stock.product_attribute_id = " . (int)$product_attribute_id : "");
		$query .= " ORDER BY warehouse.reference ASC";
		$db->setQuery($query); 
		
		return $db->loadObjectList();
	}
	
}

/*** ---- JeproshopWarehouseProductLocation ----- ***/
class JeproshopWarehouseProductLocationModelWarehouseProductLocation extends JModelLegacy
{
	/**
	 * @var int product ID
	 * */
	public $product_id;
	
	/**
	 * @var int product attribute ID
	 * */
	public $product_attribute_id;
	
	/**
	 * @var int warehouse ID
	 * */
	public $warehouse_id;
	
	/**
	 *  @var string location of the product
	 * */
	public $location;
	
	public function __construct($warehouse_location_id = null, $lang_id = null){
	
	}
	
	/**
	 * For a given product and warehouse, gets the location
	 *
	 * @param int $id_product product ID
	 * @param int $id_product_attribute product attribute ID
	 * @param int $id_warehouse warehouse ID
	 * @return string $location Location of the product
	 */
	public static function getProductLocation($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.location');
		$query->from('warehouse_product_location', 'wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);
	
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
	
	/**
	 * For a given product and warehouse, gets the WarehouseProductLocation corresponding ID
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_supplier
	 * @return int $id_warehouse_product_location ID of the WarehouseProductLocation
	 */
	public static function getIdByProductAndWarehouse($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.id_warehouse_product_location');
		$query->from('warehouse_product_location', 'wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);
	
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
	
	/**
	 * For a given product, gets its warehouses
	 *
	 * @param int $id_product
	 * @return PrestaShopCollection The type of the collection is WarehouseProductLocation
	 */
	public static function getCollection($id_product)
	{
		$collection = new PrestaShopCollection('WarehouseProductLocation');
		$collection->where('id_product', '=', (int)$id_product);
		return $collection;
	}
	
	public static function getProducts($id_warehouse)
	{
		return Db::getInstance()->executeS('SELECT DISTINCT id_product FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_warehouse='.(int)$id_warehouse);
	}
	
	/***
	 * Save current object to database (add or update)
	*
	* @param bool $null_values
	* @param bool $autodate
	* @return boolean Insertion result
	*/
	public function save($null_values = false, $autodate = true){
		return (int)$this->warehouse_id > 0 ? $this->updateWarehouse($null_values) : $this->addWarehouse($autodate, $null_values);
	}
	
	/**
	 * Add current object to database
	 *
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function addWarehouse($autodate = true, $null_values = false){
		
	}
	

	/**
	 * Update current object to database
	 *
	 * @param bool $null_values
	 * @return boolean Update result
	 */
	public function updateWarehouse($null_values = false){
		
	}
}