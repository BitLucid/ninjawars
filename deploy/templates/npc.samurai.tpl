<link href="/css/npcs.css" rel="stylesheet" type="text/css" />
<div class='glassbox'>
	<!-- floats with the text -->
	{include file="npc.samurai-image.tpl"}
	The Samurai was waiting for your attack.<br><br>
	The Samurai cuts you for <span class='damage danger-text'>{$samurai_damage_array.0}</span> damage.<br>
	The Samurai slashes you mercilessly for <span class='damage danger-text'>{$samurai_damage_array.1}</span> damage.<br>
	The Samurai thrusts his katana into you for <span class='damage danger-text'>{$samurai_damage_array.2} damage</span>.<br>
</div>
<div class='glassbox'>
	{if $victory}
		You use an ancient ninja strike upon the Samurai, <em>slaying him instantly!</em><br><br>
		You have gained <span class='gold'>{$gold} gold</span>.<br>
		<p class='reward-text'>You gain a kill point.</p>
		{if $drop}
			<p>You quickly snatch a small pouch containing 
			<span class='obtained-item'>{$drop_display}</span> 
			from the dead samurai's neck before vanishing.</p>
		{/if}
		<a href="/npc/attack/samurai" class='attack-again'>Attack Another Samurai</a>
		<br>
	{else}
		<br>The Samurai has slain you!<br>
	{/if}
</div>
