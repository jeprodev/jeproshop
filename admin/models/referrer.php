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

class JeproshopReferrerModelReferrer extends JModelLegacy
{
    public $shop_id;
    public $name;
    public $passwd;

    public $http_referrer_regexp;
    public $http_referrer_like;
    public $request_uri_regexp;
    public $request_uri_like;
    public $http_referrer_regexp_not;
    public $http_referrer_like_not;
    public $request_uri_regexp_not;
    public $request_uri_like_not;

    public $base_fee;
    public $percent_fee;
    public $click_fee;

    public $date_add;
    
    protected static $_join = "(referrer.http_referrer_like IS NULL OR referrer.http_referrer_like = '' OR connection_source.http_referrer LIKE referrer.http_referrer_like)
        AND (referrer.request_uri_like IS NULL OR referrer.request_uri_like = '' OR connection_source.request_uri LIKE referrer.request_uri_like) AND (referrer.http_referrer_like_not
        IS NULL OR referrer.http_referrer_like_not = '' OR connection_source.http_referrer NOT LIKE referrer.http_referrer_like_not) AND (referrer.request_uri_like_not IS NULL OR
        referrer.request_uri_like_not = '' OR connection_source.request_uri NOT LIKE referrer.request_uri_like_not) AND (referrer.http_referrer_regexp IS NULL OR referrer.http_referrer_regexp
        = '' OR connection_source.http_referrer REGEXP referrer.http_referrer_regexp) AND (referrer.request_uri_regexp IS NULL OR referrer.request_uri_regexp = '' OR connection_source.request_uri
        REGEXP referrer.request_uri_regexp) AND (referrer.http_referrer_regexp_not IS NULL OR referrer.http_referrer_regexp_not = '' OR connection_source.http_referrer NOT REGEXP
        referrer.http_referrer_regexp_not) AND (referrer.request_uri_regexp_not IS NULL OR referrer.request_uri_regexp_not = '' OR connection_source.request_uri NOT REGEXP referrer.request_uri_regexp_not)";
    
    
    /**
     * Get list of referrers connections of a customer
     *
     * @param int $customer_id
     */
    public static function getReferrers($customer_id){
    	$db = JFactory::getDBO();
    
    	$query = "SELECT DISTINCT connection.date_add, referrer.name, shop.shop_name AS shop_name FROM " . $db->quoteName('#__jeproshop_guest');
    	$query .= " AS guest LEFT JOIN " . $db->quoteName('#__jeproshop_connection') . " AS connection ON connection.guest_id = guest.guest_id";
    	$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_connection_source') . " AS connection_source ON connection.connection_id ";
    	$query .= "= connection_source.connection_id LEFT JOIN " . $db->quoteName('#__jeproshop_referrer') . " AS referrer ON (" . self::$_join;
    	$query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_shop') . " AS shop ON shop.shop_id = connection.shop_id WHERE guest.customer_id";
    	$query .= " = " . (int)$customer_id . " AND referrer.name IS NOT NULL ORDER BY connection.date_add DESC";
    
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }
}