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
?>
<div class="form_box_wrapper"  id="product_quantities" >
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_QUANTITIES_FOR_SALE_TITLE'); ?></div>
    <div class="panel-content well" >
        <?php if(!$this->stock_management){ ?>
        <div class="alert alert-info"><?php echo JText::_('COM_JEPROSHOP_STOCK_MANAGEMENT_IS_DISABLED_MESSAGE'); ?> ?></div>
        <?php }else{ ?>
        <div class="alert alert-info">
            <?php echo JText::_('COM_JEPROSHOP_THIS_INTERFACE_ALLOW_YOU_TO_MANAGE_AVAILABLE_QUANTITIES_MESSAGE') . '<br />' .
                JText::_('COM_JEPROSHOP_YOU_CAN_CHOOSE_WHETHER_OR_NOT_TO_USE_ADVANCED_STOCK_MANAGEMENT_SYSTEM_FOR_THIS_PRODUCT_MESSAGE') . '<br />' .
                JText::_('COM_JEPROSHOP_YOU_CAN_MANUALLY_SPECIFY_THE_QUANTITIES_FOR_THE_PRODUCT_EACH_PRODUCT_COMBINATION_OR_YOU_CAN_CHOOSE_TO_AUTOMATICALLY_DETERMINE_THESE_QUANTITIES_BASED_ON_YOUR_STOCK_IF_ADVANCED_STOCK_MANAGEMENT_IS_ACTIVATED_MESSAGE') . '<br />' .
                JText::_('COM_JEPROSHOP_IN_THIS_CASE_QUANTITIES_CORRESPOND_TO_THE_REAL_STOCK_QUANTITIES_IN_THE_WAREHOUSES_CONNECTED_WITH_THE_CURRENT_SHOP_OR_CURRENT_GROUP_OF_SHOPS_MESSAGE') . '<br />' .
                JText::_('COM_JEPROSHOP_FOR_PACKS_IF_IT_HAS_PRODUCTS_THAT_USE_ADVANCED_STOCK_MANAGEMENT_YOU_HAVE_TO_SPECIFY_A_COMMON_WAREHOUSE_FOR_THESE_PRODUCTS_IN_THE_PACKS_MESSAGE') . '<br />' .
                JText::_('COM_JEPROSHOP_ALSO_PLEASE_NOTE_THAT_WHEN_A_PRODUCT_HAS_COMBINATIONS_ITS_DEFAULT_COMBINATION_WILL_BE_USED_IN_STOCK_MOVEMENTS_MESSAGE');
            ?>
        </div>
        <?php echo $this->productMultiShopCheckFields('Prices'); ?>
        <div class="separation"></div>
    <?php if($this->show_quantities){ ?>
    <div class="control-group" <?php if($this->product->is_virtual || $this->product->cache_is_pack){ ?>style="display:none;"<?php }?> class="row stock_for_virtual_product">
        <div class="control-label" ></div>
        <div class="controls" >
        	<p class="checkbox">
                <label for="jform_advanced_stock_management" >
                    <input type="checkbox" name="quantities[advanced_stock_management]" class="advanced_stock_management" id="jform_advanced_stock_management"
                    	<?php if(($this->product->advanced_stock_management == 1) && ($this->stock_management_active == 1)){ ?> value="1" checked="checked" <?php } else { ?> value="0" <?php } ?>
                        <?php if($this->stock_management_active == 0 || $this->product->cache_is_pack){ ?> disabled="disabled" <?php } ?> />
                   	<?php echo JText::_('COM_JEPROSHOP_WANNA_USE_ADVANCED_STOCK_MANAGEMENT'); ?>
                </label>
            </p>
            <?php if($this->stock_management_active == 0 && !$this->product->cache_is_pack){ ?>
                <p class="help-block" ><i class="icon_warning_sign" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_ADVANCED_STOCK_MANAGEMENT_LABEL'); ?></p>
            <?php } elseif($this->product->cache_is_pack){ ?>
                <p class="help-block" ><?php echo JText::_('COM_JEPROSHOP_THIS_PARAMETER_DEPENDS_ON_PRODUCTS_IN_THE_PACK_MESSAGE'); ?></p>
            <?php } ?>
        </div>
    </div>
    <div <?php if($this->product->is_virtual || $this->product->cache_is_pack){ ?> style="display:none;" <?php } ?> class="stock_for_virtual_product control-group" >
          <div class="control-label" ><label  for="depends_on_stock_1" ><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_QUANTITY_LABEL'); ?></label></div>
         <div class="controls" >
         	<p class="radio">				
				<input type="radio" id="depends_on_stock_1" name="depends_on_stock" class="depends_on_stock"  value="1"
					<?php if($this->product->depends_on_stock == 1 && $this->stock_management_active == 1){ ?>	checked="checked" <?php } ?>
					<?php if($this->stock_management_active == 0 || $this->product->advanced_stock_management == 0 || $this->product->cache_is_pack){ ?> disabled="disabled" <?php } ?> />
				<label for="depends_on_stock_1">				
					<?php 
						echo JText::_('COM_JEPROSHOP_THE_AVAILABLE_QUANTITIES_FOR_THE_CURRENT_PRODUCT_AND_ITS_COMBINATIONS_ARE_BASED_ON_THE_STOCK_IN_YOUR_WAREHOUSE_USING_ADVANCED_STOCK_MANAGEMENT_LABEL'); 
					
						if(($this->stock_management_active == 0 || $this->product->advanced_stock_management == 0) && !$this->product->cache_is_pack){ 
							echo '&nbsp;-&nbsp;' . JText::_('COM_JEPROSHOP_THIS_REQUIRES_YOU_TO_ENABLE_ADVANCED_STOCK_MANAGEMENT_GLOBALLY_OR_THIS_PRODUCT_PRODUCT_LABEL'); 
						}else if($this->product->cache_is_pack){ 
							echo '&nbsp;-&nbsp;' . JText::_('COM_JEPROSHOP_THIS_PARAMETER_DEPENDS_ON_THE_PRODUCT_IN_THE_PACK_LABEL'); 
						} ?>
				</label>
			</p>
			<p class="radio" >
				<label for="depends_on_stock_0" for="depends_on_stock_0">
					<input type="radio"  id="depends_on_stock_0" name="depends_on_stock" class="depends_on_stock" value="0"
						<?php if($this->product->depends_on_stock == 0 || $this->stock_management_active == 0){ ?> checked="checked" <?php } ?> />
						<?php echo JText::_('COM_JEPROSHOP_I_WANT_TO_SPECIFY_AVAILABLE_QUANTITIES_MANUALLY_LABEL'); ?>	
				</label>
			</p>
         </div>
    </div>
    <div class="control-group" >
        <div class="control-label" ></div>
        <div class="controls" >
    		<?php if(isset($this->pack_quantity)){ ?>
			<div class="alert alert-info">
				<p><?php echo JText::_('COM_JEPROSHOP_WHEN_A_PRODUCT_HAS_COMBINATIONS_QUANTITIES_WILL_BE_BASED_ON_THE_DEFAULT_COMBINATION_LABEL'); ?></p>
				<p><?php echo JText::_('COM_JEPROSHOP_GIVEN_THE_QUANTITIES_OF_THE_PRODUCTS_IN_THIS_PACK_THE_MAXIMUM_QUANTITY_SHOULD_BE_LABEL') . ' ' . $this->pack_quantity ?></p>
			</div>	
			<?php } ?>
		</div>
	</div>
    <div class="control-group" >
        <div class="control-label" ></div>
        <div class="controls" >
        	<table class="table" >
        		<thead>
                    <tr>
                        <th class="nowrap" width="10%" ><?php  echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTE_ID_LABEL'); ?></th>
                        <th class="nowrap center" width="8%" ><?php  echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></th>
                        <th class="nowrap" ><?php  echo JText::_('COM_JEPROSHOP_DESIGNATION_LABEL'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->attributes as $index => $attribute){ ?>
                    <tr class="row_<?php echo $index % 2; ?>">
                    	<td><span><?php echo $this->available_quantity[$attribute->product_attribute_id]; ?></span></td>
                        <td class="available_quantity center" id="jform_quantity_<?php echo $attribute->product_attribute_id; ?>" >
                            <input type="text" value="<?php echo $this->available_quantity[$attribute->product_attribute_id]; ?>" class="quantity_box" />
                        </td>
                        <td><?php echo $this->product_designation[$attribute->product_attribute_id]; ?></td>
                    </tr>
                    <?php } ?>
                 </tbody>
        	</table>
        </div>
    </div>
    <div class="control-group" >
        <div class="control-label" ></div>
        <div class="controls" >
            <fieldset class="radio btn-group">
                <input type="radio" name="quantities[out_of_stock]" id="jform_out_of_stock_1" value="0" <?php if($this->product->out_of_stock == 0){ ?> checked="checked" <?php } ?> />
                <label for="jform_out_of_stock_1" ><?php echo JText::_('COM_JEPROSHOP_DENY_ORDERS_LABEL'); ?></label>
                <input type="radio" name="quantities[out_of_stock]" id="jform_out_of_stock_2" value="1" <?php if($this->product->out_of_stock == 1){ ?> checked="checked" <?php } ?> />
                <label for="jform_out_of_stock_2" ><?php echo JText::_('COM_JEPROSHOP_ALLOW_ORDERS_LABEL'); ?></label>
                <!-- input type="radio" name="quantities[out_of_stock]" id="jform_out_of_stock_3" value="2" <?php if($this->product->out_of_stock == 2){ ?> checked="checked" <?php } ?> /-->
                <label for="jform_out_of_stock_3" ><?php echo JText::_('COM_JEPROSHOP_DEFAULT_LABEL') . ' : '; 
                if($this->order_out_of_stock == 1){
					echo JText::_('COM_JEPROSHOP_ALLOW_ORDERS_LABEL');
				}else{ echo JText::_('COM_JEPROSHOP_DENY_ORDERS_LABEL'); } ?></label>
            </fieldset>                                    
        </div>
    </div>
    <?php }else{ ?>
    <div class="alert alert-warning">
		<p><?php echo JText::_('COM_JEPROSHOP_IT_IS_NOT_POSSIBLE_TO_MANAGE_QUANTITIES_WHEN_MESSAGE'); ?></p>
		<ul>
            <li><?php echo JText::_('COM_JEPROSHOP_YOU_ARE_CURRENTLY_MANAGING_ALL_OF_YOUR_SHOPS_MESSAGE'); ?></li>
            <li><?php echo JText::_('COM_JEPROSHOP_YOU_ARE_CURRENTLY_MANAGING_A_GROUP_OF_SHOPS_WHERE_QUANTITIES_ARE_NOT_SHARED_BETWEEN_EVERY_SHOP_IN_THIS_GROUP_MESSAGE'); ?></li>
            <li><?php echo JText::_('COM_JEPROSHOP_YOU_ARE_CURRENTLY_MANAGING_A_SHOP_THAT_IS_A_GROUP_WHERE_QUANTITIES_ARE_SHARED_BETWEEN_EVERY_SHOP_IN_THIS_GROUP_MESSAGE'); ?></li>
        </ul>
    </div>
    <?php } 
 } ?>
        </div>
 	<div class="panel">
 		<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_AVAILABILITY_SETTINGS_TITLE_MESSAGE'); ?></div>
 		<div class="panel-content well" >
 		<?php if(!$this->has_attribute){ ?>
        <div class="control-group">
            <div class="control-label" >
            	<span class="pull-right"><?php echo $this->productMultiShopCheckbox('minimal_quantity', 'default'); ?></span>
             	<label for="jform_minimal_quantity" ><?php echo JText::_('COM_JEPROSHOP_MINIMAL_QUANTITY_LABEL'); ?></label>
            </div>
            <div class="controls">
                <input class="fixed-width-sm" maxlength="6" name="quantities[minimal_quantity]" id="jform_minimal_quantity" type="text" value="<?php if(isset($this->product->minimal_quantity)){ echo $this->product->minimal_quantity; }else{ echo '1'; } ?>" class="quantity_box" />
                <p class="help-block"><?php echo JText::_('COM_JEPROSHOP_MINIMUM_QUANTITY_TO_BUY_THIS_PRODUCT_SET_TO_1_TO_DISABLE_THIS_FEATURE_MESSAGE') ; ?></p>
            </div>
        </div>
        <?php  }
        if($this->stock_management){ ?>
        <div class="control-group">
			<div class="control-label"><span class="pull-right"><?php echo $this->productMultiShopCheckbox('available_now', 'default'); ?></span>
				<label  for="available_now_<?php echo $this->context->controller->default_form_language; ?>">
					<span class="label-tooltip" data-toggle="tooltip" title="<?php echo JText::_('COM_JEPROSHOP_FORBIDDEN_CHARACTERS_LABEL') . ' &#60;&#62;;&#61;#&#123;&#125;'; ?>">
						<?php echo JText::_('COM_JEPROSHOP_DISPLAYED_TEXT_WHEN_IN_STOCK_LABEL'); ?>
					</span>
				</label>
			</div>
			<div class="controls">
				<?php echo $this->helper->multiLanguageInputField('available_now', 'quantities', true, $this->product->available_now); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label"><span class="pull-right"><?php echo $this->productMultiShopCheckbox('available_later', 'default'); ?></span>
				<label class=" col-lg-2" for="available_later_<?php echo $this->context->controller->default_form_language; ?>" >
					<span class="label-tooltip" data-toggle="tooltip"
						title="<?php echo JText::_('COM_JEPROSHOP_IF_EMPTY_THE_MESSAGE_IN_STOCK_WILL_BE_DISPLAYED') . ' ' . JText::_('COM_JEPROSHOP_FORBIDDEN_CHARACTERS_LABEL') . ' &#60;&#62;;&#61;#&#123;&#125' ; ?>" >
						<?php echo JText::_('COM_JEPROSHOP_DISPLAYED_TEXT_WHEN_BACK_ORDERING_IS_ALLOWED_MESSAGE'); ?>
					</span>
				</label>
			</div>
			<div class="controls">
				<?php echo $this->helper->multiLanguageInputField('available_later', 'quantities', true, $this->product->available_later); ?>
			</div>
		</div>			
		<?php if(!$this->count_attributes){ ?>
		<div class="control-group">
			<div class="control-label">
				<span class="pull-right"><?php echo $this->productMultiShopCheckbox('available_date', 'default'); ?></span>
				<label for="jform_available_date" >
					<?php echo JText::_('COM_JEPROSHOP_AVAILABLE_DATE_LABEL'); ?>
				</label>
			</div>
			<div class="controls">
				<div class="input-group fixed-width-md">
					<input id="jform_available_date" name="quantities[available_date]" value="<?php echo $this->product->available_date; ?>" class="datepicker" type="text" />
					<div class="input-group-addon">	<i class="icon-calendar-empty"></i></div>
				</div>
				<p class="help-block"><?php echo JText::_('COM_JEPROSHOP_THE_NEXT_DATE_OF_AVAILABILITY_FOR_THIS_PRODUCT_WHEN_IT_IS_OUT_OF_STOCK_MESSAGE'); ?></p>
			</div>
		</div>			
         <?php } 
         }?>
         </div>
 	</div>
</div>