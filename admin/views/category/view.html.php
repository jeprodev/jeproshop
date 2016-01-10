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

class JeproshopCategoryViewCategory extends JViewLegacy
{
	public $context = null;

    //protected $category = null;
	
    public function renderDetails($tpl = null){
        $app = JFactory::getApplication();
        $category_id = $app->input->get('category_id');

        if(!isset($this->context) || empty($this->context)){ $this->context = JeproshopContext::getContext(); }
        if(!JeproshopShopModelShop::isFeaturePublished() && count(JeproshopCategoryModelCategory::getCategoriesWithoutParent()) > 1 && $category_id){
            $categories_tree = array(get_object_vars($this->context->controller->category->getTopCategory()));
        }else{
            $categories_tree = $this->context->controller->category->getParentsCategories();
            $end = end($categories_tree);
            if(isset($categories_tree) && !JeproshopShopModelShop::isFeaturePublished() && $end->parent_id != 0){
                $categories_tree = array_merge($categories_tree, array(get_object_vars($this->context->controller->category->getTopCategory())));
            }
        }

        $count_categories_without_parent = count(JeproshopCategoryModelCategory::getCategoriesWithoutParent());

        if(empty($categories_tree) && ($this->context->controller->category->category_id != 1 || $category_id ) && (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_SHOP && !JeproshopShopModelShop::isFeaturePublished() && $count_categories_without_parent > 1)){
            $categories_tree = array(array('name' => $this->context->controller->category->name[$this->context->language->lang_id]));
        }
        $categories_tree = array_reverse($categories_tree);

        $this->assignRef('categories_tree', $categories_tree);
        $this->assignRef('categories_tree_current_id', $this->context->controller->category->category_id);

        $categoryModel = new JeproshopCategoryModelCategory();
        $categories = $categoryModel->getCategoriesList();
        $pagination = $categoryModel->getPagination();
        $this->assignRef('pagination', $pagination);
        $this->assignRef('categories', $categories);
        $this->setLayout('default');

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function  renderView($tpl = null){
        $this->renderDetails($tpl);
    }

    public function renderAddForm($tpl = null){
        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }
        $categories_tree = new JeproshopCategoriesTree('jform_categories_tree', JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'), null, $this->context->language->lang_id);
        $categories_tree->setTreeTemplate('associated_categories')->setUseCheckBox(true)->setInputName('parent_id');
        $categories = $categories_tree->render();
        $this->assignRef('categories_tree', $categories);
        $groups = JeproshopGroupModelGroup::getGroups($this->context->language->lang_id);

        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $this->assignRef('groups', $groups);

        $unidentified = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('unidentified_group'));
        $guest = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('guest_group'));
        $default = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('customer_group'));

        $unidentified_group_information = '<b>' . $unidentified->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_ALL_PEOPLE_WITHOUT_A_VALID_CUSTOMER_ACCOUNT_MESSAGE');
        $guest_group_information = '<b>' . $guest->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_CUSTOMER_WHO_PLACED_AN_ORDER_WITH_THE_GUEST_CHECKOUT_MESSAGE');
        $default_group_information = '<b>' . $default->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_ALL_PEOPLE_WHO_HAVE_CREATED_AN_CREATED_AN_ACCOUNT_ON_THIS_SITE_MESSAGE');


        $this->assignRef('unidentified_group_information', $unidentified_group_information);
        $this->assignRef('guest_group_information', $guest_group_information);
        $this->assignRef('default_group_information', $default_group_information);

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        $this->loadObject(true);
        $app = JFactory::getApplication();

        if(!isset($this->context)){ $this->context = JeproshopContext::getContext(); }

        $shop_id = JeproshopContext::getContext()->shop->shop_id;
        $selected_categories = array((isset($this->context->controller->category->parent_id) && $this->context->controller->category->isParentCategoryAvailable($shop_id)) ? (int)$this->context->controller->category->parent_id : $app->input->get('parent_id', JeproshopCategoryModelCategory::getRootCategory()->category_id));

        $unidentified = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('unidentified_group'));
        $guest = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('guest_group'));
        $default = new JeproshopGroupModelGroup(JeproshopSettingModelSetting::getValue('customer_group'));

        $unidentified_group_information = '<b>' . $unidentified->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_ALL_PEOPLE_WITHOUT_A_VALID_CUSTOMER_ACCOUNT_MESSAGE');
        $guest_group_information = '<b>' . $guest->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_CUSTOMER_WHO_PLACED_AN_ORDER_WITH_THE_GUEST_CHECKOUT_MESSAGE');
        $default_group_information = '<b>' . $default->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROSHOP_ALL_PEOPLE_WHO_HAVE_CREATED_AN_CREATED_AN_ACCOUNT_ON_THIS_SITE_MESSAGE') ;

        $this->assignRef('unidentified_group_information', $unidentified_group_information);
        $this->assignRef('guest_group_information', $guest_group_information);
        $this->assignRef('default_group_information', $default_group_information);

        $image = COM_JEPROSHOP_CATEGORY_IMAGE_DIR . $this->context->controller->category->category_id . '.jpg';
        $image_url = JeproshopImageManager::thumbnail($image, 'category_' . $this->context->controller->category->category_id . '.jpg' , 350, 'jpg', true, true);
        $imageSize = file_exists($image) ? filesize($image)/1000 : false;

        $shared_category = JeproshopTools::isLoadedObject($this->context->controller->category, 'category_id') && $this->context->controller->category->hasMultishopEntries();
        $this->assignRef('shared_category', $shared_category);
        $allow_accented_chars_url = (int)JeproshopSettingModelSetting::getValue('allow_accented_chars_url');
        $this->assignRef('allow_accented_chars_url', $allow_accented_chars_url);
        //$this->assignRef('selected_categories', $selected_categories);

        $categories_tree = new JeproshopCategoriesTree('jform_categories_tree', JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'), null, $this->context->language->lang_id);
        $categories_tree->setTreeTemplate('associated_categories')->setSelectedCategories($selected_categories)->setUseCheckBox(true)->setInputName('parent_id');
        $categories_data = $categories_tree->render();
        $this->assignRef('categories_tree', $categories_data);

        $image = JeproshopImageManager::thumbnail(COM_JEPROSHOP_CATEGORY_IMAGE_DIR . '/' . $this->context->controller->category->category_id . '.jpg', 'category_' . (int)$this->context->controller->category->category_id . '.jpg', 350, 'jpg', true);
        $this->assignRef('image', ($image ? $image : false));
        $size =  $image ? filesize(COM_JEPROSHOP_CATEGORY_IMAGE_DIR . '/' . $this->context->controller->category->category_id . 'jpg') / 1000 : false;
        $this->assignRef('size', $size);

        $category_group_ids = $this->context->controller->category->getGroups();

        $groups = JeproshopGroupModelGroup::getGroups($this->context->language->lang_id);

        //if empty $carrier_groups_ids : object creation : we set the default groups
        if(empty($category_group_ids)){
            $preSelected = array(JeproshopSettingModelSetting::getValue('unidentified_group'), JeproshopSettingModelSetting::getValue('guest_group'), JeproshopSettingModelSetting::getValue('customer_group'));
            $category_group_ids = array_merge($category_group_ids, $preSelected);
        }

        foreach($groups as $group){
            $groupBox = $app->input->get('group_box_' . $group->group_id, (in_array($group->group_id, $category_group_ids)));
            $this->assignRef('group_box_' . $group->group_id, $groupBox);
        }
        $is_root_category = (bool)$app->input->get('is_root_category');
        $this->assignRef('is_root_category', $is_root_category);

        $helper = new JeproshopHelper();
        $this->assignRef('helper', $helper);
        $this->assignRef('groups', $groups);

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    private function addToolBar(){
        switch ($this->getLayout()){
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_ADD_NEW_CATEGORY_TITLE'), 'jeproshop-category');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_EDIT_CATEGORY_TITLE'), 'jeproshop-category');
                JToolBarHelper::apply('update', JText::_('COM_JEPROSHOP_UPDATE_LABEL'));
                break;
            default:
                JToolBarHelper::title(JText::_('COM_JEPROSHOP_CATEGORIES_LIST_TITLE'), 'jeproshop-category');
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

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $category_id = $app->input->get('category_id');
        if(!isset($this->context) || $this->context == null){ $this->context = JeproshopContext::getContext(); }
        $isLoaded = false;
        if($category_id && JeproshopTools::isUnsignedInt($category_id)){
            if(!$this->context->controller->category){
                $this->context->controller->category = new JeproshopCategoryModelCategory($category_id);
            }

            if(!JeproshopTools::isLoadedObject($this->context->controller->category, 'category_id')){
                JError::raiseError(500, JText::_('COM_JEPROSHOP_CATEGORY_NOT_FOUND_MESSAGE'));
                $isLoaded = false;
            }else{
                $isLoaded = true;
            }
        }elseif($option){
            if(!$this->context->controller->category){
                $this->context->controller->category = new JeproshopCategoryModelCategory();
            }
        }else{
            JError::raiseError(500, JText::_('COM_JEPROSHOP_CATEGORY_DOES_NOT_EXIST_MESSAGE'));
            $isLoaded = false;
        }
        return $isLoaded;
    }
}