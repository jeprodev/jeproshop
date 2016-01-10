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

class JeproshopCarrierModelCarrier extends JModelLegacy
{
	/**
	 * getCarriers method filter
	 */
	const JEPROSHOP_CARRIERS_ONLY = 1;
	const JEPROSHOP_CARRIERS_MODULE = 2;
	const JEPROSHOP_CARRIERS_MODULE_NEED_RANGE = 3;
	const JEPROSHOP_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE = 4;
	const JEPROSHOP_ALL_CARRIERS = 5;

	const SORT_BY_PRICE = 0;
	const SORT_BY_POSITION = 1;
	
	const SORT_BY_ASC = 0;
	const SORT_BY_DESC = 1;
	
	const DEFAULT_SHIPPING_METHOD = 0;
	const WEIGHT_SHIPPING_METHOD = 1;
	const PRICE_SHIPPING_METHOD = 2;
	const FREE_SHIPPING_METHOD = 3;
	
	public $carrier_id;
	
	public $shop_id;
	/** @var int common id for carrier historization */
	public $reference_id;
	
	/** @var string Name */
	public $name;
	
	/** @var string URL with a '@' for */
	public $url;
	
	/** @var string Delay needed to deliver customer */
	public $delay;
	
	/** @var boolean Carrier status */
	public $published = true;
	
	/** @var boolean True if carrier has been deleted (staying in database as deleted) */
	public $deleted = 0;
	
	/** @var boolean Active or not the shipping handling */
	public $shipping_handling = true;
	
	/** @var int Behavior taken for unknown range */
	public $range_behavior;
	
	/** @var boolean Carrier module */
	public $is_module;
	
	/** @var boolean Free carrier */
	public $is_free = false;

    private $multiLang = true;
	
	/** @var int shipping behavior: by weight or by price */
	public $shipping_method = 0;
	
	/** @var boolean Shipping external */
	public $shipping_external = 0;
	
	/** @var string Shipping external */
	public $external_module_name = null;
	
	/** @var boolean Need Range */
	public $need_range = 0;
	
	/** @var int Position */
	public $position;
	
	/** @var int maximum package width managed by the transporter */
	public $max_width;
	
	/** @var int maximum package height managed by the transporter */
	public $max_height;
	
	/** @var int maximum package deep managed by the transporter */
	public $max_depth;
	
	/** @var int maximum package weight managed by the transporter */
	public $max_weight;
	
	/** @var int grade of the shipping delay (0 for longest, 9 for shortest) */
	public $grade;
	
	public function __construct($carrier_id = null, $lang_id = null){
		$db = JFactory::getDBO();
		
		if($lang_id !== NULL){
			$this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang'));
		}
		
		if($carrier_id){
			$cache_id = 'jeproshop_carrier_model_' . $carrier_id . '_' . $lang_id;
			if(!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_carrier') . " AS carrier ";
				$where = "";
				/** get language information **/
				if($lang_id){
					$query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_carrier_lang') . " AS carrier_lang ON (carrier.";
					$query .= "carrier_id = carrier_lang.carrier_id AND carrier_lang.lang_id = " . (int)$lang_id . ") ";
					/*if($this->shop_id && !(empty($this->multiLangShop))){
						$where = " AND carrier_lang.shop_id = " . $this->shop_id;
					}*/
				}
				
				/** Get shop informations **/
				if(JeproshopShopModelShop::isTableAssociated('carrier')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_carrier_shop') . " AS carrier_shop ON (carrier.";
					$query .= "carrier_id = carrier_shop.carrier_id AND carrier_shop.shop_id = " . (int)  $this->shop_id . ")";
				}
				$query .= " WHERE carrier.carrier_id = " . (int)$carrier_id . $where;
				
				$db->setQuery($query);
				$carrier_data = $db->loadObject();

				if($carrier_data){
					if(!$lang_id && isset($this->multiLang) && $this->multiLang){
						$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_carrier_lang');
						$query .= " WHERE carrier_id = " . (int)$carrier_id;
				
						$db->setQuery($query);
						$carrier_lang_data = $db->loadObjectList();
						if($carrier_lang_data){
							foreach ($carrier_lang_data as $row){
								foreach($row as $key => $value){
									if(array_key_exists($key, $this) && $key != 'carrier_id'){
										if(!isset($carrier_data->{$key}) || !is_array($carrier_data->{$key})){
											$carrier_data->{$key} = array();
										}
										$carrier_data->{$key}[$row->lang_id] = $value;
									}
								}
							}
						}
					}
					JeproshopCache::store($cache_id, $carrier_data);
				}
			}else{
                $carrier_data = JeproshopCache::retrieve($cache_id);
            }
            
            if($carrier_data) {
                $carrier_data->carrier_id = $carrier_id;
                foreach ($carrier_data as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
            }
		}
	
		/**
		 * keep retro-compatibility SHIPPING_METHOD_DEFAULT
		 * @deprecated 1.5.5
		*/
		if ($this->shipping_method == JeproshopCarrierModelCarrier::DEFAULT_SHIPPING_METHOD){
			$this->shipping_method = ((int)JeproshopSettingModelSetting::getValue('shipping_method') ? JeproshopCarrierModelCarrier::WEIGHT_SHIPPING_METHOD : JeproshopCarrierModelCarrier::PRICE_SHIPPING_METHOD);
		}
		/**
		 * keep retro-compatibility id_tax_rules_group
		 * @deprecated 1.5.0
		*/
		if ($this->carrier_id){
			$this->tax_rules_group_id = $this->getTaxRulesGroupId(JeproshopContext::getContext());
		}
		if ($this->name == '0'){
			$this->name = JeproshopSettingModelSetting::getValue('shop_name');
		}
		$this->image_dir = COM_JEPROSHOP_CARRIER_IMAGE_DIR;
	}

    /**
     * Get all carriers in a given language
     *
     * @param integer $lang_id Language id
     * @param bool $published
     * @param bool $delete
     * @param bool $zone_id
     * @param null $group_ids
     * @param int $modules_filters , possible values:
     *
     * PS_CARRIERS_ONLY
     * CARRIERS_MODULE
     * CARRIERS_MODULE_NEED_RANGE
     * PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
     * ALL_CARRIERS
     * @internal param bool $active Returns only active carriers when true
     * @return array Carriers
     */
	public static function getCarriers($lang_id, $published = false, $delete = false, $zone_id = false, $group_ids = null, $modules_filters = self::JEPROSHOP_CARRIERS_ONLY){
		// Filter by groups and no groups => return empty array
		if ($group_ids && (!is_array($group_ids) || !count($group_ids))){ return array(); }
		
		$db = JFactory::getDBO();
	
		$query = "SELECT carrier.*, carrier_lang.delay FROM " . $db->quoteName('#__jeproshop_carrier') . " AS carrier LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_carrier_lang') . " AS carrier_lang ON (carrier." . $db->quoteName('carrier_id');
		$query .= " = carrier_lang." . $db->quoteName('carrier_id') . " AND carrier_lang." . $db->quoteName('lang_id') . " = ";
		$query .= (int)$lang_id . JeproshopShopModelShop::addSqlRestrictionOnLang('carrier_lang'). ") LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_carrier_zone') . " AS carrier_zone ON (carrier_zone." . $db->quoteName('carrier_id');
		$query .= " = carrier." . $db->quoteName('carrier_id') . ") " . ($zone_id ? "LEFT JOIN " . $db->quoteName('#__jeproshop_zone') . " AS zone ON (zone." . $db->quoteName('zone_id') . " = " .(int)$zone_id . ")" : "");
		$query .= JeproshopShopModelShop::addSqlAssociation('carrier') . " WHERE carrier." . $db->quoteName('deleted') . " = ";
		$query .= ($delete ? "1" : "0") . ($published ? " AND carrier." . $db->quoteName('published') . " = 1 " : "");
		if ($zone_id){ 
			$query .= " AND carrier_zone." . $db->quoteName('zone_id') . " = " . (int)$zone_id . " AND zone." . $db->quoteName('published') . " = 1 ";
		}
		if ($group_ids){
			$query .= ' AND c.id_carrier IN (SELECT id_carrier FROM '._DB_PREFIX_.'carrier_group WHERE id_group IN ('.implode(',', array_map('intval', $group_ids)).')) ';
		}
		switch ($modules_filters){
			case 1 :
				$query .= " AND carrier.is_module = 0 ";
				break;
			case 2 :
				$query .= " AND carrier.is_module = 1 ";
				break;
			case 3 :
				$query .= " AND carrier.is_module = 1 AND carrier.need_range = 1 ";
				break;
			case 4 :
				$query .= " AND (carrier.is_module = 0 OR carrier.need_range = 1) ";
				break;
		}
		$query .= " GROUP BY carrier." . $db->quoteName('carrier_id') . " ORDER BY carrier." . $db->quoteName('position') . " ASC";
	
	
		$cache_id = 'Carrier::getCarriers_'.md5($query);
		if (!JeproshopCache::isStored($cache_id)){
			$db->setQuery($query);
			$carriers = $db->loadObjectList();
			JeproshopCache::store($cache_id, $carriers);
		}
		$carriers = JeproshopCache::retrieve($cache_id);
		foreach ($carriers as $key => $carrier){
			if ($carrier->name == '0'){
				$carriers[$key]->name = JeproshopSettingModelSetting::getValue('shop_name');  
			}
		}
		return $carriers;
	}

    public function getTaxRulesGroupId(JeproshopContext $context = null){
        return JeproshopCarrierModelCarrier::getTaxRulesGroupIdByCarrierId((int)$this->carrier_id, $context);
    }

    public static function getTaxRulesGroupIdByCarrierId($carrier_id, JeproshopContext $context = null){
        if (!$context){ $context = JeproshopContext::getContext(); }
        $key = 'jeproshop_carrier_tax_rules_group_id'.(int)$carrier_id . '_' . (int)$context->shop->shop_id;
        if (!JeproshopCache::isStored($key)){
            $db = JFactory::getDBO();
            $query = "SELECT " . $db->quoteName('tax_rules_group_id') . " FROM " . $db->quoteName('#__jeproshop_carrier_tax_rules_group_shop') . " WHERE "; ;
            $query .= $db->quoteName('carrier_id') . " = " .(int)$carrier_id . " AND shop_id = " .(int)JeproshopContext::getContext()->shop->shop_id;

            $db->setQuery($query);
            JeproshopCache::store($key, $db->loadObject()->tax_rules_group_id);
        }
        return JeproshopCache::retrieve($key);
    }

    public function getCarriersList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'position', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS carrier." . $db->quoteName('carrier_id') . ", carrier." . $db->quoteName('name') . ", carrier.";
            $query .= $db->quoteName('published') . ", carrier." . $db->quoteName('is_free') . ", carrier." . $db->quoteName('position');
            $query .= ", carrier_lang.* FROM " . $db->quoteName('#__jeproshop_carrier') . " AS carrier LEFT JOIN " . $db->quoteName('#__jeproshop_carrier_lang');
            $query .= " AS carrier_lang ON(carrier." . $db->quoteName('carrier_id') . " = carrier_lang." . $db->quoteName('carrier_id') ;
            $query .= JeproshopShopModelShop::addSqlRestrictionOnLang('carrier_lang') . " AND carrier_lang." . $db->quoteName('lang_id') . " = " ;
            $query .= (int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_carrier_tax_rules_group_shop') . " AS carrier_tax_rules_group_shop ON (carrier.";
            $query .= $db->quoteName('carrier_id') . " = carrier_tax_rules_group_shop." . $db->quoteName('carrier_id'). " AND carrier_tax_rules_group_shop.";
            $query .= $db->quoteName('shop_id') . " = " . (int)$context->shop->shop_id . ") ORDER BY ";
            $query .= ((str_replace('`', '', $order_by) == 'carrier_id') ? "carrier." : "") . $order_by . " " . $order_way ;
            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $carriers = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($carriers));

        foreach ($carriers as $key => $carrier) {
            if($carrier->name == '0'){
                $carrier->name = JeproshopSettingModelSetting::getCurrentShopName();
            }
        }


        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $carriers;
    }

    /**
     * Get all zones
     *
     * @return array Zones
     */
    public function getZones(){
        $db = JFactory::getDBO();
        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_carrier_zone') . " AS carrier_zone LEFT JOIN " . $db->quoteName('#__jeproshop_zone');
        $query .= " AS zone ON carrier_zone." . $db->quoteName('zone_id') . " = zone." . $db->quoteName('zone_id') . " WHERE carrier_zone.";
        $query .= $db->quoteName('carrier_id') . " = " .(int)$this->carrier_id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getPagination(){
        return $this->pagination;
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0)
            $this->position = Carrier::getHigherPosition() + 1;
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this))
            return false;
        if (!$count = Db::getInstance()->getValue('SELECT count(`id_carrier`) FROM `'._DB_PREFIX_.$this->def['table'].'` WHERE `deleted` = 0'))
            return false;
        if ($count == 1)
            Configuration::updateValue('PS_CARRIER_DEFAULT', (int)$this->id);

        // Register reference
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.$this->def['table'].'` SET `id_reference` = '.$this->id.' WHERE `id_carrier` = '.$this->id);

        return true;
    }

    /**
     * @since 1.5.0
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (!parent::delete())
            return false;
        Carrier::cleanPositions();
        return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_rule_carrier WHERE id_carrier = '.(int)$this->id) &&
            $this->deleteTaxRulesGroup(Shop::getShops(true, null, true)));

    }

    /**
     * Change carrier id in delivery prices when updating a carrier
     *
     * @param integer $id_old Old id carrier
     */
    public function setConfiguration($id_old)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'delivery` SET `id_carrier` = '.(int)$this->id.' WHERE `id_carrier` = '.(int)$id_old);
    }

    public function getMaxDeliveryPriceByPrice($id_zone)
    {
        $cache_id = 'Carrier::getMaxDeliveryPriceByPrice_'.(int)$this->id.'-'.(int)$id_zone;
        if (!Cache::isStored($cache_id))
        {
            $sql = 'SELECT d.`price`
					FROM `'._DB_PREFIX_.'delivery` d
					INNER JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
					WHERE d.`id_zone` = '.(int)$id_zone.'
						AND d.`id_carrier` = '.(int)$this->id.'
						'.Carrier::sqlDeliveryRangeShop('range_price').'
					ORDER BY r.`delimiter2` DESC';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }


    public static function getIdTaxRulesGroupMostUsed()
    {
        return Db::getInstance()->getValue('
					SELECT id_tax_rules_group
					FROM (
						SELECT COUNT(*) n, c.id_tax_rules_group
						FROM '._DB_PREFIX_.'carrier c
						JOIN '._DB_PREFIX_.'tax_rules_group trg ON (c.id_tax_rules_group = trg.id_tax_rules_group)
						WHERE trg.active = 1
						GROUP BY c.id_tax_rules_group
						ORDER BY n DESC
						LIMIT 1
					) most_used'
        );
    }

    public static function getDeliveredCountries($lang_id, $active_countries = false, $active_carriers = false, $contain_states = null){
        if (!JeproshopTools::isBool($active_countries) || !JeproshopTools::isBool($active_carriers)) {
            die(Tools::displayError());
        }

        $db = JFactory::getDBO();

        $query = "SELECT state.* FROM " . $db->quoteName('#__jeproshop_state') . " AS state ORDER BY state." . $db->quoteName('name') . " ASC";
        $db->setQuery($query);
        $states = $db->loadObjectList();

        $query = "SELECT country_lang.*, country.*, country_lang." . $db->quoteName('name') . " AS country_name, zone." . $db->quoteName('name') . " AS zone_name FROM " . $db->quoteName('#__jeproshop_country') . " AS country ";
        $query .= JeproshopShopModelShop::addSqlAssociation('country') . " LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id') . " = country_lang." . $db->quoteName('country_id');
        $query .= " AND country_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . ") INNER JOIN (" . $db->quoteName('#__jeproshop_carrier_zone') . " AS carrier_zone INNER JOIN " . $db->quoteName('#__jeproshop_carrier') ;
        $query .= " AS carrier ON ( carrier.carrier_id = carrier_zone.carrier_id AND carrier.deleted = 0 " . ($active_carriers ? " AND carrier.published = 1) " : ") " ) . " LEFT JOIN " . $db->quoteName('#__jeproshop_zone') . " AS zone ON(carrier_zone.";
        $query .= $db->quoteName('zone_id') . " = zone." . $db->quoteName('zone_id') . " AND zone." . $db->quoteName('zone_id') . " = country."  . $db->quoteName('zone_id') . ") WHERE 1 " . ($active_countries ? " AND country."  . $db->quoteName('published') ." = 1" : "");
        $query .= (!is_null($contain_states) ? " AND country." . $db->quoteName('contains_states') . " = " .(int)$contain_states : "") . " ORDER BY country_lang.name ASC";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $countries = array();
        foreach ($result as &$country)
            $countries[$country->country_id] = $country;
        foreach ($states as &$state)
            if (isset($countries[$state->country_id])) /* Does not keep the state if its country has been disabled and not selected */
                if ($state->published == 1)
                    $countries[$state->country_id]['states'][] = $state;

        return $countries;
    }

    /**
     * Return the default carrier to use
     *
     * @param array $carriers
     * @param array $defaultCarrier the last carrier selected
     * @return number the id of the default carrier
     */
    public static function getDefaultCarrierSelection($carriers, $default_carrier = 0)
    {
        if (empty($carriers))
            return 0;

        if ((int)$default_carrier != 0)
            foreach ($carriers as $carrier)
                if ($carrier['id_carrier'] == (int)$default_carrier)
                    return (int)$carrier['id_carrier'];
        foreach ($carriers as $carrier)
            if ($carrier['id_carrier'] == (int)Configuration::get('PS_CARRIER_DEFAULT'))
                return (int)$carrier['id_carrier'];

        return (int)$carriers[0]['id_carrier'];
    }

    /**
     *
     * @param int $id_zone
     * @param Array $groups group of the customer
     * @return Array
     */
    public static function getCarriersForOrder($id_zone, $groups = null, $cart = null)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        if (is_null($cart))
            $cart = $context->cart;
        $id_currency = $context->currency->id;

        if (is_array($groups) && !empty($groups))
            $result = Carrier::getCarriers($id_lang, true, false, (int)$id_zone, $groups, self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
        else
            $result = Carrier::getCarriers($id_lang, true, false, (int)$id_zone, array(Configuration::get('PS_UNIDENTIFIED_GROUP')), self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
        $results_array = array();

        foreach ($result as $k => $row)
        {
            $carrier = new Carrier((int)$row['id_carrier']);
            $shipping_method = $carrier->getShippingMethod();
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE)
            {
                // Get only carriers that are compliant with shipping method
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
                {
                    unset($result[$k]);
                    continue;
                }

                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior'])
                {
                    // Get id zone
                    if (!$id_zone)
                        $id_zone = Country::getIdZone(Country::getDefaultCountryId());

                    // Get only carriers that have a range compatible with cart
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                            && (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone)))
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
                            && (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, $id_currency))))
                    {
                        unset($result[$k]);
                        continue;
                    }
                }
            }

            $row['name'] = (strval($row['name']) != '0' ? $row['name'] : Configuration::get('PS_SHOP_NAME'));
            $row['price'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ? 0 : $cart->getPackageShippingCost((int)$row['id_carrier'], true, null, null, $id_zone));
            $row['price_tax_exc'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ? 0 : $cart->getPackageShippingCost((int)$row['id_carrier'], false, null, null, $id_zone));
            $row['img'] = file_exists(_PS_SHIP_IMG_DIR_.(int)$row['id_carrier']).'.jpg' ? _THEME_SHIP_DIR_.(int)$row['id_carrier'].'.jpg' : '';

            // If price is false, then the carrier is unavailable (carrier module)
            if ($row['price'] === false)
            {
                unset($result[$k]);
                continue;
            }
            $results_array[] = $row;
        }

        // if we have to sort carriers by price
        $prices = array();
        if (Configuration::get('PS_CARRIER_DEFAULT_SORT') == Carrier::SORT_BY_PRICE)
        {
            foreach ($results_array as $r)
                $prices[] = $r['price'];
            if (Configuration::get('PS_CARRIER_DEFAULT_ORDER') == Carrier::SORT_BY_ASC)
                array_multisort($prices, SORT_ASC, SORT_NUMERIC, $results_array);
            else
                array_multisort($prices, SORT_DESC, SORT_NUMERIC, $results_array);
        }

        return $results_array;
    }

    public static function checkCarrierZone($id_carrier, $id_zone)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT c.`id_carrier`
			FROM `'._DB_PREFIX_.'carrier` c
			LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = '.(int)$id_zone.')
			WHERE c.`id_carrier` = '.(int)$id_carrier.'
			AND c.`deleted` = 0
			AND c.`active` = 1
			AND cz.`id_zone` = '.(int)$id_zone.'
			AND z.`active` = 1'
        );
    }

    /**
     * Get a specific zones
     *
     * @return array Zone
     */
    public function getZone($id_zone)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'carrier_zone`
			WHERE `id_carrier` = '.(int)$this->id.'
			AND `id_zone` = '.(int)$id_zone);
    }

    /**
     * Add zone
     */
    public function addZone($id_zone)
    {
        if (Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier` , `id_zone`)
			VALUES ('.(int)$this->id.', '.(int)$id_zone.')
		'))
        {
            // Get all ranges for this carrier
            $ranges_price = RangePrice::getRanges($this->id);
            $ranges_weight = RangeWeight::getRanges($this->id);
            // Create row in ps_delivery table
            if (count($ranges_price) || count($ranges_weight))
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'delivery` (`id_carrier`, `id_range_price`, `id_range_weight`, `id_zone`, `price`) VALUES ';
                if (count($ranges_price))
                    foreach ($ranges_price as $range)
                        $sql .= '('.(int)$this->id.', '.(int)$range['id_range_price'].', 0, '.(int)$id_zone.', 0),';

                if (count($ranges_weight))
                    foreach ($ranges_weight as $range)
                        $sql .= '('.(int)$this->id.', 0, '.(int)$range['id_range_weight'].', '.(int)$id_zone.', 0),';
                $sql = rtrim($sql, ',');

                return Db::getInstance()->execute($sql);
            }
            return true;
        }
        return false;
    }

    /**
     * Delete zone
     */
    public function deleteZone($id_zone)
    {
        if (Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'carrier_zone`
			WHERE `id_carrier` = '.(int)$this->id.'
			AND `id_zone` = '.(int)$id_zone.' LIMIT 1
		'))
        {
            return Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'delivery`
				WHERE `id_carrier` = '.(int)$this->id.'
				AND `id_zone` = '.(int)$id_zone);
        }
        return false;
    }

    /**
     * Gets a specific group
     *
     * @since 1.5.0
     * @return array Group
     */
    public function getGroups()
    {
        return Db::getInstance()->executeS('
			SELECT id_group
			FROM '._DB_PREFIX_.'carrier_group
			WHERE id_carrier='.(int)$this->id);
    }

    /**
     * Clean delivery prices (weight/price)
     *
     * @param string $rangeTable Table name to clean (weight or price according to shipping method)
     * @return boolean Deletion result
     */
    public function deleteDeliveryPrice($range_table)
    {
        $where = '`id_carrier` = '.(int)$this->id.' AND (`id_'.bqSQL($range_table).'` IS NOT NULL OR `id_'.bqSQL($range_table).'` = 0) ';

        if (Shop::getContext() == Shop::CONTEXT_ALL)
            $where .= 'AND id_shop IS NULL AND id_shop_group IS NULL';
        else if (Shop::getContext() == Shop::CONTEXT_GROUP)
            $where .= 'AND id_shop IS NULL AND id_shop_group = '.(int)Shop::getContextShopGroupID();
        else
            $where .= 'AND id_shop = '.(int)Shop::getContextShopID();

        return Db::getInstance()->delete('delivery', $where);
    }

    /**
     * Add new delivery prices
     *
     * @param array $priceList Prices list in multiple arrays (changed to array since 1.5.0)
     * @return boolean Insertion result
     */
    public function addDeliveryPrice($price_list, $delete = false)
    {
        if (!$price_list)
            return false;

        $keys = array_keys($price_list[0]);
        if (!in_array('id_shop', $keys))
            $keys[] = 'id_shop';
        if (!in_array('id_shop_group', $keys))
            $keys[] = 'id_shop_group';

        $sql = 'INSERT INTO `'._DB_PREFIX_.'delivery` ('.implode(', ', $keys).') VALUES ';
        foreach ($price_list as $values)
        {
            if (!isset($values['id_shop']))
                $values['id_shop'] = (Shop::getContext() == Shop::CONTEXT_SHOP) ? Shop::getContextShopID() : null;
            if (!isset($values['id_shop_group']))
                $values['id_shop_group'] = (Shop::getContext() != Shop::CONTEXT_ALL) ? Shop::getContextShopGroupID() : null;

            if ($delete)
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'delivery`
					WHERE '.(is_null($values['id_shop']) ? 'ISNULL(`id_shop`) ' : 'id_shop = '.(int)$values['id_shop']).'
					AND '.(is_null($values['id_shop_group']) ? 'ISNULL(`id_shop`) ' : 'id_shop_group='.(int)$values['id_shop_group']).'
					AND id_carrier='.(int)$values['id_carrier'].
                    ($values['id_range_price'] !== null ? ' AND id_range_price='.(int)$values['id_range_price'] : ' AND (ISNULL(`id_range_price`) OR `id_range_price` = 0)').
                    ($values['id_range_weight'] !== null ? ' AND id_range_weight='.(int)$values['id_range_weight'] : ' AND (ISNULL(`id_range_weight`) OR `id_range_weight` = 0)').'
					AND id_zone='.(int)$values['id_zone']
                );

            $sql .= '(';
            foreach ($values as $v)
            {
                if (is_null($v))
                    $sql .= 'NULL';
                else if (is_int($v) || is_float($v))
                    $sql .= $v;
                else
                    $sql .= '\''.$v.'\'';
                $sql .= ', ';
            }
            $sql = rtrim($sql, ', ').'), ';
        }
        $sql = rtrim($sql, ', ');
        return Db::getInstance()->execute($sql);
    }

    /**
     * Copy old carrier informations when update carrier
     *
     * @param integer $oldId Old id carrier (copy from that id)
     */
    public function copyCarrierData($old_id)
    {
        if (!Validate::isUnsignedId($old_id))
            throw new PrestaShopException('Incorrect identifier for carrier');

        if (!$this->id)
            return false;

        $old_logo = _PS_SHIP_IMG_DIR_.'/'.(int)$old_id.'.jpg';
        if (file_exists($old_logo))
            copy($old_logo, _PS_SHIP_IMG_DIR_.'/'.(int)$this->id.'.jpg');

        $old_tmp_logo = _PS_TMP_IMG_DIR_.'/carrier_mini_'.(int)$old_id.'.jpg';
        if (file_exists($old_tmp_logo))
        {
            if (!isset($_FILES['logo']))
                copy($old_tmp_logo, _PS_TMP_IMG_DIR_.'/carrier_mini_'.$this->id.'.jpg');
            unlink($old_tmp_logo);
        }

        // Copy existing ranges price
        foreach (array('range_price', 'range_weight') as $range)
        {
            $res = Db::getInstance()->executeS('
				SELECT `id_'.$range.'` as id_range, `delimiter1`, `delimiter2`
				FROM `'._DB_PREFIX_.$range.'`
				WHERE `id_carrier` = '.(int)$old_id);
            if (count($res))
                foreach ($res as $val)
                {
                    Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.$range.'` (`id_carrier`, `delimiter1`, `delimiter2`)
						VALUES ('.$this->id.','.(float)$val['delimiter1'].','.(float)$val['delimiter2'].')');
                    $range_id = (int)Db::getInstance()->Insert_ID();

                    $range_price_id = ($range == 'range_price') ? $range_id : 'NULL';
                    $range_weight_id = ($range == 'range_weight') ? $range_id : 'NULL';

                    Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.'delivery` (`id_carrier`, `id_shop`, `id_shop_group`, `id_range_price`, `id_range_weight`, `id_zone`, `price`) (
							SELECT '.(int)$this->id.', `id_shop`, `id_shop_group`, '.(int)$range_price_id.', '.(int)$range_weight_id.', `id_zone`, `price`
							FROM `'._DB_PREFIX_.'delivery`
							WHERE `id_carrier` = '.(int)$old_id.'
							AND `id_'.$range.'` = '.(int)$val['id_range'].'
						)
					');
                }
        }

        // Copy existing zones
        $res = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'carrier_zone`
			WHERE id_carrier = '.(int)$old_id);
        foreach ($res as $val)
            Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier`, `id_zone`)
				VALUES ('.$this->id.','.(int)$val['id_zone'].')
			');

        //Copy default carrier
        if (Configuration::get('PS_CARRIER_DEFAULT') == $old_id)
            Configuration::updateValue('PS_CARRIER_DEFAULT', (int)$this->id);

        // Copy reference
        $id_reference = Db::getInstance()->getValue('
			SELECT `id_reference`
			FROM `'._DB_PREFIX_.$this->def['table'].'`
			WHERE id_carrier = '.(int)$old_id);
        Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.$this->def['table'].'`
			SET `id_reference` = '.(int)$id_reference.'
			WHERE `id_carrier` = '.(int)$this->id);

        $this->id_reference = (int)$id_reference;

        // Copy tax rules group
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'carrier_tax_rules_group_shop` (`id_carrier`, `id_tax_rules_group`, `id_shop`)
												(SELECT '.(int)$this->id.', `id_tax_rules_group`, `id_shop`
													FROM `'._DB_PREFIX_.'carrier_tax_rules_group_shop`
													WHERE `id_carrier`='.(int)$old_id.')');

    }

    /**
     * Get carrier using the reference id
     */
    public static function getCarrierByReference($id_reference)
    {
        // @todo class var $table must became static. here I have to use 'carrier' because this method is static
        $id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier`
			WHERE id_reference = '.(int)$id_reference.' AND deleted = 0 ORDER BY id_carrier DESC');
        if (!$id_carrier)
            return false;
        return new Carrier($id_carrier);
    }

    /**
     * Check if carrier is used (at least one order placed)
     *
     * @return integer Order count for this carrier
     */
    public function isUsed()
    {
        $row = Db::getInstance()->getRow('
		SELECT COUNT(`id_carrier`) AS total
		FROM `'._DB_PREFIX_.'orders`
		WHERE `id_carrier` = '.(int)$this->id);

        return (int)$row['total'];
    }


    public function getRangeTable()
    {
        $shipping_method = $this->getShippingMethod();
        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
            return 'range_weight';
        elseif ($shipping_method == Carrier::SHIPPING_METHOD_PRICE)
            return 'range_price';
        return false;
    }

    public function getRangeObject($shipping_method = false)
    {
        if (!$shipping_method)
            $shipping_method = $this->getShippingMethod();

        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
            return new RangeWeight();
        elseif ($shipping_method == Carrier::SHIPPING_METHOD_PRICE)
            return new RangePrice();
        return false;
    }

    public function getRangeSuffix($currency = null)
    {
        if (!$currency)
            $currency = Context::getContext()->currency;
        $suffix = Configuration::get('PS_WEIGHT_UNIT');
        if ($this->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE)
            $suffix = $currency->sign;
        return $suffix;
    }

    public function getIdTaxRulesGroup(Context $context = null)
    {
        return Carrier::getIdTaxRulesGroupByIdCarrier((int)$this->id, $context);
    }

    public static function getIdTaxRulesGroupByIdCarrier($id_carrier, Context $context = null)
    {
        if (!$context)
            $context = Context::getContext();
        $key = 'carrier_id_tax_rules_group_'.(int)$id_carrier.'_'.(int)$context->shop->id;
        if (!Cache::isStored($key))
            Cache::store($key,
                Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_tax_rules_group`
				FROM `'._DB_PREFIX_.'carrier_tax_rules_group_shop`
				WHERE `id_carrier` = '.(int)$id_carrier.' AND id_shop='.(int)Context::getContext()->shop->id));

        return Cache::retrieve($key);
    }

    public function deleteTaxRulesGroup(array $shops = null)
    {
        if (!$shops)
            $shops = Shop::getContextListShopID();

        $where = 'id_carrier = '.(int)$this->id;
        if ($shops)
            $where .= ' AND id_shop IN('.implode(', ', array_map('intval', $shops)).')';
        return Db::getInstance()->delete('carrier_tax_rules_group_shop', $where);
    }

    public function setTaxRulesGroup($id_tax_rules_group, $all_shops = false)
    {
        if (!Validate::isUnsignedId($id_tax_rules_group))
            die(Tools::displayError());

        if (!$all_shops)
            $shops = Shop::getContextListShopID();
        else
            $shops = Shop::getShops(true, null, true);

        $this->deleteTaxRulesGroup($shops);

        $values = array();
        foreach ($shops as $id_shop)
            $values[] = array(
                'id_carrier' => (int)$this->id,
                'id_tax_rules_group' => (int)$id_tax_rules_group,
                'id_shop' => (int)$id_shop,
            );
        Cache::clean('carrier_id_tax_rules_group_'.(int)$this->id.'_'.(int)Context::getContext()->shop->id);
        return Db::getInstance()->insert('carrier_tax_rules_group_shop', $values);
    }

    /**
     * Returns the taxes rate associated to the carrier
     *
     * @since 1.5
     * @param Address $address
     * @return
     */
    public function getTaxesRate(Address $address)
    {
        $tax_calculator = $this->getTaxCalculator($address);
        return $tax_calculator->getTotalRate();
    }

    /**
     * Returns the taxes calculator associated to the carrier
     *
     * @since 1.5
     * @param Address $address
     * @return
     */
    public function getTaxCalculator(Address $address)
    {
        $tax_manager = TaxManagerFactory::getManager($address, $this->getIdTaxRulesGroup());
        return $tax_manager->getTaxCalculator();
    }

    /**
     * This tricky method generates a sql clause to check if ranged data are overloaded by multishop
     *
     * @since 1.5.0
     * @param string $rangeTable
     * @return string
     */
    public static function sqlDeliveryRangeShop($range_table, $alias = 'd')
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL)
            $where = 'AND d2.id_shop IS NULL AND d2.id_shop_group IS NULL';
        else if (Shop::getContext() == Shop::CONTEXT_GROUP)
            $where = 'AND ((d2.id_shop_group IS NULL OR d2.id_shop_group = '.Shop::getContextShopGroupID().') AND d2.id_shop IS NULL)';
        else
            $where = 'AND (d2.id_shop = '.Shop::getContextShopID().' OR (d2.id_shop_group = '.Shop::getContextShopGroupID().'
					AND d2.id_shop IS NULL) OR (d2.id_shop_group IS NULL AND d2.id_shop IS NULL))';

        $sql = 'AND '.$alias.'.id_delivery = (
					SELECT d2.id_delivery
					FROM '._DB_PREFIX_.'delivery d2
					WHERE d2.id_carrier = `'.bqSQL($alias).'`.id_carrier
						AND d2.id_zone = `'.bqSQL($alias).'`.id_zone
						AND d2.`id_'.bqSQL($range_table).'` = `'.bqSQL($alias).'`.`id_'.bqSQL($range_table).'`
						'.$where.'
					ORDER BY d2.id_shop DESC, d2.id_shop_group DESC
					LIMIT 1
				)';
        return $sql;
    }

    /**
     * Moves a carrier
     *
     * @since 1.5.0
     * @param boolean $way Up (1) or Down (0)
     * @param integer $position
     * @return boolean Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT `id_carrier`, `position`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE `deleted` = 0
			ORDER BY `position` ASC'
        ))
            return false;

        foreach ($res as $carrier)
            if ((int)$carrier['id_carrier'] == (int)$this->id)
                $moved_carrier = $carrier;

        if (!isset($moved_carrier) || !isset($position))
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'carrier`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                    ? '> '.(int)$moved_carrier['position'].' AND `position` <= '.(int)$position
                    : '< '.(int)$moved_carrier['position'].' AND `position` >= '.(int)$position.'
			AND `deleted` = 0'))
            && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'carrier`
			SET `position` = '.(int)$position.'
			WHERE `id_carrier` = '.(int)$moved_carrier['id_carrier']));
    }

    /**
     * Reorders carrier positions.
     * Called after deleting a carrier.
     *
     * @since 1.5.0
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $return = true;

        $sql = '
		SELECT `id_carrier`
		FROM `'._DB_PREFIX_.'carrier`
		WHERE `deleted` = 0
		ORDER BY `position` ASC';
        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value)
            $return = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'carrier`
			SET `position` = '.(int)$i++.'
			WHERE `id_carrier` = '.(int)$value['id_carrier']);
        return $return;
    }

    /**
     * Gets the highest carrier position
     *
     * @since 1.5.0
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'carrier`
				WHERE `deleted` = 0';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }

    /**
     * Assign one (ore more) group to all carriers
     *
     * @since 1.5.0
     * @param int|array $id_group_list group id or list of group ids
     * @param array $exception list of id carriers to ignore
     */
    public static function assignGroupToAllCarriers($id_group_list, $exception = null)
    {
        if (!is_array($id_group_list))
            $id_group_list = array($id_group_list);

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'carrier_group`
			WHERE `id_group` IN ('.join(',', $id_group_list).')');

        $carrier_list = Db::getInstance()->executeS('
			SELECT id_carrier FROM `'._DB_PREFIX_.'carrier`
			WHERE deleted = 0
			'.(is_array($exception) ? 'AND id_carrier NOT IN ('.join(',', $exception).')' : ''));

        if ($carrier_list)
        {
            $data = array();
            foreach ($carrier_list as $carrier)
            {
                foreach ($id_group_list as $id_group)
                    $data[] = array(
                        'id_carrier' => $carrier['id_carrier'],
                        'id_group' => $id_group,
                    );
            }
            return Db::getInstance()->insert('carrier_group', $data, false, false, Db::INSERT);
        }

        return true;
    }

    public function setGroups($groups, $delete = true)
    {
        if ($delete)
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'carrier_group WHERE id_carrier = '.(int)$this->id);
        if (!is_array($groups) || !count($groups))
            return true;
        $sql = 'INSERT INTO '._DB_PREFIX_.'carrier_group (id_carrier, id_group) VALUES ';
        foreach ($groups as $id_group)
            $sql .= '('.(int)$this->id.', '.(int)$id_group.'),';

        return Db::getInstance()->execute(rtrim($sql, ','));
    }

}