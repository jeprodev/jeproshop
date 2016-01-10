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

if(isset($this->product->product_id)){ ?>
<div id="product-suppliers" class="panel product-tab">
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_OF_THE_CURRENT_PRODUCT_LABEL'); ?></div>
	<div class="panel-content well" >
		<div class="alert alert-info">
			<?php 
			echo JText::_('COM_JEPROSHOP_THIS_INTERFACE_ALLOWS_YOU_TO_SPECIFY_THE_SUPPLIERS_OF_THE_CURRENT_PRODUCT_AND_EVENTUALLY_ITS_COMBINATIONS_MESSAGE') . '<br /> ' .
			JText::_('COM_JEPROSHOP_IT_IS_ALSO_POSSIBLE_TO_SPECIFY__SUPPLIER_REFERENCES_ACCORDING_TO_PREVIOUSLY_ASSOCIATED_SUPPLIERS_MESSAGE') . '<br /> ' . '<br />' . 
			JText::_('COM_JEPROSHOP_WHEN_USING_ADVANCED_STOCK_MANAGEMENT_TOOL_SEE_PRODUCT_PREFERENCES_THE_VALUES_YOU_DEFINE_PRICE_REFERENCES_WILL_BE_USED_IN_SUPPLY_ORDER_MESSAGE');
			?>
		</div>
		<label><?php echo JText::_('COM_JEPROSHOP_PLEASE_CHOSE_THE_SUPPLIERS_ASSOCIATED_WITH_THIS_PLEASE_SELECT_DEFAULT_SUPPLIER_AS_WELL_LABEL'); ?></label>
				
		<table class="table">
			<thead>
				<tr>
					<th class="nowrap center" width="4%" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_SELECTED_LABEL'); ?></span></th>
					<th class="nowrap" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_SUPPLIER_NAME_LABEL'); ?></span></th>
					<th class="nowrap" ><span class="pull-right"><?php echo JText::_('COM_JEPROSHOP_DEFAULT_LABEL'); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->suppliers) > 0){
					foreach($this->suppliers as $supplier){ ?>
				<tr>
					<td class="nowrap center" ><input type="checkbox" class="supplierCheckBox" name="supplier[check_supplier_<?php echo $supplier->supplier_id; ?>]" <?php if($supplier->is_selected == true){ ?> checked="checked" <?php } ?> value="<?php echo $supplier->supplier_id; ?>" /></td>
					<td class="nowrap" ><?php echo $supplier->name; ?></td>
					<td class="nowrap center" ><span class="pull-right" ><input type="radio" id="jform_default_supplier_<?php echo $supplier->supplier_id; ?>" name="supplier[default_supplier]" value="<?php echo $supplier->supplier_id; ?>" <?php if($supplier->is_selected == false){ ?> disabled="disabled" <?php } if($supplier->is_default == true){ ?> checked="checked" <?php } ?> /></span></td>
				</tr>
				<?php }
				} ?>
			</tbody>
		</table>
		<a class="btn btn-default btn-link bt-icon confirm_leave" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier&t&task=add&' . JeproshopTools::getSupplierToken() . '=1'); ?>">
			<i class="icon-plus"></i> <?php echo JText::_('COM_JEPROSHOP_CREATE_NEW_SUPPLIER_LABEL'); ?> <i class="icon-external-link-sign"></i>
		</a>
		<div class="panel-footer">
			<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
			<button type="submit" name="save_supplier" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_AND_STAY_LABEL'); ?></button>
		</div>	
	</div>	
</div>
<div class="panel" >
	<div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_REFERENCES_LABEL'); ?></div>
	<div class="panel-content well" >
		<div class="alert alert-info">
			<?php if(count($this->associated_suppliers) == 0){
				echo JText::_('COM_JEPROSHOP_YOU_MUST_SPECIFY_THE_SUPPLIERS_ASSOCIATED_WITH_THIS_PRODUCT_YOU_MUST_ALSO_BEFORE_SETTING_REFERENCES_LABEL'); 
			}else{
				echo JText::_('COM_JEPROSHOP_YOU_CAN_SPECIFY_PRODUCT_REFERENCES_FOR-EACH_ASSOCIATED_SUPPLIER_LABEL'); 
			}
			echo JText::_('COM_JEPROSHOP_CLICK_SAVE_AND_STAY_AFTER_CHANGING_ASSOCIATED_SELECTED_SUPPLIER_TO_DISPLAY_THE_ASSOCIATED_PRODUCT_REFERENCES_LABEL');  ?>
		</div>
		<div id="accordion-supplier" >
			<?php foreach($this->associated_suppliers as $supplier){ ?>
			<div class="panel" >
				<div class="panel-title"><i class="icon-supplier" ></i> <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-supplier" href="#jform_supplier_<?php echo $supplier->supplier_id; ?>"> <?php if(isset($supplier->name)){ echo $supplier->name; } ?> </a></div>
				<div id="jform_supplier_<?php echo $supplier->supplier_id; ?>" class="panel-content well" >
					<table class="table table-stripped">
						<thead>
							<tr>
								<th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_PRODUCT_NAME_LABEL'); ?></th>
								<th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_SUPPLIER_REFERENCE_LABEL'); ?></th>
								<th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_UNIT_PRICE_TAX_EXCLUDED_LABEL'); ?></th>
								<th class="nowrap" ><span class="pull-right"><?php echo JText::_('COM_JEPROSHOP_UNIT_PRICE_CURRENCY_LABEL'); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($this->attributes AS $index => $attribute){ 
								$reference = '';
								$price_te = '';
								$currency_id = $this->default_currency_id;
								foreach($this->associated_suppliers_collection as $asc){
									if(($asc->product_id == $attribute->product_id) && ($asc->product_attribute_id == $attribute->product_attribute_id) && ($asc->supplier_id == $supplier->supplier_id)){
										$reference = $asc->product_supplier_reference; 
										$price_te = JeproshopTools::roundPrice($asc->product_supplier_price_te, 2);
										if($asc->currency_id){
											$currency_id = $asc->currency_id;
										}
									}
								}?>
							<tr class="row_<?php echo $index % 2; ?>" >
								<td><?php echo $this->product_designation[$attribute->product_attribute_id]; ?></td>
								<td>
									<input type="text" value="<?php echo $reference; ?>" name="supplier[supplier_reference_<?php echo $attribute->product_id . '_' . $attribute->product_attribute_id . '_' . $supplier->supplier_id; ?>]" />
								</td>
								<td>
									<input type="text" value="<?php echo htmlentities($price_te); ?>" name="supplier[product_price_<?php echo $attribute->product_id . '_' . $attribute->product_attribute_id . '_' . $supplier->supplier_id; ?>]" class="price_box" />
								</td>
								<td>
									<select name="supplier[product_price_currency_<?php echo $attribute->product_id . '_' . $attribute->product_attribute_id . '_' . $supplier->supplier_id; ?>]" class="price_box" >
										<?php foreach($this->currencies AS $currency){ ?>
										<option value="<?php echo $currency->currency_id; ?>" <?php if($currency->currency_id == $currency_id){ ?> selected="selected" <?php } ?> ><?php echo $currency->name; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div style="clear: both;" ></div>
				</div>
			</div>
			<?php } ?>
		</div>
		<div style="clear: both;" ></div>
		<div class="panel-footer">
			<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
			<button type="submit" name="save_reference" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_AND_STAY_LABEL'); ?></button>
		</div>
		<div style="clear: both;" ></div>
	</div>
</div>
<?php } ?>