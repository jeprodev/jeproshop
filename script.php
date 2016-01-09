<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeproshop
 * @link            http://jeprodev.net
 *
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

class com_jeproshopInstallerScript {
    /**
     * method to install the component
     *
     * @param $parent
     * @return void
     */
    function install ($parent){}

    /**
     * method to uninstall the component
     *
     * @param $parent
     * @return void
     */
    function uninstall($parent){}

    /**
     * method to update the component
     *
     * @param $parent
     * @return void
     */
    function update($parent){}

    /**
     * method to perform before install/update/uninstall of the component
     * @param $type
     * @param $parent
     * @return void
     */
    function preflight($type, $parent){}

    /**
     * method to install the component
     * @param $type
     * @param $parent
     * @return void
     */
    function postflight($type, $parent){
        if($type == 'update'){

        }else if($type == 'install') {
            $this->createDefaultShopData();
        }
    }

    private function createDefaultShopData(){
        $db = JFactory::getDBO();
        $config = JFactory::getConfig();
        $default_lang_id = JFactory::getLanguage()->get('lang_id');
        $default_category_id = 0;

        /** inserting default theme **/
        $default_theme_id = 1;

        /** inserting currencies */
        $default_currency_id = 1;

        /** create default carrier */
        $default_carrier_id = 1 ;

        /** inserting countries **/
        $default_country_id = 1;

        /* create default shop **/
        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_shop_group') . "(" . $db->quoteName('name') . ", " . $db->quoteName('share_customer');
        $query .= ", " . $db->quoteName('share_order') . ", " . $db->quoteName('share_stock') . ", "  . $db->quoteName('published') . ", " . $db->quoteName('deleted') ;
        $query .= ") VALUES (" . $db->quote($config->get('sitename')) . ", 0, 0, 0, 1, 0 )";

        $db->setQuery($query);
        $db->query();

        $groupId = $db->insertid();

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_shop') . "(" . $db->quoteName('shop_name') . ", " . $db->quoteName('shop_group_id') . ", ";
        $query .= $db->quoteName('category_id') . ", " . $db->quoteName('theme_id') . ", " . $db->quoteName('published') . ", " . $db->quoteName('deleted');
        $query .= ") VALUES (" . $db->quote($config->get('sitename')) . ", " . (int)$groupId . ", " . (int)$default_category_id . "," . (int)$default_theme_id;
        $query .= ", 1, 0)";

        $db->setQuery($query);
        $db->query();
        $default_shop_id = $db->insertid();

        /*
         * create default root category
         *n /
        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_category') . " (" . $db->quoteName('category_id') . ", ";
        $query .= $db->quoteName('parent_id') . ", " . $db->quoteName('default_shop_id') . ", " . $db->quoteName('depth_level');
        $query .= ", " .$db->quoteName('n_left') . ", " . $db->quoteName('n_right') . ", " . $db->quoteName('published') . ", ";
        $query .= $db->quoteName('date_add') . ", " . $db->quoteName('date_upd') . ", " . $db->quoteName('position') . ", ";
        $query .= $db->quoteName('is_root_category') . ") VALUES (" . $db->quote('1') . ", " . $db->quote('0') . ", " . $db->quote('1');
        $query .= ", " . $db->quote('0') . ", " . $db->quote('0') . ", " . $db->quote('0') . ", " . $db->quote('1') . ", ";
        $query .=  "NOW(), NOW(), " . $db->quote("1") . ", " . $db->quote("1") . ")";

        $db->setQuery($query);
        $db->query();

        $default_category_id = $db->insertid();

        /** settings as 'name, value, group' **/
        $settings = array(
            array('default_lang', $default_lang_id, 'basic'), array('default_currency', $default_currency_id, 'basic'),
            array('default_carrier', $default_carrier_id, 'basic'), array('default_country', $default_country_id, 'basic'),
            array('default_shop', $default_shop_id, 'basic'), array('weight_unit', 'kg', 'basic'), array('volume_unit', 'cl', 'basic'),
            array('distance_unit', 'km', 'basic'), array('rewrite_settings', '0', 'basic'),
            array('unidentified_group', '', 'basic'), array('guest_group', '', 'basic'),
            array('customer_group', '', 'basic'), array('legacy_images', '0', 'basic'),
            array('minimum_purchase', 0, 'cart'), array('cart_redirection', '1', 'cart'),
            array('group_feature_active', 1, 'featuring'), array('cart_rule_feature_active', '0', 'featuring'),
            array('customization_feature_active', '0', 'featuring'), array('pack_feature_active', '0', 'featuring'),
            array('multishop_feature_active', '1', 'featuring'), array('combination_feature_active', '1', 'featuring'),
            array('specific_price_feature_active', '1', 'featuring'), array('cart_rule_feature_active', '', 'featuring'),
            array('virtual_product_feature_active', '1', 'featuring'), array('', '', 'featuring'),
            array('activate_multishop', '0', 'generals'), array('display_suppliers_and_manufacturers', '1', 'generals'),
            array('shop_activity', '0', 'generals'), array('allow_iframes_on_html_fields', '', 'generals'),
            array('increase_front_office_security', '0', 'generals'), array('round_mode', '1', 'generals'),
            array('products_per_page', 20, 'layout'), array('display_quantities', 1, 'layout'),
            array('help_box', 1, 'layout'), array('cart_block_use_ajax', 1, 'layout'),
            array('product_picture_max_size', 8388608, 'layout'), array('enable_jqzoom', 0, 'layout'),
            array('product_picture_height', 64, 'layout'), array('display_bestsellers_block', '0', 'layout'),
            array('display_new_products_block', '0', 'layout'), array('display_specials_block', '0', 'layout'),
            array('show_all_modules', '0', 'layout'), array('product_short_desc_limit', '400', 'layout'),
            array('quick_view', '1', 'layout'), array('nb_days_product_new', '20', 'layout'),
            array('attribute_anchor_separator', '-', 'layout'), array('', '', 'layout'),
            array('attachment_maximum_size', '8', 'layout'), array('display_unavailable_attributes', '0', 'layout'),
            array('default_order_by', 'product_add_date', 'layout'), array('display_add_to_cart_on_product_with_attributes', '1', 'layout'),
            array('default_order_way', 'ASC', 'layout'), array('default_sort_way', 'position_in_category', 'layout'),
            array('return_order', 0, 'order'), array('return_order_nb_days', 7, 'order'),
            array('order_invoice_number', '1', 'order'), array('order_delivery_number', '2', 'order'),
            array('order_invoice_prefix', 'IN', 'order'), array('order_delivery_prefix', 'DE', 'order'),
            array('order_out_of_stock', '0', 'order'), array('order_process_type', 'standard', 'order'),
            array('quantity_discount_based_on', 'products', 'order'), array('allow_out_of_stock_ordering', '0', 'order'),
            array('offer_gift_wrapping', '0', 'order'), array('gift_wrapping_price', '2', 'order'),
            array('gift_wrapping_tax', '0', 'order'), array('offer_recycled_wrapping', '1', 'order'),
            array('specific_price_priorities', 'shop_id;currency_id;country_id;group_id', 'price'), array('', '', 'price'),
            array('price_round_mode', '1', 'price'), array('qty_discount_on_combination', '0', 'price'),
            array('display_discount_price', '1', 'price'), array('number_days_new_product', 20, 'product'), array('display_category_attribute', '1', 'product'),
            array('shipping_handling', '2', 'shipping'), array('shipping_free_price', '0', 'shipping'),
            array('shipping_free_method', '1', 'shipping'), array('shipping_free_weight', '0', 'shipping'),
            array('improve_front_safety', '0', 'system'), array('number_of_decimals', '', 'shipping'),
            array('stock_management', 1, 'stock'), array('advanced_stock_management', '1', 'stock'),
            array('last_quantities', '5', 'stock'), array('default_stock_mvt_reason', '3', 'stock'),
            array('display_remaining_quantity', '1', 'stock'), array('display_available_quantity', '1', 'stock'),
            array('use_advanced_stock_management_on_new_product', '1', 'stock'),
            array('cipher_algorithm', '1', 'system'), array('search_min_word_length', '3', 'system'),
            array('search_ajax', '1', 'system'), array('price_round_mode', '1', 'system'),
            array('bo_life_time', '360', 'system'), array('enable_ssl', '1', 'system'),
            array('fo_life_time', '360', 'system'), array('enable_geolocation', '1', 'system'),
            array('comparator_max_item', '4', 'system'), array('allow_mobile_device', '1', 'system'),
            array('vat_number_management', '1', 'system'), array('enable_b2b_mode', '1', 'system'),
            array('allow_accented_chars_url', '0', 'system'), array('canonical_redirect', '', 'system'),
            array('redirect_after_adding_product_to_cart', 'previous_page', 'system'), array('allowed_countries', 'AF;ZA;AX;AL;DZ;DE;AD;AO;AI;AQ;AG;AN;SA;AR;AM;AW;AU;AT;AZ;BS;BH;BD;BB;BY;BE;BZ;BJ;BM;BT;BO;BA;BW;BV;BR;BN;BG;BF;MM;BI;KY;KH;CM;CA;CV;CF;CL;CN;CX;CY;CC;CO;KM;CG;CD;CK;KR;KP;CR;CI;HR;CU;DK;DJ;DM;EG;IE;SV;AE;EC;ER;ES;EE;ET;FK;FO;FJ;FI;FR;GA;GM;GE;GS;GH;GI;GR;GD;GL;GP;GU;GT;GG;GN;GQ;GW;GY;GF;HT;HM;HN;HK;HU;IM;MU;VG;VI;IN;ID;IR;IQ;IS;IL;IT;JM;JP;JE;JO;KZ;KE;KG;KI;KW;LA;LS;LV;LB;LR;LY;LI;LT;LU;MO;MK;MG;MY;MW;MV;ML;MT;MP;MA;MH;MQ;MR;YT;MX;FM;MD;MC;MN;ME;MS;MZ;NA;NR;NP;NI;NE;NG;NU;NF;NO;NC;NZ;IO;OM;UG;UZ;PK;PW;PS;PA;PG;PY;NL;PE;PH;PN;PL;PF;PR;PT;QA;DO;CZ;RE;RO;GB;RU;RW;EH;BL;KN;SM;MF;PM;VA;VC;LC;SB;WS;AS;ST;SN;RS;SC;SL;SG;SK;SI;SO;SD;LK;SE;CH;SR;SJ;SZ;SY;TJ;TW;TZ;TD;TF;TH;TL;TG;TK;TO;TT;TN;TM;TC;TR;TV;UA;UY;US;VU;VE;VN;WF;YE;ZM;ZW', 'system'),
            array('force_update_of_friendly_url', '1', 'system'), array('enable_guest_checkout', '0', 'system'),
            array('delayed_shipping', '0', 'system'), array('terms_of_services', '', 'system'),
            array('conditions_of_use', '', 'system'), array('registration_process_type', '', 'system'),
            array('customer_phone_number', '1', 'system'), array('display_cart_on_login', '1', 'system'),
            array('send_email_after_registration', '1', 'system'), array('regenerate_password', '1', 'system'),
            array('geolocation_behavior', '1', 'system'), array('other_countries_behavior', '-1', 'system'),
            array('geolocation_white_list', '127;209.185.108;209.185.253;209.85.238;209.85.238.11;209.85.238.4;216.239.33.96;216.239.33.97;216.239.33.98;216.239.33.99;216.239.37.98;216.239.37.99;216.239.39.98;216.239.39.99;216.239.41.96;216.239.41.97;216.239.41.98;216.239.41.99;216.239.45.4;216.239.46;216.239.51.96;216.239.51.97;216.239.51.98;216.239.51.99;216.239.53.98;216.239.53.99;216.239.57.96;216.239.57.97;216.239.57.98;216.239.57.99;216.239.59.98;216.239.59.99;216.33.229.163;64.233.173.193;64.233.173.194;64.233.173.195;64.233.173.196;64.233.173.197;64.233.173.198;64.233.173.199;64.233.173.200;64.233.173.201;64.233.173.202;64.233.173.203;64.233.173.204;64.233.173.205;64.233.173.206;64.233.173.207;64.233.173.208;64.233.173.209;64.233.173.210;64.233.173.211;64.233.173.212;64.233.173.213;64.233.173.214;64.233.173.215;64.233.173.216;64.233.173.217;64.233.173.218;64.233.173.219;64.233.173.220;64.233.173.221;64.233.173.222;64.233.173.223;64.233.173.224;64.233.173.225;64.233.173.226;64.233.173.227;64.233.173.228;64.233.173.229;64.233.173.230;64.233.173.231;64.233.173.232;64.233.173.233;64.233.173.234;64.233.173.235;64.233.173.236;64.233.173.237;64.233.173.238;64.233.173.239;64.233.173.240;64.233.173.241;64.233.173.242;64.233.173.243;64.233.173.244;64.233.173.245;64.233.173.246;64.233.173.247;64.233.173.248;64.233.173.249;64.233.173.250;64.233.173.251;64.233.173.252;64.233.173.253;64.233.173.254;64.233.173.255;64.68.80;64.68.81;64.68.82;64.68.83;64.68.84;64.68.85;64.68.86;64.68.87;64.68.88;64.68.89;64.68.90.1;64.68.90.10;64.68.90.11;64.68.90.12;64.68.90.129;64.68.90.13;64.68.90.130;64.68.90.131;64.68.90.132;64.68.90.133;64.68.90.134;64.68.90.135;64.68.90.136;64.68.90.137;64.68.90.138;64.68.90.139;64.68.90.14;64.68.90.140;64.68.90.141;64.68.90.142;64.68.90.143;64.68.90.144;64.68.90.145;64.68.90.146;64.68.90.147;64.68.90.148;64.68.90.149;64.68.90.15;64.68.90.150;64.68.90.151;64.68.90.152;64.68.90.153;64.68.90.154;64.68.90.155;64.68.90.156;64.68.90.157;64.68.90.158;64.68.90.159;64.68.90.16;64.68.90.160;64.68.90.161;64.68.90.162;64.68.90.163;64.68.90.164;64.68.90.165;64.68.90.166;64.68.90.167;64.68.90.168;64.68.90.169;64.68.90.17;64.68.90.170;64.68.90.171;64.68.90.172;64.68.90.173;64.68.90.174;64.68.90.175;64.68.90.176;64.68.90.177;64.68.90.178;64.68.90.179;64.68.90.18;64.68.90.180;64.68.90.181;64.68.90.182;64.68.90.183;64.68.90.184;64.68.90.185;64.68.90.186;64.68.90.187;64.68.90.188;64.68.90.189;64.68.90.19;64.68.90.190;64.68.90.191;64.68.90.192;64.68.90.193;64.68.90.194;64.68.90.195;64.68.90.196;64.68.90.197;64.68.90.198;64.68.90.199;64.68.90.2;64.68.90.20;64.68.90.200;64.68.90.201;64.68.90.202;64.68.90.203;64.68.90.204;64.68.90.205;64.68.90.206;64.68.90.207;64.68.90.208;64.68.90.21;64.68.90.22;64.68.90.23;64.68.90.24;64.68.90.25;64.68.90.26;64.68.90.27;64.68.90.28;64.68.90.29;64.68.90.3;64.68.90.30;64.68.90.31;64.68.90.32;64.68.90.33;64.68.90.34;64.68.90.35;64.68.90.36;64.68.90.37;64.68.90.38;64.68.90.39;64.68.90.4;64.68.90.40;64.68.90.41;64.68.90.42;64.68.90.43;64.68.90.44;64.68.90.45;64.68.90.46;64.68.90.47;64.68.90.48;64.68.90.49;64.68.90.5;64.68.90.50;64.68.90.51;64.68.90.52;64.68.90.53;64.68.90.54;64.68.90.55;64.68.90.56;64.68.90.57;64.68.90.58;64.68.90.59;64.68.90.6;64.68.90.60;64.68.90.61;64.68.90.62;64.68.90.63;64.68.90.64;64.68.90.65;64.68.90.66;64.68.90.67;64.68.90.68;64.68.90.69;64.68.90.7;64.68.90.70;64.68.90.71;64.68.90.72;64.68.90.73;64.68.90.74;64.68.90.75;64.68.90.76;64.68.90.77;64.68.90.78;64.68.90.79;64.68.90.8;64.68.90.80;64.68.90.9;64.68.91;64.68.92;66.249.64;66.249.65;66.249.66;66.249.67;66.249.68;66.249.69;66.249.70;66.249.71;66.249.72;66.249.73;66.249.78;66.249.79;72.14.199;8.6.48', 'system'),
            array('force_friendly_product', '1', 'system'),
            array('new_product_default_warehouse', '1', 'system'), array('', '', 'system'),
            array('use_tax', 1, 'tax'), array('display_tax', '0', 'tax'),
            array('use_eco_tax', '0', 'tax'), array('ecotax_tax_rules_group_id', '0', 'tax')
        );

        foreach ($settings as $setting){
            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_setting') . " ( " . $db->quoteName('setting_id') . ", ";
            $query .= $db->quoteName('name') . ", " . $db->quoteName('value') . ", " . $db->quoteName('setting_group');
            $query .= " ) VALUES (NULL, " . $db->quote($setting[0]) . ", " . $db->quote($setting[1]) . ", " . $db->quote($setting[2]) . ")";

            $db->seQuery($query);
            $db->query();
        }
    }
}