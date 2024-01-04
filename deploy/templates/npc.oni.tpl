<style>
.oni figure{
	border:thin solid rgba(55, 55, 55, 0.5);padding:1em;margin-bottom:0.5em;text-align:center;
}
.oni .npc-image{
	max-width:450px;
}
/* Combat text styling */
.combat-text {
  font-size: 16px;
  line-height: 1.6;
  margin-top: 10px;
}

/* Danger text styling for ninja errors */
.danger-text {
  color: #ff5757; /* Reddish color for danger */
  font-weight: bold;
  font-size: 18px;
  text-shadow: 1px 1px 3px #000; /* Shadow for a dramatic effect */
}

/* Victory text styling */
.combat-text b {
  color: #5fff5f; /* Greenish color for victory */
}

/* Item styling */
.combat-text em {
  color: #9fd8ff; /* Bluish color for items */
}

/* Container styling */
.encounter-container {
  padding: 20px;
  border: 1px solid #444;
  border-radius: 5px;
  background-color: #450c0ca8;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  display:flex;
  justify-content: center;
  align-items: center;
  height: 20vh;
}
.encounter-container .encounter-content{
	margin: 0;
	padding: 3rem;
}
</style>
<div class='oni glassbox'>
	<figure>
		<img class='npc-image' src='{cachebust file="/images/characters/attacking_wandering_oni.jpg"}'>
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
				{if $multiple_rewards}a trove of {$item->getPluralName()}{else}a valuable {$item->getName()}{/if}.
			{/if}
			</p>
		</div>
	</section>
</div>
