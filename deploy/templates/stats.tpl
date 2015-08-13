
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
      <li>Strength: {$player.strength|escape} Speed: {$player.speed|escape} Stamina: {$player.stamina|escape}</li>
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
      {$description|escape}
    </div>

    <div class='ninja-goals'>
      {$goals|escape}
    </div>
    <div class='ninja-instincts'>
      {$instincts|escape}
    </div>
    <div class='ninja-beliefs'>
      {$beliefs|escape}
    </div>
    <div class='ninja-traits'>
      {$traits|escape}
    </div>

    <div id='player-profile-section'>
      OOC Profile Preview:
      <div id='player-profile' style='height:8.5em;overflow:scroll'>
        &nbsp;{$profile_editable|escape|replace_urls|markdown|nl2br}&nbsp;
      </div>
    </div>
</section>

  </div><!-- End of primary -->

  <!-- Scripts with actual content are hated with smarty-like templates -->

  <div class='secondary'>
    <form id="profile-edit" action="stats.php" method="post">
      <input type='hidden' name='changedetails' value=1>
      {if $dev}
      <fieldset id='details'>
      <legend>Ninja Details</legend>
      <textarea id='description' title='Visible description of your ninja' placeholder='Visible description of your ninja'>{$description|escape}</textarea>
      <textarea id='instincts' title="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if ... then ...)" placeholder="Your ninja's instincts, things that if they happen, cause your ninja to act in a certain way (e.g. if ... then ...)">{$instincts|escape}</textarea>
      <textarea id='goals' title="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring" placeholder="Your ninja's goals, what you want to accomplish in the world, or even want to get done this week while exploring">{$goals|escape}</textarea>
      <textarea id='beliefs' title="Your ninja's belief, the moral compass that keeps them going." placeholder="Your ninja's belief, the moral compass that keeps them going.">{$beliefs|escape}</textarea>
      <label class='glass-box'> Traits: <input type='text' id='traits' value='{$traits|escape}' title="Traits that your ninja has (comma separated)" placeholder="Traits that your ninja has (comma separated)" size='40'></label>
      <input type='submit' value='Update' class='formButton'>
    </fieldset>
    {/if}
      <fieldset>
        <legend>Out-of-character Profile</legend>
        <div style='padding-right:83px;width:98%'>
          <textarea id='player-profile-area' name='newprofile' style='width:100%;height:10em;' class='textField'>{$profile_editable|escape}</textarea>
        </div>
        <input type='submit' value='Update' class='formButton'> (<span id='characters-left'>{$profile_max_length} Character Limit</span>)
      </fieldset>
    </form>

  </div>
</section><!-- End of the two-column arrangement. -->


<div id='switch-to-account' class='notice'>
	<a href='account.php' target='main'>View your account info</a>
</div>

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