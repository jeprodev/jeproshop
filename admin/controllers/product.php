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

class JeproshopProductController extends JeproshopController
{
    public $current_category_id;



    public function display($cachable = FALSE, $urlParams = FALSE) {
        $this->initContent();
        parent::display();
    }

    public function initContent($token = null){
        $app = JFactory::getApplication();
        $context = JeproshopContext::getContext();
        $task = $app->input->get('task');
        $view = $app->input->get('view');
        //$viewClass = $this->getView($view, JFactory::getDocument()->getType());
        if($task == 'add' || $task == 'edit'){
            if($task == 'add'){

            }elseif($task == 'edit'){

            }
        }else{
            if($category_id = (int)$this->current_category_id){
                self::$_current_index .= '&category_id=' . (int)$this->current_category_id;
            }

            if(!$category_id){
                $this->_defaultOrderBy = 'product';
                if(isset($context->cookie->product_order_by) && $context->cookie->product_order_by == 'position'){
                    unset($context->cookie->product_order_by);
                    unset($context->cookie->product_order_way);
                }
                //$category_id = 1;
            }
        }
        parent::initContent();
    }

    public function ajaxPreProcess(){
        /*if (Tools::getIsset('update'.$this->table) && Tools::getIsset('id_'.$this->table))
        {
            $this->display = 'edit';
            $this->action = Tools::getValue('action');
        }*/
    }

    /**
     * Attach an existing attachment to the product
     *
     * @return void
     */
    public function attachment(){
        $app = JFactory::getApplication();
        if ($id = (int)$app->input->get('product_id')){
            $attachments = trim($app->input->get('arrayAttachments'), ',');
            $attachments = explode(',', $attachments);
            if (!JeproshopAttachmentModelAttachment::attachToProduct($id, $attachments))
                JError::raiseError(500, JText::_('An error occurred while saving product attachments.'));
        }
    }

    public function delete(){
        $app = JFactory::getApplication();
        $product_id = $app->input->get('product_id');
        $product = new JeproshopProductModelProduct((int)$product_id);

        if (JeproshopTools::isLoadedObject($product, 'product_id') && isset($this->fieldImageSettings)){
            // check if request at least one object with noZeroObject
            if (isset($product->noZeroObject) && count($taxes = call_user_func(array('JeproshopProductModelProduct', $product->noZeroObject))) <= 1) {
                $this->has_errors= true;
                JError::raiseError(500, JText::_('COM_JEPROSHOP_YOU_NEED_AT_LEAST_ONE_LABEL') . ' <b> ' . JText::_('COM_JEPROSHOP_PRODUCT_LABEL') . ' </b><br />' . JText::_('COM_JEPROSHOP_YOU_CANNOT_DELETE_ALL_OF_THE_ITEMS_LABEL'));
            }else{
                /*
                 * @since 1.5.0
                 * It is NOT possible to delete a product if there are currently:
                 * - physical stock for this product
                 * - supply order(s) for this product
                 */
                if (JeproshopSettingModelSetting::getValue('advanced_stock_management') && $product->advanced_stock_management){
                    $stock_manager = JeproshopStockManagerFactory::getManager();
                    $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->product_id, 0);
                    $real_quantity = $stock_manager->getProductRealQuantities($product->product_id, 0);
                    if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                        $this->has_errors = true;
                        JError::raiseError('COM_JEPROSHOP_YOU_CANNOT_DELETE_THIS_BECAUSE_THERE_IS_PHYSICAL_STOCK_LEFT_LABEL');
                    }
                }

                if (!$this->has_errors){
                    if ($product->delete()){
                        $category_id = (int)$app->input->get('category_id');
                        $category_url = empty($category_id) ? '' : '&category_id=' . (int)$category_id;
                        $app->enqueueMessage(JText::_('COM_JEPROSHOP_PRODUCT_LABEL') . ' ' . $product->product_id . ' ' . JText::_('COM_JEPROSHOP_WAS_SUCCESSFULLY_DELETED_BY_MESSAGE') . ' ' . JeproshopContext::getContext()->employee->firstname . ' ' . JeproshopContext::getContext()->employee->lastname);
                        $redirect_after = 'index.php?option=com_jeproshop&view=product&conf=1&'. JeproshopTools::getProductToken() . '=1' . $category_url;
                        $app->redirect($redirect_after);
                    } else {
                        $this->has_errors = true;
                        Tools::displayError('An error occurred during deletion.');
                    }
                }
            }
        }else {
            $this->has_errors = true;
            JError::raiseError(500, JText::_('COM_JEPROSHOP_AN_ERROR_OCCURRED_WHILE_DELETING_THE_PRODUCT_MESSAGE') . ' <b> ' . JText::_('COM_JEPROSHOP_PRODUCT_LABEL') . '</b> ' . JText::_('COM_JEPROSHOP_CANNOT_BE_LOADED_MESSAGE'));
        }
    }

    public function attribute(){
        $app = JFactory::getApplication();
        // Don't process if the combination fields have not been submitted
        if (!JeproshopCombinationModelCombination::isFeaturePublished() || !$app->input->get('attribute_combination_list'))
            return;

        if (Validate::isLoadedObject($product = $this->object)) {
            if ($this->isProductFieldUpdated('attribute_price') && (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null)) {
                $this->has_errors = Tools::displayError('The price attribute is required.');
            }

            if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list'))) {
                $this->has_errors = Tools::displayError('You must add at least one attribute.');
            }

            $array_checks = array(
                'reference' => 'isReference',
                'supplier_reference' => 'isReference',
                'location' => 'isReference',
                'ean13' => 'isEan13',
                'upc' => 'isUpc',
                'wholesale_price' => 'isPrice',
                'price' => 'isPrice',
                'ecotax' => 'isPrice',
                'quantity' => 'isInt',
                'weight' => 'isUnsignedFloat',
                'unit_price_impact' => 'isPrice',
                'default_on' => 'isBool',
                'minimal_quantity' => 'isUnsignedInt',
                'available_date' => 'isDateFormat'
            );
            foreach ($array_checks as $property => $check)
                if (Tools::getValue('attribute_'.$property) !== false && !call_user_func(array('Validate', $check), Tools::getValue('attribute_'.$property)))
                    $this->errors[] = sprintf(Tools::displayError('Field %s is not valid'), $property);

            if (!count($this->errors))
            {
                if (!isset($_POST['attribute_wholesale_price'])) $_POST['attribute_wholesale_price'] = 0;
                if (!isset($_POST['attribute_price_impact'])) $_POST['attribute_price_impact'] = 0;
                if (!isset($_POST['attribute_weight_impact'])) $_POST['attribute_weight_impact'] = 0;
                if (!isset($_POST['attribute_ecotax'])) $_POST['attribute_ecotax'] = 0;
                if (Tools::getValue('attribute_default'))
                    $product->deleteDefaultAttributes();

                // Change existing one
                if (($id_product_attribute = (int)Tools::getValue('id_product_attribute')) || ($id_product_attribute = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true)))
                {
                    if ($this->tabAccess['edit'] === '1')
                    {
                        if ($this->isProductFieldUpdated('available_date_attribute') && (Tools::getValue('available_date_attribute') != '' &&!Validate::isDateFormat(Tools::getValue('available_date_attribute'))))
                            $this->errors[] = Tools::displayError('Invalid date format.');
                        else
                        {
                            $product->updateAttribute((int)$id_product_attribute,
                                $this->isProductFieldUpdated('attribute_wholesale_price') ? Tools::getValue('attribute_wholesale_price') : null,
                                $this->isProductFieldUpdated('attribute_price_impact') ? Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact') : null,
                                $this->isProductFieldUpdated('attribute_weight_impact') ? Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact') : null,
                                $this->isProductFieldUpdated('attribute_unit_impact') ? Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact') : null,
                                $this->isProductFieldUpdated('attribute_ecotax') ? Tools::getValue('attribute_ecotax') : null,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                Tools::getValue('attribute_ean13'),
                                $this->isProductFieldUpdated('attribute_default') ? Tools::getValue('attribute_default') : null,
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                $this->isProductFieldUpdated('attribute_minimal_quantity') ? Tools::getValue('attribute_minimal_quantity') : null,
                                $this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null, false);
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    }
                    else
                        $this->errors[] = Tools::displayError('You do not have permission to add this.');
                }
                // Add new
                else
                {
                    if ($this->tabAccess['add'] === '1')
                    {
                        if ($product->productAttributeExists(Tools::getValue('attribute_combination_list')))
                            $this->errors[] = Tools::displayError('This combination already exists.');
                        else
                        {
                            $id_product_attribute = $product->addCombinationEntity(
                                Tools::getValue('attribute_wholesale_price'),
                                Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
                                Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
                                Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
                                Tools::getValue('attribute_ecotax'),
                                0,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                null,
                                Tools::getValue('attribute_ean13'),
                                Tools::getValue('attribute_default'),
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                Tools::getValue('attribute_minimal_quantity'),
                                Array(),
                                Tools::getValue('available_date_attribute')
                            );
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    }
                    else
                        $this->errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('edit here.');
                }
                if (!count($this->errors))
                {
                    $combination = new Combination((int)$id_product_attribute);
                    $combination->setAttributes(Tools::getValue('attribute_combination_list'));

                    // images could be deleted before
                    $id_images = Tools::getValue('id_image_attr');
                    if (!empty($id_images))
                        $combination->setImages($id_images);

                    $product->checkDefaultAttributes();
                    if (Tools::getValue('attribute_default'))
                    {
                        Product::updateDefaultAttribute((int)$product->id);
                        if(isset($id_product_attribute))
                            $product->cache_default_attribute = (int)$id_product_attribute;
                        if ($available_date = Tools::getValue('available_date_attribute'))
                            $product->setAvailableDate($available_date);
                    }
                }
            }
        }
    }

    public function processFeatures()
    {
        if (!Feature::isFeatureActive())
            return;

        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))){
            // delete all objects
            $product->deleteFeatures();

            // add new objects
            $languages = Language::getLanguages(false);
            foreach ($_POST as $key => $val)
            {
                if (preg_match('/^feature_([0-9]+)_value/i', $key, $match))
                {
                    if ($val)
                        $product->addFeaturesToDB($match[1], $val);
                    else
                    {
                        if ($default_value = $this->checkFeatures($languages, $match[1]))
                        {
                            $id_value = $product->addFeaturesToDB($match[1], 0, 1);
                            foreach ($languages as $language)
                            {
                                if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang']))
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
                                else
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
                            }
                        }
                    }
                }
            }
        }
        else
            $this->has_errors[] = Tools::displayError('A product must be created before adding features.');
    }

    /**
     * This function is never called at the moment (specific prices cannot be edited)
     */
    public function processPricesModification()
    {
        $id_specific_prices = Tools::getValue('spm_id_specific_price');
        $id_combinations = Tools::getValue('spm_id_product_attribute');
        $id_shops = Tools::getValue('spm_id_shop');
        $id_currencies = Tools::getValue('spm_id_currency');
        $id_countries = Tools::getValue('spm_id_country');
        $id_groups = Tools::getValue('spm_id_group');
        $id_customers = Tools::getValue('spm_id_customer');
        $prices = Tools::getValue('spm_price');
        $from_quantities = Tools::getValue('spm_from_quantity');
        $reductions = Tools::getValue('spm_reduction');
        $reduction_types = Tools::getValue('spm_reduction_type');
        $froms = Tools::getValue('spm_from');
        $tos = Tools::getValue('spm_to');

        foreach ($id_specific_prices as $key => $id_specific_price)
            if ($reduction_types[$key] == 'percentage' && ((float)$reductions[$key] <= 0 || (float)$reductions[$key] > 100))
                $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
            elseif ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key], $id_combinations[$key]))
            {
                $specific_price = new SpecificPrice((int)($id_specific_price));
                $specific_price->id_shop = (int)$id_shops[$key];
                $specific_price->id_product_attribute = (int)$id_combinations[$key];
                $specific_price->id_currency = (int)($id_currencies[$key]);
                $specific_price->id_country = (int)($id_countries[$key]);
                $specific_price->id_group = (int)($id_groups[$key]);
                $specific_price->id_customer = (int)$id_customers[$key];
                $specific_price->price = (float)($prices[$key]);
                $specific_price->from_quantity = (int)($from_quantities[$key]);
                $specific_price->reduction = (float)($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
                $specific_price->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
                $specific_price->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
                $specific_price->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
                if (!$specific_price->update())
                    $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
            }
        if (!count($this->errors))
            $this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&update'.$this->table.'&action=Prices&token='.$this->token;

    }

    public function processPriceAddition()
    {
        // Check if a specific price has been submitted
        if (!Tools::getIsset('submitPriceAddition'))
            return;

        $id_product = Tools::getValue('id_product');
        $id_product_attribute = Tools::getValue('sp_id_product_attribute');
        $id_shop = Tools::getValue('sp_id_shop');
        $id_currency = Tools::getValue('sp_id_currency');
        $id_country = Tools::getValue('sp_id_country');
        $id_group = Tools::getValue('sp_id_group');
        $id_customer = Tools::getValue('sp_id_customer');
        $price = Tools::getValue('leave_bprice') ? '-1' : Tools::getValue('sp_price');
        $from_quantity = Tools::getValue('sp_from_quantity');
        $reduction = (float)(Tools::getValue('sp_reduction'));
        $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
        $from = Tools::getValue('sp_from');
        if (!$from)
            $from = '0000-00-00 00:00:00';
        $to = Tools::getValue('sp_to');
        if (!$to)
            $to = '0000-00-00 00:00:00';

        if ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100))
            $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
        elseif ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute))
        {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int)$id_product;
            $specificPrice->id_product_attribute = (int)$id_product_attribute;
            $specificPrice->id_shop = (int)$id_shop;
            $specificPrice->id_currency = (int)($id_currency);
            $specificPrice->id_country = (int)($id_country);
            $specificPrice->id_group = (int)($id_group);
            $specificPrice->id_customer = (int)$id_customer;
            $specificPrice->price = (float)($price);
            $specificPrice->from_quantity = (int)($from_quantity);
            $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
            $specificPrice->reduction_type = $reduction_type;
            $specificPrice->from = $from;
            $specificPrice->to = $to;
            if (!$specificPrice->add())
                $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
        }
    }

    public function ajaxProcessDeleteSpecificPrice()
    {
        if ($this->tabAccess['delete'] === '1')
        {
            $id_specific_price = (int)Tools::getValue('id_specific_price');
            if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price))
                $error = Tools::displayError('The specific price ID is invalid.');
            else
            {
                $specificPrice = new SpecificPrice((int)$id_specific_price);
                if (!$specificPrice->delete())
                    $error = Tools::displayError('An error occurred while attempting to delete the specific price.');
            }
        }
        else
            $error = Tools::displayError('You do not have permission to delete this.');

        if (isset($error))
            $json = array(
                'status' => 'error',
                'message'=> $error
            );
        else
            $json = array(
                'status' => 'ok',
                'message'=> $this->_conf[1]
            );

        die(Tools::jsonEncode($json));
    }

    public function processSpecificPricePriorities(){
        if (!($obj = $this->loadObject()))
            return;
        if (!$priorities = Tools::getValue('specificPricePriority'))
            $this->errors[] = Tools::displayError('Please specify priorities.');
        elseif (Tools::isSubmit('specificPricePriorityToAll'))
        {
            if (!SpecificPrice::setPriorities($priorities))
                $this->errors[] = Tools::displayError('An error occurred while updating priorities.');
            else
                $this->confirmations[] = $this->l('The price rule has successfully updated');
        }
        elseif (!SpecificPrice::setSpecificPriority((int)$obj->id, $priorities))
            $this->errors[] = Tools::displayError('An error occurred while setting priorities.');
    }

    public function processCustomizationConfiguration()
    {
        $product = $this->object;
        // Get the number of existing customization fields ($product->text_fields is the updated value, not the existing value)
        $current_customization = $product->getCustomizationFieldIds();
        $files_count = 0;
        $text_count = 0;
        if (is_array($current_customization))
        {
            foreach ($current_customization as $field)
            {
                if ($field['type'] == 1)
                    $text_count++;
                else
                    $files_count++;
            }
        }

        if (!$product->createLabels((int)$product->uploadable_files - $files_count, (int)$product->text_fields - $text_count))
            $this->errors[] = Tools::displayError('An error occurred while creating customization fields.');
        if (!count($this->errors) && !$product->updateLabels())
            $this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
        $product->customizable = ($product->uploadable_files > 0 || $product->text_fields > 0) ? 1 : 0;
        if (!count($this->errors) && !$product->update())
            $this->errors[] = Tools::displayError('An error occurred while updating the custom configuration.');
    }

    public function processProductCustomization()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
        {
            foreach ($_POST as $field => $value)
                if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value))
                    $this->errors[] = Tools::displayError('The label fields defined are invalid.');
            if (empty($this->errors) && !$product->updateLabels())
                $this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
            if (empty($this->errors))
                $this->confirmations[] = $this->l('Update successful');
        }
        else
            $this->errors[] = Tools::displayError('A product must be created before adding customization.');
    }

    public function save(){
        if($this->viewAccess() && JeproshopTools::checkProductToken()){
            if($this->has_errors){
                return false;
            }
            $productModel = new JeproshopProductModelProduct();
            $productModel->saveProduct();
        }
    }

    public function update(){
        if($this->viewAccess() && JeproshopTools::checkProductToken()){
            $app = JFactory::getApplication();
            $product_id = $app->input->get('product_id');
            if(isset($product_id) && $product_id > 0) {
                $productModel = new JeproshopProductModelProduct($product_id);
                $productModel->updateProduct();
            }
        }
    }
}