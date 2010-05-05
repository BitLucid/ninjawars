Thief sees you and prepares to defend!<br><br>
<img src="images/characters/thief.png" alt="Thief">
{if $victory}
	{if $thief_attack gt 30}
Thief escaped and stole {$thief_gold} pieces of your gold!
	{else}
The Thief is injured!<br>
Thief does {$thief_attack} points of damage!<br>
You have gained {$thief_gold} gold.<br>
You have found a Shuriken on the thief!
	{/if}
<br>
Beware the Ninja Thieves, they have entered this world to steal from all!<br>
{else}
Thief has slain you!<br>
{/if}
