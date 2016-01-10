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
?>

<div class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-address" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-group" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-cart" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-thread" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=contact'); ?>" class="btn jeproshop_sub_menu " ><i class="icon-contact" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')); ?></a>                             
            </fieldset>
        </div>
        <div class="panel" >
    		<div class="panel-content" >
    			<table class="table table-striped" id="addressList">
            		<thead>
                		<tr>
                    		<th class="nowrap center" width="1%">#</th>
                    		<th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th> 
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_LABEL'); ?></th>
                    		<th class="nowrap " width="8%"><?php echo JText::_('COM_JEPROSHOP_EMAIL_ADDRESS_LABEL'); ?></th>
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_TYPE_LABEL'); ?></th>
                    		<th class="nowrap " width="8%"><?php echo JText::_('COM_JEPROSHOP_LANGUAGE_LABEL'); ?></th>
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_STATUS_LABEL'); ?></th>
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_EMPLOYEE_LABEL'); ?></th>
                    		<th class="nowrap " width="8%"><?php echo JText::_('COM_JEPROSHOP_MESSAGES_LABEL'); ?></th>
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_LAST_MESSAGE_LABEL'); ?></th>
                    		<th class="nowrap " width="8%"><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                    	</tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
 </div>