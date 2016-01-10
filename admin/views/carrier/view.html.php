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

class JeproshopCarrierViewCarrier extends JViewLegacy
{
    protected $carrier = null;

    public function renderDetails($tpl =null){
        $carrierModel = new JeproshopCarrierModelCarrier();
        $carriers = $carrierModel->getCarriersList();
        $this->assignRef('carriers', $carriers);
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    public function renderAddForm($tpl = null){
        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $tax_rules_groups = JeproshopTaxRulesGroupModelTaxRulesGroup::getTaxRulesGroups(true);
        $this->assignRef('tax_rules_groups', $tax_rules_groups);
        $carrier_logo = false;
        $this->assignRef('carrier_logo', $carrier_logo);
        $groups = JeproshopGroupModelGroup::getGroups(JeproshopContext::getContext()->language->lang_id);
        $this->assignRef('groups', $groups);
        $zones = JeproshopZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        //$carrierZones = $this->carrier->getZones();
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    public function renderEditForm($tpl =null){
        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $tax_rules_groups = JeproshopTaxRulesGroupModelTaxRulesGroup::getTaxRulesGroups(true);
        $this->assignRef('tax_rules_groups', $tax_rules_groups);
        $carrier_logo = JeproshopTools::isLoadedObject($this->carrier, 'carrier_id') && file_exists(COM_JEPROSHOP_CARRIER_IMAGE_DIR . $this->carrier->carrier_id . '.jpg') ? COM_JEPROSHOP_CARRIER_IMAGE_DIR . $this->carrier->carrier_id . '.jpg' : false;
        $this->assignRef('carrier_logo', $carrier_logo);
        $groups = JeproshopGroupModelGroup::getGroups(JeproshopContext::getContext()->language->lang_id);
        $this->assignRef('groups', $groups);
        $zones = JeproshopZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        $carrierZones = $this->carrier->getZones();
        $carrier_zones_ids = array();
        if (is_array($carrierZones)) {
            foreach ($carrierZones as $carrier_zone)
                $carrier_zones_ids[] = $carrier_zone->zone_id;
        }
        $this->assignRef('selected_zones', $carrier_zones_ids);
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    private function addToolBar(){
        switch ($this->getLayout()){
            case 'edit':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_CARRIER_TITLE'), 'carrier-jeproshop');
                JToolBarHelper::apply('update');
                JToolBarHelper::cancel('cancel');
                break;
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_CARRIERS_TITLE'), 'address-jeproshop');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            default:
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_CARRIERS_LIST_TITLE'), 'address-jeproshop');
                JToolBarHelper::addNew('add');
                break;
        }
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping', true);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
        JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
    }

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $carrier_id = $app->input->get('carrier_id');
        if($carrier_id && JeproshopTools::isUnsignedInt($carrier_id)){
            if(!$this->carrier){
                $this->carrier = new JeproshopCarrierModelcarrier($carrier_id);
            }
            if(JeproshopTools::isLoadedObject($this->carrier, 'carrier_id')){ return true; }
            return false;
        }elseif($option){
            if(!$this->carrier){
                $this->carrier = new JeproshopCarrierModelCarrier();
            }
            return true;
        }else{
            return false;
        }
    }

}