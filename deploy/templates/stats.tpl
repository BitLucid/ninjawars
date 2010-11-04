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
        <span style='width:85%;display:inline-block'>
          {include file="health_bar.tpl" health=$player.health health_percent=$player.hp_percent}
        </span>
      </li>
      <li>
        Level:
        <span class='player-level-category {$level_category.css|escape}'>
          {$level_category.display|escape} [{$player.level|escape}]
        </span>
      </li>
      <li>Class: <span class='class-name {$class_theme}'>{$player.class}</span></li>
      <li>Strength: {$player.strength}</li>
      <li class='gold-count'>Gold: {$player.gold}</li>
      <li>Kills: {$player.kills}</li>
      <li>Turns: {$player.turns}</li>
      <li>Created: {$player.created_date|escape}</li>
      <li>Rank: {$rank_display}</li>
      <li>Bounty: {$player.bounty} gold</li>
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
          <textarea id='player-profile-area' name='newprofile' style='width:100%;height:10em;' class='textField'>{$profile_editable}</textarea>
        </div>
        <input type='submit' value='Change Profile' class='formButton'> (<span id='characters-left'>{$profile_max_length} Character Limit</span>)
      </div>
    </form>

    <div id='player-profile-section'>
      Profile Preview:
      <div id='player-profile'>
        &nbsp;{$profile_display|nl2br}&nbsp;
      </div>
    </div>
  </div>
</div><!-- End of the two-column arrangement. -->

<div class='switch-to-account notice' style='font-size:1.3em'>
	<a href='account.php'>View your account info</a>
</div>

<p>
  If you require account help email: <a href='mailto:{$templatelite.const.SUPPORT_EMAIL}'>{$templatelite.const.SUPPORT_EMAIL}</a>
</p>

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
