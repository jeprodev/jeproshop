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

define('PS_SEARCH_MAX_WORD_LENGTH', 15);

/* Copied from Drupal search module, except for \x{0}-\x{2f} that has been replaced by \x{0}-\x{2c}\x{2e}-\x{2f} in order to keep the char '-' */
define('PREG_CLASS_SEARCH_EXCLUDE',
'\x{0}-\x{2c}\x{2e}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{bf}\x{d7}\x{f7}\x{2b0}-'.
		'\x{385}\x{387}\x{3f6}\x{482}-\x{489}\x{559}-\x{55f}\x{589}-\x{5c7}\x{5f3}-'.
				'\x{61f}\x{640}\x{64b}-\x{65e}\x{66a}-\x{66d}\x{670}\x{6d4}\x{6d6}-\x{6ed}'.
						'\x{6fd}\x{6fe}\x{700}-\x{70f}\x{711}\x{730}-\x{74a}\x{7a6}-\x{7b0}\x{901}-'.
								'\x{903}\x{93c}\x{93e}-\x{94d}\x{951}-\x{954}\x{962}-\x{965}\x{970}\x{981}-'.
										'\x{983}\x{9bc}\x{9be}-\x{9cd}\x{9d7}\x{9e2}\x{9e3}\x{9f2}-\x{a03}\x{a3c}-'.
												'\x{a4d}\x{a70}\x{a71}\x{a81}-\x{a83}\x{abc}\x{abe}-\x{acd}\x{ae2}\x{ae3}'.
														'\x{af1}-\x{b03}\x{b3c}\x{b3e}-\x{b57}\x{b70}\x{b82}\x{bbe}-\x{bd7}\x{bf0}-'.
																'\x{c03}\x{c3e}-\x{c56}\x{c82}\x{c83}\x{cbc}\x{cbe}-\x{cd6}\x{d02}\x{d03}'.
																		'\x{d3e}-\x{d57}\x{d82}\x{d83}\x{dca}-\x{df4}\x{e31}\x{e34}-\x{e3f}\x{e46}-'.
		'\x{e4f}\x{e5a}\x{e5b}\x{eb1}\x{eb4}-\x{ebc}\x{ec6}-\x{ecd}\x{f01}-\x{f1f}'.
		'\x{f2a}-\x{f3f}\x{f71}-\x{f87}\x{f90}-\x{fd1}\x{102c}-\x{1039}\x{104a}-'.
		'\x{104f}\x{1056}-\x{1059}\x{10fb}\x{10fc}\x{135f}-\x{137c}\x{1390}-\x{1399}'.
		'\x{166d}\x{166e}\x{1680}\x{169b}\x{169c}\x{16eb}-\x{16f0}\x{1712}-\x{1714}'.
		'\x{1732}-\x{1736}\x{1752}\x{1753}\x{1772}\x{1773}\x{17b4}-\x{17db}\x{17dd}'.
		'\x{17f0}-\x{180e}\x{1843}\x{18a9}\x{1920}-\x{1945}\x{19b0}-\x{19c0}\x{19c8}'.
		'\x{19c9}\x{19de}-\x{19ff}\x{1a17}-\x{1a1f}\x{1d2c}-\x{1d61}\x{1d78}\x{1d9b}-'.
		'\x{1dc3}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fcd}-\x{1fcf}\x{1fdd}-\x{1fdf}\x{1fed}-'.
		'\x{1fef}\x{1ffd}-\x{2070}\x{2074}-\x{207e}\x{2080}-\x{2101}\x{2103}-\x{2106}'.
		'\x{2108}\x{2109}\x{2114}\x{2116}-\x{2118}\x{211e}-\x{2123}\x{2125}\x{2127}'.
		'\x{2129}\x{212e}\x{2132}\x{213a}\x{213b}\x{2140}-\x{2144}\x{214a}-\x{2b13}'.
		'\x{2ce5}-\x{2cff}\x{2d6f}\x{2e00}-\x{3005}\x{3007}-\x{303b}\x{303d}-\x{303f}'.
		'\x{3099}-\x{309e}\x{30a0}\x{30fb}\x{30fd}\x{30fe}\x{3190}-\x{319f}\x{31c0}-'.
		'\x{31cf}\x{3200}-\x{33ff}\x{4dc0}-\x{4dff}\x{a015}\x{a490}-\x{a716}\x{a802}'.
		'\x{e000}-\x{f8ff}\x{fb29}\x{fd3e}-\x{fd3f}\x{fdfc}-\x{fdfd}'.
		'\x{fd3f}\x{fdfc}-\x{fe6b}\x{feff}-\x{ff0f}\x{ff1a}-\x{ff20}\x{ff3b}-\x{ff40}'.
		'\x{ff5b}-\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}');

define('PREG_CLASS_NUMBERS',
'\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}'.
		'\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}'.
				'\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}'.
						'\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-'.
								'\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}'.
										'\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}'.
												'\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}'.
														'\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-'.
														'\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}');

define('PREG_CLASS_PUNCTUATION',
'\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}'.
		'\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}'.
				'\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}'.
						'\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}'.
								'\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}'.
										'\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}'.
												'\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}'.
														'\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}'.
																'\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-'.
																		'\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}'.
																				'\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}'.
																						'\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}'.
																								'\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}'.
																										'\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-'.
																										'\x{ff65}');

/**
 * Matches all CJK characters that are candidates for auto-splitting
 * (Chinese, Japanese, Korean).
 * Contains kana and BMP ideographs.
*/
define('PREG_CLASS_CJK', '\x{3041}-\x{30ff}\x{31f0}-\x{31ff}\x{3400}-\x{4db5}\x{4e00}-\x{9fbb}\x{f900}-\x{fad9}');


class JeproshopSearch
{
	public static function sanitize($string, $id_lang, $indexation = false, $iso_code = false)
	{
		$string = trim($string);
		if (empty($string))
			return '';
	
		$string = strtolower(strip_tags($string));
		$string = html_entity_decode($string, ENT_NOQUOTES, 'utf-8');
	
		$string = preg_replace('/(['.PREG_CLASS_NUMBERS.']+)['.PREG_CLASS_PUNCTUATION.']+(?=['.PREG_CLASS_NUMBERS.'])/u', '\1', $string);
		$string = preg_replace('/['.PREG_CLASS_SEARCH_EXCLUDE.']+/u', ' ', $string);
	
		if ($indexation)
			$string = preg_replace('/[._-]+/', ' ', $string);
		else{
			$string = preg_replace('/[._]+/', '', $string);
			$string = ltrim(preg_replace('/([^ ])-/', '$1 ', ' '.$string));
			$string = preg_replace('/[._]+/', '', $string);
			$string = preg_replace('/[^\s]-+/', '', $string);
		}
	
		$blacklist = strtolower(JeproshopSettingModelSetting::getValue('search_blacklist'));
		if (!empty($blacklist)){
			$string = preg_replace('/(?<=\s)('.$blacklist.')(?=\s)/Su', '', $string);
			$string = preg_replace('/^('.$blacklist.')(?=\s)/Su', '', $string);
			$string = preg_replace('/(?<=\s)('.$blacklist.')$/Su', '', $string);
			$string = preg_replace('/^('.$blacklist.')$/Su', '', $string);
		}
	
		if (!$indexation){
			$words = explode(' ', $string);
			$processed_words = array();
			// search for aliases for each word of the query
			foreach ($words as $word)
			{
				$alias = new Alias(null, $word);
				if (JeproshopValidator::isLoadedObject($alias, ''))
					$processed_words[] = $alias->search;
				else
					$processed_words[] = $word;
			}
			$string = implode(' ', $processed_words);
		}
	
		// If the language is constituted with symbol and there is no "words", then split every chars
		if (in_array($iso_code, array('zh', 'tw', 'ja')) && function_exists('mb_strlen'))
		{
			// Cut symbols from letters
			$symbols = '';
			$letters = '';
			foreach (explode(' ', $string) as $mb_word)
				if (strlen(Tools::replaceAccentedChars($mb_word)) == mb_strlen(Tools::replaceAccentedChars($mb_word)))
					$letters .= $mb_word.' ';
				else
					$symbols .= $mb_word.' ';
	
				if (preg_match_all('/./u', $symbols, $matches))
					$symbols = implode(' ', $matches[0]);
	
				$string = $letters.$symbols;
		}elseif ($indexation){
			$minWordLen = (int)JeproshopSettingModelSetting::getValue('search_min_word_length');
			if ($minWordLen > 1){
				$minWordLen -= 1;
				$string = preg_replace('/(?<=\s)[^\s]{1,'.$minWordLen.'}(?=\s)/Su', ' ', $string);
				$string = preg_replace('/^[^\s]{1,'.$minWordLen.'}(?=\s)/Su', '', $string);
				$string = preg_replace('/(?<=\s)[^\s]{1,'.$minWordLen.'}$/Su', '', $string);
				$string = preg_replace('/^[^\s]{1,'.$minWordLen.'}$/Su', '', $string);
			}
		}
	
		$string = trim(preg_replace('/\s+/', ' ', $string));
		return $string;
	}
	
	public static function find($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position',
			$order_way = 'desc', $ajax = false, $use_cookie = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
	
		// TODO : smart page management
		if ($page_number < 1) $page_number = 1;
		if ($page_size < 1) $page_size = 1;
	
		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			return false;
	
		$intersect_array = array();
		$score_array = array();
		$words = explode(' ', Search::sanitize($expr, $id_lang, false, $context->language->iso_code));
	
		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('search_min_word_length'))
			{
				$word = str_replace('%', '\\%', $word);
				$word = str_replace('_', '\\_', $word);
				$intersect_array[] = 'SELECT si.id_product
					FROM '._DB_PREFIX_.'search_word sw
					LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = '.(int)$id_lang.'
						AND sw.id_shop = '.$context->shop->id.'
						AND sw.word LIKE
					'.($word[0] == '-'
								? ' \''.pSQL(Tools::substr($word, 1, PS_SEARCH_MAX_WORD_LENGTH)).'%\''
								: '\''.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).'%\''
						);
	
				if ($word[0] != '-')
					$score_array[] = 'sw.word LIKE \''.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).'%\'';
			}
		else
			unset($words[$key]);
	
		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));
	
		$score = '';
		if (count($score_array))
			$score = ',(
				SELECT SUM(weight)
				FROM '._DB_PREFIX_.'search_word sw
				LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
				WHERE sw.id_lang = '.(int)$id_lang.'
					AND sw.id_shop = '.$context->shop->id.'
					AND si.id_product = p.id_product
					AND ('.implode(' OR ', $score_array).')
			) position';
	
		$sql_groups = '';
		if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$sql_groups = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
		}
	
		$results = $db->executeS('
		SELECT cp.`id_product`
		FROM `'._DB_PREFIX_.'category_product` cp
		'.(Group::isFeatureActive() ? 'INNER JOIN `'._DB_PREFIX_.'category_group` cg ON cp.`id_category` = cg.`id_category`' : '').'
		INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
		INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
		'.Shop::addSqlAssociation('product', 'p', false).'
		WHERE c.`active` = 1
		AND product_shop.`active` = 1
		AND product_shop.`visibility` IN ("both", "search")
		AND product_shop.indexed = 1
		'.$sql_groups);
	
		$eligible_products = array();
		foreach ($results as $row)
			$eligible_products[] = $row['id_product'];
		foreach ($intersect_array as $query)
		{
			$eligible_products2 = array();
			foreach ($db->executeS($query) as $row)
				$eligible_products2[] = $row['id_product'];
	
			$eligible_products = array_intersect($eligible_products, $eligible_products2);
			if (!count($eligible_products))
				return ($ajax ? array() : array('total' => 0, 'result' => array()));
		}
	
		$eligible_products = array_unique($eligible_products);
	
		$product_pool = '';
		foreach ($eligible_products as $id_product)
			if ($id_product)
				$product_pool .= (int)$id_product.',';
			if (empty($product_pool))
				return ($ajax ? array() : array('total' => 0, 'result' => array()));
			$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));
	
			if ($ajax)
			{
				$sql = 'SELECT DISTINCT p.id_product, pl.name pname, cl.name cname,
						cl.link_rewrite crewrite, pl.link_rewrite prewrite '.$score.'
					FROM '._DB_PREFIX_.'product p
					INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
					)
					'.Shop::addSqlAssociation('product', 'p').'
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (
						product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
					)
					WHERE p.`id_product` '.$product_pool.'
					ORDER BY position DESC LIMIT 10';
				return $db->executeS($sql);
			}
	
			if (strpos($order_by, '.') > 0)
			{
				$order_by = explode('.', $order_by);
				$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
			}
			$alias = '';
			if ($order_by == 'price')
				$alias = 'product_shop.';
			else if ($order_by == 'date_upd')
				$alias = 'p.';
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name '.$score.', MAX(product_attribute_shop.`id_product_attribute`) id_product_attribute,
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						NOW(),
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 new
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa	ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
					Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				WHERE p.`id_product` '.$product_pool.'
				GROUP BY product_shop.id_product
				'.($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '').'
				LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;
			$result = $db->executeS($sql);
	
			$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` '.$product_pool;
			$total = $db->getValue($sql);
	
			if (!$result)
				$result_properties = false;
			else
				$result_properties = Product::getProductsProperties((int)$id_lang, $result);
	
			return array('total' => $total,'result' => $result_properties);
	}
	
	public static function getTags($db, $product_id, $lang_id){
		$tags = '';
		$query = "SELECT tag.name FROM " . $db->quoteName('#__jeproshop_product_tag') . " AS product_tag LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_tag') . " AS tag ON (product_tag.tag_id = tag.tag_id AND tag.lang_id = ";
		$query .= (int)$lang_id.") WHERE product_tag.product_id = " .(int)$product_id;
		$db->setQuery($query);
		$tagsArray = $db->loadObjectList();
		foreach ($tagsArray as $tag)
			$tags .= $tag->name .' ';
		return $tags;
	}
	
	public static function getAttributes($db, $product_id, $lang_id){
		if (!JeproshopCombinationModelCombination::isFeaturePublished())
			return '';
	
		$attributes = '';
		$query = "SELECT attribute_lang.name FROM " . $db->quoteName('#__jeproshop_product_attribute') . " AS product_attribute ";
		$query .= "INNER JOIN " . $db->quoteName('#__jeproshop_product_attribute_combination') . " AS _product_attribute_combination";
		$query .= " ON product_attribute.product_attribute_id = product_attribute_combination.product_attribute_id INNER JOIN " ;
		$query .= $db->quoteName('#__jeproshop_attribute_lang') . " AS attribute_lang ON (product_attribute_combination.attribute_id";
		$query .= " = attribute_lang.attribute_id AND attribute_lang.lang_id = " .(int)$lang_id . ") " . JeproshopShopModelShop::addSqlAssociation('product_attribute');
		$query .= "	WHERE product_attribute.product_id = " .(int)$product_id ;
		$attributesArray = $db->loadObjectList();
		foreach ($attributesArray as $attribute)
			$attributes .= $attribute->name .' ';
		return $attributes;
	}
	
	public static function getFeatures($db, $product_id, $lang_id){
		if (!JeproshopFeatureModelFeature::isFeaturePublished()){ return ''; }
	
		$features = '';
		$query = "SELECT feature_value_lang.value FROM " . $db->quoteName('#__jeproshop_product_feature') . " AS product_feature LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_feature_value_lang') . " AS feature_value_lang ON (product_feature.feature_value_id = ";
		$query .= "feature_value_lang.feature_value_id AND feature_value_lang.lang_id = " .(int)$lang_id.") WHERE product_feature.";
		$query .= "product_id = ".(int)$product_id;
		$featuresArray = $db->loadObjectList();
		foreach ($featuresArray as $feature)
			$features .= $feature->value .' ';
		return $features;
	}
	
	protected static function getProductsToIndex($total_languages, $product_id = false, $limit = 50, $weight_array = array()){
		// Adjust the limit to get only "whole" products, in every languages (and at least one)
		$max_possibilities = $total_languages * count(JeproshopShopModelShop::getShops(true));
		$limit = max($max_possibilities, floor($limit / $max_possibilities) * $max_possibilities);
		$db = JFactory::getDBO();
		$query = "SELECT product.product_id, product_lang.lang_id, product_lang.shop_id, language.sef";
	
		if (is_array($weight_array)){
			foreach($weight_array as $key => $weight){
				if ((int)$weight){
					switch($key){
						case 'product_name':
							$query .= ", product_lang.name product_name";
							break;
						case 'reference':
							$query .= ", product.reference, product_attribute.reference AS product_attribute_reference";
							break;
						case 'ean13':
							$query .= ", product.ean13";
							break;
						case 'upc':
							$query .= ", product.upc";
							break;
						case 'short_description':
							$query .= ", product_lang.short_description";
							break;
						case 'description':
							$query .= ", product_lang.description";
							break;
						case 'category_name':
							$query .= ", category_lang.name AS category_name";
							break;
						case 'manufacturer_name':
							$query .= ", manufacturer.name AS manufacturer_name";
							break;
					}
				}
			}
		}
		$query .= " FROM " . $db->quoteName('#__jeproshop_product') . " AS product LEFT JOIN " . $db->quoteName('#__jeproshop_product_attribute');
		$query .= " AS product_attribute ON product_attribute.product_id = product.product_id LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON product.product_id = product_lang.product_id " ;
		$query .= JeproshopShopModelShop::addSqlAssociation('product') . "	LEFT JOIN " . $db->quoteName('#__jeproshop_category_lang') . " AS ";
		$query .= " category_lang ON (category_lang.category_id = product_shop.default_category_id AND product_lang.lang_id = category_lang.";
		$query .= "lang_id AND category_lang.shop_id = product_shop.shop_id ) LEFT JOIN " . $db->quoteName('#__jeproshop_manufacturer');
		$query .= " AS manufacturer ON manufacturer.manufacturer_id = product.manufacturer_id LEFT JOIN " . $db->quoteName('#__languages') ;
		$query .= " AS language ON language.lang_id = product_lang.lang_id WHERE product_shop.indexed = 0 AND product_shop.visibility IN";
		$query .= " (\"both\", \"search\") " .($product_id ? "AND product.product_id = " .(int)$product_id : "") . " AND product_shop.";
		$query .= $db->quoteName('published') . " = 1 LIMIT " . (int)$limit;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function indexation($full = false, $product_id = false){
		$db = JFactory::getDBO();
	
		if($product_id){ $full = false; }
	
		if($full){
			$db->execute('TRUNCATE ' . $db->quoteName('#__jeproshop_search_index'));
			$db->execute('TRUNCATE ' . $db->quoteName('#__jeproshop_search_word'));
			self::updateMultishopTable('Product', array('indexed' => 0));
		}else{
			// Do it even if you already know the product id in order to be sure that it exists and it needs to be indexed
			$query = "SELECT product.product_id FROM " . $db->quoteName('#__jeproshop_product') . " AS product ";
			$query .= JeproshopShopModelShop::addSqlAssociation('product'). " WHERE product_shop.visibility IN (\"both\", \"search\") ";
			$query .= " AND product_shop." . $db->quoteName('published') . " = 1 AND ";
			$query .= ($product_id ? "product.product_id = " .(int)$product_id : "product_shop.indexed = 0");
			
			$db->setQuery($query);
			$products = $db->loadObjectList();
	
			$ids = array();
			if ($products){
				foreach ($products as $product){
					$ids[] = (int)$product->product_id;
				}
			}
			
			if (count($ids)){
				$query = "DELETE FROM ". $db->quoteName('#__jeproshop_search_index') . " WHERE product_id IN (" .implode(',', $ids). ")";
				$db->setQuery($query);
				$db->query();
				$data = " SET product." . $db->quoteName('indexed') . " = 0";
				$where = ' WHERE product.product_id IN ('.implode(',', $ids).')';
				JeproshopProductModelProduct::updateMultishopTable($data, $where, '', true);
			}
		}
	
		// Every fields are weighted according to the configuration in the backend
		$weight_array = array(
			'product_name' => JeproshopSettingModelSetting::getValue('search_weight_product_name'),
			'reference' => JeproshopSettingModelSetting::getValue('search_weight_reference'),
			'product_attribute_reference' => JeproshopSettingModelSetting::getValue('search_weight_reference'),
			'ean13' => JeproshopSettingModelSetting::getValue('search_weight_reference'),
			'upc' => JeproshopSettingModelSetting::getValue('search_weight_reference'),
			'short_description' => JeproshopSettingModelSetting::getValue('search_weight_short_description'),
			'description' => JeproshopSettingModelSetting::getValue('search_weight_description'),
			'category_name' => JeproshopSettingModelSetting::getValue('search_weight_category_name'),
			'manufacture_name' => JeproshopSettingModelSetting::getValue('search_weight_manufacturer_name'),
			'tag' => JeproshopSettingModelSetting::getValue('search_weight_tag'),
			'attributes' => JeproshopSettingModelSetting::getValue('search_weight_attribute'),
			'features' => JeproshopSettingModelSetting::getValue('search_weight_feature')
		);
	
		// Those are kind of global variables required to save the processed data in the database every X occurrences, in order to avoid overloading MySQL
		$count_words = 0;
		$query_array3 = array();
	
		// Every indexed words are cached into a PHP array
		$query = "SELECT word_id, word, lang_id, shop_id FROM " . $db->quoteName('#__jeproshop_search_word') ; 
		
		$db->setQuery($query);
		$word_ids = $db->loadObjectList();
		$word_ids_by_word = array();
		while ($word_id = $db->nextRow($word_ids)){
			if (!isset($word_ids_by_word[$word_id->shop_id][$word_id->lang_id])){
				$word_ids_by_word[$word_id['id_shop']][$word_id['id_lang']] = array();
			}
			$word_ids_by_word[$word_id->shop_id][$word_id->lang_id]['_'.$word_id->word] = (int)$word_id->word_id;
		}
	
		// Retrieve the number of languages
		$total_languages = count(JeproshopLanguageModelLanguage::getLanguages(false));
	
		// Products are processed 50 by 50 in order to avoid overloading MySQL
		while (($products = JeproshopSearch::getProductsToIndex($total_languages, $product_id, 50, $weight_array)) && (count($products) > 0)){
			$products_array = array();
			// Now each non-indexed product is processed one by one, langage by langage
			foreach ($products as $product)	{
				if ((int)$weight_array['tag'])
					$product->tags = JeproshopSearch::getTags($db, (int)$product->product_id, (int)$product->lang_id);
				if ((int)$weight_array['attributes'])
					$product->attributes = JeproshopSearch::getAttributes($db, (int)$product->product_id, (int)$product->lang_id);
				if ((int)$weight_array['features'])
					$product->features = JeproshopSearch::getFeatures($db, (int)$product->product_id, (int)$product->lang_id);
	
				// Data must be cleaned of html, bad characters, spaces and anything, then if the resulting words are long enough, they're added to the array
				$product_array = array();
				foreach ($product as $key => $value){
					if (strncmp($key, 'id_', 3) && isset($weight_array[$key])){
						$words = explode(' ', JeproshopSearch::sanitize($value, (int)$product->lang_id, true, JeproshopContext::getContext()->language->iso_code));
						foreach ($words as $word){
							if (!empty($word)){
								$word = substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH);
								// Remove accents
								$word = JeproshopValidator::replaceAccentedChars($word);
	
								if (!isset($product_array[$word]))
									$product_array[$word] = 0;
								$product_array[$word] += $weight_array[$key];
							}
						}
					}
				}
	
				// If we find words that need to be indexed, they're added to the word table in the database
				if (count($product_array)){
					$query_array = $query_array2 = array();
					foreach ($product_array as $word => $weight){
						if ($weight && !isset($word_ids_by_word['_'.$word])){
							$query_array[$word] = '('.(int)$product->lang_id .', '.(int)$product->shop_id .', '. $db->quote($word).')';
							$query_array2[] = $db->quote($word);
							$word_ids_by_word[$product->shop_id][$product->lang_id]['_'.$word] = 0;
						}
					}
					
					if ($query_array2){
						$query = "SELECT DISTINCT word FROM " . $db->quoteName('#__jeproshop_search_word') . " WHERE word IN (";
						$query .= implode(',', $query_array2). ") AND lang_id = " .(int)$product->lang_id . " AND shop_id = " . (int)$product->shop_id;
						$db->setQuery($query);
						$existing_words = $db->loadObjectList();
	
						foreach ($existing_words as $data)
							unset($query_array[JeproshopValidator::replaceAccentedChars($data->word)]);
					}
	
					if (count($query_array)){
						// The words are inserted...
						$query = "INSERT IGNORE INTO " . $db->quoteName('#__jeproshop_search_word') . " (lang_id, shop_id, word) VALUES ".implode(',', $query_array);
						$db->setQuery($query);
						$db->query();
					}
					
					if (count($query_array2)){
						// ...then their IDs are retrieved and added to the cache
						$query = "SELECT search_word.word_id, search_word.word FROM " . $db->quoteName('#__jeproshop_search_word') . " AS search_word ";
						$query .= " WHERE search_word.word IN (" . implode(',', $query_array2). ") AND search_word.lang_id = " . (int)$product->lang_id;
						$query .= " AND search_word.shop_id = " .(int)$product->shop_id . " LIMIT " .count($query_array2);
						$db->setQuery($query);
						$added_words = $db->loadObjectList();
						// replace accents from the retrieved words so that words without accents or with differents accents can still be linked
						foreach ($added_words as $word_id)
							$word_ids_by_word[$product->shop_id][$product->lang_id]['_'.JeproshopValidator::replaceAccentedChars($word_id->word)] = (int)$word_id->word_id;
					}
				}
	
				foreach ($product_array as $word => $weight)
				{
					if (!$weight)
						continue;
					if (!isset($word_ids_by_word[$product->shop_id][$product->lang_id]['_'.$word]))
						continue;
					if (!$word_ids_by_word[$product->shop_id][$product->lang_id]['_'.$word])
						continue;
					$query_array3[] = '('.(int)$product->product_id.','.
							(int)$word_ids_by_word[$product->shop_id][$product->lang_id]['_'.$word].','.(int)$weight.')';
					// Force save every 200 words in order to avoid overloading MySQL
					if (++$count_words % 200 == 0)
						JeproshopSearch::saveIndex($query_array3);
				}
	
				if (!in_array($product->product_id, $products_array))
					$products_array[] = (int)$product->product_id;
			}
			JeproshopSearch::setProductsAsIndexed($products_array);
	
			// One last save is done at the end in order to save what's left
			JeproshopSearch::saveIndex($query_array3);
		}
		return true;
	}
	
	/**
	 * Update a table and splits the common datas and the shop datas
	 *
	 * @since 1.5.0
	 * @param string $classname
	 * @param array $data
	 * @param string $where
	 * @param string $specific_where Only executed for common table
	 * @return bool
	 * /
	public static function updateMultishopTable($classname, $data, $where = '', $specific_where = ''){
		$def = ObjectModel::getDefinition($classname);
		$update_data = array();
		foreach ($data as $field => $value)
		{
			if (!isset($def['fields'][$field]))
				continue;
	
			if (!empty($def['fields'][$field]['shop']))
			{
				$update_data[] = "a.$field = '$value'";
				$update_data[] = "{$def['table']}_shop.$field = '$value'";
			}
			else
				$update_data[] = "a.$field = '$value'";
		}
	
		$sql = 'UPDATE '._DB_PREFIX_.$def['table'].' a
				'.Shop::addSqlAssociation($def['table'], 'a', true, null, true).'
				SET '.implode(', ', $update_data).
					(!empty($where) ? ' WHERE '.$where : '');
		return Db::getInstance()->execute($sql);
	}*/
	
	public static function removeProductsSearchIndex($products){
		if (count($products)) {
			$db = JFactory::getDBO();
			$query = "DELETE FROM " . $db->quoteName('#__jeproshop_search_index') .  " WHERE product_id IN (" . implode(',', array_map('intval', $products)).")";
			$db->setQuery($query);
			$db->query();
			$data = " SET product.". $db->quoteName('indexed') . " = 0";
			$where = " WHERE product.product_id IN (" . implode(',', array_map('intval', $products)). ")";
			JeproshopProductModelProduct::updateMultishopTable($data, $where, '', TRUE);
		}
	}
	
	protected static function setProductsAsIndexed(&$products){
		if (count($products)){
			$db = JFactory::getDBO();
			$data = " SET product." . $db->quoteName('indexed') . " = 1";
			$where = " WHERE product.product_id IN (" . implode(',', $products).")";
			JeproshopProductModelProduct::updateMultishopTable($data, $where, '', TRUE);
		}
	}
	
	/** $queryArray3 is automatically emptied in order to be reused immediatly */
	protected static function saveIndex(&$queryArray3){
		if (count($queryArray3)){
			$db = JFactory::getDBO();
			$query = "INSERT INTO " . $db->quoteName('#__jeproshop_search_index') . " (product_id, word_id, weight) VALUES ";
			$query .= implode(',', $queryArray3) . " ON DUPLICATE KEY UPDATE weight = weight + VALUES(weight) "; 
			
			$db->setQuery($query);
			$db->query();
		}
		$queryArray3 = array();
	}
	
	public static function searchTag($id_lang, $tag, $count = false, $pageNumber = 0, $pageSize = 10, $orderBy = false, $orderWay = false, $useCookie = true, JeproshopContext $context = null){
		if (!$context){	$context = JeproshopContext::getContext(); }
	
		// Only use cookie if id_customer is not present
		if ($useCookie){
			$customer_id = (int)$context->customer->customer_id;
		}else{
			$customer_id = 0;
		}
		if (!is_numeric($pageNumber) || !is_numeric($pageSize) || !Validate::isBool($count) || !Validate::isValidSearch($tag)
				|| $orderBy && !$orderWay || ($orderBy && !Validate::isOrderBy($orderBy)) || ($orderWay && !Validate::isOrderBy($orderWay)))
					return false;
	
		if ($pageNumber < 1) $pageNumber = 1;
		if ($pageSize < 1) $pageSize = 10;
	
		$shop_id = JeproshopContext::getContext()->shop->shop_id;
		$shop_id = $shop_id ? $shop_id : JeproshopSettingModelSetting::getValue('default_shop');
	
		$sql_groups = '';
		if(JeproshopGroupModelGroup::isFeaturePublished()){
			$groups = JeproshopController::getCurrentCustomerGroups();
			$sql_groups = " AND customer_group." . $db->quoteName('group_id') . (count($groups) ? " IN (" . implode(',', $groups). ")" : "= 1");
		}
	
		if ($count){
			$query = "SELECT COUNT(DISTINCT product_tag." . $db->quoteName('product_id') . ") nb FROM ". $db->quoteName('#__jeproshop_product');
			$query .= " AS product " . JeproshopShopModelShop::addSqlAssociation('product') . "	LEFT JOIN " . $db->quoteName('#__jeproshop_product_tag');
			$query .= " AS product_tag ON (product." . $db->quoteName('product_id') . " = product_tag." . $db->quoteName('product_id') . ") LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_tag') . " AS tag ON (product_tag." . $db->quoteName('tag_id') . " = tag." . $db->quoteName('tag_id');
			$query .= " AND tag." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_product_category');
			$query .= " AS product_category ON (product_category." . $db->quoteName('product_id') . " = product." . $db->quoteName('product_id') . ") LEFT JOIN ";
			$query .= $db->quoteName('#__jeproshop_category_shop') . " AS category_shop ON (product_category." .  $db->quoteName('category_id') . " = category_shop.";
			$query .=  $db->quoteName('category_id') . " AND category_shop." .  $db->quoteName('shop_id') . " = " .(int)$shop_id . ") ";
			$query .= (JeproshopGroupModelGroup::isFeaturePublished() ? " LEFT JOIN " .$db->quoteName('#__jeproshop_category_group')  . " AS category_group ON (category_group." .  $db->quoteName('category_id') . " = product_category." . $db->quoteName('category_id') . ")" : "");
			$query .= "	WHERE product_shop." . $db->quoteName('publishd') . " = 1 AND product.visibility IN ('both', 'search') AND category_shop." .  $db->quoteName('shop_id');
			$query .= " = " .(int)JeproshopContext::getContext()->shop->shop_id . $sql_groups . " AND tag." . $db->quoteName('name') . " LIKE '%'" . $db->quote($tag) . "'%')";
			/*$query .= " } 
	
				$sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description_short`, pl.`link_rewrite`, pl.`name`,
					MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name, 1 position,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							NOW(),
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 new
				FROM `' $db->quoteName('#__jeproshop_product` p
				INNER JOIN `'.$db->quoteName('#__jeproshop_product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)'.Shop::addSqlAssociation('product', 'p', false).'
				LEFT JOIN `'.$db->quoteName('#__jeproshop_image` i ON (i.`id_product` = p.`id_product`)'.
					Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `' . $db->quoteName('#__jeproshop_image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'. $db->quoteName('#__jeproshop_manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'. $db->quoteName('#__jeproshop_product_tag` pt ON (p.`id_product` = pt.`id_product`)
				LEFT JOIN `'. $db->quoteName('#__jeproshop_tag` t ON (pt.`id_tag` = t.`id_tag` AND t.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'. $db->quoteName('#__jeproshop_category_product` cp ON (cp.`id_product` = p.`id_product`)
				'.(Group::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = cp.`id_category`)' : '').'
				LEFT JOIN `'. $db->quoteName('#__jeproshop_category_shop` cs ON (cp.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
				'.Product::sqlStock('p', 0).'
				WHERE product_shop.`active` = 1
					AND cs.`id_shop` = '.(int)Context::getContext()->shop->id.'
					'.$sql_groups.'
					AND t.`name` LIKE \'%'.pSQL($tag).'%\'
					return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
			
				
					GROUP BY product_shop.id_product
				ORDER BY position DESC'.($orderBy ? ', '.$orderBy : '').($orderWay ? ' '.$orderWay : '').'
				LIMIT '.(int)(($pageNumber - 1) * $pageSize).','.(int)$pageSize;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				if (!$result)
					return false; */
		}
				return JeproshopProductModelProduct::getProductsProperties((int)$lang_id, $result);
	}
}
