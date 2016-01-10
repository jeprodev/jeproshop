<?php
/**
 * @version     1.0.3
 * @package     Components
 * @subpackage  com_jeprolab
 * @link        http://jeprodev.net
 * @copyright   (C) 2009 - 2011
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
JHtml::_('formbehavior.chosen', 'select');

$doc = JFactory::getDocument();

$doc->addStyleSheet(JURI::base(true) . '/components/com_jeprolab/assets/themes/jeprolab/css/style.css');
/**
if(!$opc){
	$current_step ='address'; 
	//{capture name=path}{l s='Addresses'}{/capture}
	$back_order_page = JRoute::_('index.php?option=com_jeproshop&view=order');   //value="view.php"}
	?>
<h1 class="page-heading"><?php echo JText::_('COM_JEPROSHOP_ADDRESS_LABEL'); ?></h1>
<?php 
	//require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'order-steps.php'); 
	//require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'errorphp');
?>
<form action="{$link->getPageLink($back_order_page, true)|escape:'html':'UTF-8'}" method="post">
<?php } else{ 
	/** /$back_order_page" value="order-opc.php";
 ?
<h1 class="page-heading step-num"><span>1</span> <?php echo JText::_('COM_JEPROSHOP_ADDRESS_LABEL'); ?></h1>
	<div id="opc_account" class="opc-main-block">
		<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
<?php }?>
		<div class="addresses clearfix">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="address_delivery select form-group selector1">
<label for="id_address_delivery">{if $cart->isVirtualCart()}{l s='Choose a billing address:'}{else}{l s='Choose a delivery address:'}{/if}</label>
<select name="id_address_delivery" id="id_address_delivery" class="address_select form-control">
{foreach from=$addresses key=k item=address}
<option value="{$address.id_address|intval}"{if $address.id_address == $cart->id_address_delivery} selected="selected"{/if}>
{$address.alias|escape:'html':'UTF-8'}
</option>
{/foreach}
</select><span class="waitimage"></span>
</div>
<p class="checkbox addressesAreEquals"{if $cart->isVirtualCart()} style="display:none;"{/if}>
<input type="checkbox" name="same" id="addressesAreEquals" value="1"{if $cart->id_address_invoice == $cart->id_address_delivery || $addresses|@count == 1} checked="checked"{/if} />
<label for="addressesAreEquals">{l s='Use the delivery address as the billing address.'}</label>
</p>
</div>
<div class="col-xs-12 col-sm-6">
<div id="address_invoice_form" class="select form-group selector1"{if $cart->id_address_invoice == $cart->id_address_delivery} style="display: none;"{/if}>
{if $addresses|@count > 1}
<label for="id_address_invoice" class="strong">{l s='Choose a billing address:'}</label>
<select name="id_address_invoice" id="id_address_invoice" class="address_select form-control">
{section loop=$addresses step=-1 name=address}
<option value="{$addresses[address].id_address|intval}"{if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice} selected="selected"{/if}>
{$addresses[address].alias|escape:'html':'UTF-8'}
</option>
{/section}
</select><span class="waitimage"></span>
{else}
<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1&select_address=1{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add'}" class="button button-small btn btn-default">
<span>
{l s='Add a new address'}
<i class="icon-chevron-right right"></i>
</span>
</a>
{/if}
</div>
</div>
</div> <!-- end row -->
<div class="row">
<div class="col-xs-12 col-sm-6"{if $cart->isVirtualCart()} style="display:none;"{/if}>
<ul class="address item box" id="address_delivery">
</ul>
</div>
<div class="col-xs-12 col-sm-6">
<ul class="address alternate_item{if $cart->isVirtualCart()} full_width{/if} box" id="address_invoice">
		</ul>
		</div>
		</div> <!-- end row -->
		<p class="address_add submit">
		<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add'}" class="button button-small btn btn-default">
		<span>{l s='Add a new address'}<i class="icon-chevron-right right"></i></span>
		</a>
		</p>
		{if !$opc}
		<div id="ordermsg" class="form-group">
		<label>{l s='If you would like to add a comment about your order, please write it in the field below.'}</label>
		<textarea class="form-control" cols="60" rows="6" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea>
		</div>
		{/if}
		</div> <!-- end addresses -->
		{if !$opc}
		<p class="cart_navigation clearfix">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="{$back}" />
		<a href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Previous'}" class="button-exclusive btn btn-default">
		<i class="icon-chevron-left"></i>
		{l s='Continue Shopping'}
		</a>
		<button type="submit" name="processAddress" class="button btn btn-default button-medium">
		<span>{l s='Proceed to checkout'}<i class="icon-chevron-right right"></i></span>
		</button>
		</p>
		</form>
		{else}
		</div> <!--  end opc_account -->
		{/if}
		{strip}
		{if !$opc}
			{addJsDef orderProcess='order'}
			{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
			{addJsDef currencyRate=$currencyRate|floatval}
			{addJsDef currencyFormat=$currencyFormat|intval}
			{addJsDef currencyBlank=$currencyBlank|intval}
			{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
			{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
			{addJsDefL name=CloseTxt}{l s='Submit' js=1}{/addJsDefL}
			{/if}
			{capture}{if $back}&mod={$back|urlencode}{/if}{/capture}
			{capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
			{addJsDef addressUrl=$smarty.capture.addressUrl}
{capture}{'&multi-shipping=1'|urlencode}{/capture}
{addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
{capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='}{/capture}
{addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
{addJsDef formatedAddressFieldsValuesList=$formatedAddressFieldsValuesList}
{addJsDef opc=$opc|boolval}
{capture}<h3 class="page-subheading">{l s='Your billing address' js=1}</h3>{/capture}
{addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{capture}<h3 class="page-subheading">{l s='Your delivery address' js=1}</h3>{/capture}
{addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd}" title="{l s='Update' js=1}"><span>{l s='Update' js=1}<i class="icon-chevron-right right"></i></span></a>{/capture}
{addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{/strip}
/*
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab'); ?>" method="post" id="contact" class="form-validate form-horizontal" enctype="multipart/form-data" >
	<fieldset style="margin-top: -25px;">
		<legend><?php //echo JText::_('COM_JEPROLAB_CONTACT_REGISTRATION'); ?></legend>
		<div style="clear:both; "></div>
		<div class="section_wrapper" id="cantact_information" >
			<div class="section_wrapper_title" ><?php echo JText::_('COM_JEPROLAB_CUSTOMER_CONTACT_INFROMATION_TITLE'); ?></div>
			<div class="control-group" >
				<div class="control-label" ><label for="jform_customer_name" id="jform_customer_name-lbl" ><?php echo JText::_('COM_JEPROLAB_YOUR_NAME_LABEL'); ?></label></div>
				<div class="controls" ><input type="text" name="jform[customer_name]" id="jform_customer_name" value="" required="required" /></div>
			</div>
			<div class="control-group" >
				<div class="control-label" ><label for="jform_customer_phone" id="jform_customer_phone-lbl" ><?php echo JText::_('COM_JEPROLAB_YOUR_PHONE_LABEL'); ?></label></div>
				<div class="controls" ><input type="tel" name="jform[customer_phone]" id="jform_customer_phone" value="" required="required"/></div>
			</div>
			<div class="control-group" >
				<div class="control-label" ><label for="jform_customer_company" id="jform_customer_company-lbl" ><?php echo JText::_('COM_JEPROLAB_YOUR_COMPANY_LABEL'); ?></label></div>
				<div class="controls" ><input type="text" name="jform[customer_company]" id="jform_customer_name" value="" required="required" /></div>
			</div>
			<div class="control-group" >
				<div class="control-label" ><label for="jform_customer_email" id="jform_customer_email-lbl" ><?php echo JText::_('COM_JEPROLAB_YOUR_EMAIL_LABEL'); ?></label></div>
				<div class="controls" ><input type="email" name="jform[customer_email]" id="jform_customer_email" value="" required="required" /></div>
			</div>
			<div class="control-group" >
				<div class="control-label" ></div>
				<div class="controls" ><?php echo JText::_('COM_JEPROLAB_REQUESTED_TIME_MESSAGE')?></div>
			</div>
			<div class="control-group" >
				<div class="control-label" ><label for="jform_call_me_from" id="jform_call_me_from-lbl" ><?php echo JText::_('COM_JEPROLAB_CALL_ME_FROM_LABEL'); ?></label></div>
				<div class="controls" >
					<input type="time" id="jform_call_me_from" name="jform[call_me_from]" value="" /> 
					<b><?php echo JText::_('COM_JEPROLAB_TO_LABEL')?></b>
					<input type="time" id="jform_call_me_to" name="jform[call_me_to]" value="" />
				</div>
			</div>
		</div>
	</fieldset>
	<div class="form-actions">
		<button type="submit"  class="btn btn-primary validate" ><?php echo JText::_('COM_JEPROLAB_SEND_LABEL'); ?></button>
		<a class="btn" href="<?php echo JRoute::_('index.php'); ?>" title="<?php echo JText::_('JCANCEL');?>" ><?php echo JText::_('JCANCEL');?></a>
		<input type="hidden" name="option" value="com_jeprolab" />
		<input type="hidden" name="view" value="request" />
		<input type="hidden" name="task" value="add_contact" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form> */