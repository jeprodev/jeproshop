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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" method="post" name="adminForm" id="adminForm" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category&category_id=null&parent_id=null'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="separation" ></div>
        <table class="table table-striped" id="productList">
        	<thead>
                <tr>
                    <th width="1%" class="nowrap center" >#</th>
                    <th width="1%" class="nowrap center" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th class="nowrap" width="60%" ><?php echo JText::_('COM_JEPROSHOP_FEATURE_NAME_LABEL'); ?></th>
                    <th class="nowrap" width="8%" ><?php echo JText::_('COM_JEPROSHOP_FEATURE_VALUES_COUNT_LABEL'); ?></th>
                    <th class="nowrap" width="8%" ><?php echo JText::_('COM_JEPROSHOP_POSITION_LABEL'); ?></th>
                    <th class="nowrap" width="8%" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                </tr>
            </thead>
            <tbody>
            	<?php if(isset($this->features) && count($this->features)){
            		foreach($this->features as $index =>$feature) {
                        $editLink = JRoute::_('index.php?option=com_jeproshop&view=feature&task=edit&feature_id=' . $feature->feature_id);
            	?>
            	<tr class="row_<?php echo $index % 2; ?>" >
            		<td class="nowrap center"><?php echo $index + 1; ?></td>
            		<td class="nowrap center"><?php echo JHtml::_('grid.id', $index, $feature->feature_id); ?></td>
            		<td class="nowrap "><a href="<?php echo $editLink; ?>" ><?php echo ucfirst($feature->name); ?></a></td>
            		<td class="nowrap center"><?php echo $feature->count_values; ?></td>
            		<td class="nowrap center"><?php echo $feature->position; ?></td>
            		<td class="nowrap center">
            			<div class="btn-group pull-right" >
            				<a class="btn btn-micro" ><i class="icon-search" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_VIEW_LABEL')); ?></a>
            				<button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
            				<ul class="dropdown-menu">
            					<li><a href="#" ><i class="icon-edit" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?></a></li>
            					<li class="divider" ></li>
            					<li><a href="#" ><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
            				</ul>
            			</div>
            		</td>
            	</tr>
            	<?php }
            	}else{ ?>
            	<tr>
            		<td colspan="6" ><div class="alert warning" ><?php echo JText::_('COM_JEPROSHOP_NOT_MATCHING_MESSAGE'); ?></div></td>
            	</tr>
            	<?php } ?>
            </tbody>
            <tfoot><tr><td colspan="7" ><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        </table>
    </div>
    <input type="hidden" name="task" value="" />
</form>