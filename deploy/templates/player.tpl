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

    <nav class='player-ranking-linkback'>
      <a href='list.php?searched={'#'|escape:'url'|escape}{$rank_spot|escape:'url'|escape}&amp;hide=none'>
        <img src='{$smarty.const.IMAGE_ROOT}return-triangle.png' alt='&lsaquo;Rank {$rank_spot|escape}' title='&lsaquo;Return to rank {$rank_spot}' style='width:50px;height:50px;float:left;'>
      </a>
    </nav>

  <article id='player-titles' class='centered'>

	{include file="gravatar.tpl" gurl=$gravatar_url}

    <span class='player-class {$target_class_theme|escape}'>
      <img id='class-shuriken' src='{$smarty.const.IMAGE_ROOT}small{$target_class_theme|escape}Shuriken.gif' alt=''>
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

  </article>
{literal}
<style>
#player-attack{

}
</style>
{/literal}


{if !$self}
  <section id='player-interact'>
	{if $attack_error}
    <div class='ninja-error centered'>Cannot Attack: {$attack_error}</div>
  </section>
	{else}
    <div id='attacks' style='width:95%;margin:0 auto'>
        <table id='player-attack'>
          <tr>
            <td id='attacking-choices'>
              <form id='attack_player' action='attack_mod.php' method='post' name='attack_player'>
                <label id='duel'>
                  Duel ({getTurnCost skillName="duel"}) <input id="duel" type="checkbox" {if $duel_checked}checked{/if} name="duel">
                </label>

		{foreach from=$combat_skills item="skill"}
                <label id='{$skill.skill_internal_name|escape}'>
                    {$skill.skill_display_name|escape}
                    ({getTurnCost skillName=$skill.skill_display_name})
                    <input id="{$skill.skill_internal_name|escape}" type="checkbox" {if $skill.checked}checked{/if} name="{$skill.skill_internal_name|escape}">
                </label>
		{/foreach}

                <input id="target" type="hidden" value="{$target|escape}" name="target" title='Attack or Duel this ninja'>
                <label class='attack-player-trigger'>
                  	<input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='{$smarty.const.IMAGE_ROOT}50pxShuriken.png' alt='Attack' title='Attack'><span style='font-size:1.5em;font-weight:bold'>Attack</span>
                </label>
              </form>
            </td>

            <!-- Inventory Items -->
            <td id='inventory-items'>
              <form id="inventory_form" action="inventory_mod.php" method="post" name="inventory_form">
                <div>
                  <input id="target" type="hidden" name="target_id" value="{$target_id|escape}">
		{if !$valid_items}
				  <div id='no-items' class='ninja-notice'>
				  	You have no items.
				  </div>
		{else}
                  <input type="submit" value="Use" class="formButton">
                  <select id="item" name="item">
			{foreach from=$items item="item"}
				{if $item.other_usable && $item.count>0}
                    <option value="{$item.item_id|escape}">{$item.name|escape} ({$item.count|escape})</option>
                {/if}
			{/foreach}
                  </select>
		{/if}

		{if $same_clan}
                  <input id="give" type="submit" value="Give" name="give" class="formButton">
		{/if}
                </div>
              </form>
            </td>
          </tr>
        </table>
    </div><!-- End of attacking section -->

    <div id='skills-section' style='padding:1em 2em;text-align:left'>
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

  </section>
	{/if} <!-- End of the attacking-had-no-errors section -->

{/if} <!-- End of the "not self" viewing section -->

  <section class='player-stats centered'>
  <!-- Will display as floats horizontally -->
    <small class='player-last-active'>
      Last active
{if $player_info.days gt 0}
      {$player_info.days} days ago
{else}
      today
{/if}
    </small>
{if $player_info.bounty gt 0}
    <small class='player-bounty'><a class='bounty-link' href='doshin_office.php' target='main'>{$player_info.bounty} bounty</a></small>
{/if}
  </section>

{if is_logged_in() and !$self}

  <section class='player-communications centered'  style='margin-bottom:1.5em'>
	<div>
      <form id='send_mail' action='player.php' method='get' name='send_mail'>
          <input type='hidden' name='target_id' value='{$player_info.player_id|escape}'>
          <div><input id='messenger' type='hidden' value='1' name='messenger'></div>
          <input type='text' name='message' size='30' maxlength="{$smarty.const.MAX_MSG_LENGTH|escape}">
          <input type='submit' value='Send Message' class='formButton'>
      </form>
	</div>

	<span id='message-ninja'>
      <a href='messages.php?target_id={$player_info.player_id|escape}'>Message <em class='char-name'>{$player_info.uname|escape}</em>
      </a>
    </span>

  <div class='set-bounty centered'>
    <a class='set-bounty-link' href='doshin_office.php?target={$player_info.uname|escape:'url'}'>Add bounty</a>
  </div>

  </section>

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

    </div>
{/if}

{if $player_info.messages}
    <div class='player-profile'>
      <div class='subtitle'>Message:</div>
      <p class='centered profile-message'>
        {$player_info.messages|trim|escape|replace_urls|nl2br}
      </p>
    </div>
{/if}

  </div><!-- End player-info -->
