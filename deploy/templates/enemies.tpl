<h1>Enemies</h1>

{if $max_enemies neq true}
<div id="ninja-enemy">
  Search for ninja to add as enemies:
  <form id="enemy-add" action="enemies.php" method="get" name="enemy_add">
    <input type="text" maxlength="50" name="enemy_match" class="textField">
    <input type="submit" value="Find Enemies" class="formButton">
  </form>    
</div>
{/if}

{if $found_enemies && count($found_enemies) gt 0}
	{include file="enemy-matches.tpl" enemies=$found_enemies}
{/if}

{if count($enemy_list) gt 0}
<ul>
	{foreach from=$enemy_list item="loop_enemy" key="loop_enemy_id"}
		{if $loop_enemy.confirmed}
			{if $loop_enemy.health gt 0}
				{assign var="status_class" value=""}
				{assign var="action" value="Attack"}
			{else}
				{assign var="status_class" value="enemy-dead"}
				{assign var="action" value="View"}
			{/if}
  <li class="{$status_class}">
    <a href="enemies.php?remove_enemy={$loop_enemy_id|escape}"><img src="{$templatelite.const.IMAGE_ROOT}icons/delete.png" alt="remove"></a>
    {$action} <a href="player.php?player_id={$loop_enemy_id|escape}">{$loop_enemy.uname|escape}</a>
    ({$loop_enemy.health|escape} health)
  </li>
		{/if}
	{/foreach}
</ul>
{else}
<p>You haven't decided who your enemies are yet, <a href="list_all_players.php" target="main">find some</a>.</p>
{/if}

{if count($recent_attackers) gt 0}
	{include file="enemies-recent-attackers.tpl" recent_attackers=$recent_attackers}
{/if}

{include file="player_list.active.tpl" active_ninja=$active_ninjas}
