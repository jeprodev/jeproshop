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

class JeproshopAddressModelAddress extends JModelLegacy
{
	public $address_id = null;

	public $customer_id = null;

	public $manufacturer_id = null;

	public $supplier_id = null;

	public $developer_id = null;

	public $warehouse_id = null;

    public $lang_id;

	public $country_id;

	public $state_id;

	public $country;

	public $alias;

	public $company;

	public $lastname;

	public $firstname;

	public $address1;

	public $address2;

	public $postcode;

	public $city;

	public $other;

	public $phone;

	public $phone_mobile;

	public $vat_number;

	public $dni;

	public $date_add;

	public $date_upd;

	public $deleted = 0;

	protected $context;

    private $pagination;

	protected static $_zonesIds = array();
	protected static $_countriesIds = array();
	
	public function __construct($address_id = NULL, $lang_id = NULL) {
		if($lang_id !== NULL){
			$this->lang_id = JeproshopLanguageModelLanguage::getLanguage($lang_id) !== FALSE ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang');
		}
	
		if($address_id){
			//Load address from database if address id is provided
			$cache_id = 'jeproshop_address_model_' . $address_id . '_' . $lang_id;
			if(!JeproshopCache::isStored($cache_id)){
				$db = JFactory::getDBO();
	
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_address') . " AS address ";
				$query .= " WHERE address.address_id = " . (int)$address_id;
	
				$db->setQuery($query);
				$address_data = $db->loadObject();
				if($address_data){
					JeproshopCache::store($cache_id, $address_data);
				}
			}  else {
				$address_data = JeproshopCache::retrieve($cache_id);
			}
	
			if($address_data){
				$address_data->address_id = $address_id;
				foreach($address_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
	
		if($this->address_id){
			$this->country = JeproshopCountryModelCountry::getNameById($lang_id, $this->country_id);
		}
	}

    public static function getCountryAndState($address_id) {
        if (isset(self::$_countriesIds[$address_id]))
            return self::$_countriesIds[$address_id];
        if ($address_id) {
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('country_id') . ", " . $db->quoteName('state_id') . ", " . $db->quoteName('vat_number') . ", " . $db->quoteName('postcode') . " FROM ";
            $query .= $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('address_id') . " = " . (int)$address_id;
            $db->setQuery($query);
            $result = $db->loadObject();
        }else
            $result = false;
        self::$_countriesIds[$address_id] = $result;
        return $result;
    }

    /**
     * Initialize an address corresponding to the specified id address or if empty to the
     * default shop configuration
     *
     * @param int $address_id
     * @return Address address
     */
	public static function initialize($address_id = null){
		//if an address_id has been specified retrieve the address
		if($address_id){
			$address = new JeproshopAddressModelAddress($address_id);
	
			if(!JeproshopTools::isLoadedObject($address, 'address_id')){
				JError::raiseError(500, JText::_('COM_JEPROSHOP_INVALID_ADDRESS_MESSAGE'));
			}
		}else{
			// Set the default address
			$address = new JeproshopAddressModelAddress();
			$address->country_id = (int)  JeproshopContext::getContext()->country->country_id;
			$address->state_id = 0;
			$address->postcode = 0;
		}
		return $address;
	}

    public function getAddressList(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $context = JeproshopContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitStart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'address_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC' , 'string');

        $use_limit = true;
        if($limit === false){
            $use_limit = false;
        }

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS address." . $db->quoteName('address_id') . ", address." . $db->quoteName('firstname') . ", address.";
            $query .= $db->quoteName('lastname') . ", address." . $db->quoteName('address1') . ", address." . $db->quoteName('postcode') . ", address.";
            $query .= $db->quoteName('city') . ", country_lang." . $db->quoteName('name') . " AS country FROM " . $db->quoteName('#__jeproshop_address');
            $query .= " AS address LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . "country_lang ON (country_lang." . $db->quoteName('country_id');
            $query .= " = address." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") LEFT JOIN ";
            $query .= $db->quoteName('#__jeproshop_customer') . " AS customer ON address." . $db->quoteName('customer_id') . " = customer." . $db->quoteName('customer_id');
            $query .= " WHERE address.customer_id != 0 " . JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER, 'customer');
            $query .= " ORDER BY " . ((str_replace('`', '', $order_by) == 'address_id') ? "address." : "") . $order_by . " " . $order_way;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit == true) ? " LIMIT " . (int)$limitStart . ", " . (int)$limit : " ");

            $db->setQuery($query);
            $addresses = $db->loadObjectList();

            if($use_limit == true){
                $limitStart = (int)$limitStart -(int)$limit;
                if($limitStart < 0){ break; }
            }else{ break; }
        }while(empty($addresses));

        $this->pagination = new JPagination($total, $limitStart, $limit);
        return $addresses;
    }

    public function getPagination(){ return $this->pagination; }

    /**
     * @see ObjectModel::add()
     */
    public function add(){
        $db = JFactory::getDBO();
        $this->date_add = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        if(JeproshopShopModelShop::isTableAssociated('address')){
            $shopListIds = JeproshopShopModelShop::getContextListShopIds();
            if(count($this->shop_list_id) > 0){
                $shopListIds = $this->shop_list_id;
            }
        }

        if(JeproshopShopModelShop::checkDefaultShopId('address')){
            $this->default_shop_id = min($shopListIds);
        }

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_address');
        if (!parent::add($autodate, $null_values))
            return false;

        if(JeproshopTools::isUnsignedInt($this->customer_id)) {
            JeproshopCustomerModelCustomer::resetAddressCache($this->customer_id);
        }
        return true;
    }

    public function update($null_values = false){
        // Empty related caches
        if (isset(self::$_countriesIds[$this->address_id]))
            unset(self::$_countriesIds[$this->address_id]);
        if (isset(self::$_zonesIds[$this->address_id]))
            unset(self::$_zonesIds[$this->address_id]);

        if (JeproshopTools::isUnsignedInt($this->customer_id)) {
            JeproshopCustomerModelCustomer::resetAddressCache($this->customer_id);
        }

        return parent::update($null_values);
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete(){
        if (JeproshopTools::isUnsignedInt($this->customer_id))
            JeproshopCustomerModelCustomer::resetAddressCache($this->customer_id);

        if (!$this->isUsed()) {
            return parent::delete();
        }else{
            $this->deleted = true;
            return $this->update();
        }
    }

    /**
     * Returns fields required for an address in an array hash
     * @return array hash values
     */
    public static function getFieldsValidate(){
        $tmp_addr = new JeproshopAddressModelAddress();
        $out = $tmp_addr->fieldsValidate;

        unset($tmp_addr);
        return $out;
    }

    /**
     * @see ObjectModel::validateController()
     */
    public function validateController($htmlentities = true)
    {
        $errors = parent::validateController($htmlentities);
        if (!Configuration::get('VATNUMBER_MANAGEMENT') || !Configuration::get('VATNUMBER_CHECKING'))
            return $errors;
        include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        if (class_exists('VatNumber', false))
            return array_merge($errors, VatNumber::WebServiceCheck($this->vat_number));
        return $errors;
    }


    /**
     * Check if country is active for a given address
     *
     * @param integer $address_id Address id for which we want to get country status
     * @return integer Country status
     */
    public static function isCountryActiveById($address_id){
        if(!isset($address_id) || empty($address_id))
            return false;

        $cache_id = 'jeproshop_address_is_country_active_by_id_'.(int)$address_id;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT category." . $db->quoteName('published') . " FROM " . $db->quoteName('#__jeproshop_address') . " AS address LEFT JOIN " . $db->quoteName('#__jeproshop_country') ;
            $query .= " AS country ON (country." . $db->quoteName('country_id') . " = address." . $db->quoteName('country_id') . ") WHERE address." . $db->quoteName('address_id') . " = " .(int)$address_id;

            $db->setQuery($query);
            $result = (bool)$db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    /**
     * Check if address is used (at least one order placed)
     *
     * @return integer Order count for this address
     */
    public function isUsed(){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(" . $db->quoteName('order_id') . ") AS used FROM " . $db->quoteName('#__jeproshop_orders') . " WHERE " . $db->quoteName('delivery_address_id') . " = " . (int)$this->address_id . " OR " . $db->quoteName('invoice_address_id') . " = " . (int)$this->address_id;
        $db->setQuery($query);
        $result = $db->loadObject();

        return isset($result->used) ? $result->used : false;
    }

    public static function getCustomerFirstAddressId($customer_id, $active = true){
        if (!$customer_id)
            return false;
        $cache_id = 'jeproshop_model_address_get_first_customer_address_id_'.(int)$customer_id .'_'.(bool)$active;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('address_id') . " FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$customer_id . " AND " . $db->quoteName('deleted');
            $query .= " = 0 " . ($active ? " AND " . $db->quoteName('published') . " = 1 " : "");
            $db->setQuery($query);
            $result = (int)$db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    /**
     * Returns id_address for a given id_supplier
     * @since 1.5.0
     * @param int $supplier_id
     * @return int $address_id
     */
    public static function getAddressIdBySupplierId($supplier_id){
        $db = JFactory::getDBO();
        $query = "SELECT "  . $db->quoteName('address_id') . " FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('supplier_id') . " = " . (int)$supplier_id . " AND " . $db->quoteName('deleted') . " = 0 AND " . $db->quoteName('customer_id');
        $query .= " = 0 AND " . $db->quoteName('manufacturer_id') . " = 0 AND " . $db->quoteName('warehouse_id') . " = 0";

        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function aliasExist($alias, $address_id, $customer_id){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('alias') . " = " . $db->quote($alias) . " AND " . $db->quoteName('address_id') . " = " . (int)$address_id . " AND ";
        $query .= $db->quoteName('customer_id') . " = " . (int)$customer_id . " AND " . $db->quoteName('deleted') . " = 0";

        $db->setQuery($query);
        return $db->loadResult();
    }
}


class JeproshopAddressFormatModelAddressFormat extends JModelLegacy{
    /** @var integer */
    public $address_format_id;

    /** @var integer */
    public $country_id;

    /** @var string */
    public $format;

    protected $_errorFormatList = array();


    public static $requireFormFieldsList = array(
        'firstname',
        'name',
        'address1',
        'city',
        'postcode',
        'Country:name',
        'State:name');

    public static $forbiddenPropertyList = array(
        'deleted',
        'date_add',
        'alias',
        'secure_key',
        'note',
        'newsletter',
        'ip_registration_newsletter',
        'newsletter_date_add',
        'optin',
        'passwd',
        'last_passwd_gen',
        'active',
        'is_guest',
        'date_upd',
        'country',
        'years',
        'days',
        'months',
        'description',
        'meta_description',
        'short_description',
        'link_rewrite',
        'meta_title',
        'meta_keywords',
        'display_tax_label',
        'need_zip_code',
        'contains_states',
        'call_prefixes',
        'show_public_prices',
        'max_payment',
        'max_payment_days',
        'geoloc_postcode',
        'logged',
        'account_number',
        'groupBox',
        'ape',
        'max_payment',
        'outstanding_allow_amount',
        'call_prefix',
        'definition',
        'debug_list'
    );

    public static $forbiddenClassList = array(
        'Manufacturer',
        'Supplier');

    const _CLEANING_REGEX_ = '#([^\w:_]+)#i';


    /**
     * Returns address format fields in array by country
     *
     * @param int $country_id
     * @param bool $split_all
     * @param bool $cleaned
     * @return Array String field address format
     */
    public static function getOrderedAddressFields($country_id = 0, $split_all = false, $cleaned = false){
        $out = array();
        $field_set = explode("\n", JeproshopAddressFormatModelAddressFormat::getAddressCountryFormat($country_id));
        foreach ($field_set as $field_item){
            if ($split_all){
                $keyList = array();
                if ($cleaned){
                    $keyList = ($cleaned) ? preg_split(self::_CLEANING_REGEX_, $field_item, -1, PREG_SPLIT_NO_EMPTY) : explode(' ', $field_item);
                }
                foreach ($keyList as $word_item){ $out[] = trim($word_item); }
            } else{
                $out[] = ($cleaned) ? implode(' ', preg_split(self::_CLEANING_REGEX_, trim($field_item), -1, PREG_SPLIT_NO_EMPTY)) : trim($field_item);
            }
        }
        return $out;
    }

    /**
     * Returns address format by country if not defined using default country
     *     *
     * @param int $country_id
     * @return String field address format
     */
    public static function getAddressCountryFormat($country_id = 0){
        $country_id = (int)$country_id;

        $tmp_obj = new JeproshopAddressFormatModelAddressFormat();
        $tmp_obj->country_id = $country_id;
        $out = $tmp_obj->getFormat($tmp_obj->country_id);
        unset($tmp_obj);
        return $out;
    }

    /**
     * Returns address format by country
     *
     * @param $country_id
     * @return String field address format
     */
    public function getFormat($country_id) {
        $out = $this->getFormatFromDataBase($country_id);
        if (empty($out))
            $out = $this->getFormatFromDataBase(JeproshopSettingModelSetting::getValue('default_country'));
        return $out;
    }

    protected function getFormatFromDataBase($country_id){
        $cache_key = 'jeproshop_address_format_get_format_from_data_base_'.$country_id;
        if (!JeproshopCache::isStored($cache_key)) {
            $db = JFactory::getDBO();

            $query = "SELECT format FROM " . $db->quoteName('#__jeproshop_address_format') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country_id;

            $db->setQuery($query);
            $format = $db->loadResult();
            JeproshopCache::store($cache_key, trim($format));
        }
        return JeproshopCache::retrieve($cache_key);
    }

    /**
     * Return a data array containing ordered, formatValue and object fields
     * @param $address
     * @return array
     */
    public static function getFormattedLayoutData($address){
        $layoutData = array();

        if ($address && $address instanceof JeproshopAddressModelAddress){
            $layoutData['ordered'] = JeproshopAddressFormatModelAddressFormat::getOrderedAddressFields((int)$address->country_id);
            $layoutData['format'] = JeproshopAddressFormatModelAddressFormat::getFormattedAddressFieldsValues($address, $layoutData['ordered']);
            $layoutData['object'] = array();

            $reflect = new ReflectionObject($address);
            $public_properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($public_properties as $property)
                if (isset($address->{$property->getName()}))
                    $layoutData['object'][$property->getName()] = $address->{$property->getName()};
        }
        return $layoutData;
    }

    public static function generateAddressSmarty($params, &$smarty){
        return JeproshopAddressFormatModelAddressFormat::generateAddress(
            $params['address'],
            (isset($params['patternRules']) ? $params['patternRules'] : array()),
            (isset($params['newLine']) ? $params['newLine'] : "\r\n"),
            (isset($params['separator']) ? $params['separator'] : ' '),
            (isset($params['style']) ? $params['style'] : array())
        );
    }

    /**
     * Returns selected fields required for an address in an array according to a selection hash
     * @param $className
     * @return array String values
     */
    public static function getValidateFields($className)
    {
        $propertyList = array();

        if (class_exists($className))
        {
            $object = new $className();
            $reflect = new ReflectionObject($object);

            // Check if the property is accessible
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property)
            {
                $propertyName = $property->getName();
                if ((!in_array($propertyName, AddressFormat::$forbiddenPropertyList)) &&
                    (!preg_match('#id|id_\w#', $propertyName)))
                    $propertyList[] = $propertyName;
            }
            unset($object);
            unset($reflect);
        }
        return $propertyList;
    }

    /**
     * Return a list of liable class of the className
     * @param $className
     * @return array
     */
    public static function getLiableClass($className){
        $objectList = array();

        if (class_exists($className))
        {
            $object = new $className();
            $reflect = new ReflectionObject($object);

            // Get all the name object liable to the Address class
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property)
            {
                $propertyName = $property->getName();
                if (preg_match('#\w_id#', $propertyName) && strlen($propertyName) > 3)
                {
                    $nameObject = ucfirst(substr($propertyName, 3));
                    if (!in_array($nameObject, self::$forbiddenClassList) &&
                        class_exists($nameObject))
                        $objectList[$nameObject] = new $nameObject();
                }
            }
            unset($object);
            unset($reflect);
        }
        return $objectList;
    }

    protected function getFormatDB($country_id){
        $cache_id = 'jeproshop_address_format_get_format_db_' . $country_id;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT format FROM " . $db->quoteName('#__jeproshop_address_format') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country_id;
            $db->setQuery($query);
            $format = $db->loadResult();
            JeproshopCache::store($cache_id, trim($format));
        }
        return JeproshopCache::retrieve($cache_id);
    }

    /***
	 * Verify the existence of a field name and check the availability
	 * of an association between a field name and a class (ClassName:fieldName)
	 * if the separator is overview
	 * @param $patternName is the composition of the class and field name
	 * @param $fieldsValidate contains the list of available field for the Address class
	 */
    protected function checkLiableAssociation($patternName, $fieldsValidate){
        $patternName = trim($patternName);

        if ($associationName = explode(':', $patternName)){
            $totalNameUsed = count($associationName);
            if ($totalNameUsed > 2) {
                $this->_errorFormatList[] = Tools::displayError('This association has too many elements.');
            }else if ($totalNameUsed == 1){
                $associationName[0] = strtolower($associationName[0]);
                if (in_array($associationName[0], self::$forbiddenPropertyList) || !$this->checkValidateClassField('Address', $associationName[0], false)) {
                    $this->_errorFormatList[] = Tools::displayError('This name is not allowed.') . ': ' . $associationName[0];
                }
            }else if ($totalNameUsed == 2){
                if (empty($associationName[0]) || empty($associationName[1]))
                    $this->_errorFormatList[] = Tools::displayError('Syntax error with this pattern.').': '.$patternName;
                else
                {
                    $associationName[0] = ucfirst($associationName[0]);
                    $associationName[1] = strtolower($associationName[1]);

                    if (in_array($associationName[0], self::$forbiddenClassList))
                        $this->_errorFormatList[] = Tools::displayError('This name is not allowed.').': '.
                            $associationName[0];
                    else
                    {
                        // Check if the id field name exist in the Address class
                        // Don't check this attribute on Address (no sense)
                        if ($associationName[0] != 'Address')
                            $this->checkValidateClassField('Address', 'id_'.strtolower($associationName[0]), true);

                        // Check if the field name exist in the class write by the user
                        $this->checkValidateClassField($associationName[0], $associationName[1], false);
                    }
                }
            }
        }
    }

    /***
     * Check if the set fields are valid
     */
    public function checkFormatFields(){
        $this->_errorFormatList = array();
        $fieldsValidate = Address::getFieldsValidate();
        $usedKeyList = array();

        $multipleLineFields = explode("\n", $this->format);
        if ($multipleLineFields && is_array($multipleLineFields))
            foreach ($multipleLineFields as $lineField){
                if (($patternsName = preg_split(self::_CLEANING_REGEX_, $lineField, -1, PREG_SPLIT_NO_EMPTY)))
                    if (is_array($patternsName)) {
                        foreach ($patternsName as $patternName){
                            if (!in_array($patternName, $usedKeyList))         {
                                $this->checkLiableAssociation($patternName, $fieldsValidate);
                                $usedKeyList[] = $patternName;
                            }
                            else
                                $this->_errorFormatList[] = Tools::displayError('This key has already been used.').
                                    ': '.$patternName;
                        }
                    }
            }

        return (count($this->_errorFormatList)) ? false : true;
    }

    /**
     * Returns the error list
     */
    public function getErrorList(){
        return $this->_errorFormatList;
    }

    /**
     ** Set the layout key with the liable value
     ** example : (firstname) =>
     **         : (firstname-lastname) =>
     * @param $formattedValueList
     * @param $currentLine
     * @param $currentKeyList
     */
    protected static function setOriginalDisplayFormat(&$formattedValueList, $currentLine, $currentKeyList){
        if ($currentKeyList && is_array($currentKeyList)) {
            if ($originalFormattedPatternList = explode(' ', $currentLine)) {
                // Foreach the available pattern
                foreach ($originalFormattedPatternList as $patternNum => $pattern) {
                    // Var allows to modify the good formatted key value when multiple key exist into the same pattern
                    $mainFormattedKey = '';

                    // Multiple key can be found in the same pattern
                    foreach ($currentKeyList as $key) {
                        // Check if we need to use an older modified pattern if a key has already be matched before
                        $replacedValue = empty($mainFormattedKey) ? $pattern : $formattedValueList[$mainFormattedKey];
                        if (($formattedValue = preg_replace('/' . $key . '/', $formattedValueList[$key], $replacedValue, -1, $count))) {
                            if ($count) {
                                // Allow to check multiple key in the same pattern,
                                if (empty($mainFormattedKey))
                                    $mainFormattedKey = $key;
                                // Set the pattern value to an empty string if an older key has already been matched before
                                if ($mainFormattedKey != $key)
                                    $formattedValueList[$key] = '';
                                // Store the new pattern value
                                $formattedValueList[$mainFormattedKey] = $formattedValue;
                                unset($originalFormattedPatternList[$patternNum]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     ** Cleaned the layout set by the user
     * @param $orderedAddressField
     */
    public static function cleanOrderedAddress(&$orderedAddressField){
        foreach ($orderedAddressField as &$line){
            $cleanedLine = '';
            if (($keyList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY))){
                foreach ($keyList as $key)
                    $cleanedLine .= $key.' ';
                $cleanedLine = trim($cleanedLine);
                $line = $cleanedLine;
            }
        }
    }

    /***
     * Returns the formatted fields with associated values
     *
     * @param $address is an instantiated Address object
     * @param $addressFormat is the format
     * @param null $lang_id
     * @return float Array
     */
    public static function getFormattedAddressFieldsValues($address, $addressFormat, $lang_id = null){
        if (!$lang_id)
            $lang_id = JeproshopContext::getContext()->language->lang_id;
        $tab = array();
        $temporaryObject = array();

        // Check if $address exist and it's an instantiate object of Address
        if ($address && ($address instanceof JeproshopAddressFormatModelAddressFormat))
            foreach ($addressFormat as $line)
            {
                if (($keyList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY)) && is_array($keyList))
                {
                    foreach ($keyList as $pattern)
                        if ($associateName = explode(':', $pattern))
                        {
                            $totalName = count($associateName);
                            if ($totalName == 1 && isset($address->{$associateName[0]}))
                                $tab[$associateName[0]] = $address->{$associateName[0]};
                            else
                            {
                                $tab[$pattern] = '';

                                // Check if the property exist in both classes
                                if (($totalName == 2) && class_exists($associateName[0]) &&
                                    property_exists($associateName[0], $associateName[1]) &&
                                    property_exists($address, 'id_'.strtolower($associateName[0])))
                                {
                                    $idFieldName = 'id_'.strtolower($associateName[0]);

                                    if (!isset($temporaryObject[$associateName[0]]))
                                        $temporaryObject[$associateName[0]] = new $associateName[0]($address->{$idFieldName});
                                    if ($temporaryObject[$associateName[0]])
                                        $tab[$pattern] = (is_array($temporaryObject[$associateName[0]]->{$associateName[1]})) ?
                                            ((isset($temporaryObject[$associateName[0]]->{$associateName[1]}[$lang_id])) ?
                                                $temporaryObject[$associateName[0]]->{$associateName[1]}[$lang_id] : '') :
                                            $temporaryObject[$associateName[0]]->{$associateName[1]};
                                }
                            }
                        }
                    JeproshopAddressFormatModelAddressFormat::setOriginalDisplayFormat($tab, $line, $keyList);
                }
            }
        JeproshopAddressFormatModelAddressFormat::cleanOrderedAddress($addressFormat);
        // Free the instantiate objects
        foreach ($temporaryObject as &$object)
            unset($object);
        return $tab;
    }

    /**
     * Generates the full address text
     * @param is|JeproshopAddressModelAddress $address is an instantiate object of Address class
     * @param array $patternRules
     * @param is|string $newLine is a string containing the newLine format
     * @param is|string $separator is a string containing the separator format
     * @param array $style
     * @return string
     * @internal param is $patternPules a defined rules array to avoid some pattern
     */
    public static function generateAddress(JeproshopAddressModelAddress $address, $patternRules = array(), $newLine = "\r\n", $separator = ' ', $style = array()){
        $addressFields = JeproshopAddressFormatModelAddressFormat::getOrderedAddressFields($address->country_id);
        $addressFormattedValues = JeproshopAddressFormatModelAddressFormat::getFormattedAddressFieldsValues($address, $addressFields);

        $addressText = '';
        foreach ($addressFields as $line)
            if (($patternsList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY)))
            {
                $tmpText = '';
                foreach ($patternsList as $pattern)
                    if ((!array_key_exists('avoid', $patternRules)) ||
                        (array_key_exists('avoid', $patternRules) && !in_array($pattern, $patternRules['avoid'])))
                        $tmpText .= (isset($addressFormattedValues[$pattern]) && !empty($addressFormattedValues[$pattern])) ?
                            (((isset($style[$pattern])) ?
                                    (sprintf($style[$pattern], $addressFormattedValues[$pattern])) :
                                    $addressFormattedValues[$pattern]).$separator) : '';
                $tmpText = trim($tmpText);
                $addressText .= (!empty($tmpText)) ? $tmpText.$newLine: '';
            }

        $addressText = preg_replace('/'.preg_quote($newLine,'/').'$/i', '', $addressText);
        $addressText = rtrim($addressText, $separator);

        return $addressText;
    }

    /***
     * Check if the the association of the field name and a class name
     * is valid
     * @className is the name class
     * @fieldName is a property name
     * @isIdField boolean to know if we have to allowed a property name started by 'id_'
     * @param $className
     * @param $fieldName
     * @param $isIdField
     * @return bool
     */
    protected function checkValidateClassField($className, $fieldName, $isIdField){
        $isValid = false;

        if (!class_exists($className)) {
            $this->_errorFormatList[] = Tools::displayError('This class name does not exist.') .
                ': ' . $className;
        }else {
            $obj = new $className();
            $reflect = new ReflectionObject($obj);

            // Check if the property is accessible
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property)
            {
                $propertyName = $property->getName();
                if (($propertyName == $fieldName) && ($isIdField ||
                        (!preg_match('/\bid\b|id_\w+|\bid[A-Z]\w+/', $propertyName))))
                    $isValid = true;
            }

            if (!$isValid)
                $this->_errorFormatList[] = Tools::displayError('This property does not exist in the class or is forbidden.').
                    ': '.$className.': '.$fieldName;

            unset($obj);
            unset($reflect);
        }
        return $isValid;
    }
}