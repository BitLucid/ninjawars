<link href="/css/npcs.css" rel="stylesheet" type="text/css" />

<div class='encounter-overall oni'>
	<figure>
		<img class='npc-image' 
			src='{cachebust file="/images/characters/attacking_wandering_oni.jpg"}'>
	</figure>
	<section class='encounter-container'>
		<div class='encounter-content'>
			<h3>A Wandering Oni Attacks!</h3>
			<div class='centered'>
				<div class='danger-text'>⚔️ Beware! An Oni emerges from the shadows, 
				launching a ferocious attack upon you! ⚔️</div>
			</div>
			<p class='combat-text'>
			The Oni devours some fragments of your soul, leaving you weakened and 
		    vulnerable. {if $victory}But, in a swift and skilled move, 
			you manage to strike it down.{else}However, 
			it cunningly escapes into the dark wilderness, 
		    leaving you in uncertainty.{/if}
			{if $victory}
				<br>
				🔥 Victory is yours! The Oni's body ignites into flames, and amidst the ashes lies
				{if $multiple_rewards}a trove of <span class='obtained-item'>{$item->getPluralName()}</span>{else}a valuable <span class='obtained-item'>{$item->getName()}</span>{/if}.
			{/if}
			</p>
		</div>
	</section>
</div>
