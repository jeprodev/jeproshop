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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" method="post" name="adminForm" id="adminForm" class="jform-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<?php echo $this->renderSubMenu('group'); ?>
        <div class="separation" ></div>
    	<div class="panel" >
    		<div class="panel-content" >
    			<table class="table table-striped" >
    				<thead>
    					<tr>
    							<th width="1%" class="nowrap center" >#</th>
    							<th width="1%" class="nowrap center" ><?php echo JHtml::_('grid.checkall'); ?></th>
    							<th width="40%" class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_GROUP_NAME_LABEL')?></th> 
    							<th class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_REDUCTION_LABEL')?></th> 
    							<th class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_MEMBERS_LABEL')?></th> 
    							<th class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_DISPLAY_PRICES_LABEL')?></th> 
    							<th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_CREATION_DATE_LABEL')?></th> 
    							<th class="nowrap" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL')?></th> 
    					</tr>
    				</thead>
    					<tbody>
    						<?php if(empty($this->groups)){ ?>
                			<tr>
                    			<td colspan="8" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MACTHING_RESULTS'); ?></div></td>
                			</tr>
                			<?php } else { 
                				foreach($this->groups as $index => $group){ 
									$show_prices = $group->show_prices ? '<i class="icon-publish" ></i>' : '<i class="icon-unpublish" ></i>';
									$groupEditLink = JRoute::_('index.php?option=com_jeproshop&view=group&task=edit&group_id=' . (int)$group->group_id  . '&'. JeproshopTools::getGroupToken() . '=1');
									$groupViewLink = JRoute::_('index.php?option=com_jeproshop&view=group&task=view&group_id=' . (int)$group->group_id  . '&'. JeproshopTools::getGroupToken() . '=1');
									$groupDeleteLink = JRoute::_('index.php?option=com_jeproshop&view=group&task=delete&group_id=' . (int)$group->group_id . '&'. JeproshopTools::getGroupToken() . '=1'); ?>
                			<tr>
                				<td class="order nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                    			<td class="order nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $group->group_id); ?></td>
                    			<td class="order nowrap "><a href="<?php echo $groupEditLink; ?>" ><?php echo ucfirst($group->name); ?></a></td>
                    			<td class="order nowrap center"><?php echo $group->reduction . '%'; ?></td>
                    			<td class="order nowrap center"><?php echo $group->nb; ?></td>
                    			<td class="order nowrap center" ><?php echo $show_prices; ?></td>
                    			<td class="order nowrap "><?php echo $group->date_add; ?></td>
                    			<td class="order nowrap ">
                    				<div class="btn-group-action" >
	                            		<div class="btn-group pull-right" >
	                                		<a href="<?php echo $groupEditLink; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
	                                		<button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
	                                		<ul class="dropdown-menu">
	                                    		<li>
	                                        		<a href="<?php echo $groupViewLink; ?>" title="<?php echo JText::_('COM_JEPROSHOP_VIEW_LABEL'); ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_VIEW_LABEL'); ?>')) document.location ='<?php echo $groupViewtLink; ?>'; else document.location = '<?php echo $view_group_link; ?>'; return false; ">
	                                            		<i class="icon-search" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_VIEW_LABEL'); ?>
	                                        		</a>
	                                    		</li><li class="divider" ></li>
	                                    		<li><a href="<?php echo $groupDeleteLink; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_DELETE_LABEL') . $group->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
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
    	<input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>    
 </form>