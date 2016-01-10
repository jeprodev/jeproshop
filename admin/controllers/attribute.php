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

class JeproshopAttributeController extends JeproshopController
{
	public function display($cachable = FALSE, $urlParams = FALSE) {
		parent::display();
	}

    /*public function initialize(){

    }

    public function initContent(){
        if(!JeproshopCombinationModelCombination::isFeaturePublished()){
            JError::raiseError();
        }
    }*/

    public function edit_group(){
        if($this->viewAccess() && JeproshopTools::checkAttributeGroupToken()){
            $app = JFactory::getApplication();
            $view = $app->input->get('view');
            $viewClass = $this->getView($view, JFactory::getDocument()->getType());
            $viewClass->setLayout('edit_group');
            $viewClass->editGroup();
        }
    }

    public function view_group(){

    }

	public function add(){
		$view = $this->input->get('view', 'attribute');
		$layout = $this->input->get('layout', 'add');

		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->addAttribute();
	}
}