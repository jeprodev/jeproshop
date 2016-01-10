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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal" >   
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category&category_id=null&parent_id=null'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tracking" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <table class="table table-striped" id="productList">
        	<thead>
        		<tr>
        			<th class="nowrap" width="1%" >#</th>
        			<th width="1%" class="nowrap center" ><?php echo JHtml::_('grid.checkall'); ?></th>
        			<th width="3%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'c.state', 'ASC'); ?></th>
        			<th class="nowrap" width="5%" ><?php echo JText::_('COM_JEPROSHOP_CATEGORY_NAME_LABEL'); ?></th>
        			<th class="nowrap" width="60%" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></th>
        			<th class="nowrap center" width="6%" ><?php echo JText::_('COM_JEPROSHOP_POSITION_LABEL'); ?></th>
        			<th class="nowrap center" width="8%" ><span class="pull-right" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
        		</tr>
        	</thead>
            <tbody>
            	<?php if(isset($this->categories) && count($this->categories)){ 
            		foreach($this->categories as $index => $category){ 
						$categoryViewLink = JRoute::_('index.php?option=com_jeproshop&view=category&task=view&category_id=' . (int)$category->category_id . '&' . JSession::getFormToken() . '=1');
						$categoryEditLink = JRoute::_('index.php?option=com_jeproshop&view=category&task=edit&category_id=' . (int)$category->category_id . '&' . JSession::getFormToken() . '=1');
						$categoryDeleteLink = JRoute::_('index.php?option=com_jeproshop&view=category&task=delete&category_id=' . (int)$category->category_id . '&' . JSession::getFormToken() . '=1');
            		?>
            	<tr class="row_<?php echo $index % 2; ?>" >
            		<td class="nowrap center" ><?php echo $index + 1; ?></td>
                    <td class="nowrap center" ><?php echo JHtml::_('grid.id', $index, $category->category_id); ?></td>
                    <td class="center hidden-phone">
                        <div class="btn-group">
                            <a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="listCategoryTask(<?php echo  $category->category_id . ', ' . ($category->published ? '\'unpiblish\'' : '\'publish\'');?>)" >
                            	<i class="icon-publish" ></i></a> <?php //echo JHtml::_('jgrid.published', !$product->plublished, $index, 'product', $canChange, 'cb', $product->date_add, $product->available_date); ?>
                            <?php //echo JHtml::_('jeproshopadministrator.featured', $index, $canChange); ?>                            
                        </div>
                    </td>
            		<td class="nowrap" ><a href="<?php echo $categoryEditLink; ?>" ><?php echo ucfirst($category->name); ?></a></td>
            		<td class="nowrap" ><?php echo $category->description; ?></td>
            		<td class="nowrap center" ><?php echo $category->category_position; ?></td>
            		<td class="nowrap center" >
            			<div class="btn-group pull-right" >
            				<a href="<?php echo $categoryViewLink; ?>" class="btn btn-micro" ><i class="icon-search" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_VIEW_LABEL')); ?></a>
            				<button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
            				<ul class="dropdown-menu">
            					<li><a href="<?php echo $categoryEditLink; ?>" ><i class="icon-edit" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?></a></li>
            					<li class="divider" ></li>

            					<li><a href="<?php echo $categoryDeleteLink; ?>" ><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
            				</ul>
            			</div>
            		</td>
            	</tr>
            	<?php }
            	}else{ ?>
            	<tr>
            		<td colspan="7" ><div class="alert warning" ><?php echo JText::_('COM_JEPROSHOP_NOT_MATCHING_MESSAGE'); ?></div></td>
            	</tr>
            	<?php } ?>
            </tbody>
            <tfoot><tr><td colspan="7" ><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>