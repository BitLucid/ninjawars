<h1>Fight!</h1>
{if $turns lte 0}
{* These should be real error conditions, not part of the template *}
You have no turns left today. Buy a amanita mushroom or wait for your turns to replenish.
{elseif $attacked == 1}
<em class='fancy-ornamental-font'>Attacking...</em>
{include file=$npc_template}
{/if}
<nav>
{if !$health}
<p class="ninja-notice">Go to the <a href="shrine.php" class='shrine-link'>shrine</a> to resurrect.</p>
{else}
<a href="map.php" class='return-to-location block'>Return to the Village</a>
{/if}
</nav>
