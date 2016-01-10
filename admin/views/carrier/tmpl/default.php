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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=carrier'); ?>" method="post" name="adminForm" id="adminForm" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
<?php } ?>
<div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
    <!--div class="box_wrapper jeproshop_sub_menu_wrapper">
        <fieldset class="btn-group">
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-address" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-group" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-cart" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-thread" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=contact'); ?>" class="btn jeproshop_sub_menu " ><i class="icon-contact" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')); ?></a>
        </fieldset>
    </div-->
    <div class="panel" >
        <div class="panel-content" >
            <table class="table table-striped" id="addressList">
                <thead>
                <tr>
                    <th class="nowrap center" width="1%">#</th>
                    <th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th class="nowrap" width="10%"><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></th>
                    <th class="nowrap" width="10%"><?php echo JText::_('COM_JEPROSHOP_LOGO_LABEL'); ?></th>
                    <th class="nowrap hidden-phone" width="6%"><?php echo JText::_('COM_JEPROSHOP_DELAY_LABEL'); ?></th>
                    <th class="nowrap center" width="4%"><?php echo JText::_('COM_JEPROSHOP_IS_FREE_LABEL'); ?></th>
                    <th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></th>
                    <th class="nowrap center hidden-phone" width="8%"><?php echo JText::_('COM_JEPROSHOP_POSITION_LABEL'); ?></th>
                    <th class="nowrap hidden-phone" width="5%"><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($this->carriers)){ ?>
                    <tr>
                        <td colspan="9" ><div class="alert alert-no-items" ><?php echo JText::_('COM_JEPROSHOP_NO_MACTHING_RESULTS'); ?></div></td>
                    </tr>
                <?php } else {
                    foreach($this->carriers as $index => $carrier){
                        $carrier_view_link = JRoute::_('index.php?option=com_jeproshop&view=carrier&task=edit&carrier_id=' .(int)$carrier->carrier_id);
                        $delete_carrier_link = JRoute::_('index.php?option=com_jeproshop&view=carrier&task=delete&carrier_id=' .(int)$carrier->carrier_id);
                        ?>
                        <tr class="row_<?php echo ($index%2); ?>" >
                            <td class="nowrap center"><?php echo $index + 1; ?></td>
                            <td class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $carrier->carrier_id); ?></td>
                            <td class="nowrap" ><a href="<?php echo $carrier_view_link; ?>" ><?php echo ucfirst($carrier->name); ?></a></td>
                            <td class="nowrap" ><?php //echo ucfirst($address->lastname); ?></td>
                            <td class="nowrap" ><?php echo $carrier->delay; ?></td>
                            <td class="nowrap center" ><i class="icon-<?php echo $carrier->is_free ? '' : 'un'; ?>publish" ></i> </td>
                            <td class="nowrap center" ><i class="icon-<?php echo $carrier->published ? '' : 'un'; ?>publish" ></i> </td>
                            <td class="nowrap center" ><?php echo $carrier->position; ?></td>
                            <td class="nowrap" >
                                <div class="btn-group-action" >
                                    <div class="btn-group pull-right" >
                                        <a href="<?php echo $carrier_view_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                        <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                        <ul class="dropdown-menu" >
                                            <li><a href="<?php echo $delete_carrier_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_DELETE_LABEL') . $carrier->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); }" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
</div>
</form>