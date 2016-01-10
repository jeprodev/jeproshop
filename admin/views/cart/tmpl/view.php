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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-address" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-group" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-cart" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-thread" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=contact'); ?>" class="btn jeproshop_sub_menu " ><i class="icon-contact" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')); ?></a>
            </fieldset>
        </div>
        <div class="separation"></div>
        <div class="panel" >
            <div class="panel-content well" ></div>
        </div>
        <div class="form_layout" >
            <div class="half_wrapper left" >
                <div class="panel" >
                    <div class="panel-title" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROSHOP_CUSTOMER_INFORMATION_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <?php if(isset($this->customer->customer_id)){ ?>
                            <a class="btn btn-default pull-right" href="mailto:<?php echo $this->customer->email; ?>"><i class="icon-envelope"></i> <?php echo $this->customer->email; ?></a>
                            <h2>
                                <?php if($this->customer->gender_id == 1){ ?>
                                <i class="icon-male"></i>
                                <?php }elseif($this->customer->gender_id == 2){ ?>
                                <i class="icon-female"></i>
                                <?php }else{ ?>
                                <i class="icon-question"></i>
                                <?php } ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&customer_id=' . $this->customer->customer_id . '&task=view'); ?>" ><?php echo $this->customer->firstname . ' ' . $this->customer->lastname; ?></a>
                            </h2>
                            <div class="form-horizontal">
                                <div class="control-group">
                                    <div class="control-label"><label for=""><?php echo JText::_('COM_JEPROSHOP_ACCOUNT_REGISTRATION_DATE_LABEL'); ?></label></div>
                                    <div class="controls"><p class=""><?php echo JeproshopTools::dateFormat($this->customer->date_add); ?></p></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><label ><?php echo JText::_('COM_JEPROSHOP_VALID_ORDERS_PLACED_LABEL'); ?></label></div>
                                    <div class="controls"><p class=""><?php echo $this->customer_stats->nb_orders; ?></p></div>
                                </div>
                                <div class="form-group">
                                    <div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_TOTAL_SPENT_SINCE_REGISTRATION_LABEL'); ?></label></div>
                                    <div class="controls"><p class=""><?php echo JeproshopTools::displayPrice($this->customer_stats->total_orders, $this->currency); ?></p></div>
                                </div>
                            </div>
                        <?php }else{ echo JText::_('COM_JEPROSHOP_UNREGISTERED_GUEST_LABEL'); } ?>
                    </div>
                </div>
            </div>
            <div class="half_wrapper right" >
                <div class="panel" >
                    <div class="panel-title"><i class="icon-user"></i> <?php echo JText::_('COM_JEPROSHOP_ORDER_INFORMATION_LABEL'); ?></div>
                    <div class="panel-content well" >
                        <?php if(isset($this->order->order_id)){ ?>
                        <h2><a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=view&order_id=' . $this->order->order_id . '&' . JeproshopTools::getOrderFormToken() . '=1'); ?>" > <?php echo JText::_('COM_JEPROSHOP_ORDER_LABEL') . ' N° ' . $this->order->order_id; ?></a></h2>
                        <?php echo JText::_('COM_JEPROSHOP_MADE_ON_LABEL') . ' ' . JeproshopTools::dateFormat($this->order->date_add);
                        }else { ?>
                            <h2><?php echo JText::_('COM_JEPROSHOP_NO_ORDER_WAS_CREATED_FROM_THIS_CART_LABEL'); ?></h2>
                            <?php if ($this->customer->customer_id) { ?>
                                <a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=order&task=create&cart_id=' . (int)$this->cart->cart_id . '&' . JeproshopTools::getOrderFormToken() . '=1'); ?>" >
                                    <i class="icon-shopping-cart"></i> <?php echo JText::_('COM_JEPROSHOP_CREATE_A_NEW_ORDER_FROM_THIS_CART_LABEL'); ?>
                                </a>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
            <div class="clear" style="clear:both; height:20px;">&nbsp;</div>
        </div>
        <div class="clear" style="height:20px;">&nbsp;</div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-archive"></i> <?php echo JText::_('COM_JEPROSHOP_CART_SUMMARY_LABEL'); ?></div>
            <div class="panel-content well" >
                <table class="table table-bordered" id="orderProducts">
                    <thead>
                        <tr>
                            <th class="nowrap">&nbsp;</th>
                            <th class="nowrap" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_LABEL'); ?></span></th>
                            <th class="nowrap"><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_UNIT_PRICE_LABEL'); ?></span></th>
                            <th class="nowrap"><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></span></th>
                            <th class="nowrap"><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_STOCK_LABEL'); ?></span></th>
                            <th class="nowrap"><span class="pull-right"><?php echo JText::_('COM_JEPROSHOP_TOTAL_LABEL'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($this->products as $product) {
                            if (isset($customized_datas[$product->product_id][$product->product_attribute_id][$product->address_delivery_id])) { ?>
                        <tr>
                            <td><?php echo $product->image; ?></td>
                            <td>
                                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product&task=update&product_id=' . $product->product_id . '&' . JeproshopTools::getProductToken() . '=1'); ?>" >
                                    <span class="product_name"><?php echo $product->name; ?></span>
                                    <?php if(isset($product->attributes)){ echo '<br/>' . $product->attributes; } ?> <br/>
                                    <?php if($product->reference){ echo JText::_('COM_JEPROSHOP_REFERENCE_LABEL') . ' ' . $product->reference; }
                                    if($product->reference && $product->supplier_reference){ echo '/' . $product->supplier_reference; } ?>
                                </a>
                            </td>
                            <td class="nowrap"><?php echo JeproshopTools::displayPrice($product->price_wt, $this->currency); ?></td>
                            <td class="nowrap"><?php echo $product->customization_quantity; ?></td>
                            <td class="nowrap"><?php echo $product->qty_in_stock; ?></td>
                            <td class="nowrap"><?php echo JeproshopTools::displayPrice($product->total_customization_wt, $this->currency); ?></td>
                        </tr>
                        <?php foreach ($customized_datas[$product->product_id][$product->product_attribute_id][$product->address_delivery_id] as $customization) { ?>
                        <tr>
                            <td colspan="2">
                                <?php foreach($customization->datas as $type => $datas){
                                    if($type == JeproshopProductModelProduct::CUSTOMIZE_FILE){ ?>
                                <ul style="margin: 0; padding: 0; list-style-type: none;">
                                    <?php foreach($datas as $index => $data){ ?>
                                    <li style="display: inline; margin: 2px;">
                                        <a href="<?php echo JRoute::_('displayImage.php?img=' . $data->value . '&name=' . $this->order->order_id . '-file' . $index); ?> " target="_blank">
                                            <img src="<?php echo COM_JEPROSHOP_IMAGE_DIR . $data->value. '_small'; ?>" alt=""/>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php }elseif($type == JeproshopProductModelProduct::CUSTOMIZE_TEXT_FIELD){ ?>
                                <div class="form-horizontal">
                                    <?php foreach($datas as $index => $data){ ?>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <label class="">
                                                <strong>
                                                    <?php if($data->name){ echo $data->name; }else{ echo JText::_('COM_JEPROSHOP_TEXT_LABEL') . ' #' . $index; } ?>
                                                </strong>
                                            </label>
                                        </div>
                                        <div class="controls" >
                                            <p class="form-control-static"><?php echo $data->value; ?></p>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php }
                                } ?>
                            </td>
                            <td></td>
                            <td class="text-center"><?php echo $customization->quantity; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php }
                            }

                        if ($product->cart_quantity > $product->customization_quantity) { ?>
                        <tr>
                            <td><?php echo $product->image; ?></td>
                            <td>
                                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product&task=update&product_id=' . $product->product_id . '&' . JeproshopTools::getProductToken() . '=1'); ?>" >
                                    <span class="product_name"><?php echo $product->name; ?></span><?php if(isset($product->attributes)){ echo '<br/>' . $product->attributes; } ?> <br/>
                                    <?php if($product->reference){ echo JText::_('COM_JEPROSHOP_REFERENCE_LABEL') . ' ' . $product->reference; }
                                    if($product->reference && $product->supplier_reference){ echo ' / ' . $product->supplier_reference; } ?>
                                </a>
                            </td>
                            <td class="nowrap"><span class="pull-right"> <?php echo JeproshopTools::displayPrice($$product->product_price, $this->currency); ?></span></td>
                            <td class="nowrap center"><?php echo  (int)($product->cart_quantity - $product->customization_quantity); ?></td>
                            <td class="nowrap center"><?php echo $product->qty_in_stock; ?></td>
                            <td class="nowrap"><span class="pull-right"> <?php echo JeproshopTools::displayPrice($product->product_total, $this->currency); ?></span></td>
                        </tr>
                        <?php }
                        } ?>
                        <tr>
                            <td colspan="5"><?php echo JText::_('COM_JEPROSHOP_TOTAL_COST_OF_PRODUCTS_LABEL'); ?></td>
                            <td class="nowrap" ><span class="pull-right"><?php echo JeproshopTools::displayPrice($this->total_products, $this->currency); ?></span></td>
                        </tr>
                        <?php if($this->total_discounts != 0){ ?>
                        <tr>
                            <td colspan="5"><?php echo JText::_('COM_JEPROSHOP_TOTAL_VALUE_OF_VOUCHERS_LABEL'); ?></td>
                            <td class="nowrap" ><span class="pull-right"><?php echo JeproshopTools::displayPrice($this->total_discounts,  $this->currency); ?></span></td>
                        </tr>
                        <?php }
                        if($this->total_wrapping > 0){ ?>
                        <tr>
                            <td colspan="5"><?php echo JText::_('COM_JEPROSHOP_TOTAL_COST_OF_GIFT_WRAPPING_LABEL'); ?></td>
                            <td class="nowrap" ><span class="pull-right"><?php echo JeproshopTools::displayPrice($this->total_wrapping, $this->currency); ?></span></td>
                        </tr>
                        <?php }
                        if($this->cart->getOrderTotal(true, JeproshopCartModelCart::ONLY_SHIPPING) > 0){ ?>
                        <tr>
                            <td colspan="5"><?php echo JText::_('COM_JEPROSHOP_TOTAL_COST_OF_SHIPPING_LABEL'); ?></td>
                            <td class="nowrap" ><span class="pull-right"><?php echo JeproshopTools::displayPrice($this->total_shipping, $this->currency); ?></span></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="5" class=" success"><strong><?php echo JText::_('COM_JEPROSHOP_TOTAL_LABEL'); ?></strong></td>
                            <td class="nowrap" ><span class="pull-right success"><strong><?php echo JeproshopTools::displayPrice($this->total_price,  $this->currency); ?></strong></span></td>
                        </tr>
                    </tbody>
                </table>
                <?php if($this->discounts){ ?>
                <table class="table">
                    <tr>
                        <th><img src="../img/admin/coupon.gif" alt="<?php echo JText::_('COM_JEPROSHOP_DISCOUNTS_LABEL'); ?>" /><?php  echo JText::_('COM_JEPROSHOP_DISCOUNT_NAME_LABEL'); ?></th>
                        <th align="center" style="width: 100px"><?php echo JText::_('COM_JEPROSHOP_VALUE_LABEL'); ?></th>
                    </tr>
                    <?php foreach($this->discounts as $discount){ ?>
                    <tr>
                        <td><a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=discount&task=update&discount_id=' . $discount->discount_id . '&' . JeproshopTools::getDiscountToken() . '=1'); ?>"><?php echo $discount->name; ?></a></td>
                        <td class="text-center">- <?php echo JeproshopTools::displayPrice($discount->value_real, $this->currency); ?></td>
                    </tr>
                    <?php } ?>
                </table>
                <?php } ?>
                <div class="alert alert-warning">
                    <?php
                    echo JText::_('COM_JEPROSHOP_FOR_THIS_PARTICULAR_CUSTOMER_GROUP_PRICES_ARE_DISPLAYED_AS_LABEL') . ' : <b>' . (($this->order->getTaxCalculationMethod() == COM_JEPROSHOP_TAX_EXCLUDED) ?  JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL') : JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL') ). '</b>';  ?>
                </div>
                <div class="clear" style="height:20px;">&nbsp;</div>
            </div>
        </div>
    </div>
</form>