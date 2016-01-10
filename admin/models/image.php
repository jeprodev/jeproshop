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

class JeproshopImageModelImage extends JModelLegacy
{
	public $image_id;
	
	public $product_id;
	
	public $position;
	
	public $cover;
	
	public $legend;
	
	public $image_format ='jpg';

    /** @var string image folder */
    protected $folder;
	
	protected static $_cacheGetSize = array();

    /** @var string image path without extension */
    protected $existing_path;
	
	public function __construct($image_id = null, $lang_id = null){
		//parent::__construct($id, $id_lang);
		if($lang_id !== null){
			$this->lang_id = (JeproshopSettingModelSetting::getLanguage($lang_id) !== false) ? $lang_id : JeproshopSettingModelSetting::getValue('default_lang');
		}
		
		if($image_id){
			$cache_id = 'jeproshop_image_model_' . (int)$image_id . '_' . (int)$lang_id;
			if(!JeproshopCache::isStored($cache_id)){
				$db = JFactory::getDBO();
				
				$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_image') . " AS image ";
				if($lang_id){
					$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_image_lang') . " AS image_lang ON ";
					$query .= "(image." . $db->quoteName('image_id') . " = image_lang." . $db->quoteName('image_id');
					$query .= " AND image." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") ";
				}
				$query .= "WHERE image." . $db->quoteName('image_id') . " = " . (int)$image_id;
				
				$db->setQuery($query);
				$image_data = $db->loadObject();
				
				if($image_data){
					if(!$lang_id && isset($this->multilang) && $this->multilang){
						$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_image_lang');
						$query .= " WHERE image_id = " . (int)$image_id;
						
						$db->setQuery($query);
						$image_lang_data = $db->loadObjectList();
						if($image_lang_data){
							foreach ($image_lang_data as $row){
								foreach($row as $key => $value){
									if(array_key_exists($key, $this) && $key != 'image_id'){
										if(!isset($image_data->{$key}) || !is_array($image_data->{$key})){
											$image_data->{$key} = array();
										}
										$image_data->{$key}[$row->lang_id] = $value;
									}
								}
							}
						}
						JeproshopCache::store($cache_id, $image_data);
					}
				}
			}else{
				$image_data = JeproshopCache::retrieve($cache_id);
			}
			
			if($image_data){
				$image_data->image_id = $image_id;
				foreach($image_data as $key => $value){
					if(array_key_exists($key, $this)){
						$this->{$key} = $value;
					}
				}
			}
		}
		$this->image_dir = COM_JEPROSHOP_PRODUCT_IMAGE_DIR;
		$this->source_index = COM_JEPROSHOP_PRODUCT_IMAGE_DIR.'index.php';
	}
	
	/**
	 * Return available images for a product
	 *
	 * @param integer $lang_id Language ID
	 * @param integer $product_id Product ID
	 * @param integer $product_attribute_id Product Attribute ID
	 * @return array Images
	 */
	public static function getImages($lang_id, $product_id, $product_attribute_id = NULL){
		$db = JFactory::getDBO();
		$attribute_filter = ($product_attribute_id ? " AND attribute_image." . $db->quoteName('product_attribute_id') . " = " . (int)$product_attribute_id : "");
		
		$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_image') . " AS image LEFT JOIN " . $db->quoteName('#__jeproshop_image_lang');
		$query .= " AS image_lang ON (image." . $db->quoteName('image_id') . " = image_lang." . $db->quoteName('image_id') .") ";
	
		if ($product_attribute_id){
			$query .= " LEFT JOIN " . $db->quoteName('#__jeproshop_product_attribute_image') . " AS attribute_image ON (image.";
			$query .= $db->quoteName('image_id') . " = attribute_image." . $db->quoteName('image_id') . ")";
		}
		$query .= " WHERE image." . $db->quoteName('product_id') . " = " . (int)$product_id . " AND image_lang.";
		$query .= $db->quoteName('lang_id') . " = " .(int)$lang_id . $attribute_filter. " ORDER BY image." . $db->quoteName('position') . " ASC";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Returns image path in the old or in the new filesystem
	 *
	 * @ returns string image path
	 */
	public function getExistingImagePath(){
		if (!$this->image_id){ return false; }
	
		if (!$this->existing_path){
			if (JeproshopSettingModelSetting::getValue('legacy_images') && file_exists(COM_JEPROSHOP_PRODUCT_IMAGE_DIR . $this->product_id . '_' . $this->product_id . '.' . $this->image_format)){
				$this->existing_path = $this->product_id . '_' . $this->image_id;
			}else{
				$this->existing_path = $this->getImagePath();
			}
		}
	
		return $this->existing_path;
	}

    /**
     * Returns the path to the image without file extension
     *
     * @return string path
     */
    public function getImagePath() {
        if (!$this->image_id){ return false; }

        $path = $this->getImageFolder() . $this->image_id;
        return $path;
    }


    /**
     * Returns the path to the folder containing the image in the new filesystem
     *
     * @return string path to folder
     */
    public function getImageFolder() {
        if (!$this->image_id){ return false; }

        if (!$this->folder){
            $this->folder = JeproshopImageModelImage::getStaticImageFolder($this->image_id);
        }
        return $this->folder;
    }

    /**
     * Returns the path to the folder containing the image in the new filesystem
     *
     * @param mixed $image_id
     * @return string path to folder
     */
    public static function getStaticImageFolder($image_id){
        if (!is_numeric($image_id)){ return false; }
        $folders = str_split((string)$image_id);
        return implode('/', $folders).'/';
    }

    public function delete(){
        $db = JFactory::getDBO();

        $this->clearCache();
        $result = true;

        if(JeproshopShopModelShop::isTableAssociated('image')){
            $shopListIds = JeproshopShopModelShop::getContextListShopIds();
            if(count($this->shop_list_id)){ $shopListIds = $this->shop_list_id; }

            $query = "DELETE FROM " . $db->quoteName('#__jeproshop_image_shop') . " WHERE " . $db->quoteName('image_id') . " = " . (int)$this->image_id . " AND " . $db->quoteName('shop_id') . " IN(" . implode($shopListIds). ")";
            $db->setQuery($query);

            $result &= $db->query();
        }

        $hasMultiShopEntries = $this->hasMultiShopEntries();
        if($result && !$hasMultiShopEntries){
            $query = " DELETE FROM " . $db->quoteName('#__jeproshop_image') . " WHERE " . $db->quoteName('image_id') . " = " . (int)$this->image_id;
            $db->setQuery($query);
            $result &= $db->query();
        }

        if(!$hasMultiShopEntries){
            $query = " DELETE FROM " . $db->quoteName('#__jeproshop_image_lang') . " WHERE " . $db->quoteName('image_id') . " = " . (int)$this->image_id;
            $db->setQuery($query);
            $result &= $db->query();
        }
        if (!$result)
            return false;

        if ($this->hasMultishopEntries()){ return true; }

        if (!$this->deleteProductAttributeImage() || !$this->deleteImage()){ return false; }

        // update positions
        $query = "SELECT * FROM " . $db->quoteName('#__jeproshop_image') . " WHERE " . $db->quoteName('product_id') . " = " . (int)$this->product_id . " ORDER BY " . $db->quoteName('position');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $i = 1;
        if ($result) {
            foreach ($result as $row) {
                $row->position = $i++;
                $query = "UPDATE " . $db->quoteName('#_jeproshop_image') . " SET " . $db->quoteName('position') . " = " . (int)$row->position . " WHERE " . $db->quoteName('image_id') . " = " .(int)$row->image_id;

                $db->setQuery($query);
                $db->query();
                Db::getInstance()->update($this->def['table'], $row, '`id_image` = ' . (int)$row['id_image'], 1);
            }
        }
        return true;
    }

    /**
     * Delete the product image from disk and remove the containing folder if empty
     * Handles both legacy and new image filesystems
     * @param bool $force_delete
     * @return bool
     */
    public function deleteImage($force_delete = false){
        if (!$this->image_id)
            return false;

        // Delete base image
        if (file_exists($this->image_dir.$this->getExistingImagePath().'.'.$this->image_format)) {
            unlink($this->image_dir . $this->getExistingImagePath() . '.' . $this->image_format);
        }else {
            return false;
        }

        $files_to_delete = array();

        // Delete auto-generated images
        $image_types = JeproshopImageTypeModelImageType::getImagesTypes();
        foreach ($image_types as $image_type)
            $files_to_delete[] = $this->image_dir.$this->getExistingImagePath().'-'.$image_type->name . '.' .$this->image_format;

        // Delete watermark image
        $files_to_delete[] = $this->image_dir . $this->getExistingImagePath(). '_watermark.' .$this->image_format;
        // delete index.php
        $files_to_delete[] = $this->image_dir.$this->getImageFolder().'index.php';
        // Delete tmp images
        $files_to_delete[] = COM_JEPROSHOP_TEMP_IMAGE_DIR . 'product_' . $this->product_id . '.' . $this->image_format;
        $files_to_delete[] = COM_JEPROSHOP_TEMP_IMAGE_DIR . 'product_mini_' .$this->product_id . '.' . $this->image_format;

        foreach ($files_to_delete as $file)
            if (file_exists($file) && !@unlink($file))
                return false;

        // Can we delete the image folder?
        if (is_dir($this->image_dir.$this->getImageFolder())){
            $delete_folder = true;
            foreach (scandir($this->image_dir.$this->getImageFolder()) as $file) {
                if (($file != '.' && $file != '..')) {
                    $delete_folder = false;
                    break;
                }
            }
        }
        if (isset($delete_folder) && $delete_folder)
            @rmdir($this->image_dir.$this->getImageFolder());

        return true;
    }

    /**
     * Recursively deletes all product images in the given folder tree and removes empty folders.
     *
     * @param string $path folder containing the product images to delete
     * @param string $format image format
     * @return bool success
     */
    public static function deleteAllImages($path, $format = 'jpg'){
        if (!$path || !$format || !is_dir($path))
            return false;
        foreach (scandir($path) as $file)
        {
            if (preg_match('/^[0-9]+(\-(.*))?\.'.$format.'$/', $file))
                unlink($path.$file);
            else if (is_dir($path.$file) && (preg_match('/^[0-9]$/', $file)))
                Image::deleteAllImages($path.$file.'/', $format);
        }

        // Can we remove the image folder?
        if (is_numeric(basename($path)))
        {
            $remove_folder = true;
            foreach (scandir($path) as $file)
                if (($file != '.' && $file != '..' && $file != 'index.php'))
                {
                    $remove_folder = false;
                    break;
                }

            if ($remove_folder){
                // we're only removing index.php if it's a folder we want to delete
                if (file_exists($path.'index.php'))
                    @unlink ($path.'index.php');
                @rmdir($path);
            }
        }

        return true;
    }


    /**
     * Check if there is more than one entries in associated shop table for current entity
     *
     * @since 1.5.0
     * @return bool
     */
    public function hasMultishopEntries(){
        if (!JeproshopShopModelShop::isTableAssociated('image') || !JeproshopShopModelShop::isFeaturePublished()){  return false; }
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeproshop_image_shop') . " WHERE " . $db->quoteName('image_id') . " = " . (int)$this->image_id;
        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    public function clearCache($all = false){
        if ($all) {
            JeproshopCache::clean('jeproshop_image_model_*');
        }elseif ($this->image_id) {
            JeproshopCache::clean('jeproshop_image_model_' . (int)$this->image_id . '_*');
        }
    }

    /**
     * Delete Image - Product attribute associations for this image
     */
    public function deleteProductAttributeImage(){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeproshop_product_attribute_image') . " WHERE " . $db->quoteName('image_id') . " = " . (int)$this->image_id;
        $db->setQuery($query);
        return $db->query();
    }
}

/**** ---------  IMAGE TYPE  -------- ****/
class JeproshopImageTypeModelImageType extends JModelLegacy
{
	public $image_type_id;
	
	/** @var string Name */
	public $name;
	
	/** @var integer Width */
	public $width;
	
	/** @var integer Height */
	public $height;
	
	/** @var boolean Apply to products */
	public $products;
	
	/** @var integer Apply to categories */
	public $categories;
	
	/** @var integer Apply to manufacturers */
	public $manufacturers;
	
	/** @var integer Apply to suppliers */
	public $suppliers;
	
	/** @var integer Apply to scenes */
	public $scenes;
	
	/** @var integer Apply to store */
	public $stores;
	
	/**
	 * @var array Image types cache
	 */
	protected static $images_types_cache = array();
	
	protected static $images_types_name_cache = array();
	
	protected $webserviceParameters = array();
	
	/**
	 * Finds image type definition by name and type
	 * @param string $name
	 * @param string $type
     * @param JeproshopOrderModelOrder $order
	 */
	public static function getByNameNType($name, $type = null, $order = null){
		if (!isset(self::$images_types_name_cache[$name.'_'.$type.'_'.$order]))	{
			$db = Jfactory::getDBO();
			
			$query = "SELECT " . $db->quoteName('image_type_id') . ", " . $db->quoteName('name') . ", ";
			$query .= $db->quoteName('width') . ", " . $db->quoteName('height') . ", " . $db->quoteName('products');
			$query .= ", " . $db->quoteName('categories') . ", " . $db->quoteName('manufacturers') . ", ";
			$query .= $db->quoteName('suppliers') . ", " . $db->quoteName('scenes') . " FROM " . $db->quoteName('#__jeproshop_image_type');
			$query .= "	WHERE " . $db->quoteName('name') . " LIKE " . $db->quote($db->escape($name)); 
			$query .= (!is_null($type) ? " AND " . $db->quoteName($db->escape($type)) . " = 1" : "");
			$query .= (!is_null($order) ? " ORDER BY " . $db->quoteName($db->escape($order)) . " ASC" : "" );
			
			$db->setQuery($query);
			self::$images_types_name_cache[$name.'_'.$type.'_'.$order] = $db->loadObject();
		}
		return self::$images_types_name_cache[$name.'_'.$type.'_'.$order];
	}
	
	/**
	 * Returns image type definitions
	 *
	 * @param string|null Image type
	 * @return array Image type definitions
	 */
	public static function getImagesTypes($type = null){
		if (!isset(self::$images_types_cache[$type])){
			$db = JFactory::getDBO();
			$where = " WHERE 1";
			if (!empty($type)){
				$where .= " AND " . $db->quoteName($db->escape($type)) . " = 1 ";
			}
			$query = "SELECT * FROM " . $db->quoteName('#__jeproshop_image_type') . $where . " ORDER BY " . $db->quoteName('name') . " ASC";
			
			$db->setQuery($query);
			self::$images_types_cache[$type] = $db->loadObjectList();
		}
		return self::$images_types_cache[$type];
	}
}