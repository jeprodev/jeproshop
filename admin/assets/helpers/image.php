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

class JeproshopImageManager
{
	const ERROR_FILE_NOT_EXIST = 1;
	const ERROR_FILE_WIDTH     = 2;
	const ERROR_MEMORY_LIMIT   = 3;
	
	/**
	 * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc)
	 *
	 * @param string $image Real image filename
	 * @param string $cache_image Cached filename
	 * @param int $size Desired size
	 * @param string $image_type Image type
	 * @param bool $disable_cache When turned on a timestamp will be added to the image URI to disable the HTTP cache
	 * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
	 * @return string
	 */
	public static function thumbnail($image, $cache_image, $size, $image_type = 'jpg', $disable_cache = true, $regenerate = false){
		if (!file_exists($image)){ return ''; }
	
		if (file_exists(COM_JEPROSHOP_TMP_IMG_DIR . $cache_image) && $regenerate){
			@unlink(COM_JEPROSHOP_TMP_IMG_DIR . $cache_image);
		}
	
		if ($regenerate || !file_exists(COM_JEPROSHOP_TMP_IMG_DIR . $cache_image)){
			$infos = getimagesize($image);
	
			// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
			if (!JeproshopImageManager::checkImageMemoryLimit($image)){
				return false;
			}
			$x = $infos[0];
			$y = $infos[1];
			$max_x = $size * 3;
	
			// Size is already ok
			if ($y < $size && $x <= $max_x){
				copy($image, COM_JEPROSHOP_TMP_IMG_DIR . $cache_image);
			// We need to resize */
			}else{
				$ratio_x = $x / ($y / $size);
				if ($ratio_x > $max_x)
				{
					$ratio_x = $max_x;
					$size = $y / ($x / $max_x);
				}
	
				JeproshopImageManager::resize($image, COM_JEPROSHOP_TMP_IMG_DIR . $cache_image, $ratio_x, $size, $image_type);
			}
		}
		// Relative link will always work, whatever the base uri set in the admin
		if (JeproshopContext::getContext()->controller->controller_type == 'admin')
			return '<img src="../img/tmp/'.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
		else
			return '<img src="'._PS_TMP_IMG_.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
	}
}