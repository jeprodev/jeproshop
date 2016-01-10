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

class JeproshopCountryViewCountry extends JViewLegacy
{
	public $context;

    protected $helper;

    protected $country;
    protected $zone;
    protected $state;

    protected $zones;
    protected $states;

	public function renderDetails($tpl = NULL){
		$countryModel = new JeproshopCountryModelCountry();
		$this->countries = $countryModel->getCountryList();
		$zones = JeproshopZoneModelZone::getZones();
		$this->assignRef('zones', $zones);
		$this->pagination = $countryModel->getPagination();
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}

	public function renderAddForm($tpl = NULL){
		$this->helper = new JeproshopHelper();
		$languages = JeproshopLanguageModelLanguage::getLanguages();
		$this->assignRef('languages', $languages);
		$currencies = JeproshopCurrencyModelCurrency::getStaticCurrencies();
		$this->assignRef('currencies', $currencies);
		$zones = JeproshopZoneModelZone::getZones();
		$this->assignRef('zones', $zones);
		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    public function renderEditForm($tpl = NULL){
        $app = JFactory::getApplication();
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $this->helper = new JeproshopHelper();
        $languages = JeproshopLanguageModelLanguage::getLanguages();
        $this->assignRef('languages', $languages);
        $currencies = JeproshopCurrencyModelCurrency::getStaticCurrencies();
        $this->assignRef('currencies', $currencies);
        $zones = JeproshopZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        $addressLayout = JeproshopAddressFormatModelAddressFormat::getAddressCountryFormat($this->country->country_id);
        if($value = $app->input->get('address_layout')){ $addressLayout = $value; }

        $default_layout = '';

        $default_layout_tab = array(
            array('firstname', 'lastname'),
            array('company'),
            array('vat_number'),
            array('address1'),
            array('address2'),
            array('postcode', 'city'),
            array('Country:name'),
            array('phone'),
            array('phone_mobile'));
        foreach ($default_layout_tab as $line) {
            $default_layout .= implode(' ', $line) . "\r\n";
        }

        $this->assignRef('address_layout', $addressLayout);
        $encodingAddressLayout = urlencode($addressLayout);
        $this->assignRef('encoding_address_layout', $encodingAddressLayout);
        $encodingDefaultLayout = urlencode($default_layout);
        $this->assignRef('encoding_default_layout', $encodingDefaultLayout);
        $displayValidFields = $this->displayValidFields();
        $this->assignRef('display_valid_fields', $displayValidFields);

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function renderEditZone($tpl = null){
        $this->loadZone();
        $this->helper = new JeproshopHelper();
		/*$zoneModel = new JeproshopZoneModelZone();
		$this->zones = $zoneModel->getZoneList();*/

		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    public function renderAddZone($tpl = null){
        $this->helper = new JeproshopHelper();

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderAddState($tpl = null){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $this->helper = new JeproshopHelper();
        $countries = JeproshopCountryModelCountry::getStaticCountries($this->context->language->lang_id);
        $zones = JeproshopZoneModelZone::getZones();

        $this->assignRef('countries', $countries);
        $this->assignRef('zones', $zones);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function viewZones($tpl = null){
        $zoneModel = new JeproshopZoneModelZone();
        $this->zones = $zoneModel->getZoneList();

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function viewStates($tpl = null){
		$stateModel = new JeproshopStateModelState();
		$this->states = $stateModel->getStateList();

		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    /**
     * Load class supplier using identifier in $_GET (if possible)
     * otherwise return an empty supplier, or die
     *
     * @param boolean $opt Return an empty supplier if load fail
     * @return supplier|boolean
     */
    public function loadObject($opt = false){
        $app =JFactory::getApplication();

        $country_id = (int)$app->input->get('country_id');
        if ($country_id && JeproshopTools::isUnsignedInt($country_id)) {
            if (!$this->country) {
                $this->country = new JeproshopCountryModelCountry($country_id);
            }
            if (JeproshopTools::isLoadedObject($this->country, 'country_id'))
                return $this->country;
            // throw exception
            JError::raiseError(500, 'The country cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->country)
                $this->country = new JeproshopCountryModelCountry();
            return $this->country;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError('The country cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

    public function loadZone($opt = false){
        $app =JFactory::getApplication();

        $zone_id = (int)$app->input->get('zone_id');
        if ($zone_id && JeproshopTools::isUnsignedInt($zone_id)) {
            if (!$this->zone) {
                $this->zone = new JeproshopZoneModelZone($zone_id);
            }
            if (JeproshopTools::isLoadedObject($this->zone, 'zone_id'))
                return $this->zone;
            // throw exception
            JError::raiseError(500, 'The zone cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->zone)
                $this->zone = new JeproshopZoneModelZone();
            return $this->zone;
        } else {
            $this->context->controller->has_errors = true;
            Tools::displayError('The zone cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

	private function addToolBar(){
		switch($this->getLayout()){
			case 'states':
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_STATES_LIST_TITLE'), 'country-jeproshop');
				JToolbarHelper::addNew('add_state');
				JToolbarHelper::editList('edit_state');
				JToolbarHelper::publish('publish_state');
				JToolbarHelper::unpublish('unpublish_state');
				JToolbarHelper::trash('trash_state');
				break;
            case 'add_state' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_STATE_TITLE'), 'country-jeproshop');
                JToolbarHelper::apply('save_state');
                break;
            case 'edit_state' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_STATE_TITLE'), 'country-jeproshop');
                JToolbarHelper::apply('update_state', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                break;
			case 'zones' :
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_ZONES_LIST_TITLE'), 'country-jeproshop');
				JToolbarHelper::addNew('add_zone');
				JToolbarHelper::editList('edit_zone');
				JToolbarHelper::publish('publish_zone');
				JToolbarHelper::unpublish('unpublish_zone');
				JToolbarHelper::trash('trash_zone');
				break;
            case 'edit_zone' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_ZONE_TITLE'), 'country-jeproshop');
                JToolbarHelper::apply('update_zone', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
            case 'add_zone' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_ZONE_TITLE'), 'country-jeproshop');
                JToolbarHelper::apply('save_zone');
                JToolbarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_COUNTRY_TITLE'), 'country-jeproshop');
                JToolbarHelper::apply('update', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
			default:
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_COUNTRIES_LIST_TITLE'), 'country-jeproshop');
                JToolbarHelper::addNew('add');
				JToolbarHelper::editList('edit');
				JToolbarHelper::publish('publish');
				JToolbarHelper::unpublish('unpublish');
				JToolbarHelper::trash('trash');

				$status_options = '<option value="1" >' . JText::_('JPUBLISHED') . '</option>';
				$status_options .= '<option value="0" >' . JText::_('JUNPUBLISHED') . '</option>';
				JHtmlSidebar::addFilter(JText::_('COM_JEPROSHOP_SELECT_COUNTRY_STATUS_LABEL'), 'jform[filter_state]', $status_options, FALSE);
				$zone_options = '';
				foreach ($this->zones as $zone){
					$zone_options .= '<option value="'. $zone->zone_id . '" >' . $zone->name . '</option>';
				}
				JHtmlSidebar::addFilter(JText::_('COM_JEPROSHOP_SELECT_COUNTRY_ZONE_LABEL'), 'jform[filter_zone]', $zone_options, FALSE);
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SHIPPING_LABEL'), 'index.php?option=com_jeproshop&task=shipping');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_LOCALIZATION_LABEL'), 'index.php?option=com_jeproshop&task=localization', true);
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_SETTINGS_LABEL'), 'index.php?option=com_jeproshop&task=settings');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ADMINISTRATION_LABEL'), 'index.php?option=com_jeproshop&task=administration');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_STATS_LABEL'), 'index.php?option=com_jeproshop&task=stats');
	}

    protected function renderSubMenu($current = 'country'){
        $script = '<fieldset class="btn-group" >';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=country') . '" class="btn jeproshop_sub_menu' . (($current == 'country') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_COUNTRIES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=country&task=zone') . '" class="btn jeproshop_sub_menu' . (($current == 'zone') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_ZONES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=country&task=states') . '" class="btn jeproshop_sub_menu' . (($current == 'states') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_STATES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_languages') . '" class="btn jeproshop_sub_menu' . (($current == 'languages') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_LANGUAGES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=currency') . '" class="btn jeproshop_sub_menu' . (($current == 'currency') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_CURRENCIES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=tax') . '" class="btn jeproshop_sub_menu' . (($current == 'tax') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_TAXES_LABEL')) . '</a>';
        //$script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=tax&task=rules') . '" class="btn jeproshop_sub_menu' . (($current == 'rules') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_TAX_RULES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeproshop&view=tax&task=rule_group') . '" class="btn jeproshop_sub_menu' . (($current == 'rule_group') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROSHOP_TAX_RULES_GROUP_LABEL')) . '</a>';
        $script .= '</fieldset>';

        return $script;
    }

    protected function displayValidFields(){
        $objectList = JeproshopAddressFormatModelAddressFormat::getLiableClass('JeproshopAddressModelAddress');
    }

    public function displayCallPrefix($prefix)
    {
        return ((int)$prefix ? '+'.$prefix : '-');
    }
}