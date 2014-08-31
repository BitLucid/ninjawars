{literal}
<style type="text/css">
.ninja-description{
  padding:0.3em 1em;background-color:rgba(200, 200, 200, 0.1);
}
.ninja-traits{
  font-style:italic;
}
.two-column {
  width: 100%;
  clear: both;
}
.two-column .primary {
  display:inline-block;
  width: 45%;
  min-width:20em;
  padding-left:5%;
  margin-top:1em;
  vertical-align:top;
}
.two-column .secondary {
  margin-top:1em;
  display:inline-block;
  width:47%;
  min-width:20em;
  vertical-align:top;
}
#profile-edit, #player-profile-area{
  width:90%;
}
fieldset#details textarea{
  display:block;
  width:100%;
}
fieldset#details textarea + textarea{
  margin-top:1.5em;
}
.your-stats input[type=submit]{
  color:ghostwhite;
  background-color:#617798;
background: #04667a; /* Old browsers */
background: -moz-linear-gradient(top, #04667a 0%, #207cca 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#04667a), color-stop(100%,#207cca)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #04667a 0%,#207cca 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #04667a 0%,#207cca 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, #04667a 0%,#207cca 100%); /* IE10+ */
background: linear-gradient(to bottom, #04667a 0%,#207cca 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#04667a', endColorstr='#207cca',GradientType=0 ); /* IE6-9 */
  border-radius:0;
  border:0;
  margin-top:1em;
  padding:.7em 1em;
}
.your-stats input[type=submit]:hover, .your-stats input[type=submit]:active{
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  text-shadow: 2px 2px 2px rgba(150, 150, 150, 1);
  color:red;
}
.two-column ul{
  margin-left:.3em;
  padding-left:.3em;
}
#switch-to-account{
  font-size:1.3em;
  margin-top:1em
}
#switch-to-account a{
  color:skyblue;
}
.secondary .glass-box{
  display:block;
}
.turns-count{
  background-color:#003366;
  color:ghostwhite;
  padding:0 1em;
}
.your-stats .turns-count{
  display:inline-block;
}
</style>
{/literal}


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
	<a href='account.php'>View your account info</a>
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