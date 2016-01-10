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

?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting');?>"  method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="form_box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=general'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=product'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=theme'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-themes" ></i> <?php echo JText::_('COM_JEPROSHOP_THEMES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-image" ></i> <?php echo JText::_('COM_JEPROSHOP_IMAGES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-shop" ></i> <?php echo JText::_('COM_JEPROSHOP_SHOP_STORE_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=search'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-search" ></i> <?php echo JText::_('COM_JEPROSHOP_SEARCH_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=grolocation'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_GEOLOCATION_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="form_box_wrapper" >
        	<div class="half_wrapper left" >
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-" ></i><?php echo JText::_('COM_JEPROSHOP_GENERAL_PRODUCT_SETTING_TITLE'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label for="jfrom_catalog_mode" id="jform_catalog_form-lbl" title="<?php echo JText::_('COM_JEPROSHOP_CATALOG_MODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CATALOG_MODE_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_catalog_mode" >
        							<input type="radio" id="jform_catalog_mode_on" name="jform[catalog_mode]" value="1" <?php if($this->catalog_mode == 1){ ?> checked="checked" <?php }?>  /><label for="jform_catalog_mode_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_catalog_mode_off" name="jform[catalog_mode]" value="0" <?php if($this->catalog_mode == 0){ ?> checked="checked" <?php }?> /><label for="jform_catalog_mode_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_COMPARATOR_MAX_ITEM_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COMPARATOR_MAX_ITEM_LABEL'); ?></label></div>
        					<div class="controls" ><input type="text" id="jform_comparator_max_item" name="jform[comparator_max_item]" value="<?php echo $this->comparator_max_item; ?>" /></div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_DAYS_PRODUCT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_DAYS_PRODUCT_LABEL'); ?></label></div>
        					<div class="controls" ><input type="text" id="jform_number_days_new_product" name="jform[number_days_new_product]" value="<?php echo $this->number_days_new_product; ?>" /></div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_REDIRECT_AFTER_ADDING_PRODUCT_TO_CART_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_REDIRECT_AFTER_ADDING_PRODUCT_TO_CART_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_redirect_after_adding_product_to_cart" >
        							<input type="radio" id="jform_redirect_after_adding_product_to_cart_previous_page" name="jform[redirect_after_adding_product_to_cart]" <?php if($this->redirect_after_adding_product_to_cart == 'previous_page'){ ?> checked="checked" <?php } ?> value="preious_page" /><label for="jform_redirect_after_adding_product_to_cart_previous_page" ><?php echo JText::_('COM_JEPROSHOP_PREVIOUS_PAGE_LABEL'); ?></label>
        							<input type="radio" id="jform_redirect_after_adding_product_to_cart_cart_content" name="jform[redirect_after_adding_product_to_cart]" <?php if($this->redirect_after_adding_product_to_cart == 'cart_content'){ ?> checked="checked" <?php } ?> value="cart_content" /><label for="jform_redirect_after_adding_product_to_cart_cart_content" ><?php echo JText::_('COM_JEPROSHOP_CART_CONTENT_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_LIMIT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SHORT_DESCRIPTION_LIMIT_LABEL'); ?></label></div>
        					<div class="controls" ><input type="text" id="jform_product_short_desc_limit" name="jform[product_short_desc_limit]" value="<?php echo $this->product_short_desc_limit; ?>" ></div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_QUANTITY_DISCOUNT_BASED_ON_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_QUANTITY_DISCOUNT_BASED_ON_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_quantity_discount_based_on" >
        							<input type="radio" id="jform_quantity_discount_based_on_products" name="jform[quantity_discount_based_on]" <?php if($this->quantity_discount_based_on == 'products'){ ?> checked="checked" <?php } ?> value="products" /><label for="jform_quantity_discount_based_on_products" ><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></label>
        							<input type="radio" id="jform_quantity_discount_based_on_declinations" name="jform[quantity_discount_based_on]" <?php if($this->quantity_discount_based_on == 'declinations'){ ?> checked="checked" <?php } ?> value="declinations" /><label for="jform_quantity_discount_based_on_combinations" ><?php echo JText::_('COM_JEPROSHOP_DECLINATIONS_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_FORCE_UPDATE_OF_FRIENDLY_URL_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_FORCE_UPDATE_OF_FRIENDLY_URL_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_" >
        							<input type="radio" id="jform_force_update_of_friendly_url_on" name="jform[force_update_of_friendly_url]" <?php if($this->force_update_of_friendly_url == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_force_update_of_friendly_url_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_force_update_of_friendly_url_off" name="jform[force_update_of_friendly_url]" <?php if($this->force_update_of_friendly_url == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_force_update_of_friendly_url_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        			</div><!-- end panel container -->
        		</div>
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-" ></i><?php echo JText::_('COM_JEPROSHOP_GENERAL_PRODUCT_SETTING_TITLE'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCTS_PER_PAGE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_PER_PAGE_LABEL'); ?></label></div>
        					<div class="controls" ><input type="text" value="<?php echo $this->products_per_page; ?>" id="jform_products_per_page" name="jform[products_per_page]" /></div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DEFAULT_ORDER_WAY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DEFAULT_ORDER_WAY_LABEL'); ?></label></div>
        					<div class="controls" >
        						<select id="jform_default_order_way" name="jform[default_order_way]" >
        							<option value="ASC" <?php if($this->default_order_way == 'ASC'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ASCENDING_LABEL'); ?></option>
        							<option value="DESC" <?php if($this->default_order_way == 'DESC'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_DESCENDING_LABEL'); ?></option>
        						</select>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DEFAULT_SORT_WAY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DEFAULT_SORT_WAY_LABEL'); ?></label></div>
        					<div class="controls" >
        						<select>
        							<option value="" <?php if($this->default_sort_way == 'product_name'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'product_price'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRICE_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'date_added'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_DATE_ADDED_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'date_updated'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_DATE_UPDATED_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'position_in_category'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_POSITION_IN_CATEGORY_LABEL'); ?></option>
        							<option value="" <?php if($this->default_sort_way == 'manufacturer'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_MANUFACTURER_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'product_quantity'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_QUANTITY_LABEL'); ?></option> 
        							<option value="" <?php if($this->default_sort_way == 'product_reference'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCE_LABEL'); ?></option>  
        						</select>
        					</div>
        				</div>
        			</div><!-- end panel container -->
        		</div>
        	</div>
        	<div class="half_wrapper right" >
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-" ></i><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PAGE_SETTING_TITLE'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM8JEPROSHOP_DISPLAY_AVAILABLE_QUANTITIES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_AVAILABLE_QUANTITIES_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_display_available_quantity" >
        							<input type="radio" id="jform_display_available_quantity_on" name="jform[display_available_quantity]" <?php if($this->display_available_quantity == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_display_available_quantity_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_display_available_quantity_off" name="jform[display_available_quantity]" <?php if($this->display_available_quantity == '0'){ ?> checked="checked" <?php }?> value="0" /><label for="jform_display_available_quantity_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_LAST_QUANTITIES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_LAST_QUANTITIES_LABEL'); ?></label></div>
        					<div class="controls" ><input type="text" id="jform_last_quantities" name="jform[last_quantities]" value="<?php echo $this->last_quantities; ?>" /></div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_UNAVAILABLE_ATTRIBUTES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_UNAVAILABLE_ATTRIBUTES_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_display_unavailable_attributes" >
        							<input type="radio" id="jform_display_unavailable_attributes_on" name="jform[display_unavailable_attributes]" <?php if($this->display_unavailable_attributes == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_display_unavailable_attributes_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_display_unavailable_attributes_off" name="jform[display_unavailable_attributes]" <?php if($this->display_unavailable_attributes == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_display_unavailable_attributes_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_ADD_TO_CART_ON_PRODUCT_WITH_ATTRIBUTES_LABEL'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_ADD_TO_CART_ON_PRODUCT_WITH_ATTRIBUTES_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" >
        							<input type="radio" id="jform_display_add_to_cart_on_product_with_attributes_on" name="jform[display_add_to_cart_on_product_with_attributes]" <?php if($this->display_add_to_cart_on_product_with_attributes == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_display_add_to_cart_on_product_with_attributes_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_display_add_to_cart_on_product_with_attributes_off" name="jform[display_add_to_cart_on_product_with_attributes]" <?php if($this->display_add_to_cart_on_product_with_attributes == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_display_add_to_cart_on_product_with_attributes_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_ATTRIBUTE_ANCHOR_SEPARATOR_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ATTRIBUTE_ANCHOR_SEPARATOR_LABEL'); ?></label></div>
        					<div class="controls" >
        						<select id="jform_attribute_anchor_separator" name="jform[attribute_anchor_separator]" >
        							<option value="-" <?php if($this->attribute_anchor_separator == '-'){ ?> selected="selected" <?php } ?> >-</option>
        							<option value="," <?php if($this->attribute_anchor_separator == ','){ ?> selected="selected" <?php } ?> >,</option>
        						</select>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_DISCOUNT_PRICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_DISCOUNT_PRICE_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" >
        							<input type="radio" id="jform_display_discount_price_on" name="jform[display_discount_price]" <?php if($this->display_discount_price == '1'){?> checked="checked" <?php } ?> value="1" /><label for="jform_display_discount_price_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_display_discount_price_off" name="jform[display_discount_price]" <?php if($this->display_discount_price == '0'){?> checked="checked" <?php } ?> value="0" /><label for="jform_display_discount_price_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        			</div><!-- end panel container -->
        		</div>
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-" ></i><?php echo JText::_('COM_JEPROSHOP_GENERAL_PRODUCT_SETTING_TITLE'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_ALLOW_OUT_OF_STOCK_ORDERING_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ALLOW_OUT_OF_STOCK_ORDERING_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_allow_out_of_stock_ordering" >
        							<input type="radio" id="jform_allow_out_of_stock_ordering_on" name="jform[allow_out_of_stock_ordering]" <?php  if($this->allow_out_of_stock_ordering == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_allow_out_of_stock_ordering_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_allow_out_of_stock_ordering_off" name="jform[allow_out_of_stock_ordering]" <?php  if($this->allow_out_of_stock_ordering == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_allow_out_of_stock_ordering_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_stock_management" title="<?php echo JText::_('COM_JEPROSHOP_STOCK_MANAGEMENT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STOCK_MANAGEMENT_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" >
        							<input type="radio" id="jform_stock_management_on" name="jform[stock_management]" <?php if($this->stock_management == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_stock_management_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_stock_management_off" name="jform[stock_management]" <?php if($this->stock_management == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_stock_management_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_advanced_stock_management" title="<?php echo JText::_('COM_JEPROSHOP_ADVANCED_STOCK_MANAGEMENT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADVANCED_STOCK_MANAGEMENT_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_advanced_stock_management" >
        							<input type="radio" id="jform_advanced_stock_management_on" name="jform[advanced_stock_management]" <?php if($this->advanced_stock_management == '1'){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_advanced_stock_management_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_advanced_stock_management_off" name="jform[advanced_stock_management]" <?php if($this->advanced_stock_management == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_advanced_stock_management_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_use_advanced_stock_management_on_new_product" title="<?php echo JText::_('COM_JEPROSHOP_USE_ADVANCED_STOCK_MANAGEMENT_ON_NEW_PRODUCT_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_USE_ADVANCED_STOCK_MANAGEMENT_ON_NEW_PRODUCT_LABEL'); ?></label></div>
        					<div class="controls" >
        						<fieldset class="radio btn-group" id="jform_use_advanced_stock_management_on_new_product" >
        							<input type="radio" id="jform_use_advanced_stock_management_on_new_product_on"  name="jform[use_advanced_stock_management_on_new_product]" <?php if($this->use_advanced_stock_management_on_new_product == '1'){ ?> checked="checked" <?php }?> value="1" /><label for="jform_use_advanced_stock_management_on_new_product_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        							<input type="radio" id="jform_use_advanced_stock_management_on_new_product_off" name="jform[use_advanced_stock_management_on_new_product]" <?php if($this->use_advanced_stock_management_on_new_product == '0'){ ?> checked="checked" <?php }?> value="0" /><label for="jform_use_advanced_stock_management_on_new_product_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        						</fieldset>
        					</div>
        				</div>
        				<!-- div class="control-group" >
        					<div class="control-label" ><label for="jform_default_warehouse_on_new_products" title="<?php echo JText::_('COM_JEPROSHOP_DEFAULT_WAREHOUSE_ON_NEW_PRODUCTS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DEFAULT_WAREHOUSE_ON_NEW_PRODUCT_LABEL'); ?></label></div>
        					<div class="controls" >
        						<select id="jform_default_warehouse_on_new_products" name="jform[default_warehouse_on_new_products]" >
        							<?php foreach($this->warehouses as $warehouse){ ?>
        							<option value="<?php echo $warehouse->warehouse_id; ?>" <?php if($warehouse->warehouse_id == $this->default_warehouse){}?> ></option>
        							<?php } ?>
        						</select>
        					</div>
        				</div-->
        			</div><!-- end panel container -->
        		</div>
        	</div>
        </div>
    </div>
</form>