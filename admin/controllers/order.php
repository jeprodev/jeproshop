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

class JeproshopOrderController extends JeproshopController
{
	public function display($cachable = FALSE, $urlParams = FALSE) {
		
		parent::display();
	}

	public function view(){
		$view = $this->input->get('view', 'order');
		$layout = $this->input->get('layout', 'order');

		$viewClass = $this->getView($view, JFactory::getDocument()->getType());
		$viewClass->setLayout($layout);
		$viewClass->viewOrder();
	}

    public function status(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'status');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderStatusList();
    }

    public function refund(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'refunds');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderRefundsList();
    }

    public function invoices(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'invoices');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderInvoicesList();
    }

    public function delivery(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'deliveries');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderDeliveriesList();
    }

    public function returns(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'returns');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderReturnsList();
    }

    public function messages(){
        $view = $this->input->get('view', 'order');
        $layout = $this->input->get('layout', 'messages');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->renderMessagesList();
    }

    public function update_status(){
        $app = JFactory::getApplication();
        $order_status_id = $app->input->get('order_status_id');
        $order_id = $app->input->get('order_id');
        $order = new JeproshopOrderModelOrder($order_id);
        $context = JeproshopContext::getContext();

        if(!JeproshopTools::isLoadedObject($order, 'order_id')){
            echo '<p>' . JText::_('COM_JEPROSHOP_THE_ORDER_CANNOT_BE_FOUND_WITH_IN_YOUR_DATABASE_MESSAGE') . '</p>';
        }
        if($this->viewAccess()){
            $orderStatus = new JeproshopOrderStatusModelOrderStatus($order_status_id);
            if(!JeproshopTools::isLoadedObject($orderStatus, 'order_status_id')){
                echo JText::_('COM_JEPROSHOP_THE_ORDER_STATUS_CANNOT_BE_FOUND_WITH_IN_YOUR_DATABASE_MESSAGE');
            }else{
                $order = new JeproshopOrderModelOrder();
                $currentOrderStatus = $order->getCurrentOrderStatus();
                if($currentOrderStatus->order_status_id != $orderStatus){
                    // Create a order history
                    $orderHistory = new JeproshopOrderHistoryModelOrderHistory();
                    $orderHistory->order_id = $order->order_id;
                    $orderHistory->employee_id = (int)$context->employee->employee_id;

                    $useExistingPayment = false;
                    if(!$order->hasInvoice()){
                        $useExistingPayment = true;
                    }
                    $orderHistory->changeOrderStatusId((int)$orderStatus->order_status_id, $order, $useExistingPayment);
                    $carrier = new JeproshopCarrierModelCarrier($order->carrier_id, $order->lang_id);

                    $templateVars = array();
                    if($orderHistory->order_status_id == JeproshopSettingModelSetting::getValue('order_status_shipping') && $order->shipping_number){

                    }

                    if($orderHistory->addWithEmail(true, $templateVars)){
                        // synchronizes quantities if needed...
                        if(JeproshopSettingModelSetting::getValue('advanced_stock_management')){
                            foreach($order->getProducts() as $product){
                                if(JeproshopStockAvailableModelStockAvailable::dependsOnStock($product->product_id)){
                                    JeproshopStockAvailableModelStockAvailable::synchronize($product->product_id, (int)$product->sho_id);
                                }
                            }
                        }
                        $app->redirect('index.php?option=com_jeproshop&view=order&task=view&order_id=' . (int)$order->order_id . '&' . JeproshopTools::getOrderToken() . '=1');
                    }
                    echo JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_CHANGING_ORDER_STATUS_OR_WE_WERE_UNABLE_TO_SEND_AN_EMAIL_TO_THE_CUSTOMER_MESSAGE');
                }else{
                    echo JText::_('COM_JEPROSHOP_THE_ORDER_HAS_ALREADY_BEEN_ASSIGNED_THIS_STATUS_MESSAGE');
                }
            }
        }else{
            echo JText::_('COM_JEPROSHOP_YOU_DO_NOT_HAVE_PERMISSION_TO_EDIT_THIS_ORDER_MESSAGE');
        }
    }

    public function update_message(){
        $app = JFactory::getApplication();
        $order_id = $app->input->get('order_id');
        $customer_id = $app->input->get('customer_id');
        $order = new JeproshopOrderModelOrder($order_id);
        if(isset($order)){
            if($this->viewAccess()){
                $customer = new JeproshopCustomerModelCustomer($customer_id);
                if(!JeproshopTools::isLoadedObject($customer, 'customer_id')){
                    JError::raiseError(JText::_('COM_JEPROSHOP_CUSTOMER_IS_INVALID_MESSAGE'));
                }elseif(!$inputMessage){
                    JError::raiseError(JText::_('COM_JEPROSHOP_THE_MESSAGE_CANNOT_BE_BLANK_MESSAGE'));
                }else{
                    /** Get Message rules anf check fields validity */
                    $rules = JeproshopMessageModelMessage::getValidationRules();
                    foreach($rules->required as $field){
                        $value =  $app->input->get($field) == false;
                        if($value && (string)$value != '0'){
                            if($order_id || $field != 'passwd'){
                                JError::raiseError($field . ' ' . JText::_('COM_JEPROSHOP_IS_REQUIRED_FIELD_MESSAGE'));
                            }
                        }
                    }

                    foreach($rules->size as $field => $maxLength){
                        if($app->input->get($field) && strlen($field) > $maxLength){
                            JError::raiseError($field . ' ' . JText::_('COM_JEPROSHOP_FIELD_IS_TOO_LONG_LABEL') . ' ' . $maxLength . ' ' . JText::_('COM_JEPROSHOP_MAX_CHARS_LABEL'));
                        }
                    }

                    foreach($rules->validate as $field => $function){
                        if($app->input->get($field)){
                            if(!JeproshopTools::$function()){
                                JError::raiseError(JText::_('COm_JEPROSHOP_FIELD_IS_INVALID_LABEL'));
                            }
                        }
                    }

                    if(12){
                        $customer_thread_id = JeproshopCustomerThreadModelCustomerThread::getCustomerThreadIdByEmailAndOrderId($customer->email, $order->order_id);
                        if(!$customer_thread_id){
                            $customerThread = new JeproshopCustomerThreadModelCustomerThread();
                            $customerThread->contact_id = 0;
                            $customerThread->customer_id = (int)$order->customer_id;
                            $customerThread->shop_id = (int)$context->shop->shop_id;
                            $customerThread->order_id = (int)$order->order_id;
                            $customerThread->lang_id = (int)$context->language->lang_id;
                            $customerThread->email = $customer->email;
                            $customerThread->status = 'open';
                            $customerThread->token = JeproshopTools::passwdGen(12);
                            $customerThread->add();
                        }else{
                            $customerThread = new JeproshopCustomerThreadModelCustomerThread((int)$customer_thread_id);
                        }

                        $customerMessage = new JeproshopCustomerMessageModelCustomerMessage();
                        $customerMessage->customer_thread_id = $customerThread->customer_thread_id;
                        $customerMessage->employee_id = (int)$context->employee->employee_id;
                        $customerMessage->message = $app->input->get('message');
                        $customerMessage->private = $app->input->get('visibility');

                        if(!$customerMessage->add()){
                            JError::raiseError(JText::_('COM_JEPROSHOP_AN_ERROR_WHILE_'));
                        }elseif($customerMessage->private){
                            $app->redirect('index.php?option=com_jeproshop&view=order&task=view&order_id=' . (int)$order->order_id);
                        }else{

                        }
                    }
                }
            }
        }
    }


}