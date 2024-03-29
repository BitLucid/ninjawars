
	<div class='shop-list'>
		{foreach from=$items item="item_info" key="item_internal_name"}
		<div class='item-purchase-area'>
				<button name="item" id='item-{$item_internal_name}' type="submit" value="{$item_info.item_internal_name|escape}" class="btn btn-default btn-lg">{if $item_info.image}<img class='item-icon' src="/images/items/{$item_info.image}" alt="{$item_info.item_display_name}">{/if} {$item_info.item_display_name|escape}</button>
		<div class='gold'>
			<label for='item-{$item_internal_name}'>石{$item_info.item_cost}</label>
		</div>
		<ul class='item-balance-details'>
			<li>For sale: {if $item_info.for_sale eq 1}Yes{else}No{/if}</li>
			<li>Goes Through stealth: {if $item_info.ignore_stealth eq 1}Yes{else}No{/if}</li>
			<li>Item type: {if isset($item_info.item_type)}{$item_info.item_type}{else}None{/if}</li>
			<li>Covert: {if $item_info.covert eq 1}Yes{else}No{/if}</li>
			<li>Turn cost: {$item_info.turn_cost}</li>
			<li>Target damage: {$item_info.target_damage}</li>
			<li>Turn change: {$item_info.turn_change}</li>
			<li>Self use: {if $item_info.self_use eq 1}Yes{else}No{/if}</li>
			<li>Plural: {if $item_info.plural eq 1}Yes{else}No{/if}</li>
			<li>Other usable: {if $item_info.other_usable eq 1}Yes{else}No{/if}</li>
			<li>Traits: {$item_info.traits}</li>
			<li>Stock: {$item_info.stock}</li>
			<li>Stock refresh rate: {$item_info.stock_refresh_rate} seconds</li>
			<li>Stock refresh amount: {$item_info.stock_refresh_amount}</li>
		</ul>
		<p class='usage-text'>
			<small class='item-desc'>{$item_info.usage}</small>
		</p>
		</div>
		{/foreach}
	</div>
