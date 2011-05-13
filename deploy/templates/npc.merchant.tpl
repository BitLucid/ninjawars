Merchant sees you and prepares to defend!<br><br>
<img src="images/characters/merchant.png" alt="Merchant">
{if $victory}
The merchant is defeated.<br>
The Merchant did {$attack} points of damage.<br>
You have gained {$gold} gold.<br>
	{if $attack gt 34}
The Merchant has dropped a Fire Scroll. You have a new Fire Scroll in your inventory.<br>
	{/if}
	{if $bounty}
You have slain a member of the village! A bounty of {$bounty} gold has been placed on your head!<br>
	{/if}
<a href="npc.php?attacked=1&amp;victim=merchant" class='attack-again'>Attack Another Merchant</a>
<br>
{else}
<p>The Merchant has slain you!</p>
{/if}
