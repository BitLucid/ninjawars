<style>
nav.npcs .death .large{
	font-size:larger;
}
nav.npcs{
	clear:both;margin-left:5em;
}
</style>

<h1>Fight!</h1>
<nav class='npcs'>
{if !$health}
    <p class='ninja-notice death'>
      You are a ghost. You must resurrect before you may act again. Go to the <a href='/shrine' class='large'>shrine</a> for the monks to bring you back to life, or <a href='/shrine/heal_and_resurrect' class='large'>heal fully</a>.
    </p>
{else}
	<a href="/enemies" class='return-to-location block'>Fight something else</a>
{/if}
</nav>
{if $turns lte 0}
{* These should be real error conditions, not part of the template *}
You have no turns left today. Buy a amanita mushroom or wait for your turns to replenish.
{elseif $attacked == 1}
	{include file=$npc_template}
{/if}
