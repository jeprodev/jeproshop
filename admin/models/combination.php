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

class JeproshopCombinationModelCombination extends JModelLegacy
{
    public  $product_attribute_id;

	public $product_id;
	
	public $reference;
	
	public $supplier_reference;
	
	public $location;
	
	public $ean13;
	
	public $upc;
	
	public $wholesale_price;
	
	public $price;
	
	public $unit_price_impact;
	
	public $ecotax;
	
	public $minimal_quantity = 1;
	
	public $quantity;
	
	public $weight;
	
	public $default_on;

    public $shop_list_id = array();
	
	public $available_date = '0000-00-00';
	
	public function __construct($product_attribute_id = null, $lang_id = null){
	
	}

    /**
     * This method is allow to know if a feature is active
     * @return bool
     */
	public static function isFeaturePublished(){
		static $feature_active = NULL;
		if($feature_active === NULL){
			$feature_active = JeproshopSettingModelSetting::getValue('combination_feature_active');
		}
		return $feature_active;
	}
	
	public static function updateMultishopTable($data, $where = '', $specific_where = '', $update_shop = FALSE){
		$db = JFactory::getDBO();
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product_attribute') . $data . $where . $specific_where;
		$db->setQuery($query);
		$return = $db->query();
		if($update_shop){
			$query = "UPDATE " . $db->quoteName('#__jeproshop_product_attribute_shop') . $data . $where . $specific_where;
			$db->setQuery($query);
			$return &= $db->query();
		}
		return $return;
	}

    public function setAttributes($attribute_ids){
        $result = $this->deleteAssociations();
        if ($result && !empty($attribute_ids)){
            $sql_values = array();
            foreach ($attribute_ids as $value) {
                $sql_values[] = '(' . (int)$value . ', ' . (int)$this->product_attribute_id . ')';
            }

            $db = JFactory::getDBO();
            $query  = "INSERT INTO " . $db->quoteName('#__jeproshop_product_attribute_combination') . " (" . $db->quoteName('attribute_id') . ", " . $db->quoteName('product_attribute_id') . ") VALUES (" .implode(',', $sql_values) . ")";

            $db->setQuery($query);
            $result = $db->query();
        }
        return $result;
    }

    public function setImages($image_ids){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id;

        $db->setQuery($query);
        if ($db->query() === false){ return false; }

        if (!empty($image_ids)){
            $sql_values = array();

            foreach ($image_ids as $value) {
                $sql_values[] = '(' . (int)$this->product_attribute_id . ', ' . (int)$value . ')';
            }

            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_product-attribute_image') . "(" . $db->quoteName('product_attribute_id') . ", " . $db->quoteName('image_id') . " VALUES " . implode(',', $sql_values);

            $db->setQuery($query);
            $db->query();
        }
        return true;
    }

    public function delete(){
        $db = JFactory::getDBO();

        $this->clearCache();
        $result = true;

        if(JeproshopShopModelShop::isTableAssociated('product_attribute')){
            $shopListIds = JeproshopShopModelShop::getContextListShopIds();
            if(count($this->shop_list_id)){ $shopListIds = $this->shop_list_id; }

            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_shop') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id . " AND " . $db->quoteName('shop_id') . " IN (" . implode($shopListIds) . ")";

            $db->setQuery($query);
            $result &= $db->query();
        }

        $hasMultiShopEntries = $this->hasMultiShopEntries();
        if($result && !$hasMultiShopEntries){
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id;
            $db->setQuery($query);
            $result &= $db->query();
        }

        if($this->multi_lang && !$hasMultiShopEntries){
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_lang') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id;
            $db->setQuery($query);
            $result &= $db->query();
        }

        if (!$result)
            return false;

        // Removes the product from StockAvailable, for the current shop
        JeproshopStockAvailableModelStockAvailable::removeProductFromStockAvailable((int)$this->product_id, (int)$this->product_attribute_id);

        if ($specific_prices = JeproshopSpecificPriceModelSpecificPrice::getByProductId((int)$this->product_id, (int)$this->product_attribute_id))
            foreach ($specific_prices as $specific_price){
                $price = new JeproshopSpecificPriceModelSpecificPrice((int)$specific_price->specific_price_id);
                $price->delete();
            }

        if (!$this->hasMultishopEntries() && !$this->deleteAssociations())
            return false;

        $this->deleteFromSupplier($this->product_id);
        JeproshopProductModelProduct::updateDefaultAttribute($this->product_id);

        return true;
    }

    public function deleteFromSupplier($product_id){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_supplier')  . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id;
        $query .= " AND " . $db->quoteName('product_attribute_id') . " = " .(int)$this->product_attribute_id;

        $db->setQuery($query);
        return $db->query();
    }

    public function deleteAssociations(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_combination') . " WHERE " . $db->quoteName('product_attribute_id') . " = " .(int)$this->product_attribute_id;

        $db->setQuery($query);
        $result = $db->query();
        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart_product') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id;
        $db->setQuery($query);
        $result &= $db->query();
        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " WHERE " . $db->quoteName('product_attribute_id') . " = " . (int)$this->product_attribute_id;

        $db->setQuery($query);
        $result &= $db->query();

        return $result;
    }

    /**
     * Check if there is more than one entries in associated shop table for current entity
     *
     * @since 1.5.0
     * @return bool
     */
    public function hasMultishopEntries(){
        if (!JeproshopShopModelShop::isTableAssociated('product_attribute') || !JeproshopShopModelShop::isFeaturePublished())
            return false;

        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_product_attribute_shop') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_attribute_id;
        $db->quoteName($query);
        return (bool)$db->loadResult();
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values))
            return false;

        $product = new Product((int)$this->id_product);
        if ($product->getType() == Product::PTYPE_VIRTUAL)
            StockAvailable::setProductOutOfStock((int)$this->id_product, 1, null, (int)$this->id);
        else
            StockAvailable::setProductOutOfStock((int)$this->id_product, StockAvailable::outOfStock((int)$this->id_product), null, $this->id);

        SpecificPriceRule::applyAllRules(array((int)$this->id_product));

        Product::updateDefaultAttribute($this->id_product);

        return true;
    }

    public function update($null_values = false)
    {
        $return = parent::update($null_values);
        Product::updateDefaultAttribute($this->id_product);

        return $return;
    }


    public function setWsProductOptionValues($values)
    {
        $ids_attributes = array();
        foreach ($values as $value)
            $ids_attributes[] = $value['id'];
        return $this->setAttributes($ids_attributes);
    }

    public function getWsProductOptionValues()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'product_attribute_combination` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_product_attribute = '.(int)$this->id);

        return $result;
    }

    public function getWsImages()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.`id_image` as id
			FROM `'._DB_PREFIX_.'product_attribute_image` a
			'.Shop::addSqlAssociation('product_attribute', 'a').'
			WHERE a.`id_product_attribute` = '.(int)$this->id.'
		');
    }

    public function setWsImages($values)
    {
        $ids_images = array();
        foreach ($values as $value)
            $ids_images[] = (int)$value['id'];
        return $this->setImages($ids_images);
    }

    public function getAttributesName($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang='.(int)$id_lang.')
			WHERE pac.id_product_attribute='.(int)$this->id);
    }

    /**
     * This method is allow to know if a Combination entity is currently used
     * @since 1.5.0.1
     * @param $table
     * @param $has_active_column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        return parent::isCurrentlyUsed('product_attribute');
    }

    /**
     * For a given product_attribute reference, returns the corresponding id
     *
     * @param int $id_product
     * @param string $reference
     * @return int id
     */
    public static function getIdByReference($id_product, $reference)
    {
        if (empty($reference))
            return 0;

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->where('pa.reference LIKE \'%'.pSQL($reference).'%\'');
        $query->where('pa.id_product = '.(int)$id_product);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getColorsAttributes()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_attribute
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute a ON (pac.id_attribute = a.id_attribute)
			JOIN '._DB_PREFIX_.'attribute_group ag ON (ag.id_attribute_group = a.id_attribute_group)
			WHERE pac.id_product_attribute='.(int)$this->id.' AND ag.is_color_group = 1
		');
    }

    /**
     * Retreive the price of combination
     *
     * @since 1.5.0
     * @param int $id_product_attribute
     * @return float mixed
     */
    public static function getPrice($id_product_attribute)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT product_attribute_shop.`price`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			'.Shop::addSqlAssociation('product_attribute', 'pa').'
			WHERE pa.`id_product_attribute` = '.(int)$id_product_attribute
        );
    }


}