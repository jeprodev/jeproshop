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

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$css_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $css_dir .'/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$helper = new JeproshopHelper();
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image');?>"  method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=general'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=theme'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-themes" ></i> <?php echo JText::_('COM_JEPROSHOP_THEMES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-image" ></i> <?php echo JText::_('COM_JEPROSHOP_IMAGES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-shop" ></i> <?php echo JText::_('COM_JEPROSHOP_SHOP_STORE_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=search'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-search" ></i> <?php echo JText::_('COM_JEPROSHOP_SEARCH_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=geolocation'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_GEOLOCATION_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel" >
            <table class="table" >
                <thead>
                <tr>
                    <th class="nowrap" width="1%" >#</th>
                    <th class="nowrap" width="" ><?php ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_WIDTH_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_HEIGHT_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_SCENES_LABEL'); ?></th>
                    <th class="nowrap" width="" ><?php echo JText::_('COM_JEPROSHOP_STORES_LABEL'); ?></th>
                    <th class="nowrap" width="" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                </tr>
                </thead>
                <tbody>
                    <?php if(!isset($this->image_types)){ ?>
                    <?php }else{
                        foreach($this->image_types as $index => $image_type){
                            ?>
                    <tr class="row_<?php echo $index % 2; ?>" >

                    </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROSHOP_'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_image_format" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_IMAGE_FORMAT_LABEL'); ?></label></div>
                    <div class="controls" >
                        <fieldset id="jform_image_format" class="radio btn-group" >
                            <input type="radio" id="jform_image_format_jpeg" name="jform[image_format]" <?php if($this->image_format == 'jpeg'){ ?> checked="checked" <?php } ?> value="jpeg" /><label for="jform_image_format_jpeg" ><?php echo JText::_('COM_JEPROSHOP_JPEG_LABEL'); ?></label>
                            <input type="radio" id="jform_image_format_png" name="jform[image_format]" <?php if($this->image_format == 'png'){ ?> checked="checked" <?php } ?> value="png" /><label for="jform_image_format_png" ><?php echo JText::_('COM_JEPROSHOP_PNG_LABEL'); ?></label>
                            <input type="radio" id="jform_image_format_png_all" name="jform[image_format]" <?php if($this->image_format == 'png_all'){ ?> checked="checked" <?php } ?> value="png_all" /><label for="jform_image_format_png_all" ><?php echo JText::_('COM_JEPROSHOP_PNG_ALL_LABEL'); ?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_jpeg_quality" title="<?php echo JText::_('COM_JEPROSHOP_JPEG_QUALITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_JPEG_QUALITY_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" required="required" id="jform_jpeg_quality" name="jform[jpeg_quality]" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_png_quality" title="<?php echo JText::_('COM_JEPROSHOP_PNG_QUALITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PNG_QUALITY_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_png_quality" name="jform[png_quality]" required="required" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_image_generation_method" title="<?php echo JText::_('COM_JEPROSHOP_IMAGE_GENERATION_METHOD_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_IMAGE_GENERATION_METHOD_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_image_generation_method" name="jform[image_generation_method]" >
                            <option value="0" <?php if($this->image_generation_method == 0){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ATOMIC_LONGEST_SIDE_LABEL'); ?></option>
                            <option value="1" <?php if($this->image_generation_method == 1){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_WIDTH_LABEL'); ?></option>
                            <option value="2" <?php if($this->image_generation_method == 2){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_HEIGHT_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_product_picture_max_size" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PICTURE_MAX_SIZE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PICTURE_MAX_SIZE_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_product_picture_max_size" name="jform[product_picture_max_size" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_product_picture_width" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_product_picture_width" name="jform[picture_product]"/></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_product_picture_height" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_product_picture_height" name="jform[picture_product_height]"/></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls" ></div>
                </div>
            </div>
        </div>
    </div>
</form>