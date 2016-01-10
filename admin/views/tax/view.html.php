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

class JeproshopTaxViewTax extends JViewLegacy
{
	public $context;

    protected  $tax;
    protected  $tax_rules_group;

    protected $helper;
	
	public function renderDetails($tpl = NULL){
        $taxModel = new JeproshopTaxModelTax();

        $taxes = $taxModel->getTaxList();
        $this->assignRef('taxes', $taxes);
		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	
	public function renderAddForm($tpl = NULL){
        $this->helper = new JeproshopHelper();
		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    public function renderEditForm($tpl = NULL){
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function viewRules($tpl = NULL){
		$this->addToolBar();
		$this->sideBar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    public function renderAddRulesGroup($tpl = NULL){
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderEditRulesGroup($tpl = NULL){
        $this->helper = new JeproshopHelper();
        $this->loadTaxRuleGroup();
        if(!isset($this->tax_rules_group->tax_rules_group_id)){
            //to do redirect to add page with warning
        }
        $tax_rules = JeproshopTaxRuleModelTaxRule::getStaticTaxRulesList($this->tax_rules_group->tax_rules_group_id);
        $this->assignRef('tax_rules', $tax_rules);

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderAddRule($tpl = NULL){
        $app = JFactory::getApplication();
        $this->helper = new JeproshopHelper();
        $this->context = JeproshopContext::getContext();
        $taxModel = new JeproshopTaxModelTax();
        $taxes = $taxModel->getTaxes($this->context->language->lang_id);
        $this->assignRef('taxes', $taxes);
        $taxRulesGroupId = (int)$app->input->get('tax_rules_group_id');
        $this->assignRef('tax_rules_group_id', $taxRulesGroupId);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderRuleGroup($tpl = null){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $taxRuleGroupModel = new JeproshopTaxRulesGroupModelTaxRulesGroup();
        $taxRuleGroups = $taxRuleGroupModel->getTaxRulesGroupsList();
        $this->assignRef('tax_rules_groups', $taxRuleGroups);
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderEditRule($tpl = NULL){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $this->helper = new JeproshopHelper();

        $countryModel = new JeproshopCountryModelCountry();
        $countries = $countryModel->getCountries($this->context->language->lang_id);

        $taxModel = new JeproshopTaxModelTax();
        $taxes = $taxModel->getTaxes((int)$this->context->language->lang_id);
        $this->assignRef('taxes', $taxes);

        $taxRuleModel = new JeproshopTaxRuleModelTaxRule();
        $taxRules = $taxRuleModel->getTaxRuleList();
        $this->assignRef('tax_rules', $taxRules);
        $this->assignRef('countries', $countries);
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

        $tax_id = (int)$app->input->get('tax_id');
        if ($tax_id && JeproshopTools::isUnsignedInt($tax_id)) {
            if (!$this->tax) {
                $this->tax = new JeproshopTaxModelTax($tax_id);
            } //print_r($this->tax); exit();
            if (JeproshopTools::isLoadedObject($this->tax, 'tax_id'))
                return $this->tax;
            // throw exception
            JError::raiseError(500, 'The tax cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->tax)
                $this->tax = new JeproshopTaxModelTax();
            return $this->tax;
        } else {
            $this->context->controller->has_errors = true;
            Tools::displayError('The tax cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

    /**
     * Load class supplier using identifier in $_GET (if possible)
     * otherwise return an empty supplier, or die
     *
     * @param boolean $opt Return an empty supplier if load fail
     * @return supplier|boolean
     */
    public function loadTaxRuleGroup($opt = false){
        $app =JFactory::getApplication();

        $tax_rules_group_id = (int)$app->input->get('tax_rules_group_id');
        if ($tax_rules_group_id && JeproshopTools::isUnsignedInt($tax_rules_group_id)) {
            if (!$this->tax_rules_group) {
                $this->tax_rules_group = new JeproshopTaxRulesGroupModelTaxRulesGroup($tax_rules_group_id);
            }
            if (JeproshopTools::isLoadedObject($this->tax_rules_group, 'tax_rules_group_id'))
                return true;
            // throw exception
            JError::raiseError(500, 'The tax rules group cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->tax_rules_group)
                $this->tax_rules_group = new JeproshopTaxRulesGroupModelTaxRulesGroup();
            return true;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError(500, 'The tax cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

	private function addToolBar(){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
		switch ($this->getLayout()){
            case 'add_rules_group' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_TAX_RULES_GROUP_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('save_rules_group');
                JToolbarHelper::cancel('cancel');
                break;
            case 'edit_rules_group' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_TAX_RULES_GROUP_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('update_rules_group', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                JToolBarHelper::custom('add_rule', '', '', JText::_('COM_JEPROSHOP_ADD_NEW_RULE_LABEL'), false);
                JToolbarHelper::cancel('cancel');
                break;
			case 'rules' :
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_TAX_RULES_LIST_TITLE'), 'tax-jeproshop');

				break;
            case 'add_rule' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_TAX_RULE_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('save_rule');
                JToolbarHelper::cancel('cancel');
                break;
            case 'edit_rule' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_TAX_RULE_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('update_rule');
                JToolbarHelper::cancel('cancel');
                break;
            case 'add' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_TAX_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('save');
                JToolbarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_EDIT_TAX_LABEL'), 'tax-jeproshop');
                JToolbarHelper::apply('update', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
            case 'groups' :
                JToolbarHelper::title(JText::_('COM_JEPROSHOP_TAX_RULES_GROUPS_LIST_TITLE'), 'tax-jeproshop');
                JToolbarHelper::addNew('add_rules_group');
                JToolbarHelper::custom('add_rule','', '', JText::_('COM_JEPROSHOP_ADD_NEW_RULE_LABEL'), false);
                break;
			default:
				JToolbarHelper::title(JText::_('COM_JEPROSHOP_TAXES_LIST_TITLE'), 'tax-jeproshop');
				JToolbarHelper::addNew('add');
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
}