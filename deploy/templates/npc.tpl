<h1>Fight!</h1>
{if $turns lte 0}
{* These should be real error conditions, not part of the template *}
You have no turns left today. Buy a amanita mushroom or wait for your turns to replenish.
{elseif $attacked == 1}
	{include file=$npc_template}
{/if}
<nav>
{if !$health}
    <p class='ninja-notice death'>
      You are a ghost. You must resurrect before you may act again. Go to the <a href='shrine.php' style='font-size:2em'>shrine</a> for the monks to bring you back to life, or <a href='shrine_mod.php?heal_and_resurrect=1' style='font-size:2em'>heal fully</a>.
    </p>
{else}
<a href="enemies.php" class='return-to-location block'>Fight something else</a>
{/if}
</nav>
