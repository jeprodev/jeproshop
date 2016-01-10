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

class JeproshopGroupModelGroup extends JModelLegacy
{
	public $group_id;

    public $shop_id;

    public $name;

    public $reduction;

    public $show_prices = 1;

    public $price_display_method;

    public $date_add;
    public $date_upd;

    protected static $cache_reduction = array();
    protected static $group_price_display_method;

    protected $context;
    private $pagination = null;

	public function __construct($group_id = null, $lang_id = null, $shop_id = null){
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
        
        if($group_id){
            $cache_id = 'jeproshop_group_model_' . $group_id . '_' . $lang_id . '_' . $shop_id;
            if(!JeproshopCache::isStored($cache_id)){ 
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_group') . " AS j_group ";
                $where = "";
                /** get language information **/
                if($lang_id){
                    $query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_group_lang') . " AS group_lang ";
                    $query .= "ON (j_group.group_id = group_lang.group_id AND group_lang.lang_id = " . (int)$lang_id . ") ";
                    if($this->shop_id && !(empty($this->multiLangShop))){
                        $where = " AND group_lang.shop_id = " . $this->shop_id;
                    }
                }
                
                /** Get shop informations **/
                if(JeproshopShopModelShop::isTableAssociated('group')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_group_shop') . " AS group_shop ON (";
                    $query .= "j_group.group_id = group_shop.group_id AND group_shop.shop_id = " . (int)  $this->shop_id . ")";
                }
                $query .= " WHERE j_group.group_id = " . (int)$group_id . $where;
                
                $db->setQuery($query);
                $group_data = $db->loadObject();
                
                if($group_data){
                    if(!$lang_id && isset($this->multiLang) && $this->multiLang){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_group_lang');
                        $query .= " WHERE group_id = " . (int)$group_id;
                        
                        $db->setQuery($query);
                        $group_lang_data = $db->loadObjectList();
                        if($group_lang_data){
                            foreach ($group_lang_data as $row){
                                foreach($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'group_id'){
                                        if(!isset($group_data->{$key}) || !is_array($group_data->{$key})){
                                            $group_data->{$key} = array();
                                        }
                                        $group_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $group_data);
                }
            }else{
                $group_data = JeproshopCache::retrieve($cache_id);
            }
            
            if($group_data){ 
                $this->group_id = $group_id;
                foreach($group_data as $key => $value){
                    if(property_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
        
        if($this->group_id && !isset(JeproshopGroupModelGroup::$group_price_display_method[$this->group_id])){
            self::$group_price_display_method[$this->group_id] = $this->price_display_method;
        }
	}
	
	/**
	 * Return current group object
	 * Use context
	 * @static
	 * @return Group Group object
	 */
	public static function getCurrent()	{
		static $groups = array();
	
		$customer = JeproshopContext::getContext()->customer;
		if (JeproshopTools::isLoadedObject($customer, 'customer_id')){
			$group_id = (int)$customer->default_group_id;
		}else{
			$group_id = (int)  JeproshopSettingModelSetting::getValue('unidentified_group');
		}
	
		if (!isset($groups[$group_id])){
			$groups[$group_id] = new JeproshopGroupModelGroup($group_id);
		}
	
		if (!$groups[$group_id]->isAssociatedToShop(JeproshopContext::getContext()->shop->shop_id)){
			$group_id = (int)  JeproshopSettingModelSetting::getValue('customer_group');
			if (!isset($groups[$group_id])){
				$groups[$group_id] = new JeproshopGroupModelGroup($group_id);
			}
		}
		return $groups[$group_id];
	}
	
	public static function getDefaultPriceDisplayMethod(){
		return JeproshopGroupModelGroup::getPriceDisplayMethod((int)  JeproshopSettingModelSetting::getValue('customer_group'));
	}
	
	public static function getPriceDisplayMethod($group_id){
		if(!isset(JeproshopGroupModelGroup::$group_price_display_method[$group_id])){
			$db = JFactory::getDbO();
	
			$query = "SELECT " . $db->quoteName('price_display_method') . " FROM " . $db->quoteName('#__jeproshop_group');
			$query .= " WHERE " . $db->quoteName('group_id') . " = " . (int)$group_id;
	
			$db->setQuery($query);
			self::$group_price_display_method[$group_id] = $db->loadResult();
		}
		return self::$group_price_display_method[$group_id];
	}	
	
	public static function getGroups($shop_id = FALSE){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$option = $app->input->get('option');
		$view = $app->input->get('view');

		$context = JeproshopContext::getContext();
		$shop_criteria = '';
		if ($shop_id){
			$shop_criteria = JeproshopShopModelShop::addSqlAssociation('group');
		}
		
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		/*$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int'); */
		
		$query = "SELECT DISTINCT grp." . $db->quoteName('group_id') . ", grp." . $db->quoteName('reduction') . ", grp.";
		$query .= $db->quoteName('price_display_method') . ", group_lang." . $db->quoteName('name') . " FROM " ;
		$query .= $db->quoteName('#__jeproshop_group') . " AS grp LEFT JOIN " . $db->quoteName('#__jeproshop_group_lang');
		$query .= " AS group_lang ON (grp." . $db->quoteName('group_id') . " = group_lang." . $db->quoteName('group_id');
		$query .= " AND group_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . ") " . $shop_criteria ;
		$query .= " ORDER BY grp." . $db->quoteName('group_id') . " ASC";
		
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		return $groups;
	}

    public static function getStaticGroups($lang_id, $shop_id = false){
        $db = JFactory::getDBO();
        $shop_criteria = '';
        if ($shop_id){
            $shop_criteria = JeproshopShopModelShop::addSqlAssociation('group');
        }

        $query = "SELECT DISTINCT grp." . $db->quoteName('group_id') . ", grp." . $db->quoteName('reduction') . ", grp." . $db->quoteName('price_display_method');
        $query .= ", group_lang." . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_group') . " AS grp LEFT JOIN " . $db->quoteName('#__jeproshop_group_lang');
        $query .= " AS group_lang ON (grp." . $db->quoteName('group_id') . " = group_lang." . $db->quoteName('group_id') . " AND group_lang." . $db->quoteName('lang_id');
        $query .= " = " .(int)$lang_id . ") " . $shop_criteria . " ORDER BY grp." . $db->quoteName('group_id') . " ASC" ;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function  getCustomersList(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $context = JeproshopContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');

        /* Manage default params values */
        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS customer." . $db->quoteName('customer_id') . ", customer." . $db->quoteName('firstname') . ", customer." . $db->quoteName('lastname') . ", customer.";
            $query .= $db->quoteName('email') . ", customer." . $db->quoteName('birthday') . ", customer." . $db->quoteName('birthday') . ", customer." . $db->quoteName('date_add') . ", customer.";
            $query .= $db->quoteName('published') . ", customer_group." . $db->quoteName('group_id') . " FROM " . $db->quoteName('#__jeproshop_customer_group') . " AS customer_group LEFT JOIN ";
            $query .= $db->quoteName('#__jeproshop_customer') . " AS customer ON (customer." . $db->quoteName('customer_id') . " = customer_group." . $db->quoteName('customer_id') . ") WHERE customer_group.";
            $query .= $db->quoteName('group_id') . " = " . (int)$this->group_id . " AND customer." . $db->quoteName('deleted') . " != 1 " ;


            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= " ORDER BY " . $db->quoteName('date_add') . " ASC " . ( $use_limit ? "LIMIT " . $limitstart . ", " . $limit : "");
            $db->setQuery($query);
            $customers = $db->loadObjectList();

            if($use_limit == true){
                $limitstart = (int)$limitstart -(int)$limit;
                if($limitstart < 0){ break; }
            }else{ break; }
        }while(empty($customers));
        $this->pagination = new JPagination($total, $limitstart, $limit);

        return $customers;
    }
	
	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeaturePublished(){
		return JeproshopSettingModelSetting::getValue('group_feature_active');
	}
	
	public function isMultiShop(){
		return (JeproshopShopModelShop::isTableAssociated('group') || !empty($this->multiLangShop));
	}	
	
	public function isAssociatedToShop($shop_id = NULL){
		if($shop_id === NULL){
			$shop_id = (int)JeproshopContext::getContext()->shop->shop_id;
		}
	
		$cache_id = 'jeproshop_shop_model_group_' . (int)$this->group_id . '_' . (int)$this->shop_id;
		if(!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
			$query = "SELECT shop_id FROM " . $db->quoteName('#__jeproshop_group_shop') . " WHERE " . $db->quoteName('group_id') . " = " . (int)$this->group_id;
			$query .= " AND shop_id = " . (int)$shop_id;
	
			$db->setQuery($query);
			$result = (bool)$db->loadResult();
			JeproshopCache::store($cache_id, $result);
		}
		return JeproshopCache::retrieve($cache_id);
	}
	
	public function getGroupList(JeproshopContext $context = NULL){
		jimport('joomla.html.pagination');
		$context = JeproshopContext::getContext();
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
		$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
		
		/* Manage default params values */
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		$select = "(SELECT COUNT(customer_group." . $db->quoteName('customer_id') . ") FROM " . $db->quoteName('#__jeproshop_customer_group');
		$select .= " AS customer_group LEFT JOIN " . $db->quoteName('#__jeproshop_customer') . " AS customer ON (customer.";
		$select .= $db->quoteName('customer_id') . " = customer_group." . $db->quoteName('customer_id') . ") WHERE customer.";
		$select .= $db->quoteName('deleted') . " != 1 " . JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER);
		$select .= " AND customer_group." . $db->quoteName('group_id') . " = groupe." . $db->quoteName('group_id') . ") AS nb";
		
		$lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_group_lang') . " AS group_lang ON (group_lang." . $db->quoteName('group_id');
		$lang_join .= " = groupe." . $db->quoteName('group_id') . " AND group_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . ")";
		
		
		$tmpTableFilter = "";
		// Add SQL shop restriction
		$select_shop = $join_shop = $where_shop = '';
		$explicit_select = true;
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS " . ($tmpTableFilter ? " * FROM (SELECT " : ""); 
			if($explicit_select){
				$query .= "groupe.group_id, group_lang.name, groupe.reduction, groupe.show_prices, groupe.date_add,";
			}else{
				$query .= "groupe.*";
			}
			$query .= $select . $select_shop . " FROM " . $db->quoteName('#__jeproshop_group') . " AS groupe " . $lang_join ; 
			$query .= $join_shop ;
			$query .= " ORDER BY " .((str_replace('`', '', $order_by) == 'group_id') ? "group_id" : "") . " groupe." . $db->quoteName($order_by) . " ";
			$query .= $db->escape($order_way) . ($tmpTableFilter ? ") tmpTable WHERE 1" . $tmpTableFilter : "" );
			
			$db->setQuery($query);
			$total = count($db->loadObjectList());
				
			$query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
			
			$db->setQuery($query);
			$groups = $db->loadObjectList();
			
			if($use_limit == true){
				$limit_start = (int)$limit_start -(int)$limit;
				if($limit_start < 0){ break; }
			}else{ break; }
		}while (empty($groups));
		
		$this->pagination = new JPagination($total, $limit_start, $limit);
		return $groups;
	}

    public function getCustomers($count = false, $start = 0, $limit = 0, $shop_filtering = false)
    {
        if ($count)
            return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'customer_group` cg
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
			WHERE cg.`id_group` = '.(int)$this->id.'
			'.($shop_filtering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
			AND c.`deleted` != 1');
        return Db::getInstance()->executeS('
		SELECT cg.`id_customer`, c.*
		FROM `'._DB_PREFIX_.'customer_group` cg
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
		WHERE cg.`id_group` = '.(int)$this->id.'
		AND c.`deleted` != 1
		'.($shop_filtering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
		ORDER BY cg.`id_customer` ASC
		'.($limit > 0 ? 'LIMIT '.(int)$start.', '.(int)$limit : ''));
    }

    public static function getReduction($id_customer = null)
    {
        if (!isset(self::$cache_reduction['customer'][(int)$id_customer]))
        {
            $id_group = $id_customer ? Customer::getDefaultGroupId((int)$id_customer) : (int)Group::getCurrent()->id;
            self::$cache_reduction['customer'][(int)$id_customer] = Group::getReductionByIdGroup($id_group);
        }
        return self::$cache_reduction['customer'][(int)$id_customer];
    }

    public static function getReductionByIdGroup($id_group)
    {
        if (!isset(self::$cache_reduction['group'][$id_group]))
        {
            self::$cache_reduction['group'][$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
        }
        return self::$cache_reduction['group'][$id_group];
    }

    public function add($autodate = true, $null_values = false)
    {
        Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
        if (parent::add($autodate, $null_values))
        {
            Category::setNewGroupForHome((int)$this->id);
            Carrier::assignGroupToAllCarriers((int)$this->id);
            return true;
        }
        return false;
    }

    public function update($autodate = true, $null_values = false)
    {
        if (!Configuration::getGlobalValue('PS_GROUP_FEATURE_ACTIVE') && $this->reduction > 0)
            Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', 1);
        return parent::update($autodate, $null_values);
    }

    public function delete()
    {
        if ($this->id == (int)Configuration::get('PS_CUSTOMER_GROUP'))
            return false;
        if (parent::delete())
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_group` = '.(int)$this->id);
            $this->truncateModulesRestrictions($this->id);

            // Add default group (id 3) to customers without groups
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_group` (
				SELECT c.id_customer, '.(int)Configuration::get('PS_CUSTOMER_GROUP').' FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cg
				ON cg.id_customer = c.id_customer
				WHERE cg.id_customer IS NULL)');

            // Set to the customer the default group
            // Select the minimal id from customer_group
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customer` cg
				SET id_default_group =
					IFNULL((
						SELECT min(id_group) FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_customer = cg.id_customer),
						'.(int)Configuration::get('PS_CUSTOMER_GROUP').')
				WHERE `id_default_group` = '.(int)$this->id);

            return true;
        }
        return false;
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_GROUP_FEATURE_ACTIVE');
    }

    /**
     * This method is allow to know if there are other groups than the default ones
     * @since 1.5.0.1
     * @param $table
     * @param $has_active_column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        return (bool)(Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'group`') > 3);
    }

    /**
     * Truncate all modules restrictions for the group
     * @param integer id_group
     * @return boolean result
     */
    public static function truncateModulesRestrictions($id_group)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_group` = '.(int)$id_group);
    }

    /**
     * Truncate all restrictions by module
     * @param integer id_module
     * @return boolean result
     */
    public static function truncateRestrictionsByModule($id_module)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_module` = '.(int)$id_module);
    }

    /**
     * Adding restrictions modules to the group with id $id_group
     * @param $id_group
     * @param $modules
     * @param array $shops
     * @return bool
     */
    public static function addModulesRestrictions($id_group, $modules, $shops = array(1))
    {
        if (!is_array($modules) || !count($modules) || !is_array($shops) || !count($shops))
            return false;

        // Delete all record for this group
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE `id_group` = '.(int)$id_group);

        $sql = 'INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`) VALUES ';
        foreach ($modules as $module)
            foreach ($shops as $shop)
                $sql .= '("'.(int)$module.'", "'.(int)$shop.'", "'.(int)$id_group.'"),';
        $sql = rtrim($sql, ',');

        return (bool)Db::getInstance()->execute($sql);
    }

    /**
     * Add restrictions for a new module
     * We authorize every groups to the new module
     * @param integer id_module
     * @param array $shops
     */
    public static function addRestrictionsForModule($id_module, $shops = array(1))
    {
        if (!is_array($shops) || !count($shops))
            return false;

        $res = true;
        foreach ($shops as $shop)
            $res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`)
			(SELECT '.(int)$id_module.', '.(int)$shop.', id_group FROM `'._DB_PREFIX_.'group`)');
        return $res;
    }

    /**
     * Light back office search for Group
     *
     * @param integer $id_lang Language ID
     * @param string $query Searched string
     * @param boolean $unrestricted allows search without lang and includes first group and exact match
     * @return array Corresponding groupes
     */
    public static function searchByName($query)
    {
        return Db::getInstance()->getRow('
			SELECT g.*, gl.*
			FROM `'._DB_PREFIX_.'group` g
			LEFT JOIN `'._DB_PREFIX_.'group_lang` gl
				ON (g.`id_group` = gl.`id_group`)
			WHERE `name` LIKE \''.pSQL($query).'\'
		');
    }
}


class JeproshopGroupReductionModelGroupReduction extends JModelLegacy
{
	public	$group_id;
	public	$category_id;
	public	$reduction;

	protected static $reduction_cache = array();

	public function add($autodate = true, $null_values = false)
	{
		return (parent::add($autodate, $null_values) && $this->_setCache());
	}

	public function update($null_values = false)
	{
		return (parent::update($null_values) && $this->_updateCache());
	}

	public function delete()
	{
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT ps.`id_product`
			FROM `'._DB_PREFIX_.'product_shop` ps
			WHERE ps.`id_category_default` = '.(int)$this->id_category
		);

		$ids = array();
		foreach ($products as $row)
			$ids[] = $row['id_product'];

		if ($ids)
			Db::getInstance()->delete('product_group_reduction_cache', 'id_product IN ('.implode(', ', $ids).')');
		return (parent::delete());
	}

	public static function getGroupReductions($id_group, $id_lang)
	{
		$lang = $id_lang.Shop::addSqlRestrictionOnLang('cl');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction`, gr.`id_group`, gr.`id_category`, gr.`reduction`, cl.`name` AS category_name
			FROM `'._DB_PREFIX_.'group_reduction` gr
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = gr.`id_category` AND cl.`id_lang` = '.(int)$lang.')
			WHERE `id_group` = '.(int)$id_group
		);
	}

	public static function getValueForProduct($product_id, $group_id){
		if (!JeproshopGroupModelGroup::isFeaturePublished()){ return 0; }

		if (!isset(self::$reduction_cache[$product_id . '_' . $group_id])){
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('reduction') . " FROM " . $db->quoteName('#__jeproshop_product_group_reduction_cache');
            $query .= " WHERE " . $db->quoteName('product_id') . " = " .(int)$product_id . " AND " . $db->quoteName('group_id') . " = " .(int)$group_id;

            $db->setQuery($query);
            $reduction = $db->loadObject();
			self::$reduction_cache[$product_id.'_'.$group_id] = ($reduction ? $reduction : 0);
        }
		// Should return string (decimal in database) and not a float
		return self::$reduction_cache[$product_id. '_'. $group_id];
	}

	public static function doesExist($id_group, $id_category)
	{
		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_group`
		FROM `'._DB_PREFIX_.'group_reduction`
		WHERE `id_group` = '.(int)$id_group.' AND `id_category` = '.(int)$id_category);
	}

	public static function getGroupsByCategoryId($category_id){
		$db = JFactory::getDBO();		
		
		$query = "SELECT group_reduction." . $db->quoteName('group_id') . " AS group_id, group_reduction." . $db->quoteName('reduction');
		$query .= " AS reduction, group_reduction_id FROM " . $db->quoteName('#__jeproshop_group_reduction') . " AS group_reduction WHERE ";
		$query .= $db->quoteName('category_id') . " = ".(int)$category_id;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getGroupsReductionByCategoryId($id_category)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction` as id_group_reduction, id_group
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category
		);
	}

	public static function getGroupReductionByCategoryId($id_category)
	{
		Tools::displayAsDeprecated('Use GroupReduction::getGroupsByCategoryId($id_category)');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT gr.`id_group_reduction` as id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category
				, false);
	}

	public static function setProductReduction($product_id, $group_id = null, $category_id, $reduction = null){
		$res = true;
		JeproshopGroupReductionModelGroupReduction::deleteProductReduction((int)$product_id);
		$reductions = JeproshopGroupReductionModelGroupReduction::getGroupsByCategoryId((int)$category_id);
		if ($reductions){
			$db = JFactory::getDBO();
			foreach ($reductions as $reduction){
				$query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_group_reduction_cache') . " (" . $db->quoteName('product_id');
				$query .= ", " . $db->quoteName('group_id') . ", " . $db->quoteName('reduction') . ") VALUES (" . (int)$product_id . ", ";
				$query .= (int)$reduction->group_id . ", " . (float)$reduction->reduction . ")";
				
				$db->setQuery($query);
				$res &= $db->query();
			}
		}

		return $res;
	}

	public static function deleteProductReduction($product_id){
		$db = JFactory::getDBO();
	
		$query = "DELETE FROM ". $db->quoteName('#__jeproshop_product_group_reduction_cache');
		$query .= " WHERE " . $db->quoteName('product_id') . " = " . (int)$product_id;
		
		$db->setQuery($query);
		if($db->query() === false){ return false; }
		return true;
	}

	public static function duplicateReduction($id_product_old, $id_product)
	{
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executes('
			SELECT pgr.`id_product`, pgr.`id_group`, pgr.`reduction`
			FROM `'._DB_PREFIX_.'product_group_reduction_cache` pgr
			WHERE pgr.`id_product` = '.(int)$id_product_old
		);

		if (!$res)
			return true;

		$query = '';

		foreach ($res as $row)
		{
			$query .= 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`) VALUES ';
			$query .= '('.(int)$id_product.', '.(int)$row['id_group'].', '.(float)$row['reduction'].') ON DUPLICATE KEY UPDATE `reduction` = '.(float)$row['reduction'].';';
		}

		return Db::getInstance()->execute($query);
	}

	public static function deleteCategory($id_category)
	{
		$query = 'DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_category` = '.(int)$id_category;
		if (Db::getInstance()->Execute($query) === false)
			return false;
		return true;
	}

    protected function clearObjectCache()
    {
        return Db::getInstance()->delete('product_group_reduction_cache', 'id_group = '.(int)$this->group_id);
    }

    protected function setObjectCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT ps.`id_product`
			FROM `'._DB_PREFIX_.'product_shop` ps
			WHERE ps.`id_category_default` = '.(int)$this->id_category
        );

        $query = 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`) VALUES ';
        $updated = false;
        foreach ($products as $row)
        {
            $query .= '('.(int)$row['id_product'].', '.(int)$this->id_group.', '.(float)$this->reduction.'), ';
            $updated = true;
        }

        if ($updated)
            return (Db::getInstance()->execute(rtrim($query, ', ')));
        return true;
    }

    protected function updateObjectCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT ps.`id_product`
			FROM `'._DB_PREFIX_.'product_shop` ps
			WHERE ps.`id_category_default` = '.(int)$this->id_category,
            false);

        $ids = array();
        foreach ($products as $product)
            $ids[] = $product['id_product'];

        $result = true;
        if ($ids)
            $result &= Db::getInstance()->update('product_group_reduction_cache', array(
                'reduction' => (float)$this->reduction,
            ), 'id_product IN('.implode(', ', $ids).') AND id_group = '.(int)$this->id_group);

        return $result;
    }

    public static function getGroupByCategoryId($id_category)
    {
        Tools::displayAsDeprecated('Use GroupReduction::getGroupsByCategoryId($id_category)');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT gr.`id_group` as id_group, gr.`reduction` as reduction, id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category
            , false);
    }
}
