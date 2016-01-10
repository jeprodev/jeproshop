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

class JeproshopGroupViewGroup extends JViewLegacy
{
	protected $helper = NULL;

    protected $context = null;

    protected $group;
	
	public function renderDetails($tpl = null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		$unidentified = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('unidentified_group'));
		$guest = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('guest_group'));
		$default = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('customer_group'));
		/*
		$unidentified_group_information = sprintf(
				/*$this->l('%s - All persons without a customer account or customers that are not logged in.'),
				'<b>'.$unidentified->name[$this->context->language->id].'</b>' * /
		);
		$guest_group_information = sprintf(
				/*$this->l('%s - All persons who placed an order through Guest Checkout.'),
				'<b>'.$guest->name[$this->context->language->id].'</b>' * /
		);
		$default_group_information = sprintf(
				/*$this->l('%s - All persons who created an account on this site.'),
				'<b>'.$default->name[$this->context->language->id].'</b>' */
		//);
		$groupModel = new JeproshopGroupModelGroup();
		$groups = $groupModel->getGroupList();
		
		$this->assignRef('groups', $groups);
		/*$this->displayInformation($this->l('PrestaShop has three default customer groups:'));
		$this->displayInformation($unidentified_group_information);
		$this->displayInformation($guest_group_information);
		$this->displayInformation($default_group_information); */
		
		parent::display($tpl);
	}

    public function renderView($tpl = null){
        $app = JFactory::getApplication();
        $this->context = JeproshopContext::getContext();
        $customerList = $this->group->getCustomersList();
        $this->assignRef('customers', $customerList);
		$categoryReductions = $this->formatCategoryDiscountList($this->group->group_id);
        $this->assignRef('category_reductions', $categoryReductions);

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    public function formatCategoryDiscountList($group_id){
        return false;
    }
	
	public function addGroup($tpl = null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		$this->helper = new JeproshopHelper();
		parent::display($tpl);
	}
    public function renderAddForm($tpl = null){
        $this->helper = new JeproshopHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        $this->helper = new JeproshopHelper();
        parent::display($tpl);
    }

	private function addToolBar(){
		switch ($this->getLayout()){
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_GROUP_TITLE'), 'group-jeproshop');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
            case 'view' :
                break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_GROUPS_LIST_TITLE'), 'group-jeproshop');
				JToolBarHelper::addNew('add');
				break;
		}
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

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die
     *
     * @param boolean $opt Return an empty object if load fail
     * @return object|boolean
     */
    public function loadObject($opt = false){
        $app = JFactory::getApplication();
        $group_id = (int)$app->input->get('group_id');
        if ($group_id && JeproshopTools::isUnsignedInt($group_id)){
            if (!$this->group)
                $this->group = new JeproshopGroupModelGroup($group_id);
            if (JeproshopTools::isLoadedObject($this->group, 'group_id'))
                return true;
            // throw exception
            $this->context->controller->has_errors= true;
            JError::raiseError('The object cannot be loaded (or found)');
            return false;
        }
        elseif ($opt){
            if (!$this->group)
                $this->group = new JeproshopGroupModelGroup();
            return $this->group;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError('The object cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
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