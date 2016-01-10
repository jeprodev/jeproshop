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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div id="logo_wrapper" class="pull-left">
            <div id="carrier_logo_block" class="panel">
                <div class="panel-title">
                    <?php echo JText::_('COM_JEPROSHOP_LOGO_LABEL'); ?>
                    <div class="panel-title-action">
                        <a id="carrier_logo_remove" class="btn btn-default" <?php if(!$this->carrier_logo){ ?>style="display:none"<?php } ?> href="jeproCarrier.removeCarrierLogo();" >
                        <i class="icon-trash"></i>
                        </a>
                    </div>
                </div>
                <img id="carrier_logo_img" src="<?php if($this->carrier_logo){ echo $this->carrier_logo; }else{ echo '../img/admin/carrier-default.jpg';  } ?>" class="img-thumbnail" alt=""/>
            </div>
        </div>
        <div id="info_wrapper" class="pull-left">
            <?php
            echo JHtml::_('bootstrap.startTabSet', 'carrier_form', array('active' =>'information'));
            echo JHtml::_('bootstrap.addTab', 'carrier_form', 'information', JText::_('COM_JEPROSHOP_PRODUCT_INFORMATION_TAB_LABEL')) . $this->loadTemplate('information') . JHtml::_('bootstrap.endTab');
            if(JeproshopShopModelShop::isFeaturePublished()){
                echo JHtml::_('bootstrap.addTab', 'carrier_form', 'multi_store', JText::_('COM_JEPROSHOP_STORES_TAB_LABEL')) . $this->loadTemplate('stores') . JHtml::_('bootstrap.endTab');
            }
            echo JHtml::_('bootstrap.addTab', 'carrier_form', 'cost', JText::_('COM_JEPROSHOP_COST_TAB_LABEL')) . $this->loadTemplate('cost') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'carrier_form', 'size', JText::_('COM_JEPROSHOP_SIZE_TAB_LABEL')) . $this->loadTemplate('size') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'carrier_form', 'resume', JText::_('COM_JEPROSHOP_RESUME_TAB_LABEL')) . $this->loadTemplate('resume') . JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.endTabSet');
            ?>
        </div>
    </div>
    <input type="hidden" name="task" value="" >
</form>