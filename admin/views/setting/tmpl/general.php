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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting');?>"  method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=general'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=theme'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-themes" ></i> <?php echo JText::_('COM_JEPROSHOP_THEMES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-image" ></i> <?php echo JText::_('COM_JEPROSHOP_IMAGES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-shop" ></i> <?php echo JText::_('COM_JEPROSHOP_SHOP_STORE_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=search'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-search" ></i> <?php echo JText::_('COM_JEPROSHOP_SEARCH_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=geolocation'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_GEOLOCATION_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-setting" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_SETTING_TITLE'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_improve_front_safety" title="<?php echo JText::_('COM_JEPROSHOP_IMPROVE_FRONT_SITE_SAFETY_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_IMPROVE_FRONT_SAFETY_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $helper->radioButton('improve_front_safety', 'edit', $this->improve_front_safety); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_allow_iframes_in_html_field" title="<?php echo JText::_('COM_JEPROSHOP_ALLOW_IFRAMES_IN_HTML_FIELDS_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ALLOW_IFRAMES_IN_HTML_FIELDS_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $helper->radioButton('allow_iframes_in_html_field', 'edit', $this->allow_iframes_in_html_field); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_USE_PURIFIER_LIBRARY_LABEL'); ?></label></div>
                    <div class="controls" ><?php  echo $helper->radioButton('use_purifier_library', 'edit', $this->use_purifier_library); ?></div>
                </div>
                <!-- div class="control-group" >
                    <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" /></div>
                </div -->
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_price_round_mode" title="<?php echo JText::_('COM_JEPROSHOP_PRICE_ROUND_MODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRICE_ROUND_MODE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_price_round_mode" name="jform[price_round_mode]" >
                            <option value="0" <?php if($this->price_round_mode == 0){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_THE_CLOSEST_UPPER_VALUE_LABEL'); ?></option>
                            <option value="1" <?php if($this->price_round_mode == 1){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_THE_CLOSEST_LOWEST_VALUE_LABEL'); ?></option>
                            <option value="2" <?php if($this->price_round_mode == 2){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_INFINITE_LABEL'); ?></option>
                            <option value="3" <?php if($this->price_round_mode == 3){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_ZERO_LABEL'); ?></option>
                            <option value="4" <?php if($this->price_round_mode == 4){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_THE_CLOSEST_EVEN_VALUE_LABEL'); ?></option>
                            <option value="5" <?php if($this->price_round_mode == 5){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TO_THE_CLOSEST_ODD_VALUE_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_round_mode_type" title="<?php echo JText::_('COM_JEPROSHOP_ROUND_MODE_TYPE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ROUND_MODE_TYPE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_round_mode_type" name="jform[round_mode_type]" >
                            <option value="" <?php if($this->round_mode_type == 1){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_ITEM_LABEL'); ?></option>
                            <option value="" <?php if($this->round_mode_type == 2){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_LINE_LABEL'); ?></option>
                            <option value="" <?php if($this->round_mode_type == 3){ ?>selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ROUND_TOTAL_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_number_of_decimals" title="<?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_DECIMALS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NUMBER_OF_DECIMALS_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_number_of_decimals" name="jform[number_of_decimals]" value="<?php echo $this->number_of_decimals; ?>"/></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_display_supplier_manufacturer" title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_SUPPLIER_AND_MANUFACTURER_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_DISPLAY_SUPPLIER_AND_MANUFACTURER_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $helper->radioButton('display_supplier_manufacturer', 'edit', $this->display_supplier_manufacturer); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_display_best_sells" title="<?php echo JText::_('COM_JEPROSHOP_DISPLAY_BEST_SELLS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_BEST_SELLS_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $helper->radioButton('display_best_sells', 'edit', $this->display_best_sells); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_activate_multishop" title="<?php echo JText::_('COM_JEPROSHOP_ACTIVATE_MULTISHOP_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ACTIVATE_MULTISHOP_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $helper->radioButton('activate_multishop', 'edit', $this->activate_multishop); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_shop_activity" title="<?php echo JText::_('COM_JEPROSHOP_SHOP_MAIN_ACTIVITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_MAIN_ACTIVITY_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select name="jform[shop_activity]" id="jform_shop_activity" >
                            <option value="0" >--<?php echo JText::_('COM_JEPROSHOP_CHOOSE_YOUR_MAIN_ACTIVITY_LABEL'); ?>--</option>
                            <option value="animals_and_pets" <?php if($this->shop_activity == 'animals_and_pets'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ANIMALS_AND_PETS_LABEL'); ?></option>
                            <option value="art_and_culture" <?php if($this->shop_activity == 'art_and_culture'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_ART_AND_CULTURE_LABEL'); ?></option>
                            <option value="babies" <?php if($this->shop_activity == 'barbies'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_BARBIES_LABEL'); ?></option>
                            <option value="beauty_and_personal_care" <?php if($this->shop_activity == 'beauty_and_personal_care'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_BEAUTY_AND_PERSONAL_CARE_LABEL'); ?></option>
                            <option value="cars" <?php if($this->shop_activity == 'cars'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_CARS_LABEL'); ?></option>
                            <option value="computer_hardware_and_software" <?php if($this->shop_activity == 'computer_hardware_and_software'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_COMPUTER_HARDWARE_AND_SOFTWARE_LABEL'); ?></option>
                            <option value="download" <?php if($this->shop_activity == 'download'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_DOWNLOAD_LABEL'); ?></option>
                            <option value="fashion_and_accessories" <?php if($this->shop_activity == 'fashion_and_accessories'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_FASHION_AND_ACCESSORIES_LABEL'); ?></option>
                            <option value="flowers_gifts_and_crafts" <?php if($this->shop_activity == 'flowers_gifts_and_crafts'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_FLOWERS_GIFTS_AND_CRAFTS_LABEL'); ?></option>
                            <option value="food_and_beverage" <?php if($this->shop_activity == 'food_and_beverage'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_FOOD_AND_BEVERAGE_LABEL'); ?></option>
                            <option value="hifi_photo_and_video" <?php if($this->shop_activity == 'hifi_photo_and_video'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_HIFI_PHOTO_AND_VIDEO_LABEL'); ?></option>
                            <option value="home_and_garden" <?php if($this->shop_activity == 'home_and_garden'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_HOME_AND_GARDEN_LABEL'); ?></option>
                            <option value="home_appliances" <?php if($this->shop_activity == 'home_appliances'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_HOME_APPLIANCES_LABEL'); ?></option>
                            <option value="jewelry" <?php if($this->shop_activity == 'jewelry'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_JEWELRY_LABEL'); ?></option>
                            <option value="lingerie_and_adult" <?php if($this->shop_activity == 'lingerie_and_adult'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_LINGERIE_AND_ADULT_LABEL'); ?></option>
                            <option value="mobile_and_telecom" <?php if($this->shop_activity == 'mobile_and_telecom'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_MOBILE_AND_TELECOM_LABEL'); ?></option>
                            <option value="services"  <?php if($this->shop_activity == 'services'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_SERVICES_LABEL'); ?></option>
                            <option value="shoes_and_accessories"  <?php if($this->shop_activity == 'shoes_and_accessories'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_SHOES_AND_ACCESSORIES_LABEL'); ?></option>
                            <option value="sport_and_entertainment" <?php if($this->shop_activity == 'sport_and_entertainment'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_SPORT_AND_ENTERTAINMENT_LABEL'); ?></option>
                            <option value="travel" <?php if($this->shop_activity == 'travel'){ ?> selected="selected" <?php } ?>  ><?php echo JText::_('COM_JEPROSHOP_TRAVEL_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
            </div><!-- end panel container -->
        </div>
    </div>
</form>