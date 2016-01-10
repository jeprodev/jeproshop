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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=country'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
    	<div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('country'); ?></div>
		<table class="table table-striped" id="countryList" >
            <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">#</th>
                    <th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('JSTATUS'), 'c.state', 'ASC'); ?></th>
                    <th width="20%" class="nowrap"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_COUNTRY_NAME_LABEL'), 'c.name', 'ASC'); ?></th>
                    <th width="1%" class="nowrap center "><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_ISO_CODE_LABEL'), 'c.', 'ASC'); ?></th>
                    <th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_CALL_PREFIX_LABEL'), 'c.', 'ASC'); ?></th>
                    <th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_COUNTRY_ZONE_LABEL'), 'c.', 'ASC'); ?></th>
                    <th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', JText::_('COM_JEPROSHOP_ACTIONS_LABEL'), 'c.', 'ASC'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($this->countries)){ ?>
                <tr><td colspan="8" >
                <div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
                </td></tr>
                <?php }else{ 
                    foreach($this->countries as $index => $country){ 
                        $country_link = JRoute::_('index.php?option=com_jeproshop&view=country&task=edit&country_id=' . $country->country_id . '&' . JeproshopTools::getCountryToken() . '=1');
                        $country_state = ($country->published ? 'icon-publish' : 'icon-unpublish');
                    ?>
                <tr class="row_<?php echo $index % 2; ?> " >
                    <td class="order nowrap center hidden-phone" ><?php echo $index + 1; ?></td>
                    <td class="order nowrap center hidden-phone" ><?php echo JHtml::_('grid.id', $index, $country->country_id); ?></td>
                    <td class="order nowrap center hidden-phone" >
                    	<div class="btn-group">
                            <a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="listCountryTask(<?php echo $country->country_id . ', ' . ($country->published ? '\'unpiblish\'' : '\'publish\'');?>)" >
                            <i class="<?php echo $country_state; ?>" ></i></a>
                        </div>
                    </td>
                    <td class="order nowrap " ><a href="<?php echo $country_link; ?>" ><?php echo $country->name; ?></a></td>
                    <td class="order nowrap center" ><?php echo $country->iso_code; ?></td>
                    <td class="order nowrap center hidden-phone" ><?php echo $this->displayCallPrefix($country->call_prefix); ?></td>
                    <td class="order nowrap center hidden-phone" ><?php echo $country->zone_name; ?></td>
                    <td class="order nowrap center hidden-phone" >
                    	<div class="btn-group-action" >
                    		<a href="<?php echo $country_link; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROSHOP_EDIT_LABEL'); ?></a>
                    	</div>
                    </td>
                </tr>
                <?php }
                }?>
            </tbody>
        </table>    
		<input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>