{if $no_funny_business}
<p><em class='speech'>No funny business now...</em> the shopkeeper says, eyeing you suspiciously.
{elseif $current_item_cost gt $gold}
<p><em class='speech'>The total comes to {$current_item_cost|number_format:0|escape} gold,</em> the shopkeeper tells you.</p>
<p>Unfortunately, you do not have that much gold.</p>
{else}
<p class='obtained-item'>The shopkeeper hands over {$quantity|escape} {$item_text}.</p>
<p>As he puts your gold into his safe he asks you, <em class='speech'>Will you be needing anything else today?</em>.</p>
{/if}
