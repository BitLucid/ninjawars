<p>The villager sees you and prepares to defend!</p>
{if $just_villager}
	{assign var="img_src" value="images/characters/fighter.png"}
	{assign var="img_alt" value="Villager"}
{else}
	{assign var="img_src" value="images/characters/ninja.png"}
	{assign var="img_alt" value="Ninja"}
{/if}
<img src="{$img_src}" alt="{$img_alt}">
{if $victory}
<p>The villager is no match for you!</p>
Villager does {$attack} points of damage.<br>
You have gained {$gold} gold.<br>
	{if $level gt 20}
You slay the villager easily, leaving no trace behind!<br>
	{elseif $bounty}
You have unjustly slain a commoner! A bounty of {$bounty} gold has been placed on your head!<br>
	{/if}
	{if !$just_villager}
<p>The villager dropped a Shuriken.</p>
	{/if}
<a href="attack_npc.php?attacked=1&amp;victim=villager">Attack Another Villager</a>
<br>
{else}
<p>The villager has slain you!</p>
{/if}
