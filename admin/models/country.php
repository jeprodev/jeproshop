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

class JeproshopCountryModelCountry extends JModelLegacy
{
	public $country_id;
	
	public $lang_id;
	
	public $shop_id;
	
	public $zone_id;
	
	public $currency_id;
	
	public $states = array();
	
	public $name = array();

	public $iso_code;
	
	public $call_prefix;
	
	public $published;
	
	public $contains_states;
	
	public $need_identification_number;
	
	public $need_zip_code;
	
	public $zip_code_format;
	
	public $display_tax_label;

	public $country_display_tax_label;
	public $get_shop_from_context = false;
	
	public $multiLangShop = true;
	public $multiLang = true;

    protected static $_zone_ids = array();
    protected static $cache_iso_by_id = array();

    private $pagination = null;
	
	public function __construct($country_id = null, $lang_id = null, $shop_id = NULL){
		$db = JFactory::getDBO();

        if($lang_id !== NULL){
            $this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang'));
        }
		
		if($shop_id  && $this->isMultiShop()){
			$this->shop_id = (int)$shop_id;
			$this->get_shop_from_context = FALSE;
		}
		
		if($this->isMultiShop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		}

        if($country_id){
            $cache_id = 'jeproshop_country_model_' . (int)$country_id . '_' . (int)$lang_id . '_' . (int)$shop_id;
            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_country') . " AS country ";

                //Get language data
                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country_lang." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }

                if(JeproshopShopModelShop::isTableAssociated('country')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_country_shop') . " AS country_shop ON (country_shop." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country_shop." . $db->quoteName('shop_id') . " = " . (int)$shop_id . ") ";
                }

                $db->setQuery($query);
                $country_data = $db->loadObject();

                if($country_data){
                    if(!$lang_id){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_country_lang') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country_id;

                        $db->setQuery($query);
                        $country_lang_data = $db->loadObjectList();
                        if($country_lang_data){
                            foreach($country_lang_data as $row){
                                foreach($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'country_id'){
                                        if(!isset($country_data->{$key}) || !is_array($country_data->{$key})){
                                            $country_data->{$key} = array();
                                        }
                                        $country_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $country_data);
                }
            }else{
                $country_data = JeproshopCache::retrieve($cache_id);
            }

            if($country_data){
                $country_data->country_id = (int)$country_id;
                foreach($country_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
	}

    /**
     * Get a country name with its ID
     *
     * @param $lang_id
     * @param $country_id
     * @internal param int $id_lang Language ID
     * @internal param int $id_country Country ID
     * @return string Country name
     */
	public static function getNameById($lang_id, $country_id){
		$key = 'country_getNameById_'.$country_id .'_' . $lang_id;
		if (!JeproshopCache::isStored($key)){
			$db = JFactory::getDBO();
			
			$query = "SELECT " . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeproshop_country_lang') . " WHERE ";
			$query .= $db->quoteName('lang_id') . " = " . (int)$lang_id . " AND " . $db->quoteName('country_id') . " = ".(int)$country_id; 
			
			$db->setQuery($query);
			
			JeproshopCache::store($key, $db->loadResult());
		}
		return JeproshopCache::retrieve($key);
	}

    /**
     * This method is allow to know if a entity is currently used
     * @since 1.5.0.1
     * @param string $table name of table linked to entity
     * @param bool $has_active_column true if the table has an active column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false) {
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('country_id') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('published') . " = 1";
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * @brief Return available countries
     *
     * @param integer $lang_id Language ID
     * @param boolean $published return only active countries
     * @param boolean $contain_states return only country with states
     * @param boolean $list_states Include the states list with the returned list
     *
     * @return Array Countries and corresponding zones
     */
    public static function getStaticCountries($lang_id, $published = false, $contain_states = false, $list_states = true) {
        $countries = array();
        $db = JFactory::getDBO();

        $query = "SELECT country_lang.*, country.*, country_lang." . $db->quoteName('name') . " AS country_name, zone." . $db->quoteName('name');
        $query .= " AS zone_name FROM " . $db->quoteName('#__jeproshop_country') . " AS country " . JeproshopShopModelShop::addSqlAssociation('country');
        $query .= "	LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id') ;
        $query .= " = country_lang." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id;
        $query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_zone') . " AS zone ON (zone." . $db->quoteName('zone_id') . " = country.";
        $query .= $db->quoteName('zone_id') . ") WHERE 1 " .($published ? " AND country.published = 1" : "") ;
        $query .= ($contain_states ? " AND country." . $db->quoteName('contains_states') . " = " .(int)$contain_states : "")." ORDER BY country_lang.name ASC";

        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row){ $countries[$row->country_id] = $row; }

        if ($list_states){
            $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_state') . " ORDER BY " . $db->quoteName('name') . " ASC";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            foreach ($result as $row)
                if (isset($countries[$row->country_id]) && $row->published == 1) /* Does not keep the state if its country has been disabled and not selected */
                    $countries[$row->country_id]->states[] = $row;
        }
        return $countries;
    }

	public function getCountries($lang_id, $published = FALSE, $contain_states = FALSE, $states_list = TRUE){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS country." . $db->quoteName('country_id') . ", country_lang." . $db->quoteName('name') . " AS name,";
		$query .= $db->quoteName('iso_code') . ", " . $db->quoteName('call_prefix') . ",zone." . $db->quoteName('zone_id'). " AS zone,";
		$query .= "country.published AS published, zone." . $db->quoteName('name') . " AS zone_name FROM " . $db->quoteName('#__jeproshop_country'); 
		$query .= " AS country LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country_lang.";
		$query .= $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id');
		$query .= " = " . $lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_zone') . " AS zone ON (zone." . $db->quoteName('zone_id');
		$query .= " = country." . $db->quoteName('zone_id') .") WHERE 1 ORDER BY country." . $db->quoteName('country_id');
		$query .= " ASC LIMIT " .$limitstart . ", " . $limit;
		
		$db->setQuery($query);
		$countries = $db->loadObjectList();
		
		$total = count($countries);
		
		$this->pagination = new JPagination($total, $limitstart, $limit);
		return $countries;
	}
	
	public function getCountryList(JeproshopContext $context = NULL){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		if(!$context){ $context = JeproshopContext::getContext(); }
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
		$shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
		$category_id = $app->getUserStateFromRequest($option. $view. '.cat_id', 'cat_id', 0, 'int');
		$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
		$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
		$product_attribute_id = $app->getUserStateFromRequest($option. $view. '.product_attribute_id', 'product_attribute_id', 0, 'int');
		
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS country." . $db->quoteName('country_id') . ", country_lang." . $db->quoteName('name');
			$query .= " AS name, country." . $db->quoteName('iso_code') . ", country." . $db->quoteName('call_prefix') . ", country.";
			$query .= $db->quoteName('published') . ",zone." . $db->quoteName('zone_id'). " AS zone, zone." . $db->quoteName('name');
			$query .= " AS zone_name FROM " . $db->quoteName('#__jeproshop_country') . " AS country LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country_lang." . $db->quoteName('country_id');
			$query .= " = country." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . $lang_id;
			$query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_zone') . " AS zone ON (zone." . $db->quoteName('zone_id') . " = country.";
			$query .= $db->quoteName('zone_id') .") WHERE 1 ORDER BY country." . $db->quoteName('country_id');
			
			$db->setQuery($query);
			$total = count($db->loadObjectList());
			
			$query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
			
			$db->setQuery($query);
			$countries = $db->loadObjectList();
			
			if($use_limit == true){
				$limit_start = (int)$limit_start -(int)$limit;
				if($limit_start < 0){ break; }
			}else{ break; }
		}while(empty($countries));
		
		$this->pagination = new JPagination($total, $limit_start, $limit);
		return $countries;
	}
	
	public function getPagination(){
		return $this->pagination;
	}

    /**
     * Replace letters of zip code format And check this format on the zip code
     * @param $zip_code
     * @return bool (bool)
     */
    public function checkZipCode($zip_code){
        $zip_regexp = '/^'.$this->zip_code_format.'$/ui';
        $zip_regexp = str_replace(' ', '( |)', $zip_regexp);
        $zip_regexp = str_replace('-', '(-|)', $zip_regexp);
        $zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
        $zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
        $zip_regexp = str_replace('C', $this->iso_code, $zip_regexp);

        return (bool)preg_match($zip_regexp, $zip_code);
    }

    public static function getCountriesByZoneId($zone_id, $lang_id){
        if (empty($zone_id) || empty($lang_id)) {
            die(JError::raiseError());
        }

        $db = JFactory::getDBO();
        $query = "SELECT DISTINCT country.*, ccountry_lang.* FROM " . $db->quoteName('#__jeproshop_country') . " AS country " . JeproshopShopModelShop::addSqlAssociation('country', false) . " LEFT JOIN ";
        $query .= $db->quoteName('#__jeproshop_state') . " AS state ON (state." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang');
        $query .= " AS country_lang ON (country." .  $db->quoteName('country_id') . " = country_lang." .  $db->quoteName('country_id') . ") WHERE (country." .  $db->quoteName('zone_id') . " = " . (int)$zone_id;
        $query .= " OR state." .  $db->quoteName('zone_id') . " = " . (int)$zone_id . ") AND " .  $db->quoteName('lang_id') . " = " . (int)$lang_id;

        $db->setQuery($query);
        return $db->loadOjectList();
    }

    public function needIdentificationNumber(){
        return JeproshopCountryModelCountry::needIdentificationNumberByCountryId($this->country_id);
    }

    public static function needIdentificationNumberByCountryId($country_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('need_identification_number') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " .(int)$country_id;

        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    public static function containsStates($country_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('contains_states') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " .(int)$country_id;
        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    /**
     * @param $countries_ids
     * @param $zone_id
     * @return bool
     */
    public function affectZoneToSelection($countries_ids, $zone_id){
        // cast every array values to int (security)
        $countries_ids = array_map('intval', $countries_ids);

        $db = JFactory::getDBO();
        $query = "UPDATE " . $db->quoteName('#__jeproshop_country') . " SET " . $db->quoteName('zone_id') . " = " . (int)$zone_id . " WHERE " . $db->quoteName('country_id') . " IN (" . implode(',', $countries_ids) . ") ";
        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Get a country id with its name
     *
     * @param integer $lang_id Language ID
     * @param string $country Country Name
     * @return intval Country id
     */
    public static function getIdByName($lang_id = null, $country){
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('country_id') . " FROM " . $db->quoteName('#__jeproshop_country_lang') . " WHERE " . $db->quoteName('name') . " LIKE " . $db->quote($country);
        if ($lang_id) {
            $query .= " AND " . $db->quoteName('lang_id') . " = " . (int)$lang_id;
        }
        $db->setQuery($query);
        $result =$db->loadObject();

        return (int)$result->country_id;
    }

    public static function getNeedZipCode($country_id){
        if (!(int)$country_id)
            return false;

        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('need_zip_code') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " .(int)$country_id;

        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function getZipCodeFormat($country_id){
        if (!(int)$country_id)
            return false;

        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('zip_code_format') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country_id;

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Get a country iso with its ID
     *
     * @param integer $country_id Country ID
     * @return string Country iso
     */
    public static function getIsoById($country_id){
        if (!isset(JeproshopCountryModelCountry::$cache_iso_by_id[$country_id])){
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('iso_code') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " . (int)($country_id);
            $db->setQuery($query);
            JeproshopCountryModelCountry::$cache_iso_by_id[$country_id] = $db->loadResult();
        }

        return JeproshopCountryModelCountry::$cache_iso_by_id[$country_id];
    }

    /**
     * Get a country ID with its iso code
     *
     * @param string $iso_code Country iso code
     * @return integer Country ID
     */
    public static function getByIso($iso_code){
        if (!JeproshopTools::isLanguageIsoCode($iso_code))
            die(Tools::displayError());

        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('country_id') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('iso_code') . " = " . $db->quote(strtoupper($iso_code));
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->country_id;
    }

    public static function getCountriesByShopId($shop_id, $lang_id){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_country') . " AS country LEFT JOIN " . $db->quoteName('#__jeproshop_country_shop') . " AS country_shop ON (country_shop." . $db->quoteName('country_id') . " = country.";
        $query .= $db->quoteName('country_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id') . " = country_lang." . $db->quoteName('country_id');
        $query .= " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE " . $db->qauoteName('shop_id') . " = " . (int)$shop_id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getZoneId($country_id){
        if (!JeproshopTools::isUnsignedInt($country_id))
            die(JError::raiseError());

        if (isset(self::$_zone_ids[$country_id]))
            return self::$_zone_ids[$country_id];
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('zone_id') . " FROM " . $db->quoteName('#__jeproshop_country') . " WHERE " . $db->quoteName('country_id') . " = " .(int)$country_id;
        $db->setQuery($query);
        $result = $db->loadObject();

        self::$_zone_ids[$country_id] = $result->zone_id;
        return $result->zone_id;
    }

    public function updateCountry(JeproshopContext $context = null){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $input = JRequest::get('post');
        $input_data = $input['jform'];

        if(!isset($context)){ $context = JeproshopContext::getContext();  }

        $languages = $context->controller->getLanguages();
        $country_id = $app->input->get('country_id') ? $app->input->get('country_id') : $input['country_id'];

        $country = new JeproshopCountryModelCountry($country_id);

        $currency_id = $input_data['default_currency'];
        $iso_code = $input_data['iso_code'];
        $contains_states = $input_data['contains_states'];
        $need_zip_code = $input_data['need_zip_code'];
        $zone_id = $input_data['zone_id'];
        $display_tax_label = $input_data['display_tax_label'];
        $call_prefix = $input_data['call_prefix'];
        $published = $input_data['published'];
        $need_identification_number = $input_data['need_identification_number'];

        $result = true;

        if(JeproshopTools::isLoadedObject($country, 'country_id')){
            $oldZoneId = $country->zone_id;

            $query = "SELECT " . $db->quoteName('state_id') . " FROM " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country->country_id . " AND " . $db->quoteName('zone_id') . " = " . (int)$oldZoneId;
            $db->setQuery($query);
            $results = $db->loadObjectList();

            if($results && count($results)){
                $ids = array();
                foreach($results as $res){
                    $ids[] = (int)$res->state_id;
                }

                if(count($ids)){
                    $query = "UPDATE " . $db->quoteName('#__jeproshop_state') . " SET " . $db->quoteName('zone_id') . " = " . (int)$zone_id . "	WHERE " . $db->quoteName('state_id') . " IN (" . implode(',', $ids).")";
                    $db->setQuery($query);
                    $result &= $db->query();
                }
            }

            if(!$context->controller->has_errors){
                $query = "UPDATE " . $db->quoteName('#__jeproshop_country') . " country " . JeproshopShopModelShop::addSqlAssociation('country', true, null, true) . " SET country." . $db->quoteName('zone_id') . " = " . (int)$zone_id . ", country.";
                $query .= $db->quoteName('currency_id') . " = " . (int)$currency_id . ", " . $db->quoteName('iso_code') . " = " . $db->quote($iso_code) . ", country." . $db->quoteName('call_prefix') . " = " . $db->quote($call_prefix) . ", country.";
                $query .= $db->quoteName('published') . " = " . (int)$published . ", country." . $db->quoteName('contains_states') . " = " . (int)$contains_states . ", country." . $db->quoteName('need_identification_number') . " = ";
                $query .= (int)$need_identification_number . ", country." . $db->quoteName('need_zip_code') . " = " . (int)$need_zip_code . ", country." . $db->quoteName('display_tax_label') . " = " . (int)$display_tax_label . " WHERE country.";
                $query .= $db->quoteName('country_id') . " = " . (int)$country_id;

                $db->setQuery($query);
                $result &= $db->query();

                foreach($languages as $language){
                    $query = "UPDATE " . $db->quoteName('#__jeproshop_country_lang') .  " SET " . $db->quoteName('name') . " = " . $db->quote($input_data['name_' . $language->lang_id]) . " WHERE " . $db->quoteName('country_id');
                    $query .= " = " . (int)$country_id  . " AND " . $db->quoteName('lang_id') . " = " . (int)$language->lang_id;
                    $db->setQuery($query);
                    $result &= $db->query();
                }
            }
        }

        return $result;
    }
	
	public function isMultiShop(){
		return JeproshopShopModelShop::isTableAssociated('country') || !empty($this->multiLangShop);
	}

    public function delete()
    {
        if (!parent::delete())
            return false;
        return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_rule_country WHERE id_country = '.(int)$this->id);
    }

    public static function addModuleRestrictions(array $shops = array(), array $countries = array(), array $modules = array()){
        if (!count($shops))
            $shops = Shop::getShops(true, null, true);

        if (!count($countries))
            $countries = Country::getCountries((int)Context::getContext()->cookie->id_lang);

        if (!count($modules))
            $modules = Module::getPaymentModules();

        $sql = false;
        foreach ($shops as $id_shop)
            foreach ($countries as $country)
                foreach ($modules as $module)
                    $sql .= '('.(int)$module['id_module'].', '.(int)$id_shop.', '.(int)$country['id_country'].'),';

        if ($sql)
        {
            $sql = 'INSERT IGNORE INTO `'._DB_PREFIX_.'module_country` (`id_module`, `id_shop`, `id_country`) VALUES '.rtrim($sql, ',');
            return Db::getInstance()->execute($sql);
        }
        else
            return true;
    }

    public function add($autodate = true, $null_values = false)
    {
        $return = parent::add($autodate, $null_values) && self::addModuleRestrictions(array(), array(array('id_country' => $this->id)), array());
        return $return;
    }
}

class JeproshopStateModelState extends JModelLegacy
{
	public $state_id;
	
	/** @var integer Country id which state belongs */
	public $country_id;
	
	/** @var integer Zone id which state belongs */
	public $zone_id;
	
	/** @var string 2 letters iso code */
	public $iso_code;
	
	/** @var string Name */
	public $name;
	
	/** @var boolean Status for delivery */
	public $published = true;
	
	public function __construct($state_id = null){
		$db = JFactory::getDBO();
		/*
		if ($lang_id !== null)
			$this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) !== false) ? $lang_id : JeproshopSettingModelSetting::getValue('default_lang');
		
		if ($shop_id && $this->isMultishop()){
			$this->shop_id = (int)$shop_id;
			$this->get_shop_from_context = false;
		}
		
		if ($this->isMultishop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		} */
		
		if ($state_id){
			// Load object from database if object id is present
			$cache_id = 'jeproshop_model_state_'.(int)$state_id.'_'; //.(int)$this->shop_id . '_'.(int)$lang_id;
			if (!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_state') . " AS state WHERE state.state_id = " . (int)$state_id;
				
		
				/*/ Get lang informations
				if ($lang_id)
				{
					$sql->leftJoin($this->def['table'].'_lang', 'b', 'a.'.$this->def['primary'].' = b.'.$this->def['primary'].' AND b.id_lang = '.(int)$id_lang);
					if ($this->id_shop && !empty($this->def['multilang_shop']))
						$sql->where('b.id_shop = '.$this->id_shop);
				}
		
				// Get shop informations
				if (Shop::isTableAssociated($this->def['table']))
					$sql->leftJoin($this->def['table'].'_shop', 'c', 'a.'.$this->def['primary'].' = c.'.$this->def['primary'].' AND c.id_shop = '.(int)$this->id_shop);
				*/
				
				$db->setQuery($query);
				$state_data = $db->loadObject();
				
				if ($state_data){
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
					}*/
					JeproshopCache::store($cache_id, $state_data);
				}
			}else{
				$state_data = JeproshopCache::retrieve($cache_id);
			}
			
			if ($state_data){
				$this->state_id = (int)$state_id;
				foreach ($state_data as $key => $value){
					if (array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
	}

    public function isMultiShop(){
        return (JeproshopShopModelShop::isTableAssociated('state') || !empty($this->multiLangShop));
    }

	public function getStateList(JeproshopContext $context = NULL){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
	
		if(!$context){ $context = JeproshopContext::getContext(); }
	}

    /**
     * Get a state name with its ID
     *
     * @param integer $state_id Country ID
     * @return string State name
     */
    public static function getNameById($state_id){
        if (!$state_id)
            return false;
        $cache_id = 'jeproshop_state_get_name_by_id_'. (int)$state_id;
        if (!JeproshopCache::isStored($cache_id)) {
            $db = JFactory::getDBO();
            $query = "SELECT " . $db->quoteName('name') . "	FROM " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('state_id') . "= " . (int)$state_id;

            $db->setQuery($query);
            $result = $db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    public static function getStates($lang_id = false, $active = false)
    {
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('state_id') . ", " . $db->quoteName('country_id') . ", " . $db->quoteName('zone_id') . ", " . $db->quoteName('iso_code') . ", " . $db->quoteName('name') . ", " . $db->quoteName('published');
        $query .= " FROM " . $db->quoteName('#__jeproshop_state') . ($active ? " WHERE " . $db->quoteName('published') . " = 1 " : "");
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get a state id with its name
     *
     * @param string $state_name Country ID
     * @return integer state id
     */
    public static function getIdByName($state_name)
    {
        if (empty($state_name))
            return false;
        $cache_id = 'jeproshop_state_get_id_by_name_'. $state_name;
        if (!JeproshopCache::isStored($cache_id))
        {
            $db =Jfactory::getDBO();
            $query = "SELECT " . $db->quoteName('state_id') . " FROM " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('name') . " LIKE " . $db->quote($state_name);
            $db->setQuery($query);
            $result = (int)$db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    /**
     * Get a state id with its iso code
     *
     * @param string $iso_code Iso code
     * @param null $country_id
     * @return int state id
     */
    public static function getIdByIso($iso_code, $country_id = null)
    {
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('state_id') . " FROM " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('iso_code') . " = " . $db->quote($iso_code);
        $query .= ($country_id ? " AND " . $db->quoteName('country_id') . " = " . (int)$country_id : "");

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Delete a state only if is not in use
     *
     * @return boolean
     */
    public function delete()
    {
        if (!$this->isUsed())
        {
            $db = JFactory::getDBO();
            $query = "DELETE " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('state_id') . " = " . (int)$this->state_id;
            // Database deletion
            $db->setQuery($query);
            $result = $db->query();
            if (!$result)
                return false;

            // Database deletion for multilingual fields related to the object
            if (!empty($this->multilang)) {
                $query = "DELETE " . $db->quoteName('#__jeproshop_state_lang') . " WHERE " . $db->quoteName('state_id') . " = " . (int)$this->state_id;
                $db->setQuery($query);
                $result &= $db->query();
            }
            return $result;
        }
        else
            return false;
    }

    /**
     * Check if a state is used
     *
     * @return boolean
     */
    public function isUsed()
    {
        return ($this->countUsed() > 0);
    }

    /**
     * Returns the number of utilisation of a state
     *
     * @return integer count for this state
     */
    public function countUsed()
    {
        $db  = Jfactory::getDBO();

        $query = "SELECT  COOUNT(*) FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('state_id') . " = " . (int)$this->state_id;
        $db->setQuery($query);

        return $db->loadResult();
    }

    public static function getStatesByCountryId($country_id)
    {
        if (empty($country_id))
            die(JRError::raiseError());

        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_state') . " AS state WHERE state." . $db->quoteName('country_id') . " = " . (int)$country_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function hasCounties($state_id)
    {
        return count(JeproshopCountyModelCounty::getCounties((int)$state_id));
    }

    public static function geZoneId($state_id)
    {
        if (!JeproshopTools::isUnsignedInt($state_id))
            die(JError::raiseError());

        $db  = Jfactory::getDBO();

        $query = "SELECT " . $db->quoteName('zone_id') . " FROM " . $db->quoteName('#__jeproshop_state') . " WHERE " . $db->quoteName('state_id') . " = " . (int)$state_id;
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * @param $state_ids
     * @param int $zone_id
     * @return bool
     */
    public function affectZoneToSelection($state_ids, $zone_id)
    {
        // cast every array values to int (security)
        $state_ids = array_map('intval', $state_ids);
        $db  = Jfactory::getDBO();

        $query = "UPDATE " . $db->quoteName('#__jeproshop_state') . " SET " . $db->quoteName('zone_id') . " = " . (int)$zone_id . " WHERE " . $db->quoteName('state_id') . " IN(" . implode(',', $state_ids) . ")";
        $db->setQuery($query);
        return $db->query();
    }
}

class JeproshopZoneModelZone extends JModelLegacy
{
	public $zone_id;
	
	/** @var string Name */
	public $name;
	
	public $allow_delivery;

    private $pagination;

    public function __construct($zone_id = null){
        $db = JFactory::getDBO();

        if($zone_id){
            $cache_id =  'jeproshop_zone_model_' . (int)$zone_id;
            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_zone') . " AS zone WHERE " . $db->quoteName('zone_id') . " = " . (int)$zone_id;
                $db->setQuery($query);
                $zoneData = $db->loadObject();
                JeproshopCache::store($cache_id, $zoneData);
            }else{
                $zoneData = JeproshopCache::retrieve($cache_id);
            }

            if($zoneData){
                $zoneData->zone_id = (int)$zone_id;
                foreach($zoneData as $key => $value){
                    $this->{$key} = $value;
                }
            }
        }
    }
	
	public function getZoneList(JeproshopContext $context = NULL){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		if(!$context){ $context = JeproshopContext::getContext(); }
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
		$shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
		$allow_delivery = $app->getUserStateFromRequest($option. $view. '.allow_delivery', 'allow_delivery', 0, 'int');
		
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS zone." .  $db->quoteName('zone_id') . ", zone." .  $db->quoteName('name');
			$query .= " AS zone_name, zone." .  $db->quoteName('allow_delivery') . " FROM " . $db->quoteName('#__jeproshop_zone');
			$query .= ($allow_delivery ? " WHERE zone.allow_delivery = 1 " : "");
			$query .= " AS zone ORDER BY " . $db->quoteName('name') . " ASC ";
		
			$db->setQuery($query);
			$total = count($db->loadObjectList());
				
			$query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
				
			$db->setQuery($query);
			$zones = $db->loadObjectList();
			
			if($use_limit == true){
				$limit_start = (int)$limit_start -(int)$limit;
				if($limit_start < 0){ break; }
			}else{ break; }
		}while(empty($zones));
		
		$this->pagination = new JPagination($total, $limit_start, $limit);
		return $zones;
	}
	
	public function getPagination(){
		return $this->pagination;
	}

    /**
     * Get all available geographical zones
     *
     * @param bool|type $allow_delivery
     * @return type
     */
	public static function getZones($allow_delivery = FALSE){
        $cache_id = 'jeproshop_zone_model_get_zones_' . (bool)$allow_delivery;
        if(!JeproshopCache::isStored($cache_id)) {
            $db = JFactory::getDBO();

            $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_zone') . ($allow_delivery ? " WHERE allow_delivery = 1 " : "");
            $query .= " ORDER BY " . $db->quoteName('name') . " ASC ";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
	}

    /**
     * Get a zone ID from its default language name
     *
     * @param string $name
     * @return integer id_zone
     */
    public static function getIdByName($name){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('zone_id') . " FROM " . $db->quoteName('#__jeproshop_zone') . " WHERE " . $db->quoteName('name') . " = " . $db->quote($name);

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Delete a zone
     *
     * @return boolean Deletion result
     */
    public function delete() {
        $db = JFactory::getDBO();
        if (parent::delete()) {
            // Delete regarding delivery preferences
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_carrier_zone') . " WHERE " . $db->quoteName('zone_id') . " = " .(int)$this->zone_id;
            $db->setQuery($query);
            $result = $db->query();
            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_delivery') . " WHERE " . $db->quoteName('zone_id') . " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();

            // Update Country & state zone with 0
            $query = "UPDATE " . $db->quoteName('#__jeproshop_country') . " SET " . $db->quoteName('zone_id') . " = 0 WHERE " . $db->quoteName('zone_id'). " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();
            $query = "UPDATE " . $db->quoteName('#__jeproshop_state') ." SET " . $db->quoteName('zone_id') . " = 0 WHERE " . $db->quoteName('zone_id') . " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();

            return $result;
        }

        return false;
    }
}