{if $message}
    <div id='message-sent' class='ninja-notice'>Message sent</div>
{/if}

<div class='player-info'>


    {$ranking_link_section}
	{$player_stats_section}
	{$avatar_section}

    <table id='player-profile-table'>
        <tr>
        {if $attack_error}
		    <td><div class='ninja-error centered'>Cannot Attack: {$attack_error}</div></td>
		{else}
            {if !$is_own_profile}
		    <td colspan='2'>
		      <table id='player-profile-attack'>
		        <tr>
		          <td id='attacking-choices'>
			        <form id='attack_player' action='attack_mod.php' method='post' name='attack_player.php'>
			          <span id='duel'>
                              <label><a href="#">Duel</a> <input id="duel" type="checkbox" name="duel"></label>
                      </span>

            {if $skills_available.Blaze}
				      <span id='blaze'>
                              <label><a href="#">Blaze</a><input id="blaze" type="checkbox" name="blaze"></label>
                            </span>
			{/if}

            {if $skills_available.Deflect}
				      <span>
                              <label><a href="#">Deflect</a><input id="deflect" type="checkbox" name="deflect"></label>
                            </span>
            {/if}

			          <input id="target" type="hidden" value="{$target}" name="target">
                            <label class='attack-player-trigger'>
                              <input class='attack-player-image' type='image' value='Attack'
                                 name='attack-player-shuriken' src='{$IMAGE_ROOT}50pxShuriken.png' alt='Attack' title='Attack'>
                              <a>Attack</a>
                            </label>
			        </form>
			      </td>

			<!-- Inventory Items -->
			      <td id='inventory-items'>

			{$item_use_section}

			      </td>
                      </tr>
                      <tr>
                        <td id='skills-section'>
                        
            {$skill_use_section}

			      </td>
			    </tr>
		      </table>
		    </td>
		{/if}
		<!-- End of the "viewing someone else's profile" section. -->
	{/if}
	    <!-- End of the attacking-had-no-errors section -->

	  </tr>

	</table>

	{$player_activity_section}

    {$set_bounty_section}
    {$communication_section}

    <!-- Clan leader options on players in their clan. -->
    {$clan_options_section}

    <!-- Player clan and clan members -->

	{$player_clan_section}

    {$player_profile_message}

	</div><!-- End player-info -->
