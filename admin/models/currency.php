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

class JeproshopCurrencyModelCurrency extends JModelLegacy
{
	public $currency_id;
	
	/** @var string name */
	public $name;
	
	/** @var string Iso code */
	public $iso_code;
	
	/** @var  string Iso code numeric */
	public $iso_code_num;
	
	/** @var string symbol for short display */
	public $sign;
	
	/** @var int bool used for displaying blank between sign and price */
	public $blank;
	
	/**
	 * contains the sign to display before price, according to its format
	 * @var string
	 */
	public $prefix;
	
	/**
	 * contains the sign to display after price, according to its format
	 * @var string
	 */
	public $suffix;
	
	/** @var double conversion rate  */
	public $conversion_rate;
	
	/** @var int ID used for displaying prices */
	public $format;
	
	/** @var boolean True if currency has been deleted(staying in database as deleted) */
	public $deleted;
	
	/** @var int bool Display decimals on prices */
	public $decimals;
	
	/** @var int bool published  */
	public $published;
	
	public $shop_id;
	
	static protected $currencies = array();
	
	
	public function __construct($currency_id = null, $shop_id = null){
		$db = JFactory::getDBO();		
		
		if($shop_id && $this->isMultiShop()){
			$this->shop_id = (int)$shop_id;
			$this->get_shop_from_context = false;
		}
		
		if($this->isMultiShop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		}
		
		if($currency_id){
			//load object from the  database if the currency id is provided
			$cache_id = 'jeproshop_currency_model_' . (int)$currency_id . '_' . (int)$shop_id;
			if(!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_currency') . " AS currency ";
			
				if(JeproshopShopModelShop::isTableAssociated('currency')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_currency_shop') ." AS currency_shop ON( currency.currency_id = currency_shop.currency_id AND currency_shop.shop_id = " . (int)$this->shop_id . ")";
				} 
				$query .= " WHERE currency.currency_id = " . (int)$currency_id ; 
		
				$db->setQuery($query);
				$currency_data = $db->loadObject();  
				if($currency_data){					
					JeproshopCache::store($cache_id, $currency_data);
				}
			}else{
				$currency_data = JeproshopCache::retrieve($cache_id);
			}

			if($currency_data){
                $currency_data->currency_id = $currency_id;
				foreach($currency_data as $key => $value){ 
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		
		/* prefix and suffix are convenient short cut for displaying price sign before or after the price number */
		$this->prefix = $this->format % 2 != 0 ? $this->sign . " " : "";
		$this->suffix = $this->format % 2 == 0 ? " " . $this->sign : "";
	}
	
	public function isMultiShop(){
		return JeproshopShopModelShop::isTableAssociated('currency') || !empty($this->multiLangShop);
	}
	
	public function isLangMultiShop(){
		return !empty($this->multiLang) && !empty($this->multiLangShop);
	}
	
	public static function getCurrency($currency_id){
		$db = JFactory::getDBO();
	
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_currency') . " WHERE " . $db->quoteName('deleted');
		$query .= " = 0 AND " . $db->quoteName('currency_id') . " = " . (int)$currency_id;
	
		$db->setQuery($query);
		return $db->loadObject();
	}

    public function getCurrenciesList(JeproshopContext $context = NULL){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!isset($context)){ $context = JeproshopContext::getContext(); }


        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT currency." . $db->quoteName('currency_id') . ", currency." . $db->quoteName('name') . ", currency." . $db->quoteName('iso_code') . ", currency." . $db->quoteName('iso_code_num') . ", currency.";
            $query .= $db->quoteName('sign') . ", currency_shop." . $db->quoteName('conversion_rate') . ", currency." . $db->quoteName('published') . " FROM " . $db->quoteName('#__jeproshop_currency') . " AS currency ";
            $query .= JeproshopShopModelShop::addSqlAssociation('currency') . " WHERE currency." . $db->quoteName('deleted') . " = 0 GROUP BY currency." . $db->quoteName('currency_id');

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $currencies = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($currencies));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $currencies;
    }
	
	public function getCurrencies(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		$group_by = $app->getUserStateFromRequest($option. $view. '.group_by', 'group_by', '', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', true, 'string');
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_currency') . " AS currency " . JeproshopShopModelShop::addSqlAssociation('currency');
		$query .= " WHERE " . $db->quoteName('deleted') . " = 0 " . ($published ? " AND currency." . $db->quoteName('published') . " = 1" : "");
		$query .= ($group_by ? " GROUP BY currency." . $db->quoteName('currency_id') : "") . " ORDER BY " . $db->quoteName('name') . " ASC";
		
		/*$query = "SELECT SQL_CALC_FOUND_ROWS currency.* , currency_shop.conversion_rate conversion_rate FROM " . $db->quoteName('#__jeproshop_currency');
		$query .= " AS currency INNER JOIN " . $db->quoteName('#__jeproshop_currency_shop') . " AS currency_shop ON (currency_shop." . $db->quoteName('currency_id');
		$query .= " = currency." . $db->quoteName('currency_id') . " AND currency_shop." . $db->quoteName('shop_id') . " = " . $shop_id . ") WHERE 1 AND currencuy.";
		$query .= $db->quoteName('deleted') . " = 0 GROUP BY currency_id ORDER BY currency." . $db->quoteName('currency_id') . " ASC LIMIT 0,50 "; **/
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getCurrenciesByShopId($shop_id = 0){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_currency') . " AS currency LEFT JOIN " . $db->quoteName('#__jeproshop_currency_shop');
		$query .= " AS currency_shop ON (currency_shop." . $db->quoteName('currency_id') . " = currency." . $db->quoteName('currency_id') . ") ";
		$query .= ($shop_id ? " WHERE currency_shop." . $db->quoteName('shop_id') . " = " .(int)$shop_id : "") . "	ORDER BY " . $db->quoteName('name') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    /**
     * Return available currencies
     *
     * @param bool $object
     * @param bool $published
     * @param bool $group_by
     * @return array Currencies
     */
    public static function getStaticCurrencies($object = false, $published = true, $group_by = false) {
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_currency') . " AS currency " . JeproshopShopModelShop::addSqlAssociation('currency');
        $query .= " WHERE " . $db->quoteName('deleted') . " = 0" . ($published ? " AND currency." . $db->quoteName('published') . " = 1" : "");
        $query .= ($group_by ? " GROUP BY currency." . $db->quoteName('currency_id') : ""). " ORDER BY " . $db->quoteName('name') . " ASC";

        $db->setQuery($query);
        $tab = $db->loadObjectList();
        if ($object){
            foreach ($tab as $key => $currency)
                $tab[$key] = JeproshopCurrencyModelCurrency::getCurrencyInstance($currency->currency_id);
        }
        return $tab;
    }
	
	public static function getCurrencyInstance($currency_id){
		if (!isset(self::$currencies[$currency_id])){
			self::$currencies[(int)($currency_id)] = new JeproshopCurrencyModelCurrency($currency_id);
		}
		return self::$currencies[(int)($currency_id)];
	}

    /**
     * Overriding check if currency rate is not empty and if currency with the same iso code already exists.
     * If it's true, currency is not added.
     *
     * @see ObjectModelCore::add()
     */
    public function add()
    {
        if ((float)$this->conversion_rate <= 0)
            return false;

        if(JeproshopCurrencyModelCurrency::exists($this->iso_code, $this->iso_code_num)){
            return false;
        }else{
            return parent::add($autodate, $nullValues);
        }
    }

    public function update($autodate = true, $nullValues = false)
    {
        if ((float)$this->conversion_rate <= 0)
            return false;
        return parent::update($autodate, $nullValues);
    }

    /**
     * Check if a currency already exists.
     *
     * @param int|string $iso_code int for iso code number string for iso code
     * @param $iso_code_num
     * @param int $shop_id
     * @return bool
     */
    public static function exists($iso_code, $iso_code_num, $shop_id = 0)
    {
        if (is_int($iso_code))
            $currency_id = JeproshopCurrencyModelCurrency::getIdByIsoCodeNum((int)$iso_code_num, (int)$shop_id);
        else
            $currency_id = JeproshopCurrencyModelCurrency::getIdByIsoCode($iso_code, (int)$shop_id);

        if ($currency_id)
            return true;
        else
            return false;
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection))
            return false;

        $res = array();
        foreach ($selection as $currency_id)
        {
            $currency = new JeproshopCurrencyModelCurrency((int)$currency_id);
            $res[$currency_id] = $currency->delete();
        }

        foreach ($res as $value)
            if (!$value)
                return false;
        return true;
    }

    public function delete()
    {
        if ($this->currency_id == JeproshopSettingModelSetting::getValue('default_currency'))
        {
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('currency_id') . " FROM " . $db->quoteName('#__jeproshop_currency') . " WHERE " . $db->quoteName('currency_id') . " = " . (int)$this->currency_id . " AND " . $db->quoteName('deleted') . " = 0";
            $db->setQuery($query);
            $result = $db->loadObject();
            if (!$result->currency_id)
                return false;
            JeproshopSettingModelSetting::updateValue('default_currency', $result->currency_id);
        }
        $this->deleted = 1;
        return $this->update();
    }

    /**
     * Return formatted sign
     *
     * @param string $side left or right
     * @return string formatted sign
     */
    public function getSign($side = null)
    {
        if (!$side)
            return $this->sign;
        $formatted_strings = array(
            'left' => $this->sign.' ',
            'right' => ' '.$this->sign
        );

        $formats = array(
            1 => array('left' => &$formatted_strings['left'], 'right' => ''),
            2 => array('left' => '', 'right' => &$formatted_strings['right']),
            3 => array('left' => &$formatted_strings['left'], 'right' => ''),
            4 => array('left' => '', 'right' => &$formatted_strings['right']),
            5 => array('left' => '', 'right' => &$formatted_strings['right'])
        );
        if (isset($formats[$this->format][$side]))
            return ($formats[$this->format][$side]);
        return $this->sign;
    }

    public static function getPaymentCurrenciesSpecial($id_module, $id_shop = null)
    {
        if (is_null($id_shop))
            $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT *
				FROM '._DB_PREFIX_.'module_currency
				WHERE id_module = '.(int)$id_module.'
					AND id_shop ='.(int)$id_shop;
        return Db::getInstance()->getRow($sql);
    }

    public static function getPaymentCurrencies($id_module, $id_shop = null)
    {
        if (is_null($id_shop))
            $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT c.*
				FROM `'._DB_PREFIX_.'module_currency` mc
				LEFT JOIN `'._DB_PREFIX_.'currency` c ON c.`id_currency` = mc.`id_currency`
				WHERE c.`deleted` = 0
					AND mc.`id_module` = '.(int)$id_module.'
					AND c.`active` = 1
					AND mc.id_shop = '.(int)$id_shop.'
				ORDER BY c.`name` ASC';
        return Db::getInstance()->executeS($sql);
    }

    public static function checkPaymentCurrencies($id_module, $id_shop = null)
    {
        if (empty($id_module))
            return false;

        if (is_null($id_shop))
            $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'module_currency`
				WHERE `id_module` = '.(int)$id_module.'
					AND `id_shop` = '.(int)$id_shop;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }


    /**
     * @static
     * @param $iso_code
     * @param int $id_shop
     * @return int
     */
    public static function getIdByIsoCode($iso_code, $id_shop = 0)
    {
        $cache_id = 'Currency::getIdByIsoCode_'.pSQL($iso_code).'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id))
        {
            $query = Currency::getIdByQuery($id_shop);
            $query->where('iso_code = \''.pSQL($iso_code).'\'');

            $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * @static
     * @param $iso_code
     * @param int $id_shop
     * @return int
     */
    public static function getIdByIsoCodeNum($iso_code_num, $id_shop = 0)
    {
        $query = Currency::getIdByQuery($id_shop);
        $query->where('iso_code_num = \''.pSQL($iso_code_num).'\'');

        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());
    }

    /**
     * @static
     * @param int $id_shop
     * @return DbQuery
     */
    public static function getIdByQuery($id_shop = 0)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('deleted = 0');

        if (JeproshopShopModelShop::isFeatureActive() && $id_shop > 0)
        {
            $query->leftJoin('currency_shop', 'cs', 'cs.id_currency = c.id_currency');
            $query->where('id_shop = '.(int)$id_shop);
        }
        return $query;
    }

    /**
     * Refresh the currency exchange rate
     * The XML file define exchange rate for each from a default currency ($isoCodeSource).
     *
     * @param $data XML content which contains all the exchange rates
     * @param $isoCodeSource The default currency used in the XML file
     * @param $defaultCurrency The default currency object
     */
    public function refreshCurrency($data, $isoCodeSource, $defaultCurrency)
    {
        // fetch the exchange rate of the default currency
        $exchange_rate = 1;
        if ($defaultCurrency->iso_code != $isoCodeSource)
        {
            foreach ($data->currency as $currency)
                if ($currency['iso_code'] == $defaultCurrency->iso_code)
                {
                    $exchange_rate = round((float)$currency['rate'], 6);
                    break;
                }
        }

        if ($defaultCurrency->iso_code == $this->iso_code)
            $this->conversion_rate = 1;
        else
        {
            if ($this->iso_code == $isoCodeSource)
                $rate = 1;
            else
            {
                foreach ($data->currency as $obj)
                    if ($this->iso_code == strval($obj['iso_code']))
                    {
                        $rate = (float)$obj['rate'];
                        break;
                    }
            }

            if (isset($rate))
                $this->conversion_rate = round($rate / $exchange_rate, 6);
        }
        $this->update();
    }

    public static function getDefaultCurrency()
    {
        $id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        if ($id_currency == 0)
            return false;

        return new Currency($id_currency);
    }

    public static function refreshCurrencies()
    {
        // Parse
        if (!$feed = Tools::simplexml_load_file('http://api.prestashop.com/xml/currencies.xml'))
            return Tools::displayError('Cannot parse feed.');

        // Default feed currency (EUR)
        $isoCodeSource = strval($feed->source['iso_code']);

        if (!$default_currency = Currency::getDefaultCurrency())
            return Tools::displayError('No default currency');

        $currencies = Currency::getCurrencies(true, false);
        foreach ($currencies as $currency)
            if ($currency->id != $default_currency->id)
                $currency->refreshCurrency($feed->list, $isoCodeSource, $default_currency);
    }

    /**
     * Get current currency
     *
     * @deprecated as of 1.5 use $context->currency instead
     * @return Currency
     */
    public static function getCurrent()
    {
        Tools::displayAsDeprecated();
        return Context::getContext()->currency;
    }

    public static function countActiveCurrencies($id_shop = null)
    {
        if ($id_shop === null)
            $id_shop = (int)Context::getContext()->shop->id;

        if (!isset(self::$countActiveCurrencies[$id_shop]))
            self::$countActiveCurrencies[$id_shop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT c.id_currency) FROM `'._DB_PREFIX_.'currency` c
				LEFT JOIN '._DB_PREFIX_.'currency_shop cs ON (cs.id_currency = c.id_currency AND cs.id_shop = '.(int)$id_shop.')
				WHERE c.`active` = 1
			');
        return self::$countActiveCurrencies[$id_shop];
    }

    public static function isMultiCurrencyActivated($id_shop = null)
    {
        return (Currency::countActiveCurrencies($id_shop) > 1);
    }
}