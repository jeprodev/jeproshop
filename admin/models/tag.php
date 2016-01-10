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

class JeproshopTagModelTag extends JModelLegacy
{
    public $tag_id;
	/** @var integer Language id */
	public $lang_id;
	
	/** @var string Name */
	public $name;

    public function __construct($tag_id = null, $name = null, $lang_id = null){
        $db = JFactory::getDBO();
        if ($tag_id) {
            // Load tags from database if object is present in
            $cache_id = 'jeproshop_tag_model_' . (int)$tag_id . '_' . (int)$lang_id;
            if(!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tag') . " AS tag WHERE tag." . $db->quoteName('tag_id') . " = " . (int)$tag_id;
                $query .= (($lang_id) ?  " AND " . $db->quoteName('lang_id') . " = " . (int)$lang_id : "");

                $db->setQuery($query);
                $tagData = $db->loadObject();
                JeproshopCache::store($cache_id, $tagData);
            }else{
                $tagData = JeproshopCache::retrieve($cache_id);
            }

            if($tagData){
                $this->tag_id = $tagData->tag_id;
                $this->lang_id = $tagData->lang_id;
                $this->name = $tagData->name;
            }
        }else if ($name && JeproshopTools::isGenericName($name) && $lang_id && JeproshopTools::isUnsignedInt($lang_id)){
            $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_tag') . " AS tag WHERE " . $db->quoteName('name') . " LIKE " . $db->quote($db->escape($name));
            $query .= " AND " . $db->quoteName('lang_id') . " = " . (int)$lang_id;

            $db->setQuery($query);

            $row = $db->loadObject();

            if ($row){
                $this->tag_id = (int)$row->tag_id;
                $this->lang_id = (int)$row->lang_id;
                $this->name = $row->name;
            }
        }
    }

    public function add(){
        $db = JFactory::getDBO();
        $view = JFactory::getApplication()->input->get('view');
        $languages = JeproshopLanguageModelLanguage::getLanguages();
        $data = JRequest::get('post');
        $input_data = $data['jform'];
        $result = true;

        foreach($languages as $language) {
            if($view == 'tag') {
                $name = $input_data['name_' . $language->lang_id];
            }else{
                $name = $input_data['tag_' . $language->lang_id];
            }
            $query = "INSERT INTO " . $db->quoteName('#__jeproshop_tag') . "(" . $db->quoteName('lang_id') . ", " . $db->quoteName('name') . ") VALUES (";
            $query .= (int)$language->lang_id . ", " . $db->quote($name) . ")";

            $db->setQuery($query);
            $result &= $db->query();
        }
        if (!$result) {
            return false;
        }else if (isset($input_data['products']))
            return $this->setProducts($input_data['products']);
        return true;
    }

    public function setProducts($array){
        $db = JFactory::getDBO();
        $result = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'product_tag WHERE id_tag = '.(int)$this->id);
        if (is_array($array))
        {
            $array = array_map('intval', $array);
            $result &= ObjectModel::updateMultishopTable('Product', array('indexed' => 0), 'a.id_product IN ('.implode(',', $array).')');
            $ids = array();
            foreach ($array as $id_product)
                $ids[] = '('.(int)$id_product.','.(int)$this->id.')';

            if ($result)
            {
                $result &= Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'product_tag (id_product, id_tag) VALUES '.implode(',', $ids));
                if (Configuration::get('PS_SEARCH_INDEXATION'))
                    $result &= Search::indexation(false);
            }
        }
        return $result;
    }
	
	public static function getProductTags($product_id){
		$db = JFactory::getDBO(); 
		
		$query = "SELECT tag." . $db->quoteName('lang_id') . ", tag." . $db->quoteName('name') . " FROM ";
		$query .= $db->quoteName('#__jeproshop_tag') . " AS tag LEFT JOIN " . $db->quoteName('#__jeproshop_product_tag');
		$query .= " AS product_tag ON (product_tag.tag_id = tag.tag_id ) WHERE product_tag.";
		$query .= $db->quoteName('product_id') . " = " . (int)$product_id;
		
		$db->setQuery($query);
		$tags = $db->loadObjectList();
		if(!$tags)
			return false;
		
		return $tags;			
	}

    /**
     * Add several tags in database and link it to a product
     *
     * @param integer $lang_id Language id
     * @param integer $product_id Product id to link tags with
     * @param string|array $tag_list List of tags, as array or as a string with comas
     * @param string $separator
     * @return bool Operation success
     */
	public static function addTags($lang_id, $product_id, $tag_list, $separator = ','){
		$db = JFactory::getDBO();
		
		if (!JeproshopTools::isUnsignedInt($lang_id)){ return false; }
	
		if (!is_array($tag_list)){
			$tag_list = array_filter(array_unique(array_map('trim', preg_split('#\\'.$separator.'#', $tag_list, null, PREG_SPLIT_NO_EMPTY))));
		}
		$list = array();
		if (is_array($tag_list)){
			foreach ($tag_list as $tag){
				if (!JeproshopTools::isGenericName($tag)){ return false; }
				$tag = trim(substr($tag, 0, 32));
				$tag_obj = new JeproshopTagModelTag(null, $tag, (int)$lang_id);
	
				/* Tag does not exist in database */
				if (!JeproshopTools::isLoadedObject($tag_obj, 'tag_id')){
					$tag_obj->name = $tag;
					$tag_obj->lang_id = (int)$lang_id;
					$tag_obj->add();
				}
				if (!in_array($tag_obj->tag_id, $list))
					$list[] = $tag_obj->tag_id;
			}
		}
		$data = '';
		$result = true;
		foreach ($list as $tag_id){
			$query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_tag') . " ( " . $db->quoteName('tag_id') . ", ";
			$query .= $db->quoteName('product_id') . ") VALUES (" . (int)$tag_id . ", " . (int)$product_id . ")";
			
			$db->setQuery($query);
			$result &= $db->query();
		}
	
		return $result;
		
	}
	
	public static function deleteTagsForProduct($product_id){
		$db = JFactory::getDBO();
		
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_tag') . " WHERE " . $db->quoteName('product_id') . " = " .(int)$product_id;
		
		$db->setQuery($query);
		return $db->query();
	}

    public function getTagsList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!isset($context) || $context == null){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        // v$lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'tag_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');

        $select = ", lang." . $db->quoteName('title') . " AS lang_name, COUNT(product_tag." . $db->quoteName('product_id') . ") AS products";
        $join = " LEFT JOIN " . $db->quoteName('#__jeproshop_product_tag') . " AS product_tag ON(tag." . $db->quoteName('tag_id') . " = product_tag.";
        $join .= $db->quoteName('tag_id') . ") LEFT JOIN " . $db->quoteName('#__languages') . " AS lang ON(lang." . $db->quoteName('lang_id') . " = tag.";
        $join .=  $db->quoteName('lang_id') . ") ";
        $group = " GROUP BY tag." . $db->quoteName('name') . ", tag." . $db->quoteName('lang_id');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS tag." .  $db->quoteName('tag_id') .", tag." . $db->quoteName('name') . $select ;
            $query .= " FROM " . $db->quoteName('#__jeproshop_tag') . " AS tag " . $join . " WHERE 1 " . $group . " ORDER BY ";
            $query .= ((str_replace('`', '', $order_by) == 'tag_id') ? " tag." : "") . $order_by . " " . $order_way;
            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");
            $db->setQuery($query);
            $tags = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($tags));
        return $tags;
    }
}