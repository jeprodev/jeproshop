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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop');?>"  method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >    
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=general'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_GENERAL_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=order'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gears" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=customer'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-customer" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=theme'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-themes" ></i> <?php echo JText::_('COM_JEPROSHOP_THEMES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=image'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-image" ></i> <?php echo JText::_('COM_JEPROSHOP_IMAGES_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-shop" ></i> <?php echo JText::_('COM_JEPROSHOP_SHOP_STORE_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=search'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-search" ></i> <?php echo JText::_('COM_JEPROSHOP_SEARCH_LABEL'); ?></a>
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=setting&task=geolocation'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-globe" ></i> <?php echo JText::_('COM_JEPROSHOP_GEOLOCATION_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel" >
        	<div class="panel-title" ><i class="icon-" ></i><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_SETTING_TITLE'); ?></div>
        	<div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_REGISTRATION_PROCESS_TYPE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_registration_process_type" name="jform[registration_process_type]">
                            <option value="account_only" <?php if($this->registration_process_type == 'account_only'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ACCOUNT_ONLY_LABEL'); ?></option>
                            <option value="account_with_address" <?php if($this->registration_process_type == 'account_with_address'){ ?> selected="selected" <?php } ?> ><?php echo JText::_('COM_JEPROSHOP_ACCOUNT_WITH_ADDRESS_LABEL'); ?></option>
                        </select>
                    </div>
                </div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_require_phone_number" title="<?php echo JText::_('COM_JEPROSHOP_REQUIRE_PHONE_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_REQUIRE_PHONE_NUMBER_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_require_phone_number" >
        					<input type="radio" id="jform_require_phone_number_on" name="jform[require_phone_number]" <?php if($this->require_phone_number == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_require_phone_number_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_require_phone_number_off" name="jform[require_phone_number]" <?php if($this->require_phone_number == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_require_phone_number_off"><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_refresh_cart_after_identification" title="<?php echo JText::_('COM_JEPROSHOP_REFRESH_CART_AFTER_IDENTIFICATION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_REFRESH_CART_AFTER_IDENTIFICATION_LABEL'); ?></label></div>
        			<div class="controls" >
                        <fieldset class="radio btn-group" id="jform_refresh_cart_after_identification" >
                            <input type="radio" id="jform_refresh_cart_after_identification_on" name="jform[refresh_cart_after_identification]" <?php if($this->refresh_cart_after_identification == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_refresh_cart_after_identification_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                            <input type="radio" id="jform_refresh_cart_after_identification_off" name="jform[refresh_cart_after_identification]" <?php if($this->refresh_cart_after_identification == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_refresh_cart_after_identification_off"><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                        </fieldset>
                    </div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_email_on_registration" title="<?php echo JText::_('COM_JEPROSHOP_SEND_EMAIL_ON_REGISTRATION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SEND_EMAIL_ON_REGISTRATION_LABEL'); ?></label></div>
        			<div class="controls" >
                        <fieldset class="radio btn-group" id="jform_email_on_registration" >
                            <input type="radio" id="jform_email_on_registration_on" name="jform[email_on_registration]" <?php if($this->email_on_registration == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_email_on_registration_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
                            <input type="radio" id="jform_email_on_registration_off" name="jform[email_on_registration]" <?php if($this->email_on_registration == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_email_on_registration_off"><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                        </fieldset>
                    </div>
        		</div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_password_regeneration_delay"><?php echo JText::_('COM_JEPROSHOP_PASSWORD_REGENERATION_DELAY_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_password_regeneration_delay" name="jform[password_regeneration_delay]" value="<?php echo $this->password_regeneration_delay; ?>" ></div>
                </div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_enable_b2b_mode" title="<?php echo JText::_('COM_JEPROSHOP_ENABLE_B2B_MODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ENABLE_B2B_MODE_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_enable_b2b_mode" >
        					<input type="radio" id="jform_enable_b2b_mode_on" name="jform[enable_b2b_mode]" <?php if($this->enable_b2b_mode == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_enable_b2b_mode_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_enable_b2b_mode_off" name="jform[enable_b2b_mode]" <?php if($this->enable_b2b_mode == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_enable_b2b_mode_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_activate_newsletter_subscription" title="<?php echo JText::_('COM_JEPROSHOP_ACTIVATE_NEWSLETTER_SUBSCRIPTION_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ACTIVATE_NEWSLETTER_SUBSCRIPTION_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_activate_newsletter_subscription" >
        					<input type="radio" id="jform_activate_newsletter_subscription_off" name="jform[activate_newsletter_subscription]" <?php if($this->activate_newsletter_subscription == 1){ ?> checked="checked" <?php } ?> value="0"  /><label for="jform_activate_newsletter_subscription_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_activate_newsletter_subscription_off" name="jform[activate_newsletter_subscription]" <?php if($this->activate_newsletter_subscription == 0){ ?> checked="checked" <?php } ?> value="0" /><label for="jform_activate_newsletter_subscription_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_activate_opt_in" title="<?php echo JText::_('COM_JEPROSHOP_ACTIVATE_OPT_IN_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_ACTIVATE_OPT_IN_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_activate_opt_in" >
        					<input type="radio" id="jform_activate_opt_in_on" name="jform[activate_opt_in]" <?php if($this->activate_opt_in == 1){ ?> checked="checked" <?php } ?> value="1" /><label for="jform_activate_opt_in_on" ><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_activate_opt_in_off" name="jform[activate_opt_in]" <?php if($this->activate_opt_in == 0){ ?> checked="checked" <?php } ?> value="0"  /><label for="jform_activate_opt_in_off" ><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<!--div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" >
        					<input type="radio" /><label><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
        					<input type="radio" /><label><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
        			<div class="controls" >
        				<select >
        					
        				</select>
        			</div>
        		</div -->
        	</div><!-- end panel container -->
        </div>
        
    </div>
</form>