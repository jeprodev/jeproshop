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
$themes_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $themes_dir . '/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.framework');
if($this->check_product_association_ajax){
	$class_input_ajax = 'check_product_name';
}else{
	$class_input_ajax = '';
}
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/js/ckeditor.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/plugins/jquery.typewatch.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/tools/tools.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/customer.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/language.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/price.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/product.js');

$script = "jQuery(document).ready(function(){ jQuery('#jform_product_edit_form').JeproshopProduct({}); });";
$document->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category&category_id=null&parent_id=null'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div> 
        <div id="jform_product_edit_form" style="width: 100%;">
        	<?php 
            echo JHtml::_('bootstrap.startTabSet', 'product_form', array('active' =>'information'));
            echo JHtml::_('bootstrap.addTab', 'product_form', 'information', JText::_('COM_JEPROSHOP_PRODUCT_INFORMATION_TAB_LABEL')) . $this->loadTemplate('information') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'price', JText::_('COM_JEPROSHOP_PRODUCT_PRICE_TAB_LABEL')) . $this->loadTemplate('price') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'seo', JText::_('COM_JEPROSHOP_PRODUCT_SEO_TAB_LABEL')) . $this->loadTemplate('referencing') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'associations', JText::_('COM_JEPROSHOP_PRODUCT_ASSOCIATION_TAB_LABEL')) . $this->loadTemplate('association') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'declinations', JText::_('COM_JEPROSHOP_PRODUCT_DECLINATIONS_TAB_LABEL')) . $this->loadTemplate('declination') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'quantities', JText::_('COM_JEPROSHOP_PRODUCT_QUANTITIES_TAB_LABEL')) . $this->loadTemplate('quantities') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'images', JText::_('COM_JEPROSHOP_PRODUCT_IMAGES_TAB_LABEL')) . $this->loadTemplate('images') . JHtml::_('bootstrap.endTab'); 
            echo JHtml::_('bootstrap.addTab', 'product_form', 'features', JText::_('COM_JEPROSHOP_PRODUCT_CHARACTERISTICS_TAB_LABEL')) . $this->loadTemplate('features') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'customization', JText::_('COM_JEPROSHOP_PRODUCT_PERSONALIZATION_TAB_LABEL')) . $this->loadTemplate('customization') . JHtml::_('bootstrap.endTab'); 
            echo JHtml::_('bootstrap.addTab', 'product_form', 'join_files', JText::_('COM_JEPROSHOP_PRODUCT_JOIN_FILE_TAB_LABEL')) . $this->loadTemplate('join_files') . JHtml::_('bootstrap.endTab'); 
            echo JHtml::_('bootstrap.addTab', 'product_form', 'shipping', JText::_('COM_JEPROSHOP_PRODUCT_SHIPPING_TAB_LABEL')) . $this->loadTemplate('shipping') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'supplier', JText::_('COM_JEPROSHOP_PRODUCT_SUPPLIER_TAB_LABEL')) . $this->loadTemplate('supplier') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'product_form', 'developer', JText::_('COM_JEPROSHOP_PRODUCT_DEVELOPER_TAB_LABEL')) . $this->loadTemplate('developer') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.endTabSet');              
            ?>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="product_id" value="<?php echo $this->product->product_id; ?>" />
        <input type="hidden" name="return" value="<?php echo $app->input->get('return'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>