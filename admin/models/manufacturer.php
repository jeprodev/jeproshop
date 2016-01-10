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

class JeproshopManufacturerModelManufacturer extends JModelLegacy
{
    /** @var integer manufacturer ID //FIXME is it really usefull...? */
    public $manufacturer_id;

    /** @var string Name */
    public $name;

    /** @var string A description */
    public $description;
    public $short_description;

    /** @var int Address */
    public $address_id;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var string Friendly URL */
    public $link_rewrite;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string Meta description */
    public $meta_description;

    /** @var boolean active */
    public $published;


    private $pagination;
	
	protected static $_cache_name = array();

	public function __construct($manufacturer_id = null, $lang_id = null){
		$db = JFactory::getDBO();

        if ($lang_id !== null) {
            $this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) !== false) ? $lang_id : JeproshopSettingModelSetting::getValue('default_lang');
        }

        if ($manufacturer_id) {
            // Load object from database if object id is present
            $cache_id = 'jeproshop_manufacturer_model_' . (int)$manufacturer_id . '_' . (int)$lang_id;
            $db = JFactory::getDBO();
            if (!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_manufacturer') . " AS manufacturer ";

                // Get lang informations
                if ($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_manufacturer_lang') . " AS manufacturer_lang ON (manufacturer." . $db->quoteName('manufacturer_id');
                    $query .= " = manufacturer_lang." . $db->quoteName('manufacturer_id') . " AND manufacturer_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }
                $query .= " WHERE manufacturer." . $db->quoteName('manufacturer_id') . " = " . (int)$manufacturer_id;
                // Get shop informations
                /*if (Shop::isTableAssociated($this->def['table']))
                    $sql->leftJoin($this->def['table'].'_shop', 'c', 'a.'.$this->def['primary'].' = c.'.$this->def['primary'].' AND c.id_shop = '.(int)$this->id_shop); */
                $db->setQuery($query);
                $manufacturer_data = $db->loadObject();
                if ($manufacturer_data){
                    if (!$lang_id ){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_manufacturer_lang')  . " WHERE " . $db->quoteName('manufacturer_id') . " = " . (int)$manufacturer_id;

                        $db->setQuery($query);
                        $manufacturer_data_lang = $db->loadObjectList();
                        if ($manufacturer_data_lang)
                            foreach ($manufacturer_data_lang as $row)
                                foreach ($row as $key => $value){
                                    if (array_key_exists($key, $this) && $key != 'manufacturer_id'){
                                        if (!isset($manufacturer_data->{$key}) || !is_array($manufacturer_data->{$key}))
                                            $manufacturer_data->{$key} = array();
                                        $manufacturer_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                    }
                    JeproshopCache::store($cache_id, $manufacturer_data);
                }
            } else {
                $manufacturer_data = JeproshopCache::retrieve($cache_id);
            }

            if ($manufacturer_data){
                //$this->id = (int)$id;
                foreach ($manufacturer_data as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
            }
        }

        $this->link_rewrite = JeproshopTools::str2url($this->name);
	}
	
	/**
	 * Return name from id
	 *
	 * @param int $manufacturer_id
	 * @return string name
	 */
	public static function getNameById($manufacturer_id){
		if(!isset(self::$_cache_name[$manufacturer_id])){
			$db = JFactory::getDBO();
	
			$query = "SELECT " . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_manufacturer') . " WHERE " ;
			$query .= $db->quoteName('manufacturer_id') . " = " . (int)$manufacturer_id . " AND " . $db->quoteName('published') . " = 1";
	
			$db->setQuery($query);
			self::$_cache_name[$manufacturer_id] = $db->loadResult();
		}
		return self::$_cache_name[$manufacturer_id];
	}
	
	/**
	 * Return manufacturers
	 *
	 * @param boolean $get_nb_products [optional] return products numbers for each
	 * @param int $lang_id
	 * @param bool $published
	 * @param int $p
	 * @param int $n
	 * @param bool $all_group
	 * @return array Manufacturers
	 */
	public static function getManufacturers($get_nb_products = false, $lang_id = 0, $published = true, $p = false, $n = false, $all_group = false, $group_by = false){
		if (!$lang_id){
			$lang_id = (int)JeproshopSettingModelSetting::getValue('default_lang');
		}
		if (!JeproshopGroupModelGroup::isFeaturePublished()){ $all_group = true; }
		
		$db = JFactory::getDBO();
		
		$query = "SELECT manufacturer.*, manufacturer_lang."  . $db->quoteName('description') . ", manufacturer_lang.";
		$query .= $db->quoteName('short_description') . " FROM " . $db->quoteName('#__jeproshop_manufacturer') . " AS ";
		$query .= "manufacturer " . JeproshopShopModelShop::addSqlAssociation('manufacturer') . " INNER JOIN ";
		$query .= $db->quoteName('#__jeproshop_manufacturer_lang') . " AS manufacturer_lang ON (manufacturer.";
		$query .= $db->quoteName('manufacturer_id') . " = manufacturer_lang." . $db->quoteName('manufacturer_id') ;
		$query .= " AND manufacturer_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
		$query .= ($published  ? " WHERE manufacturer." . $db->quoteName('published') . " = 1" : "");
		$query .= ($group_by ? " GROUP BY manufacturer." . $db->quoteName('manufacturer_id') : "" ) . " ORDER BY ";
		$query .= "manufacturer." . $db->quoteName('name') . " ASC " . ($p ? " LIMIT ".(((int)$p - 1) * (int)$n).", ".(int)$n : "");
		
		$db->setQuery($query);
		$manufacturers = $db->loadObjectList();
		if ($manufacturers === false)
			return false;
	/*
		if ($get_nb_products)
		{
			$sql_groups = '';
			if (!$all_group)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
	
			foreach ($manufacturers as $key => $manufacturer)
			{
				$manufacturers[$key]['nb_products'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(DISTINCT p.`id_product`)
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE p.`id_manufacturer` = '.(int)$manufacturer['id_manufacturer'].'
				AND product_shop.`visibility` NOT IN ("none")
				'.($active ? ' AND product_shop.`active` = 1 ' : '').'
				'.($all_group ? '' : ' AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sql_groups.'
				)'));
			}
		}	
		*/
		$total_manufacturers = count($manufacturers);
		$rewrite_settings = (int)JeproshopSettingModelSetting::getValue('rewrite_settings');
		for ($i = 0; $i < $total_manufacturers; $i++)
			$manufacturers[$i]->link_rewrite = ($rewrite_settings ? JeproshopValidator::link_rewrite($manufacturers[$i]->name) : 0);
		return $manufacturers;
	}
	
	
	public function getManufacturerList($explicitSelect = TRUE){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		$context = JeproshopContext::getContext();
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
		$shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
		$category_id = $app->getUserStateFromRequest($option. $view. '.category_id', 'category_id', 0, 'int');
		$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'manufacturer_id', 'string');
		$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');		
		
		/* Manage default params values */
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		$select = " COUNT(" . $db->quoteName('product_id') . ") AS " . $db->quoteName('products') . ", (SELECT ";
		$select .= "COUNT(address." . $db->quoteName('manufacturer_id') . ") AS " . $db->quoteName('addresses') ;
		$select .= " FROM " . $db->quoteName('#__jeproshop_address'). " AS address WHERE address.";
		$select .= $db->quoteName('manufacturer_id') . " = manufacturer." . $db->quoteName('manufacturer_id');
		$select .= " AND address." . $db->quoteName('deleted') . " = 0 GROUP BY address." . $db->quoteName('manufacturer_id');
		$select .= ") AS " . $db->quoteName('addresses');

		$join = "LEFT JOIN " . $db->quoteName('#__jeproshop_product') . " AS product ON (manufacturer.";
		$join .= $db->quoteName('manufacturer_id') . " = product." . $db->quoteName('manufacturer_id') . ") ";
		$group = " GROUP BY manufacturer." . $db->quoteName('manufacturer_id');
		
		if ($context->controller->multishop_context && JeproshopShopModelShop::isTableAssociated('manufacturer')){
			if(JeproshopShopModelShop::getShopContext() != JeproshopShopMoelShop::CONTEXT_ALL || !$context->employee->isSuperAdmin()){
				$test_join = !preg_match('#`?'.preg_quote('#__jeproshop_manufacturer_shop').'`? *manufacturer-shop#', $join);
				if(JeproshopShopModelShop::isFeaturePublished() && $test_join && JeproshopModelShopShop::isTableAssociated('manufacturer')){
					$where .= ' AND a.'.$this->identifier.' IN (
						SELECT sa.'.$this->identifier.'
						FROM `'._DB_PREFIX_.$this->table.'_shop` sa
						WHERE sa.id_shop IN ('.implode(', ', JeproshopShopModelShop::getContextListShopIds()). ") )";
				}
			}
		}
		
		/* Query in order to get results with all fields */
		$lang_join = '';
		if ($context->language->lang_id){
			$lang_join = "LEFT JOIN " . $db->quoteName('#__jeproshop_manufacturer_lang') . " AS manufacturer_lang ";
			$lang_join .= " ON (manufacturer_lang." . $db->quoteName('manufacturer_id') . " = manufacturer."; 
			$lang_join .=  $db->quoteName('manufacturer_id') . " AND manufacturer_lang." . $db->quoteName('lang_id');
			$lang_join .= " = " .(int)$lang_id . ") ";
		}
		
		$having_clause = '';
		if (isset($this->_filterHaving) || isset($this->_having))
		{
			$having_clause = ' HAVING ';
			if (isset($this->_filterHaving))
				$having_clause .= ltrim($this->_filterHaving, ' AND ');
			if (isset($this->_having))
				$having_clause .= $this->_having.' ';
		}
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS " ;
			if(!$explicitSelect){
				$query .= " manufacturer.name, manufacturer.published, "; //logo, 
								
			/*
				foreach($fields_list as $key => $value){
					if(isset($select) && preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\S*,/', $select)){
						continue;
					}
						
					if (isset($value['filter_key'])){
						$query .= str_replace('!', '.', $value['filter_key']) . " AS " . $key . ", ";
					}elseif ($key == 'manufacturer_id'){
						$query .= "manufacture." . $db->quoteName($db->escape($key)) . ", ";
					}elseif ($key != 'image' && !preg_match('/'. preg_quote($key, '/').'/i', $select)){
						$query .= $db->quoteName($db->escape($key)) .", ";
					}
				}
				$query = rtrim($query, ',') */;
			}else{
				$query .= ($lang_id ? "manufacturer_lang.*, " : "") . "manufacturer.*, ";
			}
			$query .= (isset($select) ? rtrim($select, ", ") : "") .  " FROM " . $db->quoteName('#__jeproshop_manufacturer');
			$query .= " AS manufacturer " . $lang_join . (isset($join) ? $join . " " : "") . " WHERE 1 " .(isset($where) ? $where . " " : "");
			$query .= (isset($filter) ? $filter : "" ) .(isset($group) ? $group ." " : "");
			$query .= $having_clause . " ORDER BY " .((str_replace('`', '', $order_by) == 'manufacturer_id') ? "manufacturer.manufacturer_id " : "") ;//. " manufacturer." . $db->quoteName($order_by) . " ";
			$query .= $db->escape($order_way) . (($use_limit === true) ? " LIMIT " .(int)$limitstart . ", " .(int)$limit : "");
		
			$db->setQuery($query);
			$manufacturers = $db->loadObjectList();
			
			if($use_limit == true){
				$limitstart = (int)$limitstart - (int)$limit;
				if($limitstart < 0){ break; }
			}else{ break; }
		}while(empty($manufacturers));
		$total = count($manufacturers);
		
		$this->pagination = new JPagination($total, $limitstart, $limit);
		return $manufacturers;
	}
	
	public function getManufacturerAddressesList($explicitSelect = TRUE){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
	
		$context = JeproshopContext::getContext();
	
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
		$shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
		$category_id = $app->getUserStateFromRequest($option. $view. '.category_id', 'category_id', 0, 'int');
		$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'position', 'string');
		$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
		
		/* Manage default params values */
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		$select = " country_lang." . $db->quoteName('name') . " AS country, manufacturer." . $db->quoteName('name') . " AS manufacturer_name ";
		$join = " LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country_lang.";
		$join .= $db->quoteName('country_id') . " = address." . $db->quoteName('country_id') . " AND country_lang.";
		$join .= $db->quoteName('lang_id') . " = " . (int)$this->context->language->lang_id . ") LEFT JOIN ";
		$join .= $db->quoteName('#__jeproshop_manufacturer') . " AS manufacturer ON (manufacturer." . $db->quoteName('manufacturer_id');
		$join .= " = manufacturer." . $db->quoteName('manufacturer_id') . ") ";
		$where = " AND address." . $db->quoteName('customer_id') . " = 0 AND address." . $db->quoteName('manufacturer_id') . " = 0 AND address.";
		$where .= $db->quoteName('warehouse_id') . " = 0 AND address." . $db->quoteName('deleted') . " = 0" ;
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS " . ($tmpTableFilter ? " * FROM (SELECT " : "");
			if($explicitSelect){
				foreach($fields_list as $key => $value){
					if(isset($select) && preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\S*,/', $select)){
						continue;
					}
						
					if (isset($value['filter_key'])){
						$query .= str_replace('!', '.', $value['filter_key']) . " AS " . $key . ", ";
					}elseif ($key == 'product_id'){
						$query .= "product." . $db->quoteName($db->escape($key)) . ", ";
					}elseif ($key != 'image' && !preg_match('/'. preg_quote($key, '/').'/i', $select)){
						$query .= $db->quoteName($db->escape($key)) .", ";
					}
				}
				$query = rtrim($query, ',');
			}else{
				$query .= ($lang_id ? "product_lang.*, " : "") . "product.*";
			}
			$query .= (isset($select) ? rtrim($select, ", ") : "") . $select_shop . " FROM " . $db->quoteName('#__jeproshop_product');
			$query .= " AS product " . $lang_join . (isset($join) ? $join . " " : "") . $join_shop . " WHERE 1 " .(isset($where) ? $where . " " : "");
			$query .= ($this->deleted_product ? " AND product." . $db->quoteName('deleted'). " = 0 " : ""). (isset($filter) ? $filter : "" ).$where_shop .(isset($group) ? $group ." " : "");
			$query .= $having_clause . " ORDER BY " .((str_replace('`', '', $order_by) == 'product_id') ? "product_id" : "") . " product." . $db->quoteName($order_by) . " ";
			$query .= $db->escape($order_way) . ($tmpTableFilter ? ") tmpTable WHERE 1" . $tmpTableFilter : "" );
			$query .= (($use_limit === true) ? " LIMIT " .(int)$limitstart . ", " .(int)$limit : "");
		
			$db->setQuery($query);
			$products = $db->loadObjectList();
				
			if($use_limit == true){
				$limitstart = (int)$limitstart -(int)$limit;
				if($limitstart < 0){ break; }
			}else{ break; }
		}while(empty($products));
	}
	
	
	
	public function getPagination(){
		return $this->pagination;
	}
}