{literal}
<style type='text/css'>
label{
    color:cornflowerblue;
}
section.player-communications{
    margin-bottom:1.5em;
}
article#player-titles{
  font-size:larger;
}
#health-bar-container{
  width:10em;display:inline-block;
}
#attacking-choices label{
  margin-right:.5em;
}
#attacking-choices .btn-vital{
  text-transform:none;
}
#attacking-choices #attack-text{
  font-size:1.5em;font-weight:bold;vertical-align:text-bottom;
}
#attacks{
   width:95%;margin:0 auto;font-size:larger;clear:both;
}
#skills-use-list{
  margin-left:0;
  padding-left:0;
}
#skills-use-list li{
  display:inline-block;margin-right:2em;
}
.player-info blockquote{
  max-height:3.5em;overflow:auto;
}
</style>
{/literal}

{if $message}
    <div id='message-sent' class='ninja-notice'>Message sent</div>
{/if}

<div class='player-info'>

    <h1 class='player-name'>{$player|escape}</h1>

    <nav class='player-ranking-linkback'>
      <a href='list.php?searched={'#'|escape:'url'|escape}{$rank_spot|escape:'url'|escape}&amp;hide=none'>
        <img src='{cachebust file="/images/return-triangle.png"}' alt='&lsaquo;Rank {$rank_spot|escape}' title='&lsaquo;Return to rank {$rank_spot}' style='width:50px;height:50px;float:left;'>
      </a>
    </nav>

  <article id='player-titles' class='centered'>


    <span class='player-class {$target_class_theme|escape}'>
      <img id='class-shuriken' src='{$smarty.const.IMAGE_ROOT}small{$target_class_theme|escape}Shuriken.gif' alt=''>
      {$player_info.class|escape}
    </span>

    <span class='player-level-category {$level_category.css|escape}'>
      {$level_category.display|escape} [{$player_info.level|escape}]
    </span>

    {include file="status_section.tpl" statuses=$status_list}

	{if $player_info.health}
    <span id='health-bar-container'>
      {include file="health_bar.tpl" health=$player_info.health health_percent=$player_info.health_percent}
    </span>
	{/if}

  {include file="gravatar.tpl" gurl=$gravatar_url}
  {if $viewing_player_obj->isAdmin()}<a style='font-size:small;float:right;' href='{$smarty.const.WEB_ROOT}ninjamaster/?view={$player_info.player_id|escape}'>Admin View</a>{/if}

  </article>

  {if $player_info.description}
      <blockquote>{$player_info.uname|escape} {$player_info.description|escape}</blockquote>
  {/if}


{if !$self}
  <section id='player-interact'>
	{if $attack_error}
    <div class='ninja-error centered'>Cannot Attack: {$attack_error}</div>
  </section>

<script>
var attacking_possible = false;
</script>

	{else}
<script>
var attacking_possible = true;
var combat_skills = {$combat_skills|@json_encode};
// Store to allow settings to get cached on the front end.
</script>

    <div id='attacks'>
        <table id='player-attack'>
          <tr>
            <td id='attacking-choices'>
              <form id='attack_player' action='attack_mod.php' method='post' name='attack_player'>
                <label for='duel' title='Multi-attack duel for an additional {getTurnCost skillName="duel"} turns.'>
                  <input id="duel" type="checkbox" name="duel" value="1"> Duel
                </label>

		{foreach from=$combat_skills item="skill"}
                <label for='{$skill.skill_internal_name|escape}' title='{$skill.skill_internal_name|escape} while attacking for {getTurnCost skillName=$skill.skill_display_name} turns more'>
                    <input id="{$skill.skill_internal_name|escape}" type="checkbox" name="{$skill.skill_internal_name|escape}" value="1"> {$skill.skill_display_name|escape}
                </label>
		{/foreach}

                <input id="target" type="hidden" value="{$target|escape}" name="target" title='Attack or Duel this ninja'>
                <label class='attack-player-trigger btn btn-vital'>
                  	<input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='{cachebust file="/images/50pxShuriken.png"}' alt='Attack' title='Attack'><span id='attack-text'>Attack</span>
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
                  <input type="submit" value="Use Item" class="btn btn-primary">
                  <select id="item" name="item">
			{foreach from=$items item="item"}
				{if $item.other_usable && $item.count>0}
                    <option value="{$item.item_id|escape}">{$item.name|escape} ({$item.count|escape})</option>
                {/if}
			{/foreach}
                  </select>
		{/if}

		{if $same_clan}
                  <input id="give" type="submit" value="Give" name="give" class="btn btn-default">
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
        <div class='parent'>
          <ul id='skills-use-list' class='child'>
          {foreach from=$targeted_skills item="skill"}
            <li>
              <input id="command-{$skill.skill_internal_name}" class="command btn btn-primary" type="submit" value="{$skill.skill_display_name}" name="command" title='Use the {$skill.skill_display_name} skill for a cost of {getTurnCost skillName=$skill.skill_display_name} turns'>
              <input id="target" class="target" type="hidden" value="{$target|escape}" name="target">
            </li>
          {/foreach}
          </ul>
        </div>
      </form>
      {/if}
    </div>

  </section>
	{/if} <!-- End of the attacking-had-no-errors section -->

{/if} <!-- End of the "not self" viewing section -->

{if is_logged_in() and !$self}

  <section class='player-communications centered'>

	<span id='message-ninja'>
      <a href='messages.php?to={$player_info.uname|escape}'>Message <em class='char-name'>{$player_info.uname|escape}</em>
      </a>
    </span>

  <div class='set-bounty centered'>
    <a class='set-bounty-link' href='doshin_office.php?target={$player_info.uname|escape:'url'}'>Add bounty</a>
  </div>

  </section>

{/if}

  <section class='player-stats centered'>
  <!-- Will display as floats horizontally -->

    <small class='player-last-active de-em'>
      Last active
      {if $player_info.days gt 0}
        {$player_info.days} days ago
      {else}
        today {if $kills_today gt 0}<span>with {$kills_today} kills</span>{/if}
      {/if}
    </small>
    {if $player_info.bounty gt 0}
      <small class='player-bounty'><a class='bounty-link' href='doshin_office.php' target='main'>{$player_info.bounty} bounty</a></small>
    {/if}
  </section>

    <!-- Clan leader options on players in their clan. -->
{if $clan}

    <!-- Player clan and clan members -->
    <div class='player-clan'>
	{if $same_clan}
      <p class='ninja-notice'><em class='charname'>{$player_info.uname|escape}</em> is part of your clan.</p>
	{/if}
      <p class='clan-link centered'>
        <span class='subtitle'>Clan:</span>
        <a href='clan.php?command=view&amp;clan_id={$clan_id}'>{$clan_name|escape}</a>
      </p>
  {if $display_clan_options}
    <div class='clan-leader-options centered'>
      <form id="kick_form" class='js-hooked' action="clan.php" method="get" name="kick_form">
        <div>
          <input id="kicked" type="hidden" value="{$player_info.player_id}" name="kicked">
          <input id="command" type="hidden" value="kick" name="command">
          <input type="submit" value="Kick This Ninja From Your Clan" class="formButton">
        </div>
      </form>
    </div>
  {/if}

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

  {if $viewing_player_obj->isAdmin()}
    <div class='admin-view centered'><small class='de-em'>IP Address: {$target_player_obj->ip()}</small></div>
  {/if}

  </div><!-- End player-info -->

<script src='{cachebust file="/js/disagreement.js"}'></script>
