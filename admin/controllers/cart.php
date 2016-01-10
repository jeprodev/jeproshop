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

class JeproshopCartController extends JeproshopController
{
	public function rules(){
		$view = $this->input->get('view', 'cart');
		$layout = $this->input->get('layout', 'rules');
		
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewRules();
	}
	
	public function catalog_prices(){
		$view = $this->input->get('view', 'cart');
		$layout = $this->input->get('layout', 'catalog_prices');
	
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewCatalogPrices();
	}
	
	public function marketing(){
		$view = $this->input->get('view', 'cart');
		$layout = $this->input->get('layout', 'marketing');
	
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewMarketings();
	}	
}