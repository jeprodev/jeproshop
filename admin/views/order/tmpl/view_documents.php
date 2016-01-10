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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="table-responsive">
    <table class="table table-stripped" id="documents_table">
        <thead>
        <tr>
            <th width="8%" class="nowrap center" ><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_DATE_LABEL'); ?></span></th>
            <th width="58%" class="nowrap" ><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_DOCUMENT_LABEL'); ?></span></th>
            <th width="18%" class="nowrap center" ><span class="title_box "><?php echo JText::_('COM_JEPROSHOP_NUMBER_LABEL'); ?></span></th>
            <th width="8%" class="nowrap " ><span class="pull-right "><?php echo JText::_('COM_JEPROSHOP_AMOUNT_LABEL'); ?></span></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php if($this->order->getDocuments()){
                foreach ($this->order->getDocuments() as $document) {
                    if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                        if (isset($document->is_delivery)) { ?>
                            <tr id="delivery_<?php echo $document->id; ?>" >
                        <?php } else { ?>
                            <tr id="invoice_<?php echo $document->id; ?>" >
                        <?php }
                    } elseif (get_class($document) == 'JeproshopOrderSlipModelOrderSlip') { ?>
                        <tr id="order_slip_<?php echo $document->id; ?>">
                    <?php } ?>
                    <td>{dateFormat date=$document->date_add}</td>
                    <td>
                        <?php if (get_class($document) == 'JeproshopOrderInvoiceModelInvoice') {
                            if (isset($document->is_delivery)) {
                                echo JText::_('COM_JEPROSHOP_DELIVERY_SLIP_LABEL');
                            } else {
                                echo JText::_('COM_JEPROSHOP_INVOICE_LABEL');
                            }
                        } elseif (get_class($document) == 'JeproshopOrderSlipModelOrderSlip') {
                            echo JText::_('COM_JEPROSHOP_CREDIT_SLIP_LABEL');
                        } ?>
                    </td>
                    <td>
                        <?php if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                        if (isset($document->is_delivery)) { ?>
                        <a target="_blank" title="<?php echo JText::_('COM_JEPROSHOP_SEE_THE_DOCUMENT_LABEL'); ?>"
                           href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order_invoice={$document->id}">
                            <?php } else { ?>
                            <a target="_blank" title="<?php echo JText::_('COM_JEPROSHOP_SEE_THE_DOCUMENT_LABEL'); ?>"
                               href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order_invoice={$document->id}">
                                <?php }
                                }elseif (get_class($document) == 'JeproshopOrderSlipModelOrderSlip'){ ?>
                                <a target="_blank"
                                   title="<?php echo JText::_('COM_JEPROSHOP_SEE_THE_DOCUMENT_LABEL'); ?>"
                                   href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateOrderSlipPDF&amp;id_order_slip={$document->id}">
                                    <?php }
                                    if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                                        if (isset($document->is_delivery)) {
                                            echo '#' . JeproshopSettingModelSetting::getValue('delivery_prefix', $this->current_lang_id, null, $this->order->shop_id) . ' ' . $document->delivery_number;
                                        } else {
                                            $document->getInvoiceNumberFormatted($this->current_lang_id, $this->order->shop_id);
                                        }
                                    } elseif (get_class($document) == 'JeproshopOrderSlipModelOrderSlip') {
                                        echo '#' . JeproshopSettingModelSetting::getValue('credit_slip_prefix', $this->current_lang_id) . ' ' . $document->id;
                                    } ?>
                                </a>
                    </td>
                    <td>
                        <?php if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                            if (isset($document->is_delivery)) {
                                echo '--';
                            } else {
                                echo JeproshopTools::displayPrice($document->total_paid_tax_incl, $this->currency->currency_id) . '&nbsp';
                                if ($document->getTotalPaid()) { ?>
                                    <span>
                        <?php if ($document->getRestPaid() > 0) {
                            echo '(' . JeproshopTools::displayPrice($document->getRestPaid(), $this->currency->currency_id) . ' ' . JText::_('COM_JEPROSHOP_NOT_PAID_LABEL') . ')';
                        } else if ($document->getRestPaid() < 0) {
                            echo '(' . JeproshopTools::displayPrice(-$document->getRestPaid(), $currency->currecy_id) . ' ' . JText::_('COM_JEPROSHOP_OVER_PAID_LABEL') . ')';
                        } ?>
                    </span>
                                <?php }
                            }
                        } elseif (get_class($document) == 'JeproshopOrderSlipModelOrderSlip') {
                            echo JeproshopTools::displayPrice($document->amount, $this->currency->currencyid);
                        } ?>
                    </td>
                    <td class="text-right document_action">
                        <?php if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                            if (!isset($document->is_delivery)) {
                                if ($document->getRestPaid()) { ?>
                                    <a href="#formAddPaymentPanel" class="js-set-payment btn btn-default anchor"
                                       data-amount="<?php echo $document->getRestPaid(); ?>"
                                       data-invoice-id="<?php echo $document->id; ?>"
                                       title="<?php echo JText::_('COM_JEPROSHOP_SET_PAYMENT_FORM_TITLE_DESC'); ?>">
                                        <i class="icon-money"></i> <?php echo JText::_('COM_JEPROSHOP_ENTER_PAYMENT_LABEL'); ?>
                                    </a>
                                <?php } ?>

                                <a href="#" class="btn btn-default"
                                   onclick="$('#invoiceNote<?php echo $document->id; ?>').show(); return false;"
                                   title="<?php if ($document->note == '') {
                                       echo JText::_('COM_JEPROSHOP_ADD_NOTE_LABEL');
                                   } else {
                                       echo JText::_('COM_JEPROSHOP_EDIT_NOTE_LABEL');
                                   } ?>">
                                    <?php if ($document->note == '') { ?>
                                        <i class="icon-plus-sign-alt"></i>
                                        <?php echo JText::_('COM_JEPROSHOP_ADD_NOTE_LABEL'); ?>
                                    <?php } else { ?>
                                        <i class="icon-pencil"></i>
                                        <?php echo JText::_('COM_JEPROSHOP_EDIT_NOTE_LABEL');
                                    } ?>
                                </a>

                            <?php }
                        } ?>
                    </td>
                    </tr>
                    <?php if (get_class($document) == 'JeproshopOrderInvoiceModelOrderInvoice') {
                        if (!isset($document->is_delivery)) { ?>
                            <tr id="invoiceNote{$document->id}" style="display:none">
                                <td colspan="5">
                                    <form
                                        action="{$current_index}&amp;viewOrder&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}"
                                        method="post">
                                        <p>
                                            <label for="editNote{$document->id}"
                                                   class="t"><?php echo JText::_('COM_JEPROSHOP_NOTE_LABEL'); ?></label>
                                            <input type="hidden" name="id_order_invoice" value="{$document->id}"/>
                                            <textarea name="note" id="editNote{$document->id}"
                                                      class="edit-note textarea-autosize">{$document->note|escape:'html':'UTF-8'}</textarea>
                                        </p>

                                        <p>
                                            <button type="submit" name="submitEditNote" class="btn btn-default">
                                                <i class="icon-save"></i> <?php echo JText::_('COM_JEPROSHOP_SAVE_LABEL'); ?>
                                            </button>
                                            <a class="btn btn-default" href="#" id="cancelNote"
                                               onclick="$('#invoiceNote<?php echo $document->id; ?>').hide();return false;">
                                                <i class="icon-remove"></i> <?php echo JText::_('COM_JEPROSHOP_CANCEL_LABEL'); ?>
                                            </a>
                                        </p>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    }
                }
        }else{ ?>
            <tr>
                <td colspan="5" class="list-empty">
                    <div class="list-empty-msg alert-warning">
                        <i class="icon-warning-sign list-empty-icon"></i> <?php echo JText::_('COM_JEPROSHOP_THERE_IS_NO_AVAILABLE_DOCUMENT_LABEL'); ?>
                    </div>
                    <?php if(isset($invoice_management_active) && $invoice_management_active){ ?>
                    <a class="btn btn-default" href="{$current_index}&amp;viewOrder&amp;submitGenerateInvoice&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
                        <i class="icon-repeat"></i> <?php echo JText::_('COM_JEPROSHOP_GENERATE_INVOICE_LABEL'); ?>
                    </a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>