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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=currency'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('currency'); ?></div>
		<div class="panel" >
            <div class="panel-title" ><i class="icon-currency" ></i> <?php echo JText::_('COM_JEPROSHOP_YOU_ARE_ABOUT_TO_EDIT_LABEL') . ' '; ?></div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_ONLY_LETTERS_AND_THE_MINUS_CHARACTER_ARE_ALLOWED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_name" name="jform[name]" size="30" maxlength="32" required="required" value="<?php echo $this->currency->name; ?>"/> </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_iso_code" title="<?php echo JText::_('COM_JEPROSHOP_ISO_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ISO_CODE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_iso_code" name="jform[iso_code]" maxlength="32" required="required" value="<?php echo $this->currency->iso_code; ?>"/></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_iso_code_num" title="<?php echo JText::_('COM_JEPROSHOP_NUMERIC_ISO_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NUMERIC_ISO_CODE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_iso_code_num" name="jform[iso_code_num]" maxlength="32" value="<?php echo $this->currency->iso_code_num; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_symbol" title="<?php echo JText::_('COM_JEPROSHOP_SYMBOL_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SYMBOL_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_symbol" name="jform[symbol]" maxlength="8" required="required" value="<?php echo $this->currency->sign; ?>"/></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_conversion_rate" title="<?php echo JText::_('COM_JEPROSHOP_CONVERSION_RATE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CONVERSION_RATE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_conversion_rate" maxlength="11" name="jform[conversion_rate]" value="<?php echo $this->currency->conversion_rate; ?>" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_currency_format" title="<?php echo JText::_('COM_JEPROSHOP_CURRENCY_FORMAT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CURRENCY_FORMAT_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <select id="jform_currency_format" name="jform[currency_format]" required="required">
                            <option value="1" <?php if($this->currency->format == 1){ ?> selected="selected" <?php } ?> >X 0,000.00 <?php echo JText::_('COM_JEPROSHOP_WITH_DOLLAR_LABEL'); ?></option>
                            <option value="2" <?php if($this->currency->format == 2){ ?> selected="selected" <?php } ?> >0,000.00 x <?php echo JText::_('COM_JEPROSHOP_WITH_EURO_LABEL'); ?></option>
                            <option value="3" <?php if($this->currency->format == 3){ ?> selected="selected" <?php } ?> >X 0,000.00 </option>
                            <option value="4" <?php if($this->currency->format == 4){ ?> selected="selected" <?php } ?> >0,000.00 x</option>
                            <option value="5" <?php if($this->currency->format == 5){ ?> selected="selected" <?php } ?> >X 0'000.00 </option>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_decimals" title="<?php echo JText::_('COM_JEPROSHOP_DECIMALS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DECIMALS_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $helper->radioButton('decimals', 'edit', $this->currency->decimals); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_spacing" title="<?php echo JText::_('COM_JEPROSHOP_SPACING_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SPACING_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $helper->radioButton('blank', 'edit', $this->currency->blank); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROSHOP_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $helper->radioButton('published', 'edit', $this->currency->published); ?></div>
                </div>
                <?php if(JeproshopShopModelShop::isFeaturePublished()){ ?>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_associated_shop" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ASSOCIATED_SHOP_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->associated_shop; ?></div>
                </div>
                <?php } ?>
            </div>
        </div>
	</div>
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>