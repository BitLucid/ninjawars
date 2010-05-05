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
Villager does {$villager_attack} points of damage.<br>
You have gained {$villager_gold} gold.<br>
	{if $attacker_level gt 20}
You slay the villager easily, leaving no trace behind!<br>
	{else if $added_bounty}
You have unjustly slain a commoner! A bounty of {$added_bounty} gold has been placed on your head!<br>
	{/if}
{else}
<p>The villager has slain you!</p>
{/if}
{if !$just_villager}
<p>The villager dropped a Shuriken.</p>
{/if}
