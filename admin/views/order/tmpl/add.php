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

$document->addScript(JURI::base() . 'components/com_jeproshop/assets/javascript/typewatch/typewatch.js');
$document->addScript(JURI::base() . 'components/com_jeproshop/assets/javascript/jquery/plugins/autocomplete/jquery.autocomplete.js');
$document->addScript(JURI::base() . 'components/com_jeproshop/assets/javascript/jquery/plugins/fancybox/jquery.fancybox.js');
$document->addScript(JURI::base() . 'components/com_jeproshop/assets/javascript/tools/tools.js');
$document->addScript(JURI::base() . 'components/com_jeproshop/assets/javascript/script/order.js');

$script = 'jQuery(document).ready(function(){
        jQuery("#jform_add_new_order").JeproshopOrder({layout: \'add\'});
    });';

$document->addScriptDeclaration($script);
?>
<!--script type="text/javascript" >

    //var txt_show_carts = '{l s='Show carts and orders for this customer.' js=1}';
    //var txt_hide_carts = '{l s='Hide carts and orders for this customer.' js=1}';


    <?php  foreach($this->defaults_order_statues as $module => $order_status_id){ ?>
    defaults_order_statues['<?php echo $module; ?>'] = '<?php echo $order_status_id; ?>';
    <?php } ?>



</script -->
<div id="jform_add_new_order" >
<?php
if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?>  >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-order" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=invoices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-bill" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_BILLS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=returns'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-returns" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_RETURN_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=delivery'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-delivery" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_DELIVERY_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=refund'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-refund" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_REFUND_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=status'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-order-status" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=messages'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-messages" ></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGES_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel form-horizontal"  >
            <div class="panel-title" ><i class="icon-user" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMER_LABEL'); ?></div>
            <div class="panel-content well" >
                <div class="control-group" id="jform_search_form_customer" >
                    <div class="control-label" ><label for="jform_customer" title="<?php echo JText::_('COM_JEPROSHOP_SEARCH_CUSTOMER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SEARCH_A_CUSTOMER_LABEL'); ?></label></div>
                    <div class="controls"  >
                        <div class="input-append" >
                            <input type="text" id="jform_customer" value="" >
                            <button class="btn "><i class="icon-search" ></i> </button>
                        </div>
                        <span class="control-static" > <?php echo JText::_('COM_JEPROSHOP_OR_LABEL'); ?> </span>
                        <a class="fancybox_customer btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&use_ajax=1&' . JeproshopTools::getCustomerToken() . '=1'); ?>" ><i class="icon-plus-sign-alt" ></i> <?php echo JText::_('COM_JEPROSHOP_ADD_NEW_CUSTOMER_LABEL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel" ><div id="jform_customers" ></div></div>
        <div class="panel" style="display: none;" >
            <?php echo JHtml::_('bootstrap.startTabSet', 'cart_order_form', array('active' =>'cart'));
            echo JHtml::_('bootstrap.addTab', 'cart_order_form', 'cart', '<i class="icon-shopping-cart" ></i> ' . JText::_('COM_JEPROSHOP_CARTS_LABEL') ); ?>
            <div class="panel-content well">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_ID_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_DATE_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_TOTAL_LABEL'); ?></span></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <?php echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'cart_order_form', 'order', '<i class="icon-credit-card" ></i> ' . JText::_('COM_JEPROSHOP_ORDERS_LABEL')); ?>
            <div class="panel-content well">
                <table class="table">
                    <thead>
                        <tr>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_ID_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_DATE_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_TOTAL_PAID_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_PAYMENT_LABEL'); ?></span></th>
                            <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_STATUS_LABEL'); ?></span></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </div>
        <div class="panel" >
            <form class="form-horizontal" action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order'); ?>" method="post" autocomplete="off" >
                <div class="panel" id="products_part" style="display:none;">
                    <div class="panel-title"><i class="icon-shopping-cart"></i> <?php echo JText::_('COM_JEPROSHOP_CART_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <div class="control-group">
                            <div class="control-label">
				                <label title="" data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo JText::_('COM_JEPROSHOP_SEARCH_FOR_AN_EXISTING_PRODUCT_BY_TYPING_THE_FIRST_LETTERS_OF_ITS_NAME_LABEL'); ?>">
					                <?php echo JText::_('COM_JEPROSHOP_SEARCH_FOR_PRODUCT_LABEL'); ?>
				                </label>
                            </div>
                            <div class="controls" >
                                <input type="hidden" value="" id="jform_cart_id" name="cart_id" />
                                <div class="input-append">
                                    <input type="text" id="jform_product" value="" />
					                <button class="btn"><i class="icon-search"></i> </button>
                                </div>
                            </div>
                        </div>
                        <div class="control-group" id="jform_products_found" >
                            <div class="controls" >
                                <hr/>
                                <div id="product_list" class="form-group"></div>
                                <div id="attributes_list" class="form-group"></div>
                                <!-- @TODO: please be kind refacto -->
                                <div class="form-group">
                                    <div class="col-lg-9 col-lg-offset-3">
                                        <iframe id="customization_list" seamless>
                                            <html>
                                            <head>
                                                <?php if(isset($this->css_files_orders)){
                                                foreach ($this->css_files_orders as $css_uri => $media){ ?>
                                                <link href="<?php echo $css_uri; ?>" rel="stylesheet" type="text/css"
                                                      media="<?php echo $media; ?>"/>
                                                <?php }
                                                } ?>
                                            </head>
                                            <body>
                                            </body>
                                            </html>
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label" ><label for="jform_quantity" title="" ><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></label></div>
                            <div class="controls">
                                <input type="text" name="jform[quantity]" id="jform_quantity" class="form-control fixed-width-sm" value="1" />
                                <p class="help-none"><?php echo JText::_('COM_JEPROSHOP_IN_STOCK_LABEL'); ?> <span id="jform_quantity_in_stock"></span></p>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button type="button" class="btn btn-default" id="submitAddProduct" >
                                <i class="icon-ok text-success"></i>
                                <?php echo JText::_('COM_JEPROSHOP_ADD_TO_CART_LABEL'); ?></button>
                            </div>
                        </div>
                        <div id="jform_products_error" class="hide alert alert-danger"></div>
                        <hr/>

                        <table class="table" id="customer_cart">
                            <thead>
                                <tr>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_LABEL'); ?></span></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></span></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_REFERENCE_LABEL'); ?></span></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_UNIT_PRICE_LABEL'); ?></span></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></span></th>
                                    <th><span class="pull-right"><?php echo JText::_('COM_JEPROSHOP_PRICE_LABEL'); ?></span></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div class="control-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <div class="alert alert-warning"><?php echo JText::_('COM_JEPROSHOP_THE_PRICES_ARE_WITHOUT_TAXES_LABEL'); ?></div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><label for="jform_currency_id" ><?php echo JText::_('COM_JEPROSHOP_CURRENCY_LABEL'); ?></label></div>
                            <script type="text/javascript">
                                <?php foreach($this->currencies as $currency){ ?>
                                //currencies[<?php echo $currency->currency_id; ?>] = '<?php echo $currency->sign; ?>';
                               <?php } ?>
                            </script>
                            <div class="controls">
                                <select id="jform_currency_id" name="jform[currency_id]">
                                    <?php foreach($this->currencies as $currency){ ?>
                                    <option rel="<?php echo $currency->iso_code; ?>" value="<?php echo $currency->currency_id; ?>"><?php $currency->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label" ><label for="jform_lang_id" ><?php echo JText::_('COM_JEPROSHOP_LANGUAGE_LABEL'); ?></label></div>
                            <div class="controls">
                                <select id="jform_lang_id" name="jform[lang_id]" >
                                    <?php foreach($this->languages as $language){ ?>
                                    <option value="<?php echo $language->lang_id; ?>"><?php echo $language->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel" id="jform_vouchers_part" style="display:none;">
                    <div class="panel-title"><i class="icon-ticket"></i> <?php echo JText::_('COM_JEPROSHOP_VOUCHERS_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <div class="control-group">
                            <div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_SEARCH_FOR_A_VOUCHER_LABEL'); ?></label></div>
                            <div class="controls">
                                <div class="input-append">
                                    <input type="text" id="jform_voucher" value="" />
                                    <button class="btn"><i class="icon-search"></i> </button>
                                </div>
                                &nbsp;<span class="form-control-static"><?php echo JText::_('COM_JEPROSHOP_OR_LABEL'); ?>&nbsp;</span>
                                <a class="fancybox btn btn-default" href="{$link->getAdminLink('AdminCartRules')|escape:'html':'UTF-8'}&amp;addcart_rule&amp;liteDisplaying=1&amp;submitFormAjax=1#">
                                    <i class="icon-plus-sign-alt"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_NEW_VOUCHER_LABEL'); ?>
                                </a>
                            </div>
                        </div>

                        <table class="table" id="jform_voucher_list">
                            <thead>
                                <tr>
                                    <th><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></span></th>
                                    <th><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_VALUE_LABEL'); ?></span></th>
                                    <th><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="jform_vouchers_error" class="alert alert-warning" style="display: none;"></div>
                    </div>

                </div>

                <div class="panel" id="jform_address_part" style="display:none;">
                    <div class="panel-title"><i class="icon-envelope"></i> <?php echo JText::_('COM_JEPROSHOP_ADDRESSES_LABEL'); ?> </div>
                    <div class="panel-content well">
                        <div id="jform_addresses_error" class="alert alert-warning" style="display:none;"></div>
                        <div id="jform_address_delivery" class="half_wrapper_left">
                            <h4>
                                <i class="icon-truck"></i>
                                <?php echo JText::_('COM_JEPROSHOP_DELIVERY_LABEL'); ?>
                                <span class="row-margin-bottom">
                                    <select id="jform_address_delivery_id" name="jform[address_delivery_id]" ></select>
                                </span>
                            </h4>

                            <div class="well">
                                <a href="" id="jform_edit_delivery_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> <?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                <div id="jform_address_delivery_detail"></div>
                            </div>
                        </div>
                        <div id="jform_address_invoice" class="half_wrapper_right">
                            <h4>
                                <i class="icon-file-text"></i> <?php echo JText::_('COM_JEPROSHOP_INVOICE_LABEL'); ?>
                                <span class="row-margin-bottom">
                                    <select id="jform_address_invoice_id" name="jform[address_invoice_id]"></select>
                                </span>
                            </h4>

                            <div class="well">
                                <a href="" id="edit_invoice_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> <?php echo JText::_('COM_JEPROSHOP_Edit_LABEL'); ?></a>
                                <div id="address_invoice_detail"></div>
                            </div>
                        </div>
                        <div style="clear: both" ></div>
                        <div class="row">
                            <a class="fancybox btn btn-default pull-right" id="new_address" href="<?php echo JRoute::_('index.php?option=com_jeproshop&viewp=address&task=add&customer_id=' . $this->customer->customer_id . '&use_ajax=1&' . JeproshopTools::getAddressToken() . '=1', true, 1); ?>$link->getAdminLink('AdminAddresses')|escape:'html':'UTF-8'}&amp;addaddress&amp;id_customer=42&amp;liteDisplaying=1&amp;submitFormAjax=1#" >
                                <i class="icon-plus-sign-alt"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_A_NEW_ADDRESS_LABEL'); ?>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="panel" id="carriers_part" style="display:none;">
                    <div class="panel-title"><i class="icon-truck"></i>  <?php echo JText::_('COM_JEPROSHOP_SHIPPING_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <div id="jform_carriers_error" style="display:none;" class="alert alert-warning"></div>
                        <div id="jform_carrier_form">
                            <div class="control-group">
                                <div class="control-label" ><label for="jform_delivery_option" ><?php echo JText::_('COM_JEPROSHOP_DELIVERY_OPTION_LABEL'); ?></label></div>
                                <div class="controls">
                                    <select name="jform[delivery_option]" id="jform_delivery_option" ></select>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label  for="jform_shipping_price" ><?php echo JText::_('COM_JEPROSHOP_SHIPPING_PRICE_LABEL'); ?></label></div>
                                <div class="controls">
                                    <p id="jform_shipping_price" class="form-control-static" name="jform[shipping_price]"></p>
                                </div>
                            </div>
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_free_shipping"><?php echo JText::_('COM_JEPROSHOP_FREE_SHIPPING_LABEL'); ?></label></div>
                                <div class="controls">
					                <fieldset class="radio btn-group" id="jform_free_shipping" >
						                <input type="radio" name="jform[free_shipping]" id="jform_free_shipping_on" value="1" ><label for="jform_free_shipping_on" class="radioCheck"><?php echo JText::_('COM_JEPROSHOP_YES_LABEL'); ?></label>
						                <input type="radio" name="jform[free_shipping]" id="jform_free_shipping_off" value="0" checked="checked"><label for="jform_free_shipping_off" class="radioCheck"><?php echo JText::_('COM_JEPROSHOP_NO_LABEL'); ?></label>
                                    </fieldset>
                                </div>
                            </div>
                            <?php if(isset($this->recyclable_pack) && $this->recyclable_pack){ ?>
                            <div class="control-group">
                                <div class="controls">
                                    <p class="checkbox" >
                                        <label for="carrier_recycled_package">
                                            <input type="checkbox" name="jform[carrier_recycled_package]" value="1" id="jform_carrier_recycled_package" /> <?php echo JText::_('COM_JEPROSHOP_RECYCLED_PACKAGE_LABEL'); ?>
                                        </label>
                                    </p>
                                </div>
                            </div>
                            <?php }
                            if(isset($this->gift_wrapping) && $this->gift_wrapping){ ?>
                            <div class="control-group" >
                                <div class="checkbox controls">
                                    <label for="order_gift">
                                        <input type="checkbox" name="order_gift" id="jform_order_gift" value="1" />  <?php echo JText::_('COM_JEPROSHOP_GIFT_LABEL'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_gift_message"><?php echo JText::_('COM_JEPROSHOP_GIFT_MESSAGE_LABEL'); ?></label></div>
                                <div class="controls">
                                    <textarea id="jform_gift_message"  cols="40" rows="4"></textarea>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel" id="jform_summary_part" style="display:none;">
                    <div class="panel-title"> <i class="icon-align-justify"></i>  <?php echo JText::_('COM_JEPROSHOP_SUMMARY_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <div id="jform_send_email_feedback" class="hide alert"></div>

                        <div id="jform_cart_summary" class="row-margin-bottom">
                            <div class="panel">
                                <div class="control-group">
                                    <div class="control-label"><label for="jform_total_products" ><?php echo JText::_('COM_JEPROSHOP_TOTAL_PRODUCTS_LABEL'); ?></label></div>
                                    <div class="controls" ><span id="jform_total_products" class="size_l text-success"></span></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label" ><label for="jform_total_vouchers"><?php echo JText::_('COM_JEPROSHOP_TOTAL_VOUCHERS_LABEL'); ?></label></div>
                                    <div class="controls"><span id="jform_total_vouchers" class="size_l text-danger"></span></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label" ><label for="jform_total_shipping" ><?php echo JText::_('COM_JEPROSHOP_TOTAL_SHIPPING_LABEL'); ?></label></div>
                                    <div class="controls" ><span id="jform_total_shipping" class="size_l"></span></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label for="jform_total_taxes"><?php echo JText::_('COM_JEPROSHOP_TOTAL_TAXES_LABEL'); ?></label></div>
                                    <div class="controls" ><span id="jform_total_taxes" class="size_l"></span></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label" ><label id="jform_total_without_taxes" ><?php echo JText::_('COM_JEPROSHOP_TOTAL_WITHOUT_TAXES_LABEL'); ?></label></div>
                                    <div class="controls" ><span id="jform_total_without_taxes" class="size_l"></span></div>
                                </div>
                                <div class="control-group" >
                                    <div class="control-label" ><label for="jform_total_with_taxes" ><?php echo JText::_('COM_JEPROSHOP_TOTAL_WITH_TAXES_LABEL'); ?></label></div>
                                    <div class="controls" ><span id="jform_total_with_taxes" class="size_l"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="order_message_right span12">
                                <div class="control-group">
                                    <label class="control-label col-lg-3" for="jform_order_message"><?php echo JText::_('COM_JEPROSHOP_ORDER_MESSAGE_LABEL'); ?></label>
                                    <div class="controls">
                                        <textarea name="jform[order_message]" id="jform_order_message" rows="3" cols="45"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="col-lg-9 col-lg-offset-3">
                                        <a href="javascript:void(0);" id="jform_send_email_to_customer" class="btn btn-default">
                                            <i class="icon-credit-card"></i>
                                            <?php echo JText::_('COM_JEPROSHOP_SEND_AN_EMAIL_TO_THE_CUSTOMER_WITH_THE_LINK_TO_PROCESS_PAYMENT_LABEL'); ?>
                                        </a>
                                        <a target="_blank" id="go_order_process" href="" class="btn btn-link">
                                            <?php echo JText::_('COM_JEPROSHOP_GO_ON_PAYMENT_PAGE_TO_PROCESS_THE_PAYMENT_LABEL'); ?>
                                            <i class="icon-external-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label for="jform_payment_module_name"><?php echo JText::_('COM_JEPROSHOP_PAYMENT_LABEL'); ?></label></div>
                                    <div class="controls">
                                        <select name="jform[payment_module_name]" id="jform_payment_module_name">
                                            <?php foreach($this->payment_modules as $module){ ?>
                                            <option value="<?php echo $module->name; ?>" <?php if(isset($this->payment_module_name) && $module->name == $this->payment_module_name){ ?> selected="selected"<?php } ?>><?php echo $module->displayName; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div  class="control-label"><label for="jform_order_status_id" ><?php echo JText::_('COM_JEPROSHOP_ORDER_STATUS_LABEL'); ?></label></div>
                                    <div class="controls">
                                        <select name="jform[order_status_id]" id="jform_order_status_id">
                                            <?php foreach ($this->order_statues as $order_status){ ?>
                                            <option value="<?php echo $order_status->order_status_id; ?>" <?php if(isset($this->order_status_id) && $order_status->order_status_id == $this->order_status_id){ ?>selected="selected"<?php } ?>><?php echo $order_status->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submitAddOrder" class="btn btn-default pull-right" >
                                        <i class="icon-check"></i> <?php echo JText::_('COM_JEPROSHOP_CREATE_THE_ORDER_LABEL'); ?>
                                        </button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                    <input type="hidden" name="task" value="" />
            </form>
        </div>
    </div>
    <div id="jform_loader_container"><div id="loader"></div></div>
</div>

