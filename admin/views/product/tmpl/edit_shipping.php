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
 <div id="product-shipping" class="panel product-tab">	
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_SHIPPING_LABEL'); ?></div>
	<div class="panel-content well" >
		<?php if(isset($this->display_common_field) && $this->display_common_field){ ?>
		<div class="alert alert-info"><?php echo JText::_('COM_JEPROSHOP_MESSAGE'); ?>{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
		<?php }?>
		<div class="control-group" >
			<div class="control-label" ><label for="jform_width"><?php echo $this->bullet_common_field . ' ' . JText::_('COM_JEPROSHOP_PACKAGE_WIDTH_LABEL'); ?></label></div>
			<div class="controls"><div class="input-append">
				<input maxlength="14" id="jform_width" name="shipping[width]" type="text" value="<?php echo $this->product->width; ?>" class="unit_box small_box" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');"  />
				<button class="btn"><?php echo $this->dimension_unit; ?></button></div>
			</div>
		</div>
	
		<div class="control-group" >
			<div class="control-label" ><label for="jform_height"><?php echo $this->bullet_common_field . ' ' . JText::_('COM_JEPROSHOP_PACKAGE_HEIGHT_LABEL'); ?> </label></div>
			<div class="controls">
                <div class="input-append">
				<input maxlength="14" id="jform_height" name="shipping[height]" type="text" class="unit_box small_box" value="<?php echo $this->product->height; ?>" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');"   />
				<button class="btn"><?php echo $this->dimension_unit; ?></button>
                </div>
			</div>
		</div>
	
		<div class="control-group" >
			<div class="control-label " ><label for="jform_depth" ><?php echo $this->bullet_common_field. ' ' . JText::_('COM_JEPROSHOP_PACKAGE_DEPTH_LABEL'); ?> </label></div>
			<div class="controls"><div class="input-append">
				<input maxlength="14" id="jform_depth" name="shipping[depth]" type="text" class="unit_box small_box" value="<?php echo $this->product->depth; ?>" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');"   />
				<button class="btn"><?php echo $this->dimension_unit; ?></button></div>
			</div>
		</div>
	
		<div class="control-group" >
			<div class="control-label" ><label for="jform_weight"><?php echo $this->bullet_common_field . ' ' . JText::_('COM_JEPROSHOP_PACKAGE_WEIGHT_LABEL'); ?> </label></div>
			<div class="controls"><div class="input-append" >
				<input maxlength="14" id="jform_weight" name="shipping[weight]" type="text" class="unit_box small_box" value="<?php echo $this->product->weight; ?>" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');"   />
				<button class="input-group-addon"><?php echo $this->weight_unit; ?></button></div>
			</div>
		</div>

		<div class="control-group" >
			<div class="control-label" >
				<label for="jform_additional_shipping_cost">
					<span class="label-tooltip" data-toggle="tooltip" title="<?php JText::_('COM_JEPROSHOP_IF_A_CARRIER_HAS_A_TAX_IT_WILL_BE_ADDED_TO_THE_SHIPPING_FEES_TITLE_DESC'); ?>" >
						<?php echo JText::_('COM_JEPROSHOP_ADDITIONAL_SHIPPING_FEES_LABEL'); ?>
					</span>			
				</label>
			</div>
			<div class="controls">
                <div class="input-append" >
                    <?php if($this->currency->prefix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->prefix; ?></button><?php } ?>
                    <input type="text" id="jform_additional_shipping_cost" name="shipping[additional_shipping_cost]" onchange="this.value = this.value.replace(/,/g, '.');" value="<?php echo $this->product->additional_shipping_cost; ?>" class="price_box" />
                    <?php if($this->currency->suffix != ""){ ?><button type="button" class="btn" id="jform_img" ><?php echo $this->currency->suffix; ?></button><?php } ?>
                </div>
				<?php if($this->context->country->country_display_tax_label){ echo '(' . JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL') . ')'; } ?>
			</div>
		</div>

		<div class="control-group" >
			<div class="control-label" ><label for="jform_available_carriers"><?php echo JText::_('COM_JEPROSHOP_SHIPPING_CARRIERS_LABEL'); ?></label></div>
			<div class="controls">
				<div class="one_third" >
					<p><?php echo JText::_('COM_JEPROSHOP_AVAILABLE_CARRIERS_LABEL'); ?></p>
					<select id="jform_available_carriers" name="availableCarriers" multiple="multiple" >
						<?php foreach($this->carrier_list as $carrier){
							if(!isset($carrier->selected) || !$carrier->selected){ ?>
						<option value="<?php echo $carrier->reference_id; ?>"><?php echo $carrier->name; ?></option>
						<?php }
						}?>	
					</select>
				</div>
				<div class="one_third" >
					<a href="#" id="addCarrier" class="btn btn-default btn-block"><?php echo JText::_('COM_JEPROSHOP_ADD_LABEL'); ?> <i class="icon-arrow-right"></i></a>
					<a href="#" id="removeCarrier" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> <?php echo JText::_('COM_JEPROSHOP_REMOVE_LABEL'); ?></a>
				</div>
				<div class="one_third" >
					<p><?php echo JText::_('COM_JEPROSHOP_SELECTED_CARRIERS_LABEL'); ?></p>
					<select id="jform_selected_carriers" name="selected_carriers[]" multiple="multiple" >
						<?php foreach($this->carrier_list as $carrier){
							if(isset($carrier->selected) && $carrier->selected){ ?>
						<option value="<?php echo $carrier->reference_id; ?>" ><?php echo $carrier->name; ?></option>
						<?php } 
						} ?>
					</select>				
				</div>
			</div>
		</div>
		<div class="control-group" id="no-selected-carries-alert">
			<div class="controls">
				<div class="alert alert-warning"><?php echo JText::_('COM_JEPROSHOP_IF_NO_CARRIER_IS_SELECTED_THEN_ALL_THE_CARRIERS_WILL_BE_AVAILABLE_FOR_CUSTOMERS_ORDERS_MESSAGE'); ?></div>
			</div>
		</div>
		<div class="panel-footer control-group" >
            <div class="controls" >
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
                <button type="submit" name="save_shipping" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_AND_STAY_LABEL'); ?></button>
            </div>
		</div>
	</div>	
</div>