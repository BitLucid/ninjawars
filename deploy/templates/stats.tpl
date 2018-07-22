<style>
.player-info{
  color:#A1A1A7;
}
.player-info .physical{
  display:inline-block;margin-right:5%;color:ghostwhite;font-weight:bold;
}
.details div + div{
  margin-top:0.7em;
}
#profile-edit .right-padded{
  padding-right:83px;width:98%;
}
#profile-edit #player-profile-area{
  width:100%;height:10em;
}
</style>


<h1><i class="fas fa-heart"></i> Ninja Stats for {$char->name()|escape}</h1>

<div id='content' class='your-stats'>

{if $error}
  <p class='error'>{$error|escape}</p>
{elseif $successMessage}
  <p class='notice'>{$successMessage|escape}</p>
{/if}

{if $profile_changed}
  <p class='notice'>Profile has been changed.</p>
{/if}

{if $changed}
  <p class='notice'>Ninja details have been changed.</p>
{/if}

<div style="position:relative">
  <a style="position:absolute;bottom:0;right:0;" class='btn btn-default' href='/account' target='main'><i class='fas fa-cogs'></i></a>
</div>

<div class='stats-avatar'>
  Avatar: (change your avatar for your account email at <a rel="noopener noreferrer" target="_blank" href='http://gravatar.com'>gravatar.com</a>) →
  {include file="gravatar.tpl" gurl=$char->avatarUrl()}
</div>
<section class='two-column'>
  <div class='primary'>
    {include file="status_section.tpl" statuses=$status_list}
    <ul id='player-info' class='player-info'>
      <li>Class: <span class='class-name {$char->theme|escape}'>{$char->class_name|escape}</span></li>
      <li>
        Level:
        <span class='player-level-category {$char->level|level_label|css_classify}'>
          {$char->level|level_label} [{$char->level|escape}]
        </span>
      </li>
      <li><span class='physical'>Strength: {$char->getStrength()|escape}</span><span class='physical'>Speed: {$char->getSpeed()|escape}</span><span class='physical'>Stamina: {$char->getStamina()|escape}</span></li>
      <li>Ki: {$char->ki|number_format:0|escape}</li>
      <li>Karma: {$char->karma|number_format:0|escape}</li>
      <li>
        Health:
        <span class='health-bar-area' title='Max health: {$char->getMaxHealth()|escape}'>
          {include file="health_bar.tpl" health=$char->health level=$char->level}
        </span>
      </li>
      <li>Turns: <span class='turns-count'>{$char->turns|number_format:0|escape}</span></li>
      <li>Kills: <span class='kills-bar-area'>{$char->kills|escape}</span></li>
      <li class='gold-count'>Gold: 石{$char->gold|number_format|escape}</li>
      <li>Created: <time class='timeago' datetime='{$char->created_date|escape}'>{$char->created_date|escape}</time></li>
      <li>Rank: {$rank_display|escape}</li>
      <li>Bounty: <span class='gold-count'>石{$char->bounty|number_format|escape}</span></li>
        {if $clan}
      <li>
        Clan:
        <a href='/clan/view?clan_id={$clan->id|escape:'url'}'>{$clan->getName()|escape}</a>
      </li>
        {/if}
    </ul>

<section class='details'>
    <legend>Details</legend>
    <div class='ninja-description'>
      {$char->uname|escape} {if !$char->description}is.{else}{$char->description|escape}{/if}
    </div>

    {if $char->goals}
    <div class='ninja-goals'>
      Goals: {$char->goals|escape}
    </div>
    {/if}
    {if $char->instincts}
    <div class='ninja-instincts'>
      Instinctually: {$char->instincts|escape}
    </div>
    {/if}
    {if $char->beliefs}
    <div class='ninja-beliefs'>
      Believes that: {$char->beliefs|escape}
    </div>
    {/if}
    {if $char->traits}
    <div class='ninja-traits'>
      Traits: {$char->traits|escape}
    </div>
    {/if}

</section>

  </div><!-- End of primary -->

  <div class='secondary'>
    <form id="profile-edit" name='profile-edit' action="/stats/change_details" method="post">
        <fieldset id='details'>
        <legend>Ninja Details</legend>
        <textarea name='description' id='description' title='Visible description of your ninja' placeholder='Visible description of your ninja'>{$char->description|escape}</textarea>
        <textarea name='instincts' id='instincts' title="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if ... then ...)" placeholder="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if I see Samurai, then I automatically attack.)">{$char->instincts|escape}</textarea>
        <textarea name='goals' id='goals' title="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring" placeholder="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring">{$char->goals|escape}</textarea>
        <textarea name='beliefs' id='beliefs' title="Your ninja's belief, the moral compass that keeps them going." placeholder="Your ninja's belief, the moral compass that keeps them going.">{$char->beliefs|escape}</textarea>
        <label class='glass-box'> Traits: <input name='traits' id='traits' type='text' value='{$char->traits|escape}' title="Traits that your ninja has (comma separated)" placeholder="Traits that your ninja has (comma separated)" size='40'></label>
        <input type='submit' value='Update' class='btn btn-primary formButton'>
      </fieldset>
    </form>
    <form id="profile-edit" name='profile-edit' action="/stats/update_profile" method="post">
      <fieldset>
        <legend>Out-of-character Profile</legend>
        <div class='right-padded'>
          <textarea id='player-profile-area' name='newprofile' class='textField'>{$char->messages|escape}</textarea>
        </div>
        <input type='submit' value='Update' class='btn btn-primary formButton'> (<span id='characters-left'>{$profile_max_length} Character Limit</span>)
      </fieldset>
    </form>
  </div>
</section><!-- End of the two-column arrangement. -->

{literal}
<!-- Google Code for View self/ninja stats page. Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1068723773;
var google_conversion_language = "en";
var google_conversion_format = "1";
var google_conversion_color = "333333";
var google_conversion_label = "AMq2CMHd_AEQvdzN_QM";
var google_conversion_value = 0;
if (0) {
  google_conversion_value = 0;
}
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1068723773/?value=0&amp;label=AMq2CMHd_AEQvdzN_QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
{/literal}

</div><!-- End of content div -->

<script type='text/javascript' src='{cachebust file="/js/textAreaLimits.js"}'></script>
