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

class JeproshopCountryController extends JeproshopController
{
	public function add(){
		JSession::checkToken() or die('Invalid token');
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'add');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->addCountry();
	}
	
	public function edit(){
		JSession::checkToken('get') or die('Invalid token');
		 
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'edit');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->editCountry();
	}
	
	public function save(){
		JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->saveCountry();
	}
	
	public function update(){
        if($this->viewAccess() && JeproshopTools::checkCountryToken()){
            if($this->has_errors){
                return false;
            }
            $input = JRequest::get('post');
            $country_id = $input['country_id'];
            $app = JFactory::getApplication();
            $countryModel = new JeproshopCountryModelCountry();
            if($countryModel->updateCountry()){
                $app->enqueueMessage(JText::_('COM_JEPROSHOP_COUNTRY_HAS_BEEN_SUCCESSFULLY_UPDATED_MESSAGE'));
                $app->redirect('index.php?option=com_jeproshop&view=country&task=edit&country_id=' . (int)$country_id . '&' . JeproshopTools::getCountryToken() . '&=1');
            }
        }
	}
	
	public function zone(){
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'zones');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewZones();
	}
	
	public function add_zone(){
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'add_zone');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->renderAddZone();
	}

    public function edit_zone(){
        $view = $this->input->get('view', 'country');
        $layout = $this->input->get('layout', 'edit_zone');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderEditZone();
    }

    public function add_state(){
        $view = $this->input->get('view', 'country');
        $layout = $this->input->get('layout', 'add_state');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderAddState();
    }

    public function edit_state(){
        $view = $this->input->get('view', 'country');
        $layout = $this->input->get('layout', 'edit_state');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderEditState();
    }
	
	public function save_zone(){
        if($this->viewAccess() && JeproshopTools::checkCountryToken()){
            if($this->has_errors){
                return false;
            }
            $zoneModel = new JeproshopZoneModelZone();
            $zoneModel->saveZone();
        }
	}
	
	public function save_add_zone(){
		//JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->saveAddZone();
	}
	
	public function save2new_zone(){
		//JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->save2NewZone();
	}	
	
	public function states(){
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'states');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewStates();
	}
	/*
	public function add_state(){
		$view = $this->input->get('view', 'country');
		$layout = $this->input->get('layout', 'add_zone');
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setModel($this->getModel('country'), TRUE);
		$viewClass->setLayout($layout);
		$viewClass->addState();
	}*/
	
	public function save_state(){
		//JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->saveState();
	}
	
	public function save_add_state(){
		JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->saveAddState();
	}
	
	public function save2new_state(){
		JSession::checkToken() or die('Invalid token');
		$countryModel = $this->getModel('country');
		$countryModel->save2NewState();
	}
}