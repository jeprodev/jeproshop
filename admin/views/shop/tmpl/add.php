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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=shop'); ?>" method="post" id="adminForm" name="adminForm"  class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
		<div class="control-group" >
            <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_SHOP_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_NAME_LABEL'); ?></label></div>
            <div class="controls" >
                <input type="text" name="jform[name]" id="jform_name" value="" required="required" />
                <p class="field_description" >
                    <?php echo JText::_('COM_JEPROSHOP_SHOP_NAME_DESCRIPTION_PART_1'); ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=store&' . JSession::getFormToken() . '=1'); ?>" ><?php echo JText::_('COM_JEPROSHOP_THIS_LINK_LABEL'); ?></a>
                   	<?php echo JText::_('COM_JEPROSHOP_SHOP_NAME_DESCRIPTION_PART_2'); ?>
                </p>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SHOP_GROUP_ID_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_GROUP_ID_LABEL'); ?></label></div>
            <div class="controls" >
                 <select name="jform[shop_group_id]" id="jform_shop_group_id" >
                    <?php foreach($this->shop_groups as $shop_group){ ?>
                    <option value="<?php echo $shop_group->shop_group_id; ?>" ><?php echo $shop_group->name; ?></option>
                    <?php } ?>
                </select>
                <p class="field_description" ><?php echo JText::_('COM_JEPROSHOP_SHOP_GROUP_ID_DESCRIPTION'); ?></p>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SHOP_ROOT_CATEGORY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_ROOT_CATEGORY_LABEL'); ?></label></div>
            <div class="controls" >
                <select name="jform[category_id]" id="jform_category_id" >
                    <?php foreach($this->categories as $category){ ?>
                    <option value="<?php echo $category->category_id; ?>" ><?php echo $category->name; ?></option>
                    <?php } ?>
                </select>
                <p class="preference_description"><?php JText::_('COM_JEPROSHOP_SHOP_ROOT_CATEGORY_DESCRIPTION'); ?></p>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SHOP_ASSOCIATED_CATEGORIES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_ASSOCIATED_CATEGORIES_LABEL'); ?></label></div>
            <div class="controls" >
                <div class="panel">
                    <div class="tree_panel_head_controls clearfix">
                         <div class="tree_actions pull_right">
                            <input type="text" id="jform_categories_tree_categories_search" placeholder="search..." class="search_field" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group" >
            <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_SHOP_THEME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_SHOP_THEME_LABEL'); ?></label></div>
            <div class="controls" >
                <?php foreach ($this->themes as $theme){ ?>
                <div class="col-lg-9 " onclick="$(this).find('input').attr('checked', true); $('.select_theme').removeClass('select_theme_choice'); $(this).toggleClass('select_theme_choice');" >
                    <div class="theme_radio" >
                        <label><input type="radio" name="jform[theme_id]" value="<?php echo $theme->theme_id; ?>" /><?php echo $theme->name; ?></label>
                    </div>
                    <div class="theme_container" ><img src="<?php echo JURI::base() . 'components/com_jeproshop/assets/images/themes/' . $theme->name . '.jpg'; ?>" alt="<?php echo $theme->name; ?>" /></div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"></div>
            <div class="controls">
                <table>
                    <tbody>
                        <tr>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[attribute_group]]" /><?php echo JText::_('COM_JEPROSHOP_ATTRIBUTE_GROUPS_LABEL'); ?></p></td>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[stock_available]]" /><?php echo JText::_('COM_JEPROSHOP_STOCK_AVAILABLE_LABEL'); ?></p></td>
                        </tr>                                
                        <tr>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[carrier]]" /><?php echo JText::_('COM_JEPROSHOP_CARRIER_LABEL'); ?></p></td>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[cart_rule]]" /><?php echo JText::_('COM_JEPROSHOP_CART_RULES_LABEL'); ?></p></td>
                        </tr>                                
                        <tr>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[product_attribute]]" /><?php echo JText::_('COM_JEPROSHOP_COMBINATIONS_LABEL'); ?></p></td>
                            <td><p class="checkbox"><input type="checkbox" name="jform[importData[contact]]" /><?php echo JText::_('COM_JEPROSHOP_CONTACT_INFORMATIONS_LABEL'); ?></p></td>
                        </tr>                                
                        <tr>
                           <td><p class="checkbox"><input type="checkbox" name="jform[importData[country]]" /><?php echo JText::_('COM_JEPROSHOP_OUNTRIES_LABEL'); ?></p></td>
                           <td><p class="checkbox"><input type="checkbox" name="jform[importData[currency]]" /><?php echo JText::_('COM_JEPROSHOP_CURRENCIES_LABEL'); ?></p></td>
                        </tr> 
                    </tbody>
                 </table>
            </div>
        </div>
        
	</div>
</form>