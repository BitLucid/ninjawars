<style>
{literal}
#ninja-matches .even{
	float:right; clear:right; padding-right:5em;
}
#enemies-stuff{
	background-color:black;
}
#current-enemies-list li em, #peer-chars li em{
	margin-left:.5em;color:#D21;display:inline-block; border-left:thick solid #D21; border-right:thick solid #D21; padding: 0 .2em;text-align:center;width:1.7em;
}
#current-enemies-list .enemy-stats-box{
	display:inline-block;margin-left:1em;width: 6.9em;
}
#current-enemies-list li{
	position:relative;margin-bottom:.7em;
}
#current-enemies-list .enemy-action-box{
	display:inline-block;width: 13em;
}
.enemies-lefthalf{
	width:55%;float:left;margin-left:0;margin-right:0;
}
.enemies-righthalf{
	width:45%;float:right;margin-left:0;margin-right:0;
}
#peer-chars .peer{
	position:relative;margin-bottom:.5em;
}
#peer-chars .peer-name, #current-enemies-list .enemy-name{
	width:10em;display:inline-block;overflow:hidden;text-overflow:ellipsis;
}
#peer-chars .peer-name{
	width:8.5em;
}
#peer-chars .stats-block{
	margin-left:2em;width:6.5em;display:inline-block;
}
#npc-list{
	margin: .5em auto;text-align:center;font-size:1.3em;
}
#npc-list .creature-image{
	max-width:50px;max-height:50px;
}
#more-matches{
	clear:both;text-align:center;display:none;
}
{/literal}
</style>

<h1>Combat</h1>

<div id="ninja-enemy">
  Search for ninja:
  <form id="enemy-add" action="enemies.php" method="get" name="enemy_add">
    <input id='enemy-match' type="text" maxlength="50" name="enemy_match" class="textField">
    <input type="submit" value="Find Enemies" class="formButton">
  </form>    
</div>

<!-- Js at bottom -->


<div id='ninja-matches' class='cf'>
	<ul>
		<li id='sample-enemy-match' class='enemy' style='display:none'>
			Duel <strong class='char-name'><a class='char-name-link' href='/attack_mod.php?duel=1&target='>Someone</a></strong>
		</li>
	</ul>
	<div id='more-matches'>
		...with more live matches...
	</div>
	<br style='clear:both'>
</div>

{if $found_enemies && count($found_enemies) gt 0}
	{include file="enemy-matches.tpl" enemies=$found_enemies}
{elseif $match_string}
<div>
  Your search returned no ninja. maybe you should make an enemy of someone who recently attacked you.
	{include file="enemy-matches.tpl" enemies=$recent_attackers}
</div>
{/if}

<section id='enemies-stuff' class='clearfix'>
{if $enemyCount gt 0}
<div class='enemies-lefthalf'>
  <h3>Enemies</h3>
  <ul id='current-enemies-list'>
	{foreach from=$enemy_list item="loop_enemy"}
		{if $loop_enemy.active}
			{if $loop_enemy.health gt 0}
				{assign var="status_class" value=""}
				{assign var="action" value="Attack"}
			{else}
				{assign var="status_class" value="enemy-dead"}
				{assign var="action" value="View"}
			{/if}
    <li class="{$status_class}">
      <a href="enemies.php?remove_enemy={$loop_enemy.player_id|escape}"><img src="{$smarty.const.IMAGE_ROOT}icons/mono/stop32.png" height='16' width='16' alt="remove" title='Remove'></a>
      <span class='enemy-action-box'>{$action} <a class='enemy-name' title='View {$loop_enemy.uname|escape} to attack them' href="player.php?player_id={$loop_enemy.player_id|escape}">{$loop_enemy.uname|escape}</a></span>
      <span class='enemy-stats-box'>
        {include file="health_bar.tpl" health=$loop_enemy.health health_percent=$loop_enemy.health_percent}
      </span>
      <em title='Level {$loop_enemy.level}'>{$loop_enemy.level}</em>
    </li>
		{/if}
	{/foreach}
  </ul>
</div>
{else}
<p class='enemies-lefthalf'>You haven't decided who your enemies are yet, <a href="list.php" target="main">find some</a>.</p>
{/if}

{if count($peers) gt 0}
<div class='enemies-righthalf'>
  <h3>Nearby Ninja</h3>
  <ul id='peer-chars'>
	{foreach from=$peers item="loop_peer"}
    <li class='peer'>
       <a class='peer-name' title='View {$loop_peer.uname|escape} to attack them' href='player.php?player_id={$loop_peer.player_id}' target='main'>{$loop_peer.uname|escape}</a>
		{if $char_info.health}
       <span class='stats-block'>
         {include file="health_bar.tpl" health=$loop_peer.health health_percent=$loop_peer.health_percent}
       </span>
<!-- (level {$loop_peer.level}) -->
		{/if}
       	<em title='Level {$loop_peer.level}'>{$loop_peer.level}</em>
    </li>
    {/foreach}
  </ul>
</div>
{else}
<p class='enemies-righthalf'>No nearby ninja, <em class='char-name'>{$username|escape}</em>.</p>
{/if}
</section><!-- End of clearfix section -->

{if count($recent_attackers) gt 0}
	{include file="enemies-recent-attackers.tpl" recent_attackers=$recent_attackers}
{/if}

<!-- Display recently active ninja -->
{include file="list.active.tpl" active_ninja=$active_ninjas}

<section id='npc-list-section'>
  <h3>Attack a:</h3>
  <ul id='npc-list'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <li><a href='npc.php?attacked=1&amp;victim={$npc.identity|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}' style='width:25px;height:46px'> {$npc.name|escape}</a></li>
{/foreach}
{foreach name="creatures" from=$other_npcs key="idx" item="npc"}
      <li><a href='npc.php?attacked=1&amp;victim={$idx|escape}' target='main'>
      	{if isset($npc.img) && $npc.img}
      	<img alt='' class='creature-image' src='images/characters/{$npc.img|escape:'url'|escape}'>
      	{else}<span style='width:25px;height:46px'>&#9733;</span>
      	{/if} 
      	{$npc.name|escape}</a></li>
{/foreach}
  </ul>
</section>

<script type='text/javascript'>
{literal}
	$(document).ready(function(){
	
		// Function to display the matches.
		NW.displayMatches = function(json_matches){
			var sample = $('#sample-enemy-match');
			var moreMatches = $('#more-matches');
			//NW.debug(json_matches);
			if(typeof(json_matches.char_matches) != 'undefined'){
				// Remove all li's not preceded by an li.
				$('#ninja-matches li+li').remove();
				// Take the matches, extract them into individuals.
				var inc = 0;
				for(var i in json_matches.char_matches){
					if(inc>9){
						break;
					}
					var clone = sample.clone().attr('id', 'enemy-match-'+i);
					if(i%2 == 1){ // Classify the even entries (here 0, 2, 4, etc)
						clone.addClass('even');
					}
					var match = json_matches.char_matches[i];
					//NW.debug(match);
					var link = clone.find('a');
					//NW.debug(sample);
					// For each individual, extend the default link to make an attack link.
					var newlink = link.attr('href')+match.uname;
					// Add the new ones back on after the sample.
					sample.after(link.attr('href',newlink).text(match.uname).end().show());
					inc++;
				}
				if(json_matches.char_matches.length > 9){
					moreMatches.show(); // Show the "with more matches" section.
				} else {
					moreMatches.hide();
				}
			}
		};
		
		
		var searchbox = $('#enemy-match');
		searchbox.keyup(function () {
			NW.typewatch(function () {
				// executed only 500 ms after the last keyup event.
				var term = $('#enemy-match').val();
				var limit = 11; // Limit to 11, and only display 10.
				if(term && term.length>2){
					// Only search after a few characters are typed out
					NW.charMatch(term, limit, NW.displayMatches);
				}
			}, 500);
		});
		
	});
{/literal}
</script>

