<h1>Combat</h1>

<div id="ninja-enemy">
  Search for ninja:
  <form id="enemy-add" action="enemies.php" method="get" name="enemy_add">
    <input id='enemy-match' type="text" maxlength="50" name="enemy_match" class="textField">
    <input type="submit" value="Find Enemies" class="formButton">
  </form>    
</div>

<script type='text/javascript'>
{literal}
	$(document).ready(function(){
	
		// Function to display the matches.
		NW.displayMatches = function(json_matches){
			var sample = $('#sample-enemy-match');
			//NW.debug(json_matches);
			if(typeof(json_matches.char_matches) != 'undefined'){
				// Make this remove instead of just hiding.
				$('#ninja-matches .enemy:not(#sample-enemy-match)').remove();
				// Take the matches, extract them into individuals.
				for(var i in json_matches.char_matches){
					var clone = sample.clone().attr('id', 'enemy-match-'+i);
					var match = json_matches.char_matches[i];
					//NW.debug(match);
					var link = clone.find('a');
					//NW.debug(sample);
					// For each individual, extend the default link to make an attack link.
					var newlink = link.attr('href')+match.uname;
					// Add the new ones back on after the sample.
					sample.after(link.attr('href',newlink).text(match.uname).end().show());
				}
			}
		};
		
		
		var searchbox = $('#enemy-match');
		searchbox.keyup(function () {
			NW.typewatch(function () {
				// executed only 500 ms after the last keyup event.
				var term = $('#enemy-match').val();
				if(term && term.length>2){
					// Only search after a few characters are typed out
					NW.charMatch(term, NW.displayMatches);
				}
			}, 500);
		});
		
	});
{/literal}
</script>


<div id='ninja-matches' style=''>
	<ul>
		<li id='sample-enemy-match' class='enemy' style='display:none'>
			Duel <strong class='char-name'><a class='char-name-link' href='/attack_mod.php?duel=1&target='>Someone</a></strong>
		</li>
	</ul>
</div>

{if $found_enemies && count($found_enemies) gt 0}
	{include file="enemy-matches.tpl" enemies=$found_enemies}
{elseif $match_string}
<div>
  Your search returned no ninja. maybe you should make an enemy of someone who recently attacked you.
	{include file="enemy-matches.tpl" enemies=$recent_attackers}
</div>
{/if}

{if $enemyCount gt 0}
<div style='width:55%;float:left;margin-left:0;margin-right:0'>
  <h3>Enemies</h3>
  <ul>
	{foreach from=$enemy_list item="loop_enemy"}
		{if $loop_enemy.active}
			{if $loop_enemy.health gt 0}
				{assign var="status_class" value=""}
				{assign var="action" value="Attack"}
			{else}
				{assign var="status_class" value="enemy-dead"}
				{assign var="action" value="View"}
			{/if}
    <li class="{$status_class}" style='position:relative;margin-bottom:.2em;'>
      <a href="enemies.php?remove_enemy={$loop_enemy.player_id|escape}"><img src="{$smarty.const.IMAGE_ROOT}icons/delete.png" alt="remove"></a>
      <span style='display:inline-block;width: 16em;'>{$action} <a href="player.php?player_id={$loop_enemy.player_id|escape}">{$loop_enemy.uname|escape}</a></span>
      <span style='display:inline-block;margin-left:1em;width: 5.9em;'>
        {include file="health_bar.tpl" health=$loop_enemy.health health_percent=$loop_enemy.health_percent}
      </span>
    </li>
		{/if}
	{/foreach}
  </ul>
</div>
{else}
<p style='width:55%;float:left;margin-left:0;margin-right:0'>You haven't decided who your enemies are yet, <a href="list.php" target="main">find some</a>.</p>
{/if}

{if count($peers) gt 0}
<div style='width:45%;float:right;margin-left:0;margin-right:0'>
  <h3>Nearby Ninja</h3>
  <ul id='peer-chars'>
	{foreach from=$peers item="loop_peer"}
    <li style='position:relative;margin-bottom:.5em'>
       <a style='width:10em;display:inline-block;' href='player.php?player_id={$loop_peer.player_id}' target='main'>{$loop_peer.uname}</a>
		{if $char_info.health}
       <span style='margin-left:2em;width:6em;display:inline-block;'>
         {include file="health_bar.tpl" health=$loop_peer.health health_percent=$loop_peer.health_percent}
       </span>
<!-- (level {$loop_peer.level}) -->
		{/if}
    </li>
    {/foreach}
  </ul>
</div>
{else}
<p style='width:45%;float:right;margin-left:0;margin-right:0;'>No nearby ninja, <em class='char-name'>{$username|escape}</em>.</p>
{/if}

{if count($recent_attackers) gt 0}
	{include file="enemies-recent-attackers.tpl" recent_attackers=$recent_attackers}
{/if}

{include file="list.active.tpl" active_ninja=$active_ninjas}
