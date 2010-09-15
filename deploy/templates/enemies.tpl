<h1>Combat</h1>

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
<div style='width:55%;float:left;margin-left:0;margin-right:0'>
<h3>Enemies</h3>
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
  <li class="{$status_class}" style='position:relative;margin-bottom:.2em'>
    <a href="enemies.php?remove_enemy={$loop_enemy_id|escape}"><img src="{$templatelite.const.IMAGE_ROOT}icons/delete.png" alt="remove"></a>
    {$action} <a href="player.php?player_id={$loop_enemy_id|escape}">{$loop_enemy.uname|escape}</a>
	<span style='margin-left:2em;width:10em;display:inline-block;position:absolute;right:2em;'>{include file="health_bar.tpl" health=$loop_enemy.health health_percent=$loop_enemy.health_percent}</span>
  </li>
		{/if}
	{/foreach}
</ul>
</div>
{else}
<p style='width:55%;float:left;margin-left:0;margin-right:0'>You haven't decided who your enemies are yet, <a href="list_all_players.php" target="main">find some</a>.</p>
{/if}


{if count($peers) gt 0}
<div style='width:45%;float:right;margin-left:0;margin-right:0'>
<h3>Competitors</h3>
<ul id='peer-chars'>
    {foreach from=$peers item="loop_peer"}
        <li style='position:relative;margin-bottom:.5em'>
            <a href='player.php?player_id={$loop_peer.player_id}' target='main'>
                {$loop_peer.uname}
            </a>
        	<span style='margin-left:2em;width:10em;display:inline-block;position:absolute;right:2em;'>{include file="health_bar.tpl" health=$loop_peer.health health_percent=$loop_peer.health_percent}</span>
            <!-- (level {$loop_peer.level}) -->
        </li>
    {/foreach}
</ul>
</div>
{else}
<p style='width:45%;float:right;margin-left:0;margin-right:0;'>No nearby enemies, <em class='char-name'>{$username}</em>.</p>
{/if}


{if count($recent_attackers) gt 0}
	{include file="enemies-recent-attackers.tpl" recent_attackers=$recent_attackers}
{/if}

{include file="player_list.active.tpl" active_ninja=$active_ninjas}
