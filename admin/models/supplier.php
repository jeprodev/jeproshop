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

class JeproshopSupplierModelSupplier extends JModelLegacy
{
	/** @var integer supplier ID */
	public $supplier_id;
	
	/** @var string Name */
	public $name;
	
	/** @var string A short description for the discount */
	public $description;
	
	/** @var string Object creation date */
	public $date_add;
	
	/** @var string Object last modification date */
	public $date_upd;
	
	/** @var string Friendly URL */
	public $link_rewrite;
	
	/** @var string Meta title */
	public $meta_title;
	
	/** @var string Meta keywords */
	public $meta_keywords;
	
	/** @var string Meta description */
	public $meta_description;
	
	/** @var boolean active */
	public $published;

    private $pagination;

    public function __construct($supplier_id = null, $lang_id = null) {
        if ($lang_id !== null) {
            $this->lang_id = (JeproshopLanguageModelLanguage::getLanguage($lang_id) !== false) ? $lang_id : JeproshopSettingModelSetting::getValue('default_lang');
        }

        if ($supplier_id) {
            // Load object from database if object id is present
            $cache_id = 'jeproshop_supplier_model_' . (int)$supplier_id . '_' . (int)$lang_id;
            $db = JFactory::getDBO();
            if (!JeproshopCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_supplier') . " AS supplier ";

                // Get lang informations
                if ($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_supplier_lang') . " AS supplier_lang ON (supplier." . $db->quoteName('supplier_id');
                    $query .= " = supplier_lang." . $db->quoteName('supplier_id') . " AND supplier_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }
                $query .= " WHERE supplier." . $db->quoteName('supplier_id') . " = " . (int)$supplier_id;
                // Get shop informations
                /*if (Shop::isTableAssociated($this->def['table']))
                    $sql->leftJoin($this->def['table'].'_shop', 'c', 'a.'.$this->def['primary'].' = c.'.$this->def['primary'].' AND c.id_shop = '.(int)$this->id_shop); */
                $db->setQuery($query);
                $supplier_data = $db->loadObject();
                if ($supplier_data){
                    if (!$lang_id ){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_supplier_lang')  . " WHERE " . $db->quoteName('supplier_id') . " = " . (int)$supplier_id;

                        $db->setQuery($query);
                        $supplier_data_lang = $db->loadObjectList();
                        if ($supplier_data_lang)
                            foreach ($supplier_data_lang as $row)
                                foreach ($row as $key => $value){
                                    if (array_key_exists($key, $this) && $key != 'supplier_id'){
                                        if (!isset($supplier_data->{$key}) || !is_array($supplier_data->{$key}))
                                            $supplier_data->{$key} = array();
                                        $supplier_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                    }
                    JeproshopCache::store($cache_id, $supplier_data);
                }
            } else {
                $supplier_data = JeproshopCache::retrieve($cache_id);
            }

            if ($supplier_data){
                //$this->id = (int)$id;
                foreach ($supplier_data as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
            }
        }

        $this->link_rewrite = $this->getLink();
        $this->image_dir = COM_JEPROSHOP_SUPPLIER_IMAGE_DIR;
    }

    public function getLink(){
        return JeproshopTools::str2url($this->name);
    }

    /**
     * Return suppliers
     *
     * @param bool $get_nb_products
     * @param int $lang_id
     * @param bool $published
     * @param bool $p
     * @param bool $n
     * @param bool $all_groups
     * @return array Suppliers
     */
	public static function getSuppliers($get_nb_products = false, $lang_id = 0, $published = true, $p = false, $n = false, $all_groups = false){
		if (!$lang_id){ $lang_id = JeproshopSettingModelSetting::getValue('default_lang'); }
		if (!JeproshopGroupModelGroup::isFeaturePublished()){ $all_groups = true; }
	
		$db = JFactory::getDBO();
		
		$query = "SELECT supplier.*, supplier_lang." . $db->quoteName('description') . " FROM " . $db->quoteName('#__jeproshop_supplier');
		$query .= " AS supplier LEFT JOIN " . $db->quoteName('#__jeproshop_supplier_lang') . " AS supplier_lang ON(supplier.";
		$query .= $db->quoteName('supplier_id') . " = supplier_lang." . $db->quoteName('supplier_id') . " AND supplier_lang.";
		$query .= $db->quoteName('lang_id') . " = " . (int)$lang_id . JeproshopShopModelShop::addSqlAssociation('supplier') . ")";
		$query .= ($published ? " WHERE supplier." . $db->quoteName('published') . " = 1" : "") . " ORDER BY supplier.";
		$query .= $db->quoteName('name') . " ASC " . (($p && $n) ? " LIMIT " . $n . ", " . ($p - 1)*$n : "") ; //. " GROUP BY supplier" . $db->quoteName('supplier_id');
		
		$db->setQuery($query);
		$suppliers = $db->loadObjectList();
		
		if ($suppliers === false){ return false; }
		if ($get_nb_products){
			$sql_groups = '';
			if (!$all_groups)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
	
			foreach ($suppliers as $key => $supplier){
				$sql = '
					SELECT DISTINCT(ps.`id_product`)
					FROM `'._DB_PREFIX_.'product_supplier` ps
					JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE ps.`id_supplier` = '.(int)$supplier['id_supplier'].'
					AND ps.id_product_attribute = 0'.
						($active ? ' AND product_shop.`active` = 1' : '').
						' AND product_shop.`visibility` NOT IN ("none")'.
						($all_groups ? '' :'
					AND ps.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				$suppliers[$key]['nb_products'] = count($result);
			}
		}
	
		$nb_suppliers = count($suppliers);
		$rewrite_settings = (int)JeproshopSettingModelSetting::getValue('rewrite_settings');
		for ($i = 0; $i < $nb_suppliers; $i++){
			$suppliers[$i]->link_rewrite = ($rewrite_settings ? JeproshopValidator::link_rewrite($suppliers[$i]->name) : 0);
		}
		return $suppliers;
	}

    public function getSuppliersList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!isset($context) || $context == null){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'supplier_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{//", supplier." . $db->quoteName('logo') .
            $query = "SELECT SQL_CALC_FOUND_ROWS supplier." . $db->quoteName('supplier_id') . ", supplier."  .$db->quoteName('name');
            $query .= ", COUNT(DISTINCT product_supplier." . $db->quoteName('product_id') . ") AS products, supplier." . $db->quoteName('published') . " FROM ";
            $query .= $db->quoteName('#__jeproshop_supplier') . " AS supplier LEFT JOIN " . $db->quoteName('#__jeproshop_product_supplier') . " AS product_supplier ON (supplier.";
            $query .= $db->quoteName('supplier_id') . " = product_supplier." . $db->quoteName('supplier_id') . ") GROUP BY supplier." . $db->quoteName('supplier_id') . " ORDER BY ";
            $query .= ((str_replace('`', '', $order_by) == 'supplier_id') ? "supplier." : "") . $order_by . " " . $order_way;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $suppliers = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($suppliers));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $suppliers;
    }

    public function getPagination(){
        return $this->pagination;
    }

    public function getSupplierAddress(){
        $db = JFactory::getDBO();

        $query = "SELECT address." . $db->quoteName('company') . ", address." . $db->quoteName('phone') . ", address." . $db->quoteName('phone_mobile') . ", address." . $db->quoteName('address1');
        $query .= ", address." . $db->quoteName('address2') . ", address." . $db->quoteName('postcode') . ", address." . $db->quoteName('country_id') . ", address." . $db->quoteName('state_id');
        $query .= ", address." . $db->quoteName('city') . " FROM " . $db->quoteName('#__jeproshop_address') . " AS address WHERE address." . $db->quoteName('supplier_id') . " = " . (int)$this->supplier_id;

        $db->setQuery($query);
        return $db->loadObject();
    }

    /*
* Tells if a supplier exists
*
* @param $id_supplier Supplier id
* @return boolean
*/
    public static function supplierExists($supplier_id){
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('supplier_id') . " FROM  " . $db->quoteName('#__jeproshop_supplier') . " WHERE " . $db->quoteName('supplier_id') . " = " . $supplier_id;

        $db->setQuery($query);
        $res = $db->loaObject();

        return (isset($res) && ($res->supplier_id > 0));
    }

}