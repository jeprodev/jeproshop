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

class JeproshopTrackingViewTracking extends JViewLegacy{
    public function renderDetails($tpl = null){
        $app = JFactory::getApplication();

        $category_id = $app->input->get('category_id');
        $viewCategory = $app->input->get('view_category');

        if($category_id && $viewCategory){
            $app->redirect('index.php?option=com_jeproshop&view=product&category_id=' . (int)$category_id . '&view_category=1');
        }

        if(!JeproshopSettingModelSetting::getValue('stock_management')){
            JError::raiseWarning(JText::_('List of products without available quantities for sale are not displayed because stock management is disabled.'));
        }

        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    private function addToolBar(){
        switch ($this->getLayout()){
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_TRACKING_TITLE'), 'jeproshop-tracking');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_TRACKING_TITLE'), 'jeproshop-tracking');
                JToolBarHelper::apply('update');
                break;
            default:
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_TRACKING_LIST_TITLE'), 'jeproshop-tracking');
                JToolBarHelper::addNew('add');
                break;
        }
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog', true);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
    }

}