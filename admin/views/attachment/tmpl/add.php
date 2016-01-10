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
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/js/ckeditor.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="separation" ></div>
        <div class="panel" >
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label"><label for="jform_filename" ><?php echo JText::_('COM_JEPROSHOP_FILENAME_LABEL'); ?></label></div>
                    <div class="controls"><?php echo $this->helper->multiLanguageInputField('filename', true, null, null); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label"><label for="jform_description" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></label></div>
                    <div class="controls"><?php echo $this->helper->multiLanguageTextAreaField('description', null); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label"><label for="jform_file" ><?php echo JText::_('COM_JEPROSHOP_FILE_LABEL'); ?></label></div>
                    <div class="controls"><?php echo ''; ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label"><label for="jform_-lbl" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label></div>
                    <div class="controls"></div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" >
</form>