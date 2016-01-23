<style>
.oni figure{
	border:thin solid rgba(55, 55, 55, 0.5);padding:1em;margin-bottom:0.5em;text-align:center;
}
.oni .npc-image{
	max-width:450px;
}
</style>
<div class='oni glassbox'>
	<figure><img class='npc-image' src='{cachebust file="/images/scenes/Oni_pelted_by_beans.jpg"}'></figure>
	<div class='ninja-error'>An Oni attacks you as you wander!</div>
	<p>
	  The Oni eats some of your soul before {if $victory}you kill it{else}it escapes into the wilderness{/if}.
	{if $victory}
	  <br>
	  The Oni's body bursts into flame upon death. A {if $multiple_rewards}pile of {$item->getPluralName()} lie{else}{$item->getName()} lies{/if} in the ashes...
	{/if}
	</p>
</div>