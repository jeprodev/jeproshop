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

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$css_dir = JeproshopContext::getContext()->shop->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeproshop/assets/themes/' . $css_dir .'/css/jeproshop.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/js/ckeditor.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/jquery/plugins/jquery.typewatch.js');
$document->addScript(JURI::base(). 'components/com_jeproshop/assets/javascript/script/tools.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacture'); ?>" method="post" name="adminForm" id="adminForm" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=product'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-product" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=category&category_id=null&parent_id=null'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROSHOP_CATEGORIES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tracking'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-monitoring" ></i> <?php echo JText::_('COM_JEPROSHOP_MONITORING_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attribute'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attribute" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_ATTRIBUTES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=feature'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-feature" ></i> <?php echo JText::_('COM_JEPROSHOP_PRODUCT_FEATURES_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufaturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="separation" ></div>
        <div class="panel" >
            <div class="panel-content well form-horizontal" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_name" name="jform[name]" value="<?php echo $this->supplier->name; ?>" required="required" /> </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_description" title="<?php echo JText::_('COM_JEPROSHOP_INVALID_CHARACTERS_LABEL') . '<br />' . JText::_('COM_JEPROSHOP_WILL_APPEAR_IN_THE_LIST_OF_SUPPLIERS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_DESCRIPTION_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageTextAreaField('description', 'jform', $this->supplier->description); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_company" title="<?php  echo JText::_('COM_JEPROSHOP_COMPANY_NAME_FOR_THIS_SUPPLIER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COMPANY_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_company" name="jform[company]" required="required" maxlength="32" value="<?php echo $this->address->company; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_phone" title="<?php echo JText::_('COM_JEPROSHOP_SUPPLIER_PHONE_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PHONE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_phone" name="jform[phone]" required="required" maxlength="16" value="<?php echo $this->address->phone; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_mobile_phone" title="<?php echo JText::_('COM_JEPROSHOP_SUPPLIER_MOBILE_PHONE_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MOBILE_PHONE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_mobile_phone" name="jform[mobile_phone]" required="required" maxlength="16" value="<?php echo $this->address->phone_mobile; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_address" title="<?php echo JText::_('COM_JEPROSHOP_ADDRESS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADDRESS_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_" name="jform[address]" maxlength="128" required="required" value="<?php echo $this->address->address1; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_address_2" title="<?php echo JText::_('COM_JEPROSHOP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADDRESS_LABEL') . ' (2)'; ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_address_2" name="jform[address_2]" maxlength="128" value="<?php echo $this->address->address2; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_postcode" title="<?php echo JText::_('COM_JEPROSHOP_POSTCODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_postcode" name="jform[postcode]" value="<?php echo $this->address->postcode; ?>" maxlength="12" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_city" title="<?php echo JText::_('COM_JEPROSHOP_CITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CITY_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_city" name="jform[city]" maxlength="32" required="required" value="<?php echo $this->address->city; ?>" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_country_id" title="<?php echo JText::_('COM_JEPROSHOP_COUNTRY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <select id="jform_country_id"  name="jform[country_id]" >
                            <?php foreach($this->countries as $country){ ?>
                                <option value="<?php echo $country->country_id; ?>" <?php if($country->country_id == $this->address->country_id){ ?> selected="selected" <?php } ?> ><?php echo ucfirst($country->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_state_id" title="<?php echo JText::_('COM_JEPROSHOP_STATE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STATE_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <select id="jform_state_id" name="jform[state_id]" >

                        </select>
                    </div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_logo" title="<?php echo JText::_('COM_JEPROSHOP_LOGO_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LOGO_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_" maxlength="16" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_meta_title" title="<?php echo JText::_('COM_JEPROSHOP_META_TITLE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_TITLE_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_title', null, false, $this->supplier->meta_title); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_meta_description" title="<?php echo JText::_('COM_JEPROSHOP_META_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_DESCRIPTION_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_description', null, false, $this->supplier->meta_description); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_meta_keywords" title="<?php echo JText::_('COM_JEPROSHOP_SUPPLIER_META_KEYWORDS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_META_KEYWORDS_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_keywords', null, false, $this->supplier->meta_keywords); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_published" title="<?php echo JText::_('COM_JEPROSHOP_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->radioButton('published', 'edit', $this->supplier->published); ?></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="<?php echo JeproshopTools::getSupplierToken(); ?>" value="1" />
    </div>
</form>