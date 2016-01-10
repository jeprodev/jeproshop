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

class JeproshopEmployeeModelEmployee extends JModelLegacy
{
	public $employee_id;
	
	public $customer_id;

    /** @var string Lastname */
    public $lastname;

    /** @var string Firstname */
    public $firstname;

    /** @var string e-mail */
    public $email;

    /** @var string Password */
    public $passwd;
	
	public $profile_id;
	
	public $lang_id;
	
	public $shop_id;
	
	public $theme = 'default';
	
	public $stats_date_from;
	public $stats_date_to;
	
	/** @var datetime Password **/
	public $last_passwd_gen;
	public $stats_compare_from;
	public $stats_compare_to;
	public $stats_compare_option = 1;
	
	public $preselect_date_range;
	
	protected $associated_shops = array();
	
	
	public function __construct($employee_id = NULL, $lang_id = NULL, $shop_id = null) {
		parent::__construct();
		
		$db = JFactory::getDBO();
		
		if($lang_id !== null){
			$this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) !== false)  ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang');
		}
		
		if($shop_id && $this->isMultiShop()){
			$this->shop_id = (int)$shop_id;
			$this->getShopFromContext = false;
		}		
/*
		if($this->isMultiShop() && !$this->shop_id){
			$this->shop_id = JeproshopContext::getContext()->shop->shop_id;
		}
		
		if($employee_id){
			/** load employee from database if employee * /
			$cache_id = 'jeproshop_employee_model_' . (int)$employee_id . (($lang_id) ? '_lang_' . (int)$lang_id : '') .(($shop_id) ? '_shop_' .(int)$shop_id : '');
			if(!JeproshopCache::isStored($cache_id)){
				$query = "SELECT * FROM " . $db->quoteName('#__users') . " AS employee LEFT JOIN " . $db->quoteName('#__jeproshop_employee');
				$query .= " AS alias ON(employee.id = alias.employee_id) ";
				//add language filter
				$where = "";
				if($lang_id){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_employee_lang') . " employee_lang ON(alias.";
					$query .= $db->quoteName('employee_id') . " = employee_lang." . $db->quoteName('employee_id') ;
					$query .= " AND employee_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
					if($this->shop_id && !empty($this->multiLangShop)){
						$where = " AND employee_lang." . $db->quoteName('shop_id') . " = " . $this->shop_id;
					}
				}
				 
				/** get shop information * /
				if(JeproshopShopModelShop::isTableAssociated('employee')){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_employee_shop') . " AS shop ON(alias.employee_id";
					$query .= " = shop.employee_id AND shop.shop_id = " .  (int)$this->shop_id . ")";
				}
		
				$query .= " WHERE employee." . $db->quoteName('id') . " = " . (int)$employee_id . $where;
				$db->setQuery($query);
		
				$employee_data = $db->loadObject();
				if($employee_data){
					if(!$lang_id && isset($this->multiLang) && $this->multiLang){
						$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_employee_lang') . " WHERE " . $db->quoteName('employee_id');
						$query .= " = " . (int)$employee_id . (($this->shop_id && $this->isLangMultiShop()) ? " AND " . $db->quoteName('shop_id') . " = " . $this->shop_id : "");
		
						$db->setQuery($query);
						$employee_lang_data = $db->loadObjectList();
						if($employee_lang_data){
							foreach($employee_lang_data as $row){
								foreach($row as $key => $value){
									if(array_key_exists($key, $this) && $key != 'employee_id'){
										if(!isset($employee_data->{$key}) || !is_array($employee_data->{$key})){
											$employee_data->{$key} = array();
										}
										$employee_data->{$key}[$row->lang_id] =$value;
									}
								}
							}
						}
					}
					JeproshopCache::store($cache_id, $employee_data);
				}
			} else{
				$employee_data = JeproshopCache::retrieve($cache_id);
			}
		
			if($employee_data){
				$employee_data->employee_id = (int)$employee_id;
				foreach($employee_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		*/
		if($this->employee_id){
			$this->associated_shops = $this->getAssociatedShops();
		}

		$this->image_dir = COM_JEPROSHOP_EMPLOYEE_IMAGE_DIR;
	}

    /**
     * Check employee informations saved into cookie and return employee validity
     *
     * @return boolean employee validity
     */
    public function isLoggedBack(){
        if (!JeproshopCache::isStored('jeproshop_is_logged_back_' . $this->employee_id)) {
            /* Employee is valid only if it can be load and if cookie password is the same as database one */
            JeproshopCache::store('jeproshop_is_logged_back_'.$this->employee_id, (
                $this->employee_id && JeproshopValidator::isUnsignedInt($this->employee_id) && JeproshopEmployeeModelEmployee::checkPassword($this->employee_id, JeproshopContext::getContext()->cookie->passwd)
                && (!isset(JeproshopContext::getContext()->cookie->remote_addr) || JeproshopContext::getContext()->cookie->remote_addr == ip2long(JeproshopValidator::getRemoteAddr()) || !JeproshopSettingModelSetting::getValue('cookie_check_ip'))
            ));
        }
        return JeproshopCache::retrieve('jeproshop_is_logged_back_' . $this->employee_id);
    }

    /**
     * Logout
     */
    public function logout(){
        if (isset(JeproshopContext::getContext()->cookie)) {
            JeproshopContext::getContext()->cookie->logout();
            JeproshopContext::getContext()->cookie->write();
        }
        $this->employee_id = null;
    }

    /**
     * Check if employee password is the right one
     *
     * @param int $employee_id
     * @param string $passwd Password
     * @return boolean result
     */
    public static function checkPassword($employee_id, $passwd) {
        if (!JeproshopTools::isUnsignedInt($employee_id) || !JeproshopTools::isPasswd($passwd, 8)){ die (''); }

        return Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_employee` = '.(int)$employee_id.'
		AND `passwd` = \''.pSQL($passwd).'\'
		AND active = 1');
    }

    /**
     * @see ObjectModel::getFields()
     * @return array
     */
    public function getFields()
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00')
            $this->stats_date_from = date('Y-m-d', strtotime("-1 month"));

        if (empty($this->stats_compare_from) || $this->stats_compare_from == '0000-00-00')
            $this->stats_compare_from = null;

        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00')
            $this->stats_date_to = date('Y-m-d');

        if (empty($this->stats_compare_to) || $this->stats_compare_to == '0000-00-00')
            $this->stats_compare_to = null;

        return parent::getFields();
    }

    public function add($autodate = true, $null_values = true)
    {
        $this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_BACK').'minutes'));
        $this->saveOptin();
        $this->updateTextDirection();
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00')
            $this->stats_date_from = date('Y-m-d');
        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00')
            $this->stats_date_to = date('Y-m-d');
        $this->saveOptin();
        $this->updateTextDirection();
        return parent::update($null_values);
    }

    protected function updateTextDirection()
    {
        if (!defined('_PS_ADMIN_DIR_'))
            return;
        $path = _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$this->bo_theme.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR;
        $language = new Language($this->id_lang);
        if ($language->is_rtl && !strpos($this->bo_css, '_rtl'))
        {
            $bo_css = preg_replace('/^(.*)\.css$/', '$1_rtl.css', $this->bo_css);
            if (file_exists($path.$bo_css))
                $this->bo_css = $bo_css;
        }
        elseif (!$language->is_rtl && strpos($this->bo_css, '_rtl'))
        {
            $bo_css = preg_replace('/^(.*)_rtl\.css$/', '$1.css', $this->bo_css);
            if (file_exists($path.$bo_css))
                $this->bo_css = $bo_css;
        }
    }

    protected function saveOptin()
    {
        if ($this->optin && !defined('PS_INSTALLATION_IN_PROGRESS'))
        {
            $language = new Language($this->id_lang);
            $params = http_build_query(array(
                'email' => $this->email,
                'method' => 'addMemberToNewsletter',
                'language' => $language->iso_code,
                'visitorType' => 1,
                'source' => 'backoffice'
            ));
            Tools::file_get_contents('http://www.prestashop.com/ajax/controller.php?'.$params);
        }
    }

    /**
     * Return list of employees
     */
    public static function getEmployees(){
        return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`
			FROM `'._DB_PREFIX_.'employee`
			WHERE `active` = 1
			ORDER BY `lastname` ASC
		');
    }

    /**
     * Return employee instance from its e-mail (optionnaly check password)
     *
     * @param string $email e-mail
     * @param string $passwd Password is also checked if specified
     * @return Employee instance
     */
    public function getByEmail($email, $passwd = null)
    {
        if (!Validate::isEmail($email) || ($passwd != null && !Validate::isPasswd($passwd)))
            die(Tools::displayError());

        $result = Db::getInstance()->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `active` = 1
		AND `email` = \''.pSQL($email).'\'
		'.($passwd !== null ? 'AND `passwd` = \''.Tools::encrypt($passwd).'\'' : ''));
        if (!$result)
            return false;
        $this->id = $result['id_employee'];
        $this->id_profile = $result['id_profile'];
        foreach ($result as $key => $value)
            if (property_exists($this, $key))
                $this->{$key} = $value;
        return $this;
    }

    public static function employeeExists($email)
    {
        if (!Validate::isEmail($email))
            die (Tools::displayError());

        return (bool)Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `email` = \''.pSQL($email).'\'');
    }



    public static function countProfile($id_profile, $active_only = false)
    {
        return Db::getInstance()->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
    }

    public function isLastAdmin()
    {
        return ($this->isSuperAdmin()
            && Employee::countProfile($this->id_profile, true) == 1
            && $this->active
        );
    }

    public function setWsPasswd($passwd)
    {
        if ($this->id != 0)
        {
            if ($this->passwd != $passwd)
                $this->passwd = Tools::encrypt($passwd);
        }
        else
            $this->passwd = Tools::encrypt($passwd);
        return true;
    }

    public function favoriteModulesList()
    {
        return Db::getInstance()->executeS('
			SELECT module
			FROM `'._DB_PREFIX_.'module_preference`
			WHERE `id_employee` = '.(int)$this->id.' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)'
        );
    }

    /**
     * Check if the employee is associated to a specific shop
     *
     * @since 1.5.0
     * @param int $id_shop
     * @return bool
     */
    public function hasAuthOnShop($id_shop)
    {
        return $this->isSuperAdmin() || in_array($id_shop, $this->associated_shops);
    }

    /**
     * Check if the employee is associated to a specific shop group
     *
     * @since 1.5.0
     * @param int $id_shop_shop
     * @return bool
     */
    public function hasAuthOnShopGroup($id_shop_group)
    {
        if ($this->isSuperAdmin())
            return true;

        foreach ($this->associated_shops as $id_shop)
            if ($id_shop_group == Shop::getGroupFromShop($id_shop, true))
                return true;
        return false;
    }

    /**
     * Get default id_shop with auth for current employee
     *
     * @since 1.5.0
     * @return int
     */
    public function getDefaultShopID()
    {
        if ($this->isSuperAdmin() || in_array(Configuration::get('PS_SHOP_DEFAULT'), $this->associated_shops))
            return Configuration::get('PS_SHOP_DEFAULT');
        return $this->associated_shops[0];
    }

    public static function getEmployeesByProfile($id_profile, $active_only = false)
    {
        return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
    }

    /**
     * Check if current employee is super administrator
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id_profile == _PS_ADMIN_PROFILE_;
    }

    public function getImage()
    {
        if (!Validate::isLoadedObject($this))
            return Tools::getAdminImageUrl('prestashop-avatar.png');
        return Tools::getShopProtocol().'profile.prestashop.com/'.urlencode($this->email).'.jpg';
    }

    public function getLastElementsForNotify($element)
    {
        $element = bqSQL($element);
        $max = Db::getInstance()->getValue('
			SELECT MAX(`id_'.$element.'`) as `id_'.$element.'`
			FROM `'._DB_PREFIX_.$element.($element == 'order' ? 's': '').'`');

        // if no rows in table, set max to 0
        if ((int)$max < 1)
            $max = 0;

        return (int)$max;
    }
}