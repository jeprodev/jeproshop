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

class JeproshopAddressViewAddress extends JViewLegacy
{
    protected  $address;
    protected $context;

	public function renderDetails($tpl =null){
        $addressModel = new JeproshopAddressModelAddress();
        $addresses = $addressModel->getAddressList();
        $pagination = $addressModel->getPagination();

        $this->assignRef('addresses', $addresses);
        $this->assignRef('pagination', $pagination);


		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	public function renderView($tpl =null){
        $app = JFactory::getApplication();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);

	}

    public function renderEditForm($tpl = null){
        $app = JFactory::getApplication();
        $this->context = JeproshopContext::getContext();
        $customer_id = $app->input->get('customer_id');
        if(!$customer_id && JeproshopTools::isLoadedObject($this->address, 'address_id')){
            $customer_id = $this->address->customer_id;
        }

        if($customer_id){
            $customer = new JeproshopCustomerModelCustomer((int)$customer_id);
        }else{
            $customer = null;
        }

        //Order address fields depending on country format
        $addresses_fields = $this->processAddressFormat();

        // we use delivery address
        $addresses_fields = $addresses_fields['all_delivery_fields'];

        $temp_fields = array();
        $zones = JeproshopZoneModelZone::getZones($this->context->language->lang_id);
        $countries = JeproshopCountryModelCountry::getStaticCountries($this->context->language->lang_id);

        $this->assignRef('customer', $customer);
        $this->assignRef('countries', $countries);
        $this->assignRef('zones', $zones);

        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    public function renderAddForm($tpl = null){
        $app = JFactory::getApplication();
        $this->context = JeproshopContext::getContext();
        $customer_id = (int)$app->input->get('customer_id');
        if($customer_id){
            $customer = new JeproshopCustomerModelCustomer((int)$customer_id);
            $this->assignRef('customer', $customer);
        }
        // Order address fields depending on country format
        $addresses_fields = $this->processAddressFormat();
        $this->assignRef('delivery_fields', $addresses_fields['all_delivery_fields']);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }
	
	private function addToolBar(){
		switch ($this->getLayout()){
            case 'edit':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_ADDRESS_TITLE'), 'address-jeproshop');
                JToolBarHelper::apply('update');
                JToolBarHelper::cancel('cancel');
                break;
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_ADDRESS_TITLE'), 'address-jeproshop');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADDRESSES_LIST_TITLE'), 'address-jeproshop');
				JToolBarHelper::addNew('add');
				break;
		}
        //JeproshopHelper::
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers', true);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
	}

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $address_id = $app->input->get('address_id');
        if($address_id && JeproshopTools::isUnsignedInt($address_id)){
            if(!$this->address){
                $this->address = new JeproshopAddressModelAddress($address_id);
            }
            if(JeproshopTools::isLoadedObject($this->address, 'address_id')){ return true; }
            return false;
        }elseif($option){
            if(!$this->address){
                $this->address = new JeproshopAddressModelAddress();
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get Address formats used by the country where the address id retrieved from POST/GET is.
     *
     * @return array address formats
     */
    protected function processAddressFormat(){
        $app = JFactory::getApplication();
        $tmp_addr = new JeproshopAddressModelAddress((int)$app->input->get('address_id'));

        $selected_country = ($tmp_addr && $tmp_addr->country_id) ? $tmp_addr->country_id : (int)JeproshopSettingModelSetting::getValue('default_country');

        $invoice_address_fields = JeproshopAddressFormatModelAddressFormat::getOrderedAddressFields($selected_country, false, true);
        $delivery_address_fields = JeproshopAddressFormatModelAddressFormat::getOrderedAddressFields($selected_country, false, true);

        $all_invoice_fields = array();
        $all_delivery_fields = array();

        $out = array();

        foreach (array('invoice','delivery') as $adr_type){
            foreach (${$adr_type.'_address_fields'} as $fields_line){
                foreach (explode(' ', $fields_line) as $field_item){
                    ${'all_' . $adr_type .'_fields'}[] = trim($field_item);
                }
            }

            $out[$adr_type.'_address_fields'] = ${$adr_type.'_address_fields'};
            $out['all_'.$adr_type . '_fields'] = ${'all_' . $adr_type.'_fields'};
        }

        return $out;
    }

    protected function renderSubMenu($current = 'customer'){
        $script = '<div class="box_wrapper jeproshop_sub_menu_wrapper"><fieldset class="btn-group">';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=customer') . '" class="btn jeproshop_sub_menu ' . (($current == 'customer' ) ? 'btn-success' : '') . '" ><i class="icon-customer" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=address') . '" class="btn jeproshop_sub_menu ' . (($current == 'address' ) ? 'btn-success' : '') . '" ><i class="icon-address" ></i> '. ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=group') . '" class="btn jeproshop_sub_menu ' . (($current == 'group' ) ? 'btn-success' : '') . '" ><i class="icon-group" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=cart') . '" class="btn jeproshop_sub_menu ' . (($current == 'cart' ) ? 'btn-success' : '') . '" ><i class="icon-cart" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads') . '" class="btn jeproshop_sub_menu ' . (($current == 'threads' ) ? 'btn-success' : '') . '" ><i class="icon-thread" ></i> ' .  ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=contact') . '" class="btn jeproshop_sub_menu ' . (($current == 'contact' ) ? 'btn-success' : '') . '" ><i class="icon-contact" ></i> ' . ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')) . '</a>';
        $script .= '</fieldset></div>';
        return $script;
    }
}