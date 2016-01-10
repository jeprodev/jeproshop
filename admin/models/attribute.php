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

class JeproshopAttributeModelAttribute extends JModelLegacy
{
	/** @var integer Group id which attribute belongs */
	public $attribute_id;
	public $attribute_group_id;
	public $product_attribute_id;
    public $product_id;
	public $attribute_designation;

	public $shop_id;

	/** @var string Name */
	public $name;
	public $color;
	public $position;
	public $default;

    protected $multiLang = true;
    protected $multiLangShop = true;

	protected $shop_list_id;
	protected $image_dir = COM_JEPROSHOP_COLOR_IMAGE_DIR;

    public function __construct($attribute_id = null, $lang_id = null, $shop_id = null){
        if($lang_id !== NULL){
            $this->lang_id = JeproshopLanguageModelLanguage::getLanguage($lang_id) !== FALSE ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang');
        }

        if($shop_id && $this->isMultiShop()){
            $this->shop_id = (int)$shop_id;
            $this->get_shop_from_context = false;
        }

        if($this->isMultiShop() && !$this->shop_id){
            $this->shop_id = JeproshopContext::getContext()->shop->shop_id;
        }

        if($attribute_id){
            $cache_id = 'jeproshop_attribute_model_' . (int)$attribute_id;
            if(!JeproshopCache::isStored($cache_id)){
                $db = JFactory::getDBO();

                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_attribute') . " AS attribute ";
                $where = " WHERE attribute." . $db->quoteName('attribute_id') . " = " . (int)$attribute_id;

                //Get Language information
                if($lang_id){
                    $query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_lang') . " AS attribute_lang ON (attribute." . $db->quoteName('attribute_id');
                    $query .= " = attribute_lang." . $db->quoteName('attribute__id') . " AND attribute_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                    if($this->shop_id && !empty($this->multiLangShop)){
                        $where .= " AND attribute_lang." . $db->quoteName('shop_id') . " = " . (int)$shop_id;
                    }
                }

                // Get Shop Information
                if(JeproshopShopModelShop::isTableAssociated('attribute')){
                    $query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_shop') . " AS attribute_shop ON (attribute.";
                    $query .= $db->quoteName('attribute_id') . " = attribute_shop." . $db->quoteName('attribute_id') . " AND attribute_shop.";
                    $query .= $db->quoteName('shop_id') . " = " . (int)$this->shop_id . ")";
                }

                $db->setQuery($query . $where);
                $attributeData = $db->loadObject();

                if($attributeData){
                    if(!$lang_id && isset($this->multiLang) && $this->multiLang){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_attribute_lang') . " WHERE " . $db->quoteName('attribute_id') . " = " ;
                        $query .= (int)$attribute_id; // . (($this->shop_id && $this->isMultiLangShop()) ? " AND " . $db->quoteName('shop_id') . " = " . (int)$this->shop_id : "");
                        $db->setQuery($query);
                        $attributeDataLang = $db->loadObjectList();
                        if($attributeDataLang){
                            foreach ($attributeDataLang as $row) {
                                foreach ($row as $key => $value) {
                                    if(array_key_exists($key, $this) && $key != 'attribute_id'){
                                        if(!isset($attributeData->{$key}) || !is_array($attributeData->{$key})){
                                            $attributeData->{$key} = array();
                                        }
                                        $attributeData->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $attributeData);
                }
            }else{
                $attributeData = JeproshopCache::retrieve($cache_id);
            }

            if($attributeData){
                $this->attribute__id = (int)$attribute_id;
                foreach ($attributeData as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }
    public function delete(){
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL)
        {
            $result = Db::getInstance()->executeS('SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_attribute = '.(int)$this->id);
            foreach ($result as $row)
            {
                $combination = new Combination($row['id_product_attribute']);
                $combination->delete();
            }

            // Delete associated restrictions on cart rules
            CartRule::cleanProductRuleIntegrity('attributes', $this->id);

            /* Reinitializing position */
            $this->cleanPositions((int)$this->id_attribute_group);
        }
        $return = parent::delete();
        if ($return)
            Hook::exec('actionAttributeDelete', array('id_attribute' => $this->id));

        return $return;
    }

    public function update($null_values = false)
    {
        $return = parent::update($null_values);

        if ($return)
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));

        return $return;
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0)
            $this->position = Attribute::getHigherPosition($this->id_attribute_group) + 1;

        $return = parent::add($autodate, $null_values);

        if ($return)
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));

        return $return;
    }

    /**
	 * Get all attributes for a given language
	 *
	 * @param integer $lang_id Language id
	 * @param boolean $not_null Get only not null fields if true
	 * @return array Attributes
	 */
	public static function getAttributes($lang_id, $not_null = false){
		if (!JeproshopCombinationModelCombination::isFeaturePublished()){ return array(); }
		
		$db = JFactory::getDBO();
		
		$query = "SELECT DISTINCT attribute_group.*, attribute_group_lang.*, attribute." . $db->quoteName('attribute_id') . ", attribute_lang." . $db->quoteName('name') . ", attribute_group_lang." . $db->quoteName('name'). " AS ";
		$query .= $db->quoteName('attribute_group_name') . " FROM " . $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang') . " AS ";
		$query .= "attribute_group_lang ON (attribute_group." . $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id') . " AND attribute_group_lang." . $db->quoteName('lang_id') . " = ";
		$query .= (int)$lang_id . ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute') . " AS attribute ON (attribute." . $db->quoteName('attribute_group_id') . " = attribute_group." . $db->quoteName('attribute_group_id');
		$query .= ") LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_lang') . " AS attribute_lang ON (attribute." . $db->quoteName('attribute_id') . " = attribute_lang." . $db->quoteName('attribute_id') . " AND ";
		$query .= "attribute_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") " . JeproshopShopModelShop::addSqlAssociation('attribute_group') ;
        $notNullQuery =  ($not_null ? " WHERE attribute." . $db->quoteName('attribute_id') . " IS NOT NULL AND attribute_lang." . $db->quoteName('name') . " IS NOT NULL AND attribute_group_lang." . $db->quoteName('attribute_group_id') . " IS NOT NULL" : "");
		$query .= JeproshopShopModelShop::addSqlAssociation('attribute') . $notNullQuery  . " ORDER BY attribute_group_lang." . $db->quoteName('name') .  " ASC, attribute." . $db->quoteName('position') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public static function isAttribute($id_attribute_group, $name, $id_lang)
    {
        if (!Combination::isFeatureActive())
            return array();

        $result = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE al.`name` = \''.pSQL($name).'\' AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY agl.`name` ASC, a.`position` ASC
		');

        return ((int)$result > 0);
    }

    /**
     * @deprecated 1.5.0, use StockAvailable::getQuantityAvailableByProduct()
     */
    public static function getAttributeQty($id_product)
    {
        Tools::displayAsDeprecated();

        return StockAvailable::getQuantityAvailableByProduct($id_product);
    }

    /**
     * Update array with veritable quantity
     *
     * @deprecated since 1.5.0
     * @param array &$arr
     * @return bool
     */
    public static function updateQtyProduct(&$arr)
    {
        Tools::displayAsDeprecated();

        $id_product = (int)$arr['id_product'];
        $qty = Attribute::getAttributeQty($id_product);

        if ($qty !== false)
        {
            $arr['quantity'] = (int)$qty;
            return true;
        }

        return false;
    }

    /**
     * Return true if attribute is color type
     *
     * @acces public
     * @return bool
     */
    public function isColorAttribute()
    {
        if (!Db::getInstance()->getRow('
			SELECT `group_type`
			FROM `'._DB_PREFIX_.'attribute_group`
			WHERE `id_attribute_group` = (
				SELECT `id_attribute_group`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute` = '.(int)$this->id.')
			AND group_type = \'color\''))
            return false;

        return Db::getInstance()->numRows();
    }



    /**
     * Move an attribute inside its group
     * @param boolean $way Up (1)  or Down (0)
     * @param integer $position
     * @return boolean Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$id_attribute_group = (int)Tools::getValue('id_attribute_group'))
            $id_attribute_group = (int)$this->id_attribute_group;

        $sql = '
			SELECT a.`id_attribute`, a.`position`, a.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute` a
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY a.`position` ASC';

        if (!$res = Db::getInstance()->executeS($sql))
            return false;

        foreach ($res as $attribute)
            if ((int)$attribute['id_attribute'] == (int)$this->id)
                $moved_attribute = $attribute;

        if (!isset($moved_attribute) || !isset($position))
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases

        $res1 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int)$moved_attribute['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_attribute['position'].' AND `position` >= '.(int)$position).'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
        );

        $res2 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute` = '.(int)$moved_attribute['id_attribute'].'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
        );

        return ($res1 && $res2);
    }

    /**
     * Reorder attribute position in group $id_attribute_group.
     * Call it after deleting an attribute from a group.
     *
     * @param int $id_attribute_group
     * @param bool $use_last_attribute
     * @return bool $return
     */
    public function cleanPositions($id_attribute_group, $use_last_attribute = true)
    {
        $return = true;

        $sql = '
			SELECT `id_attribute`
			FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$id_attribute_group;

        // when delete, you must use $use_last_attribute
        if ($use_last_attribute)
            $sql .= ' AND `id_attribute` != '.(int)$this->id;

        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value)
            $return = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute`
				SET `position` = '.(int)$i++.'
				WHERE `id_attribute_group` = '.(int)$id_attribute_group.'
				AND `id_attribute` = '.(int)$value['id_attribute']
            );

        return $return;
    }

    /**
     * getHigherPosition
     *
     * Get the higher attribute position from a group attribute
     *
     * @param integer $attribute_group_id
     * @return integer $position
     */
    public static function getHigherPosition($attribute_group_id)
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'attribute`
				WHERE id_attribute_group = '.(int)$id_attribute_group;

        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    public function isMultiShop(){
        return JeproshopShopModelShop::isTableAssociated('attribute_group') || !empty($this->multiLangShop);
    }

    public function isMultiLangShop(){
        return !empty($this->multiLang) && !empty($this->multiLangShop);
    }
}



Class JeproshopAttributeGroupModelAttributeGroup extends JModelLegacy
{
    public $attribute_group_id;
    /** @var string Name */
    public $name;
    public $is_color_group;
    public $position;
    public $group_type;

    public $shop_id;
    public $lang_id;

    protected  $multiLang = true;
    protected  $multiLangShop = true;

    /** @var string Public Name */
    public $public_name;

	private $pagination;

    public function __construct($attribute_group_id = null, $lang_id = null, $shop_id = null){
        if($lang_id !== NULL){
            $this->lang_id = JeproshopLanguageModelLanguage::getLanguage($lang_id) !== FALSE ? (int)$lang_id : JeproshopSettingModelSetting::getValue('default_lang');
        }

        if($shop_id && $this->isMultiShop()){
            $this->shop_id = (int)$shop_id;
            $this->get_shop_from_context = false;
        }

        if($this->isMultiShop() && !$this->shop_id){
            $this->shop_id = JeproshopContext::getContext()->shop->shop_id;
        }

        if($attribute_group_id){
            $cache_id = 'jeproshop_attribute_group_model_' . (int)$attribute_group_id;
            if(!JeproshopCache::isStored($cache_id)){
                $db = JFactory::getDBO();

                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group ";
                $where = " WHERE attribute_group." . $db->quoteName('attribute_group_id') . " = " . (int)$attribute_group_id;

                //Get Language information
                if($lang_id){
                    $query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang') . " AS attribute_group_lang ON (attribute_group.";
                    $query .= $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id') . " AND ";
                    $query .= "attribute_group_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                    if($this->shop_id && !empty($this->multiLangShop)){
                        $where .= " AND attribute_group_lang." . $db->quoteName('shop_id') . " = " . (int)$shop_id;
                    }
                }

                // Get Shop Information
                if(JeproshopShopModelShop::isTableAssociated('attribute_group')){
                    $query .= "LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_shop') . " AS attribute_group_shop ON (attribute_group.";
                    $query .= $db->quoteName('attribute_group_id') . " = attribute_group_shop." . $db->quoteName('attribute_group_id') . " AND attribute_group_shop.";
                    $query .= $db->quoteName('shop_id') . " = " . (int)$this->shop_id . ")";
                }

                $db->setQuery($query . $where);
                $attributeGroupData = $db->loadObject();

                if($attributeGroupData){
                    if(!$lang_id && isset($this->multiLang) && $this->multiLang){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_attribute_group_lang') . " WHERE " . $db->quoteName('attribute_group_id') . " = " ;
                        $query .= (int)$attribute_group_id; // . (($this->shop_id && $this->isMultiLangShop()) ? " AND " . $db->quoteName('shop_id') . " = " . (int)$this->shop_id : "");
                        $db->setQuery($query);
                        $attributeGroupDataLang = $db->loadObjectList();
                        if($attributeGroupDataLang){
                            foreach ($attributeGroupDataLang as $row) {
                                foreach ($row as $key => $value) {
                                    if(array_key_exists($key, $this) && $key != 'attribute_group_id'){
                                        if(!isset($attributeGroupData->{$key}) || !is_array($attributeGroupData->{$key})){
                                            $attributeGroupData->{$key} = array();
                                        }
                                        $attributeGroupData->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeproshopCache::store($cache_id, $attributeGroupData);
                }
            }else{
                $attributeGroupData = JeproshopCache::retrieve($cache_id);
            }

            if($attributeGroupData){
                $this->attribute_group_id = (int)$attribute_group_id;
                foreach ($attributeGroupData as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

	/**
	 * Get all attributes groups for a given language
	 *
	 * @param integer $lang_id Language id
	 * @return array Attributes groups
	 */
	public static function getAttributesGroups($lang_id){
		if (!JeproshopCombinationModelCombination::isFeaturePublished()){
			return array();
		}
		
		$db = JFactory::getDBO();
		
		$query = "SELECT DISTINCT attribute_group_lang." . $db->quoteName('name') . ", attribute_group.*, ";
		$query .= "attribute_group_lang.* FROM " . $db->quoteName('#__jeproshop_attribute_group') . " AS ";
		$query .= "attribute_group " . JeproshopShopModelShop::addSqlAssociation('attribute_group'). " LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_attribute_group_lang') . " AS attribute_group_lang ON (attribute_group.";
		$query .= $db->quoteName('attribute_group_id') . " = attribute_group_lang." . $db->quoteName('attribute_group_id');
		$query .= " AND " . $db->quoteName('lang_id') . " = " .(int)$lang_id . ") ORDER BY " . $db->quoteName('name') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getAttributeGroupList($explicitSelect = TRUE){
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
		$category_id = $app->getUserStateFromRequest($option. $view. '.category_id', 'category_id', 0, 'int');
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
			$lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_attribute_group_lang') . " AS attribute_group_lang ON (";
			$lang_join .= "attribute_group_lang." . $db->quoteName('attribute_group_id') . " = attribute_group.";
			$lang_join .= $db->quoteName('attribute_group_id');
			$lang_join .= " AND attribute_group_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id .") ";
			
		}
		
		do{
			$query = "SELECT SQL_CALC_FOUND_ROWS ";
			if($explicitSelect){
				$query .= "attribute_group." . $db->quoteName('attribute_group_id') . ", attribute_group_lang.";
				$query .= $db->quoteName('name') . ", attribute_group." . $db->quoteName('position'); 				
			}
			
			$query .= " FROM " . $db->quoteName('#__jeproshop_attribute_group') . " AS attribute_group " . $lang_join;
			
			$query .= " ORDER BY " . ((str_replace('`', '', $order_by) == 'attribute_group_id') ? "attribute_group." : ""). " attribute_group." ;
			$query .= $order_by ." " . $db->escape($order_way) . (($use_limit === true) ? " LIMIT " . (int)$limitstart.", ".(int)$limit : "" );
					
			$db->setQuery($query);
			$attribute_groups = $db->loadObjectList();
			
			if ($use_limit === true){
				$limitstart = (int)$limitstart - (int)$limit;
				if ($limitstart < 0){ break; }
			}else{ break; }
		}while(empty($attribute_groups));
		
		foreach($attribute_groups as $attribute_group){
			$query = "SELECT attribute.attribute_id as count_values FROM " . $db->quoteName('#__jeproshop_attribute') . " AS attribute ";
			$query .= JeproshopShopModelShop::addSqlAssociation('attribute') . " WHERE attribute.attribute_group_id = ";
			$query .= (int)$attribute_group->attribute_group_id . " GROUP BY attribute_shop.shop_id ORDER BY count_values DESC";
			
			$db->setQuery($query);
			$attribute_group->count_values = (int)count($db->loadObjectList());
		}
		
		$total = count($attribute_groups);
		
		$this->pagination = new JPagination($total, $limitstart, $limit);
		return $attribute_groups;
	}
	
	public function getPagination(){
		return $this->pagination;
	}

    public function isMultiShop(){
        return JeproshopShopModelShop::isTableAssociated('attribute_group') || !empty($this->multiLangShop);
    }

    public function isMultiLangShop(){
        return !empty($this->multiLang) && !empty($this->multiLangShop);
    }

    public function add($autodate = true, $nullValues = false)
    {
        if ($this->group_type == 'color')
            $this->is_color_group = 1;
        else
            $this->is_color_group = 0;

        if ($this->position <= 0)
            $this->position = AttributeGroup::getHigherPosition() + 1;

        $return = parent::add($autodate, true);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));
        return $return;
    }

    public function update($nullValues = false)
    {
        if ($this->group_type == 'color')
            $this->is_color_group = 1;
        else
            $this->is_color_group = 0;

        $return = parent::update($nullValues);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));
        return $return;
    }

    public static function cleanDeadCombinations()
    {
        $attribute_combinations = Db::getInstance()->executeS('
			SELECT pac.`id_attribute`, pa.`id_product_attribute`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		');
        $to_remove = array();
        foreach ($attribute_combinations as $attribute_combination)
            if ((int)$attribute_combination['id_attribute'] == 0)
                $to_remove[] = (int)$attribute_combination['id_product_attribute'];
        $return = true;
        if (!empty($to_remove))
            foreach ($to_remove as $remove)
            {
                $combination = new Combination($remove);
                $return &= $combination->delete();
            }
        return $return;
    }

    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL)
        {
            /* Select children in order to find linked combinations */
            $attribute_ids = Db::getInstance()->executeS('
				SELECT `id_attribute`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute_group` = '.(int)$this->id
            );
            if ($attribute_ids === false)
                return false;
            /* Removing attributes to the found combinations */
            $to_remove = array();
            foreach ($attribute_ids as $attribute)
                $to_remove[] = (int)$attribute['id_attribute'];
            if (!empty($to_remove) && Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_attribute`
					IN ('.implode(', ', $to_remove).')') === false)
                return false;
            /* Remove combinations if they do not possess attributes anymore */
            if (!AttributeGroup::cleanDeadCombinations())
                return false;
            /* Also delete related attributes */
            if (count($to_remove))
                if (!Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_lang`
				WHERE `id_attribute`	IN ('.implode(',', $to_remove).')') ||
                    !Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_shop`
				WHERE `id_attribute`	IN ('.implode(',', $to_remove).')') ||
                    !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$this->id))
                    return false;
            $this->cleanPositions();
        }
        $return = parent::delete();
        if ($return)
            Hook::exec('actionAttributeGroupDelete', array('id_attribute_group' => $this->id));
        return $return;
    }

    /**
     * Get all attributes for a given language / group
     *
     * @param integer $id_lang Language id
     * @param boolean $id_attribute_group Attribute group id
     * @return array Attributes
     */
    public static function getAttributes($id_lang, $id_attribute_group)
    {
        if (!Combination::isFeatureActive())
            return array();
        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY `position` ASC
		');
    }

    /**
     * Delete several objects from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        /* Also delete Attributes */
        foreach ($selection as $value)
        {
            $obj = new AttributeGroup($value);
            if (!$obj->delete())
                return false;
        }
        return true;
    }

    public function setWsProductOptionValues($values)
    {
        $ids = array();
        foreach ($values as $value)
            $ids[] = intval($value['id']);
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$this->id.'
			AND `id_attribute` NOT IN ('.implode(',', $ids).')'
        );
        $ok = true;
        foreach ($values as $value)
        {
            $result = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute`
				SET `id_attribute_group` = '.(int)$this->id.'
				WHERE `id_attribute` = '.(int)$value['id']
            );
            if ($result === false)
                $ok = false;
        }
        return $ok;
    }

    public function getWsProductOptionValues()
    {
        $result = Db::getInstance()->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_attribute_group = '.(int)$this->id
        );
        return $result;
    }

    /**
     * Move a group attribute
     * @param boolean $way Up (1)  or Down (0)
     * @param integer $position
     * @return boolean Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT ag.`position`, ag.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			WHERE ag.`id_attribute_group` = '.(int)Tools::getValue('id_attribute_group', 1).'
			ORDER BY ag.`position` ASC'
        ))
            return false;

        foreach ($res as $group_attribute)
            if ((int)$group_attribute['id_attribute_group'] == (int)$this->id)
                $moved_group_attribute = $group_attribute;

        if (!isset($moved_group_attribute) || !isset($position))
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                    ? '> '.(int)$moved_group_attribute['position'].' AND `position` <= '.(int)$position
                    : '< '.(int)$moved_group_attribute['position'].' AND `position` >= '.(int)$position)
            ) && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute_group`='.(int)$moved_group_attribute['id_attribute_group'])
        );
    }

    /**
     * Reorder group attribute position
     * Call it after deleting a group attribute.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $return = true;

        $sql = '
			SELECT `id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group`
			ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value)
            $return = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute_group`
				SET `position` = '.(int)$i++.'
				WHERE `id_attribute_group` = '.(int)$value['id_attribute_group']
            );
        return $return;
    }

    /**
     * getHigherPosition
     *
     * Get the higher group attribute position
     *
     * @return integer $position
     */
    public static function getHigherPosition(){
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'attribute_group`';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
}