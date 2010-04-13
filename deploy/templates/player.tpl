{if $message}
    <div id='message-sent' class='ninja-notice'>Message sent</div>
{/if}

{literal}
<style type='text/css'>
label{
    color:cornflowerblue;
}
</style>

<script type='text/javascript'>
$('#kick_form').submit(function(){return confirm('Are you sure you want to kick this player?');});
</script>
{/literal}

<div class='player-info'>

    <h1 class='player-name'>{$player}</h1>

    {$ranking_link_section}
		
	<div class='player-titles centered'>

	{$avatar_section}
    {$class_section}
	{$level_and_category}

	{$status_section}
	
	</div>

    {if !$self}
    <table id='player-profile-table'>
        <tr>
        {if $attack_error}
		    <td><div class='ninja-error centered'>Cannot Attack: {$attack_error}</div></td>
        </tr></table>
		{else}
		    <td colspan='2'>
		      <table id='player-profile-attack'>
		        <tr>
		          <td id='attacking-choices'>
			        <form id='attack_player' action='attack_mod.php' method='post' name='attack_player.php'>
			          <span id='duel'>
                              <label>Duel<input id="duel" type="checkbox" name="duel"></label>
                      </span>

            {if $skills_available.blaze}
				      <span id='blaze'>
                              <label>Blaze<input id="blaze" type="checkbox" name="blaze"></label>
                            </span>
			{/if}

            {if $skills_available.deflect}
				      <span>
                              <label>Deflect<input id="deflect" type="checkbox" name="deflect"></label>
                            </span>
            {/if}

			          <input id="target" type="hidden" value="{$target}" name="target" title='Attack or Duel this ninja'>
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
    		      </table>
    		  </td>
    		</tr>
    	   </table>
    		      <div id='skills-section'>
                        <ul id='skills-use-list'>
                        
            {$skill_use_section}
            
                    </ul>
                    </div>
	    {/if} <!-- End of the attacking-had-no-errors section -->

	{/if} <!-- End of the "not self" viewing section -->

	{$player_activity_section}

    {$set_bounty_section}
    {$communication_section}

    <!-- Clan leader options on players in their clan. -->
    {$clan_options_section}
    
    <!-- Player clan and clan members -->

	{$player_clan_section}
	
    {$player_profile_message}

	</div><!-- End player-info -->
