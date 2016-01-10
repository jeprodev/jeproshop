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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" method="post" name="adminForm" id="adminForm" >
    <?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
        <?php echo $this->renderSubMenu('customer'); ?>
        <div class="separation"></div>
        <div class="panel" >
    		<div class="panel-content" >
        		<table class="table table-striped" id="customerList">
            		<thead>
                		<tr>
                    		<th class="nowrap center" width="1%">#</th>
                    		<th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                    		<th class="nowrap " width="5%"><?php echo JText::_('COM_JEPROSHOP_TITLE_LABEL'); ?></th>
                    		<th class="nowrap " width="15%"><?php echo JText::_('COM_JEPROSHOP_LAST_NAME_LABEL'); ?></th>
                    		<th class="nowrap " width="15%"><?php echo JText::_('COM_JEPROSHOP_FIRST_NAME_LABEL'); ?></th>
                    		<th class="nowrap  hidden-phone" width="25%"><?php echo JText::_('COM_JEPROSHOP_EMAIL_ADDRESS_LABEL'); ?></th>
                    		<th class="nowrap " width="6%"><?php echo JText::_('COM_JEPROSHOP_SALES_LABEL'); ?></th>
                            <?php if(JeproshopSettingModelSetting::getValue('enable_b2b_mode')){ ?><th class="nowrap hidden-phone" width="5%" ><?php echo ucfirst(JText::_('COM_JEPROSHOP_COMPANY_LABEL')); ?></th><?php } ?>
                    		<th class="nowrap center hidden-phone" width="5%"><?php echo JText::_('COM_JEPROSHOP_ENABLED_LABEL'); ?></th>
                    		<th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_NEWSLETTER_LABEL'); ?></th>
                    		<th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_OPTION_LABEL'); ?></th>
                    		<th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_REGISTRATION_LABEL'); ?></th>
                    		<th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_LAST_VISIT_LABEL'); ?></th>
                    		<th class="nowrap center hidden-phone" width="4%"><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                		</tr>
           	 		</thead>
            		<tbody>
                		<?php if(empty($this->customers)){ ?>
                		<tr><td colspan="13" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                		<?php }else{
                    	foreach ($this->customers as $index => $customer){ 
							$published_icon = $customer->published ? '<i class="icon-publish" ></i>' : '<i class="icon-unpublish" ></i>';
							$customer_optin = $customer->optin ? '<i class="icon-publish" ></i>' : '<i class="icon-unpublish" ></i>';
							$customer_optin = $customer->optin ? '<i class="icon-publish" ></i>' : '<i class="icon-unpublish" ></i>';
							$customer_actions = '<a href="javascript:void(0);" class="btn btn-micro hastooltip" ><span class="icon-view"></span>' . ucfirst(JText::_('COM_JEPROSHOP_VIEW_LABEL')) . '</a><button data-toggle="dropdown" class="dropdown-toggle btn btn-micro" ><span class="caret" ></span></button><ul class="dropdown-menu" >'; 
							$customer_actions .= '<li><a href="javascript://" onclick=""><span class="icon-edit"></span> ' . ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')) . '</a></li><li><a href="';
							$customer_actions .= 'javascript://" onclick="" ><span class="icon-trash" ></span> ' . ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')) . '</a></li></ul>';
						
							$customer_edit_link = JRoute::_('index.php?option=com_jeproshop&view=customer&task=edit&customer_id='. $customer->customer_id . '&' . JeproshopTools::getCustomerToken() . '=1');
							$customer_view_link = JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id='. $customer->customer_id . '&' . JeproshopTools::getCustomerToken() . '=1');
							$customer_delete_link = JRoute::_('index.php?option=com_jeproshop&view=customer&task=delete&customer_id='. $customer->customer_id . '&' . JeproshopTools::getCustomerToken() . '=1');
						?>
                		<tr class="row_<?php echo ($index%2); ?>" >
                    		<td class="nowrap center " width="1%" ><?php echo ($index + 1); ?></td>
                    		<td class="nowrap center " width="1%" ><?php echo JHtml::_('grid.id', $index, $customer->customer_id); ?></td>
                    		<td class="nowrap " width="3%" ><?php echo ucfirst($customer->title); ?></td>
                    		<td class="nowrap " width="15%" ><a href="<?php echo $customer_view_link; ?>" ><?php echo ucfirst($customer->lastname); ?></a></td>
                    		<td class="nowrap " width="15%" ><a href="<?php echo $customer_view_link; ?>" ><?php echo ucfirst($customer->firstname); ?></a></td>
                    		<td class="nowrap  hidden-phone" width="25%" ><?php echo $customer->email; ?></td>
                    		<td class="nowrap " width="6%" ><?php echo JeproshopTools::displayPrice($customer->total_spent); ?></td>
                            <?php if(JeproshopSettingModelSetting::getValue('enable_b2b_mode')){ ?>
                                <td class="nowrap" width="5" ><?php if(isset($customer->website)){ ?><a href="<?php echo $customer->website; ?>" ><?php } echo ucfirst($customer->company); if(isset($customer->website)){ ?></a><?php } ?></td>
                            <?php } ?>
                    		<td class="nowrap center " width="5%" ><?php echo $published_icon; ?></td>
                    		<td class="nowrap center " width="4%" ><i class="icon-<?php echo (isset($customer->newsletter) ? '' : 'un') . 'publish'; ?>" ></i></td>
                    		<td class="nowrap center " width="4%" ><?php echo $customer_optin; ?></td>
                    		<td class="nowrap center " width="4%" ><?php echo $customer->date_add; ?></td>
                    		<td class="nowrap center " width="4%" ><?php echo $customer->connect; ?></td>
                    		<td class="nowrap center " width="4%" >
                                <div class="btn-group-action" >
                                    <div class="btn-group pull-right" >
                                        <a href="<?php echo $customer_edit_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                        <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo $customer_view_link; ?>" ><i class="icon-search" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_VIEW_LABEL'); ?></a> </li>
                                            <li class="divider" ></li>
                                            <li><a href="<?php echo $customer_delete_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_CUSTOMER_DELETE_LABEL') . $customer->firstname . ' ' . $customer->lastname; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                		</tr>
                		<?php }
                		} ?>
           	 		</tbody>
            		<tfoot><tr><td colspan="14" ><?php echo($this->pagination->getListFooter()); ?></td></tr></tfoot>
        		</table>
        	</div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

