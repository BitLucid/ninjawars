The Guard sees you and prepares to defend!<br><br>
<img src="images/characters/guard.png" alt="Guard">
{if $victory}
The guard is defeated!<br>
Guard does {$attack} points of damage.<br>
You have gained {$gold} gold.<br>
	{if $bounty}
You have slain a member of the military! A bounty of {$bounty} gold has been placed on your head!<br>
	{/if}
{else}
The Guard has slain you!<br>
{/if}
