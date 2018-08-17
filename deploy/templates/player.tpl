<style>
.player-info .ninja-card{
  background: rgba(134, 120, 120, 0.3);
  box-shadow: 10px 5px 5px black;
  border: thin solid rgba(134, 120, 120, 0.3);;
  border-radius: 0.3rem;
  display:inline-block;
  font-family:'Open Sans', "Arial", sans-serif;
  width:60%;
  margin:auto;
  padding: 2rem;
}
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
    <a style='font-size:small;float:right;' href='/ninjamaster/?view={$target_player_obj->id()|escape}'>Admin <i class="fas fa-eye"></i></a>
  {/if}

  <article id='player-titles' class='centered'>

{* Direct inject svg *}
{$shuriken_svg='
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg id="nw-shuriken-svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" height="95.245mm" width="113.09mm" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 400.69856 337.48064">
 <metadata id="metadata7">
  <rdf:RDF>
   <cc:Work rdf:about="">
    <dc:format>image/svg+xml</dc:format>
    <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>
    <dc:title/>
   </cc:Work>
  </rdf:RDF>
 </metadata>
 <g id="layer1" transform="translate(-5.2544 -5.2344)">
  <path id="path3441" d="m144.14 326.16c0.48651-9.1094 1.6844-25.281 2.6619-35.938 4.0025-43.632 5.5573-63.935 4.9689-64.887-1.2529-2.0273 1.8122-15.671 3.9686-17.665 1.2165-1.1251 4.4618-2.7018 7.2118-3.5038 7.1811-2.0943 16.182-9.1913 20.069-15.824 3.157-5.3871 3.2605-6.0499 1.7734-11.361-2.1376-7.6348-10.663-16.894-18.993-20.626-5.2595-2.3567-8.7564-2.9586-17.224-2.9647-9.2599-0.006-11.497 0.44897-17.411 3.5468-5.0286 2.6338-7.9426 3.3976-11.25 2.9489-6.88-0.96-110.86-39.05-113.73-41.68-1.4857-1.3648-1.2923-1.7238 1.25-2.3206 7.2558-1.7033 105.04-10.666 116.54-10.681 4.7915-0.006 7.7468 1.0071 14.375 4.9299 16.245 9.6142 32.819 10.182 44.914 1.5393 8.5889-6.1378 3.9538-17.395-11.417-27.727-4.8839-3.2831-8.6651-6.7937-9.0822-8.4321-0.81344-3.1955 2.9762-69.157 4.034-70.215 0.37992-0.37992 1.6367 0.50479 2.7928 1.966s13.352 15.147 27.102 30.414c13.75 15.266 25.758 28.984 26.684 30.484 1.5035 2.4358 1.2591 3.4101-2.2857 9.1125l-3.9692 6.3851 2.4886 4.8301c4.9956 9.6958 14.871 16.435 28.077 19.161 8.3562 1.7246 15.043 0.91205 20.673-2.5121 3.371-2.05 3.9578-3.1399 3.9578-7.3515 0-2.7195 0.61957-5.3275 1.3768-5.7955 1.5059-0.93069 5.9993-1.4776 50.498-6.1469 16.5-1.7313 31.547-3.4155 33.438-3.7426 2.0262-0.35052 3.4375-0.0304 3.4375 0.77963 0 1.3533-10.357 13.183-33.38 38.123l-11.709 12.685-15.268 0.43981c-18.37 0.52917-23.738 2.6746-24.424 9.7616-1.3945 14.409 13.424 27.943 37.132 33.915 7.7812 1.9601 11.037 4.8338 28.899 25.511 9.8746 11.431 49.753 56.129 61.047 68.426 6.5751 7.1587 2.5757 5.8573-35.422-11.526-11.688-5.3469-34.679-15.549-51.091-22.671-16.565-7.1882-30.112-13.79-30.449-14.838-1.9668-6.1094-7.7798-15.944-11.985-20.278-11.099-11.437-31.263-15.766-43.465-9.3304-11.952 6.3036-14.165 16.173-6.7591 30.134 7.1823 13.538 7.4173 12.928-16.478 42.783-11.469 14.329-28.944 36.178-38.832 48.553-9.8885 12.375-19.063 23.766-20.389 25.312-4.7513 5.5458-5.3005 3.8149-4.3624-13.75zm96.701-170.99c7.5222-7.008-2.0546-20.674-16.605-23.694-8.0302-1.6671-17.548 0.26442-19.527 3.9628-2.7918 5.2165 2.6313 13.661 12.618 19.648 6.1507 3.6873 19.595 3.735 23.514 0.0834z"/>
 </g>
</svg>'}

    <section class='ninja-card grid'>
      <div class='grid'>
        <div class='a-box'>
        {include file="gravatar.tpl" gurl=$target_player_obj->avatarUrl()}
        </div>
        <div class="b-box">
          <div style="text-align:justify;margin-bottom:1rem;">
            <span class='player-class class-name {$target_player_obj->theme|escape}'>
              <span class="svg-shuriken">
                {$shuriken_svg}
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
        <blockquote>{$target_player_obj->name()|escape} {$target_player_obj->description|escape}</blockquote>
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
                {if $same_clan}
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
	{if $same_clan}
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
