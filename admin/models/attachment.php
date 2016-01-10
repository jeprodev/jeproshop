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

class JeproshopAttachmentModelAttachment extends JModelLegacy
{
    public $attachment_id;
	public $file;
	public $file_name;
	public $file_size;
	public $name;
	public $mime;
	public $description;

    private $pagination;
	
	/** @var integer position */
	public $position;
	
	public static function getAttachments($lang_id, $product_id, $include = true){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_attachment') . " AS attachment LEFT JOIN ";
		$query .= $db->quoteName('#__jeproshop_attachment_lang') . " AS attachment_lang ON (attachment.";
		$query .= "attachment_id = attachment_lang.attachment_id AND attachment_lang.lang_id = " . (int)$lang_id;
		$query .= ") WHERE attachment.attachment_id " . ($include ? "IN" : "NOT IN") . " ( SELECT product_attachment.";
		$query .= "attachment_id FROM " . $db->quoteName('#__jeproshop_product_attachment') . " AS product_attachment";
		$query .= " WHERE product_id = " .(int)$product_id . ")";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * associate $product_id to the current object.
	 *
	 * @param int $product_id id of the product to associate
	 * @return boolean true if succeeded
	 */
	public function attachProduct($product_id){
        $db = JFactory::getDBO();

        $query = "INSERT INTO " . $db->quoteName('#__jeproshop_product_attachment') . " (attachment_id, product_id) VALUES (";
        $query .= (int)$this->attachment_id . ", " .(int)$product_id . ")";

        $db->setQuery($query);
		$res = $db->query();
			
		JeproshopProductModelProduct::updateCacheAttachment((int)$product_id);
	
		return $res;
	}
	
	/**
	 * associate an array of id_attachment $array to the product $id_product
	 * and remove eventual previous association
	 *
	 * @static
	 * @param $product_id
	 * @param $array
	 * @return bool
	 */
	public static function attachToProduct($product_id, $array){
		$result1 = JeproshopAttachmentModelAttachment::deleteProductAttachments($product_id);
	
		if (is_array($array)){
			$attachment_ids = array();
			foreach ($array as $attachment_id){
				if ((int)$attachment_id > 0){
					$attachment_ids[] = array('product_id' => (int)$product_id, 'attachment_id' => (int)$attachment_id);
				}
			}
	
			if (!empty($attachment_ids)){
				$result2 = Db::getInstance()->insert('product_attachment', $attachment_ids);
			}
		}
	
		JeproshopProductModelProduct::updateCacheAttachment((int)$product_id);
		if (is_array($array))
			return ($result1 && (!isset($result2) || $result2));
			
		return $result1;
	}
	
	/**
	 * de-associate $product_id from the current object
	 *
	 * @static
	 * @param $product_id int
	 * @return bool
	 */
	public static function deleteProductAttachments($product_id){
		$db = JFactory::getDBO();
		$query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attachment') . " WHERE product_id = " . (int)$product_id;
		
		$db->setQuery($query);
		$res = $db->query();
	
		JeproshopProductModelProduct::updateCacheAttachment((int)$product_id);
	
		return $res;
	}

    public function getAttachmentsList(JeproshopContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeproshopContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'attachment_id', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        $lang_join = " LEFT JOIN " . $db->quoteName('#__jeproshop_attachment_lang') . " AS attachment_lang ON (attachment_lang." . $db->quoteName('attachment_id');
        $lang_join .= " = attachment." . $db->quoteName('attachment_id') . " AND attachment_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id   . ")";
        /*if(!JeproshopShopModelShop::isFeaturePublished()){
            $lang_join .= " AND attachment_lang." . $db->quoteName('shop_id') . " 1";
        }else{
            $lang_join .= " AND attachment_lang." . $db->quoteName('shop_id') . " = " . ()
            $lang_join .= " AND attachment_lang." . $db->quoteName('shop_id') . " = a"
        }*/

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS attachment." . $db->quoteName('attachment_id') . ", attachment_lang." . $db->quoteName('name') . ", attachment.";
            $query .= $db->quoteName('file') . ", attachment." . $db->quoteName('file_name') . ", attachment." . $db->quoteName('mime') . ", IFNULL(virtual.";
            $query .= "products, 0) AS products FROM " . $db->quoteName('#__jeproshop_attachment') . " AS attachment LEFT JOIN (SELECT " . $db->quoteName('attachment_id');
            $query .= ", COUNT(*) AS products FROM " . $db->quoteName('#__jeproshop_product_attachment') . " GROUP BY attachment_id) virtual ON (attachment.";
            $query .= $db->quoteName('attachment_id') . " = virtual." . $db->quoteName('attachment_id') . ") " . $lang_join . " ORDER BY " ;
            $query .= (str_replace('`', '', $order_by) == 'attachment_id' ? " attachment." : "") . $order_by . " " . $order_way;
            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $attachments = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($attachments));

        if(count($attachments)){
            $productAttachments = JeproshopAttachmentModelAttachment::getAttachedProduct((int)$lang_id, $attachments);
            $list_product_list = array();
            foreach ($attachments as $attachment){
                $product_list = '';
                if (isset($productAttachments[$attachment->attachment_id])){
                    foreach ($productAttachments[$attachment->attachment_id] as $product)
                        $product_list .= $product.', ';
                }
                $list_product_list[$attachment->attachment_id] = $product_list;
            }
        }

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $attachments;
    }

    public static function getAttachedProduct($lang_id, $list) {
        $attachment_ids = array();
        if (is_array($list)){
            foreach ($list as $attachment) {
                $attachment_ids[] = $attachment->attachment_id;
            }

            $db = JFactory::getDBO();
            $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_product_attachment') . " AS product_attachment LEFT JOIN ";
            $query .= $db->quoteName('#__jeproshop_product_lang') . " AS product_lang ON (product_attachment." . $db->quoteName('product_id');
            $query .= " = product_lang." . $db->quoteName('product_id') . JeproshopShopModelShop::addSqlRestrictionOnLang('product_lang');
            $query .= ") WHERE " . $db->quoteName('attachment_id') . " IN (" . implode(',', array_map('intval', $attachment_ids));
            $query .= ") AND product_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id;

            $db->setQuery($query);
            $tmp = $db->loadObjectList();
            $productAttachments = array();
            foreach ($tmp as $t)
                $productAttachments[$t->attachment_id][] = $t->name;
            return $productAttachments;
        }
        else
            return false;
    }

    public function getPagination(){
        return $this->pagination;
    }

    public function delete() {
        @unlink(_PS_DOWNLOAD_DIR_.$this->file);

        $db = JFactory::getDBO();

        $query = "SELECT product_id FROM " . $db->quoteName('#__jeproshop_product_attachment') . " WHERE attachment_id = " . (int)$this->attachment_id;

        $db->setQuery($query);
        $products = $db->loadObjectList();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attachment') . " WHERE attachment_id = " .(int)$this->attachment_id;

        $db->setQuery($query);
        $db->query();

        foreach ($products as $product) {
            JeproshopProductModelProduct::updateCacheAttachment((int)$product->product_id);
        }

        return parent::delete();
    }

    public function deleteSelection($attachments){
        $return = 1;
        foreach ($attachments as $attachment_id){
            $attachment = new JeproshopAttachmentModelAttachment((int)$attachment_id);
            $return &= $attachment->delete();
        }
        return $return;
    }

    public function saveAttachment(){

    }

    public function add($autodate = true, $null_values = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_.$this->file);
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_.$this->file);
        return parent::update($null_values);
    }
}