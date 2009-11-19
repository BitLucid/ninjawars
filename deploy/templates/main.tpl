{$header}

<!-- Replace this with our banner image -->

<h2>Welcome to <span id="ninjawars-title">Ninja Wars</span></h2>

<h3><span id="ninjawars-subtitle">Ninja Wars</span>
     is an online game where you battle players all over the world.
     <a href="about.php">(read more)</a></h3>

{$progression}

<p>
View the <a href="about.php">Intro</a> to learn more about Ninjawars.<br>
Post messages to other players on the <a href="village.php">Chat Board</a>
or the <a href="{$WEB_ROOT}forum/" target="_blank" class="extLink">
    Forum <img src="images/externalLinkGraphic.gif"></a>, or find out what has changed about NW in the <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">Announcements<img src="images/externalLinkGraphic.gif"></a> section of the Forum</a>.
</p>
<p><a href="lostpass.php">Lost Your Password ?</a> / Didn't get your <a href="lostconfirm.php">confirmation code ?</a></p>
<!-- TODO: put display_active(); here -->
<p style="border-top:thin solid white;padding-top:10px;">
	To attack a ninja, use the <a href="list_all_players.php?hide=dead">player list</a> or search for a ninja below.</a>
	<form id="player_search" action="list_all_players.php" method="get" name="player_search">
    	Search by Ninja Name
    	<input id="searched" type="text" maxlength="50" name="searched" class="textField">
    	<input id="hide" type="hidden" name="hide" value="dead">
    	<input type="submit" value="Search for Ninja" class="formButton">
	</form>
</p>

{$footer}
