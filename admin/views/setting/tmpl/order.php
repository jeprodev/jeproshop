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
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=general'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=order'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=theme'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-themes" ></i> <?php echo JText::_('COM_JEPROSHOP_THEMES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-image" ></i> <?php echo JText::_('COM_JEPROSHOP_IMAGES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-shop" ></i> <?php echo JText::_('COM_JEPROSHOP_SHOP_STORE_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=search'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-search" ></i> <?php echo JText::_('COM_JEPROSHOP_SEARCH_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=geolocation'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_GEOLOCATION_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="form_box_wrapper" >
        	<div class="half_wrapper left" >
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-tools" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_SETTINGS_LABEL'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label id="jform_order_process_type-lbl" for="jform_order_process_type" title="<?php echo JText::_('COM_JEPROSHOP_ORDER_PROCESS_TYPE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ORDER_PROCESS_TYPE_LABEL'); ?></label></div>
        					<div class="controls">
        						<select id="jform_order_process_type" name="jform[order_process_type]" >
        							<option value="standard" <?php if($this->order_process_type == 'standard'){ ?> selected="selected" <?php }?>><?php  echo JText::_('COM_JEPROSHOP_STANDARD_LABEL'); ?></option>
        							<option value="page_checkout" <?php if($this->order_process_type == 'page_checkout'){ ?> selected="selected" <?php }?>><?php  echo JText::_('COM_JEPROSHOP_PAGE_CHECKOUT_LABEL'); ?></option>
        						</select>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_allow_order_with_unregistered_customer" title="<?php echo JText::_('COM_JEPROSHOP_ALLOW_ORDER_WITH_UNREGISTERED_CUSTOMER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ALLOW_ORDER_WITH_UNREGISTERED_CUSTOMER_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" >
        							<input type="radio" id="jform_allow_order_with_unregistered_customer_on" name="jform[allow_order_with_unregistered_customer]" <?php if($this->allow_order_with_unregistered_customer == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_allow_order_with_unregistered_customer_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_allow_order_with_unregistered_customer_off" name="jform[allow_order_with_unregistered_customer]" <?php if($this->allow_order_with_unregistered_customer == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_allow_order_with_unregistered_customer_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label id="jform_deactivate_reordering_option" for="jform_" title="<?php echo JText::_('COM_JEPROSHOP_DEACTIVATE_REORDERING_OPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DEACTIVATE_REORDERING_OPTION_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" id="jform_deactivate_reordering_option">
        							<input type="radio" id="jform_deactivate_reordering_option_on" name="jform[deactivate_reordering_option]" <?php if($this->deactivate_reordering_option == 1){ ?> checked="checked" <?php } ?>value="1" /><label for="jform_deactivate_reordering_option_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_deactivate_reordering_option_off" name="jform[deactivate_reordering_option]" <?php if($this->deactivate_reordering_option == 0){ ?>checked="checked" <?php } ?> value="0" /><label for="jform_deactivate_reordering_option_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_minimum_amount_required_for_order" title="<?php echo JText::_('COM_JEPROSHOP_MINIMUM_AMOUNT_REQUIRED_FOR_ORDER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MINIMUM_AMOUNT_REQUIRED_FOR_ORDER_LABEL'); ?></label></div>
        					<div class="controls">
        						<div class="input-append" >
                                    <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
        							<input type="text" class="price_box hasTooltip" id="jform_img" value="<?php echo $this->minimum_amount_required_for_order; ?>" />
        							<?php if($this->currency->suffix != ""){  ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
        						</div>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_delay_shipping" title="<?php echo JText::_('COM_JEPROSHOP_DELAY_SHIPPING_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DELAY_SHIPPING_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" id="jform_delay_shipping" >
        							<input type="radio" id="jform_delay_shipping_on" name="jform[delay_shipping]" <?php if($this->delay_shipping == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_delay_shipping_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_delay_shipping_off" name="jform[delay_shipping]" <?php if($this->delay_shipping == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_delay_shipping_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_general_selling_condition" title="<?php echo JText::_('COM_JEPROSHOP_GENERAL_SELLING_CONDITION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_GENERAL_SELLING_CONDITION_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" id="jform_general_selling_condition" >
        							<input type="radio" id="jform_general_selling_condition_on" name="jform[general_selling_condition]" <?php if($this->general_selling_condition == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_general_selling_condition_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_general_selling_condition_off" name="jform[general_selling_condition]" <?php if($this->general_selling_condition == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_general_selling_condition_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label id="jform_" for="jform_" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
        					<div class="controls">
        						<select></select>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
        	<div class="half_wrapper right" >
                <div class="panel" >
                    <div class="panel-title" ><i class="icon-file" ></i> <?php echo JText::_('COM_JEPROSHOP_PDF_SETTINGS_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <div class="control-group" >
                            <div class="control-label" ><label for="" title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRODUCT_IMAGE_ON_INVOICE_DISPLAY_PRODUCT_IMAGE_ON_INVOICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRODUCT_IMAGE_ON_INVOICE_LABEL'); ?></label> </div>
                            <div class="controls" >
                                <fielset id="jform_display_product_image_on_invoice" class="radio btn-group" >
                                    <input type="radio" id="jform_display_product_image_on_invoice_on" name="jform[display_product_image_on_invoice]" <?php if($this->display_product_image_on_invoice == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_display_product_image_on_invoice_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                    <input type="radio" id="jform_display_product_image_on_invoice_off" name="jform[display_product_image_on_invoice]" <?php if($this->display_product_image_on_invoice == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_display_product_image_on_invoice_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                                </fielset>
                            </div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_display_product_image_on_delivery" title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRODUCT_IMAGE_ON_INVOICE_DISPLAY_PRODUCT_IMAGE_ON_DELIVERY_FILE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRODUCT_IMAGE_ON_INVOICE_LABEL'); ?></label> </div>
                            <div class="controls" >
                                <fielset id="jform_display_product_image_on_delivery" class="radio btn-group" >
                                    <input type="radio" id="jform_display_product_image_on_delivery_file_on" name="jform[display_product_image_on_delivery_file]" <?php if($this->display_product_image_on_delivery_file == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_display_product_image_on_delivery_file_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                    <input type="radio" id="jform_display_product_image_on_delivery_file_off" name="jform[display_product_image_on_delivery_file]" <?php if($this->display_product_image_on_delivery_file == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_display_product_image_on_delivery_file_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                                </fielset>
                            </div>
                        </div>
                    </div>
                </div>
        		<div class="panel" >
        			<div class="panel-title" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_SETTINGS_LABEL'); ?></div>
        			<div class="panel-content well" >
        				<div class="control-group" >
        					<div class="control-label" ><label id="jform_offer_gift_wrapping-lbl" for="jform_offer_gift_wrapping" title="<?php echo JText::_('COM_JEPROSHOP_OFFER_GIFT_WRAPPING_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_OFFER_GIFT_WRAPPING_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" id="jform_offer_gift_wrapping" >
        							<input type="radio" id="jform_offer_gift_wrapping_on" name="jform[offer_gift_wrapping]" <?php if($this->offer_gift_wrapping == '1'){ ?> checked="checked" <?php }?> value="1" /><label for="jform_offer_gift_wrapping_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_offer_gift_wrapping_off" name="jform[offer_gift_wrapping]" <?php if($this->offer_gift_wrapping == '0'){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_offer_gift_wrapping_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>        				
        				<div class="control-group" >
        					<div class="control-label" ><label id="jform_" for="jform_gift_wrapping_price" title="<?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_PRICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_PRICE_LABEL'); ?></label></div>
        					<div class="controls">
        						<div class="input-append" >
                                    <?php if($this->currency->prefix != ""){  ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
        							<input type="text" class="price_box hasTooltip" id="jform_gift_wrapping_price" name="jform[gift_wrapping_price]"  value="<?php echo $this->gift_wrapping_price; ?>" />
                                    <?php if($this->currency->suffix != ""){  ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
        						</div>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_gift_wrapping_tax" title="<?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_TAX_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_GIFT_WRAPPING_TAX_LABEL'); ?></label></div>
        					<div class="controls">
        						<select id="jform_gift_wrapping_tax" name="jform[gift_wrapping_tax]">

                                </select>
        					</div>
        				</div>
        				<div class="control-group" >
        					<div class="control-label" ><label for="jform_offer_recycled_wrapping" title="<?php echo JText::_('COM_JEPROSHOP_OFFER_RECYCLED_WRAPPING_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_OFFER_RECYCLED_WRAPPING_LABEL'); ?></label></div>
        					<div class="controls">
        						<fieldset class="radio btn-group" id="jform_offer_recycled_wrapping" >
        							<input type="radio" id="jform_offer_recycled_wrapping_on" name="jform[offer_recycled_wrapping]" <?php if($this->offer_recycled_wrapping == '1'){?> checked="checked" <?php } ?> value="1" /><label for="jform_offer_recycled_wrapping_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL')?></label>
        							<input type="radio" id="jform_offer_recycled_wrapping_off" name="jform[offer_recycled_wrapping]" <?php if($this->offer_recycled_wrapping == '0'){?> checked="checked" <?php } ?> value="0" /><label for="jform_offer_recycled_wrapping_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL')?></label>
        						</fieldset>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
        </div>
    </div>
</form>