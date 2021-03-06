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
$productLink = JRoute::_('index.php?option=com_jeproshop&view=product&product_id=' . $product->product_id . '&link_rewrite=' . $product->link_rewrite . '&category_id=' . $product->category_id . '&shop_id=' . $product->shop_id . '&product_attribute_id=' . $product->product_attribute_id);
?>
<tr id="product_<?php echo $product->product_id . '_' . $product->product_attribute_id . '_' . (($quantityDisplayed > 0) ? 'no_custom' : 0) . '_' . $product->address_delivery_id . (!empty($product->gift) ? '_gift' : ''); ?>" class="cart_item';
        $script .= ((isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)) ? ' last_item' : '') . ((isset($productFirst) && $productFirst) ? ' first_item' : '') . ((isset($customizedDatas) AND $quantityDisplayed == 0) ? ' alternate_item' : '');
        $script .= 'address_' . $product->address_delivery_id . ($odd ? ' odd' : ' even') . '" ><td class="cart_product"><a href="' . $productLink . '"><img src="' . JeproshopHelper::getImageLink($product->link_rewrite, $product->image_id, 'small_default') . '" alt="' . $product->name . '" ';
            $script .= (isset($smallSize) ? 'width="' . $smallSize->width . '" height="' . $smallSize->height . '" ' : '') . ' /></a></td><td class="cart_description"><p class="product-name"><a href="' . $productLink . '">' .$product->name . '</a></p>';
        $script .= (($product->reference) ? '<small class="cart_ref">' . 'SKU' . ' : ' . $product->reference . '</small>'  : '') . ((isset($product->attributes) && $product->attributes) ? '<small><a href="' . $productLink . '" >' . $product->attributes . '</a></small>' : '') . '</td>';
    if($stock_management){
    //$script .= '<td class="cart_avail"><span class="{if $product.quantity_available <= 0 && !$product.allow_oosp}label label-available_later{else}label label-success{/if}">{if $product.quantity_available <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}</span>{hook h="displayProductDeliveryTime" product=$product}</td>';
    }
    $script .= '<td class="cart_unit" data-title="' . JText::_('COM_JEPROSHOP_UNIT_PRICE_LABEL') . '" ><span class="price" id="product_price_' . $product->product_id . '_' . $product->product_attribute_id . (($quantityDisplayed > 0) ? '_no_custom' : '') . '_' . $product->address_delivery_id;
        /*$script .= {if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
            	{if !$priceDisplay}
					<span class="price{if isset($product.is_discounted) && $product.is_discounted} special-price{/if}">{convertPrice price=$product.price_wt}</span>
				{else}
               	 	<span class="price{if isset($product.is_discounted) && $product.is_discounted} special-price{/if}">{convertPrice price=$product.price}</span>
				{/if}
				{if isset($product.is_discounted) && $product.is_discounted}
                	<span class="price-percent-reduction small">
            			{if !$priceDisplay}
            				{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                    			{assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                    			{assign var='symbol' value=$currency->sign}
                    		{else}
                    			{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                    			{assign var='symbol' value='%'}
                    		{/if}
						{else}
							{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
								{assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
								{assign var='symbol' value=$currency->sign}
                    		{else}
                    			{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * 100 * -1}
                    			{assign var='symbol' value='%'}
                    		{/if}
						{/if}
						{if $symbol == '%'}
							&nbsp;{$priceReduction|round|string_format:"%d"}{$symbol}&nbsp;
						{else}
							&nbsp;{$priceReduction|string_format:"%.2f"}{$symbol}&nbsp;
						{/if}
                    </span>
					<span class="old-price">{convertPrice price=$product.price_without_specific_price}</span>
				{/if}
			{/if}
		</span>
    </td>

    <td class="cart_quantity text-center">
        {if isset($cannotModify) AND $cannotModify == 1}
			<span>
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{$product.customizationQuantityTotal}
				{else}
					{$product.cart_quantity-$quantityDisplayed}
				{/if}
			</span>
        {else}
        {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
        <span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
        {/if}
        {if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}

        <input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}_hidden" />
        <input size="2" type="text" autocomplete="off" class="cart_quantity_input form-control grey" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" />
        <div class="cart_quantity_button clearfix">
            {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
            <a rel="nofollow" class="cart_quantity_down btn btn-default button-minus" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
                <span><i class="icon-minus"></i></span>
            </a>
            {else}
            <a class="cart_quantity_down btn btn-default button-minus disabled" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
                <span><i class="icon-minus"></i></span>
            </a>
            {/if}
            <a rel="nofollow" class="cart_quantity_up btn btn-default button-plus" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
        </div>
        {/if}
        {/if}
    </td>
    <td class="cart_total" data-title="{l s='Total'}">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
				{else}
					{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
				{/if}
			{/if}
		</span>
    </td>
    {if !isset($noDeleteButton) || !$noDeleteButton}
    <td class="cart_delete text-center" data-title="Delete">
        {if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
        <div>
            <a rel="nofollow" title="{l s='Delete'}" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a>
        </div>
        {else}

        {/if}
    </td>
    {/if}
</tr>