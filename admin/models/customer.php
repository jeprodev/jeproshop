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

class JeproshopCustomerModelCustomer extends JModelLegacy
{
	public $customer_id;
	
	public $shop_id;
	
	public $shop_group_id;
	
	public $secure_key;
	
	public $note;
	
	public $gender_id = 0;
	
	public $default_group_id;
	
	public $lang_id;
	
	public $title;
	
	public $lastname;
	
	public $firstname;
	
	public $birthday = null;
	
	public $email;
	
	public $newsletter;
	
	public $ip_registration_newsletter;
	
	public $newsletter_date_add;
	
	public $optin;
	
	public $is_guest;
	
	public $website;
	
	public $company;
	
	public $siret;
	
	public $ape;
	
	public $published;

    public $state_id;

    public $postcode;

    public $geolocation_country_id;
	
	public $date_add;
	public $date_upd;
	
	protected static $_customer_groups = array();
	protected static $_customer_has_address = array();

	private $pagination;
	
	public function __construct($customer_id = NULL){
		parent::__construct();
		
		if($customer_id){
			$cache_id = 'jeproshop_customer_model_' . $customer_id . ( $this->shop_id ? '_' . $this->shop_id : '');
			if(!JeproshopCache::isStored($cache_id)){
				$db = JFactory::getDBO();
		
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_customer') . " AS customer ";
		
		
				/** Get shop informations **/
				if(JeproshopShopModelShop::isTableAssociated('order')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_order_shop') . " AS order_shop ON (";
					$query .= "ord.order_id = order_shop.order_id AND order_shop.shop_id = " . (int)  $this->shop_id . ")";
				}
				$query .= " WHERE customer.customer_id = " . (int)$customer_id ;
		
				$db->setQuery($query);
				$customer_data = $db->loadObject();
		
				if($customer_data){				
					JeproshopCache::store($cache_id, $customer_data);
				}
			}else{
				$customer_data = JeproshopCache::retrieve($cache_id);
			}
		
			if($customer_data){
				$customer_data->customer_id = $customer_id;
				foreach($customer_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		$this->default_group_id = JeproshopSettingModelSetting::getValue('customer_group');
	}
	
	public function getCustomerList(){
		jimport('joomla.html.pagination');
		$context = JeproshopContext::getContext();
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
        $context->controller->default_form_language = $context->language->lang_id;
	
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');

        $selectShop = ", shop.shop_name AS shop_name ";
        $joinShop = " LEFT JOIN " . $db->quoteName('#__jeproshop_shop') . " AS shop ON(customer." . $db->quoteName('shop_id') . " = shop." . $db->quoteName('shop_id') . ") ";
        $whereShop = JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER, 'customer');


        /* Manage default params values */
        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do {
            $query = "SELECT SQL_CALC_FOUND_ROWS customer." . $db->quoteName('customer_id') . ", " . $db->quoteName('lastname') . ", " . $db->quoteName('firstname') . ", " . $db->quoteName('email') . $selectShop;
            if (JeproshopSettingModelSetting::getValue('enable_b2b_mode')) {
                $query .= ", customer." . $db->quoteName('company') . ", customer." . $db->quoteName('website');
            }
            $query .= ", customer.published AS published, customer." . $db->quoteName('newsletter') . ", customer." . $db->quoteName('optin') . ", customer.date_add, customer.title AS title, ( SELECT SUM(";
            $query .= "total_paid_tax_excl / conversion_rate) FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord WHERE ord.customer_id = customer.customer_id AND ord.shop_id IN (";
            $query .= implode(',', JeproshopShopModelShop::getContextListShopIds()) . ") AND customer.published = 1 ) AS total_spent, ( SELECT connection.date_add FROM " . $db->quoteName('#__jeproshop_guest');
            $query .= " AS guest LEFT JOIN " . $db->quoteName('#__jeproshop_connection') . " AS connection ON connection.guest_id = guest.guest_id  WHERE guest.customer_id = customer.customer_id ORDER BY ";
            $query .= " customer.date_add DESC LIMIT 1 ) AS connect FROM " . $db->quoteName('#__jeproshop_customer') . " AS customer "; //LEFT JOIN " . $db->quoteName('#__jeproshop_gender_lang') . " AS gender_lang ";
            $query .=  $joinShop ." WHERE 1 AND customer." . $db->quoteName('deleted') . " = 0 ORDER BY ";
            $query .= $db->quoteName('date_add') . $whereShop;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= " ASC " . ( $use_limit ? "LIMIT " . $limitstart . ", " . $limit : "");
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
	
	public function getPagination(){
		return $this->pagination;
	}
	
	/**
	 * Return several useful statistics about customer
	 *
	 * @return array Stats
	 */
	public function getStats(){
		$db = JFactory::getDBO();
	
		$query = "SELECT COUNT(" . $db->quoteName('order_id') . ") AS nb_orders, SUM(" . $db->quoteName('total_paid');
		$query .= " / ord." . $db->quoteName('conversion_rate') . ") AS total_orders FROM " . $db->quoteName('#__jeproshop_orders');
		$query .= " AS ord WHERE ord." . $db->quoteName('customer_id') . " = " . (int)$this->customer_id . " AND ord.valid = 1";
		
		$db->setQuery($query);
		$result = $db->loadObject();
		
		$query = "SELECT MAX(connection." . $db->quoteName('date_add') . ") AS last_visit FROM " .$db->quoteName('#__jeproshop_guest');
		$query .= " AS guest LEFT JOIN " . $db->quoteName('#__jeproshop_connection') . " AS connection ON connection.guest_id = guest.";
		$query .= "guest_id WHERE guest." . $db->quoteName('customer_id') . " = " .(int)$this->customer_id ;
		
		$db->setQuery($query);
		$result2 = $db->loadObject();	
		
		$query = "SELECT (YEAR(CURRENT_DATE)-YEAR(customer." . $db->quoteName('birthday') . ")) - (RIGHT(CURRENT_DATE, 5) < RIGHT(customer.";
		$query .= $db->quoteName('birthday') . ", 5)) AS age FROM " . $db->quoteName('#__jeproshop_customer') . " AS customer WHERE customer.";
		$query .= $db->quoteName('customer_id') . " = " . (int)$this->customer_id;
		
		$db->setQuery($query);
		$result3 = $db->loadObject();

		$result->last_visit =  isset($result2) ? $result2->last_visit : '';
		$result->age = (isset($result3) &&($result3->age != date('Y')) ? $result3->age : '--');
		return $result;
	}
	
	public static function getStaticGroups($customer_id){
		if (!JeproshopGroupModelGroup::isFeaturePublished()){
			return array(JeproshopSettingModelSetting::getValue('customer_group'));
		}
	
		if($customer_id == 0){
			self::$_customer_groups[$customer_id] = array((int)  JeproshopSettingModelSetting::getValue('unidentified_group'));
		}
	
		if (!isset(self::$_customer_groups[$customer_id])){
			self::$_customer_groups[$customer_id] = array();
			$db = JFactory::getDBO();
	
			$query = "SELECT customer_group." . $db->quoteName('group_id') . " FROM " . $db->quoteName('#__jeproshop_customer_group');
			$query .= " AS customer_group WHERE customer_group." . $db->quoteName('customer_id') . " = " .(int)$customer_id;
			$db->setQuery($query);
			$result = $db->loadObjectList();
			foreach ($result as $group){
				self::$_customer_groups[$customer_id][] = (int)$group->group_id;
			}
		}
		return self::$_customer_groups[$customer_id];
	}
	
	public function getGroups(){
		return JeproshopCustomerModelCustomer::getStaticGroups((int)$this->customer_id);
	}
	
	public function getBoughtProducts(){
		$db = JFactory::getDBO();
	
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_orders') . " AS ord LEFT JOIN " . $db->quoteName('#__jeproshop_order_detail') . " AS order_detail ON ord.order_id";
		$query .= " = order_detail.order_id WHERE ord.valid = 1 AND ord." . $db->quoteName('customer_id') . " = " . (int)$this->customer_id;
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}	

	public function getLastConnections(){
		$db = JFactory::getDBO();
	
		$query = "SELECT connection.date_add, COUNT(connection_page.page_id) AS pages, TIMEDIFF(MAX(connection_page.time_end), connection.date_add) as time, http_referrer,";
		$query .= " INET_NTOA(ip_address) as ip_address FROM " . $db->quoteName('#__jeproshop_guest') . " AS g LEFT JOIN "  . $db->quoteName('#__jeproshop_connection') . " AS ";
		$query .= " connection ON connection.guest_id = g.guest_id LEFT JOIN " . $db->quoteName('#__jeproshop_connection_page') . " AS connection_page  ON connection.";
		$query .= "connection_id = connection_page.connection_id WHERE g." . $db->quoteName('customer_id') . " = " . (int)$this->customer_id . " GROUP BY connection.";
		$query .= $db->quoteName('connection_id') . " ORDER BY connection.date_add DESC LIMIT 10 ";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Check if e-mail is already registered in database
	 *
	 * @param string $email e-mail
	 * @param $return_id boolean
	 * @param $ignore_guest boolean, to exclude guest customer
	 * @return Customer ID if found, false otherwise
	 */ 
	public static function customerExists($email, $return_id = false, $ignore_guest = true){
		if (!JeproshopTools::isEmail($email)){
			if (defined('COM_JEPROSHOP_DEV_MODE') && COM_JEPROSHOP_DEV_MODE)
				die (Tools::displayError('Invalid email'));
			else
				return false;
		}
		$db = JFactory::getDBO();
		$query = "SELECT " . $db->quoteName('customer_id') . " FROM " . $db->quoteName('#__jeproshop_customer');
		$query .= " WHERE " . $db->quoteName('email') . " = " . $db->quote($db->escape($email));
		$query .= JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER);
		$query .= ($ignore_guest ? " AND " . $db->quoteName('is_guest') . " = 0" : "");
		
		$db->setQuery($query);
		$result = $db->loadObject();
	
		if ($return_id)
			return $result->customer_id;
		return isset($result->customer_id);
	}
	
	/**
	 * Specify if a customer already in base
	*
	* @param $customer_id Customer id
	* @return boolean
	*/
	// DEPRECATED
	public function customerIdExists($customer_id){
		return JeproshopCustomerModelCustomer::customerIdExistsStatic((int)$customer_id);
	}
	
	public function isGuest(){
		return (bool)$this->is_guest;
	}
	
	public static function customerIdExistsStatic($customer_id){
		$cache_id = 'jeproshop_customer_exists_id_'.(int)$customer_id;
		if (!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
			
			$query = "SELECT " . $db->quoteName('customer_id') . " FROM " . $db->quoteName('#__jeproshop_customer') . " AS customer WHERE customer." . $db->quoteName('customer_id') . " = " . (int)$customer_id;
			$db->setQuery($query);
			$result = $db->loadResult();
			JeproshopCache::store($cache_id, $result);
		}
		return JeproshopCache::retrieve($cache_id);
	}
	
	/**
	 * Return customer addresses
	 *
	 * @param integer $lang_id Language ID
	 * @return array Addresses
	 */
	public function getAddresses($lang_id){
		$share_order = (bool)JeproshopContext::getContext()->shop->getShopGroup()->share_order;
		$cache_id = 'jeproshop_customer_getAddresses_'.(int)$this->customer_id.'-'.(int)$lang_id . '_' . $share_order;
		if (!JeproshopCache::isStored($cache_id)){
			$db = JFactory::getDBO();
			$query = "SELECT DISTINCT address.*, country_lang." . $db->quoteName('name') . " AS country, stat.name AS ";
			$query .= "state, stat.iso_code AS state_iso FROM " . $db->quoteName('#__jeproshop_address') . " AS address";
			$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_country') . " AS country ON (address.";
			$query .= $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . ") LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id');
			$query .= " = country_lang." . $db->quoteName('country_id') . ") LEFT JOIN " . $db->quoteName('#__jeproshop_state');
			$query .= " AS stat ON (stat." . $db->quoteName('state_id') . " = address." . $db->quoteName('state_id') . ") ";
			$query .= ($share_order ? "" : JeproshopShopModelShop::addSqlAssociation('country')) . " WHERE " . $db->quoteName('lang_id');
			$query .= " = " .(int)$lang_id . " AND " . $db->quoteName('customer_id') . " = " .(int)$this->customer_id . " AND address.";
			$query .= $db->quoteName('deleted') . " = 0";
			
			$db->setQuery($query);
			$result = $db->loadObjectList();
			JeproshopCache::store($cache_id, $result);
		}
		return JeproshopCache::retrieve($cache_id);
	}

	public function add(){
		$this->shop_id = ($this->shop_id) ? $this->shop_id : JeproshopContext::getContext()->shop->shop_id;
		$this->shop_group_id = ($this->shop_group_id) ? $this->shop_group_id : JeproshopContext::getContext()->shop->shop_group_id;
		$this->lang_id = ($this->lang_id) ? $this->lang_id : JeproshopContext::getContext()->language->lang_id;
		$this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);
		$this->secure_key = md5(uniqid(rand(), true));
		$this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.JeproshopSettingModelSetting::getValue('password_regeneration_delay').' minutes'));
	
		if ($this->newsletter && !JeproshopTools::isDate($this->newsletter_date_add))
			$this->newsletter_date_add = date('Y-m-d H:i:s');
	
		if ($this->default_group_id == JeproshopSettingModelSetting::getValue('customer_group')) {
			if ($this->is_guest) {
				$this->default_group_id = (int)JeproshopSettingModelSetting::getValue('guest_group');
			} else {
				$this->default_group_id = (int)JeproshopSettingModelSetting::getValue('customer_group');
			}
		}
		/* Can't create a guest customer, if this feature is disabled */
		if ($this->is_guest && !JeproshopSettingModelSetting::getValue('enable_guest_checkout')) {
			return false;
		}
		$this->date_add = date('Y-m-d H:i:s');
		$this->date_upd = date('Y-m-d H:i:s');
	
		$db = JFactory::getDBO();
		$input = JRequest::get('post');
		$isNewCustomer = isset($input['is_new_customer']) ? 1 : 0;
	
		$email = isset($input['email']) ? $input['email'] : '';
		$company = '';
		if (!$isNewCustomer){
			$password = md5(time() . COM_JEPROSHOP_COOKIE_KEY);
		}else {
			$password = md5($input['passwd'] . COM_JEPROSHOP_COOKIE_KEY);
		}
		$siret = isset($input['siret']) ? $input['siret'] : '';
		$ape = isset($input['ape']) ? $input['ape'] : '';
		$title = isset($input['title']) ? $input['title'] : '';
		$firstName = isset($input['firstname']) ? $input['firstname'] : '';
		$lastName = isset($input['lastname']) ? $input['lastname'] : '';
		$deleted = isset($input['deleted']) ? $input['deleted'] : 0;
	
		$query = "INSERT INTO " . $db->quoteName('#__jeproshop_customer') . "(" . $db->quoteName('shop_group_id') . ", " . $db->quoteName('shop_id') . ", " . $db->quoteName('default_group_id') . ", " . $db->quoteName('lang_id') . ", ";
		$query .= $db->quoteName('company') . ", " . $db->quoteName('siret') . ", " . $db->quoteName('ape') . ", " . $db->quoteName('title') . ", "  . $db->quoteName('firstname') . ", " . $db->quoteName('lastname') . ", " ;
		$query .= $db->quoteName('email') . ", " . $db->quoteName('passwd') . ", " . $db->quoteName('last_passwd_gen') . ", " . $db->quoteName('secure_key') . ", "  . $db->quoteName('published') . ", " . $db->quoteName('is_guest') . ", ";
		$query .= $db->quoteName('deleted') . ", " . $db->quoteName('date_add') . ", " . $db->quoteName('date_upd') . ") VALUES (" . (int)$this->shop_group_id . ", " . (int)$this->shop_id . ", " . (int)$this->default_group_id . ", " ;
		$query .= (int)$this->lang_id . ", " . $db->quote($company) . ", " . $db->quote($siret) . ", "  . $db->quote($ape) . ", "  . $db->quote($title) . ", "  . $db->quote($firstName) . ", " . $db->quote($lastName) . ", ";
		$query .= $db->quote($email) . ", " . $db->quote($password) . ", " . $db->quote($this->last_passwd_gen) . ", " . $db->quote($this->secure_key) . ", " . (int)$this->published . ", " . (int)$this->is_guest . ", " . (int)$deleted;
		$query .= ", " . $db->quote($this->date_add) . ", " . $db->quote($this->date_upd) . ")";
	
		$db->setQuery($query);
		$success = $db->query();
		$this->updateGroup($this->groupBox);
		return $success;
	}

    public function update($nullValues = false)
    {
        $this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);

        if ($this->newsletter && !Validate::isDate($this->newsletter_date_add))
            $this->newsletter_date_add = date('Y-m-d H:i:s');
        if (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin')
            $this->updateGroup($this->groupBox);

        if ($this->deleted)
        {
            $addresses = $this->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
            foreach ($addresses as $address)
            {
                $obj = new Address((int)$address['id_address']);
                $obj->delete();
            }
        }

        return parent::update(true);
    }

    public function delete(){
        if (!count(JeproshopOrderModelOrder::getCustomerOrders((int)$this->customer_id))){
            $addresses = $this->getAddresses((int)JeproshopSettingModelSetting::getValue('default_lang'));
            foreach ($addresses as $address){
                $obj = new JeproshopAddressModelAddress((int)$address->address_id);
                $obj->delete();
            }
        }
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_customer_group') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$this->customer_id;

        $db->setQuery($query);
        $db->query();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_message') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$this->customer_id;

        $db->setQuery($query);
        $db->query();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_specific_price') . " WHERE " . $db->quoteName('customer_id') . " = " .(int)$this->customer_id;

        $db->setQuery($query);
        $db->query();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_compare') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$this->customer_id;

        $db->setQuery($query);
        $db->query();

        $query = "SELECT "  . $db->quoteName('cart_id') . " FROM " . $db->quoteName('#__jeproshop_cart') . " WHERE " .  $db->quoteName('customer_id') . " = " . (int)$this->customer_id;

        $db->setQuery($query);
        $carts = $db->loadObjectList();

        if ($carts){
            foreach ($carts as $cart){
                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart') . " WHERE " . $db->quoteName('cart_id') . " = " . (int)$cart->cart_id;

                $db->setQuery($query);
                $db->query();

                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_cart_product') . " WHERE " . $db->quoteName('cart_id') . " = " .(int)$cart->cart_id;
                $db->setQuery($query);
                $db->query();
            }
        }

        $query = "SELECT " . $db->quoteName('customer_thread_id') . " FROM " . $db->quoteName('#__jeproshop_customer_thread') . " WHERE " . $this->quoteName('customer_id') . " = " . (int)$this->customer_id;
        $db->setQuery($query);
        $customer_threads = $db->loadObjectList();

        if ($customer_threads){
            foreach ($customer_threads as $customer_thread){
                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_customer_thread') . " WHERE " . $db->quoteName('customer_thread_id') . " = " . (int)$customer_thread->customer_thread_id;
                $db->setQuery($query);
                $db->query();

                $query = "DELETE FROM " . $db->quoteName('#__jeproshop_customer_message') . " WHERE " . $db->quoteName('customer_thread_id') . " = " .(int)$customer_thread->customer_thread_id;
                $db->setQuery($query);
                $db->query();
            }
        }
        JeproshopCartRuleModelCartRule::deleteByCustomerId((int)$this->customer_id);
        return parent::delete();
    }

    /**
     * Return customers list
     *
     * @return array Customers
     */
    public static function getCustomers(){
        $db = JFactkory::getDBO();

        $query = "SELECT "  . $db->quoteName('customer_id') . ", " . $db->quoteName('email') . ", " . $db->quoteName('firstname') . ", " . $db->quoteName('lastname') . " FROM " . $db->quoteName('#__jeproshop_customer');
        $query .= "	WHERE 1 " . JeproshopShopModelShop::addSqlRestriction(JeproshopfShopModelShop::SHARE_CUSTOMER) . " ORDER BY " . $db->quoteName('customer_id') . " ASC";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Return customer instance from its e-mail (optionally check password)
     *
     * @param string $email e-mail
     * @param string $passwd Password is also checked if specified
     * @param bool $ignore_guest
     * @return Customer instance
     */
    public function getByEmail($email, $passwd = null, $ignore_guest = true){
        if (!JeproshopTools::isEmail($email) || ($passwd && !JeproshopTools::isPasswd($passwd)))
            die (Tools::displayError());

        $db = JFactory::getDBO();

        $query = "SELECT * FROM ". $db->quoteName('#__jeproshop_customer') . " WHERE "  . $db->quoteName('email') . " = " . $db->quote($email) . JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER);
        $query .= (isset($passwd) ? " AND " . $db->quoteName('passwd') . " = " . $db->quote(JeproshopTools::encrypt($passwd)) : "") . " AND "  . $db->quoteName('deleted') .  " = 0" . ($ignore_guest ? " AND " . $db->quoteName('is_guest') . " = 0" : "");

        $db->setQuery($query);
        $result = $db->loadObject();

        if (!$result){ return false; }

        $this->customer_id = $result->customer_id;
        foreach ($result as $key => $value)
            if (array_key_exists($key, $this))
                $this->{$key} = $value;

        return $this;
    }

    /**
     * Retrieve customers by email address
     *
     * @static
     * @param $email
     * @return array
     */
    public static function getCustomersByEmail($email){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_customer') . " WHERE " . $db->quoteName('email') . " = " . $db->quote($email) . JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER);

        $db->setQuery($query);
        return $db->loadObjectList();
    }


    /**
     * Check id the customer is active or not
     *
     * @param $customer_id
     * @return bool customer validity
     */
    public static function isBanned($customer_id){
        if (!JeproshopTools::isUnsignedInt($customer_id)){ return true; }
        $cache_id = 'jeproshop_model_customer_isBanned_' . (int)$customer_id;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT "  . $db->quoteName('customer_id') . " FROM " . $db->quoteName('#__jeproshop_customer') . " WHERE " . $db->quoteName('customer_id') ." = " .(int)$customer_id;
            $query .= " AND " . $db->quoteName('published') . " = 1 AND " .  $db->quoteName('deleted') . " = 0";

            $db->setQuery($query);
            $result = (bool)!$db->loadObject();

            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    public static function resetAddressCache($customer_id)
    {
        if (array_key_exists($customer_id, self::$_customer_has_address))
            unset(self::$_customer_has_address[$customer_id]);
    }

    /**
     * Count the number of addresses for a customer
     *
     * @param integer $customer_id Customer ID
     * @return integer Number of addresses
     */
    public static function getAddressesTotalById($customer_id){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT("  . $db->quoteNBame('address_id') . ") FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$customer_id . " AND " .  $db->quoteName('deleted') . " = 0";
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Check if customer password is the right one
     *
     * @param $customer_id
     * @param string $passwd Password
     * @return bool result
     */
    public static function checkPassword($customer_id, $passwd){
        if (!JeproshopTools::isUnsignedInt($customer_id) || !JeproshopValidate::isMd5($passwd))
            die (Tools::displayError());

        $cache_id = 'jeproshop_model_customer_check_password_' .(int)$customer_id . '_' . $passwd;
        if (!JeproshopCache::isStored($cache_id)){
            $db = JFactory::getDBO();
            $query = "SELECT " . $db->quoteName('customer_id') . " FROM " . $db->quoteName('#__jeproshop_customer') . " WHERE " . $db->quoteName('customer_id') . " = " . $customer_id . " AND " . $db->quoteName('passwd') . " = " . $db->quote($passwd);

            $db->setQuery($query);
            $result = (bool)$db->loadResult();
            JeproshopCache::store($cache_id, $result);
        }
        return JeproshopCache::retrieve($cache_id);
    }

    /**
     * Light back office search for customers
     *
     * @param string $sql Searched string
     * @return array Corresponding customers
     */
    public static function searchByName($sql) {
        $db = JFactory::getDBO();
        $sql = '%' . $sql . '%';
        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_customer') . " WHERE ( " . $db->quoteName('email') . " LIKE " . $db->quote($sql) . " OR "  . $db->quoteName('customer_id') . " LIKE " . $db->quote($sql) . " OR "  . $db->quoteName('lastname');
        $query .= " LIKE " . $db->quote($sql) ." OR " . $db->quoteName('firstname') . " LIKE " . $db->quote($sql). " ) " ; // . JeproshopShopModelShop::addSqlRestriction(JeproshopShopModelShop::SHARE_CUSTOMER);
		$db->setQuery($query);
        return ($db->loadObjectList());
    }

    /**
     * Search for customers by ip address
     *
     * @param string $ip Searched string
     */
    public static function searchByIp($ip){
        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT customer.* FROM " . $db->quoteName('#__jeproshop_customer') . " AS customer LEFT JOIN " . $db->quoteName('#__jeproshop_guest') . " AS guest ON (guest."  . $db->quoteName('customer_id') . " = customer." . $db->quoteName('customer_id');
        $query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_connection') . " AS connection ON (group." . $db->quoteName('guest_id') . " = connection."  . $db->quoteName('guest_id') .  " WHERE connection." . $db->quoteName('ip_address') . " = " . $db->quote(ip2long(trim($ip)));

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Update customer groups associated to the object
     *
     * @param array $list groups
     */
    public function updateGroup($list){
        if ($list && !empty($list)){
            $this->cleanGroups();
            $this->addGroups($list);
        }else {
            $this->addGroups(array($this->default_group_id));
        }
    }

    public function cleanGroups() {
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_.customer_group') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$this->customer_id;
        $db->setQuery($query);
        $db->query();
    }

	public function addGroups($groups){
        $db = JFactory::getDBO();
        foreach ($groups as $group) {
            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_customer_group') . "(" . $db->quoteName('customer_id') . ", " . $db->quoteName('group_id') . ") VALUES ( " . (int)$this->customer_id . ", " . (int)$group .")";
            $db->setQuery($query);
            $db->query();
        }
    }

    public static function getDefaultGroupId($customer_id){
        if (!JeproshopGroupModelGroup::isFeaturePublished()) {
            return JeproshopSettingModelSetting::getValue('customer_group');
        }

        if (!isset(self::$_defaultGroupId[(int)$customer_id])) {
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('default_group_id') . " FROM " . $db->quoteName('#__jeproshop_customer') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$customer_id;

            $db->setQuery($query);
            self::$_defaultGroupId[(int)$customer_id] = $db->loadResult();
        }
        return self::$_defaultGroupId[(int)$customer_id];
    }

    public static function getCurrentCountry($customer_id, JeproshopCartModelCart $cart = null){
        if (!$cart)
            $cart = JeproshopContext::getContext()->cart;
        if (!$cart || !$cart->{JeproshopSettingModelSetting::getValue('tax_address_type')}){
            $db = JFactory::getDBO();

            $query = "SELECT " . $db->quoteName('address_id') . " FROM " . $db->quoteName('#__jeproshop_address') . " WHERE " . $db->quoteName('customer_id') . " = " . (int)$customer_id . " AND " . $db->quoteName('deleted') . " = 0 ORDER BY " . $db->quoteName('address_id');

            $db->setQuery($query);
            $address_id = (int)$db->loadResult();
        }else {
            $address_id = $cart->{JeproshopSettingModelSetting::getValue('tax_address_type')};
        }
        $address = JeproshopAddressModelAddress::getCountryAndState($address_id);
        return (int)$address->country_id ? $address->country_id : JeproshopSettingModelSetting::getValue('default_country');
    }

    public function toggleStatus(){
        $db = JFacctory::getDBO();

        parent::toggleStatus();

        /* Change status to active/inactive */
        $query = "UPDATE " . $db->quoteName('#__jeproshop_customer') . " SET " . $db->quoteName('date_upd') . " = NOW() WHERE " . $db->quoteName('customer_id') . " = " . (int)$this->customer_id ;

        $db->setQuery($query);
        return $db->query();
    }


    public function transformToCustomer($lang_id, $password = null){
        if (!$this->isGuest()){ return false; }
        if (empty($password)) {
            $password = JeproshopTools::passwdGen();
        }
        if (!JeproshopTools::isPasswd($password)){ return false; }

        $this->is_guest = 0;
        $this->passwd = JeproshopTools::encrypt($password);
        $this->cleanGroups();
        $this->addGroups(array(JeproshopSettingModelSetting::getValue('customer_group'))); // add default customer group
        if ($this->update()){
            $vars = array(
                '{firstname}' => $this->firstname,
                '{lastname}' => $this->lastname,
                '{email}' => $this->email,
                '{passwd}' => $password
            );

            Mail::Send(
                (int)$lang_id,
                'guest_to_customer',
                Mail::l('Your guest account has been transformed into a customer account', (int)$lang_id),
                $vars,
                $this->email,
                $this->firstname.' '.$this->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                (int)$this->shop_id
            );
            return true;
        }
        return false;
    }

    public function setWebServicePasswd($passwd){
        if ($this->customer_id != 0){
            if ($this->passwd != $passwd) {
                $this->passwd = JeproshopTools::encrypt($passwd);
            }
        }
        else
            $this->passwd = JeproshopTools::encrypt($passwd);
        return true;
    }

    /**
     * Check customer informations and return customer validity
     *
     * @since 1.5.0
     * @param boolean $with_guest
     * @return boolean customer validity
     */
    public function isLogged($with_guest = false){
        if (!$with_guest && $this->is_guest == 1){ return false; }

        /** Customer is valid only if it can be load and if object password is the same as database one **/
        if ($this->logged == 1 && $this->customer_id && JeproshopTools::isUnsignedInt($this->customer_id) && JeproshopCustomerModelCustomer::checkPassword($this->customer_id, $this->passwd))
            return true;
        return false;
    }

    /**
     * Logout
     *
     * @since 1.5.0
     */
    public function logout(){
        if (isset(JeproshopContext::getContext()->cookie))
            JeproshopContext::getContext()->cookie->logout();
        $this->logged = 0;
    }

    /**
     * Soft logout, delete everything links to the customer
     * but leave there affiliate's informations
     *
     * @since 1.5.0
     */
    public function mylogout()
    {
        if (isset(Context::getContext()->cookie))
            Context::getContext()->cookie->mylogout();
        $this->logged = 0;
    }

    public function getLastCart($with_order = true)
    {
        $carts = Cart::getCustomerCarts((int)$this->id, $with_order);
        if (!count($carts))
            return false;
        $cart = array_shift($carts);
        $cart = new Cart((int)$cart['id_cart']);
        return ($cart->nbProducts() === 0 ? (int)$cart->id : false);
    }

    public function getOutstanding(){
        $query = new DbQuery();
        $query->select('SUM(oi.total_paid_tax_incl)');
        $query->from('order_invoice', 'oi');
        $query->leftJoin('orders', 'o', 'oi.id_order = o.id_order');
        $query->groupBy('o.id_customer');
        $query->where('o.id_customer = '.(int)$this->id);
        $total_paid = (float)Db::getInstance()->getValue($query->build());

        $query = new DbQuery();
        $query->select('SUM(op.amount)');
        $query->from('order_payment', 'op');
        $query->leftJoin('order_invoice_payment', 'oip', 'op.id_order_payment = oip.id_order_payment');
        $query->leftJoin('orders', 'o', 'oip.id_order = o.id_order');
        $query->groupBy('o.id_customer');
        $query->where('o.id_customer = '.(int)$this->id);
        $total_rest = (float)Db::getInstance()->getValue($query->build());

        return $total_paid - $total_rest;
    }

    public function getWebServiceGroups(){
        $db = JFactory::getDBO();

        $query = "SELECT customer_group." . $db->quoteName('group_id') . " AS group_id FROM " . $db->quoteName('#__jeproshop_customer_group') . " AS customer_group "
			.Shop::addSqlAssociation('group', 'cg'). " WHERE customer_group." . $db->quoteName('customer_id') . " = ".(int)$this->customer_id;

        $db->setQuery($query);
        return $db->loadOjectList();
    }

    public function setWsGroups($result){
        $groups = array();
        foreach ($result as $row)
            $groups[] = $row['id'];
        $this->cleanGroups();
        $this->addGroups($groups);
        return true;
    }

    /**
     * @see ObjectModel::getWebserviceObjectList()
     * @param $sql_join
     * @param $sql_filter
     * @param $sql_sort
     * @param $sql_limit
     * @return
     */
    public function getWebServiceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $sql_filter .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'main');
        return parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
    }
	
}


/*** ------ CUSTOMER THREAD ---------****/
class JeproshopCustomerThreadModelCustomerThread extends JModelLegacy
{
	public $customer_thread_id;
	public $shop_id;
	public $lang_id;
	public $contact_id;
	public $customer_id;
	public $order_id;
	public $product_id;
	public $status;
	public $email;
	public $token;
	public $date_add;
	public $date_upd;
	
	public static function getCustomerMessages($customer_id, $read = null){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_customer_thread'). " AS customer_thread LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_customer_message') . " AS customer_message ON (customer_thread.customer_thread_id";
		$query .= " = customer_message.customer_thread_id ) WHERE customer_id = " . (int)$customer_id;
	
		if (!is_null($read)){
			$query .= " AND customer_message." . $db->quoteName('read') . " = " . (int)$read;
		}
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getWsCustomerMessages(){
		$db = JFactory::getDBO();
	
		$query = "SELECT " . $db->quoteName('customer_message_id') . " AS message_id FROM " . $db->quoteName('#__jeproshop_customer_message');
		$query .= " WHERE " . $db->quoteName('customer_thread_id') . " = " .(int)$this->customer_thread_id;
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function delete(){
        if (!JeproshopTools::isUnsignedInt($this->customer_thread_id))
            return false;

        $return = true;

        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteNAme('customer_message_id') . " FROM " . $db->quoteName('#__jeproshop_customer_message') . " WHERE " . $db->quoteName('customer_thread_id') . " = " . (int)$this->customer_thread_id;
        $db->setQuery($query);
        $results = $db->loadObjectList();

        if( count($results)){
            foreach ($results AS $result){
                $message = new JeproshopCustomerMessageModelCustomerMessage((int)$result->customer_message_id);
                if (!JeproshopTools::isLoadedObject($message, 'customer_thread_id'))
                    $return = false;
                else
                    $return &= $message->delete();
            }
        }
        $return &= parent::delete();
        return $return;
    }

    public static function getIdCustomerThreadByEmailAndIdOrder($email, $order_id){
        $db = JFactory::getDBO();

        $query = "SELECT customer_thread." . $db->quoteName('customer_thread_id') . " FROM " . $db->quoteName('#__jeproshop_customer_thread') . " AS customer_thread WHERE customer_thread.email = " . $db->quote($email);
        $query .= "	AND customer_thread.shop_id = " . (int)JeproshopContext::getContext()->shop->shop_id . " AND customer_thread.order_id = " .(int)$order_id;

        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function getContacts(){
        $db = JFactory::getDBO();

        $query = "SELECT contact_lang.*, COUNT(*) as total, ( SELECT " . $db->quoteName('customer_thread_id') . " FROM " . $db->quoteName('#__jeproshop_customer_thread') . " AS customer_thread_2 WHERE status = 'open' AND customer_thread.";
        $query .= $db->quoteName('contact_id') . " = customer_thread_2." . $db->quoteName('contact_id') . JeproshopShopModelShop::addSqlRestriction() . " ORDER BY date_upd ASC LIMIT 1 ) AS customer_thread_id FROM " . $db->quoteName('#__jeproshop_customer_thread');
        $query .= " AS customer_thread LEFT JOIN " . $db->quoteName('#__jeproshop_contact_lang') . " AS contact_lang ON (contact_lang.contact_id = customer_thread.contact_id AND contact_lang.lang_id = " . (int)JeproshopContext::getContext()->language->lang_id;
        $query .= ") WHERE customer_thread.status = 'open' AND customer_thread.contact_id IS NOT NULL AND contact_lang.contact_id IS NOT NULL " . JeproshopShopModelShop::addSqlRestriction() . " GROUP BY customer_thread.contact_id HAVING COUNT(*) > 0";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getTotalCustomerThreads($where = null) {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_customer_thread');
        if (!is_null($where)){
            $query .= " WHERE " . $where;
        }
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function getMessageCustomerThreads($customer_thread_id){
        $db = JFactory::getDBO();

        $query ="SELECT customer_thread.*, customer_message.*, contact_lang.name subject, CONCAT(employee.firstname, ' ', employee.lastname) employee_name, CONCAT(customer.firstname, ' ', customer.lastname) customer_name,";
        $query .= " customer.firstname FROM " . $db->quoteName('#__jeproshop_customer_thread') . " AS customer_thread LEFT JOIN " . $db->quoteName('#__jeproshop_customer_message') . " AS customer_message ON (customer_thread.";
        $query .= $db->quoteName('customer_thread_id') . " = customer_message."  . $db->quoteName('customer_thread_id') . " LEFT JOIN ". $db->quoteName('#__jeproshop_contact_lang') . " AS contact_lang ON (contact_lang.";
        $query .= $db->quoteName('contact_id') . " = customer_thread." . $db->quoteName('contact_id') . " AND contact_lang." . $db->quoteName('lang_id') . " = " . (int)JeproshopContext::getContext()->language->lang_id;
        $query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_employee') . " AS employee ON employee.employee_id = customer_message.employee_id LEFT JOIN " . $db->quoteName('#__jeproshop_customer') . " AS customer ";
        $query .= " ON (IFNULL(customer_thread.customer_id, customer_thread.email) = IFNULL(customer.customer_id, customer.email)) WHERE customer_thread.customer_thread_id = " . (int)$customer_thread_id . " ORDER BY customer_message.date_add ASC";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getNextThread($customer_thread_id){
        $db = JFactory::getDBO();

        $context = JeproContext::getContext();

        $query = "SELECT " . $db->quoteName('customer_thread_id') . " FROM " . $db->quoteName('#__jeproshop_customer_thread') . " AS customer_thread WHERE customer_thread.status = 'open' AND customer_thread.date_upd = (";
        $query .= " SELECT date_add FROM " . $db->quoteName('#__jeproshop_customer_message') . " WHERE (" . $db->quoteName('employee_id') . " IS NULL OR employee_id = 0) AND customer_thread_id = " . (int)$customer_thread_id;
        $query .= " ORDER BY date_add DESC LIMIT 1) " .($context->cookie->{'customer_threadFilter_cl!id_contact'} ? "AND customer_thread.contact_id = " .(int)$context->cookie->{'customer_threadFilter_cl!id_contact'} : "");
        $query .= ($context->cookie->{'customer_threadFilter_l!id_lang'} ? "AND customer_thread.id_lang = " .(int)$context->cookie->{'customer_threadFilter_l!id_lang'} : "") . " ORDER BY customer_thread.date_upd ASC";

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}


class JeproshopCustomerMessageModelCustomerMessage extends JModelLegacy
{
    public $customer_message_id;
    public $customer_thread_id;
    public $employee_id;
    public $message;
    public $file_name;
    public $ip_address;
    public $user_agent;
    public $private;
    public $date_add;
    public $date_upd;
    public $read;

    public static function getMessagesByOrderId($order_id, $private = true){
        $db = JFactory::getDBO();

        $query = "SELECT cm.*,
				c.`firstname` AS cfirstname,
				c.`lastname` AS clastname,
				e.`firstname` AS efirstname,
				e.`lastname` AS elastname,
				(COUNT(cm.id_customer_message) = 0 AND ct.id_customer != 0) AS is_new_for_me
			FROM `'. $db->quoteName('#__jeproshop_customer_message') cm
			LEFT JOIN `'. $db->quoteName('#__jeproshop_customer_thread') ct
				ON ct.`id_customer_thread` = cm.`id_customer_thread`
			LEFT JOIN `'. $db->quoteName('#__jeproshop_customer') c
				ON ct.`id_customer` = c.`id_customer`
			LEFT OUTER JOIN `'. $db->quoteName('#__jeproshop_employee') e
				ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_order = '.(int)$id_order.'
			'.(!$private ? 'AND cm.`private` = 0' : '').'
			GROUP BY cm.id_customer_message
			ORDER BY cm.date_add DESC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getTotalCustomerMessages($where = null){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_customer_message');
        if(!is_null($where)){
            $query .= " WHERE " . $where;
        }
        $db->setQuery($query);
        $db->loadResult();
    }

    public function delete()
    {
        if (!empty($this->file_name))
            @unlink(_PS_UPLOAD_DIR_.$this->file_name);
        return parent::delete();
    }
}