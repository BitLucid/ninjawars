

		<!-- Darken row if dead, change a little on odd vs. even -->
		<tr class="playerRow {$alive_class} {$odd_or_even}">
		  <td class="playerCell rankCell">{$player_rank}</td>
		  <td class="playerCell nameCell">
		  	<a href="player.php?player_id={$player_id|escape:"url"}&amp;linkbackpage={$page}">{$uname|escape}</a>
		  </td>
		  <!-- Level category as a static resource -->
		  <td class="playerCell levelCell">
		  	<span class='{$level_cat_css}'>{$level_cat} [{$level}]</span>
		  </td>
		  <td class="playerCell classCell">
		    <!-- Display an image of the right colored shuriken. -->
		    <span class='{$class}'><img src='{$WEB_ROOT}images/small{$class}Shuriken.gif' alt=''>
		      {$class}
		    </span>
		  </td>
		  <td class="playerCell clanCell">
		    {if $clan ne "-"}<a href='clan.php?command=view&amp;clan_long_name={$clan|escape:"url"}'>{/if}{$clan|escape}{if $clan ne "-"}</a>{/if}
		  </td>
		</tr>
		<!-- Location to display the player profile content
		<tr class='profile' style='display:none'>
		</tr>
		-->
