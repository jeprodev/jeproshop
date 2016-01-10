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


require_once JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' .DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cipher' .DIRECTORY_SEPARATOR . 'rijndael.php';
require_once JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' .DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cipher' .DIRECTORY_SEPARATOR . 'blowfish.php';

class JeproshopCookie
{
	public $lang_id;
	
	public $employee_id;
	
	public $currency_id;
	
	public $cart_id = null;
	
	public $customer_id = null;
	
	public $guest_id = null;
	
	public $no_mobile;
	
	public $products_filter_category_id;
	
	public $account_created = FALSE;
	
	public $employee_form_lang;

    public $last_activity;

    public $passwd;

    public $remote_addr;
	
	/** @var array Contain cookie content in a key => value format */
	protected $_content;
	
	/** @var array Crypted cookie name */
	protected $_name;
	
	/** @var array expiration date for setcookie() */
	protected $_expire;
	
	/** @var array website domain for setcookie() */
	protected $_domain;
	
	/** @var array Path for setcookie() */
	protected $_path;
	
	/** @var array cipher tool instance  */
	protected $_cipherTool;

	/** @var array cipher tool initialization key */
	protected $_key;
	
	/** @var array cipher tool initialization vector */
	protected $_iv;
	
	protected $_modified = false;
	 
	protected $_allow_writing;
	
	/**
	 * Get data if the cookie exists and else initialize an new one
	 *
	 * @param String $name
	 * @param String $path
	 * @param String $expire
	 * @param String $shared_urls
	 */
	public function __construct($name, $path = '', $expire = null, $shared_urls = null) {
		$this->_content = array();
		$this->_expire = is_null($expire) ? time() + 1728000 : (int)$expire;
		$this->_name = md5(COM_JEPROSHOP_VERSION . $name);
		$this->_path = trim(JeproshopContext::getContext()->shop->physical_uri.$path, '/\\').'/';
		if ($this->_path{0} != '/'){ $this->_path = '/'.$this->_path; }
		$this->_path = rawurlencode($this->_path);
		$this->_path = str_replace('%2F', '/', $this->_path);
		$this->_path = str_replace('%7E', '~', $this->_path);
	
		$this->_key = COM_JEPROSHOP_COOKIE_KEY;
		$this->_iv = COM_JEPROSHOP_COOKIE_IV;
		$this->_domain = $this->getDomain($shared_urls);
		$this->_allow_writing = TRUE;
		if(JeproshopSettingModelSetting::getValue('cipher_algorithm')){
			$this->_cipherTool = new Rijndael(COM_JEPROSHOP_RIJNDAEL_KEY, COM_JEPROSHOP_RIJNDAEL_IV);
		}else{
			$this->_cipherToll = new BlowFish($this->_key, $this->_iv);
		}
		$this->lang_id = JeproshopLanguageModelLanguage::getLanguage((int)  JeproshopSettingModelSetting::getValue('default_lang'))->lang_id;
		$this->update();
	}
	
	public function disallowWriting(){
		$this->_allow_writing = false;
	}
	
	/** --------- SETTERS ------------ **/
	/**
	 * Set expiration date
	 * @param integer $expire Expiration time from now
	 */
	public function setExpire($expire){
		$this->_expire = (int)$expire;
	}
	
	/**
	 * Setcookie according to php version
	 */
	protected function _setCookie($cookie = NULL){
		if($cookie){
			$content = $this->_cipherTool->encrypt($cookie);
			$time = $this->_expire;
		}else{
			$content = 0; $time = 1;
		}
	
		if(PHP_VERSION_ID <= 50200){
			return setcookie($this->_name, $content, $time, $this->_path,  $this->_domain, 0);
		}else{
			return setcookie($this->_name, $content, $time, $this->_path,  $this->_domain, 0, TRUE);
		}
	}
	
	/** Get a family of variables (e.g. "filter_") **/
	public function getFamily($origin) {
		$result = array();
		if(count($this->_content) == 0){
			return $result;
		}
	
		foreach($this->_content as $key => $value){
			if(strcmp($key, $origin, strlen($origin)) == 0){
				$result[$key] = $value;
			}
		}
		return $result;
	}
	
	/**
	 * @return string cookie name
	 */
	public function getName(){
		return $this->_name;
	}
	
	/**
	 * Check if the cookie exists
	 */
	public function exists(){ 
		$cookie = JRequest::get('COOKIE'); 
		return true; //isset($cookie[$this->_name]);
	}
	
	/**
	 *
	 * @return Save cookie with setcookie
	 */
	public function write(){
		if(!$this->_modified || headers_sent() || !$this->_allow_writing){ return; }
	
		$cookie = '';
	
		/** Serialize cookie Content **/
		if(isset($this->_content['checksum'])){
			unset($this->_content['checksum']);
		}
	
		foreach($this->_content as $key => $value){
			$cookie .= $key . '|' . $value . '�';
		}
	
		/** Add checksum to cookie **/
		$cookie .= 'checksum|' . crc32($this->_iv . $cookie);
		$this->_modified = FALSE;
	
		/** Cookies are encrypted for evident security reasons **/
		return $this->_setCookie($cookie);
	}
	
	/** Get cookie content **/
	public function update(){
		$cookie = JRequest::get('COOKIE');
		if(isset($cookie[$this->_name])){
			/** Decrypt cookie content **/
			$content = $this->_cipherTool->decrypt($cookie[$this->_name]);
	
			/** Get cookie checksum **/
			$checksum = crc32($this->_iv . substr($content, 0, strrpos($content, '�') + 2));
	
			/** Unserialize cookie content **/
			$tmpTab = explode('�', $content);
			foreach($tmpTab as $keyAndValue){
				$tmpTab2 = explode('|', $keyAndValue);
				if(count($tmpTab2)== 2){
					$this->_content[$tmpTab2[0]] = $tmpTab2[1];
				}
			}
	
			/** Blowfish fix **/
			if(isset($this->_content['checksum'])){
				$this->_content['checksum'] = (int)($this->_content['checksum']);
			}
	
			/** Check if cookie has not been modified **/
			if(!isset($this->_content['checksum']) || $this->_content['checksum'] != $checksum){
				$this->logOut();
			}
	
			if(!isset($this->_content['date_add'])){
				$this->_content['date_add'] = date('Y-m-d H:i:s');
			}
		} else {
			$this->_content['date_add'] = date('Y-m-d H:i:s');
		}
	
		/** checks if the language exists , if not choose  the default  language **/
		if(!JeproshopLanguageModelLanguage::getLanguage((int)  $this->lang_id)){
			$this->lang_id = JeproshopSettingModelSetting::getValue('default_lang');
		}
	}
	
	/**
	 * Delete cookie
	 */
	public function logout(){
		$cookie = JRequest::get('COOKIE');
		$this->_content = array();
		$this->_setCookie();
		unset($cookie[$this->_name]);
		$this->_modified = TRUE;
	}
	
	/**
	 * Soft logout, delete everything links to the customer
	 * but leave there affiliate's
	 */
	public function myLogout(){
		unset($this->_content['compare_id']);
		unset($this->_content['customer_id']);
		unset($this->_content['guest_id']);
		unset($this->_content['is_guest']);
		unset($this->_content['connections_id']);
		unset($this->_content['customer_lastname']);
		unset($this->_content['customer_firstname']);
		unset($this->_content['passwd']);
		unset($this->_content['logged']);
		unset($this->_content['email']);
		unset($this->_content['cart_id']);
		unset($this->_content['address_invoice_id']);
		unset($this->_content['address_delivery_id']);
		$this->_modified = TRUE;
	}
	
	protected function getDomain($shared_urls = null){
		$out = null;
		$r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
		if(!preg_match($r, JeproshopTools::getHttpHost(false, false), $out) || !isset($out[4])){
			return false;
		}
	
		if (preg_match('/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)'.
				'{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)'.
				'{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/', $out[4])){
				return false;
		}
	
		if(!strstr(JeproshopTools::getHttpHost(false, false), '.')){
			return false;
		}
	
		$domain = false;
	
		if($shared_urls !== null){
			foreach($shared_urls as $shared_url){
				if($shared_url != $out[4]){
					continue;
				}
				$res = null;
				if (preg_match('/^(?:.*\.)?([^.]*(?:.{2,4})?\..{2,3})$/Ui', $shared_url, $res))
				{
					$domain = '.'.$res[1];
					break;
				}
			}
		}
	
		if (!$domain){
			$domain = $out[4];
		}
		return $domain;
	}
	
}