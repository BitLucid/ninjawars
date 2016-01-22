Thief sees you and prepares to defend!<br><br>
<img src="{cachebust file="/images/characters/thief.png"}" alt="Thief">
{if $victory}
	{if $attack gt 30}
Thief escaped and stole {$gold} pieces of your gold!
	{else}
The Thief is injured!<br>
Thief does {$attack} points of damage!<br>
You have gained {$gold} gold.<br>
		{if $attack lt 30}
You have found a Shuriken on the thief!
		{/if}
	{/if}
<br>
Beware the Ninja Thieves, they have entered this world to steal from all!<br>
<a href="/npc/attack/thief" class='attack-again'>Attack Another Thief</a>
<br>
{else}
Thief has slain you!<br>
{/if}
