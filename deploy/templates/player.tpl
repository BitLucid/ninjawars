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
$().ready(function(){$('#kick_form').submit(function(){return confirm('Are you sure you want to kick this player?');});});
</script>
{/literal}

<div class='player-info'>

    <h1 class='player-name'>{$player}</h1>

    <div class='player-ranking-linkback'>
      <a href='list_all_players.php?searched={'#'|escape:'url'|escape}{$rank_spot|escape:'url'|escape}&amp;hide=none'><img src='{$templatelite.const.IMAGE_ROOT}return-triangle.png' alt='&lsaquo;Rank {$rank_spot|escape}' title='&lsaquo;Return to rank $rank_spot' style='width:50px;height:50px;float:left;'></a>
    </div>

	<div class='player-titles centered'>

	{include file="gravatar.tpl" url=$gravatar_url}

    <span class='player-class {$player_info.class|escape}'>
      <img id='class-shuriken' src='{$templatelite.const.IMAGE_ROOT}small{$player_info.class|escape:'url'|escape}Shuriken.gif' alt=''>
      {$player_info.class|escape}
    </span>

    <span class='player-level-category {$level_category.css|escape}'>
      {$level_category.display|escape} [{$player_info.level|escape}]
    </span>

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

			{foreach from=$combat_skills item="skill"}
				   <span id='{$skill.skill_internal_name|escape}'>
                     <label>
                       {$skill.skill_display_name|escape}
                       <input id="{$skill.skill_internal_name|escape}" type="checkbox" name="{$skill.skill_internal_name|escape}">
                     </label>
                   </span>
            {/foreach}

			       <input id="target" type="hidden" value="{$target}" name="target" title='Attack or Duel this ninja'>
                   <label class='attack-player-trigger'>
                     <input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='{$templatelite.const.IMAGE_ROOT}50pxShuriken.png' alt='Attack' title='Attack'>
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

{if !$self}
     <div class='set-bounty centered'>
       <form id="set_bounty" action="doshin_office.php" method="post" name="set_bounty">
         <div>
           <input id="amount" type="text" size="4" maxlength="5" name="amount" class="textField">
           <input id="command" type="submit" value="Offer Bounty" name="command" class="formButton">
           <input id="target" type="hidden" value="{$player_info.uname|escape}" name="target">
         </div>
       </form>
     </div>

     <div class='player-communications centered'>
       <form id='send_mail' action='player.php' method='get' name='send_mail'>
         <div>
           <input type='hidden' name='target_id' value='{$player_info.player_id|escape}'>
           <div><input id='messenger' type='hidden' value='1' name='messenger'></div>
           <textarea name='message' cols='20' rows='2'></textarea>
           <input type='submit' value='Send Message' class='formButton'>
         </div>
       </form>
     </div>
{/if}

    <!-- Clan leader options on players in their clan. -->
    {$clan_options_section}

    <!-- Player clan and clan members -->

	{$player_clan_section}

{if $player_info.messages}
    <div class='player-profile'>
      <div class='subtitle'>Message:</div>
      <p class='centered profile-message'>
        {$player_info.messages|escape|nl2br}
      </p>
    </div>
{/if}

	</div><!-- End player-info -->
