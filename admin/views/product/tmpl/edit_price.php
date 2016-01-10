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

$script = 'jQuery(document).ready(function(){ alert({you can manage the page\'); ';
 ?>
 <div class="form_box_wrapper"  id="product_price" >
 	<div id="step_price" >
        <div class="panel" >
 		    <div class="panel-title"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_EDIT_PRICE_INFORMATION_TITLE'); ?></div>
            <div class="panel-content well" >
                <?php echo $this->productMultiShopCheckFields('Prices'); ?>
                <div class="alert alert-info"><?php echo JText::_('COM_JEPROSHOP_MUST_ENTER_EITHER_PRE_TAX_RETAIL_PRICE_MESSAGE'); ?></div>
                <div class="separation"></div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('wholesale_price', 'default'); ?>
                        <label for="jform_wholesale_price" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRE_TAX_WHOLE_SALE_PRICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRE_TAX_WHOLE_SALE_PRICE_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <div class="input-append" >
                            <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                            <input type="text" maxlength="14" name="price_field[wholesale_price]" id="jform_wholesale_price" value="<?php echo JeproshopTools::convertPrice($this->product->wholesale_price); ?>" onchange=" this.value.replace(/,/g,'.');" class="price_box" />
                            <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                        </div>
                        <p class="field_description"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_WHOLE_SALE_PRICE_DESCRIPTION'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('price', 'price'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRE_TAX_RETAIL_PRICE_TITLE_DESC'); ?>" ><?php if(!$this->context->country->display_tax_label || $this->tax_exclude_tax_option){ echo JText::_('COM_JEPROSHOP_PRODUCT_RETAIL_PRICE_LABEL'); }else{ echo JText::_('COM_JEPROSHOP_PRODUCT_PRE_TAX_RETAIL_PRICE_LABEL'); } ?></label>
                    </div>
                    <div class="controls">
                        <input type="hidden" id="jform_real_price_tax_excluded" name="price_field[price]" value=""/>
                        <div class="input-append" >
                            <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                            <input type="text" maxlength="14" id="jform_price_tax_excluded" name="price_field[price_displayed]" value="" class="price_box" />
                            <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                        </div>
                        <p class="field_description" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRE_TAX_RETAIL_PRICE_DESCRIPTION'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('tax_rules_group_id', 'default');  ?>
                        <label for="jform_tax_rules_group_id" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_TAX_RULE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_TAX_RULE_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <span  >
                            <select  name="price_field[tax_rules_group_id]" id="jform_tax_rules_group_id" <?php if($this->tax_exclude_tax_option){ ?> disabled="disabled" <?php } ?> >
                                <option value="0"  ><?php echo JText::_('COM_JEPROSHOP_NO_TAX_LABEL'); ?></option>
                                <?php foreach($this->tax_rules_groups as $tax_rules_group){ ?>
                                <option value="<?php echo $tax_rules_group->tax_rules_group_id; ?>" <?php if($this->product->getTaxRulesGroupId() == $tax_rules_group->tax_rules_group_id){ ?>selected="selected" <?php } ?> ><?php echo $tax_rules_group->name; ?></option>
                                <?php } ?>
                            </select>
                            <a class="button btn confirm_leave" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tax&task=add_rules_group&product_id=' . $this->product->product_id); ?>" >
                                <i class="icon-plus" ></i> &nbsp;<?php echo JText::_('COM_JEPROSHOP_CREATE_LABEL'); ?>
                            </a>
                            <?php if($this->tax_exclude_tax_option){ ?>
                                <span style="margin-left:10px; " ></span>
                                <input type="hidden" value="<?php echo $this->product->getTaxRulesGroupId(); ?>" name="price_field[tax_rules_group_id]" />
                            <?php } ?>
                        </span>
                    </div>
                </div>
                <?php if($this->tax_exclude_tax_option){ ?>
                    <div class="control-group alert" >
                        <?php echo JText::_('COM_JEPROSHOP_TAXES_ARE_CURRENTLY_DISABLED_LABEL'); ?>
                        <a class="btn btn-default" href="<?php echo JText::_('index.php?option=com_jeproshop&view=setting&task=tax'); ?>" ><?php  echo JText::_('COM_JEPROSHOP_CLICK_HERE_TO_OPEN_TAXES_CONFIGURATION_PAGE_LABEL'); ?>.</a>
                        <input type="hidden" value="<?php echo $this->product->getTaxRulesGroupId(); ?>" name="price_field[tax_rules_group_id]" />
                    </div>
                <?php } ?>
                <div class="control-group" <?php if(!$this->use_ecotax){ ?> style="display: none;" <?php } ?> >
                    <div class="control-label">
                        <?php echo $this->productMultiShopCheckbox('ecotax', 'default'); ?>
                        <label for="jform_ecotax" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRICE_USE_ECO_TAX_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRICE_USE_ECO_TAX_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <div clas="input-append" >
                            <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                            <input type="text" size="11" maxlength="14" id="jform_ecotax" name="price_field[ecotax]" value="<?php echo $this->product->ecotax; ?>"  class="price_box" />
                            <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                        </div>
                        <span style="margin-left: 10px;" ><?php echo "(" . JText::_('COM_JEPROSHOP_ALREADY_INCLUDED_IN_PRICE_MESSAGE') . ")"; ?></span>
                    </div>
                </div>
                <div class="control-group" <?php if(!$this->context->country->country_display_tax_label || $this->tax_exclude_tax_option){ ?> style="display:none;" <?php } ?> >
                    <div class="control-label"><label for="jform_price_tax_included" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_RETAIL_PRICE_WITH_TAX_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_RETAIL_PRICE_WITH_TAX_LABEL'); ?></label></div>
                    <div class="controls">
                        <div class="input-append" >
                            <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                            <input size="11" maxlength="14" id="jform_price_tax_included" type="text" value=""   class="price_box" />
                            <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                        </div>
                        <input id="jform_price_type" name="price_field[price_type]" type="hidden" value="TE" />
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php  echo $this->productMultiShopCheckbox('unit_price','unit_price'); ?>
                        <label for="jform_unit_price" title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_UNIT_PRICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_UNIT_PRICE_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <div class="input-append" >
                            <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                            <input id="jform_unit_price" name="price_field[unit_price]" type="text" value="<?php echo $this->unit_price;  ?>"  class="price_box" />
                            <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                        </div> &nbsp;&nbsp;
                        <?php echo JText::_('COM_JEPROSHOP_PER_LABEL'); ?>&nbsp;&nbsp;<input id="jform_unity" name="price_field[unity]" type="text" value="<?php echo htmlentities($this->product->unity); ?>"  class="price_box" />
                            <?php if($this->use_tax && $this->context->country->country_display_tax_label){ ?>
                            <span style="margin-left:15px">
                                <?php echo JText::_('COM_JEPROSHOP_OR_LABEL'); ?>
                                <?php echo $this->context->currency->prefix; ?> <span id="jform_unit_price_with_tax">0.00</span><?php echo $this->context->currency->suffix; ?>
                                <?php echo JText::_('COM_JEPROSHOP_PER_LABEL'); ?> <span id="jform_unity_second"><?php echo $this->product->unity; ?></span> <?php echo JText::_('COM_JEPROSHOP_WITH_TAX_LABEL'); ?>
                            </span>
                            <?php } ?>
                        <p><?php echo JText::_('COM_JEPROSHOP_EG_LABEL') . " " . JText::_('COM_JEPROSHOP_PER_LABEL') . " " . JText::_('COM_JEPROSHOP_UNIT_LABEL'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php  echo $this->productMultiShopCheckbox('on_sale','default'); ?>
                        <label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_ON_SALE_PRICE_TITLE_DESC'); ?>" >&nbsp;</label>
                    </div>
                    <div class="controls">
                        <p class="checkbox"  >
                            <input type="checkbox" name="price_field[on_sale]" id="jform_on_sale" style="padding-top: 5px;" <?php if($this->product->on_sale){ ?>checked="checked" <?php } ?> value="1" /><label for="jform_on_sale" class="t"><?php echo JText::_('COM_JEPROSHOP_PRODUCT_DISPLAY_ON_SALE_ICON_MESSAGE'); ?></label>
                        </p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_PRODUCT_FINAL_RETAIL_PRICE_TITLE_DESC'); ?>" ><b><?php echo JText::_('COM_JEPROSHOP_PRODUCT_FINAL_RETAIL_PRICE_LABEL'); ?></b></label></div>
                    <div class="controls">
                        <span style="font-weight: bold;">
                            <?php echo $this->context->currency->prefix; ?><span id="jform_final_price" >0.00</span><?php echo $this->context->currency->suffix; ?>
                            <span <?php if(!$this->use_tax){ ?> style="display:none; " <?php } ?> > ( <?php echo JText::_('COM_JEPROSHOP_TAX_INCLUDED_LABEL'); ?>)</span>
                        </span>
                        <span <?php if(!$this->use_tax){ ?> style="display:none; " <?php } ?> >
                            <?php if($this->context->country->display_tax_label){ echo ' / '; } ?>
                            <?php echo $this->context->currency->prefix . " "; ?><span id="jform_final_price_without_tax" ></span><?php echo  ' ' . $this->context->currency->suffix; if( $this->context->country->display_tax_label){ echo ' (' . JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL') . ')'; } ?>
                        </span>
                    </div>
                </div>
                <div class="panel-footer">
                    <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
                    <button type="submit" name="save_price" class="btn btn-default pull-right"  onclick="Joomla.submitbutton('save_price'); " ><i class="process-icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_AND_STAY_LABEL'); ?></button>
                </div>
            </div>
        </div>
        <div class="panel" >
            <?php if(isset($this->specific_price_modification_form)) { ?>
            <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_SPECIFIC_PRICE_LABEL'); ?></div>
            <div class="panel-content well" >
                <div class="hint" style="display:none; min-height:0;">
                    <?php echo JText::_('COM_JEPROSHOP_PRODUCT_SPECIFIC_PRICE_SETTING_MESSAGE'); ?>
                </div>
                <div class="control-group" >
                    <div class="control-label" ></div>
                    <div class="controls" >
                        <br /><a class="button btn btn-icon" href="#" id="jform_show_specific_price"  onclick="Joomla.submitbutton('add_specific_price'); "><i class="add-icon" ></i> <span><?php echo JText::_('COM_JEPROSHOP_ADD_NEW_SPECIFIC_PRICE_LABEL'); ?></span></a>
                        <a class="button bt-icon" href="#" id="jform_hide_specific_price" style="display:none" ><i class="cross-icon" ></i> <span><?php echo JText::_('COM_JEPROSHOP_CANCEL_NEW_SPECIFIC_PRICE_LABEL'); ?></span></a>
                        <br/>
                    </div>
                </div>
                <div class="control-group" id="jform_add_specific_price" style="display:none;">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_SHOP_ID_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_SHOP_ID_LABEL'); ?></label></div>
                    <div class="controls margin-form" >
                        <?php if(!$this->multi_shop){ ?>
                        <input type="hidden" name="price_field[sp_shop_id]" value="" />
                        <?php }else{ ?>
                        <select name="price_field[sp_shop_id]" id="jform_sp_shop_id" class="medium_select" >
                            <?php if(!$this->admin_one_shop){ ?><option value="0"><?php echo JText::_('COM_JEPROSHOP_ALL_SHOPS_LABEL'); ?></option><?php } ?>
                            <?php foreach($this->shops as $shop){ ?>
                            <option value="<?php echo $shop->shop_id; ?>"><?php echo htmlentities($shop->name); ?></option>
                            <?php } ?>
                        </select>&nbsp;&gt;&nbsp;
                        <?php } ?>
                        <select name="price_field[sp_currency_id]" id="jform_sp_currency_0" class="medium_select" >
                            <option value="0"><?php echo JText::_('COM_JEPROSHOP_ALL_CURRENCIES_LABEL'); ?></option>
                            <?php foreach($this->currencies as $currency){ ?>
                            <option value="<?php echo $currency->currency_id; ?>"><?php echo htmlentities($currency->name); ?></option>
                            <?php } ?>
                        </select>&nbsp;&gt;&nbsp;
                        <select name="price_field[sp_country_id]" id="jform_sp_country_id" class="medium_select" >
                            <option value="0"><?php echo JText::_('COM_JEPROSHOP_ALL_COUNTRIES_LABEL'); ?></option>
                            <?php foreach($this->countries as $country){ ?>
                            <option value="<?php echo $country->country_id; ?>"><?php echo htmlentities($country->name); ?></option>
                            <?php } ?>
                        </select>&nbsp;&gt;&nbsp;
                        <select name="price_field[sp_group_id]" id="jform_sp_group_id" class="medium_select" >
                            <option value="0"><?php echo JText::_('COM_JEPROSHOP_ALL_GROUPS_LABEL'); ?></option>
                            <?php foreach($this->groups as $group){ ?>
                            <option value="<?php echo $group->group_id; ?>"><?php echo htmlentities($group->name); ?></option>
                            <?php } ?>
                         </select>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_CUSTOMER_ID_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_CUSTOMER_ID_LABEL'); ?></label></div>
                    <div class="controls">
                        <input type="hidden" name="price_field[sp_customer_id]" id="jform_customer_id" value="0" />
                        <input type="text" name="price_field[customer]" value="<?php echo JText::_('COM_JEPROSHOP_ALL_CUSTOMERS_LABEL'); ?>" id="jform_customer" autocomplete="off" />
                        <i class="loading-icon"  id="jform_customer_loader" style="display: none;" ></i>
                        <div id="jform_customers"></div>
                    </div>
                </div>
                <?php if(count($this->combinations) != 0) { ?>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_COMBINATION_LABEL'); ?></label></div>
                    <div class="controls">
                        <select id="jform_sp_product_attribute_id" name="price_field[sp_product_attribute_id]" >
                            <option value="0"><?php echo JText::_('COM_JEPROSHOP_APPLY_TO_ALL_COMBINATIONS_LABEL'); ?></option>
                            <?php foreach($this->combinations as $combination) { ?>
                            <option value="<?php echo $combination->product_attribute_id; ?>"><?php echo $combination->attributes; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php }?>
                <div class="control-group">
                    <div class="control-label"><label title="<?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_AVAILABLE_FROM_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROSHOP_SPECIFIC_PRICE_AVAILABLE_FROM_LABEL') ; ?></label></div>
                    <div class="controls">
                        <input class="date_picker" type="text" name="price_field[sp_from]" value="" style="text-align: center" id="jform_sp_from" /><span style="font-weight:bold; color:#000000; font-size:12px"><?php echo " ". JText::_('COM_JEPROSHOP_TO_LABEL') . " "; ?></span>
                        <input class="date_picker" type="text" name="price_field[sp_to]" value="" style="text-align: center" id="jform_sp_to" />
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_STARTING_AT_LABEL'); ?></label></div>
                    <div class="controls">
                        <input type="text" name="price_field[sp_from_quantity]" value="1" size="3" /> <span style="font-weight:bold; color:#000000; font-size:12px"><?php echo JText::_('COM_JEPROSHOP_UNIT_LABEL'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>
                            <?php echo JText::_('COM_JEPROSHOP_PRODUCT_PRICE_LABEL'); ?>
                            <?php if($this->context->country->country_display_tax_label){ echo JText::_('COM_JEPROSHOP_PRODUCT_PRICE_TAX_EXCLUDED_LABEL'); } ?>
                        </label>
                    </div>
                    <div class="controls" ><div class="input-append">
                        <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                        <input type="text" disabled="disabled" name="price_field[sp_price]" id="jform_sp_price" value="<?php echo $this->product->price; ?>" class="price_box" />
                        <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                    </div></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label for="jform_leave_base_price" ><?php echo JText::_('COM_JEPROSHOP_LEAVE_BASE_PRICE_LABEL'); ?></label></div>
                    <div class="controls"><p class="checkbox" ><input id="jform_leave_base_price" type="checkbox" value="1" checked="checked" name="price_field[leave_base_price]" /></p></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_JEPROSHOP_APPLY_DISCOUNT_OF_LABEL'); ?></label></div>
                    <div class="controls">
                        <input type="text" name="price_field[sp_reduction]" value="0.00" class="price_box" />&nbsp;
                        <select name="price_field[sp_reduction_type]" id="jform_reduction" class="medium_select" >
                            <option selected="selected" >---</option>
                            <option value="amount"><?php echo JText::_('COM_JEPROSHOP_AMOUNT_LABEL'); ?></option>
                            <option value="percentage"><?php echo JText::_('COM_JEPROSHOP_PERCENTAGE_LABEL'); ?></option>
                        </select>
                        <p class="field_description"><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_APPLIED_AFTER_TAX_MESSAGE'); ?></p>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $this->specific_price_modification_form; ?>
                </div>
            </div>
            <?php } ?>
        </div>
 	</div>
 </div>