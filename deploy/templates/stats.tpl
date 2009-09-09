<span class='brownHeading'>Your Stats</span>

<div id='content'>

{if $error}
    <p>{$error}</p>
{else}

{if $confirm_delete}

    <p>Please provide your password to confirm.</p>
    <form method="POST" action="stats.php">
    <div>
    <input id="passw" type="password" maxlength="50" name="passw" class="textField">
    <input type="hidden" name="deleteaccount" value="1">
    <input type="submit" onSubmit="alert('DELETING ACCOUNT')" value="Confirm Delete" class="formButton">
    </div>
    </form>

{/if}

{if $profile_changed}
    <p>Profile has been changed.</p>
{/if}



{/if}

    <h2>Account Info for {$username}</h2>
<ul id='player-info'>
    <li>Avatar: {$avatar_display} (get one at <a href='http://gravatar.com'>gravatar.com</a>)</li>
    <li>Health: {$player.health}</li>
    <li>Strength: {$player.strength}</li>
    <li>Gold: {$player.gold}</li>
    <li>Kills: {$player.kills}</li>
    <li>Turns: {$player.turns}</li>
    <li>Email: {$player.email|escape}</li>
    <li>Class: {$player.class}</li>
    <li>Level: {$player.level}</li>
    <li>Rank: {$rank_display}</li>
    <li>Bounty: {$player.bounty} gold</li>
    <li>Clan: {$player.clan|escape}</li>
</ul>
Status: 
{$status_list}


<form id='action="stats.php" method="post">
    <div>
    <input type="hidden" name="changeprofile" value="1">
    Profile: <br><textarea id='player-profile-area' name='newprofile' cols='45' rows='10' class='textField'>
    {$profile_message}
    </textarea><br>
    <input type='submit' value='Change Profile' class='formButton'> (400 Character limit)
    </div>
</form>

<div id='player-profile'>
Profile Preview:
    <div id='player-profile>
        {$profile_display}
    </div>
</div>

<hr>
<p>If you require account help email: <a href='mailto:{$SUPPORT_EMAIL}'>{$SUPPORT_EMAIL}</a></p>
<hr>
<p>WARNING: Clicking on the button below will terminate your account.</p>
<form action='stats.php' method='POST'>
    <div>
    <input type='hidden' name='deleteaccount' value='1'>
    <input type='submit' value='Permanently Remove Your Account' class='formButton'>
    </div>
</form>

</div><!-- End of content -->
