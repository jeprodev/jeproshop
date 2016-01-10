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

class JeproshopViewDashboard extends JViewLegacy
{
    protected $context;

	public function display($tpl = null){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $app = JFactory::getApplication();

        $warning = $this->getWarningDomainName();

        $calendarHelper = new JeproshopCalendarHelper();
        $calendarHelper->setDateFrom($app->input->get('date_from', $this->context->employee->stats_date_from));
        $calendarHelper->setDateTo($app->input->get('date_to', $this->context->employee->stats_date_to));

        $stats_compare_from = $this->context->employee->stats_compare_from;
        $stats_compare_to = $this->context->employee->stats_compare_to;

        if (is_null($stats_compare_from) || $stats_compare_from == '0000-00-00'){ $stats_compare_from = null; }
        if (is_null($stats_compare_to) || $stats_compare_to == '0000-00-00'){ $stats_compare_to = null; }

        $calendarHelper->setCompareDateFrom($stats_compare_from);
        $calendarHelper->setCompareDateTo($stats_compare_to);
        $calendarHelper->setCompareOption($app->input->get('compare_date_option', $this->context->employee->stats_compare_option));


        $calendar = $calendarHelper->generate();

        $dashboard_zone_one = '';
        $dashboard_zone_two = '';

        $this->assignRef('warning', $warning);
        $this->assignRef('calendar', $calendar);
        $this->assignRef('dashboard_zone_one', $dashboard_zone_one);
        $this->assignRef('dashboard_zone_two', $dashboard_zone_two);

		$this->addToolBar();
		$this->sideBar = JHtmlSideBar::render();
	
		parent::display($tpl);
	}

    protected function getWarningDomainName(){
        $warning = false;
        if(JeproshopShopModelShop::isFeaturePublished()){ return null; }

        $shop = JeproshopContext::getContext()->shop;

        return $warning;
    }
	
	private function addToolBar(){
		switch ($this->getLayout()){
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_CATEGORY_TITLE'), 'jeproshop-dashboard');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'jeproshop-dashboard');
				//JToolBarHelper::addNew('add');
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop', true);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
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