Merchant sees you and prepares to defend!<br><br>
<img src="images/characters/merchant.png" alt="Merchant">
{if $victory}
The merchant is defeated.<br>
The Merchant did {$merchant_attack} points of damage.<br>
You have gained {$merchant_gold} gold.<br>
	{if $merchant_attack gt 34}
The Merchant has dropped a Fire Scroll. You have a new Fire Scroll in your inventory.<br>
	{/if}
	{if $added_bounty}
You have slain a member of the village!  A bounty of {$added_bounty} gold has been placed on your head!<br>
	{/if}
{else}
<p>The Merchant has slain you!</p>
{/if}
