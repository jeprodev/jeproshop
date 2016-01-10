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
<div >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <?php echo $this->renderSubMenu('group'); ?>
        <div class="separation" ></div>
        <div class="panel form-horizontal">
            <div class="half_wrapper_left">
                <div class="panel">
                    <div class="panel-title" >
                        <i class="icon-group"></i> <?php echo JText::_('COM_JEPROSHOP_GROUP_INFORMATION_LABEL'); ?>
                        <span class="title-description"><i class="icon-group"></i> <?php echo ucfirst($this->group->name[$this->context->language->lang_id]); ?></span>
                    </div>
                    <div class="panel-content well">
                        <div class="control-group">
                            <div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_LABEL'); ?> : </label></div>
                            <div class="controls"><p class="form-control-static"><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_LABEL') . ' : ' . $this->group->reduction; ?></p></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label" ><label><?php echo JText::_('COM_JEPROSHOP_PRICE_DISPLAY_METHOD_LABEL'); ?></label></div>
                            <div class="controls">
                                <p class="form-control-static">
                                    <?php if($this->group->price_display_method) {
                                        echo JText::_('COM_JEPROSHOP_TAX_EXCLUDED_LABEL');
                                    }else{
                                        echo JText::_('COm_JEPROSHOP_TAX_INCLUDED_LABEL');
                                    } ?>
                                </p>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label" ><label ><?php echo JText::_('COM_JEPROSHOP_SHOW_PRICES_LABEL'); ?></label></div>
                            <div class="controls"><p class="form-control-static"><?php if($this->group->show_prices){ echo JText::_('COM_JEPROSHOP_YES_LABEL'); }else{ JText::_('COM_JEPROSHOP_NO_LABEL'); } ?></p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="half_wrapper_right" >
                <div class="panel">
                    <div class="panel-title" ><i class="icon-dollar"></i> <?php echo JText::_('COM_JEPROSHOP_CURRENT_CATEGORY_DISCOUNT_LABEL'); ?> </div>
                    <div class="panel-content well" >
                        <?php if(!$this->category_reductions){ ?>
                        <div class="alert alert-warning"><?php echo JText::_('COM_JEPROSHOP_NONE_LABEL'); ?></div>
                        <?php }else{ ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th><?php echo JText::_('COM_JEPROSHOP_CATEGORY_LABEL'); ?></th>
                                <th><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_LABEL'); ?></span></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach($this->categorie_reductions as $key => $category ){ ?>
                                <tr class="row_<?php echo $key % 2; ?>">
                                    <td><?php echo $category->path; ?></td>
                                    <td><?php echo JText::_('COM_JEPROSHOP_DISCOUNT_OF_LABEL') . ' ' .  $category->reduction; ?></td>
                                </tr>
                                <?php } ?>
                            <tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;" ></div>
        <div class="panel">
            <div class="panel-title" >
                <?php echo JText::_('COM_JEPROSHOP_MEMBERS_OF_THIS_CUSTOMER_GROUP_LABEL'); ?>
                <span ><?php echo JText::_('COM_JEPROSHOP_LIMIT_TO_THE_FIRST_ONE_HUNDREDS_CUSTOMERS_LABEL') . ' ' . JText::_('COM_JEPROSHOP_PLEASE_USE_FILTER_TO_ARROW_YOUR_SEARCH_LABEL'); ?></span>
            </div>
            <div class="panel-content well" >
                <table class="table" >
                    <thead>
                        <tr>
                            <th class="nowrap center" width="1%" >#</th>
                            <th class="nowrap center" width="1%" ><?php echo JHtml::_('grid.checkall'); ?></th>
                            <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_FIRSTNAME_LABEL'); ?></th>
                            <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_LASTNAME_LABEL'); ?></th>
                            <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_EMAIL_ADDRESS_LABEL'); ?></th>
                            <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_BIRTHDAY_LABEL'); ?></th>
                            <th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_DATE_ADDED_LABEL'); ?></th>
                            <th class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></th>
                            <th class="nowrap" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($this->customers) ){
                            foreach($this->customers as $index => $customer){ ?>
                        <tr class="row_<?php echo $index % 2; ?>" >
                            <td class="nowrap center"><?php echo $index + 1; ?></td>
                            <td class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $customer->customer_id); ?></td>
                            <td class="nowrap" ><?php echo ucfirst($customer->firstname); ?></td>
                            <td class="nowrap" ><?php echo ucfirst($customer->lastname); ?></td>
                            <td class="nowrap" ><?php echo $customer->email; ?></td>
                            <td class="nowrap" ><?php echo $customer->birthday; ?></td>
                            <td class="nowrap" ><?php echo $customer->date_add; ?></td>
                            <td class="nowrap center" ><i class="icon-<?php echo (isset($customer->published) ? '' : 'un') . 'publish'; ?>" ></i></td>
                            <td class="nowrap" ><span class="pull-right" ><a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=edit&customer_id=' . (int)$customer->customer_id . '&' . JeproshopTools::getCustomerToken() . '=1'); ?>" class="btn btn-group-action btn-micro" ><i class="icon-edit" ></i> <?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a></span></td>
                        </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>