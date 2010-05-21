<img src="images/characters/samurai.png" alt="Samurai">
{if $attacker_level lt 6}
You are too weak to take on the Samurai.<br>
{elseif $attacker_kills lt 1}
You are too exhausted to take on the Samurai.<br>
{else}
The Samurai was waiting for your attack.<br><br>
The Samurai cuts you for {$samurai_damage_array.0} damage.<br>
The Samurai slashes you mercilessly for {$samurai_damage_array.1} damage.<br>
The Samurai thrusts his katana into you for {$samurai_damage_array.2} damage.<br>
	{if $victory}
You use an ancient ninja strike upon the Samurai, slaying him instantly!<br><br>
You have gained {$gold} gold.<br>
You gain a kill point.<br>
		{if $samurai_damage_array.2 gt 100}
The Samurai had a speed scroll on him. You have a new Speed Scroll in your inventory.
		{/if}
		{if $samurai_damage_array.3 eq $ninja_str*3}
You have gained a Dim Mak from the Samurai.<br>
		{/if}
	{else}
<br>The Samurai has slain you!<br>
	{/if}
{/if}
