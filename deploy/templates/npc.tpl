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
{if !$ninja}
    <div class='centered'>
        <p class='ninja-error glassbox'>
            You are not a ninja. You may not fight. </br>
            <a href='/signup' class='large'><button class='btn-primary'>New Game</button></a>
        </p>
    </div>
{elseif $turns lte 0}
{* These should be real error conditions, not part of the template *}
<div class='glassbox centered'>
    <span class='ninja-error'>
        You have no turns left today. <br /> Buy an amanita mushroom or wait for your turns to replenish.
    </span>
</div>
{elseif $attacked == 1}
	{include file=$npc_template}
{/if}
