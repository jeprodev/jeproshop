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

class JeproshopProductModelProduct extends JModelLegacy
{
	/** @var int product id */
	public $product_id;
	
	private $context;
	
	public $product_redirected_id;
	
	/** @var int default category id */
	public $default_category_id;
	
	/** @var int default shop id */
	public $default_shop_id;
	
	/** @var int manufacturer  */
	public $manufacturer_id;
	
	/** @var int supplier  */
	public $supplier_id;
	
	public $lang_id ;
	
	/** @var int  developer id*/
	public $developer_id;
	
	/** @var array shop list id */
	public $shop_list_id;
	
	public $shop_id;
	
	public $name = array();
	
	public $ecotax;
	
	public $unity = null;
	
	public $tax_rules_group_id = 1;
	
	/**
	 * We keep this variable for retro_compatibility for themes
	 * @deprecated 1.5.0
	 */
	public $default_color_id = 0;
	
	public $meta_title = array();
	public $meta_keywords = array();
	public $meta_description = array();
	
	/** @var string Friendly URL */
	public $link_rewrite;
	
	/**
	 * @since 1.5.0
	 * @var boolean Tells if the product uses the advanced stock management
	 */
	public $advanced_stock_management = 0;
	public $out_of_stock;
	public $depends_on_stock;
	
	public $isFullyLoaded = false;
	public $cache_is_pack;
	public $cache_has_attachments;
	public $is_virtual;
	public $cache_default_attribute;
	
	/**
	 * @var string If product is populated, this property contain the rewrite link of the default category
	 */
	public $category;
	
	/** @var string Tax name */
	public $tax_name;
	
	/** @var string Tax rate */
	public $tax_rate;
	
	/** @var DateTime date_add */
	public $date_add;
	
	/** @var DateTime date_upd */
	public $date_upd;
	
	public $manufacturer_name;
	
	public $supplier_name;
	
	public $developer_name;
	
	/** @var string Long description */
	public $description;
	
	/** @var string Short description */
	public $short_description;
	
	/** @var float Price in euros */
	public $price = 0;
	public $base_price = 0;
	
	/** @var float price for product's unity */
	public $unit_price;
	
	/** @var float price for product's unity ratio */
	public $unit_price_ratio = 0;
	
	/** @var float Additional shipping cost */
	public $additional_shipping_cost = 0;
	
	/** @var float Wholesale Price in euros */
	public $wholesale_price = 0;
	
	/** @var boolean on_sale */
	public $on_sale = false;
	
	/** @var boolean online_only */
	public $online_only = false;
	
	/** @var integer Quantity available */
	public $quantity = 0;
	
	/** @var integer Minimal quantity for add to cart */
	public $minimal_quantity = 1;
	
	/** @var string available_now */
	public $available_now;
	
	/** @var string available_later */
	public $available_later;
	
	/** @var string Reference */
	public $reference;
	
	/** @var string Supplier Reference */
	public $supplier_reference;
	
	/** @var string Location */
	public $location;
	
	/** @var string Width in default width unit */
	public $width = 0;
	
	/** @var string Height in default height unit */
	public $height = 0;
	
	/** @var string Depth in default depth unit */
	public $depth = 0;
	
	/** @var string Weight in default weight unit */
	public $weight = 0;
	
	/** @var string Ean-13 barcode */
	public $ean13;
	
	/** @var string Upc barcode */
	public $upc;
	
	/** @var boolean Product status */
	public $quantity_discount = 0;

    public $current_stock;
	
	/** @var boolean Product customization */
	public $customizable;
	
	/** @var boolean Product is new */
	public $is_new = null;
	
	public $uploadable_files;
	
	/** @var int Number of text fields */
	public $text_fields;
	
	/** @var boolean Product status */
	public $published = true;
	
	/** @var boolean Table records are not deleted but marked as deleted if set to true */
	protected $deleted_product = false;
	
	/** @var boolean Product status */
	public $redirect_type = '';
	
	/** @var boolean Product available for order */
	public $available_for_order = true;
	
	/** @var enum Product condition (new, used, refurbished) */
	public $condition;
	
	/** @var boolean Show price of Product */
	public $show_price = true;
	
	/** @var boolean is the product indexed in the search index? */
	public $indexed = 0;
	
	/** @var string Object available order date */
	public $available_date = '0000-00-00';
	
	/** @var string ENUM('both', 'catalog', 'search', 'none') front office visibility */
	public $visibility;
	
	/*** @var array Tags */
	public $tags;
	
	/**
	 * Note:  prefix is "PRODUCT_TYPE" because TYPE_ is used in ObjectModel (definition)
	 */
	const SIMPLE_PRODUCT = 1;
	const PACKAGE_PRODUCT = 2;
	const VIRTUAL_PRODUCT = 3;
	
	const CUSTOMIZE_FILE = 0;
	const CUSTOMIZE_TEXT_FIELD = 1;
	
	public $product_type = self::SIMPLE_PRODUCT;
	
	public static $_taxCalculationMethod = null;
	protected static $_prices = array();
	protected static $_pricesLevel2 = array();
	protected static $_in_category = array();
	protected static $_cart_quantity = array();
	protected static $_tax_rules_group = array();
	protected static $_cacheFeatures = array();
	protected static $_frontFeaturesCache = array();
	protected static $_productPropertiesCache = array();
	
	/** @var array cache stock data in getStock() method */
	protected static $cacheStock = array();
	
	/** definition element */
	public $multiLangShop = true;
	
	public $multiLang = true;
	
	private $pagination;
	
	public function __construct($product_id = NULL, $full = FALSE, $lang_id = NULL, $shop_id = NULL, JeproshopContext $context = NULL){
		$db = JFactory::getDBO();
		
		if($lang_id !== NULL){
			$this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang'));
		}
		
		if($shop_id && $this->isMultiShop()){
			$this->shop_id = (int)$shop_id;
			$this->getShopFromContext = FALSE;
		}
		
		if($this->isMultiShop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		}
		
		if($product_id){
			$cache_id = 'jeproshop_product_model_' . $product_id . '_' . $lang_id . '_' . $shop_id;
			if(!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_product') . " AS product ";
				$where = "";
				/** get language information **/
				if($lang_id){
					$query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON (product.product_id = product_lang.product_id AND product_lang.lang_id = " . (int)$lang_id . ") ";
					if($this->shop_id && !(empty($this->multiLangShop))){
						$where = " AND product_lang.shop_id = " . $this->shop_id;
					}
				}
		
				/** Get shop informations **/
				if(JeproshopShopModelShop::isTableAssociated('product')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_product_shop') . " AS product_shop ON (";
					$query .= "product.product_id = product_shop.product_id AND product_shop.shop_id = " . (int)  $this->shop_id . ")";
				}
				$query .= " WHERE product.product_id = " . (int)$product_id . $where;
		
				$db->setQuery($query);
				$product_data = $db->loadObject();
		
				if($product_data){
					if(!$lang_id && isset($this->multiLang) && $this->multiLang){
						$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_product_lang');
						$query .= " WHERE product_id = " . (int)$product_id;
		
						$db->setQuery($query);
						$product_lang_data = $db->loadObjectList();
						if($product_lang_data){
							foreach ($product_lang_data as $row){
								foreach($row as $key => $value){
									if(array_key_exists($key, $this) && $key != 'product_id'){
										if(!isset($product_data->{$key}) || !is_array($product_data->{$key})){
											$product_data->{$key} = array();
										}
										$product_data->{$key}[$row->lang_id] = $value;
									}
								}
							}
						}
					}
					JeproshopCache::store($cache_id, $product_data);
				}
			}else{
				$product_data = JeproshopCache::retrieve($cache_id);
			}
		
			if($product_data){
				$product_data->product_id = $product_id;
				foreach($product_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		
		if(!$context){
			$context = JeproshopContext::getContext();
		}
		
		if($full && $this->product_id){
			$this->isFullyLoaded = $full;
			$this->manufacturer_name = JeproshopManufacturerModelManufacturer::getNameById((int)$this->manufacturer_id);
			$this->supplier_name = JeproshopSupplierModelSupplier::getNameById((int)$this->supplier_id);
			if($this->getType() == self::VIRTUAL_PRODUCT){
				$this->developer_name = JeproshopDeveloperModelDeveloper::getNameById((int)$this->developer_id);
			}
			$address = NULL;
			if(is_object($context->cart) && $context->cart->{JeproshopSettingModelSetting::getValue('tax_address_type')} != null){
				$address = $context->cart->{JeproshopSettingModelSetting::getValue('tax_address_type')};
			}
		
			$this->tax_rate = $this->getTaxesRate(new JeproshopAddressModelAddress($address));
		
			$this->is_new = $this->isNew();
		
			$this->base_price = $this->price;
		
			$this->price = JeproshopProductModelProduct::getStaticPrice((int)$this->product_id, false, null, 6, null, false, true, 1, false, null, null, null, $this->specific_price);
			$this->unit_price = ($this->unit_price_ratio != 0 ? $this->price / $this->unit_price_ratio : 0);
			if($this->product_id){
				$this->tags = JeproshopTagModelTag::getProductTags((int)$this->product_id);
			}
			$this->loadStockData();
		}
		
		if($this->default_category_id){
			$this->category = JeproshopCategoryModelCategory::getLinkRewrite((int)$this->default_category_id, (int)$lang_id);
		}
	}
	
	public function getType(){
		if(!$this->product_id){
			return JeproshopProductModelProduct::SIMPLE_PRODUCT;
		}
	
		if(JeproshopProductPack::isPack($this->product_id)){
			return JeproshopProductModelProduct::PACKAGE_PRODUCT;
		}
	
		if($this->is_virtual){
			return JeproshopProductModelProduct::VIRTUAL_PRODUCT;
		}
		return JeproshopProductModelProduct::SIMPLE_PRODUCT;
	}
	
	public function isMultiShop(){
		return (JeproshopShopModelShop::isTableAssociated('product') || !empty($this->multiLangShop));
	}
	
	public function isNew(){
		$db = JFactory::getDBO();
		$query = "SELECT product.product_id FROM " . $db->quoteName('#__jeproshop_product') . " AS product ";
		$query .= JeproshopShopModelShop::addSqlAssociation('product') . " WHERE product.product_id = " . (int)$this->product_id;
		$query .= " AND DATEDIFF(product_shop." . $db->quoteName('date_add') . ", DATE_SUB(NOW(), INTERVAL " ;
		$query .= (JeproshopTools::isUnsignedInt(JeproshopSettingModelSetting::getValue('nb_days_product_new')) ? JeproshopSettingModelSetting::getValue('nb_days_product_new') : 20);
		$query .= " DAY) ) > 0";
	
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return count($result) > 0;
	}
	
	public function getCustomizationFieldIds(){
		if (!JeproshopCustomization::isFeaturePublished()){ return array(); }
		return Db::getInstance()->executeS('
			SELECT `id_customization_field`, `type`, `required`
			FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)$this->id);
	}
	
	public function getCustomizationFields($lang_id = false){
		if (!JeproshopCustomization::isFeaturePublished())
			return false;
	
		if (!$result = Db::getInstance()->executeS('
			SELECT cf.`id_customization_field`, cf.`type`, cf.`required`, cfl.`name`, cfl.`id_lang`
			FROM `'._DB_PREFIX_.'customization_field` cf
			NATURAL JOIN `'._DB_PREFIX_.'customization_field_lang` cfl
			WHERE cf.`id_product` = '.(int)$this->id.($lang_id ? ' AND cfl.`id_lang` = '.(int)$lang_id : '').'
			ORDER BY cf.`id_customization_field`'))
					return false;
	
				if ($id_lang)
					return $result;
	
				$customization_fields = array();
				foreach ($result as $row)
					$customization_fields[(int)$row['type']][(int)$row['id_customization_field']][(int)$row['id_lang']] = $row;
	
				return $customization_fields;
	}
	
	
	/**
	 * Check if product has attributes combinations
	 *
	 * @return integer Attributes combinations number
	 */
	public function hasAttributes(){
		if (!JeproshopCombinationModelCombination::isFeaturePublished()){ return 0; }
		
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM " .$db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute ";
		$query .= JeproshopShopModelShop::addSqlAssociation('product_attribute') . " WHERE ";
		$query .= "product_attribute." . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getCombinationImages($lang_id){
		if (!JeproshopCombinationModelCombination::isFeaturePublished()){ return false; }
	
		$db = JFactory::getDBO();
		 
		$query = "SELECT " . $db->quoteName('product_attribute_id') . " FROM " . $db->quoteName('#__jeproshop_product_attribute');
		$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		$db->setQuery($query);
		$product_attributes = $db->loadObjectList();
	
		if (!$product_attributes)
			return false;
	
		$ids = array();
	
		foreach ($product_attributes as $product_attribute){
			$ids[] = (int)$product_attribute->product_attribute_id;
		}
		
		$query = "SELECT product_attribute_image." . $db->quoteName('image_id') . ", product_attribute_image." . $db->quoteName('product_attribute_id');
		$query .= ", image_lang." . $db->quoteName('legend') . " FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " AS product_attribute_image";
		$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_image_lang') . " AS image_lang ON (image_lang." . $db->quoteName('image_id') . " = product_attribute_image.";
		$query .= $db->quoteName('image_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_image') . " AS image ON (image." . $db->quoteName('image_id');
		$query .= " = product_attribute_image." . $db->quoteName('image_id') . ") WHERE product_attribute_image." . $db->quoteName('product_attribute_id');
		$query .= " IN (" .implode(', ', $ids). ") AND image_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . " ORDER by image." . $db->quoteName('position');
		
		$db->setQuery($query); 
		$result = $db->loadObjectList();
	
		if (!$result)
			return false;
	
		$images = array();
	
		foreach ($result as $row)
			$images[$row['id_product_attribute']][] = $row;
	
		return $images;
	}

    public static function addCustomizationPrice(&$products, &$customized_datas){
        if (!$customized_datas)
            return;

        foreach ($products as &$product_update)
        {
            if (!JeproshopCustomization::isFeaturePublished())
            {
                $product_update['customizationQuantityTotal'] = 0;
                $product_update['customizationQuantityRefunded'] = 0;
                $product_update['customizationQuantityReturned'] = 0;
            }
            else
            {
                $customization_quantity = 0;
                $customization_quantity_refunded = 0;
                $customization_quantity_returned = 0;

                /* Compatibility */
                $product_id = (int)(isset($product_update['id_product']) ? $product_update['id_product'] : $product_update['product_id']);
                $product_attribute_id = (int)(isset($product_update['id_product_attribute']) ? $product_update['id_product_attribute'] : $product_update['product_attribute_id']);
                $id_address_delivery = (int)$product_update['id_address_delivery'];
                $product_quantity = (int)(isset($product_update['cart_quantity']) ? $product_update['cart_quantity'] : $product_update['product_quantity']);
                $price = isset($product_update['price']) ? $product_update['price'] : $product_update['product_price'];
                if (isset($product_update['price_wt']) && $product_update['price_wt'])
                    $price_wt = $product_update['price_wt'];
                else
                    $price_wt = $price * (1 + ((isset($product_update['tax_rate']) ? $product_update['tax_rate'] : $product_update['rate']) * 0.01));

                if (!isset($customized_datas[$product_id][$product_attribute_id][$id_address_delivery]))
                    $id_address_delivery = 0;
                if (isset($customized_datas[$product_id][$product_attribute_id][$id_address_delivery])){
                    foreach ($customized_datas[$product_id][$product_attribute_id][$id_address_delivery] as $customization){
                        $customization_quantity += (int)$customization->quantity;
                        $customization_quantity_refunded += (int)$customization->quantity_refunded;
                        $customization_quantity_returned += (int)$customization->quantity_returned;
                    }
                }

                $product_update['customizationQuantityTotal'] = $customization_quantity;
                $product_update['customizationQuantityRefunded'] = $customization_quantity_refunded;
                $product_update['customizationQuantityReturned'] = $customization_quantity_returned;

                if ($customization_quantity)
                {
                    $product_update['total_wt'] = $price_wt * ($product_quantity - $customization_quantity);
                    $product_update['total_customization_wt'] = $price_wt * $customization_quantity;
                    $product_update['total'] = $price * ($product_quantity - $customization_quantity);
                    $product_update['total_customization'] = $price * $customization_quantity;
                }
            }
        }
    }

    /**
     * Update a table and splits the common datas and the shop datas
     *
     * @since 1.5.0
     * @param array $data
     * @param string $where
     * @param string $specific_where Only executed for common table
     * @param bool $update_shop
     * @return bool
     */
	 public static function updateMultishopTable($data, $where = '', $specific_where = '', $update_shop = FALSE){	 
	 	$db = JFactory::getDBO();
	 	$return = true;
	 	$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " AS product " . $data . $where . $specific_where;
	 	$db->setQuery($query);
	 	$return &= $db->query();
	 	if($update_shop){
	 		$query = "UPDATE " . $db->quoteName('#__jeproshop_product_shop') . " AS product " . $data . $where . $specific_where;
	 		$db->setQuery($query);
	 		$return &= $db->query();
	 	}
	 	return $return;
	 	
	 }
	
	/**
	 * Fill the variables used for stock management
	 */
	public function loadStockData(){
		if (JeproshopTools::isLoadedObject($this, 'product_id')){
			// By default, the product quantity correspond to the available quantity to sell in the current shop
			$this->quantity = JeproshopStockAvailableModelStockAvailable::getQuantityAvailableByProduct($this->product_id, 0);
			$this->out_of_stock = JeproshopStockAvailableModelStockAvailable::outOfStock($this->product_id);
			$this->depends_on_stock = JeproshopStockAvailableModelStockAvailable::dependsOnStock($this->product_id);
			if (JeproshopContext::getContext()->shop->getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP && JeproshopContext::getContext()->shop->getContextShopGroup()->share_stock == 1){
				$this->advanced_stock_management = $this->useAdvancedStockManagement();
			}
		}
	}
	
	public function getAttributesGroups($lang_id){
		if(!JeproshopCombinationModelCombination::isFeaturePublished()){ return array(); }
	
		$db = JFactory::getDBO();
		$query = "SELECT attribute_group." . $db->quoteName('attribute_group_id') . ", attribute_group." . $db->quoteName('is_color_group');
		$query .= ", attribute_group_lang." . $db->quoteName('name') . " AS group_name, attribute_group_lang." . $db->quoteName('public_name');
		$query .= " AS public_group_name, attribute." . $db->quoteName('attribute_id') . ", attribute_lang." . $db->quoteName('name') . " AS ";
		$query .= "attribute_name, attribute." . $db->quoteName('color') . " AS attribute_color, product_attribute_shop." . $db->quoteName('product_attribute_id');
		$query .= ", IFNULL(stock.quantity, 0) AS quantiy, product_attribute_shop." . $db->quoteName('price') .  ", product_attribute_shop.";
		$query .= $db->quoteName('ecotax') . ", product_attribute_shop." . $db->quoteName('weight') . ", product_attribute_shop." . $db->quoteName('default_on');
		$query .= ", product_attribute." . $db->quoteName('reference') . ", product_attribute_shop." .  $db->quoteName('unit_price_impact');
		$query .= ", product_attribute_shop." . $db->quoteName('minimal_quantity') . ", product_attribute_shop." .  $db->quoteName('available_date');
		$query .= ", attribute_group." .  $db->quoteName('group_type') . " FROM " .  $db->quoteName('#__jeproshop_product_attribute') . " AS ";
		$query .= " product_attribute " . JeproshopShopModelShop::addSqlAssociation('product_attribute'). JeproshopProductModelProduct::sqlStock('product_attribute');
		$query .= " LEFT JOIN " .  $db->quoteName('#__jeproshop_product_attribute_combination') . " AS  product_attribute_combination ON ( product_attribute_combination.";
		$query .=  $db->quoteName('product_attribute_id') . " = product_attribute." . $db->quoteName('product_attribute_id') . ") LEFT JOIN " .  $db->quoteName('#__jeproshop_attribute');
		$query .= " AS attribute ON ( attribute." . $db->quoteName('attribute_id') . " = product_attribute_combination." .  $db->quoteName('attribute_id') . " ) LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group ON ( attribute_group." . $db->quoteName('attribute_group_id') . " = attribute.";
		$query .= $db->quoteName('attribute_group_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_lang') . " AS attribute_lang ON ( attribute." . $db->quoteName('attribute_id');
		$query .= " = attribute_lang." . $db->quoteName('attribute_id') . " ) LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang') . " AS attribute_group_lang ON ( attribute_group.";
		$query .= $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id') . ") " . JeproshopShopModelShop::addSqlAssociation('attribute');
		$query .= " WHERE product_attribute." . $db->quoteName('product_id') . " = " . (int)$this->product_id . " AND attribute_lang." . $db->quoteName('lang_id') . " = ". (int)$lang_id ;
		$query .= " AND attribute_group_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . " GROUP BY attribute_group_id, product_attribute_id ORDER BY attribute_group.";
		$query .= $db->quoteName('position') . " ASC, attribute." . $db->quoteName('position') . " ASC, attribute_group_lang." . $db->quoteName('name') . " ASC";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    /**
     * Get all available product attributes resume
     *
     * @param integer $lang_id Language id
     * @param string $attribute_value_separator
     * @param string $attribute_separator
     * @return array Product attributes combinations
     */
	public function getAttributesResume($lang_id, $attribute_value_separator = ' - ', $attribute_separator = ', '){
		if (!JeproshopCombinationModelCombination::isFeaturePublished()){ return array(); }
		$add_shop = '';
		
		$db = JFactory::getDBO();
		
		$query = "SELECT product_attribute.*, product_attribute_shop.* FROM " . $db->quoteName('#__jeproshop_product_attribute');
		$query .= " AS product_attribute " . JeproshopShopModelShop::addSqlAssociation('product_attribute') . "	WHERE product_attribute.";
		$query .= $db->quoteName('product_id') . " = " .(int)$this->product_id . " GROUP BY product_attribute." . $db->quoteName('product_attribute_id');
		
		$db->setQuery($query);
		$combinations = $db->loadObjectList(); 
	
		if (!$combinations){ return false; }
	
		$product_attributes = array();
		foreach ($combinations as $combination){
			$product_attributes[] = (int)$combination->product_attribute_id;
		}
		$query = "SELECT product_attribute_combination.product_attribute_id, GROUP_CONCAT(attribute_group_lang." . $db->quoteName('name') . ", ";
		$query .= $db->quote($attribute_value_separator) . ",attribute_lang." . $db->quoteName('name') . " ORDER BY attribute_group_lang.";
		$query .= $db->quoteName('attribute_group_id') . " SEPARATOR " . $db->quote($attribute_separator).") as attribute_designation FROM ";
		$query .= $db->quoteName('#__jeproshop_product_attribute_combination') . " AS product_attribute_combination LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_attribute') . " AS attribute ON attribute." . $db->quoteName('attribute_id') . " = product_attribute_combination.";
		$query .= $db->quoteName('attribute_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group ON attribute_group.";
		$query .= $db->quoteName('attribute_group_id') . " = attribute." . $db->quoteName('attribute_group_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_lang');
		$query .= " AS attribute_lang ON (attribute." . $db->quoteName('attribute_id') . " = attribute_lang." . $db->quoteName('attribute_id') . " AND attribute_lang.";
		$query .= $db->quoteName('lang_id') . " = " .(int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang');
		$query .= " AS attribute_group_lang ON (attribute_group." . $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id');
		$query .= " AND attribute_group_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE product_attribute_combination.product_attribute_id IN (";
		$query .= implode(',', $product_attributes).") GROUP BY product_attribute_combination.product_attribute_id";
		
		$db->setQuery($query);
		$lang = $db->loadObjectList();
	
		foreach ($lang as $k => $row)
			$combinations[$k]->attribute_designation = $row->attribute_designation;
			

		//Get quantity of each variations
		foreach ($combinations as $key => $row){
			$cache_key = $row->product_id.'_'.$row->product_attribute_id.'_quantity';
	
			if (!JeproshopCache::isStored($cache_key))
				JeproshopCache::store(
						$cache_key,
					JeproshopStockAvailableModelStockAvailable::getQuantityAvailableByProduct($row->product_id, $row->product_attribute_id)
				);
	
			$combinations[$key]->quantity = JeproshopCache::retrieve($cache_key);
		}
	
		return $combinations;
	}
	
	public function isAssociatedToShop($shop_id = NULL){
		if($shop_id === NULL){
			$shop_id = (int)JeproshopContext::getContext()->shop->shop_id;
		}
	
		$cache_id = 'jeproshop_shop_model_product_' . (int)$this->product_id . '_' . (int)$this->shop_id;
		if(!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
			$query = "SELECT shop_id FROM " . $db->quoteName('#__jeproshop_product_shop') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
			$query .= " AND shop_id = " . (int)$shop_id;
	
			$db->setQuery($query);
			$result = (bool)$db->loadResult();
			JeproshopCache::store($cache_id, $result);
		}
		return JeproshopCache::retrieve($cache_id);
	}
	
	/**
	 *
	 * @param JeproshopAddressModelAddress $address
	 * @return the total taxes rate applied to the product
	 */
	public function getTaxesRate(JeproshopAddressModelAddress $address = null){
		if(!$address || $address->country_id){
			$address = JeproshopAddressModelAddress::initialize();
		}
	
		$tax_manager = JeproshopTaxManagerFactory::getManager($address, $this->tax_rules_group_id);
		$tax_calculator = $tax_manager->getTaxCalculator();
	
		return $tax_calculator->getTotalRate();
	}

    /**
     * Get product accessories (only names)
     *
     * @param integer $lang_id Language id
     * @param integer $product_id Product id
     * @param JeproshopContext $context
     * @return array Product accessories
     */
	public static function getAccessoriesLight($lang_id, $product_id, JeproshopContext $context = null){
		if (!$context){
			$context = JeproshopContext::getContext();
		}
		$db = JFactory::getDBO();
		
		$query = "SELECT product." . $db->quoteName('product_id') . ", product." . $db->quoteName('reference');
		$query .= ", product_lang." . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_accessory');
		$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_product') . " AS product ON (product.";
		$query .= $db->quoteName('product_id') . " = " . $db->quoteName('product_2_id') . ") " . JeproshopShopModelShop::addSqlAssociation('product');
		$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON ( product.";
		$query .= $db->quoteName('product_id') . " = product_lang." . $db->quoteName('product_id') . " AND product_lang.";
		$query .= $db->quoteName('lang_id') ." = " .(int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang');
		$query .= ") WHERE " . $db->quoteName('product_1_id') . " = " .(int)$product_id;
		
		$db->setQuery($query);		
	
		return $db->loadObjectList();
	}

    /**
     * @param type $product_alias
     * @param int|\type $product_attribute
     * @param bool|\type $inner_join
     * @param JeproshopShopModelShop $shop
     * @return string
     */
	public static function sqlStock($product_alias, $product_attribute = 0, $inner_join = FALSE, JeproshopShopModelShop $shop = NULL){
		$db = JFactory::getDBO();
		$shop_id = ($shop !== NULL ? (int)$shop->shop_id : NULL);
		$query = (( $inner_join) ? " INNER " : " LEFT ") . "JOIN " . $db->quoteName('#__jeproshop_stock_available');
		$query .= " stock ON(stock.product_id = " . $db->escape($product_alias) . ".product_id";
	
		if(!is_null($product_attribute)){
			if(!JeproshopCombinationModelCombination::isFeaturePublished()){
				$query .= " AND stock.product_attribute_id = 0";
			}elseif(is_numeric($product_attribute)){
				$query .= " AND stock.product_attribute_id = " . $product_attribute;
			}elseif (is_string($product_attribute)) {
				$query .= " AND stock.product_attribute_id = IFNULL(" . $db->quoteName($db->escape($product_attribute)) . ".product_attribute_id, 0)";
			}
		}
		$query .=  JeproshopStockAvailableModelStockAvailable::addShopRestriction($shop_id, 'stock') . ")";
	
		return $query;
	}
	
	/**
	 * Check if a field is edited (if the checkbox is checked)
	 * This method will do something only for multishop with a context all / group
	 *
	 * @param string $field Name of field
	 * @param int $lang_id
	 * @return bool
	 */
	 protected function isProductFieldUpdated($field, $lang_id = null) {
         // Cache this condition to improve performances
         static $is_activated = null;
         if (is_null($is_activated)) {
             $is_activated = JeproshopShopModelShop::isFeaturePublished() && JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_SHOP && $this->product_id;
         }

         if (!$is_activated) {
             return true;
         }

         $app = JFactory::getApplication();
         $data = JRequest::get('post');
         $input_data = $data['jform'];

         if (is_null($lang_id)) {
             return !empty($input_data[$field]);
         } else {
             return !empty($input_data[$field][$lang_id]);
         }
     }

    /**
     * Gets the name of a given product, in the given lang
     *
     * @since 1.5.0
     * @param $product_id
     * @param null $product_attribute_id
     * @param null $lang_id     
     * @return string
     */
	public static function getProductName($product_id, $product_attribute_id = null, $lang_id = null){
		// use the lang in the context if $id_lang is not defined
		if (!$lang_id){
			$lang_id = (int)JeproshopContext::getContext()->language->lang_id;
		}
		// creates the query object
		$db = JFactory::getDBO();
	
		// selects different names, if it is a combination
		if ($product_attribute_id){
			$query = "SELECT IFNULL(CONCAT(product_lang.name, ' : ', GROUP_CONCAT(DISTINCT attribute_group_lang.";
			$query .= $db->quoteNam('name') . ", ' - ', attribute_lang.name SEPARATOR ', ')),product_lang.name) AS name FROM ";
		}else{
			$query = "SELECT DISTINCT product_lang.name AS name FROM ";
		}
		// adds joins & where clauses for combinations
		if($product_attribute_id){
			$query .= $db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute ";
			$query .= JeproshopShopModelShop::addSqlAssociation('product_attribute');
			$query .= $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON(product_lang.product_id = product_attribute.";
			$query .= "product_id AND product_lang.lang_id = " . (int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang');
			$query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_product_attribute_combination') . " AS product_attribute_combination ON (";
			$query .= "product_attribute_combination.product_attribute_id = product_attribute.product_attribute_id) LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_attribute') . " AS attribute ON (attribute.attribute_id = product_attribute_combination.attribute_id)";
			$query .= $db->quoteName('#__jeproshop_attribute_lang') . " AS attribute_lang ON (attribute_lang.attribute_id = attribute.attribute_id AND ";
			$query .= " attribute_lang.lang_id = " .(int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang');
			$query .= " AS attribute_group_lang ON(attribute_group_lang.attribute_group_id = attribute.attribute_group_id AND attribute_group_lang.lang_id = ";
			$query .= (int)$lang_id . " WHERE product_attribute.product_id = ".(int)$product_id ." AND product_attribute.product_attribute_id = ".(int)$product_attribute_id;
		}
		else // or just adds a 'where' clause for a simple product
		{
			$query .= $db->quoteName('#__jeproshop_product_lang') . " AS product_lang WHERE product_lang.product_id = " . (int)$product_id . " AND product_lang.";
			$query .= "lang_id = " . (int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang');
		}
	
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * get the default category according to the shop
	 */
	public function getDefaultCategoryId(){
		$db = JFactory::getDBO();
		
		$query = "SELECT product_shop." . $db->quoteName('default_category_id') . " FROM " . $db->quoteName('#__jeproshop_product') . " AS product " ;
		$query .= JeproshopShopModelShop::addSqlAssociation('product') . " WHERE product." . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		
		$db->setQuery($query);
		$default_category_id = $db->loadResult();
	
		if (!$default_category_id){
			return JeproshopContext::getContext()->shop->category_id;
		}else{
			return $default_category_id;
		}
	}
	
	/*
	 ** Customization management
	*/
	public static function getAllCustomizedDatas($cart_id, $lang_id = null, $only_in_cart = true){
		if (!JeproshopCustomization::isFeaturePublished()){ return false; }
	
		// No need to query if there isn't any real cart!
		if (!$cart_id){ return false; }
		if (!$lang_id){	$lang_id = JeproshopContext::getContext()->language->lang_id; }
		
		$db = JFactory::getDBO();
	
		$query = "SELECT customized_data." . $db->quoteName('customization_id') . ", customization." . $db->quoteName('address_delivery_id');
		$query .= ", customization." . $db->quoteName('product_id') . ", customization_field_lang." . $db->quoteName('customization_field_id');
		$query .= ", customization." . $db->quoteName('product_attribute_id') . ", customized_data." . $db->quoteName('type') . ", ";
		$query .= "customized_data." . $db->quoteName('index') . ", customized_data." . $db->quoteName('value') . ", ";
		$query .= "customization_field_lang." . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_customized_data') . " AS ";
		$query .= " customized_data NATURAL JOIN " . $db->quoteName('#__jeproshop_customization') . " AS customization LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_customization_field_lang') . " AS customization_field_lang ON (customization_field_lang.";
		$query .= "customization_field_id = customized_data." . $db->quoteName('index') . " AND lang_id = " .(int)$lang_id . ") WHERE ";
		$query .= "customization." . $db->quoteName('cart_id') . " = " . (int)$cart_id . ($only_in_cart ? " AND customization." .$db->quoteName('in_cart') . " = 1"  : "");
		$query .= " ORDER BY " . $db->quoteName('product_id'). ", " . $db->quoteName('product_attribute_id') . ", " . $db->quoteName('type') . ", " . $db->quoteName('index');
		
		$db->seQuery($query);
		$result = $db->loadObjectList();
		
		if (!$result){ return false; }
	
		$customized_datas = array();
	
		foreach ($result as $row){
			$customized_datas[(int)$row->product_id][(int)$row->product_attribute_id][(int)$row->address_delivery_id][(int)$row->customization_id]['datas'][(int)$row->type][] = $row;
		}

		$query = "SELECT " . $db->quoteName('product_id') . ", " . $db->quoteName('product_attribute_id') . ", " . $db->quoteName('customization_id');
		$query .= ", " . $db->quoteName('address_delivery_id') . ", " . $db->quoteName('quantity') . ", " . $db->quoteName('quantity_refunded') . ", ";
		$query .= $db->quoteName('quantity_returned') . " FROM " . $db->quoteName('#__jeproshop_customization') . " WHERE " . $db->quoteName('cart_id');
		$query .= " = " . (int)($cart_id) . ($only_in_cart ? " AND " . $db->quoteName('in_cart') . " = 1"  : ""); 
		
		$db->seQuery($query);
		$result = $db->loadObjectList();
		if (!$result ){ return false; }
	
		foreach ($result as $row){
			$customized_datas[(int)$row->product_id][(int)$row->product_attribute_id][(int)$row->address_delivery_id][(int)$row->customization_id]['quantity'] = (int)$row->quantity;
			$customized_datas[(int)$row->product_id][(int)$row->product_attribute_id][(int)$row->address_delivery_id][(int)$row->customization_id]['quantity_refunded'] = (int)$row->quantity_refunded;
			$customized_datas[(int)$row->product_id][(int)$row->product_attribute_id][(int)$row->address_delivery_id][(int)$row->customization_id]['quantity_returned'] = (int)$row->quantity_returned;
		}
	
		return $customized_datas;
	}
	
	function getProductList(JeproshopContext $context = NULL){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');

        if(!$context){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
        $shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
        $category_id = $app->getUserStateFromRequest($option. $view. '.cat_id', 'cat_id', 0, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
        $published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
        $product_attribute_id = $app->getUserStateFromRequest($option. $view. '.product_attribute_id', 'product_attribute_id', 0, 'int');


        if(JeproshopShopModelShop::isFeaturePublished() && $context->cookie->products_filter_category_id){
            $category = new JeproshopCategoryModelCategory((int)$context->cookie->products_filter_category_id);
            if(!$category->inShop()){
                $context->cookie->products_filter_category_id = null;
                $app->redirect('index.php?option=com_jeproshop&view=product');
            }
        }

        //Join categories table
        $category_id = (int)$app->input->get('product_filter_category_lang!name');
        if($category_id){
            $category = new JeproshopCategoryModelCategory($category_id);
            $app->input->set('product_filter_category_lang!name', $category->name[$context->language->lang_id]);
        }else {
            $category_id = $app->input->get('category_id');
            $current_category_id = null;
            if ($category_id) {
                $current_category_id = $category_id;
                $context->cookie->products_filter_category_id = $category_id;
            } elseif ($category_id = $context->cookie->products_filter_category_id) {
                $current_category_id = $category_id;
            }

            if ($current_category_id) {
                $category = new JeproshopCategoryModelCategory((int)$current_category_id);
            } else {
                $category = new JeproshopCategoryModelCategory();
            }
        }
        $join_category = false;
        if(JeproshopTools::isLoadedObject($category, 'category_id') && empty($filter)){
            $join_category = true;
        }

        $shop_id = (JeproshopShopModelShop::isFeaturePublished() && JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP) ? (int)$this->context->shop->shop_id  : "product." . $db->quoteName('default_shop_id');

        $join = " LEFT JOIN " . $db->quoteName('#__jeproshop_image') . " AS image ON (image." . $db->quoteName('product_id') . " = product.";
        $join .= $db->quoteName('product_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_stock_available') . " AS stock_available ON (stock_available.";
        $join .= $db->quoteName('product_id') . " = product." . $db->quoteName('product_id') . " AND stock_available." . $db->quoteName('product_attribute_id');
        $join .= " = 0 " . JeproshopStockAvailableModelStockAvailable::addShopRestriction(null, 'stock_available') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_product_shop');
        $join .= " AS product_shop ON (product." . $db->quoteName('product_id') . " = product_shop." . $db->quoteName('product_id'). " AND product_shop." . $db->quoteName('shop_id');
        $join .= " = " . $shop_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_category_lang') . " AS category_lang ON (product_shop." . $db->quoteName('default_category_id');
        $join .= " = category_lang." . $db->quoteName('category_id') . " AND product_lang." . $db->quoteName('lang_id') . " = category_lang." . $db->quoteName('lang_id') . " AND category_lang.";
        $join .= $db->quoteName('shop_id') . " = " . $shop_id . ") LEFT JOIN  " . $db->quoteName('#__jeproshop_shop') . " AS shop ON (shop." . $db->quoteName('shop_id') . " = " . $shop_id;
        $join .= ")	LEFT JOIN  " . $db->quoteName('#__jeproshop_image_shop') . " AS image_shop ON (image_shop." . $db->quoteName('image_id') . " = image." . $db->quoteName('image_id');
        $join .= " AND image_shop." . $db->quoteName('cover') . " = 1 AND image_shop." . $db->quoteName('shop_id') . " = " . $shop_id . ") LEFT JOIN  " . $db->quoteName('#__jeproshop_product_download');
        $join .= " AS product_download ON (product_download." . $db->quoteName('product_id') . " = product." . $db->quoteName('product_id') . ") ";

        $select = "shop." . $db->quoteName('shop_name') . " AS shop_name, product." . $db->quoteName('default_shop_id') . ", MAX(image_shop." . $db->quoteName('image_id') . ") AS image_id, category_lang.";
        $select .= $db->quoteName('name') . " AS category_name, product_shop." . $db->quoteName('price') . ", 0 AS final_price, product." . $db->quoteName('is_virtual') . ", product_download.";
        $select .= $db->quoteName('nb_downloadable') . ", stock_available." . $db->quoteName('quantity') . " AS stock_available_quantity, product_shop." . $db->quoteName('published');
        $select .= ", IF(stock_available." . $db->quoteName('quantity') . " <= 0, 1, 0) badge_danger";

        if($join_category){
            $join .= " INNER JOIN " .  $db->quoteName('#__jeproshop_category_product') . " product_category ON (product_category." .  $db->quoteName('product_id') . " = product.";
            $join .=  $db->quoteName('product_id') . " AND product_category." .  $db->quoteName('category_id') . " = " . (int)$category->category_id .") ";
            $select .= " , product_category." .  $db->quoteName('position') . ", ";
        }

        $group = " GROUP BY product_shop." . $db->quoteName('product_id');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        // Add SQL shop restriction
        $select_shop = $join_shop = $where_shop = $where = $filter = "";
        if ($context->controller->shopLinkType){
            $select_shop = ", shop.shop_name AS shopname ";
            $join_shop = ") LEFT JOIN " . $db->quoteName('#__jeproshop_shop') . " AS shop ON product.shop_id = shop.shop_id";
            $where_shop = JeproshopShopModelShop::addSqlRestriction($this->shopShareDatas, 'product'); //, $this->shopLinkType
        }

        /* Query in order to get results with all fields */
        $lang_join = '';
        if ($lang_id){
            $lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON (product_lang.";
            $lang_join .= $db->quoteName('product_id') . " = product." . $db->quoteName('product_id') . " AND product_lang.";
            $lang_join .= $db->quoteName('lang_id') . " = " . (int)$lang_id;

            if (!JeproshopShopModelShop::isFeaturePublished()) {
                $lang_join .= " AND product_lang." . $db->quoteName('shop_id') . " = 1";
            }elseif (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP) {
                //$lang_join .= " AND product_lang." . $db->quoteName('shop_id') . " = " . (int)$shop_lang_id;
            }else{
                    $lang_join .= " AND product_lang." . $db->quoteName('shop_id') ." = product.default_shop_id";
            }

            $lang_join .= ")";
        }

        if ($context->controller->multishop_context && JeproshopShopModelShop::isTableAssociated('product')){
            if (JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()){
                $test_join = !preg_match('/`?'.preg_quote('#__jeproshop_product_shop').'`? *product_shop/', $join);
                if (JeproshopShopModelShop::isFeaturePublished() && $test_join && JeproshopShopModelShop::isTableAssociated('product')){
                    $where .= " AND product.product_id IN (	SELECT product_shop.product_id FROM " . $db->quoteName('#__jeproshop_product_shop');
                    $where .= " AS product_shop WHERE product_shop.shop_id IN (";
                    $where .= implode(', ', JeproshopShopModelShop::getContextListShopIds())."))";
                }
            }
        }

        $having_clause = '';
        if (isset($filterHaving) || isset($having)){
            $having_clause = " HAVING ";
            if (isset($filterHaving)){
                $having_clause .= ltrim($filterHaving, " AND ");
            }
            if (isset($having)){ $having_clause .= $having . " "; }
        }
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS product." . $db->quoteName('product_id') . ", product_lang." . $db->quoteName('name') . ", product.";
            $query .= $db->quoteName('reference') . ", " . $select . $select_shop . " FROM " . $db->quoteName('#__jeproshop_product') . " AS product ";
            $query .= $lang_join . $join . $join_shop . " WHERE 1 " . $where . $filter .$where_shop . $group . $having_clause . " ORDER BY ";
            $query .= ((str_replace('`', '', $order_by) == 'product_id') ? "product." : " product.") . $db->quoteName($order_by) . " " . $db->escape($order_way);

			
			$db->setQuery($query);
			$total = count($db->loadObjectList());
			
			$query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
		
			$db->setQuery($query);
			$products = $db->loadObjectList();
			
			if($use_limit == true){
				$limit_start = (int)$limit_start -(int)$limit;
				if($limit_start < 0){ break; }
			}else{ break; }				
		}while(empty($products));
		
		$this->pagination = new JPagination($total, $limit_start, $limit);
		return $products;
	}
	
	public function getPagination(){
		return $this->pagination;
	}
	
	protected function removeTaxFromEcotax(){
		$app = JFactory::getApplication();
		$ecotaxTaxRate = JeproshopTaxModelTax::getProductEcotaxRate();
		$ecotax = $app->input->get('ecotax');
		if($ecotax){
			$app->input->set('ecotax', JeproshopTools::roundPrice($ecotax / (1+ $ecotaxTaxRate/100), 6));
		}
	}
	
	public function saveProduct(){
		$input = JRequest::get('post');
		$db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $languages = JeproshopLanguageModelLanguage::getLanguages(false);

		if($this->context == null){ $this->context = JeproshopContext::getContext(); }
		
		$input_data = $input['jform'];
		
		$this->date_add = date('Y-m-d H:i:s');
		$this->date_upd = date('Y-m-d H:i:s');

        $is_virtual = ($input_data['product_type'] == JeproshopProductModelProduct::VIRTUAL_PRODUCT) ? 1 : 0;

        if($is_virtual && $input_data['product_type'] != 2){

        }
        $this->checkProduct();
        $this->removeTaxFromEcotax();

        $product = $this->addProduct();
        if(JeproshopTools::isLoadedObject($product, 'product_id')){
            $product->addCarriers();
            $product->updateAccessories();
            $product->updatePackItems();
            $product->updateDownloadProduct();

            if(JeproshopSettingModelSetting::getValue('use_advanced_stock_management_on_new_product') && JeproshopSettingModelSetting::getValue('advanced_stock_management')){
                $product->advanced_stock_management = 1;
                JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock($product->product_id, true, (int)$this->context->shop->shop_id, 0);
                $product->updateProduct();
            }

            if(!$this->context->controller->has_errors){
                /*if($product->isProductFieldUpdated('category_box') && !$product->updateCategories($categories)){
                    $this->context->controller->has_errors = true;
                    JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_LINKING_THE_OBJECT_MESSAGE') . ' <b>' . JText::_('COM_JEPROSHOP_TO_CATEGORIES_LABEL') . '</b>');
                }elseif(!$this->updateTags($languages)){
                    $this->context->controller->has_errors = true;
                    JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_ADDING_TAGS_MESSAGE'));
                }else{
                    if(in_array($product->visibility, array('both', 'search')) && JeproshopSettingModelSetting::getValue('search_indexation')){
                        JeproshopSearch::indexation(false, $product->product_id);
                    }
                }*/

                if(JeproshopSettingModelSetting::getValue('default_warehouse_new_product') != 0 && JeproshopSettingModelSetting::getValue('advanced_stock_management')){
                    $warehouseLocationEntity = new JeproshopWarehouseProductLocationModelWarehouseProductLocation();
                    $warehouseLocationEntity->product_id = $this->product_id;
                    $warehouseLocationEntity->product_attribute_id = 0;
                    $warehouseLocationEntity->warehouse_id = JeproshopSettingModelSetting::getValue('default_warehouse_new_product');
                    $warehouseLocationEntity->location = $db->quote('');
                    $warehouseLocationEntity->save();
                }

                // Save and Preview
                if($app->input->get('preview')){
                    $link = 'index.php?option=com_jeproshop&view=product';
                    $message = '';
                }

                if($app->input->get('task') == 'edit'){
                    $link = 'index.php?option=com_jeproshop&view=product&task=update&product_id=' . (int)$this->product_id;
                    $category_id = $app->input->get('category_id');
                    $link .= (isset($category_id) ? '&category_id=' . (int)$category_id : '') . JeproshopTools::getProductToken() . '=1';
                    $message = '';
                }else{
                    $link = 'index.php?option=com_jeproshop&view=product';
                    $category_id = $app->input->get('category_id');
                    $link .= (isset($category_id) ? '&category_id=' . (int)$category_id : '') . JeproshopTools::getProductToken() . '=1';
                    $message = '';
                }
                $app->redirect($link, $message);
            }else{
                $this->delete();
                $app->input->set('task', 'edit');
            }
        }else{
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_CREATING_AN_OBJECT_MESSAGE') . ' <b>' . JText::_('COM_JEPROSHOP_PRODUCT_LABEL') . '</b>');
        }
    }

    public function addProduct(){
        $input = JRequest::get('post');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();

        if ($this->context == null) {
            $this->context = JeproshopContext::getContext();
        }

        $input_data = $input['jform'];

        $this->date_add = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        $shop_list_ids = array();
        if(JeproshopShopModelShop::isTableAssociated('product')){
            $shop_list_ids = JeproshopShopModelShop::getContextListShopIds();
            if(count($this->shop_list_id) > 0){ $shop_list_ids = $this->shop_list_id; }
        }

        if(JeproshopShopModelShop::checkDefaultShopId('product')){
            $this->default_shop_id = min($shop_list_ids);
        }

        $languages = JeproshopLanguageModelLanguage::getLanguages(false);

        $reference = JeproshopTools::isReference($input_data['reference']) ? $input_data['reference'] : '';
        $ean13 = JeproshopTools::isEan13($input_data['ean13']) ? $input_data['ean13'] : '';
        $upc = JeproshopTools::isUpc($input_data['upc']) ? $input_data['upc'] : '';
        $product_redirect_id = JeproshopTools::isUnsignedInt($input_data['product_redirected_id']) ? (int)$input_data['product_redirected_id'] : 0;

        $available_for_order = isset($input_data['available_for_order']) ? 1 : 0;
        $show_price = isset($input_data['show_price']) ? 1 : 0;
        $online_only = isset($input_data['online_only']) ? 1 : 0;

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_product') . "(" . $db->quoteName('reference') . ", " . $db->quoteName('ean13') . ", " . $db->quoteName('upc') . ", " . $db->quoteName('published') . ", ";
        $query .= $db->quoteName('redirect_type') . ", " . $db->quoteName('visibility') . ", " . $db->quoteName('condition') . ", " . $db->quoteName('available_for_order') . ", " . $db->quoteName('show_price') . ", ";
        $query .= $db->quoteName('online_only') . ", " . $db->quoteName('default_shop_id') . ", " . $db->quoteName('product_redirected_id') . ", " . $db->quoteName('date_add') . ", " . $db->quoteName('date_upd') . " ) VALUES(";
        $query .= $db->quote($reference, true) . ", " . $db->quote($ean13, true) . ", " . $db->quote($upc, true) . ", " . $db->quote($input_data['published'], true) . ", " . $db->quote($input_data['redirect_type'], true) . ", ";
        $query .= $db->quote($input_data['visibility'], true) . ", " . $db->quote($input_data['condition']) . ", " . (int)$available_for_order . ", " . (int)$show_price . ", " . (int)$online_only . ", " . $this->default_shop_id . ", ";
        $query .= $product_redirect_id . ", " . $db->quote($this->date_add) . ", " . $db->quote($this->date_upd) . ") ";

        $db->setQuery($query);
        if($db->query()){
            $product_id = $db->insertid();
            if(JeproshopShopModelShop::isTableAssociated('product')) {
                /* Shop fields */
                foreach($shop_list_ids as $shop_id){
                    $query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_shop') . "( " .$db->quoteName('product_id') . ", ";
                    $query .= $db->quoteName('shop_id') . ", " . $db->quoteName('online_only') .  ", " . $db->quoteName('published') . ", ";
                    $query .= $db->quoteName('redirect_type') . ", " . $db->quoteName('product_redirected_id') . ", " . $db->quoteName('available_for_order');
                    $query .= ", " . $db->quoteName('condition') . ", " . $db->quoteName('show_price') . ", " . $db->quoteName('visibility') . ", " ;
                    $query .= $db->quoteName('date_add') . ", " . $db->quoteName('date_upd') . ") VALUES( " . (int)$product_id . ", " . (int)$shop_id . ", ";
                    $query .= (int)$online_only . ", " . (int)$input_data['published'] . ", " . $db->quote($input_data['redirect_type']) . ", ";
                    $query .= (int)$input_data['product_redirected_id'] . ", " . (int)$available_for_order . ", "  . $db->quote($input_data['condition']) . ", ";
                    $query .= (int)$show_price . ", " . $db->quote($input_data['visibility']) . ", " . $db->quote($this->date_add) . ", " . $db->quote($this->date_upd) . ") ";

                    $db->setQuery($query);
                    if($db->query()){
                        /* Multilingual fields */
                        foreach ($languages as $language) {
                            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_lang') . "(" . $db->quoteName('product_id') . ", " . $db->quoteName('shop_id') . ", " . $db->quoteName('lang_id') . ", " . $db->quoteName('description') . ", ";
                            $query .= $db->quoteName('short_description') . ", " . $db->quoteName('name') . ") VALUES (" . (int)$product_id . ", " . (int)$shop_id . ", " . (int)$language->lang_id . ", " . $db->quote($input_data['description_' . $language->lang_id]);
                            $query .= ", " . $db->quote($input_data['short_description_' . $language->lang_id]) . ", ";
                            $query .= $db->quote($input_data['name_' . $language->lang_id]) . ")";

                            $db->setQuery($query);
                            $db->query();
                        }
                    }
                }
            }

            $product = new JeproshopProductModelProduct($product_id);

            if($product->getType() == JeproshopProductModelProduct::VIRTUAL_PRODUCT){
                JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock((int)$product->product_id, 1);
                if($product->published && ! JeproshopSettingModelSetting::getValue('virtual_product_feature_active')){
                    JeproshopSettingModelSetting::updateValue('virtual_product_feature_active', '1');
                }
            }else{
                JeproshopStockAvailableModelStockAvailable::setProductOutOfStock((int)$product->product_id, 2);
            }
            $product->setGroupReduction();
            return $product;
        }
        return null;
    }

    /**
     * Update categories to index product into
     *
     * @param $categories
     * @param boolean $keeping_current_pos (deprecated, no more used)
     * @internal param string $productCategories Categories list to index product into
     * @return array Update/insertion result
     */
	public function updateCategories($categories, $keeping_current_pos = false){
		if (empty($categories)){ return false; }
		
		$db = JFactory::getDBO();
		
		$query = "SELECT category." . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeproshop_product_category');
		$query .= " AS product_category LEFT JOIN " . $db->quoteName('#__jeproshop_category') . " AS category ON (category.";
		$query .= $db->quoteName('category_id') . " = product_category." . $db->quoteName('category_id') . ") ";
		$query .= JeproshopShopModelShop::addSqlAssociation('category', true, null, true) . " WHERE product_category." ;
		$query .= $db->quoteName('category_id') . " NOT IN (". implode(',', array_map('intval', $categories)). ") AND ";
		$query .= "product_category.product_id = " . $this->product_id;
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		// if none are found, it's an error
		if (!is_array($result)){ return false; }
	
		foreach ($result as $category_to_delete)
			$this->deleteCategory($category_to_delete->category_id);
	
		if (!$this->addToCategories($categories)){ return false; }
	
		JeproshopSpecificPriceRuleModelSpecificPriceRule::applyAllRules(array((int)$this->product_id));
		return true;
	}

    /**
     * deleteCategory delete this product from the category $id_category
     *
     * @param $category_id
     * @param mixed $clean_positions
     * @internal param mixed $id_category
     * @return boolean
     */
	public function deleteCategory($category_id, $clean_positions = true){
		$db = JFactory::getDBO();
		
		$query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeproshop_product_category');
		$query .= "	WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id . " AND ";
		$query .= $db->quoteName('category_id') . " = " . (int)$category_id;
		 
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_category') . " WHERE " . $db->quoteName('product_id');
		$query .= " = " . (int)$this->product_id . " AND " . $db->quoteName('category_id') . " = " . (int)$category_id;
		
		$db->setQuery($query);
		$return = $db->query(); 
		
		if($clean_positions === true){
			foreach ($result as $row){
				$this->cleanPositions((int)$row->category_id);
			}
		}
		JeproshopSpecificPriceRuleModelSpecificPriceRule::applyAllRules(array((int)$this->product_id));
		return $return;
	}
	
	/**
	 * Delete all association to category where product is indexed
	 *
	 * @param boolean $clean_positions clean category positions after deletion
	 * @return array Deletion result
	 */
	public function deleteCategories($clean_positions = false){
		$db = JFactory::getDBO();
		if ($clean_positions === true){
			$query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeproshop_product_category');
			$query .= "	WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id ;
				
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_category') . " WHERE " . $db->quoteName('product_id');
		$query .= " = " . (int)$this->product_id ;
		
		$db->setQuery($query);
		$return = $db->query();
		
		if ($clean_positions === true && is_array($result)){
			foreach ($result as $row){
				$return &= $this->cleanPositions((int)$row->category_id);
			}
		}
		return $return;
	}

    /**
     * Get all product attributes ids
     *
     * @param $product_id
     * @param bool $shop_only
     * @return array product attribute id list
     */
	public static function getProductAttributesIds($product_id, $shop_only = false){
		$db = JFactory::getDBO();
		
		$query = "SELECT product_attribute.product_attribute_id FROM " . $db->quoteName('#__jeproshop_product_attribute');
		$query .= " AS product_attribute " . ($shop_only ? JeproshopShopModelShop::addSqlAssociation('product_attribute') : "");
		$query .= "	WHERE product_attribute." . $db->quoteName('product_id') . " = " . (int)$product_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    /**
     * Update product download
     *
     * @param int $edit
     * @return bool
     */
	public function updateDownloadProduct($edit = 0){
		$app = JFactory::getApplication();
		if ((int)$app->input->get('is_virtual_file') == 1){
			if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0){
				$virtual_product_filename = JeproshopProductDownloadModelProductDownload::getNewFilename();
				$helper = new HelperUploader('virtual_product_file_uploader');
				$files = $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
				->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
			}else{
				$virtual_product_filename = $app->input->get('virtual_product_filename', JeproshopProductDownloadModelProductDownload::getNewFilename());
			}
			
			$this->setDefaultAttribute(0);//reset cache_default_attribute
			if ($app->input->get('virtual_product_expiration_date') && !JeproshopTools::isDate($app->input->get('virtual_product_expiration_date')))
				if (!$app->input->get('virtual_product_expiration_date')){
                    JText::_('The expiration-date attribute is required.');
                    $this->context->controller->has_errors = true;
					return false;
				}
	
			// Trick's
			if ($edit == 1){
				$product_download_id = (int)JeproshopProductDownloadModelProductDownload::getIdFromProductId((int)$this->product_id);
				if (!$product_download_id){
					$product_download_id = (int)$app->input->get('virtual_product_id');
				}
			}else{
				$product_download_id = $app->input->get('virtual_product_id');
			}
			$is_sharable = $app->input->get('virtual_product_is_sharable');
			$virtual_product_name = $app->input->get('virtual_product_name');
			$virtual_product_nb_days = $app->input->get('virtual_product_nb_days');
			$virtual_product_nb_downloadable = $app->input->get('virtual_product_nb_downloadable');
			$virtual_product_expiration_date = $app->input->get('virtual_product_expiration_date');
	
			$download = new JeproshopProductDownloadModelProductDownload((int)$product_download_id);
			$download->product_id = (int)$this->product_id;
			$download->display_filename = $virtual_product_name;
			$download->filename = $virtual_product_filename;
			$download->date_add = date('Y-m-d H:i:s');
			$download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
			$download->nb_days_accessible = (int)$virtual_product_nb_days;
			$download->nb_downloadable = (int)$virtual_product_nb_downloadable;
			$download->published = 1;
			$download->is_sharable = (int)$is_sharable;
	
			if ($download->save())
				return true;
		}else{
			/* un-active download product if checkbox not checked */
			if ($edit == 1){
				$product_download_id = (int)JeproshopProductDownloadModelProductDownload::getIdFromProductId((int)$this->product_id);
				if (!$product_download_id){
					$product_download_id = (int)$app->input->get('virtual_product_id');
				}
			}else{
				$product_download_id = JeproshopProductDownloadModelProductDownload::getIdFromProductId($this->product_id);
			}
			
			if (!empty($product_download_id)){
				$product_download = new JeproshopProductDownloadModelProductDownload((int)$product_download_id);
				$product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
				$product_download->published = 0;
				return $product_download->save();
			}
		}
		return false;
	}
	
	/**
	 * delete all items in pack, then check if type_product value is 2.
	 * if yes, add the pack items from input "inputPackItems"
	 *
	 * @return boolean
	 */
	public function updatePackItems(){
		JeproshopProductPack::deleteItems($this->product_id);
		// lines format: QTY x ID-QTY x ID
		$app = JFactory::getApplication();
		$data = JRequest::get('post');
		$input_data = isset($data['information']) ?  $data['information'] : $data['jform'];
		if($input_data['product_type'] == JeproshopProductModelProduct::PACKAGE_PRODUCT){
			$this->setDefaultAttribute(0); //reset cache_default_attribute
			$items = $app->input->get('input_pack_items');
			$lines = array_unique(explode('-', $items));
			// lines is an array of string with format : QTYxID
			if (count($lines)){
				foreach ($lines as $line){
					if (!empty($line)){
						list($qty, $item_id) = explode('x', $line);
						if ($qty > 0 && isset($item_id)){
							if(JeproshopProductPack::isPack((int)$item_id)){
								$this->context->controller->has_errors  = JText::_('COM_JEPROSHOP_YOU_CANT_ADD_PRODUCT_PACKS_INTO_A_PACK_MESSAGE');
							}elseif (!JeproshopProductPack::addItem((int)$this->product_id, (int)$item_id, (int)$qty)){
								$this->context->controller->has_errors  = JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_ATTEMPTING_TO_ADD_PRODUCTS_TO_THE_PACK_MESSAGE');
							}
						}
					}
				}
			}
		}
	}	
	
	/**
	 * Update product accessories
	 */
	public function updateAccessories(){
		$app = JFactory::getApplication();
		$this->deleteAccessories();
		$accessories = $app->input->get('input_accessories');
		if($accessories){
			$accessories_id = array_unique(explode('-', $accessories));
			if (count($accessories_id)){
				array_pop($accessories_id);
				$this->changeAccessories($accessories_id);
			}
		}
	}
	
	/**
	 * Delete product accessories
	 *
	 * @return mixed Deletion result
	 */
	public function deleteAccessories(){
		$db = JFactory::getDBO();
		
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_accessory') . " WHERE " . $db->quoteName('product_1_id') . " = " . $this->product_id;
		
		$db->setQuery($query);
		return $db->query(); 
	}	

	/**
	 * Delete product from other products accessories
	 *
	 * @return mixed Deletion result
	 */
	public function deleteFromAccessories(){
		$db = JFactory::getDBO();
		
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_accessory') . " WHERE ";
		$query .= $db->quoteName('product_2_id') . " = " . $this->product_id;
		
		$db->setQuery($query);
		return $db->query();
	}
	
	/**
	 * Update product tags
	 *
	 * @param array Languages
	 * @return boolean Update result
	 */
	public function updateTags($languages){
		$tag_success = true;
		/* Reset all tags for THIS product */
		if (!JeproshopTagModelTag::deleteTagsForProduct((int)$this->product_id)){
			$this->context->controller->has_errors = true;
            JError::raiseError(500, 'An error occurred while attempting to delete previous tags.');
		}
		$input = JRequest::get('post');
		$input_data = $input['information'];
		/* Assign tags to this product */
		foreach ($languages as $language){
			if ($value = $input_data['tag_'.$language->lang_id]){
				$tag_success &= JeproshopTagModelTag::addTags($language->lang_id, (int)$this->product_id, $value);
			}
		}
		if (!$tag_success){
			$this->context->controller->has_errors = true;
            JError::raiseError(500, 'An error occurred while adding tags.');
		}
		return $tag_success;
	}

    public static function cacheProductsFeatures($product_ids) {
        if (!JeproshopFeatureModelFeature::isFeaturePublished()){ return; }

        $product_implode = array();
        foreach ($product_ids as $product_id) {
            if ((int)$product_id && !array_key_exists($product_id, self::$_cacheFeatures)) {
                $product_implode[] = (int)$product_id;
            }
        }

        if (!count($product_implode)){ return; }

        $db = JFactory::getBO();

        $query = "SELECT feature_id, product_id, feature_value_id FROM " . $db->quoteName('#__jeproshop_feature_product') . " WHERE ";
        $query .= $db->quoteName('product_id') . " IN (" .implode($product_implode, ','). ")";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $row){
            if (!array_key_exists($row->product_id, self::$_cacheFeatures))
                self::$_cacheFeatures[$row->product_id] = array();
            self::$_cacheFeatures[$row->product_id][] = $row;
        }
    }
	
	/**
	 * Link accessories with product
	 *
	 * @param array $accessories_id Accessories ids
	 */
	public function changeAccessories($accessories_id){
		$db = JFactory::getDBO();
		foreach ($accessories_id as $product_2_id){
			$query = "INSERT INTO " . $db->quoteName('#__jeproshop_accessory') . "(" . $db->quoteName('product_1_id') . ", ";
			$query .= $db->quoteName('product_2_id') . ") VALUES(" . (int)$this->product_id . ", " . (int)$product_2_id . ")";
			
			$db->setQuery($query);
			$db->query($query);
		}
	}
	
	public function addCarriers($product = NULL){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		if (!isset($product)){
			$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		}
		if (JeproshopTools::isLoadedObject($product, 'product_id')){
			$carriers = array(); 
			$input = JRequest::get('post');
			$product_data = $input['shipping'];
			if (isset($product_data['selected_carriers[]'])){
				$carriers = $product_data['selected_carriers[]'];
			}

            $query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('width') . " = " . (float)$product_data['width'] . ", " . $db->quoteName('height') . " = ";
            $query .= (float)$product_data['height'] . ", " . $db->quoteName('weight') . " = " . (float)$product_data['weight'] . ", " . $db->quoteName('additional_shipping_cost') . " = ";
            $query .= (float)$product_data['additional_shipping_cost'] . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product->product_id;

            $db->setQuery($query);
            $db->query();

			if(count($carriers)){
                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_carrier') . " WHERE product_id = " . (int)$product->product_id . " AND shop_id = " . (int)$product->shop_id;

                $db->setQuery($query);
                $db->query();
				foreach ($carriers as $carrier){
					$query = "INSERT INGORE INTO " . $db->quoteName('#__jeproshop_product_carrier') . $db->quoteName('product_id') . ", ";
					$query .= $db->quoteName('carrier_reference_id') . ", " . $db->quoteName('shop_id') . " VALUES (" . (int)$product->product_id;
					$query .= ", " . (int)$carrier . ", " . (int)$product->shop_id . ") ";
					
					$db->setQuery($query);
					$db->query();
				}
			}				
		}
	}
	
	/**
	* Check that a saved product is valid
	*/
	public function checkProduct(){
		// @todo : the call_user_func seems to contains only statics values (className = 'Product')
		//$rules = call_user_func(array('JeproshopProductModelProduct', 'getValidationRules'), 'JeproshopProductModelProduct:');
		$default_language = new JeproshopLanguageModelLanguage((int)JeproshopSettingModelSetting::getValue('default_lang'));
		$languages = JeproshopLanguageModelLanguage::getLanguages(false);
	/*
		// Check required fields
		foreach ($rules['required'] as $field){
			if (!$this->isProductFieldUpdated($field)){	continue; }
	
			if (($value = Tools::getValue($field)) == false && $value != '0')
			{
				if (Tools::getValue('id_'.$this->table) && $field == 'passwd')
					continue;
				$this->context->controller->has_errors  = sprintf(
						Tools::displayError('The %s field is required.'),
						call_user_func(array($className, 'displayFieldName'), $field, $className)
				);
			}
		}
	
		// Check multilingual required fields
		/*foreach ($rules['requiredLang'] as $fieldLang)
			if ($this->isProductFieldUpdated($fieldLang, $default_language->id) && !Tools::getValue($fieldLang.'_'.$default_language->id))
				$this->context->controller->has_errors  = sprintf(
						Tools::displayError('This %1$s field is required at least in %2$s'),
						call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
						$default_language->name
				);
	* /
			// Check fields sizes
			foreach ($rules['size'] as $field => $maxLength)
				if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)) && Tools::strlen($value) > $maxLength)
					$this->context->controller->has_errors  = sprintf(
							Tools::displayError('The %1$s field is too long (%2$d chars max).'),
							call_user_func(array($className, 'displayFieldName'), $field, $className),
							$maxLength
					);
	
				if (Tools::getIsset('description_short') && $this->isProductFieldUpdated('description_short'))
				{
					$saveShort = Tools::getValue('description_short');
					$_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
				}
	
				// Check description short size without html
				$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
				if ($limit <= 0) $limit = 400;
				foreach ($languages as $language)
					if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue('description_short_'.$language['id_lang'])))
						if (Tools::strlen(strip_tags($value)) > $limit)
							$this->context->controller->has_errors  = sprintf(
									Tools::displayError('This %1$s field (%2$s) is too long: %3$d chars max (current count %4$d).'),
									call_user_func(array($className, 'displayFieldName'), 'description_short'),
									$language['name'],
									$limit,
									Tools::strlen(strip_tags($value))
							);
	
						// Check multilingual fields sizes
						foreach ($rules['sizeLang'] as $fieldLang => $maxLength)
							foreach ($languages as $language)
							{
								$value = Tools::getValue($fieldLang.'_'.$language['id_lang']);
								if ($value && Tools::strlen($value) > $maxLength)
									$this->context->controller->has_errors  = sprintf(
											Tools::displayError('The %1$s field is too long (%2$d chars max).'),
											call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
											$maxLength
									);
							}
	
						if ($this->isProductFieldUpdated('description_short') && isset($_POST['description_short']))
							$_POST['description_short'] = $saveShort;
	
						// Check fields validity
						foreach ($rules['validate'] as $field => $function)
							if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)))
							{
								$res = true;
								if (Tools::strtolower($function) == 'iscleanhtml')
								{
									if (!Validate::$function($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
										$res = false;
								}
								else
									if (!Validate::$function($value))
										$res = false;
	
									if (!$res)
										$this->context->controller->has_errors  = sprintf(
												Tools::displayError('The %s field is invalid.'),
												call_user_func(array($className, 'displayFieldName'), $field, $className)
										);
							}
						// Check multilingual fields validity
						foreach ($rules['validateLang'] as $fieldLang => $function)
							foreach ($languages as $language)
								if ($this->isProductFieldUpdated($fieldLang, $language['id_lang']) && ($value = Tools::getValue($fieldLang.'_'.$language['id_lang'])))
									if (!Validate::$function($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
										$this->context->controller->has_errors  = sprintf(
												Tools::displayError('The %1$s field (%2$s) is invalid.'),
												call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
												$language['name']
										);
	
									// Categories
									if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox'))))
										$this->context->controller->has_errors  = $this->l('Products must be in at least one category.');
	
									if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox'))))
										$this->context->controller->has_errors  = $this->l('This product must be in the default category.');
	
									// Tags
									foreach ($languages as $language)
										if ($value = Tools::getValue('tags_'.$language['id_lang']))
											if (!Validate::isTagsList($value))
												$this->context->controller->has_errors  = sprintf(
														Tools::displayError('The tags list (%s) is invalid.'),
														$language['name']
												); */
	}
	
	public function update(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$data = JRequest::get('post');
		$input_data = $data['jform'];
		if($this->context == null){ $this->context = JeproshopContext::getContext(); }
		$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		$existing_product = JeproshopTools::isLoadedObject($product, 'product_id');
		
		$this->checkProduct();
		
		if (!empty($this->context->controller->errors)){
			$this->context->controller->layout = 'edit'; 
			return false;
		}
		
		$product_id = (int)$app->input->get('product_id');  
		/* Update an existing product */
		if (isset($product_id) && !empty($product_id)){
			if (JeproshopTools::isLoadedObject($product, 'product_id')){
				$this->removeTaxFromEcotax();
				$product_type_before = $product->getType();
				//$this->copyFromPost($object, $this->table);
				$product->indexed = 0;
				
				if(JeproshopShopModelShop::isFeaturePublished() && JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_SHOP){
					$product->setFieldsToUpdate((array)$app->input->get('multishop_check'));
				}
				// Duplicate combinations if not associated to shop
				if ($this->context->shop->getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP && !$product->isAssociatedToShop()){
					$is_associated_to_shop = false;
					$combinations = JeproshopProductModelProduct::getProductAttributesIds($product_id);
					if ($combinations){
						foreach ($combinations as $combination_id){
							$combination = new JeproshopCombinationModelCombination((int)$combination_id->product_attribute_id);
							$default_combination = new JeproshopCombinationModelCombination((int)$combination_id->product_attribute_id, null, (int)$this->product->default_shop_id);
		
							//$def = ObjectModel::getDefinition($default_combination);
							$combination->product_id = (int)$default_combination->product_id;
							$location = JeproshopTools::isGenericName($default_combination->location) ? $default_combination->location : '';
							$combination->location = $db->quote($location);
							$ean13 = JeproshopTools::isEan13($default_combination->ean13) ? $default_combination->ean13 : '';
							$combination->ean13 = $db->quote($ean13);
							$combination->upc = JeproshopTools::isUpc($default_combination->upc) ? $default_combination->upc : '';
							$combination->quantity = (int)$default_combination->quantity;
							$combination->reference = $default_combination->reference;
							$combination->supplier_reference = $default_combination->supplier_reference;
							$combination->wholesale_price = (float)$default_combination->wholesale_price;
							$combination->price = (float)$default_combination->price;
							$combination->ecotax = (float)$default_combination->ecotax;
							$combination->weight = (float)$default_combination->weight;
							$combination->unit_price_impact = (float)$default_combination->unit_price_impact;
							$combination->minimal_quantity = (int)$default_combination->default_on;
							$combination->default_on = (int)$default_combination->default_on;
							$combination->available_date = JeproshopTools::isDate($default_combination->available_date) ? $default_combination->available_date : null;
				
							$combination->save(); 
						}
					}
				}else{
					$is_associated_to_shop = true;
				}
				
				if ($product->updateProduct()){
					// If the product doesn't exist in the current shop but exists in another shop
					if (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP && !$product->isAssociatedToShop($this->context->shop->shop_id)){
						$out_of_stock = JeproshopStockAvailableModelStockAvailable::outOfStock($existing_product->product_id, $existing_product->default_shop_id);
						$depends_on_stock = JeproshopStockAvailableModelStockAvailable::dependsOnStock($existing_product->product_id, $existing_product->default_shop_id);
						JeproshopStockAvailableModelStockAvailable::setProductOutOfStock((int)$product->product_id, $out_of_stock, $this->context->shop->shop_id);
						JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock((int)$product->product_id, $depends_on_stock, $this->context->shop->shop_id);
					}
		
					//PrestaShopLogger::addLog(sprintf($this->l('%s edition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
					if (in_array($this->context->shop->getShopContext(), array(JeproshopShopModelShop::CONTEXT_SHOP, JeproshopShopModelShop::CONTEXT_ALL))){
						//if ($this->isTabSubmitted('Shipping'))
							$product->addCarriers();
						//if ($this->isTabSubmitted('Associations'))
							$product->updateAccessories($product);
						//if ($this->isTabSubmitted('Suppliers'))
							$product->processSuppliers();
						//if ($this->isTabSubmitted('Features'))
							$product->processFeatures();
						//if ($this->isTabSubmitted('Combinations'))
							$product->processProductAttribute();
						//if ($this->isTabSubmitted('Prices'))
						//{
							$product->processPriceAddition();
							$product->processSpecificPricePriorities();
						//}
						//if ($this->isTabSubmitted('Customization'))
							$product->processCustomizationConfiguration();
						//if ($this->isTabSubmitted('Attachments'))
							$product->processAttachments();
		
		
						$product->updatePackItems();
						// Disallow advanced stock management if the product become a pack
						if ($product_type_before == JeproshopProductModelProduct::SIMPLE_PRODUCT && $product->getType() == JeproshopProductModelProduct::PACKAGE_PRODUCT)
							JeproshopStockAvailableModelAVailable::setProductDependsOnStock((int)$product->product_id, false);
						$product->updateDownloadProduct(1);
						$product->updateTags(JeproshopLanguageModelLanguage::getLanguages(false));
		
						if ($product->isProductFieldUpdated('category_box') && !$product->updateCategories($input_data['category_box'])) {
                            $this->context->controller->has_errors = true;
                            JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_LINKING_THE_PRODUCT_TO_THE_CATEGORIES_MESSAGE'));
                        }
					}
					
					//if ($this->isTabSubmitted('Warehouses'))
					$product->processWarehouses();
					if(empty($this->context->controller->errors)){
						if (in_array($product->visibility, array('both', 'search')) && JeproshopSettingModelSetting::getValue('search_indexation'))
							JeproshopSearch::indexation(false, $product->product_id);
		
						// Save and preview
						if ($app->input->get('submitAddProductAndPreview')){
							$this->redirect_after = $this->getPreviewUrl($product);
						}else{
							// Save and stay on same form
							//if ($this->context->controller->layout == 'edit'){
							$message = JText::_('COM_JEPROSHOP_PRODUCT_SUCCESSFULLY_UPDATE_MESSAGE');
							$category_id = $app->input->get('category_id') ;
							$app->redirect('index.php?option=com_jeproshop&view=product&product_id='.(int)$product->product_id . (isset($category_id) ? '&category_id='.(int)$app->input->get('category_id') : '') . '&task=edit&' . JSession::getFormToken() . '=1', $message);
								
							/*}else{
								// Default behavior (save and back)
								$category_id = $app->input->get('category_id');
								$app->redirect('index.php?option=com_jeproshop&view=product' . (isset($category_id) ? '&category_id=' . (int)$app->input->get('category_id') : '')); //.  self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4&token='.$this->token;
							}*/
						}
					} else{// if errors : stay on edit page
						$this->context->controller->layout = 'edit';  
					}
				}else{
					if (!$is_associated_to_shop && $combinations){
						foreach ($combinations as $combination_item){
							$combination = new JeproshopCombinationModelCombination((int)$combination_item->product_attribute_id);
							$combination->delete();
						}
					}
					$this->context->controller->has_errors  = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
				}
			}
			else
				$this->context->controller->has_errors  = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Tools::displayError('The object cannot be loaded. ').')';
			$app->redirect('index.php?option=com_jeproshop&view=product&task=edit&product_id=' . $product_id);
		}
	}
	
	/**
	 * Post treatment for warehouses
	 */
	public function processWarehouses(){
		$app = JFactory::getApplication();
		$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		if ((int)$app->input->get('warehouse_loaded') === 1 && JeproshopTools::isLoadedObject($product, 'product_id')){
			// Get all id_product_attribute
			$warehouse_attributes = $product->getAttributesResume($this->context->language->lang_id);
			if (empty($warehouse_attributes)){
				$attribute = new JObject();
				$attribute->set('product_attribute_id', 0);
				$attribute->set('attribute_designation', '');
				$warehouse_attributes[] = $attribute;
			}
			
			// Get all available warehouses
			$warehouses = JeproshopWarehouseModelWarehouse::getWarehouses(true);
	
			// Get already associated warehouses
			$associated_warehouses_collection = JeproshopWarehouseProductLocationModelWarehouseProductLocation::getCollection($product->product_id);
	
			$elements_to_manage = array();
	
				// get form information
			foreach ($warehouse_attributes as $attribute){
				foreach ($warehouses as $warehouse){
					$key = $warehouse->warehouse_id . '_' . $product->product_id . '_' . $attribute->product_attribute_id;
	
					// get elements to manage
					if ($app->input->get('check_warehouse_'.$key)){
						$location = $app->input->get('location_warehouse_'.$key, '');
						$elements_to_manage[$key] = $location;
					}
				}
			}
	
			// Delete entry if necessary
			foreach ($associated_warehouses_collection as $awc){
				if (!array_key_exists($awc->warehouse_id .'_'. $awc->product_id . '_'.$awc->product_attribute_id, $elements_to_manage))
					$awc->delete();
			}
	
			// Manage locations
			foreach ($elements_to_manage as $key => $location){
				$params = explode('_', $key);
	
				$wpl_id = (int)JeproshopWarehouseProductLocationModelWarehouseProductLocation::getIdByProductAndWarehouse((int)$params[1], (int)$params[2], (int)$params[0]);
	
				if (empty($wpl_id)){
					//create new record
					$warehouse_location_entity = new JeproshopWarehouseProductLocationModelWarehouseProductLocation();
					$warehouse_location_entity->product_id = (int)$params[1];
					$warehouse_location_entity->product_attribute_id = (int)$params[2];
					$warehouse_location_entity->warehouse_id = (int)$params[0];
					$warehouse_location_entity->location = JFactory::getDBO()->query($location);
					$warehouse_location_entity->save();
				}else{
					$warehouse_location_entity = new WarehouseProductLocation((int)$wpl_id);
					$location = ($location);
	
					if ($location != $warehouse_location_entity->location){
						$warehouse_location_entity->location = ($location);
						$warehouse_location_entity->update();
					}
				}
			}
			JeproshopStockAvailableModelStockAvailable::synchronize((int)$product->product_id);
		}
	}
	
	
	/**
	 * Attach an existing attachment to the product
	 *
	 * @return void
	 */
	public function processAttachments(){
		$app = JFactory::getApplication();
		$product_id = (int)$app->input->get('product_id');
		$data = JRequest::get('post');
		$input_data = $data['jform'];
		if($product_id){
			$attachments = trim($input_data['array_attachments'], ',');
			$attachments = explode(',', $attachments);
			if (!JeproshopAttachmentModelAttachment::attachToProduct($product_id, $attachments)){
				$this->context->controller->has_errors  = JText::_('An error occurred while saving product attachments.');
			}
		}
	}
	
	public static function updateCacheAttachment($product_id){
		$db = JFactory::getDBO();
		
		$query = "SELECT attachment_id FROM " . $db->quoteName('#__jeproshop_product_attachment') . " WHERE product_id = " .(int)$product_id;
		
		$db->setQuery($query);
		$value = (bool)$db->loadResult();
		
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('cache_has_attachments');
		$query .= " = " . (int)$value . " WHERE product_id = " . (int)$product_id;
		
		$db->setQuery($query);
		return $db->query();
	}
	
	public function setDefaultAttribute($product_attribute_id){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$query = "SELECT * FROM " . $db->quoteNane('#__jeproshop_product_attribute') . " WHERE product_attribute_id = " ;
		$query .= (int)$product_attribute_id . " AND product_id = " . (int)$app->input->get('product_id');
		$db->setQuery($query);
		$attribute_exists = count($db->loaObject()) > 0;
		if($attribute_exists){
			$where = " WHERE product_attribute." . $db->quoteName('product_id') . " = " . (int)$this->product_id. " AND product_attribute.";
			$where .= $db->quoteName('product_attribute_id') . " = ". (int)$product_attribute_id;
			$data = " SET product_attribute." . $db->quoteName('default_on') . " = 1";
			$result = JeproshopCombinationModelCombination::updateMultishopTable($data, $where, '', TRUE);
		}
		
		$where = " WHERE product." . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		$data = " SET product." . $db->quoteName('cache_default_attribute') . " = " . (int)$product_attribute_id;
		$result &= JeproshopProductModelProduct::updateMultishopTable($data, $where, '', TRUE);
		$this->cache_default_attribute = (int)$product_attribute_id;
		return $result;
	}


    public static function isAvailableWhenOutOfStock($out_of_stock)
    {
        // @TODO 1.5.0 Update of STOCK_MANAGEMENT & ORDER_OUT_OF_STOCK
        $return = (int)$out_of_stock == 2 ? (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') : (int)$out_of_stock;
        return !Configuration::get('PS_STOCK_MANAGEMENT') ? true : $return;
    }

    public static function defineProductImage($row, $id_lang)
    {
        if (isset($row['id_image']) && $row['id_image'])
            return $row['id_product'].'-'.$row['id_image'];

        return Language::getIsoById((int)$id_lang).'-default';
    }

    public static function getTaxesInformations($row, JeproshopContext $context = null){
        static $address = null;

        if ($context === null)
            $context = Context::getContext();
        if ($address === null)
            $address = new JeproshopAddressModelAddress();

        $address->country_id = (int)$context->country->country_id;
        $address->state_id = 0;
        $address->postcode = 0;

        $tax_manager = JeproshopTaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$row->product_id, $context));
        $row->rate = $tax_manager->getTaxCalculator()->getTotalRate();
        $row->tax_name = $tax_manager->getTaxCalculator()->getTaxesName();

        return $row;
    }
	
	/**
	 * Post treatment for suppliers
	 */
	public function processSuppliers(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		if (JeproshopTools::isLoadedObject($product, 'product_id')){
			// Get all id_product_attribute
			$attributes = $product->getAttributesResume($this->context->language->lang_id);
			if (empty($attributes)){
				$attribute = new JObject();
				$attribute->set('product_attribute_id', 0);
				$attribute->set('attribute_designation', '');
				$attributes[] = $attribute;
			}
			// Get all available suppliers
			$suppliers = JeproshopSupplierModelSupplier::getSuppliers();

			// Get already associated suppliers
			$associated_suppliers = JeproshopProductSupplierModelProductSupplier::getSupplierCollection($product->product_id);
	
			$input = JRequest::get('post');
			$product_data = $input['supplier'];
			$suppliers_to_associate = array();
			$new_default_supplier = 0;

			if(isset($product_data['default_supplier'])){
				$new_default_supplier = (int)$product_data['default_supplier'];
			}

			// Get new associations
			foreach ($suppliers as $supplier){
				if (isset($product_data['check_supplier_'.$supplier->supplier_id])){
					$suppliers_to_associate[] = $supplier->supplier_id;
				}
			}

			// Delete already associated suppliers if needed					
			foreach ($associated_suppliers as $key => $associated_supplier){
				if (!in_array($associated_supplier->supplier_id, $suppliers_to_associate)){
					$associated_supplier->delete();
					unset($associated_suppliers[$key]);
				}
			}

			// Associate suppliers
			foreach ($suppliers_to_associate as $supplier_id){
				$to_add = true;
				foreach ($associated_suppliers as $as){
					if ($supplier_id == $as->supplier_id){
						$to_add = false;
					}
				}

				if ($to_add){
					$product_supplier = new JeproshopProductSupplierModelProductSupplier();
					$product_supplier->product_id = $product->product_id;
					$product_supplier->product_attribute_id = 0;
					$product_supplier->supplier_id = $supplier_id;
					if ($this->context->currency->currency_id){
						$product_supplier->currency_id = (int)$this->context->currency->currency_id;
					}else{
						$product_supplier->currency_id = (int)JeproshopSettingModelSetting::getValue('default_currency');
					}
					$product_supplier->save();
	
					$associated_suppliers[] = $product_supplier;
				}
			}

			// Manage references and prices
			foreach ($attributes as $attribute){
				foreach ($associated_suppliers as $supplier){
					if($product_data['supplier_reference_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id] ||
						($product_data['product_price_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id] &&
						$product_data['product_price_currency_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id])){
						$reference = JFactory::getDBO()->quote(
										$app->input->get('supplier_reference_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id, ''));
	
						$price = (float)str_replace(
									array(' ', ','), array('', '.'),
									$app->input->get('product_price_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id, 0));
						
						$price = JeproshopTools::roundPrice($price, 6);
						
						$currency_id = (int)$app->input->get('product_price_currency_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id, 0);
	
						if ($currency_id <= 0 || ( !($result = JeproshopCurrencyModelCurrency::getCurrency($currency_id)) || empty($result) ))
							$this->context->controller->has_errors  = JText::_('The selected currency is not valid'. __FILE__ . 'on line ' . __LINE__);
	
						// Save product-supplier data
						$product_supplier_id = (int)JeproshopProductSupplierModelProductSupplier::getIdByProductAndSupplier($product->product_id, $attribute->product_attribute_id, $supplier->supplier_id);
	
						if (!$product_supplier_id){
							$product->addSupplierReference($supplier->supplier_id, (int)$attribute->product_attribute_id, $reference, (float)$price, (int)$currency_id);
							if ($product->supplier_id == $supplier->supplier_id){
								if ((int)$attribute->product_attribute_id > 0){
									$data = new JObject();
									$data->set('supplier_reference', $db->quote($reference));
									$data->set('wholesale_price', (float)JeproshopTools::convertPrice($price, $currency_id));
											
									$where = " combination.product_id = " .(int)$product->product_id . " AND combination.product_attribute_id = " .(int)$attribute->product_attribute_id;
									JeproshopCombinationModelCombination::updateMultishopTable($data, $where);
								}else{
									$product->wholesale_price = (float)Tools::convertPrice($price, $currency_id); //converted in the default currency
									$product->supplier_reference = $db->quote($reference);
									$product->update();
								}
							}
						}else{
							$product_supplier = new JeproshopProductSupplierModelProductSupplier($product_supplier_id);
							$product_supplier->currency_id = (int)$currency_id;
							$product_supplier->product_supplier_price_te = (float)$price;
							$product_supplier->product_supplier_reference = $db->quote($reference);
							$product_supplier->update();	
						}
					}elseif ($app->input->get('supplier_reference_'.$product->product_id.'_'.$attribute->product_attribute_id.'_'.$supplier->supplier_id)){
						//int attribute with default values if possible
						if ((int)$attribute->product_attribute_id > 0){
							$product_supplier = new JeproshopProductSupplierModelProductSupplier();
							$product_supplier->product_id = $product->product_id;
							$product_supplier->product_attribute_id = (int)$attribute->product_attribute_id;
							$product_supplier->supplier_id = $supplier->supplier_id;
							$product_supplier->save();
						}
					}
				}
			}
/*
			// Manage default supplier for product
			if ($new_default_supplier != $product->supplier_id)	{
				$product->supplier_id = $new_default_supplier;
				$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('supplier_id') . " = " . (int)$new_default_supplier;
				$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$product->product_id;
		
				$db->setQuery($query);
				$db->query();
			} */
		}
	}
	
	public function updateSupplier(){
		$db = JFactory::getDBO();
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('supplier_id') . " = " . (int)$this->supplier_id;
		$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		
		$db->setQuery($query);
		$db->query();
		
	}
	
	public function processFeatures(){
		if (!JeproshopFeatureModelFeature::isFeaturePublished()){ return; }
		$app = JFactory::getApplication();
        $product_id = isset($this->product_id) ? $this->product_id : (int)$app->input->get('product_id');
		$product = new JeproshopProductModelProduct($product_id);
		if (JeproshopTools::isLoadedObject($product, 'product_id'))	{
			// delete all objects
			$product->deleteFeatures();
	
			// add new objects
			$languages = JeproshopLanguageModelLanguage::getLanguages(false);
			foreach ($_POST as $key => $val){
				if (preg_match('/^feature_([0-9]+)_value/i', $key, $match)){
					if ($val){
						$product->addFeaturesToDB($match[1], $val);
					}else{
						if ($default_value = $this->checkFeatures($languages, $match[1])){
							$id_value = $product->addFeaturesToDB($match[1], 0, 1);
							foreach ($languages as $language){
								if ($cust = $app->input->get('custom_'.$match[1].'_'.(int)$language->lang_id)){
									$product->addFeaturesCustomToDB($id_value, (int)$language->lang_id, $cust);
								}else{
									$product->addFeaturesCustomToDB($id_value, (int)$language->lang_id, $default_value);
								}
							}
						}
					}
				}
			}
		}else {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('COM_JEPROSHOP_A_PRODUCT_MUST_BE_CREATED_BEFORE_ADDING_FEATURES_MESSAGE'));
        }
	}
	
	/**
	 * Get product price
	 *
	 * @param integer $product_id Product id
	 * @param boolean $use_tax With taxes or not (optional)
	 * @param integer $product_attribute_id Product attribute id (optional).
	 *    If set to false, do not apply the combination price impact. NULL does apply the default combination price impact.
	 * @param integer $decimals Number of decimals (optional)
	 * @param boolean $only_reduction Returns only the reduction amount
	 * @param boolean $use_reduction Set if the returned amount will include reduction
	 * @param integer $quantity Required for quantity discount application (default value: 1)
	 * @param integer $customer_id Customer ID (for customer group reduction)
	 * @param integer $cart_id Cart ID. Required when the cookie is not accessible (e.g., inside a payment module, a cron task...)
	 * @param integer $address_id Customer address ID. Required for price (tax included) calculation regarding the guest localization
	 * @param null $specific_price_output
	 * @param boolean $with_ecotax insert ecotax in price output.
	 * @param bool $use_group_reduction
	 * @param JeproshopContext $context
	 * @param bool $use_customer_price
	 * @internal param int $divisor Useful when paying many time without fees (optional)
	 * @internal param \variable_reference $specificPriceOutput .
	 *    If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
	 * @return float Product price
	 */
	public static function getStaticPrice($product_id, $use_tax = true, $product_attribute_id = null, $decimals = 6, $only_reduction = false, $use_reduction = true, $quantity = 1, $customer_id = null,
	$cart_id = null, $address_id = null, $specific_price_output = null, $with_ecotax = true, $use_group_reduction = true, JeproshopContext $context = null, $use_customer_price = true){
		if(!$context){
			$context = JeproshopContext::getContext();
		}
	
		$cur_cart = $context->cart;
	
		if (!JeproshopTools::isBool($use_tax) || !JeproshopTools::isUnsignedInt($product_id)){
			//die(Tools::displayError());
		}
	
		// Initializations
		$group_id = (int)JeproshopGroupModelGroup::getCurrent()->group_id;
	
		// If there is cart in context or if the specified id_cart is different from the context cart id
		if (!is_object($cur_cart) || (JeproshopTools::isUnsignedInt($cart_id) && $cart_id && $cur_cart->cart_id != $cart_id)){
			/*
			 * When a user (e.g., guest, customer, Google...) is on Jeproshop, he has already its cart as the global (see /init.php)
			* When a non-user calls directly this method (e.g., payment module...) is on JeproShop, he does not have already it BUT knows the cart ID
			* When called from the back office, cart ID can be in-existent
			*/
			if (!$cart_id && !isset($context->employee)){
				JError::raiseError(500, __FILE__ . ' ' . __LINE__);
			}
			$cur_cart = new JeproshopCartModelCart($cart_id);
			// Store cart in context to avoid multiple instantiations in BO
			if (!JeproshopTools::isLoadedObject($context->cart, 'cart_id')){
				$context->cart = $cur_cart;
			}
		}
		$db = JFactory::getDBO();
		$cart_quantity = 0;
		if ((int)$cart_id){
			$cache_id = 'jeproshop_product_model_get_price_static_' . (int)$product_id .'_' . (int)$cart_id;
			$cart_qty = JeproshopCache::retrieve($cache_id);
			if (!JeproshopCache::isStored($cache_id) || ( $cart_qty != (int)$quantity)){
				$query = "SELECT SUM(" . $db->quoteName('quantity') . ") FROM " . $db->quoteName('#__jeproshop_cart_product');
				$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id . " AND " . $db->quoteName('cart_id');
				$query .= " = " .(int)$cart_id;
				$db->setQuery($query);
				$cart_quantity = (int)$db->loadResult();
				JeproshopCache::store($cache_id, $cart_quantity);
			}
			$cart_quantity = JeproshopCache::retrieve($cache_id);
		}
	
		$currency_id = (int)JeproshopTools::isLoadedObject($context->currency, 'currency_id') ? $context->currency->currency_id : JeproshopSettingModelSetting::getValue('default_currency');
	
		// retrieve address information
		$country_id = (int)$context->country->country_id;
		$state_id = 0;
		$zipcode = 0;
	
		if (!$address_id && JeproshopTools::isLoadedObject($cur_cart, 'cart_id')){
			$address_id = $cur_cart->{JeproshopSettingModelSetting::getValue('tax_address_type')};
		}
	
		if ($address_id){
			$address_info = JeproshopAddressModelAddress::getCountryAndState($address_id);
			if ($address_info->country_id){
				$country_id = (int)$address_info->country_id;
				$state_id = (int)$address_info->state_id;
				$zipcode = $address_info->postcode;
			}
		}else if (isset($context->customer->geolocation_country_id)){
			$country_id = (int)$context->customer->geolocation_country_id;
			$state_id = (int)$context->customer->state_id;
			$zipcode = (int)$context->customer->postcode;
		}
	
		if (JeproshopTaxModelTax::taxExcludedOption()){
			$use_tax = false;
		}
	
		if ($use_tax != false && !empty($address_info->vat_number)
				&& $address_info->country_id != JeproshopSettingModelSetting::getValue('vat_number_country')
				&& JeproshopSettingModelSetting::getValue('vat_number_management')){
			$use_tax = false;
		}
	
		if (is_null($customer_id) && JeproshopTools::isLoadedObject($context->customer, 'customer_id')){
			$customer_id = $context->customer->customer_id;
		}
	
		return JeproshopProductModelProduct::priceCalculation($context->shop->shop_id, $product_id,
				$product_attribute_id, $country_id, $state_id, $zipcode, $currency_id, $group_id,
				$quantity, $use_tax, $decimals, 	$only_reduction, $use_reduction, $with_ecotax, $specific_price_output,
				$use_group_reduction, $customer_id, $use_customer_price, $cart_id, $cart_quantity
		);
	}
	
	/**
	 * Price calculation / Get product price
	 *
	 * @param integer $shop_id Shop id
	 * @param integer $product_id Product id
	 * @param integer $product_attribute_id Product attribute id
	 * @param integer $country_id Country id
	 * @param integer $state_id State id
	 * @param $zipcode
	 * @param integer $currency_id Currency id
	 * @param integer $group_id Group id
	 * @param integer $quantity Quantity Required for Specific prices : quantity discount application
	 * @param boolean $use_tax with (1) or without (0) tax
	 * @param integer $decimals Number of decimals returned
	 * @param boolean $only_reduction Returns only the reduction amount
	 * @param boolean $use_reduction Set if the returned amount will include reduction
	 * @param boolean $with_ecotax insert ecotax in price output.
	 * @param $specific_price
	 * @param $use_group_reduction
	 * @param int $customer_id
	 * @param bool $use_customer_price
	 * @param int $cart_id
	 * @param int $real_quantity
	 * @internal param \variable_reference $specific_price_output If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object*    If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
	 * @return float Product price
	 */
	public static function priceCalculation($shop_id, $product_id, $product_attribute_id, $country_id, $state_id, $zipcode, $currency_id, $group_id, $quantity, $use_tax,
	$decimals, $only_reduction, $use_reduction, $with_ecotax, &$specific_price, $use_group_reduction, $customer_id = 0, $use_customer_price = true, $cart_id = 0, $real_quantity = 0){
		static $address = null;
		static $context = null;
	
		if ($address === null){
			$address = new JeproshopAddressModelAddress();
		}
	
		if ($context == null){
			$context = JeproshopContext::getContext()->cloneContext();
		}
	
		if ($shop_id !== null && $context->shop->shop_id != (int)$shop_id){
			$context->shop = new JeproshopShopModelShop((int)$shop_id);
		}
	
		if (!$use_customer_price){
			$customer_id = 0;
		}
	
		if ($product_attribute_id === null){
			$product_attribute_id = JeproshopProductModelProduct::getDefaultAttribute($product_id);
		}
	
		$cache_id = $product_id . '_' .$shop_id . '_' . $currency_id . '_' . $country_id . '_' . $state_id . '_' . $zipcode . '_' . $group_id .
		'_' . $quantity . '_' . $product_attribute_id . '_' .($use_tax?'1':'0').'_' . $decimals.'_'. ($only_reduction ? '1' :'0').
		'_'.($use_reduction ?'1':'0') . '_' . $with_ecotax. '_' . $customer_id . '_'.(int)$use_group_reduction.'_'.(int)$cart_id.'-'.(int)$real_quantity;
	
		// reference parameter is filled before any returns
		$specific_price = JeproshopSpecificPriceModelSpecificPrice::getSpecificPrice((int)$product_id, $shop_id, $currency_id,
				$country_id, $group_id, $quantity, $product_attribute_id, $customer_id, $cart_id, $real_quantity
		);
	
		if (isset(self::$_prices[$cache_id])){
			return self::$_prices[$cache_id];
		}
		$db = JFactory::getDBO();
		// fetch price & attribute price
		$cache_id_2 = $product_id.'-'.$shop_id;
		if (!isset(self::$_pricesLevel2[$cache_id_2])){
			$select = "SELECT product_shop." . $db->quoteName('price') . ", product_shop." . $db->quoteName('ecotax');
			$from = $db->quoteName('#__jeproshop_product') . " AS product INNER JOIN " . $db->quoteName('#__jeproshop_product_shop');
			$from .= " AS product_shop ON (product_shop.product_id =product.product_id AND product_shop.shop_id = " .(int)$shop_id  . ")";
	
			if (JeproshopCombinationModelCombination::isFeaturePublished()){
				$select .= ", product_attribute_shop.product_attribute_id, product_attribute_shop." . $db->quoteName('price') . " AS attribute_price, product_attribute_shop.default_on";
				$leftJoin = " LEFT JOIN " . $db->quoteName('#__jeproshop_product_attribute') .  " AS product_attribute ON product_attribute.";
				$leftJoin .= $db->quoteName('product_id') . " = product." . $db->quoteName('product_id') . " LEFT JOIN " . $db->quoteName('#__jeproshop_product_attribute_shop');
				$leftJoin .= " AS product_attribute_shop ON (product_attribute_shop.product_attribute_id = product_attribute.product_attribute_id AND product_attribute_shop.shop_id = " .(int)$shop_id .")";
			}else{
				$select .= ", 0 as product_attribute_id";
				$leftJoin = "";
			}
			$query = $select . " FROM " . $from . $leftJoin . " WHERE product." . $db->quoteName('product_id') . " = " . (int)$product_id;
	
			$db->setQuery($query);
			$results = $db->loadObjectList();
	
			foreach ($results as $row){
				$array_tmp = array(
						'price' => $row->price, 'ecotax' => $row->ecotax,
						'attribute_price' => (isset($row->attribute_price) ? $row->attribute_price : null)
				);
	
				self::$_pricesLevel2[$cache_id_2][(int)$row->product_attribute_id] = $array_tmp;
	
				if (isset($row->default_on) && $row->default_on == 1){
					self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
				}
			}
		}
	
		if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$product_attribute_id])){
			return null;
		}
	
		$result = self::$_pricesLevel2[$cache_id_2][(int)$product_attribute_id];
	
		if (!$specific_price || $specific_price->price < 0){
			$price = (float)$result['price'];
		}else{
			$price = (float)$specific_price->price;
		}
	
		// convert only if the specific price is in the default currency (id_currency = 0)
		if (!$specific_price || !($specific_price->price >= 0 && $specific_price->currency_id)){
			$price = JeproshopTools::convertPrice($price, $currency_id);
		}
	
		// Attribute price
		if (is_array($result) && (!$specific_price || !$specific_price->product_attribute_id || $specific_price->price < 0)){
			$attribute_price = JeproshopTools::convertPrice($result['attribute_price'] !== null ? (float)$result['attribute_price'] : 0, $currency_id);
			// If you want the default combination, please use NULL value instead
			if ($product_attribute_id !== false){
				$price += $attribute_price;
			}
		}
	
		// Tax
		$address->country_id = $country_id;
		$address->state_id = $state_id;
		$address->postcode = $zipcode;
	
		$tax_manager = JeproshopTaxManagerFactory::getManager($address, JeproshopProductModelProduct::getTaxRulesGroupIdByProductId((int)$product_id, $context));
		$product_tax_calculator = $tax_manager->getTaxCalculator();
	
		// Add Tax
		if ($use_tax){
			$price = $product_tax_calculator->addTaxes($price);
		}
	
		// Reduction
		$specific_price_reduction = 0;
		if (($only_reduction || $use_reduction) && $specific_price){
			if ($specific_price->reduction_type == 'amount'){
				$reduction_amount = $specific_price->reduction;
	
				if (!$specific_price->currency_id){
					$reduction_amount = JeproshopTools::convertPrice($reduction_amount, $currency_id);
				}
				$specific_price_reduction = !$use_tax ? $product_tax_calculator->removeTaxes($reduction_amount) : $reduction_amount;
			}else{
				$specific_price_reduction = $price * $specific_price->reduction;
			}
		}
	
		if ($use_reduction){
			$price -= $specific_price_reduction;
		}
	
		// Group reduction
		if($use_group_reduction){
			$reduction_from_category = JeproshopGroupReductionModelGroupReduction::getValueForProduct($product_id, $group_id);
			if ($reduction_from_category !== false){
				$group_reduction = $price * (float)$reduction_from_category;
			}else {
				// apply group reduction if there is no group reduction for this category
				$group_reduction = (($reduction = JeproshopGroupModelGroup::getReductionByGroupId($group_id)) != 0) ? ($price * $reduction / 100) : 0;
			}
		}else{
			$group_reduction = 0;
		}
	
		if ($only_reduction){
			return JeproshopTools::roundPrice($group_reduction + $specific_price_reduction, $decimals);
		}
	
		if ($use_reduction){  $price -= $group_reduction;   }
	
		// Eco Tax
		if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax){
			$ecotax = $result['ecotax'];
			if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0){
				$ecotax = $result['attribute_ecotax'];
			}
			if ($currency_id){
				$ecotax = JeproshopTools::convertPrice($ecotax, $currency_id);
			}
	
			if ($use_tax){
				// reinitialize the tax manager for ecotax handling
				$tax_manager = JeproshopTaxManagerFactory::getManager($address, (int)JeproshopSettingModelSetting::getValue('ecotax_tax_rules_group_id'));
				$ecotax_tax_calculator = $tax_manager->getTaxCalculator();
				$price += $ecotax_tax_calculator->addTaxes($ecotax);
			}else{
				$price += $ecotax;
			}
		}
		$price = JeproshopTools::roundPrice($price, $decimals);
		if ($price < 0){
			$price = 0;
		}
	
		self::$_prices[$cache_id] = $price;
		return self::$_prices[$cache_id];
	}
	
	/**
	 * Select all features for the object
	 *
	 * @return array Array with feature product's data
	 */
	public function getFeatures(){
		return JeproshopProductModelProduct::getStaticFeatures((int)$this->product_id);
	}
	
	public static function getStaticFeatures($product_id){
		if (!JeproshopFeatureModelFeature::isFeaturePublished()){ return array(); }
		if (!array_key_exists($product_id, self::$_cacheFeatures)){
			$db = JFactory::getDBO();
			
			$query = "SELECT product_feature.feature_id, product_feature.product_id, product_feature.feature_value_id, custom FROM ";
			$query .= $db->quoteName('#__jeproshop_product_feature') . " AS product_feature LEFT JOIN " . $db->quoteName('#__jeproshop_feature_value');
			$query .= " AS feature_value ON (product_feature.feature_value_id = feature_value.feature_value_id ) WHERE ";
			$query .= $db->quoteName('product_id') . " = " .(int)$product_id;
			
			$db->setQuery($query);
			self::$_cacheFeatures[$product_id] = $db->loadObjectList();
		}
		return self::$_cacheFeatures[$product_id];
	}
	
	public function processProductAttribute(){
		$app = JFactory::getApplication();
		// Don't process if the combination fields have not been submitted
		if (!JeproshopCombinationModelCombination::isFeaturePublished() || !$app->input->get('attribute_combination_list')){
			return;
		}
		$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		if (JeproshopTools::isLoadedObject($product , 'product_id')){
			if ($this->isProductFieldUpdated('attribute_price') && (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null))
				$this->context->controller->has_errors  = Tools::displayError('The price attribute is required.');
			if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list')))
				$this->context->controller->has_errors  = Tools::displayError('You must add at least one attribute.');
	
			$array_checks = array(
					'reference' => 'isReference',
					'supplier_reference' => 'isReference',
					'location' => 'isReference',
					'ean13' => 'isEan13',
					'upc' => 'isUpc',
					'wholesale_price' => 'isPrice',
					'price' => 'isPrice',
					'ecotax' => 'isPrice',
					'quantity' => 'isInt',
					'weight' => 'isUnsignedFloat',
					'unit_price_impact' => 'isPrice',
					'default_on' => 'isBool',
					'minimal_quantity' => 'isUnsignedInt',
					'available_date' => 'isDateFormat'
			);
			
			foreach ($array_checks as $property => $check)
				if (Tools::getValue('attribute_'.$property) !== false && !call_user_func(array('Validate', $check), Tools::getValue('attribute_'.$property)))
					$this->context->controller->has_errors  = sprintf(Tools::displayError('Field %s is not valid'), $property);
	
				if (!count($this->context->controller->errors))
				{
					if (!isset($_POST['attribute_wholesale_price'])) $_POST['attribute_wholesale_price'] = 0;
					if (!isset($_POST['attribute_price_impact'])) $_POST['attribute_price_impact'] = 0;
					if (!isset($_POST['attribute_weight_impact'])) $_POST['attribute_weight_impact'] = 0;
					if (!isset($_POST['attribute_ecotax'])) $_POST['attribute_ecotax'] = 0;
					if (Tools::getValue('attribute_default'))
						$product->deleteDefaultAttributes();
	
					// Change existing one
					if (($product_attribute_id = (int)Tools::getValue('id_product_attribute')) || ($product_attribute_id = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true))){
						if ($this->tabAccess['edit'] === '1'){
							if ($this->isProductFieldUpdated('available_date_attribute') && (Tools::getValue('available_date_attribute') != '' &&!Validate::isDateFormat(Tools::getValue('available_date_attribute')))) {
                                $this->context->controller->has_errors = Tools::displayError('Invalid date format.');
                            }else{
								$product->updateAttribute((int)$product_attribute_id,
										$this->isProductFieldUpdated('attribute_wholesale_price') ? Tools::getValue('attribute_wholesale_price') : null,
										$this->isProductFieldUpdated('attribute_price_impact') ? Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact') : null,
										$this->isProductFieldUpdated('attribute_weight_impact') ? Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact') : null,
										$this->isProductFieldUpdated('attribute_unit_impact') ? Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact') : null,
										$this->isProductFieldUpdated('attribute_ecotax') ? Tools::getValue('attribute_ecotax') : null,
										Tools::getValue('id_image_attr'),
										Tools::getValue('attribute_reference'),
										Tools::getValue('attribute_ean13'),
										$this->isProductFieldUpdated('attribute_default') ? Tools::getValue('attribute_default') : null,
										Tools::getValue('attribute_location'),
										Tools::getValue('attribute_upc'),
										$this->isProductFieldUpdated('attribute_minimal_quantity') ? Tools::getValue('attribute_minimal_quantity') : null,
										$this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null, false);
								StockAvailable::setProductDependsOnStock((int)$product->product_id, $product->depends_on_stock, null, (int)$product_attribute_id);
								StockAvailable::setProductOutOfStock((int)$product->product_id, $product->out_of_stock, null, (int)$product_attribute_id);
							}
						}else {
                            $this->context->controller->has_errors = true;
                            Tools::displayError('You do not have permission to add this.');
                        }
					}else{
						// Add new
						if ($this->tabAccess['add'] === '1'){
							if ($product->productAttributeExists(Tools::getValue('attribute_combination_list'))) {
                                $this->context->controller->has_errors = true;
                                Tools::displayError('This combination already exists.');
                            }else{
								$product_attribute_id = $product->addCombinationEntity(
										Tools::getValue('attribute_wholesale_price'),
										Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
										Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
										Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
										Tools::getValue('attribute_ecotax'),
										0,
										Tools::getValue('id_image_attr'),
										Tools::getValue('attribute_reference'),
										null,
										Tools::getValue('attribute_ean13'),
										Tools::getValue('attribute_default'),
										Tools::getValue('attribute_location'),
										Tools::getValue('attribute_upc'),
										Tools::getValue('attribute_minimal_quantity'),
										Array(),
										Tools::getValue('available_date_attribute')
								);
								JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock((int)$product->product_id, $product->depends_on_stock, null, (int)$product_attribute_id);
								JeproshopStockAvailableModelStockAvailable::setProductOutOfStock((int)$product->product_id, $product->out_of_stock, null, (int)$product_attribute_id);
							}
						}else {
                            $this->context->controller->has_errors = true;
                            Tools::displayError('You do not have permission to') . '<hr>' . Tools::displayError('edit here.');
                        }
					}

					if (!$this->context->controller->has_errors){
						$combination = new JeproshopCombinationModelCombination((int)$product_attribute_id);
						$combination->setAttributes(Tools::getValue('attribute_combination_list'));
	
						// images could be deleted before
						$id_images = Tools::getValue('id_image_attr');
						if (!empty($id_images))
							$combination->setImages($id_images);
	
						$product->checkDefaultAttributes();
						if (Tools::getValue('attribute_default'))
						{
							Product::updateDefaultAttribute((int)$product->id);
							if(isset($id_product_attribute))
								$product->cache_default_attribute = (int)$id_product_attribute;
							if ($available_date = Tools::getValue('available_date_attribute'))
								$product->setAvailableDate($available_date);
						}
					}
				}
		}
	}

    /**
     * Check if there is no default attribute and create it if not
     */
    public function checkDefaultAttributes(){
        if (!$this->product_id){ return false; }

        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute " . JeproshopShopModelShop::addSqlRestriction('product_attribute') . " WHERE product_attribute_shop.";
        $query .= $db->quoteName('default_on') . " = 1 AND product_attribute_attribute." . $db->quoteName('product_id') . " = " . (int)$this->product_id;

        $db->setQuery($query);

        if ($db->loadResult() > 1) {
            /*$query = "UPDATE " . $db->quoteName('#__jeproshop_product_attribute_shop') . " SET " . $db->quoteName('default_on') . " WHERE " . $db->quoteName('product_attribute_id'), ' . _DB_PREFIX_ . 'product_attribute pa
					SET product_attribute_shop.default_on=0, pa.default_on = 0
					WHERE product_attribute_shop.id_product_attribute=pa.id_product_attribute AND pa.id_product=' . (int)$this->id
                . Shop::addSqlRestriction(false, 'product_attribute_shop'));
            $db->setQuery($query);
            $db->query();*/
        }

        $query = "SELECT product_attribute." . $db->quoteName('product_id') . " FROM " . $db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute " . JeproshopShopModelShop::addSqlAssociation('product_attribute');
        $query .= " WHERE product_attribute_shop." . $db->quoteName('default_on') . " = 1 AND product_attribute." . $db->quoteName('product_id') . " = " . (int)$this->product_id;

        $db->setQuery($query);
        $row = $db->loadObject();
        if ($row)
            return true;

        $query = "SELECT MIN (product_attribute." . $db->quoteName('product_attribute_id') . ") AS attribute_id FROM " . $db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;

        $db->setQuery($query);
        $mini = $db->loadObject();
        if (!$mini)
            return false;

        if (!ObjectModel::updateMultishopTable('Combination', array('default_on' => 1), 'a.id_product_attribute = '.(int)$mini['id_attr']))
            return false;
        return true;
    }


    public function setAvailableDate($available_date = '0000-00-00') {
        if (JeproshopTools::isDateFormat($available_date) && $this->available_date != $available_date) {
            $this->available_date = $available_date;

            $db = JFactory::getDBO();

            $query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('available_date') . " = " . $available_date . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;

            $db->setQuery($query);
            $result = $db->query();

            $shopListIds = JeproshopShopModelShop::getContextShopGroupID();
            if(count($this->shop_list_id) > 0){
                $shopListIds = $this->shop_list_id;
            }

            if($result) {
                foreach($shopListIds as $shop_id) {
                    $query = "UPDATE " . $db->quoteName('#__jeproshop_product_shop') . " SET " . $db->quoteName('available_date') . " = " . $available_date . " WHERE " . $db->quoteName('product_id') . " = ";
                    $query .= (int)$this->product_id . " AND " . $db->quoteName('shop_id') . " = " . (int)$shop_id;

                    $db->setQuery($query);
                    $result &= $db->query();
                }
            }
            return $result;
        }
        return false;
    }
	
	public function processPriceAddition(){
		$app = JFactory::getApplication();
		// Check if a specific price has been submitted
				
		$data = JRequest::get('post');
		$input_data = $data['price_field'];
		
		$product_id = $app->input->get('product_id');
		$product_attribute_id = $input_data['sp_product_attribute_id'];
		$shop_id = $input_data['sp_shop_id'];
		$currency_id = $input_data['sp_currency_id'];
		$country_id = $input_data['sp_country_id'];
		$group_id = $input_data['sp_group_id'];
		$customer_id = $input_data['sp_customer_id'];
		$price = $input_data['leave_base_price'] ? '-1' : $input_data['sp_price'];
		$from_quantity = $input_data['sp_from_quantity'];
		$reduction = (float)($input_data['sp_reduction']);
		$reduction_type = !$reduction ? 'amount' : $input_data['sp_reduction_type'];
		$from = $input_data['sp_from'];
		if (!$from)
			$from = '0000-00-00 00:00:00';
		$to = $input_data['sp_to'];
		if (!$to)
			$to = '0000-00-00 00:00:00';
			
		if ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)){
			$this->context->controller->has_errors  = true;
            JError::raiseError(500, 'Submitted reduction value (0-100) is out-of-range');
		}elseif($this->validateSpecificPrice($shop_id, $currency_id, $country_id, $group_id, $customer_id, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $product_attribute_id)){
			$specificPrice = new JeproshopSpecificPriceModelSpecificPrice();
			$specificPrice->product_id = (int)$product_id;
			$specificPrice->product_attribute_id = (int)$product_attribute_id;
			$specificPrice->shop_id = (int)$shop_id;
			$specificPrice->currency_id = (int)($currency_id);
			$specificPrice->country_id = (int)($country_id);
			$specificPrice->group_id = (int)($group_id);
			$specificPrice->customer_id = (int)$customer_id;
			$specificPrice->price = (float)($price);
			$specificPrice->from_quantity = (int)($from_quantity);
			$specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
			$specificPrice->reduction_type = $reduction_type;
			$specificPrice->from = $from;
			$specificPrice->to = $to;
			if (!$specificPrice->add())
				$this->context->controller->has_errors  = JText::_('An error occurred while updating the specific price.');
		}
	}
	
	/**
	 * This method allows to flush price cache
	 * @static	 
	 */
	public static function flushPriceCache(){
		self::$_prices = array();
		self::$_pricesLevel2 = array();
	}
	
	private function validateSpecificPrice($shop_id, $currency_id, $country_id, $group_id, $customer_id, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $product_attribute_id = 0){
		$app = JFactory::getApplication();
		$product_id = $app->input->get('product_id');

        if (!JeproshopTools::isUnsignedInt($shop_id) || !JeproshopTools::isUnsignedInt($currency_id) || !JeproshopTools::isUnsignedInt($country_id) || !JeproshopTools::isUnsignedInt($group_id) || !JeproshopTools::isUnsignedInt($customer_id)) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('Wrong IDs'));
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !JeproshopTools::isNegativePrice($price)) || (isset($reduction) && !JeproshopTools::isPrice($reduction))) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('Invalid price/discount amount'));
        } elseif (!JeproshopTools::isUnsignedInt($from_quantity)) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('Invalid quantity'));
        } elseif ($reduction && !JeproshopTools::isReductionType($reduction_type)) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('Please select a discount type (amount or percentage).'));
        } elseif ($from && $to && (!JeproshopTools::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('The from/to date is invalid.'));
        } elseif (JeproshopSpecificPriceMpdelSpecificPrice::exists((int)$product_id, $product_attribute_id, $shop_id, $group_id, $country_id, $currency_id, $customer_id, $from_quantity, $from, $to, false)) {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, JText::_('A specific price already exists for these parameters.'));
        } else {
            return true;
        }
		return false;
	}
	
	public function processSpecificPricePriorities(){
		$app = JFactory::getApplication();
		$product  = new JeproshopProductModelProduct($app->input->get('product_id'));
		$data = JRequest::get('post');
		$input_data = $data['price_field'];
		
		if (!($product)){ return; }
		if (!$priorities = $data['specific_price_priority[]']){
			$this->context->controller->has_errors  = true;
            JError::raiseError(500, JText::_('Please specify priorities.'));
		}elseif(isset($input_data['specific_price_priority_to_all'])){
			if (!JeproshopSpecificPriceModelSpecificPrice::setPriorities($priorities)){
				$this->context->controller->has_errors  = true;
                JError::raiseError(500, JText::_('An error occurred while updating priorities.'));
			}else{
				$this->confirmations[] = $this->l('The price rule has successfully updated');
			}
		}elseif (!JeproshopSpecificPriceModelSpecificPrice::setSpecificPriority((int)$product->product_id, $priorities)){
			$this->context->controller->has_errors  = true;
            JError::raiseError(500, JText::_('An error occurred while setting priorities.'));
		}
	}
	
	public function processCustomizationConfiguration(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$product  = new JeproshopProductModelProduct($app->input->get('product_id'));
		// Get the number of existing customization fields ($product->text_fields is the updated value, not the existing value)
		$current_customization = $product->getCustomizationFieldIds();
		$files_count = 0;
		$text_count = 0;
		if (is_array($current_customization)){
			foreach ($current_customization as $field){
				if ($field->type == 1){
					$text_count++;
				}else{
					$files_count++;
				}
			}
		}
	
		if (!$product->createLabels((int)$product->uploadable_files - $files_count, (int)$product->text_fields - $text_count))
			$this->context->controller->has_errors  = Tools::displayError('An error occurred while creating customization fields.');
		if (!$this->context->controller->has_errors && !$product->updateLabels())
			$this->context->controller->has_errors  = JText::_('An error occurred while updating customization fields.');
		$customizable = ($product->uploadable_files > 0 || $product->text_fields > 0) ? 1 : 0;
		
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('customizable') . " = " . (int)$customizable . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product->product_id;
		
		$db->setQuery($query);
		$result = $db->query();
		
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product_shop') . " SET " . $db->quoteName('customizable') . " = " . (int)$customizable . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product->product_id;
		
		$db->setQuery($query);
		$result &= $db->query();
		if (!$this->context->controller->has_errors && !$result) {
            $this->context->controller->has_errors = true;
            JText::_('An error occurred while updating the custom configuration.' . __FILE__ . ' line ' . __LINE__);
        }
	}

    public function getTaxRulesGroupId(){
        return $this->tax_rules_group_id;
    }

    public static function getTaxRulesGroupIdByProductId($product_id, JeproshopContext $context = null) {
        if (!$context){
            $context = JeproshopContext::getContext();
        }
        $key = 'product_tax_rules_group_id_'.(int)$product_id .'_'.(int)$context->shop->shop_id;
        if (!JeproshopCache::isStored($key)){
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('tax_rules_group_id') . " FROM " . $db->quoteName('#__jeproshop_product_shop') . " WHERE ";
            $query .= $db->quoteName('product_id') . " = " .(int)$product_id . " AND shop_id = " .(int)$context->shop->shop_id;

            $db->setQuery($query);
            $tax_rules_group_id = $db->loadResult();
            JeproshopCache::store($key, $tax_rules_group_id);
        }
        return JeproshopCache::retrieve($key);
    }
	
	public function createLabels($uploadable_files, $text_fields){
		$languages = JeproshopLanguageModelLanguage::getLanguages();
		if ((int)$uploadable_files > 0){
			for ($i = 0; $i < (int)$uploadable_files; $i++){
				if (!$this->createLabel($languages, JeproshopProductModelProduct::CUSTOMIZE_FILE)){ return false; }
			}
		}
					
		if ((int)$text_fields > 0){
			for ($i = 0; $i < (int)$text_fields; $i++){
				if (!$this->createLabel($languages, JeproshopProductModelProduct::CUSTOMIZE_TEXT_FIELD)){ 	return false; }
			}
		}
		return true;
	}

	protected function createLabel(&$languages, $type){
        $db = JFactory::getDBO();

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_customization_field') . "( " . $db->quoteName('product_id') . ", " . $db->quoteName('type') . ", ";
        $query .= $db->quoteName('required') . ") VALUES (" . (int)$this->product_id . ", " . (int)$type . ", 0)";

        $db->setQuery($query);
        $result = $db->query();
        $customization_field_id = $db->insertid();
		// Label insertion
		if (!$result ||	!$customization_field_id){ return false; }
	
		// Multilingual label name creation
		$values = '';
		foreach ($languages as $language) {
            $values .= '(' . (int)$customization_field_id . ', ' . (int)$language->lang_id . ', \'\'), ';
        }

        $values = rtrim($values, ', ');
        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_customization_field_lang') . "(" . $db->quoteName('customization_field_id') . ", " . $db->quoteName('lang_id') . ", ";
        $query .= $db->quoteName('name') . ") VALUES (" . (int)$customization_field_id . ", " . (int)$language->lang_id . ", " . $db->quote($values) . ")";

        $db->setQuery($query);
        if (!$db->query()){ return false; }
	
		// Set cache of feature detachable to true
        JeproshopSettingModelSetting::updateValue('customization_feature_active', '1');
	
        return true;
	}
	
	/**
	 * Delete features
	 *
	 */
	public function deleteFeatures(){
		$db = JFactory::getDBO();
		// List products features
		$query = "SELECT product_feature.*, feature_value.* FROM " . $db->quoteName('#__jeproshop_product_feature') . " AS ";
		$query .= "product_feature LEFT JOIN " . $db->quoteName('#__jeproshop_feature_value') . " AS feature_value ON (";
		$query .= "feature_value." . $db->quoteName('feature_value_id') . " = product_feature." . $db->quoteName('feature_value_id');
		$query .= ") WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id ;
		
		$db->setQuery($query);
		$features = $db->loadObjectList();
		foreach ($features as $feature){
			// Delete product custom features
			if ($feature->custom){
				$query = "DELETE FROM " . $db->quoteName('#__jeproshop_feature_value') . " WHERE " . $db->quoteName('feature_value_id');
				$query .= " = " . (int)$feature->feature_value_id;
				
				$db->setQuery($query);
				$db->query();
				
				$query = "DELETE FROM " . $db->quoteName('#__jeproshop_feature_value_lang') . " WHERE " . $db->quoteName('feature_value_id') . " = " .(int)$feature->feature_value_id;
			
				$db->setQuery($query);
				$db->query();
			}
		}
		// Delete product features
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_feature') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
		
		$db->setQuery($query);
		$db->query();
	
		JeproshopSpecificPriceRuleModelSpecificPriceRule::applyAllRules(array((int)$this->product_id));
		return true;
	}
	
	protected function setCarriers($product = null){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		if (!isset($product)){
			$product = new JeproshopProductModelProduct((int)$app->input->get('product_id'));
		}
		if (JeproshopTools::isLoadedObject($product, 'product_id')){
			$carriers = array();
				
			if($app->input->get('selected_carriers')){
				$carriers = $app->input->get('selected_carriers');
			}
			
			$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_carrier') . " WHERE product_id = " . (int)$product->product_id . " AND shop_id = " . (int)$product->shop_id . ")";
			
			$db->setQuery($query);
			$db->query();
			
			$data = array();

			foreach ($carriers as $carrier)	{
				$data[] = array(
					'product_id' => (int)$product->product_id,
					'carrier_reference_id' => (int)$carrier,
					'shop_id' => (int)$product->shop_id
				);
			}		
		
			$uniqueArray = array();
			foreach($data as $subArray){
				if(!in_array($subArray, $uniqueArray)){
			  		$uniqueArray[] = $subArray;
				}
  		
				if(count($uniqueArray)){
					foreach($uniqueArray as $carrier_data){
						$query = "INSERT IGNORE INTO " . $db->quoteName('#__jeproshop_product_carrier') . "(" . $db->quoteName('product_id');
						$query .= ", " . $db->quoteName('carrier_reference_id') . ", " . $db->quoteName('shop_id') . ") VALUES ( ";
						$query .= (int)$product->product_id . ", " . (int)$carrier_data['carrier_reference_id'] . ", " . (int)$product->shop_id . ")";
					
						$db->setQuery($query);
						$db->query($query);
					} 
				}
			}
		}
	}
	
	/**
	 * Gets carriers assigned to the product
	 */
	public function getCarriers(){
		$db = JFactory::getDBO();
		
		$query = "SELECT carrier.* FROM " . $db->quoteName('#__jeproshop_product_carrier') . " AS product_carrier INNER JOIN ";
		$query .= $db->quoteName('#__jeproshop_carrier') . " AS carrier ON (carrier." . $db->quoteName('reference_id') . " = ";
		$query .= "product_carrier." . $db->quoteName('carrier_reference_id') . " AND carrier." . $db->quoteName('deleted');
		$query .= " = 0) WHERE product_carrier." . $db->quoteName('product_id') . " = " .(int)$this->product_id . "	AND ";
		$query .= "product_carrier." . $db->quoteName('shop_id') . " = " .(int)$this->shop_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function updateProduct(){
		$db = JFactory::getDBO();
        $app = JFactory::getApplication();
		$languages = JeproshopLanguageModelLanguage::getLanguages(false);
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
		
		$this->clearCache();

		$this->date_upd = date('Y-m-d H:i:s');
        if(JeproshopTools::isLoadedObject($this, 'product_id')) {
            $this->removeTaxFromEcotax();
            $product_type_before = $this->getType();
            $this->indexed = 0;

            $input = JRequest::get('post');
            $product_data = isset($input['information']) ? $input['information'] : $input['jform'];

            $existingProduct = $this;

            if(JeproshopShopModelShop::isFeaturePublished() && JeproshopShopModelShop::getShopContext() != JeproshopShopModelShop::CONTEXT_SHOP){
                $this->setFieldsToUpdate((array)$input['multishop_check']);
            }

            if($this->context->shop->getShopContext() == JeproshopShopModelShop::CONTEXT_ALL && !$this->isAssociatedToShop()) {
                $isAssociatedToShop = false;
                $combinations = JeproshopProductModelProduct::getProductAttributesIds($this->product_id);
                if ($combinations) {
                    foreach ($combinations as $combination_id) {
                        $combination = new JeproshopCombinationModelCombination($combination_id);
                        $default_combination = new JeproshopCombinationModelCombination($combination_id, null, $this->default_shop_id);

                        $combination->product_id = (int)$default_combination->product_id;
                        $combination->location = $db->quote($default_combination->location);
                        $combination->ean13 = $db->quote($default_combination->ean13);
                        $combination->upc = $db->quote($default_combination->upc);
                        $combination->quantity = (int)$default_combination->quantity;
                        $combination->reference = $db->quote($default_combination->reference);
                        $combination->supplier_reference = $db->quote($default_combination->supplier_reference);
                        $combination->wholesale_price = (float)$default_combination->wholesale_price;
                        $combination->price = (float)$default_combination->price;
                        $combination->ecotax = (float)$default_combination->ecotax;
                        $combination->weight = $default_combination->weight;
                        $combination->unit_price_impact = (float)$default_combination->unit_price_impact;
                        $combination->default_on = (int)$default_combination->default_on;
                        $combination->available_date = $default_combination->available_date ? $db->quote($default_combination->available_date) : '0000-00-00';
                        $combination->save();
                    }
                }
            }else{ $isAssociatedToShop = true; }


            $shop_list_ids = JeproshopShopModelShop::getCompleteListOfShopsId();
            if (count($this->shop_list_id) > 0) {
                $shop_list_ids = $this->shop_list_id;
            }

            if (JeproshopShopModelShop::checkDefaultShopId('product') && !$this->default_shop_id) {
                $this->default_shop_id = min($shop_list_ids);
            }

            /*$manufacturer_id = $product_data['manufacturer_id'];
            $default_category_id = 1;
            $default_shop_id = JeproshopSettingModelSetting::getValue('default_shop'); */
            //$tax_rules_group_id = $product_data['tax_rules_group_id'];
            $show_price = isset($input_data['show_price']) ? 1 : 0;
            $on_sale = isset($product_data['on_sale']) ? 1 : 0;
            $online_only = isset($product_data['online_only']) ? 1 : 0;
            $available_for_order = isset($product_data['available_for_order']) ? 1 : 0;
            $ean_13 = JeproshopTools::isEan13($product_data['ean13']) ? $product_data['ean13'] : '';
            $upc = JeproshopTools::isUpc($product_data['upc']) ? $product_data['upc'] : '';
            $reference = JeproshopTools::isReference($product_data['reference']) ? $product_data['reference'] : '';
            //$product_type = $product_data['product_type'];
            $published = $product_data['published'];
            $redirect_type = $product_data['redirect_type'];
            /*$ecotax = $product_data['ecotax'];
            $unity = $product_data['unity'];
            $unit_price = (float)$product_data['unit_price'];
            $wholesale_price = (float)$product_data['wholesale_price']; */
            $visibility = $product_data['visibility'];
            $condition = $product_data['condition'];
            $result = true;

            /** data base updating **/
            $query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('reference') . " = " . $db->quote($reference) . ", " . $db->quoteName('on_sale') . " = " . (int)$on_sale . ", ";
            $query .= $db->quoteName('online_only') . " = " . (int)$online_only . ", " . $db->quoteName('ean13') . " = " . $db->quote($ean_13) . ", " . $db->quoteName('upc') . " = " . $db->quote($upc) . ", ";
            $query .= $db->quoteName('published') . " = " . (int)$published . ", " . $db->quoteName('redirect_type') . "= " . $db->quote($redirect_type) . ", " . $db->quoteName('visibility') . " = " . $db->quote($visibility) . ", ";
            $query .= $db->quoteName('product_redirected_id') . " = " . $db->quote($product_data['product_redirected_id']) . ", " . $db->quoteName('available_for_order') . " = " . (int)$available_for_order .", ";
            $query .= $db->quoteName('show_price') . " = " . (int)$show_price . ", " . $db->quoteName('condition') . " = " . $db->quote($condition) . ", " . $db->quoteName('date_upd') . " = " . $db->quote($this->date_upd) ;
            $query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;

            $db->setQuery($query);
            $result &= $db->query();
            if($result) {
                // Database insertion for multishop fields related to the object
                if (JeproshopShopModelShop::isTableAssociated('product')) {
                    $default_category_id = 1;
                    /* Shop fields */
                    foreach ($shop_list_ids as $shop_id) {
                        $query = "UPDATE " . $db->quoteName('#__jeproshop_product_shop') . " SET " . $db->quoteName('default_category_id') . " = " . (int)$default_category_id;
                        $query .= ", " . $db->quoteName('online_only') . "  = " . (int)$online_only;
                        $query .= ", " . $db->quoteName('redirect_type') . " = " . $db->quote($redirect_type) . ", " . $db->quoteName('product_redirected_id') . " = " . (int)$product_data['product_redirected_id'];
                        $query .= ", " . $db->quoteName('available_for_order') . " = " . (int)$available_for_order . ", " . $db->quoteName('condition') . " = " . $db->quote($product_data['condition']);
                        $query .= ", " . $db->quoteName('published') . " = " . (int)$product_data['published'] . ", " . $db->quoteName('show_price') . " = " . (int)$show_price;
                        //$query .= ", " . $db->quoteName('additional_shipping_cost') . " = " . (float)$product_data['additional_shipping_cost'];
                        $query .= ", " . $db->quoteName('visibility') . " = " . $db->quote($product_data['visibility']) . ", " . $db->quoteName('date_upd') . " = " . $db->quote($this->date_upd);
                        $query .= " WHERE " . $db->quoteName('product_id') . " = " . $this->product_id . " AND " . $db->quoteName('shop_id') . " = " . $shop_id;

                        $db->setQuery($query);
                        $result &= $db->query();
                        if($result) {
                            /* Multilingual fields */
                            foreach ($languages as $language) {
                                $query = "UPDATE " . $db->quoteName('#__jeproshop_product_lang') . " SET " . $db->quoteName('description') . " = ";
                                $query .= $db->quote($product_data['description_' . $language->lang_id]) . ", " . $db->quoteName('short_description');
                                $query .= " = " . $db->quote($product_data['short_description_' . $language->lang_id]) . ", " . $db->quoteName('name');
                                $query .= " = " . $db->quote($product_data['name_' . $language->lang_id]) . " WHERE " . $db->quoteName('product_id') . " = ";
                                $query .= (int)$this->product_id . " AND " . $db->quoteName('shop_id');
                                $query .= " = " . (int)$shop_id . " AND " . $db->quoteName('lang_id') . " = " . (int)$language->lang_id;

                                $db->setQuery($query);
                                $result &= $db->query();
                            }
                        }
                    }
                }
            }

            if($result) {
                // If the product doesn't exist in the current shop but exists in another shop
                if (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP && $existingProduct->isAssociatedToShop($this->context->shop->shop_id)) {
                    $outOfStock = JeproshopStockAvailableModelStockAvailable::outOfStock($existingProduct->product_id, $existingProduct->default_shop_id);
                    $dependsOnStock = JeproshopStockAvailableModelStockAvailable::dependsOnStock($existingProduct->product_id, $existingProduct->default_shop_id);
                    JeproshopStockAvailableModelStockAvailable::setProductOutOfStock((int)$this->product_id, $outOfStock, $this->context->shop->shop_id);
                    JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock((int)$this->product_id, $dependsOnStock, $this->context->shop->shop_id);
                }

                if (in_array($this->context->shop->getShopContext(), array(JeproshopShopModelShop::CONTEXT_SHOP, JeproshopShopModelShop::CONTEXT_ALL))) {
                    $this->addCarriers();
                    $this->updateAccessories();
                    $this->processSuppliers();
                    $this->processFeatures();
                    $this->processProductAttribute();
                    $this->processProductAttribute();
                    $this->processPriceAddition();
                    $this->processSpecificPricePriorities();
                    $this->processCustomizationConfiguration();
                    $this->processAttachments();
                    $this->updatePackItems();

                    // Disallow advanced stock management if the product become a pack
                    if ($product_type_before == JeproshopProductModelProduct::SIMPLE_PRODUCT && $this->getType() == JeproshopProductModelProduct::PACKAGE_PRODUCT) {
                        JeproshopStockAvailableModelStockAvailable::setProductDependsOnStock($this->product_id, false);
                    }
                    $this->updateDownloadProduct(1);
                    $this->updateTags(JeproshopLanguageModelLanguage::getLanguages(false));

                    if ($this->isProductFieldUpdated('category_box') && !$this->updateCategories($product_data['category_box'])) {
                        JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_LINKING_THE_PRODUCT_TO_CATEGORIES_MESSAGE'));
                        $this->context->controller->has_errors = true;
                    } //TODO correct category update and check php $_POST limit to handle the form
                }

                $this->processWarehouses();
                $category_link = $app->input->get('category_id') ? '&category_id=' . $app->input->get('category_id') : '';
                if (!$this->context->controller->has_errors) {
                    if (in_array($this->visibility, array('both', 'search')) && JeproshopSettingModelSetting::getValue('search_indexation')) {
                        JeproshopSearch::indexation(false, $this->product_id);
                    }

                    //save and preview
                    $message = JText::_('COM_JEPROSHOP_UPDATE_SUCCESSFULLY_MESSAGE');
                    if ($app->input->get('task') == 'save_preview') {
                        $link = $this->getPreviewUrl();
                    } else {
                        if ($app->input->get('task') == 'edit') {
                            $link = JRoute::_('index.php?option=com_jeproshop&view=product&task=edit&product_id=' . $this->product_id . $category_link . JeproshopTools::getProductToken());
                        } else {
                            $link = JRoute::_('index.php?option=com_jeproshop&view=product&' . $category_link);
                            $message = JText::_('COM_JEPROSHOP_UPDATE_SUCCESSFULLY_MESSAGE');
                        }
                    }
                    $app->redirect($link, $message);
                } else {
                    $app->input->set('task', 'edit');
                    $app->redirect('index.php?option=com_jeproshop&view=product&task=edit&product_id=' . $this->product_id . $category_link . JeproshopTools::getProductToken());
                }
            }else{
                if(!$isAssociatedToShop && $combinations){
                    foreach($combinations as $combination_id){
                        $combination = new JeproshopCombinationModelCombination((int)$combination_id);
                        $combination->delete();
                    }
                    JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_UPDATING_A_PRODUCT_MESSAGE'));
                }
            }



            $this->setGroupReduction();

            if ($this->getType() == JeproshopProductModelProduct::VIRTUAL_PRODUCT && $this->published && !JeproshopSettingModelSetting::getValue('virtual_product_feature_active')) {
                JeproshopSettingModelSetting::updateValue('virtual_product_feature_active', '1');
            }
            return true;
        }
	}
	
	/**
	 * Set Group reduction if needed
	 */
	public function setGroupReduction(){
		return JeproshopGroupReductionModelGroupReduction::setProductReduction($this->product_id, null, $this->default_category_id);
	}

    /**
     * It is NOT possible to delete a product if there are currently:
     * - physical stock for this product
     * - supply order(s) for this product
     **/
    public function delete(){
        $db = JFactory::getDBO();
        if(JeproshopSettingModelSetting::getValue('advanced_stock_management') && $this->advanced_stock_management){
            $stockManager = JeproshopStockManagerFactory::getManager();
            $physicalQuantity = $stockManager->getProductPhysicalQuantities($this->product_id, 0);
            $realQuantity = $stockManager->getProductRealQuantities($this->product_id, 0);
            if($physicalQuantity > 0){ return false; }
            if($realQuantity > $physicalQuantity){ return false; }
        }

        $this->clearCache();
        $result = true;

        if(JeproshopShopModelShop::isTableAssociated('product')){
            $shopListIds = JeproshopShopModelShop::getContextShopGroupID();
            if(count($this->shop_list_id) > 0){
                $shopListIds = $this->shop_list_id;
            }

            if(!is_array($shopListIds)){ $shopListIds = array($shopListIds); }
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_shop') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id . " AND " . $db->quoteName('shop_id') . " IN(" . implode($shopListIds) . ")";
            $db->setQuery($query);
            $result &= $db->query();
        }

        $hasMultiShopEntries = $this->hasMultishopEntries();
        if($result && !$hasMultiShopEntries){
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product') . " WHERE " . $db->quoteName('product_id') . " = " . $this->product_id;
            $db->setQuery($query);
            $result &= $db->query();
        }

        if(!$result){ return false; }

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_lang') . " WHERE " . $db->quoteName('product_id') . " = " . $this->product_id;
        $db->setQuery($query);
        $result &= $db->query();

        JeproshopStockAvailableModelStockAvailable::removeProductFromStockAvailable($this->product_id);
        $result &= ($this->deleteProductAttributes() && $this->deleteImages() && $this->deleteSceneProducts());

        if($this->hasMultiShopEntries()){ return true; }

        if (!$result || !JeproshopGroupReductionModelGroupReduction::deleteProductReduction($this->product_id) || !$this->deleteCategories(true) ||
            !$this->deleteProductFeatures() || !$this->deleteTags() || !$this->deleteCartProducts() || !$this->deleteAttributesImpacts() ||
            !$this->deleteAttachments(false) ||!$this->deleteCustomization() || !JeproshopSpecificPriceModelSpecificPrice::deleteByProductId((int)$this->product_id) ||
            !$this->deletePack() || !$this->deleteProductSale() || !$this->deleteSearchIndexes() || !$this->deleteAccessories() || !$this->deleteFromAccessories() ||
            !$this->deleteFromSupplier() || !$this->deleteDownload() || !$this->deleteFromCartRules()) {
            return false;
        }

        return true;
    }

    /**
     * Delete product images from database
     *
     * @return bool success
     */
    public function deleteImages(){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('image_id') . " FROM " . $db->quoteName('#__jeproshop_image') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $status = true;
        if ($result) {
            foreach ($result as $row) {
                $image = new JeproshopImageModelImage($row->image_id);
                $status &= $image->delete();
            }
        }
        return $status;
    }

    /**
     * Delete product from cart
     *
     * @return array Deletion result
     */
    public function deleteCartProducts(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart_product') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        return $db->query();;
    }

    /**
     * Delete products tags entries
     *
     * @return array Deletion result
     */
    public function deleteTags(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_tag') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        $res = $db->query();
        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_tag') . " WHERE " . $db->quoteName('tag_id') . " IN (SELECT " . $db->quoteName('tag_id') . " FROM " . $db->quoteName('#__jeproshop_product_tag') . ")";
        $db->setQuery($query);
        $res &= $db->query();
        return $res;
    }

    /**
     * Delete product attributes
     *
     * @return array Deletion result
     */
    public function deleteProductAttributes(){
        //Hook::exec('actionProductAttributeDelete', array('id_product_attribute' => 0, 'id_product' => $this->id, 'deleteAllAttributes' => true));

        $result = true;

        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('product_attribute_id') . " FROM " . $db->quoteName('#__jeproshop_product_attribute') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        $combinations = $db->loadObjectList();

        foreach ($combinations as $combination_id) {
            $combination = new JeproshopCombinationModelCombination($combination_id);
            $result &= $combination->delete();
        }
        JeproshopSpecificPriceRuleModelSpecificPriceRule::applyAllRules(array((int)$this->product_id));
        JeproshopTools::clearColorListCache($this->product_id);
        return $result;
    }

    /**
     * Delete product attributes impacts
     *
     * @return Deletion result
     */
    public function deleteAttributesImpacts(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_attribute_impact') . " WHERE " . $db->quoteName('product_id') ." = " . (int)$this->product_id;

        $db->setQuery($query);
        return $db->query();
    }


    /**
     * Delete product features
     *
     * @return array Deletion result
     */
    public function deleteProductFeatures(){
        JeproshopSpecificPriceRuleModelSpecificPriceRule::applyAllRules(array((int)$this->product_id));
        return $this->deleteFeatures();
    }

    /**
     * Delete product attachments
     *
     * @param boolean $update_attachment_cache If set to true attachment cache will be updated
     * @return array Deletion result
     */
    public function deleteAttachments($update_attachment_cache = true){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attachment') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);

        $res = $db->query();

        if (isset($update_attachment_cache) && (bool)$update_attachment_cache === true)
            JeproshopProductModelProduct::updateCacheAttachment((int)$this->product_id);

        return $res;
    }

    /**
     * Delete product customizations
     *
     * @return array Deletion result
     */
    public function deleteCustomization(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_customization_field') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);

        $res = $db->query();
        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_customization_field_lang') . " WHERE " . $db->quoteName('customization_field_id') . " NOT IN (SELECT ";
        $query .= $db->quoteName('customization_field_id') . " FROM " . $db->quoteName('#__jeproshop_customization_field') .") ";
        $db->setQuery($query);

        $res &= $db->query();

        return $res;
    }

    /**
     * Delete product pack details
     *
     * @return array Deletion result
     */
    public function deletePack(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_pack') . " WHERE " . $db->quoteName('product_pack_id') . " = " . (int)$this->product_id . " OR " . $db->quoteName('product_item_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Delete product indexed words
     *
     * @return array Deletion result
     */
    public function deleteSearchIndexes(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_search_index') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);

        $res = $db->query();
        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_search_word') . " WHERE " . $db->quoteName('word_id') . " NOT IN (SELECT " . $db->quoteName('word_id') . " FROM " . $db->quoteName('#__jeproshop_search_index') . ")";
        $db->setQuery($query);

        $res &= $db->query();
        return $res;
    }

    /**
     * Delete product in its scenes
     *
     * @return array Deletion result
     */
    public function deleteSceneProducts(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_scene_product') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;

        $db->setQuery($query);
        return$db->query();
    }

    /**
     * Delete product sales
     *
     * @return array Deletion result
     */
    public function deleteProductSale(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_sale') . " WHERE " . $db->quoteName('product_id') . " = " . $this->product_id;

        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Remove all downloadable files for product and its attributes
     *
     * @return bool
     */
    public function deleteDownload(){
        $result = true;
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('product_download_id') . " FROM " . $db->quoteName('#__jeproshop_product_download') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        $collection_ids = $db->loadObjectList();

        foreach ($collection_ids as $product_download_id) {
            $product_download = new JeproshopProductDownloadModelProductDownload((int)$product_download_id);
            $result &= $product_download->delete($product_download->checkFile());
        }

        return $result;
    }

    /**
     * Delete products tags entries without delete tags for webservice usage
     *
     * @return array Deletion result
     */
    public function deleteWsTags(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_tag') . " WHERE " . $db->quoteName('product_id') . " = " .(int)$this->product_id;
        $db->setQuery($query);
        return $db->query();
    }

    public function deleteFromCartRules(){
        JeproshopCartRuleModelCartRule::cleanProductRuleIntegrity('products', $this->product_id);
        return true;
    }

    public function deleteFromSupplier(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_supplier') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Delete several objects from database
     *
     * @param array $selection
     * @return bool Deletion result
     */
    public function deleteSelection($selection){
        $result = true;
        foreach ($selection as $product_id){
            $this->product_id = (int)$product_id;
            $result = $result && $this->delete();
        }
        return $result;
    }

    /**
     * Check if there is more than one entries in associated shop table for current entity
     *
     * @since 1.5.0
     * @return bool
     */
    public function hasMultishopEntries(){
        if (!JeproshopShopModelShop::isTableAssociated('product') || !JeproshopShopModelShop::isFeaturePublished()) {
            return false;
        }

        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_product_shop') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id;
        $db->setQuery($query);
        return (bool)$db->loadResult();
    }
	
	private function clearCache($all = FALSE){
		if($all){
			JeproshopCache::clean('jeproshop_product_model_*');
		}elseif($this->product_id){
			JeproshopCache::clean('jeproshop_product_model_' . $this->product_id . '_*');
		}
	}
}

/** ----------- PRODUCT DOWNLOAD ---------- **/
class JeproshopProductDownloadModelProductDownload extends JModelLegacy
{
	/** @var integer Product id which download belongs */
	public $product_download_id;

    public $product_id;
	
	/** @var string DisplayFilename the name which appear */
	public $display_filename;
	
	/** @var string PhysicallyFilename the name of the file on hard disk */
	public $filename;
	
	/** @var string DateDeposit when the file is upload */
	public $date_add;
	
	/** @var string DateExpiration deadline of the file */
	public $date_expiration;
	
	/** @var string NbDaysAccessible how many days the customer can access to file */
	public $nb_days_accessible;
	
	/** @var string NbDownloadable how many time the customer can download the file */
	public $nb_downloadable;
	
	/** @var boolean Active if file is accessible or not */
	public $published = 1;
	
	/** @var boolean is_sharable indicates whether the product can be shared */
	public $is_sharable = 0;
	
	protected static $_productIds = array();
	
	public function __construct($product_download_id = NULL){
		$db = JFactory::getDBO();
		
		if($product_download_id){
			$cache_id = 'jeproshop_product_download_model_' . $product_download_id ;
			if(!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_product_download') . " AS product_download ";

		
				/** Get shop informations **/
				if(JeproshopShopModelShop::isTableAssociated('product')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_product_shop') . " AS product_shop ON (";
					$query .= "product.product_id = product_shop.product_id AND product_shop.shop_id = " . (int)  $this->shop_id . ")";
				}
				$query .= " WHERE product_download." . $db->quoteName('product_download_id') . " = " . (int)$product_download_id;
		
				$db->setQuery($query);
				$product_download_data = $db->loadObject();
		
				if($product_download_data){
					JeproshopCache::store($cache_id, $product_download_data);
				}
			}else{
				$product_download_data = JeproshopCache::retrieve($cache_id);
			}
		
			if($product_download_data){
				$product_download_data->product_download_id = $product_download_id;
				foreach($product_download_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
	}

    /**
     * Return the id_product_download from an id_product
     *
     * @param $product_id
     * @internal param int $id_product Product the id
     * @return integer Product the id for this virtual product
     */
	public static function getIdFromProductId($product_id){
		if (!JeproshopProductDownloadModelProductDownload::isFeaturePublished()){
			return false;
		}
		if (array_key_exists((int)$product_id, self::$_productIds)){
			return self::$_productIds[$product_id];
		}
		$db = JFactory::getDBO();
		$query = "SELECT " . $db->quoteName('product_download_id') . " FROM " . $db->quoteName('#__jeproshop_product_download');
		$query .= " WHERE " . $db->quoteName('product_id') . " = " .(int)$product_id . " AND " . $db->quoteName('published') . " = 1 ";
		$query .= "	ORDER BY " . $db->quoteName('product_download_id') . " DESC";
		
		$db->setQuery($query);
		self::$_productIds[$product_id] = (int)$db->loadResult();
	
		return self::$_productIds[$product_id];
	}

    /**
     * Check if file exists
     *
     * @return boolean
     */
    public function checkFile(){
        if (!$this->filename) return false;
        return file_exists(COM_JEPROSHOP_PRODUCT_DOWNLOAD_DIR . $this->filename);
    }

    public function delete($delete_file = false){
        $result = parent::delete();
        if ($result && $delete_file)
            return $this->deleteFile();
        return $result;
    }

    /**
     * Delete the file
     * @param int $product_download_id : if we need to delete a specific product attribute file
     *
     * @return boolean
     */
    public function deleteFile($product_download_id = null){
        if (!$this->checkFile())
            return false;

        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_download') . " WHERE " . $db->quoteName('product_download_id') . " = " .(int)$product_download_id;

        $db->setQuery($query);
        return unlink(COM_JEPROSHOP_PRODUCT_DOWNLOAD_DIR . $this->filename) && $db->query();
    }

    /**
     * Return the display filename from a physical filename
     *
     * @param string $filename Filename physically
     * @return integer Product the id for this virtual product
     */
    public static function getIdFromFilename($filename){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('product_download_id') . " FROM " .  $db->quoteName('#__jeproshop_product_download') . " WHERE " . $db->quoteName('filename') . " = " . $db->quote($filename);
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Return the filename from an id_product
     *
     * @param int $product_id Product the id
     * @return string Filename the filename for this virtual product
     */
    public static function getFilenameFromProductId($product_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('filename') . " FROM " . $db->quoteName('#__jeproshop_product_download') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id . " AND " . $db->quoteName('published') . " = 1 ";

        $db->setQuery($query);
        return $db->loadResult();
    }

    public function add($autodate = true, $null_values = false){
        $db = JFactory::getDBO();

        $this->date_add = date('Y-m-d H:i:s');

        if(isset($this->product_download_id) && !$this->force_id){ unset($this->product_download_id); }
        return (bool)parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)){
            // Refresh cache of feature detachable because the row can be deactive
            Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', ProductDownload::isCurrentlyUsed($this->def['table'], true));
            return true;
        }
        return false;
    }
	
	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeaturePublished(){
		return JeproshopSettingModelSetting::getValue('virtual_product_feature_active');
	}
}


class JeproshopProductPack extends JeproshopProductModelProduct
{
	protected static $cachePackItems = array();
	protected static $cacheIsPack = array();
	protected static $cacheIsPacked = array();
	
	/**
	 * Is product a pack?
	 *
	 * @static
	 * @param $product_id
	 * @return bool
	 */
	public static function isPack($product_id){
		if (!JeproshopProductPack::isFeaturePublished()){
			return false;
		}
		
		if (!$product_id){ return false; }
	
		if (!array_key_exists($product_id, self::$cacheIsPack)){
			$db = JFactory::getDBO();
			
			$query = "SELECT COUNT(*) FROM " .$db->quoteName('#__jeproshop_product_pack') . " WHERE " . $db->quoteName('product_pack_id') . " = " . (int)$product_id;
            $db->setQuery($query);
			$result = $db->loadResult();
			self::$cacheIsPack[$product_id] = ($result > 0);
		}
		return self::$cacheIsPack[$product_id];
	}
	
	/**
	 * Is product in a pack?
	 *
	 * @static
	 * @param $product_id
	 * @return bool
	 */
	public static function isPacked($product_id){
		if (!JeproshopProductPack::isFeaturePublished()){
			return false;
		}
		if (!array_key_exists($product_id, self::$cacheIsPacked)){
			$db = JFactory::getDBO();
			
			$query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_product_pack') . " WHERE product_item_id = " . (int)$product_id;
			
			$db->setQuery($query);
			$result = $db->loadResult();
			self::$cacheIsPacked[$product_id] = ($result > 0);
		}
		return self::$cacheIsPacked[$product_id];
	}
	
	public static function deleteItems($product_id){
		$db = JFactory::getDBO();
		$query = "UPDATE " . $db->quoteName('#__jeproshop_product') . " SET " . $db->quoteName('cache_is_pack');
		$query .= " = 0 WHERE product_id = " . (int)$product_id;
		
		$db->setQuery($query);
		$result = $db->query();
		
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_pack') . " WHERE " . $db->quoteName('product_pack_id');
		$query .= " = " . (int)$product_id;
		
		$db->setQuery($query);
		$result &= $db->query();
		
		return ($result && JeproshopSettingModelSetting::updateValue('pack_feature_active', JeproshopProductPack::isCurrentlyUsed()));
	}
	
	public static function noPackPrice($product_id){
		$sum = 0;
		$price_display_method = !self::$_taxCalculationMethod;
		$items = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
		foreach ($items as $item)
			$sum += $item->getPrice($price_display_method) * $item->pack_quantity;
		return $sum;
	}
	
	public static function getItems($product_id, $lang_id){
		if (!JeproshopProductPack::isFeaturePublished())
			return array();
	
		if (array_key_exists($product_id, self::$cachePackItems)) {
            return self::$cachePackItems[$product_id];
        }

        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('product_item_id') . ", " . $db->quoteName('quantity') . " FROM " . $db->quoteName('#__jeproshop_pack') . " WHERE " . $db->quoteName('product_pack_id') . " = " .(int)$product_id;

        $db->setQuery($query);
		$result = $db->loadObject();
		$array_result = array();
		foreach ($result as $row){
			$product = new JeproshopProductModelProduct($row->product_item_id, false, $lang_id);
			$product->loadStockData();
			$product->pack_quantity = $row->quantity;
			$array_result[] = $product;
		}
		self::$cachePackItems[$product_id] = $array_result;
		return self::$cachePackItems[$product_id];
	}
	

	
	public static function getItemTable($product_id, $lang_id, $full = false){
		if (!JeroshopProductPack::isFeaturePublished()){ return array(); }
	
		$query = "SELECT product.*, product_shop.*, product_lang.*, MAX(image_shop." . $db->quoteName('image_id') . " AS image_id, image_lang." . $db->quoteName('legend') . ", category_lang.";
        $query .= $db->quoteName('name') . " AS default_category, pack." . $db->quoteName('quantity') . " AS pack_quantity, product_shop." . $db->quoteName('default_category_id') . ", pack.";
        $query .= $db->quoteName('product_pack_id') . " FROM " . $db->quoteName('#__jeproshop_pack') . " AS pack LEFT JOIN " . $db->quoteame('#__jeproshop_product') . " AS product ON (product.";
        $query .= $db->quoteName('product_id') . " = product." . $db->quoteName('product_item_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON (product.";
        $query .= $db->quoteName('product_id') . " = product_lang." . $db->quoteName('product_id') . " AND product_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id ;
        $query .= JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang') . " LEFT JOIN " . $db->quoteName('#__jeproshop_image') . " AS image ON (image." . $db->quoteName('product_id');
        $query .= " = product." . $db->quoteName('product_id') . ") " . JeproshopShopModelShop::addSqlAssociation('image', false, 'image_shop.cover=1') . " LEFT JOIN " . $db->quoteName('#__jeproshop_image_lang');
        $query .= " AS image_lang ON (image." . $db->quoteName('image_id') . " = image_lang." . $db->quoteName('image_id') . " AND image_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id;
        $query .= ") " . JeproshopShopModelShop::addSqlAssociation('product') . " LEFT JOIN " . $db->quoteName('#__jeproshop_category_lang') .  " AS category_lang ON product_shop." . $db->quoteName('default_category_id');
        $query .= " = category_lang." . $db->quoteName('category_id') . " AND category_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('category_lang');
        $query .= " WHERE product_shop."  . $db->quoteName('shop_id') . " = " . (int)JeproshopContext::getContext()->shop->shop_id . " AND pack." . $db->quoteName('product_pack_id') . " =" . (int)$product_id;
        $query .= " GROUP BY product_shop." . $db->quoteName('product_id');

        $db->setQuery($query);
		$result = $db->loadObjectList();
	
		foreach ($result as &$line)
			$line = JeproshopProductModelProduct::getTaxesInformations($line);
			
		if (!$full)
			return $result;
	
		$array_result = array();
		foreach ($result as $prow) {
            if (!JeproshopProductPack::isPack($prow->product_id)) {
                $array_result[] = JeproshopProductModelProduct::getProductProperties($lang_id, $prow);
            }
        }
		return $array_result;
	}
	
	public static function getPacksTable($product_id, $lang_id, $full = false, $limit = null){
		if (!JeproshopProductPack::isFeaturePublished()) {
            return array();
        }

        $db = JFactory::getDBO();

        $query = "SELECT GROUP_CONCAT(pack." . $db->quoteName('product_pack_id') . ") FROM " . $db->quoteName('#__jeproshop_pack') . " AS pack WHERE pack." . $db->qsuoteName('product_item_id') . " = " . (int)$product_id;

        $db->quoteName($query);
		$packs = $db->loadResult();
	
		if (!(int)$packs){ return array(); }
	
		$query = "SELECT product.*, product_shop.*, product_lang.*, MAX(image_shop." . $db->quoteName('image_id') . ") image_id, image_lang." . $db->quoteName('legend') . " FROM " . $db->quoteName('#__jeproshop_product') . " AS product NATURAL LEFT JOIN ";
        $query .= $db->quoteName('#__jeproshop_product_lang') . " AS product_lang " . JeproshopShopModelShop::addSqlAssociation('product') . " LEFT JOIN " . $db->quoteName('#__jeproshop_image') . " AS image ON (image." . $db->quoteName('product_id') . " = product.";
        $query .= $db->quoteName('product_id') . ") " . JeproshopShopModelShop::addSqlAssociation('image', false, 'image_shop.cover=1') . "	LEFT JOIN " . $db->quoteName('#__jeproshop_image_lang') . " AS image_lang ON (image." . $db->quoteName('image_id');
        $query .= " = image_lang." . $db->quoteName('image_id') . " AND image_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . ") WHERE product_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang');
        $query .= "	AND product." . $db->quoteName('product_id') . " IN (" . $packs . ") GROUP BY product_shop.product_id";
		if ($limit)
			$query .= " LIMIT " . (int)$limit;

        $db->setQuery($query);
		$result = $db->loadObjectList();
		if (!$full)
			return $result;
	
		$array_result = array();
		foreach ($result as $row)
			if (!JeproshopProductPack::isPacked($row->product_id))
				$array_result[] = JeproshopProductModelProduct::getProductProperties($lang_id, $row);
		return $array_result;
	}
	
	/**
	 * Add an item to the pack
	 *
	 * @param integer $id_product
	 * @param integer $id_item
	 * @param integer $qty
	 * @return boolean true if everything was fine
	 */
	public static function addItem($id_product, $id_item, $qty)
	{
		return Db::getInstance()->update('product', array('cache_is_pack' => 1), 'id_product = '.(int)$id_product) &&
		Db::getInstance()->insert('pack', array('id_product_pack' => (int)$id_product, 'id_product_item' => (int)$id_item, 'quantity' => (int)$qty)) &&
		Configuration::updateGlobalValue('PS_PACK_FEATURE_ACTIVE', '1');
	}
	
	public static function duplicate($id_product_old, $id_product_new){
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pack (id_product_pack, id_product_item, quantity)
		(SELECT '.(int)$id_product_new.', id_product_item, quantity FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int)$id_product_old.')');
	
		// If return query result, a non-pack product will return false
		return true;
	}
	
	/**
	 * This method is allow to know if a Pack entity is currently used
	 * @since 1.5.0
	 * @param $table
	 * @param $has_active_column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table = null, $has_active_column = false){
		$db = JFactory::getDBO();
		
		$query = "SELECT " . $db->quoteName('product_pack_id') . " FROM " . $db->quoteName('#__jeproshop_product_pack');
		
		$db->setQuery($query);
		// We don't use the parent method because the identifier isn't id_pack
		return (bool)$db->loadResult();
	}
	
	/**
	 * For a given pack, tells if it has at least one product using the advanced stock management
	 *
	 * @param int $product_id pack id
	 * @return bool
	 */
	public static function usesAdvancedStockManagement($product_id){
		if (!JeproshopProductPack::isPack($product_id))
			return false;

		$products = JeproshopProductPack::getItems($product_id, JeproshopSettingModelSetting::getValue('default_lang'));
		foreach ($products as $product){
			// if one product uses the advanced stock management
			if ($product->advanced_stock_management == 1)
				return true;
		}
		// not used
		return false;
	}
	
	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeaturePublished(){
		return JeproshopSettingModelSetting::getValue('pack_feature_active');
	}
}


class JeproshopProductSupplierModelProductSupplier extends JModelLegacy
{
	public $product_supplier_id;
	/**
	 * @var integer product ID
	 * */
	public $product_id;
	
	/**
	 * @var integer product attribute ID
	 * */
	public $product_attribute_id;
	
	/**
	 * @var integer the supplier ID
	 * */
	public $supplier_id;
	
	/**
	 * @var string The supplier reference of the product
	 * */
	public $product_supplier_reference;
	
	/**
	 * @var integer the currency ID for unit price tax excluded
	 * */
	public $currency_id;
	
	/**
	 * @var string The unit price tax excluded of the product
	 * */
	public $product_supplier_price_te;

    public function __construct($product_supplier_id = null, $lang_id = null, $shop_id = null){

    }

    /**
     * For a given product, retrieves its suppliers
     *
     * @param $product_id
     * @param bool|int $group_by_supplier
     * @internal param int $id_product
     * @return Array Collection of ProductSupplier
     */
	public static function getSupplierCollection($product_id, $group_by_supplier = true){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_product_supplier') . " WHERE " . $db->quoteName('product_id');
		$query .= " = " . (int)$product_id . ($group_by_supplier ? " GROUP BY " . $db->quoteName('supplier_id') : "");
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * For a given product and supplier, gets corresponding ProductSupplier ID
	 *
	 * @param int $product_id
	 * @param int $product_attribute_id
	 * @param int $supplier_id
	 * @return array
	 */
	public static function getIdByProductAndSupplier($product_id, $product_attribute_id, $supplier_id){
		$db = JFactory::getDBO();
		// build query
		$query = "SELECT product_supplier.product_supplier_id FROM " . $db->quoteName('#__jeproshop_product_supplier') . " AS";
		$query .= " product_supplier  WHERE product_supplier.product_id = " .(int)$product_id . " AND product_supplier.product_attribute_id";
		$query .= " = " . (int)$product_attribute_id . " AND product_supplier.supplier_id = " .(int)$supplier_id . "";
	
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function save(){
		return (int)$this->product_supplier_id > 0 ? $this->update() : $this->add();
	}
	
	public function update(){
		$db = JFactory::getDBO();
	}
	
	public function add(){
		$db = JFactory::getDBO();
		
		$query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_supplier') . "(" . $db->quoteName('product_id') . ", " ;
		$query .= $db->quoteName('product_attribute_id') . ", " . $db->quoteName('supplier_id') . ", " . $db->quoteName('product_supplier_reference');
		$query .= ", " . $db->quoteName('product_supplier_price_te') . ", " . $db->quoteName('currency_id') . ") VALUES(" .(int)$this->product_id;
		$query .= ", " . (int)$this->product_attribute_id . ", " . (int)$this->supplier_id . ", " . $db->quote($this->product_supplier_reference);
		$query .= ", " . (float)$this->product_supplier_price_te . ", " . (int)$this->currency_id . ")";
		
		$db->setQuery($query);
		$db->query();
	}
	
}