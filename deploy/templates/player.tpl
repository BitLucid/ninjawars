<style>

.player-info .player-level-category, .player-info .player-class{
  display: inline-block;
  padding: 0.5rem 1.25rem;
}
.player-info blockquote{
  max-height:7em;
  overflow:auto;
  margin: 1rem 1rem 0;
  color:ghostwhite;
}
.player-profile summary{
  background-color: #1b1919;
  padding: 0.1rem 1rem;
  cursor:pointer;
}
.grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-template-areas: 
    "avatar stats stats"
    "statuses statuses statuses";
  grid-gap: 10px;
  text-align: center;
}

.a-box{
  grid-area: avatar;
}
.b-box{
  grid-area: stats;
  text-align:left;
}
.c-box{
  grid-area: statuses;
}
</style>

<div class='player-info'>

    <h2 class='player-name'>
      {$target_player_obj->name()|escape} 
    </h2>

    <nav class='player-ranking-linkback'>
      <a href='/list?searched={'#'|escape:'url'|escape}{$rank_spot|escape:'url'|escape}&amp;hide=none' title='&lsaquo;Return to rank {$rank_spot}' >
        <i class="fas fa-chevron-circle-left"></i> Ninja List
      </a>
    </nav>

  {if $viewing_player_obj && $viewing_player_obj->isAdmin()}
    <a style='font-size:small;float:right;' href='/ninjamaster/?view={$target_player_obj->id()|escape}'>
      Admin <i class="fas fa-eye"></i>
    </a>
  {/if}

  <article id='player-titles' class='centered'>
    <section class='ninja-card grid'>
      <div class='grid'>
        <div class='a-box'>
        {include file="gravatar.tpl" gurl=$target_player_obj->avatarUrl()}
        </div>
        <div class="b-box">
          <div style="text-align:justify;margin-bottom:1rem;">
            <span class='player-class class-name {$target_player_obj->theme|escape}'>
              <span class="svg-shuriken">
                {include file="shuriken.svg.tpl"}
              </span>
                {$target_player_obj->class_name|escape}
            </span>&nbsp;
            <span class='player-level-category {$target_player_obj->level|level_label|css_classify}'>
              {$target_player_obj->level|level_label} [{$target_player_obj->level|escape}]
            </span>
          </div>
          {if $target_player_obj}
            <span id='health-bar-container'>
              {include file="health_bar.tpl" health=$target_player_obj->health level=$target_player_obj->level}
            </span>
          {/if}
        </div>

        <div class='c-box'>
          {include file="status_section.tpl" statuses=$status_list}
        </div>
      </div>

    {if $target_player_obj->description}
        <blockquote>
          {$target_player_obj->name()|escape} {$target_player_obj->description|escape}
        </blockquote>
    {/if}
    </section>
  </article>


{if !$self}
  <section id='player-interact'>
	{if $attack_error}
    <div class='ninja-error centered'>Cannot Attack: {$attack_error}</div>
    {if $i_am_dead}
      <div class='glassbox'>
        <a href='/shrine/heal_and_resurrect' target='main' title='Fully heal and resurrect' class='btn btn-default ninja-info centered'>⛩ Heal</a>
      </div>
    {/if}
  </section>

<script>
var attacking_possible = false;
</script>
	{else}
  {* No attack error *}
<script>
var attacking_possible = true;
var combatSkillsList = {$json_combat_skills nofilter};
// Store to allow settings to get cached on the front end.
</script>

    <div id='attacks'>
        <table id='player-attack'>
          <tr>
            <td id='attacking-choices'>
              <form id='attack_player' action='/attack' method='post' name='attack_player'>
                <div class='btn-group' role='group'>
                  <label class='btn btn-default' for='duel' title='Enter into a multi-round duel for an additional {getTurnCost skillName="duel"} turns.'>
                    <input id="duel" type="checkbox" name="duel" value="1"> Duel
                  </label><!-- no space
		-->{foreach from=$combat_skills item="skill"}<!-- No space
                  --><label class='btn btn-default' for='{$skill.skill_internal_name|escape}' title='{$skill.skill_internal_name|escape} while attacking for {getTurnCost skillName=$skill.skill_display_name} turns more'>
                    <input id="{$skill.skill_internal_name|escape}" type="checkbox" name="{$skill.skill_internal_name|escape}" value="1"> {$skill.skill_display_name|escape}
                  </label><!-- no space
		-->{/foreach}<!-- no space
                  --><input id="target" type="hidden" value="{$target_player_obj->id()|escape}" name="target">
                  <label class='attack-player-trigger btn btn-vital'  title='Attack or Duel this ninja for a base cost of {getTurnCost skillName="attack"} turn'>
                      <input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='{cachebust file="/images/50pxShuriken.png"}' alt='Attack'><span id='attack-text'>Attack</span>
                  </label>
                </div>
              </form>
            </td>

            <!-- Inventory Items -->
            <td id='inventory-items'>
              <form id="inventory_form" action="/player/use_item/" method="post" name="inventory_form">
                <div>
                  <input id="target" type="hidden" name="target_id" value="{$target_player_obj->id()|escape}">
        {foreach $items as $item}
            {if $item@first}
                {if $same_clan && !$viewing_self}
                  <div>
                    <input id="give" type="submit" value="Give" name="give" class="btn btn-default">
                  </div>
                {/if}
                  <select id="item" name="item">
            {/if}
            {if $item.other_usable && $item.count>0}
                    <option value="{$item.item_id|escape}">{$item.name|escape} ({$item.count|escape})</option>
            {/if}
            {if $item@last}
                  </select><!-- No space between --><input type="submit" value="Use Item" class="btn btn-default" style="border-top-left-radius:0;border-bottom-left-radius:0">
            {/if}
        {foreachelse}
				  <div id='no-items' class='ninja-notice'>
				  	You have no items.
				  </div>
		{/foreach}
                </div>
              </form>
            </td>
          </tr>
        </table>
    </div><!-- End of attacking section -->

    <div id='skills-section' style='padding:1em 2em;text-align:left'>
      {if !empty($targeted_skills)}
      <form id="skill_use" class="skill_use" action="/player/use_skill/" method="post" name="skill_use">
        <div class='parent'>
          <div class='child btn-group' id='skills-use-list'>
          {foreach from=$targeted_skills item="skill"}
              <input id="act-{$skill.skill_internal_name}" class="act btn btn-default" type="submit" value="{$skill.skill_display_name}" name="act" title='Use the {$skill.skill_display_name} skill for a cost of {getTurnCost skillName=$skill.skill_display_name} turns'>
              <input id="target" class="target" type="hidden" value="{$target_player_obj->name()|escape}" name="target">
          {/foreach}
          </div>
        </div>
      </form>
      {/if}
    </div>

  </section>
	{/if} <!-- End of the attacking-had-no-errors section -->

{/if} <!-- End of the "not self" viewing section -->

{if $authenticated and !$self}

  <section class='player-communications centered'>

	<span id='message-ninja'>
      <a href='/messages?to={$target_player_obj->name()|escape}'>Talk to <em class='char-name'>{$target_player_obj->name()|escape}</em>
      </a>
    </span>

  <div class='set-bounty centered'>
    <a class='set-bounty-link' href='/doshin?target={$target_player_obj->name()|escape:'url'}'>Add bounty</a>
  </div>

  </section>

{/if}

  <section class='player-stats centered'>
  <!-- Will display as floats horizontally -->

    <small class='player-last-active de-em'>
      Last active
      {if $target_player_obj->days gt 0}
        {$target_player_obj->days} days ago
      {else}
        today {if $kills_today gt 0}<span>with {$kills_today} kills</span>{/if}
      {/if}
    </small>
    {if $target_player_obj->bounty gt 0}
      <small class='player-bounty'><a class='bounty-link' href='/doshin' target='main'>{$target_player_obj->bounty} bounty</a></small>
    {/if}
  </section>

    <!-- Clan leader options on players in their clan. -->
{if $clan}

    <!-- Player clan and clan members -->
    <div class='player-clan'>
	{if $same_clan && !$viewing_self}
      <p class='ninja-notice'><em class='charname'>{$target_player_obj->name()|escape}</em> is part of your clan.</p>
	{/if}
      <p class='clan-link centered'>
        <span class='subtitle'>Clan:</span>
        <a href='/clan/view?clan_id={$clan->id}'>{$clan->getName()|escape}</a>
      </p>
  {if $display_clan_options}
    <div class='clan-leader-options centered'>
      <form id="kick_form" class='js-hooked' action="/clan/kick" method="get" name="kick_form">
        <div>
          <input id="kicked" type="hidden" value="{$target_player_obj->id()}" name="kicked">
          <input type="submit" value="Kick This Ninja From Your Clan" class="formButton">
        </div>
      </form>
    </div>
  {/if}

    </div>
{/if}

{if $target_player_obj->messages}
    <details class='player-profile'>
      <summary>Their Message</summary>
      <p class='centered profile-message'>
        {$target_player_obj->messages|trim|escape|replace_urls|nl2br}
      </p>
    </details>
{/if}

  {if $viewing_player_obj && $viewing_player_obj->isAdmin()}
    <div class='admin-view centered'><small class='de-em'>IP Address: {$account->getLastIp()}</small></div>
  {/if}

  </div><!-- End player-info -->

<script src='{cachebust file="/js/disagreement.js"}'></script>
