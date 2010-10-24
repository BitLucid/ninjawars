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

    <h1 class='player-name'>{$player|escape}</h1>

    <div class='player-ranking-linkback'>
      <a href='list.php?searched={'#'|escape:'url'|escape}{$rank_spot|escape:'url'|escape}&amp;hide=none'>
        <img src='{$templatelite.const.IMAGE_ROOT}return-triangle.png' alt='&lsaquo;Rank {$rank_spot|escape}' title='&lsaquo;Return to rank {$rank_spot}' style='width:50px;height:50px;float:left;'>
      </a>
    </div>

  <div class='player-titles centered'>

	{include file="gravatar.tpl" gurl=$gravatar_url}

    <span class='player-class {$target_class_theme|escape}'>
      <img id='class-shuriken' src='{$templatelite.const.IMAGE_ROOT}small{$target_class_theme|escape}Shuriken.gif' alt=''>
      {$player_info.class|escape}
    </span>

    <span class='player-level-category {$level_category.css|escape}'>
      {$level_category.display|escape} [{$player_info.level|escape}]
    </span>

    {include file="status_section.tpl" statuses=$status_list}


	{if $char_info.health}
    <span style='width:10em;display:inline-block;'>
      {include file="health_bar.tpl" health=$player_info.health health_percent=$player_info.health_percent}
    </span>
	{/if}

  </div>

{if !$self}
  <table id='player-profile-table'>
	{if $attack_error}
    <tr>
      <td><div class='ninja-error centered'>Cannot Attack: {$attack_error}</div></td>
    </tr>
  </table>
	{else}
    <tr>
      <td colspan='2'>
        <table id='player-profile-attack'>
          <tr>
            <td id='attacking-choices'>
              <form id='attack_player' action='attack_mod.php' method='post' name='attack_player'>
                <span id='duel'>
                  <label>Duel ({getTurnCost skillName="duel"}) <input id="duel" type="checkbox" name="duel"></label>
                </span>

		{foreach from=$combat_skills item="skill"}
                <span id='{$skill.skill_internal_name|escape}'>
                  <label>
                    {$skill.skill_display_name|escape}
                    ({getTurnCost skillName=$skill.skill_display_name})
                    <input id="{$skill.skill_internal_name|escape}" type="checkbox" name="{$skill.skill_internal_name|escape}">
                  </label>
                </span>
		{/foreach}

                <input id="target" type="hidden" value="{$target|escape}" name="target" title='Attack or Duel this ninja'>
                <label class='attack-player-trigger'>
                  <a onclick="document.attack_player.submit();"><input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='{$templatelite.const.IMAGE_ROOT}50pxShuriken.png' alt='Attack' title='Attack'>Attack</a>
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
		{if count($targeted_skills) gt 0}
    <form id="skill_use" class="skill_use" action="skills_mod.php" method="post" name="skill_use">
      <ul id='skills-use-list'>
			{foreach from=$targeted_skills item="skill"}
        <li>
          <input id="command" class="command" type="submit" value="{$skill.skill_display_name}" name="command" class="formButton">
          <input id="target" class="target" type="hidden" value="{$target|escape}" name="target">
          ({getTurnCost skillName=$skill.skill_display_name} Turns)
        </li>
			{/foreach}
      </ul>
    </form>
		{/if}
  </div>
	{/if} <!-- End of the attacking-had-no-errors section -->

{/if} <!-- End of the "not self" viewing section -->

  <div class='player-stats centered'>
  <!-- Will display as floats horizontally -->
    <span class='player-last-active'>
      Last logged in
{if $player_info.days gt 0}
      {$player_info.days} days ago
{else}
      Today
{/if}
    </span>
{if $player_info.bounty gt 0}
    <span class='player-bounty'><a class='bounty-link' href='doshin_office.php' target='main'>{$player_info.bounty} bounty</a></span>
{/if}
  </div>

{if is_logged_in() and !$self}

  <div class='player-communications centered'>
	<div>
      <form id='send_mail' action='player.php' method='get' name='send_mail'>
        <div>
          <input type='hidden' name='target_id' value='{$player_info.player_id|escape}'>
          <div><input id='messenger' type='hidden' value='1' name='messenger'></div>
          <input type='text' name='message' size='30' maxlength="{$templatelite.const.MAX_MSG_LENGTH|escape}">
          <input type='submit' value='Send Message' class='formButton'>
        </div>
      </form>
	</div>

	<span id='message-ninja'>
      <a href='messages.php?target_id={$player_info.player_id|escape}'>Message <em class='char-name'>{$player_info.uname|escape}</em></a>
    </span>

  </div>

  <div class='set-bounty centered'>
    <a class='set-bounty-link' href='doshin_office.php?target={$player_info.uname|escape:'url'}'>Add bounty</a>
  </div>

{/if}

    <!-- Clan leader options on players in their clan. -->
{if $clan}
	{if $display_clan_options}
    <div class='clan-leader-options centered'>
      <form id="kick_form" action="clan.php" method="get" name="kick_form">
        <div>
          <input id="kicked" type="hidden" value="{$player_info.player_id}" name="kicked">
          <input id="command" type="hidden" value="kick" name="command">
          <input type="submit" value="Kick This Ninja From Your Clan" class="formButton">
        </div>
      </form>
    </div>
	{/if}

    <!-- Player clan and clan members -->
    <div class='player-clan'>
	{if $same_clan}
      <p class='ninja-notice'><em class='charname'>{$player_info.uname|escape}</em> is part of your clan.</p>
	{/if}
      <p class='clan-link centered'>
        <span class='subtitle'>Clan:</span>
        <a href='clan.php?command=view&amp;clan_id={$clan_id}'>{$clan_name|escape}</a>
      </p>
{*
Clan members are no longer shown on a player's profile, 'cause it just made it too dirty anyway.
      <div class='clan-members centered'>

{if count($clan_members) > 0}
        <div class='clan-members'>
          <h3 class='clan-members-header'>Clan members</h3>
          <ul>
	{foreach from=$clan_members item="member"}
		{if $member.health < 1}
			{assign var="added_class" value=' injured'}
		{else}
			{assign var="added_class" value=''}
		{/if}

            <li class='clan-member{$added_class}'>
              <a href='player.php?target_id={$member.player_id|escape:'url'|escape}'>{$member.uname|escape}</a>
            </li>
	{/foreach}
          </ul>
        </div>
{/if}

      </div>

*}
    </div>
{/if}

{if $player_info.messages}
    <div class='player-profile'>
      <div class='subtitle'>Message:</div>
      <p class='centered profile-message'>
        {$player_info.messages|trim|escape|nl2br}
      </p>
    </div>
{/if}

  </div><!-- End player-info -->
