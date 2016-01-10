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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=currency'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('currency'); ?></div>
		<table class="table table-striped" id="stateList" >
			<thead>
                <tr>
                	<th width="1%" class="nowrap center hidden-phone" >#</th>
                    <th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="60%" class="nowrap left " ><?php echo JText::_('COM_JEPROSHOP_CURRENCY_NAME_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_ISO_CODE_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_ISO_CODE_NUMBER_LABEL'); ?></th>
                    <th width="3%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_CURRENCY_SIGN_LABEL'); ?></th>
                    <th width="3%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_CURRENCY_EXCHANGE_RATE_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></th>
                    <th width="1%" class="nowrap center" ><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></th>
                </tr>
            </thead>
            <tbody>
            	<?php if(empty($this->currencies)){ ?>
                <tr>
                    <td colspan="9" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MACTHING_RESULTS'); ?></div></td>
                </tr>
                <?php } else { 
                	foreach($this->currencies as $index => $currency){
                        $currency_link = JRoute::_('index.php?option=com_jeproshop&view=currency&task=edit&currency_id=' . $currency->currency_id . '&' . JeproshopTools::getCurrencyToken() . '=1'); ?>
                <tr class="row_<?php echo $index % 0; ?>" >
                	<td class="nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                    <td class=" nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $currency->currency_id); ?></td>
                    <td class="nowrap" ><a href="<?php echo $currency_link; ?>" ><?php echo ucfirst($currency->name); ?></a></td>
                    <td class="nowrap center" ><?php echo strtoupper($currency->iso_code); ?></td>
                    <td class="nowrap center" ><?php echo $currency->iso_code_num; ?></td>
                    <td class="nowrap center" ><?php echo $currency->sign; ?></td>
                    <td class="nowrap" ><?php echo $currency->conversion_rate; ?></td>
                    <td class="nowrap center" ><i class="icon-<?php echo ($currency->published ? '' : 'un') . 'publish'; ?>" ></i></td>
                    <td class="nowrap hidden-phone" >
                        <span class="pull-right" >
                            <div class="btn-group-action" >
                                <div class="btn-group pull-right" >
                                    <a href="<?php echo $currency_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                                    <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $delete_currency_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROSHOP_PRODUCT_DELETE_LABEL') . $currency->name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </span>
                    </td>
                </tr>
					<?php }
                } ?>
            </tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>