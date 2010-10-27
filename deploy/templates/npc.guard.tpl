The Guard sees you and prepares to defend!<br><br>
<img src="images/characters/guard.png" alt="Guard">
{if $victory}
The guard is defeated!<br>
Guard does {$attack} points of damage.<br>
You have gained {$gold} gold.<br>
	{if $herb}
The guard was carrying a good luck charm!<br>
	{/if}
	{if $bounty}
You have slain a member of the military! A bounty of {$bounty} gold has been placed on your head!<br>
	{/if}
<a href="npc.php?attacked=1&amp;victim=guard" class='attack-again'>Attack Another Guard</a>
<br>
{else}
The Guard has slain you!<br>
{/if}
