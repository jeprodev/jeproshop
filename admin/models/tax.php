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

class JeproshopTaxModelTax extends JModelLegacy
{
    public $tax_id;

    public $lang_id;

	public $name;

	public $rate;

	public $published;

	public $deleted = 0;


    protected static $_product_country_tax = array();
    protected static $_product_tax_via_rules = array();

    public function __construct($tax_id = null, $lang_id = null){
        $db = JFactory::getDBO();

        if($lang_id !== NULL){
            $this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang'));
        }

        if($tax_id){
            $cache_id = 'jeproshop_tax_model_' . $tax_id . '_' . $lang_id;

            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax') . " AS tax ";

                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_tax_lang') . " AS tax_lang ON (tax." . $db->quoteName('tax_id') . " = tax_lang." . $db->quoteName('lang_id') . " AND tax_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") ";
                }
                $query .= " WHERE tax." . $db->quoteName('tax_id') . " = " . (int)$tax_id;

                $db->setQuery($query);
                $tax_data = $db->loadObject();
                if($tax_data){
                    if(!$lang_id){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax_lang') . " WHERE " . $db->quoteName('tax_id') . " = " . (int)$tax_id;

                        $db->setQuery($query);
                        $tax_lang_data = $db->loadObjectList();
                        foreach($tax_lang_data as $row){
                            foreach($row as $key => $value){
                                if(array_key_exists($key, $this) && $key != 'tax_id'){
                                    if(!isset($tax_data->{$key}) || !is_array($tax_data->{$key})){
                                        $tax_data->{$key} = array();
                                    }
                                    $tax_data->{$key}[$row->lang_id] = $value;
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $tax_data);
                }
            }else{
                $tax_data = JeproshopCache::retrieve($cache_id);
            }

            if($tax_data){
                $tax_data->tax_id = $tax_id;
                foreach($tax_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

    public function saveTax(){
        $db = JFactory::getDBO();

        $result = true;
        $languages = JeproshopLanguageModelLanguage::getLanguages(false);

        $input = JRequest::get('post');
        $input_data = $input['jform'];
        if(isset($input_data['published'])){
            $published = 1;
            $deleted = 0;
        }else{
            $published = 0;
            $deleted = 1;
        }

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax') . " (" . $db->quoteName('rate') . ", " . $db->quoteName('published') . ", " . $db->quoteName('deleted');
        $query .= ") VALUES ( " . (float)$input_data['tax_rate'] . ", " . (int)$published . ", " . (int)$deleted . ") ";

        $db->setQuery($query);

        if($db->query()){
            $this->tax_id = $db->insertid();

            foreach($languages as $language){
                $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_lang') . " (" . $db->quoteName('tax_id') . ", " . $db->quoteName('lang_id') . ", " . $db->quoteName('name');
                $query .= ") VALUES (" . (int)$this->tax_id . ", " . (int)$language->lang_id . ", " . $db->quote($input_data['name_' . $language->lang_id]) . ") ";

                $db->setQuery($query);
                $result &= $db->query();
            }
        }
        return $result;
    }

    public function delete(){
        /* Clean associations */
        JeproshopTaxRuleModelTaxRule::deleteTaxRuleByTaxId((int)$this->tax_id);

        if ($this->isUsed())
            return $this->historize();
        else
            return parent::delete();
    }

    /**
     * Save the object with the field deleted to true
     *
     *  @return bool
     */
    public function historize(){
        $this->deleted = true;
        return $this->updateTax();
    }

    public function toggleStatus(){
        if (parent::toggleStatus())
            return $this->onStatusChange();

        return false;
    }

    public function update($nullValues = false){
        if (!$this->deleted && $this->isUsed()){
            $historized_tax = new JeproshopTaxModelTax($this->tax_id);
            $historized_tax->historize();

            // remove the id in order to create a new object
            $this->tax_id = 0;
            $res = $this->add();

            // change tax id in the tax rule table
            $res &= JeproshopTaxRuleModelTaxRule::swapTaxId($historized_tax->tax_id, $this->tax_id);
            return $res;
        }
        elseif ($this->updateTax())
            return $this->onStatusChange();

        return false;
    }

    public function updateTax(){
        $db = JFactory::getDBO();

        $this->clearCache();

        $result = true;
        $languages = JeproshopLanguageModelLanguage::getLanguages(false);

        $input = JRequest::get('post');
        $input_data = $input['jform'];

        $published = $input_data['published'];
        $deleted = $published ? 0 : 1;

        $query = "UPDATE " . $db->quoteName('#__jeproshop_tax') . " SET " . $db->quoteName('rate') . " = " . (float)$input_data['tax_rate'] . ", " . $db->quoteName('published') . " = " . (int)$published;
        $query .= ", " . $db->quoteName('deleted') . " = " . (int)$deleted . " WHERE " . $db->quoteName('tax_id') ." = " . (int)$this->tax_id;

        $db->setQuery($query);
        $result &= $db->query();

        foreach($languages as $language) {
            $where = $db->quoteName('tax_id') . " = " . (int)$this->tax_id . " AND " . $db->quoteName('lang_id') . " = " . (int)$language->lang_id;
            $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_tax_lang') . " WHERE " . $where;
            $db->setQuery($query);
            if ($db->loadResult()) {
                $query = "UPDATE " . $db->quoteName('#__jeproshop_tax_lang') . " SET " . $db->quoteName('name') . " = " . $db->quote($input_data['name_' . $language->lang_id]) . " WHERE " . $where;
                $db->setQuery($query);
                $result &= $db->query();
            }else{
                $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_lang') . "(" . $db->quoteName('name') . ") VALUE (" . $db->quote($input_data['name_' . $language->lang_id]) . ")";
                $db->setQuery($query);
                $result &= $db->query();
            }
        }
        return $result;
    }

    /**
     * Returns true if the tax is used in an order details
     *
     * @return bool
     */
    public function isUsed(){
        $db = JFactory::betDBo();

        $query  = "SELECT " . $db->quoteName('tax_id') . " FROM " . $db->quoteName('#__jeproshop_order_detail_tax') . " WHERE " . $db->quoteName('tax_id') . " = " . (int)$this->tax_id;

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Get all available taxes
     *
     * @param bool $lang_id
     * @param bool $published_only
     * @return array Taxes
     */
    public static function getTaxes($lang_id = false, $published_only = true){
        $db = JFactory::getDBO();
        $select = "SELECT tax." . $db->quoteName('tax_id') . ", tax." . $db->quoteName('rate');
        $from = " FROM " . $db->quoteName('#__jeproshop_tax') . " AS tax";
        $where = " WHERE tax." . $db->quoteName('deleted') . " != 1";

        $orderBy = " ";
        if ($lang_id){
            $select .= ", tax_lang." . $db->quoteName('name') . ", tax_lang." . $db->quoteName('lang_id');
            $from .= " LEFT JOIN " . $db->quoteName('#__jeproshop_tax_lang') . " AS tax_lang ON (tax." . $db->quoteName('tax_id') . " = tax_lang." . $db->quoteName('tax_id') . " AND tax_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") ";
            $orderBy = " ORDER BY " . $db->quoteName('name') . " ASC";
        }

        if ($published_only) {
            $where .= " AND tax." . $db->quoteName('published') . " = 1";
        }

        $db->setQuery($select . $from . $where . $orderBy);
        return $db->loadObjectList();
    }

    /**
     * @param JeproshopContext $context
     * @return mixed
     */
    public function getTaxList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS tax." . $db->quoteName('tax_id') . ", tax_lang." . $db->quoteName('name') . ", tax." . $db->quoteName('rate') . ", tax." . $db->quoteName('published') . " FROM " . $db->quoteName('#__jeproshop_tax');
            $query .= " AS tax LEFT JOIN " . $db->quoteName('#__jeproshop_tax_lang') . " AS tax_lang ON (tax." . $db->quoteName('tax_id') . " = tax_lang." . $db->quoteName('tax_id') . " AND tax_lang." . $db->quoteName('lang_id') . " = ";
            $query .= (int)$lang_id . ") ";
            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $taxes = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($taxes));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $taxes;
    }

    /**
     * Return the tax id associated to the specified name
     *
     * @param string $tax_name
     * @param bool|int $published (true by default)
     * @return bool|int
     */
    public static function getTaxIdByName($tax_name, $published = 1) {
        $db = JFactory::getDBO();

        $query = "SELECT tax." . $db->quoteName('tax_id') . " FROM " . $db->quoteName('#__jeproshop_tax') . " AS tax LEFT JOIN " . $db->quoteName('#__jeproshop_tax_lang') . " AS tax_lang ON (tax.";
        $query .= $db->quoteName('tax_id') . " = tax_lang." . $db->quoteName('tax_id') . " WHERE tax_lang." . $db->quoteName('name') . " = " . $db->quote($tax_name) . ( $published ? " AND tax." . $db->quoteName('published') : 1);

        $db->setQuery($query);
        $tax = $db->loadObject();

        return $tax ? (int)($tax->tax_id) : false;
    }

	public static function taxExcludedOption(){
		static $use_tax = null;
		if($use_tax === NULL){
			$use_tax = JeproshopSettingModelSetting::getValue('use_tax');
		}
		return !$use_tax;
	}

	/**
	 * Returns the ecotax tax rate
	 *
	 * @param int $address_id
	 * @return float tax rate
	 */
	public static function getProductEcotaxRate($address_id = NULL){
		$address = new JeproshopAddressModelAddress($address_id);

		$taxManager = JeproshopTaxManagerFactory::getManager($address, (int)JeproshopSettingModelSetting::getValue('ecotax_tax_rules_group_id'));
		$taxCalculator = $taxManager->getTaxCalculator();

		return $taxCalculator->getTotalRate();
	}

    /**
     * Returns the carrier tax rate
     *
     * @param $carrier_id
     * @param $address_id
     * @return float $tax_rate
     */
    public static function getCarrierTaxRate($carrier_id, $address_id = null){
        $address = JeproshopAddressModelAddress::initialize($address_id);
        $tax_rules_id = (int)JeproshopCarrierModelCarrier::getTaxRulesGroupIdByCarrierId((int)$carrier_id);

        $tax_manager = JeproshopTaxManagerFactory::getManager($address, $tax_rules_id);
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }

    /**
     * Return the product tax rate using the tax rules system
     *
     * @param integer $product_id
     * @param integer $country_id
     * @param $state_id
     * @param $zipcode
     * @return Tax
     * @deprecated since 1.5
     */
    public static function getProductTaxRateViaRules($product_id, $country_id, $state_id, $zipcode){
        Tools::displayAsDeprecated();

        if (!isset(self::$_product_tax_via_rules[$product_id . '-' . $country_id . '_' . $state_id . '_' . $zipcode])){
            $tax_rate = JeproshopTaxRulesGroupModelTaxRulesGroup::getTaxesRate((int)JeproshopProductModelProduct::getTaxRulesGroupIdByProductId((int)$product_id), (int)$country_id, (int)$state_id, $zipcode);
            self::$_product_tax_via_rules[$product_id .'_'. $country_id .'_' . $zipcode] = $tax_rate;
        }

        return self::$_product_tax_via_rules[$product_id .'_'.$country_id .'_'.$zipcode];
    }

    /**
     * Returns the product tax
     *
     * @param integer $product_id
     * @param integer $address_id
     * @param JeproshopContext $context
     * @return JeproshopTaxModelTax
     */
    public static function getProductTaxRate($product_id, $address_id = null, JeproshopContext $context = null){
        if ($context == null)
            $context = JeproshopContext::getContext();

        $address = JeproshopAddressModelAddress::initialize($address_id);
        $tax_rules_id = (int)JeproshopProductModelProduct::getTaxRulesGroupIdByProductId($product_id, $context);

        $tax_manager = JeproshopTaxManagerFactory::getManager($address, $tax_rules_id);
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }

    protected function onStatusChange(){
        if(!$this->published){
            return JeproshopTaxRuleModelTaxRule::deleteTaxRuleByTaxId($this->tax_id);
        }
        return true;
    }

    private function clearCache($all = FALSE){
        if($all){
            JeproshopCache::clean('jeproshop_tax_model_*');
        }elseif($this->tax_id){
            JeproshopCache::clean('jeproshop_tax_model_' . $this->tax_id . '_*');
        }
    }
}

class JeproshopTaxRuleModelTaxRule extends JModelLegacy
{
    public $tax_rule_id;
    public $tax_rules_group_id;
    public $country_id;
    public $state_id;
    public $zipcode_from;
    public $zipcode_to;
    public $tax_id;
    public $behavior;
    public $description;

    private $pagination;

    public function __construct($tax_rule_id = null){
        $db = JFactory::getDBO();
        if($tax_rule_id){
            $cache_id = 'jeproshop_tax_rule_model_' . $tax_rule_id;
            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax_rule') . " AS tax_rule ";
                $query .= " WHERE " . $db->quoteName('tax_rule_id') . " = " . (int)$tax_rule_id;

                $db->setQuery($query);
                $taxRuleData = $db->loadObject();
            }else{
                $taxRuleData = JeproshopCache::retrieve($cache_id);
            }

            if($taxRuleData){
                foreach($taxRuleData as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
                $this->tax_rule_id = $tax_rule_id;
            }
        }
    }

    public function getTaxRuleList(JeproshopContext $context = null){
        return JeproshopTaxRuleModelTaxRule::getStaticTaxRulesList(0, $context);
    }

    /**
     * Replace a tax_rule id by an other one in the tax_rule table
     *
     * @param int $old_id
     * @param int $new_id
     */
    public static function swapTaxId($old_id, $new_id){
        $db = JFactory::getDBO();

        $query = "UPDATE " . $db->quoteName('#__jeproshop_tax_rule') . " SET " . $db->quoteName('tax_i') . " = ". (int)$new_id . " WHERE " . $db->quoteName('tax_id') . " = " . (int)$old_id;

        $db->setQuery($query);
        return $db->query();
    }

    /**
     * @param string $zip_codes a range of zipcode (eg: 75000 / 75000-75015)
     * @return array an array containing two zipcode ordered by zipcode
     */
    public function breakDownZipCode($zip_codes) {
        $zip_codes = preg_split('/-/', $zip_codes);

        $from = $zip_codes[0];
        $to = isset($zip_codes[1]) ? $zip_codes[1]: 0;
        if (count($zip_codes) == 2){
            $from = $zip_codes[0];
            $to   = $zip_codes[1];
            if ($zip_codes[0] > $zip_codes[1]){
                $from = $zip_codes[1];
                $to   = $zip_codes[0];
            }else if ($zip_codes[0] == $zip_codes[1]){
                $from = $zip_codes[0];
                $to   = 0;
            }
        }else if (count($zip_codes) == 1) {
            $from = $zip_codes[0];
            $to = 0;
        }

        return array($from, $to);
    }

    /**
     * @param int $tax_id
     * @return boolean
     */
    public static function isTaxInUse($tax_id){
        $cache_id = 'jeproshop_tax_rule_model_is_tax_in_use_' . (int)$tax_id;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteName('tax_id') . " = " . (int)$tax_id;
            $db->setQuery($query);
            $result = (int)$db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    public static function deleteTaxRuleByTaxId($tax_id) {
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteNaame('tax_id') . " = " . (int)$tax_id;
        $db->setQuery($query);
        return $db->query();
    }

    public static function deleteByGroupId($group_id){
        if (empty($group_id))
            die(JError::raiseError());

        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteName('tax_rules_group_id') . " = " . (int)$group_id;

        $db->setQuery($query);
        return $db->query();
    }

    public static function retrieveById($tax_rule_id){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteName('tax_rule_id') . " = " . (int)$tax_rule_id;

        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getTaxRulesByGroupId($lang_id, $tax_rules_group_id){
        $db = JFactory::getDBO();

        $query = "SELECT tax_rule." . $db->quoteName('tax_rule_id') . ", country." .  $db->quoteName('name') . " AS country_name, state." . $db->quoteName('name') . " AS state_name, tax." . $db->quoteName('rate');
        $query .= ", tax_rule." . $db->quoteName('zipcode_from') . ", tax_rule." . $db->quoteName('zipcode_to') . ", tax_rule." . $db->quoteName('description') . ", tax_rule." . $db->quoteName('behavior') . ", tax_rule.";
        $query .= $db->quoteName('country_id') . ", tax_rule." . $db->quoteName('state_id') . " FROM " . $db->quoteName('#__jeproshop_tax_rule') . " AS tax_rule LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang');
        $query .= " AS country ON (tax_rule." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") LEFT JOIN ";
        $query .= $db->quoteName('#__jeproshop_state') . " AS state ON (tax_rule." . $db->quoteName('state_id') . " = state." . $db->quoteName('state_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_tax') . " AS tax ";
        $query .= " ON (tax_rule." . $db->quoteName('tax_id') . " = tax." .  $db->quoteName('tax_id') . ") WHERE " . $db->quoteName('tax_rules_group_id') . " = " . (int)$tax_rules_group_id . " ORDER BY " . $db->quoteName('country_name');
        $query .= " ASC, " . $db->quoteName('state_name') . " ASC, " . $db->quoteName('zipcode_from') . " ASC, " . $db->quoteName('zipcode_to') . " ASC";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getStaticTaxRulesList($tax_rules_group_id = 0, JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!isset($context)){
            $context = JeproshopContext::getContext();
        }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option . $view . '.lang_id', 'lang_id', $context->language->lang_id, 'int');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS  country_lang." . $db->quoteName('name') . " AS country_name, state." . $db->quoteName('name') . " AS state_name, tax_rule." . $db->quoteName('behavior') . ", tax_rule." . $db->quoteName('tax_rule_id') . ", tax_rule.";
            $query .= $db->quoteName('description') . ", CONCAT_WS(' - ', tax_rule." . $db->quoteName('zipcode_from') . ", tax_rule." . $db->quoteName('zipcode_to') . ") AS zipcode, tax." . $db->quoteName('rate') . ", tax_rule." . $db->quoteName('country_id') . " FROM ";
            $query .= $db->quoteName('#__jeproshop_tax_rule') . " AS tax_rule LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (tax_rule." . $db->quoteName('country_id') . " = country_lang." . $db->quoteName('country_id') . " AND country_lang.";
            $query .= $db->quoteName('lang_id') . " = " . (int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_state') . " AS state ON(tax_rule." . $db->quoteName('state_id') . " = state." . $db->quoteName('state_id');
            $query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_tax') . " AS tax ON(tax_rule." . $db->quoteName('tax_id') . " = tax." . $db->quoteName('tax_id') . ") WHERE 1 " . (JeproshopTools::isUnsignedInt($tax_rules_group_id) ?" AND " . $db->quoteName('tax_rules_group_id') . " = " . (int)$tax_rules_group_id : "");
            $query .= " ORDER BY tax_rule." . $db->quoteName('tax_rule_id') . " ASC ";


            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $tax_rules = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($tax_rules));

        return $tax_rules;
    }

    public function add(){
        $db = JFactory::getDBO();
        $context = JeproshopContext::getContext();
        $input = JRequest::get('post');
        $inputData = $input['jform'];
        $selectedCountries = array();
        $app = JFactory::getApplication();

        $taxId = isset($inputData['tax_id']) ? $inputData['tax_id']: $app->input->get('tax_id', 0);
        $taxRuleId = isset($inputData['tax_rule_id']) ? $inputData['tax_rule_id']: $app->input->get('tax_rule_id', 0);

        $zipCode = $inputData['zipcode'];
        $behavior = $inputData['behavior'];
        $description = $inputData['description'];

        $country_id = $inputData['country_id'];
        if($country_id == 0){
            $countries = JeproshopCountryModelCountry::getStaticCountries($context->language->lang_id);

            foreach($countries as $country){
                $selectedCountries[] = $country->country_id;
            }
        }else{
            $selectedCountries[] = $country_id;
        }

        $selectedStates = array();
        $state_id = $inputData['state_id'];
        if($state_id != 0){
            $selectedStates[] = $state_id;
        }else{
            $selectedStates[] = 0;
        }

        $taxRulesGroupId = (int)$app->input->get('tax_rules_group_id'); echo $taxRulesGroupId;
        $taxRuleGroup = new JeproshopTaxRulesGroupModelTaxRulesGroup($taxRulesGroupId);
        foreach($selectedCountries as $country_id){
            $first = true;
            foreach($selectedStates as $state_id){
                if($taxRuleGroup->hasUniqueTaxRuleForCountry($country_id, $state_id, $taxRuleId)){
                    JError::raiseWarning(500, JText::_('COM_JEPROSHOP_A_TAX_ALREADY_EXISTS_FOR_THIS_COUNTRY_STATE_WITH_TAX_ONLY_BEHAVIOR_LABEL'));
                    continue;
                }
                $taxRule = new JeproshopTaxRuleModelTaxRule();
                // update or creation?
                if(isset($taxRuleId) && $first){
                    $taxRule->tax_rule_id = $taxRuleId;
                    $first = false;
                }
                $taxRule->tax_id = $taxId;
                $taxRule->tax_rules_group_id = (int)$taxRulesGroupId;
                $taxRule->country_id = $country_id;
                $taxRule->state_id = (int)$state_id;

                list($taxRule->zipcode_from, $taxRule->zipcode_to) = $taxRule->breakDownZipCode($zipCode);

                $country = new JeproshopCountryModelCountry((int)$country_id, (int)$context->language->lang_id);

                if($zipCode && $country->need_zip_code){
                    if($country->zip_code_format){
                        foreach(array($taxRule->zipcode_from, $taxRule->zipcode_to) as $zip_code){
                            if($zip_code){
                                if(!$country->checkZipCode($zip_code)){
                                    JError::raiseError(500, JText::_('COM_JEPROSHOP_THE_ZIP_POSTAL_CODE_IS_INVALID_AND_MUST_BE_TYPED_AS_FOLLOWS_MESSAGE') . JText::_('COM_JEPROSHOP_FOR_LABEL'));
                                }
                            }
                        }
                    }
                }

                $taxRule->behavior = $behavior;
                $taxRule->description = $description;

                $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_rule') . "(" . $db->quoteName('tax_rules_group_id') . ", " . $db->quoteName('country_id') . ", " . $db->quoteName('state_id') . ", " . $db->quoteName('zipcode_from') . ", ";
                $query .= $db->quoteName('zipcode_to') . ", " . $db->quoteName('tax_id') . ", " . $db->quoteName('behavior') . ", " . $db->quoteName('description') . ") VALUES (" . (int)$taxRulesGroupId . ", " . (int)$country_id . ", " . (int)$state_id;
                $query .= ", " . $db->quote($taxRule->zipcode_from) . ", " . $db->quote($taxRule->zipcode_to) . ", " . (int)$taxId . ", " . (int)$behavior . ", " . $db->quote($description) . ")";

                $db->setQuery($query);
                $link = 'index.php?option=com_jeproshop&view=tax&task=';

                if($db->query()){
                    $link .= 'edit_rules_group&tax_rules_group_id=' . (int)$taxRulesGroupId . '&' . JeproshopTools::getTaxToken() . '=1';
                }else{
                    $link .= '';
                }
                $app->redirect($link);

            }
        }
    }
}

/****** --------- TAX RULES GROUP -------- *****/
class JeproshopTaxRulesGroupModelTaxRulesGroup extends JModelLegacy
{
    public $tax_rules_group_id;

	public $name;

	public $published;

    public $shop_id;

    protected $shop_list_ids;

    protected $default_shop_id;

    private $pagination;

	protected static $_taxes = array();

    public function __construct($tax_rule_group_id = null){
        $db = JFactory::getDBO();
        if($tax_rule_group_id){
            $cache_id = 'jeproshop_tax_rule_group_model_' . $tax_rule_group_id;
            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax_rules_group') . " AS tax_rules_group ";
                /** Get Shop information **/
                if(JeproshopShopModelShop::isTableAssociated('tax_rules_group')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_tax_rules_group_shop') . " AS tax_rules_group_shop ON ( tax_rules_group.";
                    $query .= "tax_rules_group_id = tax_rules_group_shop.tax_rules_group_id AND tax_rules_group_shop.shop_id = " . (int)$this->shop_id . ")";
                }
                $query .= " WHERE tax_rules_group." . $db->quoteName('tax_rules_group_id') . " = " . (int)$tax_rule_group_id;

                $db->setQuery($query);
                $taxRuleGroupData = $db->loadObject();
            }else{
                $taxRuleGroupData = JeproshopCache::retrieve($cache_id);
            }

            if($taxRuleGroupData){
                foreach($taxRuleGroupData as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
                $this->tax_rules_group_id = $tax_rule_group_id;
            }
        }
    }
	
	public static function getTaxRulesGroups($published = TRUE){
		$db = JFactory::getDBO();
	
		$query = "SELECT DISTINCT tax_rules_group." . $db->quoteName('tax_rules_group_id') . ", tax_rules_group.name, tax_rules_group.published FROM ";
		$query .= $db->quoteName('#__jeproshop_tax_rules_group') . " AS tax_rules_group ". JeproshopShopModelShop::addSqlAssociation('tax_rules_group');
		$query .= ($published ?  " WHERE tax_rules_group." . $db->quoteName('published') . " =  1" : "") . " ORDER BY name ASC";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getAssociatedTaxRatesByCountryId($country_id){
		$db = JFactory::getDBO();
	
		$query = "SELECT tax_rules_group." . $db->quoteName('tax_rules_group_id') . ", tax." . $db->quoteName('rate') . " FROM ";
		$query .= $db->quoteName('#__jeproshop_tax_rules_group') . " AS tax_rules_group LEFT JOIN " . $db->quoteName('#__jeproshop_tax_rule');
		$query .= " AS tax_rule ON(tax_rule." . $db->quoteName('tax_rules_group_id') . " = tax_rules_group." . $db->quoteName('tax_rules_group_id');
		$query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_tax') . " AS tax ON(tax." . $db->quoteName('tax_id') . " = tax_rule.";
		$query .= $db->quoteName('tax_id') . ") WHERE tax_rule." . $db->quoteName('country_id') . " = " . (int)$country_id . " AND tax_rule.";
		$query .= $db->quoteName('state_id') . " = 0 AND 0 between " . $db->quoteName('zipcode_from') . " AND " . $db->quoteName('zipcode_to');
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    /**
     * Returns the tax rules group id corresponding to the name
     *
     * @param string name
     * @return int id of the tax rules
     */
    public static function getIdByName($name) {
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('tax_rules_group_id') . " FROM " . $db->quoteName('#__jeproshop_tax_rules_group') . " AS tax_rules_group WHERE " . $db->quoteName('name') . " = " . $db->quote($name);

        $db->setQuery($query);
        return $db->loadResult();
    }

    public function getTaxRulesGroupsList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if (!$context) {
            $context = JeproshopContext::getContext();
        }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option . $view . '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option . $view . '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option . $view . '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do {
            $query = "SELECT SQL_CALC_FOUND_ROWS tax_rules_group." . $db->quoteName('tax_rules_group_id') . ", tax_rules_group." . $db->quoteName('name');
            $query .= ", tax_rules_group." . $db->quoteName('published') . " FROM " . $db->quoteName('#__jeproshop_tax_rules_group') . " AS tax_rules_group";

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $tax_rules_groups = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        } while (empty($tax_rules_groups));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $tax_rules_groups;
    }
/*
    public function getTaxRuleList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if (!$context) {
            $context = JeproshopContext::getContext();
        }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option . $view . '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option . $view . '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option . $view . '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do {
            $query = "SELECT SQL_CALC_FOUND_ROWS tax_rule." . $db->quoteName('behavior') . ", tax_rule." . $db->quoteName('description') . ",country_lang." . $db->quoteName('name') . " AS country_name, state." . $db->quoteName('name');
            $query .= " AS state_name, CONCAT_WS(' - ', tax_rule." . $db->quoteName('zipcode_from') . " AS zipcode, tax_rule." . $db->quoteName('zipcode_to') . ") AS zipcode, tax." . $db->quoteName('rate') . " FROM ";
            $query .= $db->quoteName('#__jeproshop_tax_rule') . " AS tax_rule LEFT JOIN " . $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (tax_rule." . $db->quoteName('country_id') . " = country_lang.";
            $query .= $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_state') . " AS state ON(tax_rule." . $db->quoteName('state_id');
            $query .= " = state." . $db->quoteName('state_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_tax') . " AS tax ON(tax_rule." . $db->quoteName('tax_id') . " = tax." . $db->quoteName('tax_id') . ") WHERE " . $db->quoteName('tax_rules_group_id') . " = " . (int)$tax_rules_group_id;
        } while (empty($tax_rules));
    }*/

    /**
     * @return array an array of tax rules group formatted as $id => $name
     */
    public static function getTaxRulesGroupsForOptions()
    {
        $tax_rules[] = array('id_tax_rules_group' => 0, 'name' => JError::raiseError('No tax'));
        return array_merge($tax_rules, JeproshopTaxRulesGroupModelTaxRulesGroup::getTaxRulesGroups());
    }

    public function save(){
        $db = JFactory::getDBO();
        $input = JRequest::get('post');
        $inputData = $input['jform'];

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_rules_group') . " ( " . $db->quoteName('name') . ", " . $db->quoteName('published') . ") VALUES(" . $db->quote($inputData['name']) . ", " . (int)$inputData['published'] . ")";
        $db->setQuery($query);
        if($db->query()){
            $result = true;
            $taxRuleGroupId = $db->insertid();
            $shopListIds = JeproshopShopModelShop::getContextListShopIds();
            if(count($this->shop_list_ids) > 0){
                $shopListIds = $this->shop_list_ids;
            }

            if(JeproshopShopModelShop::checkDefaultShopId('tax_rules_group')){
                $this->default_shop_id = min($shopListIds);
            }
            foreach ($shopListIds as $shop_id) {
                $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_rules_group_shop') . " ( " . $db->quoteName('tax_rules_group_id') . ", " . $db->quoteName('shop_id') . ") VALUES ( " . (int)$taxRuleGroupId . ", " . (int)$shop_id .  ")";
                $db->setQuery($query);
                $result &= $db->query();
            }
            $link = 'index.php?option=com_jeproshop&view=tax&task=groups';
            if(!$result){
                $message = JText::_('COM_JEPROSHOP_');
            }else{

            }
        }
    }

    public function update(){
        $db = JFactory::getDBO();
        $context = JeproshopContext::getContext();
        $input = JRequest::get('post');
        $inputData = $input['jform'];

        $query = "UPDATE " . $db->quoteName('#__jeproshop_tax_rules_group') . " SET " . $db->quoteName('name') . " = " . $db->quote($inputData['name']) . ", " . $db->quoteName('published') . " = ";
        $query .= (int)$inputData['published'] . " WHERE " . $db->quoteName('tax_rules_group_id') . " = " . $this->tax_rules_group_id;
        $db->setQuery($query);

        if($db->query()){
            //Update Associated Shops
            if(JeproshopShopModelShop::isFeaturePublished()) {
                $associatedShop = $this->getAssociatedShop();
                $query = "SELECT " . $db->quoteName('shop_id') . " FROM " . $db->quoteName('#__jeproshop_shop');
                $db->setQuery($query);
                $shopIds = $db->loadObjctList();
                foreach($shopIds as $shop){
                    if($context->employee->hasAuthorutyOnShop($shop->shop_id)){
                        $associatedShop[] = $shop->shop_id;
                    }
                }
                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_tax_rules_group_shop') . " WHERE " . $db->quoteName('tax_rules_group_id') . " = " . (int)$this->tax_rules_group_id;
                $query .= ($associatedShop ? " AND " . $db->quoteName('shop_id') . " NOT IN (" . implode(',', $associatedShop) . ")" : "");
                $db->setQuery($query);
                $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tax_rules_group_shop') . "(" . $db->quoteName('tax_rules_group_id') . ", " . $db->quoteName('shop_id') . ") VALUES (" ;
                foreach($associatedShop as $shop_id){
                    $query .= "(" . (int)$this->tax_rules_group_id . ", " . (int)$shop_id . "), ";
                }
                $query = rtrim($query, ", ") . ")";
                $db->setQuery($query);
                $db->query();
            }
        }
    }

    public function delete()
    {
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteName('tax_rules_group_id') . " = " . $this->tax_rules_group_id;
        $db->setQuery($query);
        $res = $db->query();
        return (parent::deleste() && $res);
    }

    public function hasUniqueTaxRuleForCountry($country_id, $state_id, $tax_rule_id = false)
    {
        $rules = JeproshopTaxRuleModelTaxRule::getTaxRulesByGroupId((int)JeproshopContext::getContext()->language->lang_id, (int)$this->tax_rules_group_id);
        foreach ($rules as $rule) {
            if ($rule->country_id == $country_id && $state_id == $rule->state_id && !$rule->behavior && (int)$tax_rule_id != $rule->tax_rule_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @deprecated since 1.5
     * @param $tax_rules_group_id
     * @param $country_id
     * @param $state_id
     * @param $zipcode
     * @return float|int
     */
    public static function getTaxesRate($tax_rules_group_id, $country_id, $state_id, $zipcode)
    {
        Tools::displayAsDeprecated();
        $rate = 0;
        foreach (TaxRulesGroup::getTaxes($tax_rules_group_id, $country_id, $state_id, $zipcode) as $tax)
            $rate += (float)$tax->rate;

        return $rate;
    }

    /**
     * Return taxes associated to this para
     * @deprecated since 1.5
     */
    public static function getTaxes($tax_rules_group_id, $country_id, $state_id, $county_id)
    {
        Tools::displayAsDeprecated();
        return array();
    }
}


/**** ----------------- TAX MANAGER FACTORY ---------------- *****/
class JeproshopTaxManagerFactory
{
	protected static $cache_tax_manager;

	/**
	 *
	 * @param JeproshopAddressModelAddress $address
	 * @param int $type
	 */
	public static function getManager(JeproshopAddressModelAddress $address, $type){
		$cache_id = JeproshopTaxManagerFactory::getCacheKey($address);
		if(!isset(JeproshopTaxManagerFactory::$cache_tax_manager[$cache_id])){
			$tax_manager = JeproshopTaxManagerFactory::getTaxManager($address, $type);
			if(!($tax_manager instanceof JeproshopTaxManagerInterface)){
				$tax_manager = new JeproshopTaxRulesTaxManager($address, $type);
			}
			JeproshopTaxManagerFactory::$cache_tax_manager[$cache_id] = $tax_manager;
		}
		return JeproshopTaxManagerFactory::$cache_tax_manager[$cache_id];
	}

	public static function getTaxManager(JeproshopAddressModelAddress $address, $type){
		return NULL;
	}

    /**
     *
     * Create a unique identifier for the address
     * @param JeproshopAddressModelAddress $address
     * @return string
     * @internal param $Address
     */
	protected static function getCacheKey(JeproshopAddressModelAddress $address){
		return $address->country_id . '_' . (int)$address->state_id . '_' . $address->postcode . '_'
				. $address->vat_number . '_' . $address->dni;
	}
}

/***** --------- TAX RULES TAX MANAGER ----------- *******/
class JeproshopTaxRulesTaxManager implements JeproshopTaxManagerInterface
{
	public $address;

	public $type;

	public $tax_calculator;
	
	/**
	 *
	 * @param JeproshopAddressModelAddress $address
	 * @param mixed An additional parameter for the tax manager (ex: tax rules id for JeproshopTaxRuleTaxManager)
	 */
	public function __construct(JeproshopAddressModelAddress $address, $type) {
		$this->address = $address;
		$this->type = $type;
	}

    /**
     * Returns true if this tax manager is available for this address
     *
     * @param JeproshopAddressModelAddress $address
     * @return boolean
     */
	public static function isAvailableForThisAddress(JeproshopAddressModelAddress $address){
		return true; // default manager, available for all addresses
	}
	
	/**
	 * Return the tax calculator associated to this address
	 *
	 * @return TaxCalculator
	 */
	public function getTaxCalculator(){
		static $tax_enabled = null;
		if (isset($this->tax_calculator)){
			return $this->tax_calculator;
		}
	
		if ($tax_enabled === null){
			$tax_enabled = JeproshopSettingModelSetting::getValue('use_tax');
		}
	
		if (!$tax_enabled){
			return new JeproshopTaxCalculator(array());
		}
	
		$taxes = array();
		$postcode = 0;
		if (!empty($this->address->postcode)){
			$postcode = $this->address->postcode;
		}
	
		$cache_id = (int)$this->address->country_id . '_' . (int)$this->address->state_id .'_' . $postcode . '_' . (int)$this->type;
		if (!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
	
			$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tax_rule') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$this->address->country_id;
			$query .= " AND " . $db->quoteName('tax_rules_group_id') . " = " . (int)$this->type . " AND " . $db->quoteName('state_id') . " IN (0, " . (int)$this->address->state_id;
			$query .= ") AND ( " . $db->quote($db->escape($postcode)) . " BETWEEN " . $db->quoteName('zipcode_from') . " AND " . $db->quoteName('zipcode_to') . " OR (";
			$query .= $db->quote('zipcode_to') . " = 0 AND " . $db->quoteName('zipcode_from') . " IN(0, " . $db->quote($db->escape($postcode)) . "))) ORDER BY ";
			$query .= $db->quoteName('zipcode_from') . " DESC, " . $db->quoteName('zipcode_to') . " DESC, " . $db->quoteName('state_id') . " DESC, " . $db->quoteName('country_id') . " DESC" ;
	
			$db->setQuery($query);
			$results = $db->loadObjectList();
	
			$behavior = 0;
			$first_row = true;
	
			foreach ($results as $result){
				$tax = new JeproshopTaxModelTax((int)$result->tax_id);
	
				$taxes[] = $tax;
	
				// the applied behavior correspond to the most specific rules
				if ($first_row){
					$behavior = $result->behavior;
					$first_row = false;
				}
	
				if ($result->behavior == 0){ break; }
			}
			JeproshopCache::store($cache_id, new JeproshopTaxCalculator($taxes, $behavior));
		}
		return JeproshopCache::retrieve($cache_id);
	}	
}

/***** --------- TAX CALCULATOR ----------- *******/
class JeproshopTaxCalculator
{
	const COMBINE_METHOD = 1;
	const ONE_AFTER_ANOTHER_METHOD = 2;

	public $taxes;

	public $computation_method;
	
	public function __construct(array $taxes = array(), $computation_method = JeproshopTaxCalculator::COMBINE_METHOD) {
		// sanity check
		foreach($taxes as $tax){
			if(!($tax instanceof JeproshopTaxModelTax)){
				JError::raiseError(500, JText::_('COM_JEPROSHOP_INVALID_TAX_OBJECT_MESSAGE'));
			}
		}
		$this->taxes = $taxes;
		$this->computation_method = (int)$computation_method;
	}
	
	public function getTotalRate(){
		$taxes = 0;
		if($this->computation_method == JeproshopTaxCalculator::ONE_AFTER_ANOTHER_METHOD){
			$taxes = 1;
			foreach($this->taxes as $tax){
				$taxes *= (1 + (abs($tax->rate) / 100));
			}
			$tax = $taxes -1;
			$taxes = $tax * 100;
		}else{
			foreach($this->taxes as $tax){
				$taxes += abs($tax->rate);
			}
		}
		return (float)$taxes;
	}

    /**
     * Compute and remove the taxes to the specified price
     *
     * @param float $price_ti price tax inclusive
     * @return float price without taxes
     */
    public function removeTaxes($price_ti){
        return $price_ti / (1 + $this->getTotalRate() / 100);
    }
}

interface JeproshopTaxManagerInterface
{
	/**
	 * This method determine if the tax manager is available for the specified address
	 * @param JeproshopAddressModelAddress $address
	 *
	 * @return JeproshopTaxManager Description
	 */
	public static function isAvailableForThisAddress(JeproshopAddressModelAddress $address);

	/**
	 * Return the tax calculator associated to this address
	 *
	 * @return JeproshopTaxCalculator
	*/
	public function getTaxCalculator();
}
