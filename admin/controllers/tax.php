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

class JeproshopTaxController extends JeproshopController
{
    public function display($cachable = FALSE, $urlParams = FALSE){
        $this->initContent();
    }

	public function rules(){
		$view = $this->input->get('view', 'tax');
		$layout = $this->input->get('layout', 'rules');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		//$viewClass->setModel($this->getModel('tax'), TRUE);
		$viewClass->setLayout($layout);
		$viewClass->viewRules();
	}

    public function add_rule(){
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'tax');
        $layout = $app->input->get('layout', 'add_rule');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        //$viewClass->setModel($this->getModel('tax'), TRUE);
        $viewClass->setLayout($layout);
        $viewClass->renderAddRule();
    }

    public function save_rule(){
        if($this->viewAccess()  && JeproshopTools::checkTaxToken()){
            $app = JFactory::getApplication();
            if($app->input->get('tax_rules_group_id', 0)){
                $tr = new JeproshopTaxRuleModelTaxRule();
                $tr->add();
            }
        }
    }

    public function add_rules_group(){
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'tax');
        $layout = $app->input->get('layout', 'add_rules_group');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        //$viewClass->setModel($this->getModel('tax'), TRUE);
        $viewClass->setLayout($layout);
        $viewClass->renderAddRulesGroup();
    }

    public function edit_rules_group(){
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'tax');
        $layout = $app->input->get('layout', 'edit_rules_group');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        //$viewClass->setModel($this->getModel('tax'), TRUE);
        $viewClass->setLayout($layout);
        $viewClass->renderEditRulesGroup();
    }

    public function save_rules_group(){
        if($this->viewAccess() && JeproshopTools::checkTaxToken()){
            if(!$this->has_errors){
                $taxRuleGroupModel = new JeproshopTaxRulesGroupModelTaxRulesGroup();
                $taxRuleGroupModel->save();
            }
        }
    }

    public function update_rules_group(){
        if($this->viewAccess() && JeproshopTools::checkTaxToken()){
            $app = JFactory::getApplication();
            $tax_rules_group_id = $app->input->get('tax_rules_group_id');
            if(isset($tax_rules_group_id) && (!empty($tax_rules_group_id))){
                $tax_rules_group = new JeproshopTaxRulesGroupModelTaxRulesGroup($tax_rules_group_id);
                if(JeproshopTools::isLoadedObject($tax_rules_group, 'tax_rules_group_id')){
                    $tax_rules_group->update();
                }
            }
        }
    }

    public function edit_rule(){
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'tax');
        $layout = $app->input->get('layout', 'edit_rule');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        //$viewClass->setModel($this->getModel('tax'), TRUE);
        $viewClass->setLayout($layout);
        $viewClass->renderEditRule();
    }

    public function rule_group(){
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'tax');
        $layout = $app->input->get('layout', 'groups');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        //$viewClass->setModel($this->getModel('tax'), TRUE);
        $viewClass->setLayout($layout);
        $viewClass->renderRuleGroup();
    }

    public function save(){
        if($this->viewAccess() && JeproshopTools::checkTaxToken()){
            if($this->has_errors){
                return false;
            }
            $taxModel = new JeproshopTaxModelTax();
            if(!$taxModel->saveTax()){
                $this->has_errors = true;
                JFactory::getApplication()->enqueueMessage(JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_CREATING_A_TAX_OBJECT_MESSAGE'));
            }else{
                JFactory::getApplication()->redirect('index.php?option=com_jeproshop&view=tax&task=edit&tax_id=' . (int)$taxModel->tax_id . '&' . JeproshopTools::getTaxToken() . '=1');
            }
        }

    }

    public function update(){
        if($this->viewAccess() && JeproshopTools::checkTaxToken()){
            $app = JFactory::getApplication();
            $tax_id = $app->input->get('tax_id');
            if(isset($tax_id) && $tax_id > 0) {
                $taxModel = new JeproshopTaxModelTax($tax_id);
                if($taxModel->updateTax()) {
                    $link = 'index.php?option=com_jeproshop&view=tax';
                }else{
                    $link = 'index.php?option=com_jeproshop&view=tax&task=edit&tax_id=' . (int)$tax_id . '&' . JeproshopTools::getTaxToken() . '=1';
                    JError::raiseError(500, JText::_('COM_JEPROSHOP_WE_ENCOUNTER_AN_ERROR_WHILE_UPDATING_TAX_MESSAGE'));
                }
                $app->redirect($link);
            }
        }
    }
}