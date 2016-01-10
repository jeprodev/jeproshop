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

?>
<form action="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
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
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=manufacturer'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-manufacturer" ></i> <?php echo JText::_('COM_JEPROSHOP_MANUFACTURERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=supplier'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-supplier" ></i> <?php echo JText::_('COM_JEPROSHOP_SUPPLIERS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=tag'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAGS_LABEL'); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=attachment'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-attachment" ></i> <?php echo JText::_('COM_JEPROSHOP_ATTACHMENTS_LABEL'); ?></a>
            </fieldset>
        </div>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-tag" ></i> <?php echo JText::_('COM_JEPROSHOP_TAG_LABEL'); ?></div>
            <div class="panel-content" >
                <div class="control-group" >
                    <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROSHOP_TAG_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROSHOP_NAME_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', true, $this->tag->name); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><?php echo JText::_('COM_JEPROSHOP_PRODUCTS_LABEL'); ?></div>
                    <div class="controls" >
                        <div>
                            <select multiple id="jform_select_left" >
                                <?php foreach($this->products_unselected as $product){ ?>
                                <option value="<?php echo $product->product_id; ?>"><?php echo $product->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <a href="#" id="move_to_right" class="btn btn-default btn-block multiple_select_add">
                                <?php echo JText::_('COM_JEPROSHOP_ADD_LABEL'); ?> <i class="icon-arrow-right"></i>
                            </a>
                            <a href="#" id="move_to_left" class="btn btn-default btn-block multiple_select_remove">
                                <i class="icon-arrow-left"></i> <?php echo JText::_('COM_JEPROSHOP_REMOVE_LABEL'); ?>
                            </a>
                        </div>
                        <div>
                            <select multiple id="jform_select_right" name="jform[products[]]" >
                                <?php foreach($this->products as $product){ ?>
                                <option selected="selected" value="<?php echo $product->product_id; ?>"><?php echo $product->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $('#move_to_right').click(function(){
            return !$('#select_left option:selected').remove().appendTo('#select_right');
        })
        $('#move_to_left').click(function(){
            return !$('#select_right option:selected').remove().appendTo('#select_left');
        });
        $('#select_left option').live('dblclick', function(){
            $(this).remove().appendTo('#select_right');
        });
        $('#select_right option').live('dblclick', function(){
            $(this).remove().appendTo('#select_left');
        });
    });
    $('#tag_form').submit(function()
    {
        $('#select_right option').each(function(i){
            $(this).attr("selected", "selected");
        });
    });
</script>