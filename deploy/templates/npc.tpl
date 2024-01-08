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
      You are a ghost <i class="fa-solid fa-ghost"></i>. You cannot not fight.
    </p>
    <nav>
        Go to the <a href='/shrine' class='large btn btn-default'><i class="fa-solid fa-torii-gate"></i> Shrine</a> for the monks to bring you back to life, or <a href='/shrine/heal_and_resurrect' class='large btn btn-default'>full heal</a>.
    </nav>
{else}
	<a href="/enemies" class='return-to-location block'>Fight something else</a>
{/if}
</nav>
{if !$ninja}
    <div class='centered'>
        <p class='ninja-error glassbox'>
            You are not a ninja. You may not fight.
        </p>
        <nav>
            <a href='/signup' class='large'>
                <button class='btn-primary'>New Game</button>
            </a>
        </nav>
    </div>
{elseif $turns lte 0}
{* These should be real error conditions, not part of the template *}
<div class='glassbox centered'>
    <span class='ninja-error'>
        You have no turns left today.
    </span>
    <p>
        Buy an amanita mushroom or wait for your turns to replenish.
    </p>
</div>
{elseif $attacked == 1}
	{include file=$npc_template}
{/if}
