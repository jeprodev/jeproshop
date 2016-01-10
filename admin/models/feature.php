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

class JeproshopFeatureModelFeature extends JModelLegacy
{
    public $feature_id;

    public $shop_id;

    public $lang_id;

    /** @var string Name */
    public $name;
    public $position;

    public $multiLang = true;
    public $multiLangShop = false;

	private $pagination;

    public function __construct($feature_id = null, $lang_id = null, $shop_id = null){
        if ($lang_id !== null) {
            $this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) !== false) ? $lang_id : JeproshopSettingModelSetting::getValue('default_lang');
        }

        if ($shop_id && $this->isMultiShop()){
            $this->shop_id = (int)$shop_id;
            $this->get_shop_from_context = false;
        }

        if ($this->isMultiShop() && !$this->shop_id)
            $this->shop_id = JeproshopContext::getContext()->shop->shop_id;

        if ($feature_id){
            // Load object from database if object id is present
            $cache_id = 'jeproshop_feature_model_' . (int)$feature_id . '_' . (int)$this->shop_id . '_' . (int)$lang_id;
            if (!JeproshopCache::isStored($cache_id)){
                $db = JFactory::getDBO();
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_feature') . " AS feature ";

                $where = " WHERE feature." . $db->quoteName('feature_id') . " = " . (int)$feature_id;

                // Get lang informations
                if ($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_feature_lang') . " AS feature_lang ON (feature." . $db->quoteName('feature_id') . " = ";
                    $query .= $db->quoteName('feature_id') . " AND feature_lang." .$db->quoteName('lang_id') . " = " . (int)$lang_id . ") ";
                    if ($this->shop_id && !empty($this->multiLangShop)) {
                        $where .= " AND feature_lang." . $db->quoteName('shop_id') . " = " . $this->shop_id ;
                    }
                }

                // Get shop informations
                if (JeproshopShopModelShop::isTableAssociated('feature')) {
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_feature_shop') . " AS feature_shop ON (feature." . $db->quoteName('feature_id') . " = feature_shop.";
                    $query .= $db->quoteName('feature_id') . " AND feature_shop." .$db->quoteName('shop_id') . " = " . (int)$this->shop_id . ") ";
                }

                $db->setQuery($query . $where);
                $feature_data = $db->loadObject();
                if ($feature_data) {
                    if (!$lang_id && isset($this->multiLang) && $this->multiLang){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_feature_lang') . " WHERE " . $db->quoteName('feature_id') . " = " .(int)$feature_id;
                        $query .= (($this->shop_id && $this->isLangMultiShop()) ? " AND " . $db->quoteName('shop_id') . " = " . $this->shop_id : "");

                        $db->setQuery($query);
                        $feature_data_lang = $db->loadObjectList();
                        if ($feature_data_lang) {
                            foreach ($feature_data_lang as $row) {
                                foreach ($row as $key => $value) {
                                    if (array_key_exists($key, $this) && $key != 'feature_id') {
                                        if (!isset($feature_data->{$key}) || !is_array($feature_data->{$key})) {
                                            $feature_data->{$key} = array();
                                        }
                                        $feature_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $feature_data);
                }
            } else {
                $feature_data = JeproshopCache::retrieve($cache_id);
            }

            if ($feature_data){
                //$this->id = (int)$id;
                foreach ($feature_data as $key => $value)
                    if (array_key_exists($key, $this))
                        $this->{$key} = $value;
            }
        }
    }
	
	public function getFeatureList($explicitSelect = TRUE){
		jimport('joomla.html.pagination');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		
		$context = JeproshopContext::getContext();
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
		$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
		$shop_id = $app->getUserStateFromRequest($option. $view. '.shop_id', 'shop_id', $context->shop->shop_id, 'int');
		$shop_group_id = $app->getUserStateFromRequest($option. $view. '.shop_group_id', 'shop_group_id', $context->shop->shop_group_id, 'int');
		$category_id = $app->getUserStateFromRequest($option. $view. '.cat_id', 'cat_id', 0, 'int');
		$order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'position', 'string');
		$order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
		$published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
		
		/* Manage default params values */
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		
		/* Query in order to get results with all fields */
		$lang_join = '';
		if ($lang_id){
			$lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_feature_lang') . " AS feature_lang ON (";
			$lang_join .= "feature_lang." . $db->quoteName('feature_id') . " = feature." . $db->quoteName('feature_id');			
			$lang_join .= " AND feature_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id .") ";				
		}
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS ";
			if($explicitSelect){
				$query .= "feature." . $db->quoteName('feature_id') . ", feature_lang.";
				$query .= $db->quoteName('name') . ", feature." . $db->quoteName('position');
			}
				
			$query .= " FROM " . $db->quoteName('#__jeproshop_feature') . " AS feature " . $lang_join;
				
			$query .= " ORDER BY " . ((str_replace('`', '', $order_by) == 'feature_id') ? "feature." : ""). " feature." ;
			$query .= $order_by ." " . $db->escape($order_way) . (($use_limit === true) ? " LIMIT " . (int)$limitstart.", ".(int)$limit : "" );
				

			$db->setQuery($query);
			$features = $db->loadObjectList();
				
			if ($use_limit === true){
				$limitstart = (int)$limitstart - (int)$limit;
				if ($limitstart < 0){ break; }
			}else{ break; }
		}while(empty($features));
		
		foreach($features as $feature){
			$query = "SELECT feature_value.feature_value_id AS count_values FROM " . $db->quoteName('#__jeproshop_feature_value');
			$query .= " AS feature_value WHERE feature_value.feature_id = " . $feature->feature_id . " AND (feature_value.custom";
			$query .= " = 0 OR feature_value.custom IS NULL)";
			
			$db->setQuery($query);
			$feature->count_values = count($db->loadObjectList());
		}
		
		$total = count($features);
		
		$this->pagination = new JPagination($total, $limitstart, $limit);
		return $features;
	} 
	
	public function getPagination(){
		return $this->pagination;
	}

    /**
     * Get all features for a given language
     *
     * @param integer $lang_id Language id
     * @param bool $with_shop
     * @return array Multiple arrays with feature's data
     * @static
     */
	public static function getFeatures($lang_id, $with_shop = true){
		$db = JFactory::getDBO();
		
		$query = "SELECT DISTINCT feature.feature_id, feature.*, feature_lang.* FROM " . $db->quoteName('#__jeproshop_feature');
		$query .= " AS feature " .($with_shop ? JeproshopShopModelShop::addSqlAssociation('feature') : "") . " LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_feature_lang') . " AS feature_lang ON (feature." . $db->quoteName('feature_id');
		$query .= " = feature_lang." . $db->quoteName('feature_id') . " AND feature_lang." . $db->quoteName('lang_id') . " = ";
		$query .= (int)$lang_id . ") ORDER BY feature." . $db->quoteName('position') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function isMultiShop(){
        return (JeproshopShopModelShop::isTableAssociated('feature') || !empty($this->multiLangShop));
    }

    public function isLangMultishop() {
        return !empty($this->multiLang) && !empty($this->multiLangShop);
    }
	
	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeaturePublished(){
		return JeproshopSettingModelSetting::getValue('feature_feature_active');
	}
}


class JeproshopFeatureValueModelFeatureValue extends JModelLegacy
{
	/** @var integer Group id which attribute belongs */
	public $feature_id;
	
	/** @var string Name */
	public $value;
	
	/** @var boolean Custom */
	public $custom = 0;

    /**
     * Get all values for a given feature and language
     *
     * @param integer $lang_id Language id
     * @param boolean $feature_id Feature id
     * @param bool $custom
     * @return array Array with feature's values
     * @static
     */
	public static function getFeatureValuesWithLang($lang_id, $feature_id, $custom = false){
		$db = JFactory::getDBO();
			
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_feature_value') . " AS feature_value LEFT JOIN " . $db->quoteName('#__jeproshop_feature_value_lang');
		$query .= " AS feature_value_lang ON (feature_value." . $db->quoteName('feature_value_id') . " = feature_value_lang." . $db->quoteName('feature_value_id');
		$query .= " AND feature_value_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id. ") WHERE feature_value." . $db->quoteName('feature_id') . " = ";
		$query .= (int)$feature_id . (!$custom ? " AND (feature_value." . $db->quoteName('custom') . " IS NULL OR feature_value." . $db->quoteName('custom') . " = 0)" : "");
		$query .= "	ORDER BY feature_value_lang." . $db->quoteName('value') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get all language for a given value
	 *
	 * @param boolean $feature_value_id Feature value id
	 * @return array Array with value's languages
	 * @static
	 */
	public static function getFeatureValueLang($feature_value_id){
		$db = JFactory::getDBO();
			
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_feature_value_lang') . " WHERE " . $db->quoteName('feature_value_id') . " = ";
		$query .= (int)$feature_value_id . " ORDER BY " . $db->quoteName('lang_id');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function getFeatureValueList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!isset($context)){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');

        /* Manage default params values */
        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS feature_value." . $db->quoteName('feature_value_id') . ", feature_value_lang." . $db->quoteName('value');
            $query .= " FROM " . $db->quoteName('#__jeproshop_feature_value') . " AS feature_value LEFT JOIN " . $db->quoteName('#__jeproshop_feature_value_lang');
            $query .= " AS feature_value_lang ON (feature_value." . $db->quoteName('feature_value_id') . " = feature_value_lang." . $db->quoteName('feature_value_id');
            $query .= " AND feature_value_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE 1 AND (feature_value." . $db->quoteName('custom');
            $query .= " = 0 OR feature_value." . $db->quoteName('custom') . " IS NULL)";

            $db->setQuery($query);
            $featureValues = $db->loadObjectList();

            if($use_limit == true){
                $limitstart = (int)$limitstart - (int)$limit;
                if($limitstart < 0){ break; }
            }else{ break; }
        }while(empty($featureValues));

        return $featureValues;
    }
}