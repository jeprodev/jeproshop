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

class JeproshopAttributeViewAttribute extends JViewLegacy
{
	public $context = null;

    protected $attribute;

	public function renderDetails($tpl = null){
		$attributeModel = new JeproshopAttributeGroupModelAttributeGroup();
        $attribute_groups = $attributeModel->getAttributeGroupList();
		$this->assignRef('attribute_groups', $attribute_groups);
        $pagination = $attributeModel->getPagination();
		$this->assignRef('pagination', $pagination);
		$this->addToolBar();
		$this->sideBar = JHtmlSideBar::render();

		parent::display($tpl);
	}

    public function editGroup($tpl =null){
        $app = JFactory::getApplication();
        $attributeGroupId = $app->input->get('attribute_group_id');
        $attributeGroup = new JeproshopAttributeGroupModelAttributeGroup($attributeGroupId);
        $this->assignRef('attributeGroup', $attributeGroup);
        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $attributes_groups = JeproshopAttributeGroupModelAttributeGroup::getAttributesGroups($this->context->language->lang_id);

        $this->assignRef('attribute_groups', $attributes_groups);
        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }
	
	private function addToolBar(){
		switch ($this->getLayout()){
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_ATTRIBUTE_TITLE'), 'attribute-jeproshop');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_ATTRIBUTE_GROUP_TITLE'), 'attribute-jeproshop');
                JToolBarHelper::apply('update');
                JToolBarHelper::save('update_stay');
                JToolBarHelper::cancel('cancel');
                break;
			case 'add_group':
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_ATTRIBUTE_TITLE'), 'attribute-jeproshop');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
            case 'edit_group' :
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_ATTRIBUTE_GROUP_TITLE'), 'attribute-jeproshop');
                JToolBarHelper::apply('update_group');
                JToolBarHelper::cancel('cancel');
                break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROSHOP_ATTRIBUTE_GROUPS_LIST_TITLE'), 'attribute-jeproshop');
				JToolBarHelper::addNew('add');
				JToolBarHelper::custom('add_value', '', '', JText::_('COM_JEPROSHOP_ADD_ATTRIBUTE_VALUE_LABEL'));
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

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $attribute_id = $app->input->get('attribute_id');
        if($attribute_id && JeproshopTools::isUnsignedInt($attribute_id)){
            if(!$this->attribute){
                $this->attribute = new JeproshopAttributeModelAttribute($attribute_id);
            }
            if(JeproshopTools::isLoadedObject($this->attribute, 'attribute_id')){
                return $this->attribute;
            }
            JError::raiseError(500, JText::_('COM_JEPROSHOP_ATTRIBUTE_CANNOT_BE_LOADED_OR_FOUND_LABEL'));
        }elseif($option){
            if($this->attribute){
                $this->attribute = new JeproshopAttributeModelAttribute();
            }
            return $this->attribute;
        }else{
            JError::raiseError(500, JText::_('COM_JEPROSHOP_THE_ATTRIBUTE_CANNOT_BE_LOADED_THE_IDENTIFIER_IS_MISSING_OR_INVALID_MESSAGE'));
            return false;
        }
    }
}