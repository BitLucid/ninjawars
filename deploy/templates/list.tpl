<h1>Ninja List</h1>

{if $searched}
<div>
  Searching for: {$searched|escape} <a href="{$smarty.const.WEB_ROOT}list.php">(Clear Search)</a>
</div>
{/if}

<div id='player-list'>
{if $ninja_count eq 0}
  <!-- Search found nothing to display -->
  <p class='notice'>No ninja to display.</p>
  <p><a href="list.php?hide={$hide|escape:'url'}">Back to Ninja List</a></p>
{/if}

  <div class='list-all-players-search centered'>
    <form action="list.php" method="get">
      <div>
        <input type="text" name="searched" class='textField' style="font-family:Verdana, Arial;font-size:xx-small;">
        <input type="hidden" name="hide" value="{$hide|escape}">
        <button type='submit' class='formButton' value='1'>Search for Ninja</button>

{if !$searched}
       <a href="list.php?page={$page|escape:'url'}&amp;hide={if $hide == "dead"}none{else}dead{/if}&amp;searched={$searched|escape:'url'}">
         ({if $hide == "dead"}Show{else}Hide{/if} {$dead_count} dead ninja)
       </a>
{/if}

     </div>
   </form>
  </div>

  <!-- The player list navigation section -->
{include file='list.nav.tpl'}

  <!-- Active Lurker List -->
{if $active_ninjas}
	{include file='list.active.tpl' active_ninja=$active_ninjas}
{/if}

  <!-- Table header -->
  <table class="playerTable outer-table">
	  <tr class='playerTableHead'>
		<th>Rank</th><th>Name</th><th>Level</th><th>Class</th><th>Clan</th>
	  </tr>
	  <!--  Loop over and display each of the players in a table row format -->


{foreach from=$ninja_rows key=row item=ninja}
		<!-- Darken row if dead, change a little on odd vs. even -->
		<tr class="playerRow {$ninja.alive_class} {$ninja.odd_or_even}">
		  <td class="playerCell rankCell">{$ninja.player_rank|escape}</td>
		  <td class="playerCell nameCell">
		  	<a href="player.php?player_id={$ninja.player_id|escape:"url"}" target='main'>{$ninja.uname|escape}</a>
		  </td>
		  <!-- Level category as a static resource -->
		  <td class="playerCell levelCell">
		  	<span class='{$ninja.level_cat_css}'>{$ninja.level_cat|escape} [{$ninja.level|escape}]</span>
		  </td>
		  <td class="playerCell classCell">
		    <!-- Display an image of the right colored shuriken. -->
		    <span class='{$ninja.class_theme}'><img style='width:20px;height:17px' src='{$smarty.const.WEB_ROOT}images/small{$ninja.class_theme|escape:'url'}Shuriken.gif' alt=''>
		      {$ninja.class|escape}
		    </span>
		  </td>
		  <td class="playerCell clanCell">
		    {if $ninja.clan_id}<a href='clan.php?command=view&amp;clan_id={$ninja.clan_id|escape:"url"}'>{/if}{$ninja.clan_name|escape}{if $ninja.clan_id}</a>{/if}
		  </td>
		</tr>
		<!-- Location to display the player profile content
		<tr class='profile' style='display:none'>
		</tr>
		-->
{/foreach}

	</table><!-- End the player table -->

	<!-- Display the nav again -->
{include file='list.nav.tpl'}
   </div> <!-- End of player list -->
