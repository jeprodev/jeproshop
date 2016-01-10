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
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=country'); ?>" method="post" name="adminForm" id="adminForm" class="jform-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeproshop_sub_menu_wrapper" ><?php echo $this->renderSubMenu('states'); ?></div>
		<table class="table table-striped" id="stateList" >
			<thead>
                <tr>
                	<th width="1%" class="nowrap center hidden-phone" >#</th>
                    <th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="60%" class="nowrap left " ><?php echo JText::_('COM_JEPROSHOP_STATE_NAME_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_ISO_CODE_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_ZONE_NAME_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_COUNTRY_NAME_LABEL'); ?></th>
                    <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROSHOP_PUBLISHED_LABEL'); ?></th>
                    <th width="1%" class="nowrap" ><span class="pull-right"><?php echo JText::_('COM_JEPROSHOP_ACTIONS_LABEL'); ?></span></th>
                </tr>
            </thead>
            <tbody>
            	<?php if(empty($this->states)){ ?>
                <tr>
                    <td colspan="8" ><div class="alert alert-no-items" ><?php echo JText::_('COM_JEPROSHOP_NO_MACTHING_RESULTS'); ?></div></td>
                </tr>
                <?php } else { 
                	foreach($this->states as $index => $state){ ?>
                <tr>
                	<td class="order nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                    <td class="order nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $state->state_id); ?></td>
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