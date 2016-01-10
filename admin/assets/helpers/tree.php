<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package      com_jeproshop
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
/**** ----------------  TREE ---------- ***/
class JeproshopTree
{
    const DEFAULT_TEMPLATE  = 'tree';
    const DEFAULT_HEADER_TEMPLATE = 'tree_header';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item';

    protected $attributes;
    private   $context;
    protected $data;
    protected $header_template;
    private   $tree_id;
    protected $node_folder_template;
    protected $node_item_template;
    protected $template;
    private   $tree_title;
    private   $toolbar;

    public function __construct($id, $data = null){
        $this->setTreeId($id);

        if (isset($data)){ 	$this->setTreeData($data); }
    }

    /** ------ SETTERS -------- ***/

    /**
     * @param $value
     * @return $this
     */
    public function setTreeId($value){
        $this->tree_id = $value;
        return $this;
    }

    public function setTreeData($value){
        if (!is_array($value) && !$value instanceof Traversable){
            JError::raiseWarning(500, JText::_('Data value must be an traversable array'));
        }
        $this->data = $value;
        return $this;
    }

    public function setTreeTitle($value){
        $this->tree_title = $value;
        return $this;
    }

    public function setTreeLayout($value){
        $this->template = $value;
        return $this;
    }

    public function setTreeActions($value) {
        if (!isset($this->toolbar))
            $this->setTreeToolbar(new JeproshopTreeToolbar());

        $this->getToolbar()->setTreeActions($value);
        return $this;
    }

    public function setTreeAttribute($name, $value){
        if (!isset($this->attributes))
            $this->attributes = array();

        $this->attributes[$name] = $value;
        return $this;
    }

    public function setTreeAttributes($value){
        if (!is_array($value) && !$value instanceof Traversable)
            throw new JException('Data value must be an traversable array');

        $this->attributes = $value;
        return $this;
    }

    public function setContext($value){
        $this->context = $value;
        return $this;
    }

    public function setHeaderTemplate($value){
        $this->header_template = $value;
        return $this;
    }

    public function setNodeFolderTemplate($value){
        $this->node_folder_template = $value;
        return $this;
    }

    public function setNodeItemTemplate($value){
        $this->node_item_template = $value;
        return $this;
    }

    public function setTreeTemplate($value){
        $this->template = $value;
        return $this;
    }

    public function setTreeToolbar($value){
        if (!is_object($value))
            throw new JException('Toolbar must be a class object');

        $reflection = new ReflectionClass($value);

        if (!$reflection->implementsInterface('JeproshopTreeToolbarInterface'))
            throw new JException('Toolbar class must implements JeproshopTreeToolbarInterface interface');

        $this->toolbar = $value;
        return $this;
    }

    /*** GETTERS **/
    public function getContext(){
        if (!isset($this->context)){
            $this->context = JeproshopContext::getContext();
        }
        return $this->context;
    }

    public function getTreeId(){
        return $this->tree_id;
    }

    public function getTreeTitle(){
        return $this->tree_title;
    }

    public function getTreeToolbar(){
        if (isset($this->toolbar)){
            $this->toolbar->setTreeToolBarData($this->getTreeData());
        }
        return $this->toolbar;
    }

    public function getTreeData(){
        if (!isset($this->data))
            $this->data = array();

        return $this->data;
    }

    public function __toString(){
        return $this->render();
    }

    public function addAction($action){
        if (!isset($this->toolbar)){ $this->setTreeToolbar(new JeproshopTreeToolbar()); }

        $this->getTreeToolbar()->addTreeToolBarAction($action);
        return $this;
    }

    public function removeTreeActions(){
        if (!isset($this->toolbar))
            $this->setTreeToolbar(new JeproshopTreeToolbar());

        $this->getTreeToolbar()->removeTreeToolBarActions();
        return $this;
    }

    public function renderToolbar(){
        return $this->getTreeToolbar()->render();
    }

    public function useInput(){
        return isset($this->input_type);
    }

    public function useToolbar(){
        return isset($this->toolbar);
    }

    public function getInputName(){}

    public function getTreeTemplate() {
        if (!isset($this->template))
            $this->setTreeTemplate(self::DEFAULT_TEMPLATE);

        return $this->template;
    }

    public function getHeaderTemplate(){
        if (!isset($this->header_template)){
            $this->setHeaderTemplate(self::DEFAULT_HEADER_TEMPLATE);
        }
        return $this->header_template;
    }

    public function getNodeFolderTemplate(){
        if (!isset($this->node_folder_template))
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);

        return $this->node_folder_template;
    }

    public function getNodeItemTemplate(){
        if (!isset($this->node_item_template))
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);

        return $this->node_item_template;
    }

    public function renderNodes($data = null){
        if (!isset($data))
            $data = $this->getTreeData();

        if (!is_array($data) && !$data instanceof Traversable)
            throw new PrestaShopException('Data value must be an traversable array');

        $html = '';

        foreach ($data as $item){
            if (array_key_exists('children', $item)
                && !empty($item->children))
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'children' => $this->renderNodes($item['children']),
                    'node'     => $item
                ))->fetch();
            else
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'node' => $item
                ))->fetch();
        }

        return $html;
    }

    public function render($data = null){
        //$this->getContext()->controller->addJs('tree.js');
        $header_script = '';
        if (trim($this->getTreeTitle()) != '' || $this->useToolbar()){
            $title = $this->getTreeTitle();
            $toolbar = $this->useToolbar() ? $this->renderToolbar() : null;
            if($this->getHeaderTemplate() == 'tree_header'){
                $header_script .= '<div class="tree-panel-heading-controls panel-title clearfix" >';
                if(isset($title)){ $header_script .= '<i class="icon-tag"></i> ' . $title; }
                if(isset($toolbar)){ $header_script .= $toolbar; }
                $header_script .= '</div>';
            }
        }

        $nodes = $this->renderNodes($data);
        $content_script  = '';
        if($this->getTreeTemplate() == 'associated_categories'){
            $content_script = '<div class="panel">' . (isset($header_script) ? $header_script : '') . '<div class="panel-content well" >';
            if(isset($nodes)){
                $content_script .= '<ul id="jform_' . $this->getTreeId(). '" class="tree">' . $nodes . '</ul>';
            }
            $content_script .= '</div></div>';

            $script = 'jQuery(document).ready(function(){ '; //jQuery(\'#jform_' . $this->getTreeId() . '\').JeproshopTree(\'collapseAll\');
            $script .= 'jQuery(\'#jform_'. $this->getTreeId() . '\').find(\':input[type=radio]\').click(function(){ location.href = ';
            $script .= 'location.href.replace(/&category_id=[0-9]*/, \'\') + \'&category_id=\' + jQuery(this).val(); }); ';

            $script .= 'jQuery(\'#jform_' . $this->getTreeId() . '\').find(\':input[type=checkbox]\').click(function(){ ';
            $script .= 'if (jQuery(this).prop(\'checked\')){ jQuery(\'select#jform_default_category_id\').append(\'<option value="\' + ';
            $script .= 'jQuery(this).val() + \'">\' + (jQuery(this).val() !=1 ? jQuery(this).parent().find(\'label\').html() : home) + \'</option>\'); ';
            $script .= 'if (jQuery(\'select#jform_default_category_id option\').length > 0){ jQuery(\'select#jform_default_category_id\').closest(\'';
            $script .= '.control-group\').show(); jQuery(\'#jform_no_default_category\').hide(); } } else{	jQuery(\'select#jform_default_category_id option';
            $script .= '[value=\' + jQuery(this).val() + \']\').remove(); if (jQuery(\'select#jform_default_category option\').length == 0) {';
            $script .= ' jQuery(\'select#jform_default_category_id\').closest(\'.control-group\').hide();  jQuery(\'#jform_no_default_category\').show(); ';
            $script .= '}  } }); });';
            $document = JFactory::getDocument();
            JHtml::_('jquery.framework');
            $document->addScript(JURI::base(). 'components/com_jeproshop//assets/javascript/script/tree.js');
            $document->addScriptDeclaration($script);
        }elseif ($this->getTreeTemplate() == ''){

        }
        return (isset($html) ? $html : '') . $content_script;
    }
}


/**** ---------------- CATEGORY TREE ---------- ***/
class JeproshopCategoriesTree extends JeproshopTree
{
    const DEFAULT_TEMPLATE             = 'tree_categories';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_radio';

    private $disabled_categories;
    private $input_name;
    private $lang;
    private $root_category;
    private $selected_categories;
    private $shop;
    private $use_checkbox;
    private $use_search;
    private $use_shop_restriction;

    public function __construct($tree_id, $title = null, $root_category = null, $lang = null, $use_shop_restriction = true){
        parent::__construct($tree_id);

        if (isset($title)){ $this->setTreeTitle($title); }

        if (isset($root_category)){ $this->setRootCategory($root_category); }

        $this->setLang($lang);
        $this->setUseShopRestriction($use_shop_restriction);
    }

    /** ------ SETTERS -------- ***/
    /**
     * @param $value
     * @return $this
     */
    public function setLang($value){
        $this->lang = $value;
        return $this;
    }

    public function setRootCategory($value){
        if (!JeproshopTools::isInt($value)){
            JError::raiseWarning(500, JText::_('COM_JEPROSHOP_ROOT_CATEGORY_MUST_BE_AN_INTEGER_VALUE_MESSAGE'));
        }
        $this->root_category = $value;
        return $this;
    }

    public function setUseShopRestriction($value){
        $this->use_shop_restriction = (bool)$value;
        return $this;
    }

    public function setUseCheckBox($value){
        $this->use_checkbox = (bool)$value;
        return $this;
    }

    public function setUseSearch($value){
        $this->use_search = (bool)$value;
        return $this;
    }

    public function setSelectedCategories($value){
        if (!is_array($value))
            throw new JException('Selected categories value must be an array');

        $this->selected_categories = $value;
        return $this;
    }

    public function setInputName($value) {
        $this->input_name = $value;
        return $this;
    }

    public function setDisabledCategories($value){
        $this->disabled_categories = $value;
        return $this;
    }

    /** ------ GETTERS -------- ***/
    public function getRootCategory(){
        return $this->root_category;
    }

    public function useShopRestriction(){
        return (isset($this->use_shop_restriction) && $this->use_shop_restriction);
    }

    public function getData(){
        if(!isset($this->data)){
            $this->setTreeData(JeproshopCategoryModelCategory::getNestedCategories(
                $this->getRootCategory(), $this->getLang(), false, null, $this->useShopRestriction()));
        }
        return $this->data;
    }

    public function getLang(){
        if (!isset($this->lang)){
            $this->setLang($this->getContext()->employee->lang_id);
        }
        return $this->lang;
    }

    private function getSelectedChildNumbers(&$categories, $selected, &$parent = null){
        $selected_children = 0;

        foreach ($categories as $key => &$category)	{
            if (isset($parent) && in_array($category->category_id, $selected)){	$selected_children++; }

            if (isset($category->children) && !empty($category->hildren))
                $selected_children += $this->getSelectedChildNumbers($category->children, $selected, $category);
        }

        if(!isset($parent)){ $parent = new  JeproshopCategoryModelCategory(); }
        if (!isset($parent->selected_childs))
            $parent->selected_childs = 0;

        $parent->selected_childs = $selected_children;
        return $selected_children;
    }

    public function getSelectedCategories(){
        if (!isset($this->selected_categories))
            $this->selected_categories = array();

        return $this->selected_categories;
    }

    public function getDisabledCategories(){
        return $this->disabled_categories;
    }

    public function useSearch(){
        return (isset($this->use_search) && $this->use_search);
    }

    public function getTreeTemplate(){
        if (!isset($this->template))
            $this->setTreeTemplate(self::DEFAULT_TEMPLATE);

        return $this->template;
    }

    public function useCheckBox(){
        return (isset($this->use_checkbox) && $this->use_checkbox);
    }

    private function disableCategories(&$categories, $disabled_categories = null){
        foreach ($categories as &$category){
            if (!isset($disabled_categories) || in_array($category->category_id, $disabled_categories)){
                $category->disabled = true;
                if (array_key_exists('children', $category) && is_array($category->children))
                    self::disableCategories($category->children);
            }
            else if (array_key_exists('children', $category) && is_array($category->children))
                self::disableCategories($category->children, $disabled_categories);
        }
    }

    public function getNodeFolderTemplate(){
        if (!isset($this->node_folder_template))
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);

        return $this->node_folder_template;
    }

    public function getInputName(){
        if (!isset($this->input_name))
            $this->setInputName('category_box');

        return $this->input_name;
    }

    public function renderNodes($data = null){
        $table = JFactory::getApplication()->input->get('view');
        if (!isset($data)){
            $data = $this->getData();
        }

        if (!is_array($data) && !$data instanceof Traversable)
            throw new JException('Data value must be an traversable array');

        $html = '';
        $root_category = JeproshopSettingModelSetting::getValue('root_category');
        foreach ($data as $item){
            $input_name = $this->getInputName();

            if (array_key_exists('children', $item) && !empty($item->children)) {
                $template_node_folder = $this->getNodeFolderTemplate();
                if($template_node_folder == 'tree_node_folder'){
                    $html .= '<li class="tree-folder" ><span class="tree-folder-name" > <i class="icon-folder-close"></i> <label class="tree-toggler" >';
                    $html .= $item->name . '</label> </span> <ul class="tree">' .  $this->renderNodes($item->children) . '</ul></li>';
                }elseif($template_node_folder == 'tree_node_folder_checkbox'){
                    $html .= '<li class="tree-folder" ><p class="checkbox tree-folder-name';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-folder-name-disable'; }
                    $html .= '"  >';
                    if($item->category_id != $root_category) {
                        $html .= '<input type="checkbox" name="jform[' . $input_name . '[]]" value="' . $item->category_id . '"';
                        if(isset($item->disabled) && $item->disabled == true){ $html .= 'disabled="disabled"'; }
                        $html .= '/>';
                    }
                    $html .= '<i class="icon-folder-close pull-left" ></i> <label class="tree-toggler" >' . $item->name;
                    if(isset($item->selected_children) && (int)$item->selected_childs > 0){
                        $html .= $item->selected_childs . JText::_('COM_JEPROSHOP_SELECTED_LABEL');
                    }
                    $html .= '</label></p><ul class="tree">' . $this->renderNodes($item->children) . '</ul></li>';
                }elseif($template_node_folder == 'tree_node_folder_checkbox_shops'){
                    $html .= '<li class="tree-folder"><span class="tree-folder-name';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-folder-name-disable'; }
                    $html .= '" ><input type="checkbox" name="jform[check_box_shop_group_associated_' . $table . '[' . $item->{$table .'_id'} . ']]" value="';
                    $html .= $item->{$table .'_id'} . '"';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' disabled="disabled"'; }
                    $html .= ' /><i class="icon-folder-close" ></i> <label class="tree-toggler" >' . JText::_('COM_JEPROSHOP_GROUP_LABEL') . $item->name;
                    $html .= '</label> </span><ul class="tree" >' . $this->renderNodes($item->children) . '</ul></li>';
                }elseif($template_node_folder == 'tree_node_folder_radio'){
                    $html .= '<li class="tree-folder"><span class="tree-folder-name';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-folder-name-disable'; }
                    $html .= ' "> ';
                    if($item->category_id != $root_category) {
                        $html .= '<input type="radio" name="jform[' . $input_name . ']" value="' . $item->category_id . '"';
                        if (isset($item->disabled) && $item->disabled == true) {
                            $html .= ' disabled="disabled"';
                        }
                        $html .= ' />';
                    }
                    $html .= '<i class="icon-folder-close"></i> <label class="tree-toggler">' . $item->name . '</label>	</span>';
                    $html .= '<ul class="tree" >' . $this->renderNodes($item->children) . '</ul></li>';
                }
            }else {
                $template_node_item = $this->getNodeItemTemplate();
                if($template_node_item == 'tree_node_item'){
                    $html .= '<li class="tree-item" ><label class="tree-item-name" ><i class="tree-dot"></i> ' . $item->name . '</label></li>';
                }elseif($template_node_item == 'tree_node_item_checkbox_shops'){
                    $html .= '<li class="tree-item' ;
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-item-disable';  }
                    $html .= '" ><label class="tree-item-name"> <input type="checkbox" name="jform[check_box_shop_associated_' . $table .'[' . $item->shop_id;
                    $html .= ']]" value="' . $item->shop_id . '" ';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' disabled="disabled"' ; }
                    $html .= '/> <i class="tree-dot"></i>' .$item->name . '</label></li>';
                }elseif($template_node_item == 'tree_node_item_radio'){
                    $html .= '<li class="tree-item';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-item-disable'; }
                    $html .= ' "> <label class="tree-item-name" ><input type="radio" name="jform[' . $input_name . ']" value="' . $item->category_id . '" ';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' disabled="disabled"'; }
                    $html .= ' /><i class="tree-dot"></i> ' . $item->name . '</label></li>';
                }elseif($template_node_item == 'tree_node_item_checkbox'){
                    $html .= '<li class="tree-item';
                    if(isset($item->disabled) && $item->disabled == true){  $html .= 'tree-item-disable';  }
                    $html .= '" ><p class="checkbox" ><span class="tree-item-name';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' tree-item-name-disable' ; }
                    $html .= '" > <input type="checkbox" name="jform[' . $input_name . '[]]" value="' . $item->category_id . '"';
                    if(isset($item->disabled) && $item->disabled == true){ $html .= ' disabled="disabled" '; }
                    $html .= '/> <i class="tree-dot"></i> <label class="tree-toggler">' . $item->name . '</label></span></p></li>';
                }
            }
        }

        return $html;
    }

    /**
     * @param null $data
     * @return string
     */
    public function render($data = NULL){
        if (!isset($data)){ $data = $this->getData(); }

        if (isset($this->disabled_categories) && !empty($this->disabled_categories))
            $this->setDisableCategories($data, $this->getDisabledCategories());

        if (isset($this->selected_categories) && !empty($this->selected_categories))
            $this->getSelectedChildNumbers($data, $this->getSelectedCategories());

        //Default bootstrap style of search is push-right, so we add this button first
        if ($this->useSearch()){
            $this->addAction(new JeproshopTreeToolbarSearchCategories(
                    JText::_('COM_JEPROSHOP_FIND_A_CATEGORY_LABEL'), $this->getTreeId().'_categories_search')
            );
            $this->setTreeAttribute('use_search', $this->useSearch());
        }

        $collapseAll = new JeproshopTreeToolBarLink(JText::_('COM_JEPROSHOP_COLLAPSE_ALL_LABEL'), '#',
            'jQuery(\'#jform_'.$this->getTreeId().'\').JeproshopTree(\'collapseAll\');jQuery(\'#jform_collapse_all_'.$this->getTreeId().'\').hide();jQuery(\'#jform_expand_all_'.$this->getTreeId().'\').show(); return false;',
            'icon-collapse-alt');
        $collapseAll->setTreeToolBarAttribute('id', 'collapse_all_'.$this->getTreeId());
        $expandAll = new JeproshopTreeToolBarLink(JText::_('COM_JEPROSHOP_EXPAND_ALL_LABEL'), '#',
            'jQuery(\'#jform_'.$this->getTreeId().'\').JeproshopTree(\'expandAll\');jQuery(\'#jform_collapse_all_' . $this->getTreeId().'\').show();jQuery(\'#jform_expand_all_' . $this->getTreeId().'\').hide(); return false;',
            'icon-expand-alt');
        $expandAll->setTreeToolBarAttribute('id', 'expand_all_' . $this->getTreeId());
        $this->addAction($collapseAll);
        $this->addAction($expandAll);

        if ($this->useCheckBox()){
            $checkAll = new JeproshopTreeToolBarLink(JText::_('COM_JEPROSHOP_CHECK_ALL_LABEL'), '#', 'checkAllAssociatedCategories(jQuery(\'#jform_' . $this->getTreeId().'\')); return false;', 'icon-check-sign');
            $checkAll->setTreeToolBarAttribute('id', 'check_all_'.$this->getTreeId());
            $unCheckAll = new JeproshopTreeToolBarLink(JText::_('COM_JEPROSHOP_UN_CHECK_ALL_LABEL'), '#', 'unCheckAllAssociatedCategories(jQuery(\'#jform_'.$this->getTreeId().'\')); return false;', 'icon-check-empty');
            $unCheckAll->setTreeToolBarAttribute('id', 'uncheck_all_'.$this->getTreeId());
            $this->addAction($checkAll);
            $this->addAction($unCheckAll);
            $this->setNodeFolderTemplate('tree_node_folder_checkbox');
            $this->setNodeItemTemplate('tree_node_item_checkbox');
            $this->setTreeAttribute('use_checkbox', $this->useCheckBox());
        }

        $this->setTreeAttribute('selected_categories', $this->getSelectedCategories());
        if($this->getTreeTemplate() == 'associated_categories'){
            $use_checkbox = $this->useCheckBox();
            $javascript = '';
            if(isset($use_checkbox) && $use_checkbox== true){
                $javascript .= 'function checkAllAssociatedCategories($tree){ $tree.find(\':input[type=checkbox]\').each(function(){ ';
                $javascript .= ' jQuery(this).prop(\'checked\', true); jQuery(\'select#jform_default_category_id\').append(\'<option value="\' + ';
                $javascript .= ' jQuery(this).val()+ \'" >\'+(jQuery(this).val() !=1 ? jQuery(this).parent().find(\'label\').html() : home)+\'</option>\'); ';
                $javascript .= ' if(jQuery(\'select#jform_default_category_id option\').length > 0){ jQuery(\'select#jorm_default_category_id\').';
                $javascript .= 'closest(\'.control-group\').show(); jQuery(\'#jform_no_default_category\').hide(); } jQuery(this).parent().addClass(\'tree-selected\');';
                $javascript .= ' }); } function unCheckAllAssociatedCategories($tree){ $tree.find(\':input[type=checkbox]\').each(function(){';
                $javascript .= ' jQuery(this).prop(\'checked\', false); 	jQuery(\'select#jform_default_category_id option[value=\'+jQuery(this).val()+\']\').remove(); ';
                $javascript .= ' if(jQuery(\'select#jform_de_category_default option\').length == 0){ jQuery(\'select#jform_default_category_id\').closest(\'.control-group\').hide(); ';
                $javascript .= 'jQuery(\'#jform_no_default_category\').show(); }  jQuery(this).parent().removeClass(\'tree_selected\'); });	} ';
            }
            $use_search = $this->useSearch();
            if(isset($use_search) && $use_search == true){
                $javascript .= ' jQuery(\'#jform_' . $this->getTreeId() . '_categories_search\').bind(\'typeahead:selected\', function(obj, datum){
						jQuery(\'#jform_' . $this->getTreeId() . '\').find(\':input\').each(function(){
						if (jQuery(this).val() == datum.category_id){ jQuery(this).prop("checked", true); jQuery(this).parent().addClass("tree_selected");
						jQuery(this).parents(\'ul.tree\').each(function(){ jQuery(this).show();
						jQuery(this).prev().find(\'.icon-folder-close\').removeClass(\'icon-folder-close\').addClass(\'icon-folder-open\');
						});	} } ); }); ';
            }

            if(isset($selected_categories)){
                $javascript .= 'jQuery(\'#jform_no_default_category\').hide(); ';
                $imploded_selected_categories = implode(',', $selected_categories);
                $javascript .= ' var selected_categories = new Array("' . $imploded_selected_categories . '");
					if (selected_categories.length > 1){
						jQuery(\'#jform_expand_all_' . $this->getTreeId() . '\').hide();
					}else{
						jQuery(\'#jform_collapse_all_' . $this->getTreeId() . '\').hide();
					}
					jQuery(\'#jform_' . $this->getTreeId() . '\').find(\':input\').each(function(){
						if ($.inArray(jQuery(this).val(), selected_categories) != -1){
							if ($.inArray(jQuery(this).val(), selected_categories) != -1){
								jQuery(this).prop("checked", true);
								jQuery(this).parent().addClass("tree-selected");
								jQuery(this).parents(\'ul.tree\').each(function(){
									jQuery(this).show();
									jQuery(this).prev().find(\'.icon-folder-close\').removeClass(\'icon-folder-close\').addClass(\'icon-folder-open\');
								});
							}
						}
					}); ';
            }else{
                $javascript .= 'jQuery(\'#jform_collapse_all_' . $this->getTreeId() . '\').hide(); ';
            }
            JFactory::getDocument()->addScriptDeclaration($javascript);
        }

        return parent::render($data);
    }
}


class JeproshopTreeToolBarLink extends JeproshopTreeToolbarButton implements JeproshopTreeToolbarButtonInterface
{
    private $action;
    private $icon_class;
    private $link;
    protected $template = 'tree_toolbar_link';

    public function __construct($label, $link, $action = null, $iconClass = null){
        parent::__construct($label);

        $this->setTreeToolBarLink($link);
        $this->setTreeToolBarAction($action);
        $this->setTreeToolBarIconClass($iconClass);
    }

    public function setTreeToolBarIconClass($value){
        return $this->setTreeToolBarAttribute('icon_class', $value);
    }

    public function setTreeToolBarLink($value){
        return $this->setTreeToolBarAttribute('link', $value);
    }

    public function setTreeToolBarAction($value){
        return $this->setTreeToolBarAttribute('action', $value);
    }

    public function getTreeToolBarIconClass(){
        return $this->getTreeToolBarAttribute('icon_class');
    }

    public function getTreeToolBarLink(){
        return $this->getTreeToolBarAttribute('link');
    }

    public function getTreeToolBarAction(){
        return $this->getTreeToolBarAttribute('action');
    }

    public function render(){
        //$action = $this->getAction();
        $id = $this->getTreeToolBarId();
        $icon_class = $this->getTreeToolBarClass();

        if($this->getTreeToolBarTemplate() == 'tree_toolbar_link'){
            $script = '<a href="' . $this->getTreeToolBarLink() . '" ';
            if(isset($action)){ $script .= 'onclick="' . $action . '"'; }

            if(isset($id)){ $script .= ' id="jform_' . $id . '"'; };
            $script .= ' class="btn btn-middle " > ';
            if(isset($icon_class)){ $script .= '<i class="' . $icon_class . '" ></i> '; }
            $script .= $this->getTreeToolBarLabel() . '</a>';
            return $script;
        }
    }
}


abstract class JeproshopTreeToolbarButton
{
    protected $attributes;
    private   $tool_bar_class;
    private   $context;
    private   $tool_bar_id;
    private   $tool_bar_label;
    private   $tool_bar_name;
    protected $template = 'tree_toolbar_link';
    protected $template_directory;

    public function __construct($label, $id = null, $name = null, $class = null){
        $this->setTreeToolBarLabel($label);
        $this->setTreeToolBarId($id);
        $this->setTreeToolBarName($name);
        $this->setTreeToolBarClass($class);
    }

    public function __toString(){
        return $this->render();
    }

    public function setTreeToolBarAttribute($name, $value){
        if (!isset($this->attributes))
            $this->attributes = array();

        $this->attributes[$name] = $value;
        return $this;
    }

    public function getTreeToolBarAttribute($name){
        return $this->hasAttribute($name) ? $this->attributes[$name] : null;
    }

    public function setTreeToolBarAttributes($value){
        if (!is_array($value) && !$value instanceof Traversable)
            throw new JException(JText::_('COM_JEPROSHOP_DATA_VALUE_MUST_BE_AN_TRANSVERSABLE_ARRAY_MESSAGE'));

        $this->attributes = $value;
        return $this;
    }

    public function getTreeToolBarAttributes(){
        if (!isset($this->attributes))
            $this->attributes = array();

        return $this->attributes;
    }

    public function setTreeToolBarClass($value){
        return $this->setTreeToolBarAttribute('class', $value);
    }

    public function getTreeToolBarClass(){
        return $this->getTreeToolBarAttribute('class');
    }

    public function setContext($value){
        $this->context = $value;
        return $this;
    }

    public function getContext(){
        if (!isset($this->context))
            $this->context = JeproshopContext::getContext();

        return $this->context;
    }

    public function setTreeToolBarId($value){
        return $this->setTreeToolBarAttribute('tool_bar_id', $value);
    }

    public function getTreeToolBarId(){
        return $this->getTreeToolBarAttribute('tool_bar_id');
    }

    public function setTreeToolBarLabel($value){
        return $this->setTreeToolBarAttribute('tool_bar_label', $value);
    }

    public function getTreeToolBarLabel(){
        return $this->getTreeToolBarAttribute('tool_bar_label');
    }

    public function setTreeToolBarName($value){
        return $this->setTreeToolBarAttribute('tool_bar_name', $value);
    }

    public function getTreeToolBarName(){
        return $this->getTreeToolBarAttribute('tool_bar_name');
    }

    public function setTreeToolBarTemplate($value){
        $this->template = $value;
        return $this;
    }

    public function getTreeToolBarTemplate(){
        return $this->template;
    }

    public function hasAttribute($name){
        return (isset($this->attributes)
            && array_key_exists($name, $this->attributes));
    }

    public function render(){
        return '';
    }
}


class JeproshopTreeToolBar implements JeproshopTreeToolBarInterface
{
    const DEFAULT_TEMPLATE  = 'tree_toolbar';

    private $actions;
    private $context;
    private $data;
    private $template;

    public function __toString(){
        return $this->render();
    }

    public function setTreeToolBarActions($actions){
        if (!is_array($actions) && !$actions instanceof Traversable)
            throw new JException('Action value must be an traversable array');

        foreach($actions as $action){ $this->addTreeToolBarAction($action); }
    }

    public function getTreeToolBarActions(){
        if (!isset($this->actions))
            $this->actions = array();

        return $this->actions;
    }

    public function setContext($value) {
        $this->context = $value;
        return $this;
    }

    public function getContext(){
        if (!isset($this->context)){
            $this->context = JeproshopContext::getContext();
        }
        return $this->context;
    }

    public function setTreeToolBarData($value){
        if (!is_array($value) && !$value instanceof Traversable)
            throw new JException('Data value must be an traversable array');

        $this->data = $value;
        return $this;
    }

    public function getTreeToolBarData(){
        return $this->data;
    }

    public function setTreeToolBarTemplate($value){
        $this->template = $value;
        return $this;
    }

    public function getTreeToolBarTemplate(){
        if (!isset($this->template))
            $this->setTreeToolBarTemplate(self::DEFAULT_TEMPLATE);

        return $this->template;
    }

    public function addTreeToolBarAction($action){
        if (!is_object($action))
            throw new JException('Action must be a class object');

        $reflection = new ReflectionClass($action);

        if (!$reflection->implementsInterface('JeproshopTreeToolbarButtonInterface'))
            throw new JException('Action class must implements ITreeToolbarButtonCore interface');

        if (!isset($this->actions))
            $this->actions = array();

        $this->actions[] = $action;
        return $this;
    }

    public function removeTreeToolBarActions(){
        $this->actions = null;
        return $this;
    }

    public function render(){
        $script = '';
        $actions = $this->getTreeToolBarActions();
        if($this->getTreeToolBarTemplate() == 'tree_toolbar'){
            $script .= '<div class="tree-actions pull-right">';
        }
        if(isset($actions)){
            foreach ($actions as $action){
                $action->setTreeToolBarAttribute('data', $this->getTreeToolBarData());
                $script .= $action->render();
            }
        }
        $script .= '</div>';
        return $script;
    }
}


class JeproshopTreeToolBarSearchCategories extends JeproshopTreeToolBarButton implements JeproshopTreeToolbarButtonInterface
{
    protected $template = 'tree_toolbar_search';

    public function __construct($label, $id, $name = null, $class = null){
        parent::__construct($label);
        $this->setTreeToolBarName($name);
        $this->setTreeToolBarId($id);
        $this->setTreeToolBarClass($class);
    }

    public function render(){
        if($this->hasAttribute('data')){
            $this->setTreeToolBarAttribute('typehead_source', $this->renderData($this->getTreeToolBarAttribute('data')));
        }

        return (isset($html) ? $html : '') . parent::render();
    }

    private function _renderData($data){
        if(!is_array($data)  && !$data instanceof Traversable){
            throw new JException(JText::_('COM_JEPROSHOP_DATA_MUST_BE_A_TRAVERSABLE_ARRAY_MESSAGE'));
        }

        $html = '';
        foreach($data as $item){
            $html .= JeproshopTools::jsonEncode($item) . ', ';
            if(array_key_exists('children', $item) && !empty($item->children)){ $html .= $this->renderData($item->children); }
        }
        return $html;
    }
}


class JeproshopShopsTree extends JeproshopTree
{
    const DEFAULT_TEMPLATE             = 'tree_shops';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_checkbox_shops';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_checkbox_shops';

    private $lang;
    private $selected_shops;

    public function __construct($shop_tree_id, $title = null, $lang = null){
        parent::__construct($shop_tree_id);
        if(isset($title)){
            $this->setTreeTitle($title);
        }
        $this->setLang($lang);
    }

    public function setLang($value){
        $this->lang = $value;
        return $this;
    }

    public function getTreeData(){
        if(!isset($this->data)){
            $this->setData(JeproshopShopModelShop::getShopTree());
        }
        return $this->data;
    }

    public function getLang(){
        if (!isset($this->lang))
            $this->setLang($this->getContext()->employee->lang_id);

        return $this->lang;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->node_folder_template))
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);

        return $this->node_folder_template;
    }

    public function getNodeItemTemplate(){
        if (!isset($this->node_item_template))
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);

        return $this->node_item_template;
    }

    public function setSelectedShops($value){
        if (!is_array($value))
            throw new JException('Selected shops value must be an array');

        $this->selected_shops = $value;
        return $this;
    }

    public function getSelectedShops(){
        if (!isset($this->selected_shops))
            $this->selected_shops = array();

        return $this->selected_shops;
    }

    public function getTreeTemplate(){
        if (!isset($this->template))
            $this->setTreeTemplate(self::DEFAULT_TEMPLATE);

        return $this->template;
    }

    public function render($data = null, $use_default_actions = true, $use_selected_shop = true)
    {
        if (!isset($data))
            $data = $this->getData();

        if ($use_default_actions)
            $this->setActions(array(
                    new TreeToolbarLink(
                        'Collapse All',
                        '#',
                        '$(\'#'.$this->getTreeId().'\').tree(\'collapseAll\'); return false;',
                        'icon-collapse-alt'),
                    new TreeToolbarLink(
                        'Expand All',
                        '#',
                        '$(\'#'.$this->getTreeId().'\').tree(\'expandAll\'); return false;',
                        'icon-expand-alt'),
                    new TreeToolbarLink(
                        'Check All',
                        '#',
                        'checkAllAssociatedShops($(\'#'.$this->getTreeId().'\')); return false;',
                        'icon-check-sign'),
                    new TreeToolbarLink(
                        'Uncheck All',
                        '#',
                        'uncheckAllAssociatedShops($(\'#'.$this->getTreeId().'\')); return false;',
                        'icon-check-empty')
                )
            );

        if ($use_selected_shop)
            $this->setAttribute('selected_shops', $this->getSelectedShops());

        return parent::render($data);
    }

    public function renderNodes($data = null)
    {
        if (!isset($data))
            $data = $this->getData();

        if (!is_array($data) && !$data instanceof Traversable)
            throw new PrestaShopException('Data value must be an traversable array');

        $html = '';

        foreach ($data as $item)
        {
            if (array_key_exists('shops', $item) && !empty($item['shops'])){
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile(),
                    $this->getContext()->smarty
                )->assign($this->getTreeAttributes())->assign(array(
                    'children' => $this->renderNodes($item['shops']),
                    'node'     => $item
                ))->fetch();
                $nodeTemplate = $this->getNodeFolderTemplate();
                if($nodeTemplate == 'tree_node_folder_checkbox_shops'){

                }
            }else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign($this->getTreeAttributes())->assign(array(
                    'node' => $item
                ))->fetch();
                $itemTemplate = $this->getNodeItemTemplate();
                if($itemTemplate == 'tree_node_item_checkbox_shops'){

                }
            }
        }

        return $html;
    }
}


interface JeproshopTreeToolBarInterface
{
    public function __toString();
    public function setTreeToolBarActions($value);
    public function getTreeToolBarActions();
    public function setContext($value);
    public function getContext();
    public function setTreeToolBarData($value);
    public function getTreeToolBarData();
    public function setTreeToolBarTemplate($value);
    public function getTreeToolBarTemplate();
    public function addTreeToolBarAction($action);
    public function removeTreeToolBarActions();
    public function render();
}


interface JeproshopTreeToolbarButtonInterface
{
    public function __toString();
    public function setTreeToolBarAttribute($name, $value);
    public function getTreeToolBarAttribute($name);
    public function setTreeToolBarAttributes($value);
    public function getTreeToolBarAttributes();
    public function setTreeToolBarClass($value);
    public function getTreeToolBarClass();
    public function setContext($value);
    public function getContext();
    public function setTreeToolBarId($value);
    public function getTreeToolBarId();
    public function setTreeToolBarLabel($value);
    public function getTreeToolBarLabel();
    public function setTreeToolBarName($value);
    public function getTreeToolBarName();
    public function setTreeToolBarTemplate($value);
    public function getTreeToolBarTemplate();
    public function hasAttribute($name);
    public function render();
}