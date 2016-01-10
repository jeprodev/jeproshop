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

class Rijndael{
	protected $_key;
	protected $_iv;

	public function __construct($key, $iv){
		$this->_key = $key;
		$this->_iv = base64_decode($iv);
	}

	/**
	 * Base64 is not required, but it is more compact than urlencode
	 *
	 * @param string $plain_text
	 */
	public function encrypt($plain_text){
		$length = (ini_get('mbstring.func_overload') & 2) ? mb_strlen($plain_text, ini_get('default_charset')) : strlen($plain_text);

		if($length >= 1048576) return false;

		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_key, $plain_text, MCRYPT_MODE_ECB, $this->_iv)). sprintf('%06d', $length);
	}

	public function decrypt($cipher_text){
		if(ini_get('mbstring.func_overload') & 2){
			$length = intval(mb_substr($cipher_text, -6, 6, ini_get('default_charset')));
			$cipher_text = mb_substr($cipher_text, 0, -6, ini_get('default_charset'));
			return mb_substr(
					mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_key, base64_decode($cipher_text), MCRYPT_MODE_ECB, $this->_iv),
					0, $length, ini_get('default_charset')
			);
		} else {
			$length = intval(substr($cipher_text, -6));
			$cipher_text = substr($cipher_text, 0, -6);
			return substr(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_key, base64_decode($cipher_text), MCRYPT_MODE_ECB, $this->_iv), 0, $length);
		}
	}
}