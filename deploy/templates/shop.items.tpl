<style>
{literal}
.item-purchase-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.item-purchase-list .item-purchase-area {
    min-height: 5vh;
    background:#1c1a13;
    border: medium solid black;
}
{/literal}
</style>

<section class='item-purchase-list'>
    {foreach from=$item_costs item="item_info" key="item_internal_name"}
    <div class='item-purchase-area'>
        <div>
            <button name="item" id='item-{$item_internal_name}' type="submit" value="{$item_info.item_internal_name|escape}"
                class="btn btn-default btn-lg" {if $gold < $item_info.item_cost}disabled{/if}>{if $item_info.image}<img
                class='item-icon' src="/images/items/{$item_info.image}" alt="{$item_info.item_display_name}">{/if}
            {$item_info.item_display_name|escape}</button>
        </div>
        <div class='item-meta'>
            <p class='usage-text'>
                <small class='item-desc'>{$item_info.usage}</small>
            </p>
            <div class='gold'>
                <label for='item-{$item_internal_name}'>石{$item_info.item_cost}</label>
            </div>
        </div>
    </div>
    {/foreach}
</section>
