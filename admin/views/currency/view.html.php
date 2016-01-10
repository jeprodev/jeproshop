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

class JeproshopCurrencyViewCurrency extends JViewLegacy
{
    protected $currency;

    protected $context;

	public function renderDetails($tpl = NULL){
		$currencyModel = new JeproshopCurrencyModelCurrency();
		$currencies = $currencyModel->getCurrenciesList();
		$zones = JeproshopZoneModelZone::getZones();
        $this->assignRef('currencies', $currencies);
		/*$this->assignRef('zones', $zones); */
		//$this->pagination = $countryModel->getPagination();
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}

    public function renderAddForm($tpl = null){

        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){

        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
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

        $currency_id = (int)$app->input->get('currency_id');
        if ($currency_id && JeproshopTools::isUnsignedInt($currency_id)) {
            if (!$this->currency) {
                $this->currency = new JeproshopCurrencyModelCurrency($currency_id);
            }
            if (JeproshopTools::isLoadedObject($this->currency, 'currency_id'))
                return $this->currency;
            // throw exception
            JError::raiseError(500, 'The currency cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->currency)
                $this->currency = new JeproshopCurrencyModelCurrency();
            return $this->currency;
        } else {
            $this->context->controller->has_errors = true;
            Tools::displayError('The currency cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }
	
	private function addToolBar(){
		switch($this->getLayout()){
            case 'add':
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_CURRENCY_TITLE'), 'currency-jeproshop');
                JToolbarHelper::apply('add');
                break;
            case 'edit':
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_CURRENCY_TITLE'), 'currency-jeproshop');
                JToolbarHelper::apply('update', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                break;
			default:
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_CURRENCIES_LIST_TITLE'), 'currency-jeproshop');
				JToolbarHelper::addNew('add');
				break;
		}
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_DASHBOARD_LABEL'), 'index.php?option=com_jeproshop');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CATALOG_LABEL'), 'index.php?option=com_jeproshop&task=catalog');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_ORDERS_LABEL'), 'index.php?option=com_jeproshop&task=orders');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL'), 'index.php?option=com_jeproshop&task=customers');
		JHtmlSidebar::addEntry(JText::_('COM_JEPROSHOP_PRICE_RULES_LABEL'), 'index.php?option=com_jeproshop&task=price_rules');
		//JHtmlSidebar::addEntry('COM_JEPROSHOP_LABEL', 'index.php?option=com_jeproshop&task=');
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
}