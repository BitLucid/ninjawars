<h1>Fight</h1>
<link rel="stylesheet" type="text/css" href="{cachebust file="/css/enemies.css"}" media="Screen" />


{if $logged_in}

<section id='enemies-stuff' class='clearfix'>
{foreach $enemy_list as $loop_enemy}
    {if $loop_enemy@first}
<div class='enemies-lefthalf'>
  <h3>Enemies</h3>
  <ul id='current-enemies-list'>
    {/if}
    {if $loop_enemy.active}
        {if $loop_enemy.health gt 0}
            {assign var="status_class" value=""}
            {assign var="action" value="Attack"}
        {else}
            {assign var="status_class" value="enemy-dead"}
            {assign var="action" value="View"}
        {/if}
    <li class="{$status_class}">
      <span class='enemy-action-box'>{$action}&nbsp;<a class='enemy-name' title="View {$loop_enemy.uname|escape}'s info" href="/player?player_id={$loop_enemy.player_id|escape}">{$loop_enemy.uname|escape}</a></span>
      <span class='enemy-stats-box'>
        {include file="health_bar.tpl" health=$loop_enemy.health level=$loop_enemy.level}
      </span>
      <em title='Level {$loop_enemy.level}'>{$loop_enemy.level}</em>
      <form name='remove-enemy-form' id='remove-enemy-form' action="/enemies/delete" method='POST'>
        <input type='hidden' name='remove_enemy' value='{$loop_enemy.player_id|escape}'>
          <button type='submit' class='remove-enemy-button' title='Remove {$loop_enemy.uname|escape} from your hitlist'>
            <i class="fa fa-times-circle"></i>
          </button>
      </form>
    </li>
    {/if}
    {if $loop_enemy@last}
  </ul>
</div>
    {/if}
{foreachelse}
<p class='enemies-lefthalf'>You haven't decided who your enemies are yet{if $logged_in}, pick some below.{else}, become a ninja to get some enemies.{/if}</p>
{/foreach}

{if !empty($peers) && count($peers) gt 0}
<div class='enemies-righthalf'>
  <h3>Nearby Ninja</h3>
  <ul id='peer-chars'>
	{foreach from=$peers item="loop_peer"}
    <li class='peer'>
       <a class='peer-name' title='View {$loop_peer.uname|escape} to attack them' href='/player?player_id={$loop_peer.player_id}' target='main'>{$loop_peer.uname|escape}</a>
		{if $char_info.health}
       <span class='stats-block'>
         {include file="health_bar.tpl" health=$loop_peer.health level=$loop_peer.level}
       </span>
<!-- (level {$loop_peer.level}) -->
		{/if}
       	<em title='Level {$loop_peer.level}'>{$loop_peer.level}</em>
    </li>
    {/foreach}
  </ul>
</div>
{else}
<p class='enemies-righthalf'>No nearby ninja, <em class='char-name'>{$char->name()|escape}</em>.</p>
{/if}
</section><!-- End of clearfix section -->

<div id="ninja-enemy" class='solo-box'>
  <form id="enemy-add" action="/enemies/search" method="get" name="enemy_add">
    <div class='input-group'>
      <input id='enemy-match' required=required type="text" maxlength="50" name="enemy_match" class="form-control textField" placeholder='Search by ninja name' value='{if isset($enemy_match)}{$enemy_match}{/if}'>
      <span class='input-group-btn'>
        <input type="submit" value="Find Enemies" class="btn btn-default formButton">
      </span>
    </div>
  </form>
</div>
<!-- This hooks into quick-match js at bottom -->

{/if}{* End of login *}



<section id='ninja-matches' class='cf'>
	<ul>
		<li id='sample-enemy-match' class='enemy' hidden>
			Duel <strong class='char-name'><a class='char-name-link' href='/attack?duel=1&amp;target='>...</a></strong>
		</li>
	</ul>
	<div id='more-matches' class='hidden'>
		...with more live matches...
	</div>
	<br style='clear:both'>
{if !empty($found_enemies) && count($found_enemies) gt 0}
	{include file="enemy-matches.tpl" enemies=$found_enemies}
{elseif isset($enemy_match) && $enemy_match}
	<div class='hidden'>
	  Your search returned no ninja. maybe you should make an enemy of someone who recently attacked you.
		{include file="enemy-matches.tpl" enemies=$recent_attackers}
	</div>
{/if}
</section>

{* The single-next enemy attack interface *}
{include file="enemies.attack-next.tpl" enemy=$next_enemy self=$char_info}


{if !empty($recent_attackers)}
	{include file="enemies-recent-attackers.tpl" recent_attackers=$recent_attackers}
{/if}

  {include file='npc.list.tpl'}


<!-- Display recently active ninja -->
{* {include file="list.active.tpl" active_ninja=$active_ninjas} *}

<!--  Deactivating this functionality for now.
<script src='{cachebust file="/js/enemies.js"}'></script>
-->
