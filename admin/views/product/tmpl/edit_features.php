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

if(isset($this->product->product_id)){
?>
<div id="product_features" class="panel product-tab">
    <input type="hidden" name="submitted_tabs[]" value="Features" />
    <div class="panel-title" ><?php echo JText::_('COM_JEPROSHOP_ASSIGN_FEATURES_TO_THIS_PRODUCT_TITLE'); ?></div>
    <div class="panel-content well" >
	    <div class="alert alert-info">
		<?php	
	        echo JText::_('COM_JEPROSHOP_YOU_CAN_SPECIFY_A_VALUE_FOR_EACH_RELEVANT_FEATURE_REGARDING_THIS_PRODUCT_EMPTY_FIELDS_WILL_NOT_BE_DISPLAYED_MESSAGE') . '<br />';
	        echo JText::_('COM_JEPROSHOP_YOU_CAN_EITHER_CREATE_A_SPECIFIC_VALUE_OR_SELECT_AMONG_THE_EXISTING_PRE_DEFINED_VALUES_YOU_HAVE_PREVIOUSLY_ADDED_MESSAGE');        
	        ?>
	    </div>
	    <table class="table">
			<thead>
				<tr>
					<th class="nowrap" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_FEATURE_LABEL'); ?></span></th>
					<th class="nowrap" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_PRE_DEFINED_VALUE_LABEL'); ?></span></th>
					<th class="nowrap" ><span class="title_box"><?php echo JText::_('COM_JEPROSHOP_OR_LABEL') . " " . JText::_('COM_JEPROSHOP_CUSTOMIZED_VALUE_LABEL'); ?></span></th>
	            </tr>
			</thead>
			<tbody>
				<?php if(isset($this->available_features)  && count($this->available_features)){
	                foreach($this->available_features as $available_feature){ 
	                    $addFeatureValueLink = JRoute::_('index.php?option=com_jeproshop&view=feature&task=add_val&feature_id=' . $available_feature->feature_id . '&'. JeproshopTools::getFeatureToken() . '=1');
	            ?>
	            <tr>
	            	<td><?php echo $available_feature->name; ?></td>
	            	<td>
						<?php if(sizeof($available_feature->featureValues)){ ?>
						<select id="jform_feature_<?php echo $available_feature->feature_id; ?>_value" name="feature[feature_<?php echo $available_feature->feature_id; ?>_value]" onchange="$('.custom_<?php echo $available_feature->feature_id; ?>_').val('');">
							<option value="0">---</option>
							<?php foreach($available_feature->featureValues as $value){ ?>
							<option value="<?php echo $value->feature_value_id; ?>" <?php if($available_feature->current_item == $value->feature_value_id){ ?>selected="selected" <?php } ?> >
							<?php echo substr($value->value, 0, 40); ?>
							</option>
							<?php } ?>
						</select>
						<?php }else{?>
						<input type="hidden" name="feature[feature_<?php echo $available_feature->feature_id; ?>_value]" value="0" />
						<span><?php echo JText::_('COM_JEPROSHOP_NOT_ASSIGNED_LABEL'); ?> - 
							<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature&task=add_value&feature_id=' . $available_feature->feature_id . '&' . JeproshopTools::getFeatureToken() . '=1'); ?>" class="confirm_leave btn btn-link">
                                <i class="icon-plus-sign"></i> <?php echo JText::_('COM_JEPROSHOP_ADD_PREDEFINED_VALUES_FIRST_LABEL'); ?> <i class="icon-external-link-sign"></i>
                            </a>
						</span>
						<?php } ?>
					</td>
					<td>				
						<div class="row lang-0" style='display: none;'><?php  ?>
							<div class="col-lg-9">
								<textarea class="custom_<?php echo $available_feature->feature_id; ?>_all textarea-autosize"	name="feature[custom_<?php echo $available_feature->feature_id; ?>_all"
										cols="40" style='background-color:#CCF'	rows="1" onkeyup="<?php foreach($this->languages as $key => $language){ ?>$('.custom_<?php echo $available_feature->feature_id . '_' . $language->lang_id; ?>').val($(this).val());<?php } ?>" ><?php if(isset($available_feature->featureValues[1]->value)){ echo $available_feature->featureValues[1]->value; } ?>
								</textarea>										
							</div>
							<?php if(count($this->languages) > 1){ ?>
							<div class="col-lg-3">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<?php echo JText::_('COM_JEPROSHOP_ALL_LABEL'); ?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<?php foreach($this->languages as $language){ ?>
									<li><a href="javascript:void(0);" onclick="restore_lng($(this),<?php echo $language->lang_id; ?>);"><?php echo $language->iso_code; ?></a></li>
									<?php } ?>
								</ul>
							</div>
							<?php } ?>
						</div>
			
						<?php foreach($this->languages as $key => $language){
							if(count($this->languages) > 1){ ?>
						<div class="row translatable-field lang_<?php echo $language->lang_id; ?>">
							<div class="col-lg-9">
							<?php } ?>
								<textarea class="custom_<?php echo $available_feature->feature_id . '_' . $language->lang_id; ?> textarea-autosize" name="feature[custom_<?php echo $available_feature->feature_id . '_' . $language->lang_id; ?>]" cols="40" rows="1"
								onkeyup="if(isArrowKey(event)) return ;$('#feature_<?php echo $available_feature->feature_id; ?>_value').val(0);" ><?php if(isset($available_feature->featureValues[$key]->value)){ echo $available_feature->featureValues[$key]->value; }; ?></textarea>
								
							<?php if(count($this->languages) > 1){ ?>
							</div>
							<div class="col-lg-3">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<?php echo $language->iso_code; ?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="javascript:void(0);" onclick="all_languages($(this));"><?php echo JText::_('COM_JEPROSHOP_ALL_LABEL'); ?> </a></li>								
									<?php foreach($this->languages as $language){ ?>
									<li><a href="javascript:hideOtherLanguage(<?php echo $language->lang_id; ?>);"><?php echo $language->iso_code; ?></a></li>
									<?php } ?>
								</ul>
							</div>
						</div>
						<?php } 
						}?>
					</td>
	            </tr>
	            <?php } 
	            }else{ ?>
	            <tr>
					<td colspan="3" style="text-align:center;"><i class="icon-warning-sign"></i> <?php echo JText::_('COM_JEPROSHOP_NO_FEATURES_HAVE_BEEN_DEFINED_MESSAGE'); ?></td>
	            </tr>
	            <?php } ?>
			</tbody>
		</table>
		<?php 
	    $addFeatureLink = JRoute::_('index.php?option=com_jeproshop&view=feature&task=add&'. JSession::getFormToken() . '=1');
	    $productsLink = JRoute::_('index.php?option=com_jeproshop&view=product&'. JSession::getFormToken() . '=1');
	    ?>
	    <a href="<?php echo $addFeatureLink; ?>" class="btn btn-link confirm_leave button">
			<i class="icon-plus-sign"></i> <?php echo JText::_('COM_JEPROSHOP_NEW_FEATURE_LABEL'); ?><i class="icon-external-link-sign"></i>
	    </a>
	    <div class="panel-footer">
			<a href="<?php echo $productsLink; ?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?></a>
			<button type="submit" name="save_feature" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_AND_STAY_LABEL'); ?></button>
	    </div>
    </div>
</div>
<?php
}
?>		
