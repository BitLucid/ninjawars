<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Main";

include "interface/header.php";
?>

<!-- Replace this with our banner image -->
    
<h2>Welcome to <span id="ninjawars-title">Ninja Wars</span></h2>

<h3><span id="ninjawars-subtitle">Ninja Wars</span>
     is an online game where you battle players all over the world. 
     <a href="about.php">(read more)</a></h3>
<p><a href="signup.php">Create a Ninja</a> or log in above!<p>
<p>
Visit the <a href="about.php">FAQ</a> to learn more about Ninjawars.<br>
Post messages to other players on the <a href="village.php">Chat Board</a> 
or the <a href="<?php echo WEB_ROOT;?>forum/" target="_blank" class="extLink">
    Forum <img src="images/externalLinkGraphic.gif"></a><br>
Find out what has changed about NW in the announcements section of the 
    <a href="http://ninjawars.proboards19.com/index.cgi?board=ann" target="_blank" class="extLink">Forum 
        <img src="images/externalLinkGraphic.gif"></a>.
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

<?php
include "interface/footer.php";
?>


