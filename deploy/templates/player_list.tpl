<h1>Ninja List</h1>

{if $searched}
<div>
	Searching for: {$searched|escape} <a href="{$WEB_ROOT}list_all_players.php">(Clear Search)</a>
</div>
{/if}

<div id='player-list'>
	{if $ninja_count eq 0}
	<!-- Search found nothing to display -->
	<p class='notice'>No ninja to display.</p>
	<p><a href="list_all_players.php?hide={$hide}">Back to Ninja List</a></p>
	{/if}
	
	{$search_form}
	
	<!-- The player list navigation section -->
	{$player_list_nav}
	<!-- Active Lurker List -->
	{$active_ninja}

	<!-- Table header -->
	<table class="playerTable outer-table">
	  <tr class='playerTableHead'>
		<th>Rank</th><th>Name</th><th>Level</th><th>Class</th><th>Clan</th>
	  </tr>
	  <!--  Loop over and display each of the players in a table row format -->
	  
	  
	  
{foreach from=$ninja_rows key=row item=ninja}
		<!-- Darken row if dead, change a little on odd vs. even -->
		<tr class="playerRow {$ninja.alive_class} {$ninja.odd_or_even}">
		  <td class="playerCell rankCell">{$ninja.player_rank}</td>
		  <td class="playerCell nameCell">
		  	<a href="player.php?player_id={$ninja.player_id|escape:"url"}">{$ninja.uname|escape}</a>
		  </td>
		  <!-- Level category as a static resource -->
		  <td class="playerCell levelCell">
		  	<span class='{$ninja.level_cat_css}'>{$ninja.level_cat} [{$ninja.level}]</span>
		  </td>
		  <td class="playerCell classCell">
		    <!-- Display an image of the right colored shuriken. -->
		    <span class='{$ninja.class}'><img style='width:20px;height:17px' src='{$WEB_ROOT}images/small{$ninja.class}Shuriken.gif' alt=''>
		      {$ninja.class}
		    </span>
		  </td>
		  <td class="playerCell clanCell">
		    {if $ninja.clan_id}<a href='clan.php?command=view&amp;clan_id={$clan_id|escape:"url"}'>{/if}{$ninja.clan_name|escape}{if $ninja.clan_id}</a>{/if}
		  </td>
		</tr>
		<!-- Location to display the player profile content
		<tr class='profile' style='display:none'>
		</tr>
		-->
{/foreach}
	  
	</table><!-- End the player table -->

	<!-- Display the nav again -->
	{$player_list_nav}
   </div> <!-- End of player list -->
