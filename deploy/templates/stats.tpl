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


<h1>Ninja Stats for {$username|escape}</h1>

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

<div id='switch-to-account' class='notice'>
  <a href='account.php' target='main'>View your account info</a>
</div>

<div class='stats-avatar'>
  Avatar: (change your avatar for your account email at <a href='http://gravatar.com'>gravatar.com</a>) →
  {include file="gravatar.tpl" gurl=$gravatar_url}
</div>
<section class='two-column'>
  <div class='primary'>
    {include file="status_section.tpl" statuses=$status_list}
    <ul id='player-info' class='player-info'>
      <li>Class: <span class='class-name {$class_theme}'>{$player.class|escape}</span></li>
      <li>
        Level:
        <span class='player-level-category {$level_category.css|escape}'>
          {$level_category.display|escape} [{$player.level|escape}]
        </span>
      </li>
      <li><span class='physical'>Strength: {$player.strength|escape}</span><span class='physical'>Speed: {$player.speed|escape}</span><span class='physical'>Stamina: {$player.stamina|escape}</span></li>
      <li>Ki: {$player.ki|escape}</li>
      <li>Karma: {$player.karma|escape}</li>
      <li>
        Health:
        <span style='width:10em;display:inline-block' title='Max health: {$player.max_health}'>
          {include file="health_bar.tpl" health=$player.health health_percent=$player.hp_percent}
        </span>
      </li>
      <li>Turns: <span class='turns-count'>{$player.turns|escape}</span></li>
      <li>Kills: {$player.kills|escape}</li>
      <li class='gold-count'>Gold: {$player.gold|escape}</li>
      <li>Created: <time class='timeago' datetime='{$player.created_date|escape}'>{$player.created_date|escape}</time></li>
      <li>Rank: {$rank_display|escape}</li>
      <li>Bounty: <span class='gold-count'>{$player.bounty|escape} gold</span></li>
        {if $player_clan}
      <li>
        Clan:
        <a href='clan.php?command=view&amp;clan_id={$clan_id|escape:'url'}'>{$clan_name|escape}</a>
      </li>
        {/if}
    </ul>

<section class='details'>
    <legend>Details</legend>
    <div class='ninja-description'>
      {$player.uname|escape} {if !$description}is.{else}{$description|escape}{/if}
    </div>

    {if $goals}
    <div class='ninja-goals'>
      Goals: {$goals|escape}
    </div>
    {/if}
    {if $instincts}
    <div class='ninja-instincts'>
      Instinctually: {$instincts|escape}
    </div>
    {/if}
    {if $beliefs}
    <div class='ninja-beliefs'>
      Believes that: {$beliefs|escape}
    </div>
    {/if}
    {if $traits}
    <div class='ninja-traits'>
      Traits: {$traits|escape}
    </div>
    {/if}

</section>

  </div><!-- End of primary -->

  <div class='secondary'>
    <form id="profile-edit" name='profile-edit' action="stats.php" method="post">
      <input type='hidden' name='command' value='change_details'>
        <fieldset id='details'>
        <legend>Ninja Details</legend>
        <textarea name='description' id='description' title='Visible description of your ninja' placeholder='Visible description of your ninja'>{$description|escape}</textarea>
        <textarea name='instincts' id='instincts' title="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if ... then ...)" placeholder="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if I see Samurai, then I automatically attack.)">{$instincts|escape}</textarea>
        <textarea name='goals' id='goals' title="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring" placeholder="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring">{$goals|escape}</textarea>
        <textarea name='beliefs' id='beliefs' title="Your ninja's belief, the moral compass that keeps them going." placeholder="Your ninja's belief, the moral compass that keeps them going.">{$beliefs|escape}</textarea>
        <label class='glass-box'> Traits: <input name='traits' id='traits' type='text' value='{$traits|escape}' title="Traits that your ninja has (comma separated)" placeholder="Traits that your ninja has (comma separated)" size='40'></label>
        <input type='submit' value='Update' class='formButton'>
      </fieldset>
    </form>
    <form id="profile-edit" name='profile-edit' action="stats.php" method="post">
      <input type='hidden' name='command' value='update_profile'>
      <fieldset>
        <legend>Out-of-character Profile</legend>
        <div class='right-padded'>
          <textarea id='player-profile-area' name='newprofile' class='textField'>{$profile_editable|escape}</textarea>
        </div>
        <input type='submit' value='Update' class='formButton'> (<span id='characters-left'>{$profile_max_length} Character Limit</span>)
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

<script type='text/javascript' src='js/textAreaLimits.js'></script>