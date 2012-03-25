<h1>Ninja Stats for {$username|escape}</h1>

<div id='content' class='your-stats'>

{if $error}
<p class='error'>{$error}</p>
{elseif $successMessage}
<p class='notice'>{$successMessage|escape}</p>
{/if}

{if $profile_changed}
<p class='notice'>Profile has been changed.</p>
{/if}

<style type="text/css">
{literal}
.two-column {
	position: relative;
	width: 100%;
	height: 300px;
	clear: both;
}
.two-column .primary {
	position: absolute;
	width: 45%;
	left: 0;
	top: 0;
}
.two-column .secondary {
	position: absolute;
	width: 47%;
	right: 0;
	top: 0;
}
{/literal}
</style>

<div class='stats-avatar'>
  Avatar: (change your avatar for your account email at <a href='http://gravatar.com'>gravatar.com</a>)
  {include file="gravatar.tpl" gurl=$gravatar_url}
</div>
<div class='two-column'>
  <div class='primary'>
    {include file="status_section.tpl" statuses=$status_list}
    <ul id='player-info' class='player-info'>
      <li>
        Health:
        <span style='width:85%;display:inline-block' title='Max health: {$player.max_health}'>
          {include file="health_bar.tpl" health=$player.health health_percent=$player.hp_percent}
        </span>
      </li>
      <li>
        Level:
        <span class='player-level-category {$level_category.css|escape}'>
          {$level_category.display|escape} [{$player.level|escape}]
        </span>
      </li>
      <li>Class: <span class='class-name {$class_theme}'>{$player.class|escape}</span></li>
      <li>Strength: {$player.strength|escape}</li>
      <li>Speed: {$player.speed|escape}</li>
	  <li>Stamina: {$player.stamina|escape}</li>
      <li>Ki: {$player.ki|escape}</li>
      <li>Karma: {$player.karma|escape}</li>
      <li class='gold-count'>Gold: {$player.gold|escape}</li>
      <li>Kills: {$player.kills|escape}</li>
      <li>Turns: {$player.turns|escape}</li>
      <li>Created: {$player.created_date|escape}</li>
      <li>Rank: {$rank_display|escape}</li>
      <li>Bounty: {$player.bounty|escape} gold</li>
        {if $player_clan}
      <li>
        Clan:
        <a href='clan.php?command=view&amp;clan_id={$clan_id|escape:'url'}'>{$clan_name|escape}</a>
      </li>
        {/if}
    </ul>
  </div>
  <!-- Scripts with actual content are hated with smarty-like templates -->
  <script type='text/javascript' src='js/textAreaLimits.js'></script>

  <div class='secondary'>
    <form id="profile-edit" action="stats.php" method="post">
      <div>
        <input type="hidden" name="changeprofile" value="1">
        <span style='font-weight:bold'>Profile:</span>
        <div style='padding-right:83px;width:100%'>
          <textarea id='player-profile-area' name='newprofile' style='width:100%;height:10em;' class='textField'>{$profile_editable|escape}</textarea>
        </div>
        <input type='submit' value='Change Profile' class='formButton'> (<span id='characters-left'>{$profile_max_length} Character Limit</span>)
      </div>
    </form>

    <div id='player-profile-section'>
      Profile Preview:
      <div id='player-profile' style='height:8.5em;overflow:scroll'>
        &nbsp;{$profile_editable|escape|replace_urls|markdown|nl2br}&nbsp;
      </div>
    </div>
  </div>
</div><!-- End of the two-column arrangement. -->

<div class='switch-to-account notice clearfix' style='font-size:1.3em;margin-top:1em'>
	<a href='account.php'>View your account info</a>
</div>



<footer id='stats-footer' class='navigation'>
<h3>Assistance</h3>
<p>
  If you require account help email: <a href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>, 
  <br>
  or just get in touch with one or the other of us via any means on the <a href='staff.php'>staff page</a>.
</p>

      <div style='margin:2em 0'>
        <!-- The catchphrases and links -->
        {include file='footerlinks.tpl'}
      </div>

</footer>

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
