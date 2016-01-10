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

	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div id="jeproshop-dashboard" >
            <?php if(isset($this->warning) && $this->warning != ''){ ?><div class="alert alert-warning" ><?php echo $this->warning; ?></div><?php } ?>
            <div class="panel" >
                <div id="jform_calendar" class="panel-content well" >
                    <form action="<?php echo JRoute::_('index.php?option=com_jeproshop'); ?>" method="POST" id="jform_calendar_form" name="calendar_form" class="jform-horizontal" >
                        <span class="pull-left" >
                            <fieldset class="radio btn-group" id="jform_date" >
                                <input type="radio" id="jform_date_day" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="day" /><label for="jform_date_day" ><?php echo JText::_('COM_JEPROSHOP_DAY_LABEL'); ?></label>
                                <input type="radio" id="jform_date_month" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="month" /><label for="jform_date_month" ><?php echo JText::_('COM_JEPROSHOP_MONTH_LABEL'); ?></label>
                                <input type="radio" id="jform_date_year" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="year" /><label for="jform_date_year" ><?php echo JText::_('COM_JEPROSHOP_YEAR_LABEL'); ?></label>
                                <input type="radio" id="jform_date_previous_day" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="previous_day" /><label for="jform_date_previous_day" ><?php echo JText::_('COM_JEPROSHOP_DAY_LABEL') . ' - 1'; ?></label>
                                <input type="radio" id="jform_date_previous_month" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="previous_month" /><label for="jform_date_previous_month" ><?php echo JText::_('COM_JEPROSHOP_MONTH_LABEL') . ' - 1'; ?></label>
                                <input type="radio" id="jform_date_previous_year" name="jform[stats_date]" class="submit-date-day" <?php if(isset($this->preselect_date_range) && $this->preselect_date_range == ''){ ?> checked="checked" <?php } ?> value="previous_year" /><label for="jform_date_previous_year" ><?php echo JText::_('COM_JEPROSHOP_YEAR_LABEL') . ' - 1'; ?></label>
                            </fieldset>
                        </span>
                        <span class="btn-group btn-group-action pull-right" >
                            <button  type="button" class="btn hasTooltip js-stools-btn-filter" >
                                <i class="icon-calendar-empty" ></i>
                                <span class="" >
                                    <?php echo JText::_('COM_JEPROSHOP_FROM_LABEL') . ' : '; ?>
                                    <strong class="text-info" id="jform_date_picker_form_info" ><?php echo $this->context->employee->stats_date_from; ?></strong>
                                    <?php echo JText::_('COM_JEPROSHOP_TO_LABEL') . ' : '; ?>
                                    <strong class="text-info" id="jform_date_picker_to_info" ><?php echo $this->context->employee->stats_date_to; ?></strong>
                                    <strong class="text-info" id="jform_date_picker_diff_info" ></strong>
                                </span>
                                <i class="caret" ></i>
                            </button>
                        </span>
                        <?php echo $this->calendar; ?>
                    </form>
                </div>
            </div>
            <div class="panel" >
                <div id="jform_dashboard_zone_one" ><?php echo $this->dashboard_zone_one; ?></div>
                <div id="jform_dashboard_zone_two" >
                    <?php echo $this->dashboard_zone_two; ?>
                </div>
                <div id="jform_dashboard_zone_three" ></div>
            </div>
        </div>
    </div>