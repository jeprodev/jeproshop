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

class JeproshopCustomerController extends JeproshopController
{
    public $can_add_customer = true;

	public function display($cachable = FALSE, $urlParams = FALSE) {
		parent::display();
	}

    public function initialize(){
        parent::initialize();

        // Check if we can add a customer
        if (JeproshopShopModelShop::isFeaturePublished() && (JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_ALL || JeproshopShopModelShop::getShopContext() == JeproshopShopModelShop::CONTEXT_GROUP)) {
            $this->can_add_customer = false;
        }
    }

	/*public function add(){
		$view = $this->input->get('view', 'customer');
		$layout = $this->input->get('layout', 'add');

		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->addCategory();
	}*/
	
	public function view(){
		//JSession::checkToken() or die('COM_JEPROSHOP_FORBIDDEN_AREA_MESSAGE');
		$view = $this->input->get('view', 'customer');
		$layout = $this->input->get('layout', 'view');
	
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewCustomer();
	}
	
	public function threads(){
		JeproshopTools::checkCustomerToken() or die('COM_JEPROSHOP_FORBIDDEN_AREA_MESSAGE');
		$view = $this->input->get('view', 'customer');
		$layout = $this->input->get('layout', 'threads');
	
		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewThreads();
	}

    function search(){
        // Get the document object.
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $searches = explode(' ', $app->input->get('content'));
        $customers = array();
        $searches = array_unique($searches);
        $document->setMimeEncoding('application/json');

        foreach($searches as $search){
            if(!empty($search)){
                $results = JeproshopCustomerModelCustomer::searchByName($search);
                if($results) {
                    foreach ($results as $result) {
                        $customers[$result->customer_id] = $result;
                    }
                }
            }
        }

        if(count($customers)){
            $jsonData = array("success" =>  true, 'found' => true); //'{'; // "success": "true", ';
            $customersArray = array();
            foreach($customers as $customer) {
                //$jsonData .= '"customer_' . $customer->customer_id . '": {';
                /*$toReturn .= '<div class="customer_card" ><div class="panel" ><div class="panel-title" >' . ucfirst($customer->firstname) . ' ' . ucfirst($customer->lastname) . '<span class="pull-right" ># ' . $customer->customer_id . '</span></div>';
                $toReturn .= '<span>' . $customer->email . '</span><br/><span class="text-muted">' . (($customer->birthday != '0000-00-00') ? $customer->birthday : '') . '</span><br/><div class="panel-footer"><a href="';
                $toReturn .= JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id=' . $customer->customer_id . '&lite_displaying=1') .'" class="btn btn-default fancybox"><i class="icon-search"></i>' . JText::_('COM_JEPROSHOP_DETAILS_LABEL');
                $toReturn .= '</a><button type="button" data-customer="'. $customer->customer_id . '" class="setup-customer btn btn-default pull-right"><i class="icon-arrow-right"></i>' . JText::_('COM_JEPROSHOP_CHOOSE_LABEL') . '</button></div></div></div>'; */
                $customerData = array();
                foreach($customer as $key => $value){
                    $customerData[$key] =  $value;
                }
                $customersArray[] = $customerData ;
            }
            $jsonData['customers'] = $customersArray ;
        }else{
            $jsonData = array("success" =>  false, 'found' => false);
        }


        // Set the MIME type for JSON output.
        //$document->setMimeEncoding('application/json');

        // Change the suggested filename.
        //JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
        //echo ($jsonData);
        echo json_encode($jsonData);
        $app->close();
    }
}