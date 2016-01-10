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

class JeproshopContext
{
	/** @var JeproshopContext **/
	protected static $instance;
	
	public $controller;
	
	/** @var JeproshopCartModelCart Description **/
	public $cart;
	
	/** @var JeproshopCustomerModelCustomer Description **/
	public $customer;
	
	/** @var JeproshopCountryModelCountry Description **/
	public $country;
	
	/** @var JeproshopEmployeeModelEmployee Description **/
	public $employee;
	
	/** @var JeproshopLanguageModelLanguage Description **/
	public $language;
	
	/** @var JeproshopCurrencyModelCurrency Description **/
	public $currency;
	
	/** @var JeproshopShopModelShop Description **/
	public $shop;
	
	/** @var JeproshopMobile Description **/
	public $mobile_detect;
	 
	/** @var boolean Description **/
	public $mobile_device;
	
	/** @var JeproshopCookie Description **/
	public $cookie;
	
	/**
	 * Get a singleton JeproshopContext
	 * @return JeproshopContext
	 */
	public static function getContext(){
		if(!isset(self::$instance)){
			self::$instance = new JeproshopContext();
		}
		return self::$instance;
	}
	
	/**
	 * Clone current context
	 *
	 * @return Context
	 */
	public function cloneContext(){
		return clone($this);
	}
	
	public function getMobileDevice(){
		if ($this->mobile_device === null){
			$this->mobile_device = false;
			if ($this->checkMobileContext()){
				if (isset(Context::getContext()->cookie->no_mobile) && Context::getContext()->cookie->no_mobile == false AND (int)JeproshopSettingModelSetting::getValue('allow_mobile_device') != 0){
					$this->mobile_device = true;
				}else{
					$mobile_detect = $this->getMobileDetect();
					switch ((int)JeproshopSettingMobileSetting::getValue('allow_mobile_device')){
						case 1: // Only for mobile device
							if ($mobile_detect->isMobile() && !$mobile_detect->isTablet())
								$this->mobile_device = true;
								break;
						case 2: // Only for touch pads
							if ($mobile_detect->isTablet() && !$mobile_detect->isMobile())
								$this->mobile_device = true;
								break;
						case 3: // For touch pad or mobile devices
							if ($mobile_detect->isMobile() || $mobile_detect->isTablet())
								$this->mobile_device = true;
								break;
					}
				}
			}
		}	
		return $this->mobile_device;
	}
	
	protected function checkMobileContext(){
		// Check mobile context
		$app = JFactory::getApplication();
		if ($app->input->get('no_mobile_theme')){
			JeproshopContext::getContext()->cookie->no_mobile = true;
			if (JeproshopContext::getContext()->cookie->guest_id){
				$guest = new JeproshopGuestModelGuest(JeproshopContext::getContext()->cookie->guest_id);
				$guest->mobile_theme = false;
				$guest->update();
			}
		}elseif ($app->input->get('mobile_theme_ok')){
			JeproshopContext::getContext()->cookie->no_mobile = false;
			if (JeproshopContext::getContext()->cookie->guest_id){
				$guest = new JeproshopGuestModelGest(JeproshopContext::getContext()->cookie->guest_id);
				$guest->mobile_theme = true;
				$guest->update();
			}
		}
	
		return isset($_SERVER['HTTP_USER_AGENT']) && isset(JeproshopContext::getContext()->cookie) && (bool)JeproshopSettingModelSetting::getValue('allow_mobile_device') && @filemtime(_PS_THEME_MOBILE_DIR_) && !JeproshopContext::getContext()->cookie->no_mobile;
	}
	
	public function getDevice(){
		static $device = null;
	
		if ($device === null){
			$mobile_detect = $this->getMobileDetect();
			if ($mobile_detect->isTablet())
				$device = JeproshopContext::DEVICE_TABLET;
			elseif ($mobile_detect->isMobile())
			$device = JeproshopContext::DEVICE_MOBILE;
			else
				$device = JeproshopContext::DEVICE_COMPUTER;
		}	
		return $device;
	}
	
	public function getMobileDetect(){
		if ($this->mobile_detect === null){
			require_once(_PS_TOOL_DIR_.'mobile_Detect/Mobile_Detect.php');
			$this->mobile_detect = new Mobile_Detect();
		}
		return $this->mobile_detect;
	}
	
}