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
$option = $app->input->get('option');
$view = $app->input->get('view');
$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
$limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
$css_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $css_dir .'/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" method="post" name="adminForm" id="adminForm" class="jform-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="separation" ></div>
        <div class="panel" >
    		<div class="panel-content" >
        		<table class="table table-striped" id="productList">
        			<thead>
                		<tr>
                    		<th width="1%" class="nowrap center" >#</th>
                    		<th width="1%" class="nowrap center" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    		<th width="2%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_STATUS_LABEL'), 'p.state', 'ASC'); ?></th>
                    		<th width="9%" class="nowrap center" ><?php echo JHtml::_('searchtools.sort', ucfirst(JText::_('COM_JEPROSHOP_IMAGE_LABEL')), 'p.name', 'ASC'); ?></th>
                    		<th width="13%" class="nowrap " ><?php echo JHtml::_('searchtools.sort', ucfirst(JText::_('COM_JEPROSHOP_NAME_LABEL')), 'p.name', 'ASC'); ?></th>
                    		<th width="4%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_REFERENCE_LABEL'), 'p.reference', 'ASC'); ?></th>
                    		<th width="4%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_CATEGORY_NAME_LABEL'), 'p.category', 'ASC'); ?></th>
                    		<th width="3%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_BASE_PRICE_LABEL'), 'p.bprice', 'ASC'); ?></th>
                    		<th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_FINAL_PRICE_LABEL'), 'p.fprice', 'ASC'); ?></th>
                    		<th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_QUANTITY_LABEL'), 'p.quantity', 'ASC'); ?></th>
                    		<th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_ACTIONS_LABEL'), 'p.action', ''); ?></th>
                		</tr>
            		</thead>
            		<tbody>
                		<?php if(empty($this->products)){ ?>
                		<tr><td colspan="11" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                		<?php } else { 
                    		foreach($this->products as $index => $product){ 
                        		$product_link = JRoute::_('index.php?option=com_jeproshop&view=product&task=edit&product_id=' . $product->product_id .'&' . JSession::getFormToken() .'=1');
                        		$duplicate_product_link = JRoute::_('index.php?option=com_jeproshop&view=product&task=duplicate&product_id=' . $product->product_id .'&' . JSession::getFormToken() .'=1');
                        		$delete_product_link = JRoute::_('index.php?option=com_jeproshop&view=product&task=delete&product_id=' . $product->product_id .'&' . JSession::getFormToken() .'=1');
                        		$cover_image = '<img src="" class="image thumbnail" />';
                        		$product_state = ($product->published ? 'icon-publish' : 'icon-unpublish');
                    		?>
                		<tr class="row_<?php echo $index % 2; ?>" sortable-group-id="<?php echo $product->category_id; ?>">
                    		<td class="nowrap center" ><?php echo  $limit_start + $index + 1; ?></td>
                    		<td class="nowrap center" ><?php echo JHtml::_('grid.id', $index, $product->product_id); ?></td>
                    		<td class="center hidden-phone">
                        		<div class="btn-group">
                            		<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="listProductTask(<?php echo $product->product_id . ', ' . ($product->published ? '\'unpublish\'' : '\'publish\'');?>)" >
                            			<i class="<?php echo $product_state; ?>" ></i></a> <?php //echo JHtml::_('jgrid.published', !$product->plublished, $index, 'product', $canChange, 'cb', $product->date_add, $product->available_date); ?>
                            		<?php //echo JHtml::_('jeproshopadministrator.featured', $index, $canChange); ?>                            
                        		</div>
                    		</td>
                    		<td class=" nowrap center " ><a href="<?php echo $product_link; ?>" ><?php echo $cover_image; ?></a></td>
                    		<td class=" nowrap " ><a href="<?php echo $product_link; ?>" ><?php echo ucfirst($product->name); ?></a></td>
                    		<td class=" nowrap center hidden-phone" ><a href="<?php echo $product_link; ?>" ><?php echo $product->reference; ?></a></td>
                    		<td class=" nowrap center hidden-phone" ><?php echo $product->category_name; ?></td>
                    		<td class=" nowrap center hidden-phone" ><?php echo $product->price; ?></td>
                    		<td class=" nowrap center hidden-phone" ><?php echo $product->final_price; ?></td>
                    		<td class=" nowrap center hidden-phone" ><?php echo $product->stock_available_quantity; ?></td>
                    		<td class=" nowrap center hidden-phone" >
                        		<div class="btn-group-action" >
                            		<div class="btn-group pull-right" >
                                		<a href="<?php echo $product_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                		<button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                		<ul class="dropdown-menu">
                                    		<li>
                                        		<a href="#" title="<?php echo JText::_('COM_JEPROSHOP_DUPLICATE_LABEL'); ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_COPY_IMAGES_TOO_LABEL'); ?>')) document.location ='<?php echo $copyImageTooLink; ?>'; else document.location = '<?php echo $duplicate_product_link; ?>'; return false; ">
                                            		<i class="icon-copy" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_DUPLICATE_LABEL'); ?>
                                        		</a>
                                    		</li><li class="divider" ></li>
                                    		<li><a href="<?php echo $delete_product_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DELETE_LABEL') . $product->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                		</ul>
                            		</div>
                        		</div>
                    		</td>
                		</tr>
                		<?php } 
                		} ?>
           	 		</tbody>
            		<tfoot><tr><td colspan="11" class="center nowrap" ><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        		</table>
        	</div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>