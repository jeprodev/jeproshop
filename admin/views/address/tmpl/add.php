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
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <?php echo $this->renderSubMenu('address'); ?>
        <div class="panel" >
            <div class="panel-title"><i class="icon-address" ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_ADDRESS_LABEL')); ?></div>
            <div class="panel-content well">
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_customer" title="<?php echo JText::_('COM_JEPROSHOP_CUSTOMER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CUSTOMER_LABEL'); ?></label> </div>
                    <div class="controls" >
                        <?php if(isset($this->customer)){ ?>
                        <a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=view&customer_id=' . (int)$this->customer->customer_id . '&' . JeproshopTools::getCustomerToken() . '=1'); ?>" >
                            <i class="icon-eye-open"></i> <?php echo $this->customer->lastname . ' ' . $this->customer->firstname . ' (' . $this->customer->email . ') '; ?>
                        </a>
                        <input type="hidden" name="jform[customer_id]" value="<?php echo $this->customer->customer_id; ?>" />
                        <input type="hidden" name="jform[email]" value="<?php echo $this->customer->email; ?>" />
                        <?php }else{ ?>
                        <script type="text/javascript">
                            $('input[name=email]').live('blur', function(e)
                            {
                                var email = $(this).val();
                                if (email.length > 5)
                                {
                                    var data = {};
                                    data.email = email;
                                    data.token = "{$token|escape:'html':'UTF-8'}";
                                    data.ajax = 1;
                                    data.controller = "AdminAddresses";
                                    data.action = "loadNames";
                                    $.ajax({
                                        type: "POST",
                                        url: "ajax-tab.php",
                                        data: data,
                                        dataType: 'json',
                                        async : true,
                                        success: function(msg)
                                        {
                                            if (msg)
                                            {
                                                var infos = msg.infos.replace("\\'", "'").split('_');

                                                $('input[name=firstname]').val(infos[0]);
                                                $('input[name=lastname]').val(infos[1]);
                                                $('input[name=company]').val(infos[2]);
                                            }
                                        },
                                        error: function(msg)
                                        {
                                        }
                                    });
                                }
                            });
                        </script>
                        <input type="text" id="jform_email" name="jform[email]" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                        <?php } ?>
                    </div>
                </div>
                <?php foreach($this->delivery_fields as $field){
                    if($field == 'company'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_company" title="<?php echo JText::_('COM_JEPROSHOP_COMPANY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COMPANY_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_company" name="jform[company]"  ></div>
                    </div>
                    <?php }elseif($field == 'vat_number'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_vat_number" title="<?php echo JText::_('COM_JEPROSHOP_VAT_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_VAT_NUMBER_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_vat_number" name="jform[vat_number]" /></div>
                    </div>
                    <?php }elseif($field == 'lastname'){
                        if(isset($this->customer) && 1){}else{ $default_value = ''; } ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_lastname" title="<?php echo JText::_('COM_JEPROSHOP_LAST_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_LAST_NAME_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_lastname" name="jform[lastname]"  value="<?php echo $default_value; ?>"/></div>
                    </div>
                    <?php }elseif($field == 'firstname'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_firstname" title="<?php echo JText::_('COM_JEPROSHOP_FIRST_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_FIRST_NAME_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_firstname" name="jform[firstname]" /></div>
                    </div>
                    <?php }elseif($field == 'address1'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_address" title="<?php echo JText::_('COM_JEPROSHOP_ADDRESS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADDRESS_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_address" name="jform[address]" /></div>
                    </div>
                    <?php }elseif($field == 'address2'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_address_2" title="<?php echo JText::_('COM_JEPROSHOP_ADDRESS_LINE_2_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADDRESS2_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_address_2" name="jform[address2]" /></div>
                    </div>
                    <?php }elseif($field == 'postcode'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_postcode" title="<?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ZIP_POSTAL_CODE_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_postcode" name="jform[postcode]" /></div>
                    </div>
                    <?php }elseif($field == 'city'){ ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_city" title="<?php echo JText::_('COM_JEPROSHOP_CITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_CITY_LABEL'); ?></label> </div>
                        <div class="controls" ><input type="text" id="jform_city" name="jform[city]" /></div>
                    </div>
                    <?php }elseif($field == 'country' || $field == 'Country:name'){
                        $default_country_id = (int)$this->context->country->country_id;
                        $countries = JeproshopCountryModelCountry::getStaticCountries($this->context->language->lang_id); ?>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_country_id" title="<?php echo JText::_('COM_JEPROSHOP_COUNTRY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_LABEL'); ?></label> </div>
                        <div class="controls" >
                            <select id="jform_country_id" name="jform[country_id]" >
                                <?php foreach($countries as $country){ ?>
                                    <option value="<?php echo $country->country_id; ?>" ><?php echo $country->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group" >
                        <div class="control-label" ><label for="jform_state" title="<?php echo JText::_('COM_JEPROSHOP_STATE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_STATE_LABEL'); ?></label> </div>
                        <div class="controls" ><select id="jform_state" name="jform[state]" ></select></div>
                    </div>
                <?php } }  ?>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_identification_number" title="<?php echo JText::_('COM_JEPROSHOP_IDENTIFICATION_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_FISC_IDENTIFICATION_NUMBER_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_identification_number" name="jform[identification_number]" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_address_alias" title="<?php echo JText::_('COM_JEPROSHOP_ADDRESS_ALIAS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_ADDRESS_ALIAS_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_address_alias" name="jform[address_alias]" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_home_phone" title="<?php echo JText::_('COM_JEPROSHOP_HOME_PHONE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_PHONE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_home_phone" name="jform[home_phone]" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_mobile_phone" title="<?php echo JText::_('COM_JEPROSHOP_MOBILE_PHONE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_MOBILE_PHONE_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_mobile_phone" name="jform[mobile_phone]" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_other" title="<?php echo JText::_('COM_JEPROSHOP_OTHER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_OTHER_LABEL'); ?></label> </div>
                    <div class="controls" ><textarea id="jform_other" name="jform[other]" cols="25" rows="5" ></textarea></div>
                </div>
            </div>
        </div>
    </div>
</form>