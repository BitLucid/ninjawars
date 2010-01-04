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
	  {$player_rows}
	</table><!-- End the player table -->

	<!-- Display the nav again -->
	{$player_list_nav}
   </div> <!-- End of player list -->
