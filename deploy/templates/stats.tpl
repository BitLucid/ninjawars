<h1>Account Info for {$username}</h1>

{if $error}
    <p class='error'>{$error}</p>
{else}

{if $confirm_delete}

    <p>Please provide your password to confirm.</p>
    <form method="post" action="stats.php">
        <div>
            <input id="passw" type="password" maxlength="50" name="passw" class="textField">
            <input type="hidden" name="deleteaccount" value="2">
            <input type="submit" onsubmit="alert('DELETING ACCOUNT');" value="Confirm Delete" class="formButton">
        </div>
    </form>

{/if}

{if $profile_changed}
    <p class='notice'>Profile has been changed.</p>
{/if}



{/if}


<div>Avatar: (change your avatar for your account email at <a href='http://gravatar.com'>gravatar.com</a>) {include file="gravatar.tpl" url=$gravatar_url}</div>
{$status_list}
<ul id='player-info' class='player-info'>
    <li>Health: <span{if $player.health lt 80} class="injured"{/if}>{$player.health|escape}</span></li>
    <li>Level: <span class='player-level-category {$level_category.css|escape}'> {$level_category.display|escape} [{$player.level|escape}] </span></li>
    <li>Class: {$player.class}</li>
    <li>Strength: {$player.strength}</li>
    <li>Gold: {$player.gold}</li>
    <li>Kills: {$player.kills}</li>
    <li>Turns: {$player.turns}</li>
    <li>Email: {$player.email|escape}</li>
    <li>Created: {$player.created_date|escape}</li>
    <li>Rank: {$rank_display}</li>
    <li>Bounty: {$player.bounty} gold</li>
{if $player_clan}
    <li>Clan: 
        <a href='clan.php?command=view&amp;clan_id={$clan_id|escape:'url'}'>
        {$clan_name|escape}
        </a>
    </li>
{/if}
</ul>

<!-- Scripts with actual content are hated with smarty-like templates -->
<script type='text/javascript' src='js/textAreaLimits.js'></script>

<form id="profile-edit" action="stats.php" method="post">
    <div>
        <input type="hidden" name="changeprofile" value="1">
        Profile: 
        <div>
            <textarea id='player-profile-area' name='newprofile' cols='45' rows='10' class='textField'>{$profile_editable}</textarea>
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

<hr>
<p>If you require account help email: <a href='mailto:{$templatelite.const.SUPPORT_EMAIL}'>{$templatelite.const.SUPPORT_EMAIL}</a></p>
<hr>

{if !$delete_attempts}
<p>WARNING: Clicking on the button below will terminate your account.</p>
<form action='stats.php' method='post'>
    <div>
        <input type='hidden' name='deleteaccount' value='1'>
        <input type='submit' value='Permanently Remove Your Account' class='formButton'>
    </div>
</form>
{/if}
