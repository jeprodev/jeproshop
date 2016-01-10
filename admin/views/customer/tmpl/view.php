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
?>

<div class="form-horizontal" >
	<?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
    	<div class="box_wrapper jeproshop_sub_menu_wrapper">
            <fieldset class="btn-group">
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer'); ?>" class="btn jeproshop_sub_menu btn-success" ><i class="icon-customer" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMERS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-address" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=group'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-group" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GROUPS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=cart'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-cart" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_SHOPPING_CARTS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=customer&task=threads'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-thread" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CUSTOMER_THREADS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=contact'); ?>" class="btn jeproshop_sub_menu " ><i class="icon-contact" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_CONTACTS_LABEL')); ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=gender'); ?>" class="btn jeproshop_sub_menu" ><i class="icon-gender" ></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_GENDER_LABEL')); ?></a>                
            </fieldset>
        </div>
        <div class="separation"></div>
		<div class="half_wrapper left" ><?php echo $this->loadTemplate('left'); ?></div>
		<div class="half_wrapper right" ><?php echo $this->loadTemplate('right'); ?></div>
		<div class="separation" ></div>
		<div style="clear: both" ></div>
		<div class="panel" >
			<div class="panel-title" ><i class="icon-address" ></i> <?php echo strtoupper(JText::_('COM_JEPROSHOP_ADDRESSES_LABEL')); ?> <span class="label label-success"><?php echo count($this->addresses); ?></span></div>
			<div class="panel-content well" >
				<?php if(count($this->addresses)){ ?>
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_COMPANY_LABEL')); ?></span></th>
							<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_NAME_LABEL')); ?></span></th>
							<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_ADDRESS_LABEL')); ?></span></th>
							<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_COUNTRY_LABEL')); ?></span></th>
							<th><span class="title_box "><?php echo ucfirst(JText::_('COM_JEPROSHOP_PHONE_NUMBERS_LABEL')); ?></span></th>
							<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($this->addresses AS $key => $address){ ?>
							<tr>
								<td><?php if($address->company){ echo $address->company; }else{ echo '--'; } ?></td>
								<td><?php echo $address->firstname . $address->lastname; ?></td>
								<td><?php echo $address->address1; if($address->address2){ echo $address->address2; } echo $address->postcode . ' ' . $address->city; ?></td>
								<td><?php echo $address->country; ?></td>
								<td>
									<?php if($address->phone){
										echo $address->phone;
										if($address->phone_mobile){ echo '<br />' . $address->phone_mobile; }
									}else{
										if($address->phone_mobile){ echo '<br />' . $address->phone_mobile; }else{ echo '--'; }
									} ?>
								</td>
								<td class="text-right">
									<div class="btn-group pull-right">
										<a class="btn btn-default btn-micro" href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address&task=edit&address_id=' . $address->address_id . '&' . JeproshopTools::getAddressToken() . '=1'); ?>" >
											<i class="icon-edit"></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_EDIT_LABEL')); ?>
										</a>
										<button type="button" class="btn btn-default dropdown-toggle btn-micro" data-toggle="dropdown">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a href="<?php echo JRoute::_('index.php?option=com_jeproshop&view=address&task=delete&address=' . $address->address_id . '&' . JeproshopTools::getAddressToken() . '=1'); ?>">
													<i class="icon-trash"></i> <?php echo ucfirst(JText::_('COM_JEPROSHOP_DELETE_LABEL')); ?>
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php }else{ ?>
					<p class="text-muted text-center">
						<?php echo ucfirst($this->customer->firstname) . ' ' . strtoupper($this->customer->lastname) . ' ' . JText::_('COM_JEPROSHOP_HAS_NOT_REGISTER_ANY_ADDRESS_YET_MESSAGE'); ?>
					</p>
				<?php } ?>
			</div>
		</div>
	</div>
</div>