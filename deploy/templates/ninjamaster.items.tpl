	<div class='shop-list'>
		{foreach from=$items item="item_info" key="item_internal_name"}
	<div class='item-purchase-area'>
			<button name="item" id='item-{$item_internal_name}' type="submit" value="{$item_info.item_internal_name|escape}" class="btn btn-default btn-lg">{if $item_info.image}<img class='item-icon' src="/images/items/{$item_info.image}" alt="{$item_info.item_display_name}">{/if} {$item_info.item_display_name|escape}</button>
	  <div class='gold'>
	    <label for='item-{$item_internal_name}'>石{$item_info.item_cost}</label>
	  </div>
	  <p class='usage-text'>
	    <small class='item-desc'>{$item_info.usage}</small>
	  </p>
	</div>
		{/foreach}
	</div>