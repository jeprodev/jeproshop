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
$icon_directory = JURI::base() . 'components/com_jeproshop/assets/themes/' . $css_dir . '/images/';
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" method="post" name="adminForm" id="adminForm" >
    <?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
            	<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart&task=rules'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CART_RULES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart&task=catalog_prices'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-catalog" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CATALOG_PRICE_RULES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart&task=marketing'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-marketing" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_MARKETING_LABEL')); ?></a>
            </fieldset>
        </div>
        <div class="separation"></div>
        <table class="table table-striped" id="customerList">
            <thead>
                <tr>
                    <th class="nowrap center" width="1%">#</th>
                    <th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_STATUS_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_PRIORITY_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_CODE_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_QUANTITY_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_EXPIRATION_DATE_LABEL'); ?></th>
                    <th class="nowrap " width="7%"><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($this->customers)){ ?>
                <tr><td colspan="13" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                <?php }else{
                    foreach ($this->customers as $index => $customer){ ?>
				<?php }
                } ?>
            </tbody>
            <tfoot><tr><td colspan="13" ><?php //echo($this->pagination->getListFooter()); ?></td></tr></tfoot>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
					