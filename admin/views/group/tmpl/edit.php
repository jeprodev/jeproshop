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
$icon_directory = JURI::base() . 'components/com_jeproshop/assets/themes/' . $css_dir . '/images/';
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-address" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-group" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-cart" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-thread" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=contact'); ?>" class="btn jeproshop_sub_menu " ><i class="icon-contact" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')); ?></a>                
            </fieldset>
        </div>
        <div class="separation"></div>
        <div class="panel" >
        	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_CREATE_NEW_GROUP_LABEL'); ?></div>
        	<div class="panel-content well" >
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_name" ><?php echo JText::_('COM_JEPROSHOP_GROUP_NAME_LABEL'); ?></label></div>
        			<div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', 'jform'); ?></div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_group_reduction" ><?php echo JText::_('COM_JEPROSHOP_GROUP_REDUCTION_LABEL'); ?></label></div>
        			<div class="controls" >
        				<div class="input-append" >
        					<input type="text" class="price_box hasTooltip" id="jform_group_reduction" name="jform[group_reduction]" value="0.00"  />
        					<button type="button" class="btn" id="jform_img" >%</button>
        				</div>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_price_display_method" ><?php echo JText::_('COM_JEPROSHOP_PRICE_DISPLAY_METHOD_LABEL'); ?></label></div>
        			<div class="controls" >
        				<select id="jform_price_display_method" name="jform[price_display_method]" >
        					<option value="0" ><?php echo JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL'); ?></option>
        					<option value="1" ><?php echo JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?></option>
        				</select>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_show_prices" title="<?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_IN_THIS_GROUP_CAN_VIEW_PRICES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRICES_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_show_prices" >
        					<input type="radio" id="jform_show_prices_yes" name="jform[show_prices]" value="1" checked="checked" /><label for="jform_show_prices_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_show_prices_no" name="jform[show_prices]" value="0" /><label for="jform_show_prices_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_categories_tree"><?php echo JText::_('COM_JEPROSHOP_GROUP_DISCOUNT_CATEGORIES_LABEL'); ?></label></div>
        			<div class="controls" ></div>
        		</div>
        	</div> 
        </div>
        <div class="panel" >
        	 <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_AUTHORIZED_MODULES_LABEL'); ?></div>
        	<div class="panel-content well" >
                <div class="" >
        		<div class="half_wrapper left" >
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_social_networking_sharing" ><?php echo JText::_('COM_JEPROSHOP_SOCIAL_NETWORK_SHARING_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_social_network_sharing" >
                                <input type="radio" id="jform_social_network_sharing_yes" name="jform[social_network_sharing]" value="1" checked="checked" /><label for="jform_social_network_sharing_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_social_network_sharing_no" name="jform[social_network_sharing]" value="0" /><label for="jform_social_network_sharing_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_banners_block" ><?php echo JText::_('COM_JEPROSHOP_BANNERS_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_banners_block" >
                                <input type="radio" id="jform_banners_block_yes" name="jform[banners_block]" value="1" checked="checked" /><label for="jform_banners_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_banners_block_no" name="jform[banners_block]" value="0" /><label for="jform_banners_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_bank_wire" ><?php echo JText::_('COM_JEPROSHOP_BANK_WIRE_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_bank_wire" >
                                <input type="radio" id="jform_bank_wire_yes" name="jform[bank_wire]" value="1" checked="checked" /><label for="jform_bank_wire_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_bank_wire_no" name="jform[bank_wire]" value="0" /><label for="jform_bank_wire_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_best_sales" ><?php echo JText::_('COM_JEPROSHOP_BEST_SALES_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_best_sales" >
                                <input type="radio" id="jform_best_sales_yes" name="jform[best_sales]" value="1" checked="checked" /><label for="jform_best_sales_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_best_sales_no" name="jform[best_sales]" value="0" /><label for="jform_best_sales_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_cart_block" ><?php echo JText::_('COM_JEPROSHOP_CART_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cart_block" >
                                <input type="radio" id="jform_cart_block_yes" name="jform[cart_block]" value="1" checked="checked" /><label for="jform_cart_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_cart_block_no" name="jform[cart_block]" value="0" /><label for="jform_cart_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_social_block"><?php echo JText::_('COM_JEPROSHOP_SOCIAL_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_social_block" >
                                <input type="radio" id="jform_social_block_yes" name="jform[social_block]" value="1" checked="checked" /><label for="jform_social_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_social_block_no" name="jform[social_block]" value="0" /><label for="jform_social_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_categories_block" ><?php echo JText::_('COM_JEPROSHOP_CATEGORIES_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_categories_block" >
                                <input type="radio" id="jform_categories_block_yes" name="jform[categories_block]" value="1" checked="checked" /><label for="jform_categories_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_categories_block_no" name="jform[categories_block]" value="0" /><label for="jform_categories_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_currencies_block" ><?php echo JText::_('COM_JEPROSHOP_CURRENCIES_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_currencies_block" >
                                <input type="radio" id="jform_currencies_block_yes" name="jform[currencies_block]" value="1" checked="checked" /><label for="jform_currencies_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_currencies_block_no" name="jform[currencies_block]" value="0" /><label for="jform_currencies_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_facebook_like_block"><?php echo JText::_('COM_JEPROSHOP_FACEBOOK_LIKE_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_facebook_like_block" >
                                <input type="radio" id="jform_facebook_like_block_yes" name="jform[facebook_like_block]" value="1" checked="checked" /><label for="jform_facebook_like_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_facebook_like_block_no" name="jform[facebook_like_block]" value="0" /><label for="jform_facebook_like_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_language_selector_block"><?php echo JText::_('COM_JEPROSHOP_LANGUAGE_SELECTOR_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_language_selector_block" >
                                <input type="radio" id="jform_language_selector_block_yes" name="jform[language_selector_block]" value="1" checked="checked" /><label for="jform_language_selector_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_language_selector_block_no" name="jform[language_selector_block]" value="0" /><label for="jform_language_selector_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP__LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cart_block" >
                                <input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_cms_block"><?php echo JText::_('COM_JEPROSHOP_CMS_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cms_block" >
                                <input type="radio" id="jform_cms_block_yes" name="jform[cms_block]" value="1" checked="checked" /><label for="jform_cms_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_cms_block_no" name="jform[cms_block]" value="0" /><label for="jform_cms_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_cms_customer_information"><?php echo JText::_('COM_JEPROSHOP_CMS_CUSTOMER_INFORMATION_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cms_customer_information" >
                                <input type="radio" id="jform_cms_customer_information_yes" name="jform[cms_customer_information]" value="1" checked="checked" /><label for="jform_cms_customer_information_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_cms_customer_information_no" name="jform[cms_customer_information]" value="0" /><label for="jform_cms_customer_information_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_contact_block"><?php echo JText::_('COM_JEPROSHOP_CONTACT_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_contact_block" >
                                <input type="radio" id="jform_contact_block_yes" name="jform[contact_block]" value="1" checked="checked" /><label for="jform_contact_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_contact_block_no" name="jform[contact_block]" value="0" /><label for="jform_contact_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_contact_information_block"><?php echo JText::_('COM_JEPROSHOP_CONTACT_INFORMATION_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_contact_information_block" >
                                <input type="radio" id="jform_contact_information_block_yes" name="jform[contact_information_block]" value="1" checked="checked" /><label for="jform_contact_information_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_contact_information_block_no" name="jform[contact_information_block]" value="0" /><label for="jform_contact_information_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_manufacturer_block" ><?php echo JText::_('COM_JEPROSHOP_MANUFACTURER_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_manufacturer_block" >
                                <input type="radio" id="jform_manufacturer_block_yes" name="jform[manufacturer_block]" value="1" checked="checked" /><label for="jform_manufacturer_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_manufacturer_block_no" name="jform[manufacturer_block]" value="0" /><label for="jform_manufacturer_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_my_account_block" ><?php echo JText::_('COM_JEPROSHOP_MY_ACCOUNT_BLOCK_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_my_account_block" >
                                <input type="radio" id="jform_my_account_block_yes" name="jform[my_account_block]" value="1" checked="checked" /><label for="jform_my_account_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_my_account_block_no" name="jform[my_account_block]" value="0" /><label for="jform_my_account_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_footer_my_account_block" ><?php echo JText::_('COM_JEPROSHOP__LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_footer_my_account_block" >
                                <input type="radio" id="jform_footer_my_account_block_yes" name="jform[footer_my_account_block]" value="1" checked="checked" /><label for="jform_footer_my_account_block_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_footer_my_account_block_no" name="jform[footer_my_account_block]" value="0" /><label for="jform_footer_my_account_block_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
        		</div>
        		<div class="half_wrapper right"  >
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP__LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cart_block" >
                                <input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP__LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cart_block" >
                                <input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP__LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_cart_block" >
                                <input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_catalog_statistics"><?php echo JText::_('COM_JEPROSHOP_CATALOG_STATISTICS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_catalog_statistics" >
                                <input type="radio" id="jform_catalog_statistics_yes" name="jform[catalog_statistics]" value="1" checked="checked" /><label for="jform_catalog_statistics_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_catalog_statistics_no" name="jform[catalog_statistics]" value="0" /><label for="jform_catalog_statistics_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_catalog_evaluation"><?php echo JText::_('COM_JEPROSHOP_CATALOG_EVALUATION_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_catalog_evaluation" >
                                <input type="radio" id="jform_catalog_evaluation_yes" name="jform[catalog_evaluation]" value="1" checked="checked" /><label for="jform_catalog_evaluation_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_catalog_evaluation_no" name="jform[catalog_evaluation]" value="0" /><label for="jform_catalog_evaluation_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_GRAB_STATISTIC_DATA_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_grab_statistics_data" >
                                <input type="radio" id="jform_grab_statistics_data_yes" name="jform[grab_statistics_data]" value="1" checked="checked" /><label for="jform_grab_statistics_data_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_grab_statistics_data_no" name="jform[grab_statistics_data]" value="0" /><label for="jform_grab_statistics_data_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_BROWSER_AND_OPERATING_SYSTEM_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_browser_and_operating_system" >
                                <input type="radio" id="jform_browser_and_operating_system_yes" name="jform[browser_and_operating_system]" value="1" checked="checked" /><label for="jform_browser_and_operating_system_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_browser_and_operating_system_no" name="jform[browser_and_operating_system]" value="0" /><label for="jform_browser_and_operating_system_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_DASHBOARD_STATISTICS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_dashboard_statistics" >
                                <input type="radio" id="jform_dashboard_statistics_yes" name="jform[dashboard_statistics]" value="1" checked="checked" /><label for="jform_dashboard_statistics_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_dashboard_statistics_no" name="jform[dashboard_statistics]" value="0" /><label for="jform_dashboard_statistics_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_ONLINE_VISITORS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_online_visitors" >
                                <input type="radio" id="jform_online_visitors_yes" name="jform[online_visitors]" value="1" checked="checked" /><label for="jform_online_visitors_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_online_visitors_no" name="jform[online_visitors]" value="0" /><label for="jform_online_visitors_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_INFORMATION_LETTER_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_information_letter" >
                                <input type="radio" id="jform_information_letter_yes" name="jform[information_letter]" value="1" checked="checked" /><label for="jform_information_letter_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_information_letter_no" name="jform[information_letter]" value="0" /><label for="jform_information_letter_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_AFFILIATED_WEBSITE_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_affiliated_website" >
                                <input type="radio" id="jform_affiliated_website_yes" name="jform[affiliated_website]" value="1" checked="checked" /><label for="jform_affiliated_website_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_affiliated_website_no" name="jform[affiliated_website]" value="0" /><label for="jform_affiliated_website_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_DETAILS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_customer_details" >
                                <input type="radio" id="jform_customer_details_yes" name="jform[customer_details]" value="1" checked="checked" /><label for="jform_customer_details_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_customer_details_no" name="jform[customer_details]" value="0" /><label for="jform_customer_details_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DETAIL_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_product_detail" >
                                <input type="radio" id="jform_product_detail_yes" name="jform[product_detail]" value="1" checked="checked" /><label for="jform_product_detail_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_product_detail_no" name="jform[product_detail]" value="0" /><label for="jform_product_detail_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_ACCOUNTS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_customers_accounts" >
                                <input type="radio" id="jform_customers_accounts_yes" name="jform[customers_accounts]" value="1" checked="checked" /><label for="jform_customers_accounts_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_customers_accounts_no" name="jform[customers_accounts]" value="0" /><label for="jform_customers_accounts_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_orders" >
                                <input type="radio" id="jform_orders_yes" name="jform[orders]" value="1" checked="checked" /><label for="jform_orders_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_orders_no" name="jform[orders]" value="0" /><label for="jform_orders_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_STORE_SEARCH_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_store_search" >
                                <input type="radio" id="jform_store_search_yes" name="jform[store_search]" value="1" checked="checked" /><label for="jform_store_search_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_store_search_no" name="jform[store_search]" value="0" /><label for="jform_store_search_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_QUANTITIES_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_available_quantities" >
                                <input type="radio" id="jform_available_quantities_yes" name="jform[available_quantities]" value="1" checked="checked" /><label for="jform_available_quantities_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_available_quantities_no" name="jform[available_quantities]" value="0" /><label for="jform_available_quantities_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_VISITS_VISITORS_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_visits_visitors" >
                                <input type="radio" id="jform_visits_visitors_yes" name="jform[visits_visitors]" value="1" checked="checked" /><label for="jform_visits_visitors_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_visits_visitors_no" name="jform[visits_visitors]" value="0" /><label for="jform_visits_visitors_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_THEME_CONFIGURATOR_LABEL'); ?></label></div>
                        <div class="controls" >
                            <fieldset class="radio btn-group" id="jform_theme_configurator" >
                                <input type="radio" id="jform_theme_configurator_yes" name="jform[theme_configurator]" value="1" checked="checked" /><label for="jform_theme_configurator_yes" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                                <input type="radio" id="jform_theme_configurator_no" name="jform[theme_configurator]" value="0" /><label for="jform_theme_configurator_no" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                            </fieldset>
                        </div>
                    </div>
        		</div>
                    <div style="clear:both" ></div>
                </div>
        	</div>
        	<div style="clear:both; margin-bottom:10px; " ></div>
        </div>
        <div style="clear:both" ></div>
    	<input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
        <div style="clear:both" ></div>
    </div>    
 </form>