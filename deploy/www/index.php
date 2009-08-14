<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
ob_start(null, 1); // Buffer and output it in chunks.

$login = (in('action') == 'login'? true : false); // Request to login.
$logout = in('logout');
$is_logged_in = is_logged_in();
$login_error = false;

if($logout){ // When a logout action is requested
	logout(); // essentially just kill the session, and don't redirect.
} elseif ($is_logged_in){ // When user is already logged in.
	$logged_in['success'] = $is_logged_in;
} elseif($login) { // Only login if not currently logging out.
	$pass = in('pass', null, 'toPassword'); // Specially escaped password input.
	$logged_in = login_user(in('user'), $pass);
	$is_logged_in = $logged_in['success'];
}

if($login && !$is_logged_in){
    // Login was attempted, but failed, so show an error.
	$login_error = true;
}

$display_when_logged_in = display_when('logged_in');
$display_when_logged_out = display_when('logged_out');
$display_when_logout_occurs = display_when('logout_occurs');

$username = get_username();
$referrer = (isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : null);

// Display main page unless logged in.
$main_src = 'main.php';
if($is_logged_in){
    $main_src = 'list_all_players.php';
}

// TODO: Abstract the display or don't display toggles to just be booleans or integers.
// TODO: Change which items get toggled expanded when login occurs with the javascript.
// TODO: Make sure that all the password modifying changes are secure.
// TODO: create lib_deity backup players function, copy players table or as insert.
// TODO: Delete beyond a certain limit of entries in levelling_log dueling_log, and players_backup (5*players count?)
// TODO: Make default filtering be toWord, and change item-related in()s to deal with their double word.
// TODO: Put player-led multi-checking into play.
// TODO: Limit quickstats js refresh by last-occurred.
// TODO: Switch to using template-like systems/functions.
// TODO: Give the "No such ninja" message (e.g. when linking from the ---- chat) a link back to a sane part of the list.
// TODO: make clans links in the player list.
// TODO: Fix the white backgrounds in the iframes in IE.
// TODO: Fix the &apos; things with the chat.
// TODO: Limit unnecessary includes via lib_header.



// Writes out the html,head,meta,title,css,js.
write_html_for_header('Ninja Wars: Live By the Sword', 'main-body trial-font');
?>

<div id="content">

<div id="menu" class="login-menu">
	<div id="menu-start">
		<?php if(!$is_logged_in){ ?>
		<div id="login-bar">
			<form id="login-form" action="<?=WEB_ROOT?>index.php#" method="post">
		        <span class="text">
		            <input type="hidden" name="ref" value="<?php echo $referrer; ?>"/>
		            <label>
		                Username:
		                <input name="user" type="text" class="itext"/>
		            </label>
		            &nbsp;
		            <label>
		                Password:
		                <input name="pass" type="password" class="itext"/>
		            </label>
		            <input name="action" type="submit" value="login" class="ibutton formButton"/>
		        </span>
		    </form>
		</div>
		<?php } else { /*Display when logged in*/ ?>
		<div class="logged-in-bar">
	        	<?='<a target="main" href="player.php?player='.$username.'">'.$username.'</a>';?>
	        	 | <?='<a target="main" href="mail_read.php">mailbox</a>';?>
	        	 <span id='logged-in-bar-health'> </span>
	    </div>
		<?php } /* End of login/out conditional display.*/ ?>

	</div>
	<div id="menu-info">
	    <span class="signup-link" <?=$display_when_logged_out;?>>
        <a target="main" href="<?=WEB_ROOT?>signup.php?referrer=<?php echo $referrer; ?>">
            Create a Ninja!
        </a> |
        </span>
		<span <?=$display_when_logged_out;?>>
			<a href="<?=WEB_ROOT?>lostpass.php" target="main" class="blend side">&nbsp;Lost&nbsp;Password?</a> |
		</span>
		<a href="rules.php" target="main">Rules</a> |
        <a href="tutorial.php" target="main">Intro</a> |
		<a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki <img class="extLink" src="images/externalLinkGraphic.gif" alt=""></a> |
		<a href="http://ninjawars.proboards19.com" target="_blank" class="extLink">Forum </a><img class="extLink"  src="images/externalLinkGraphic.gif" alt=""> |
		<a href="http://ninjawars.proboards19.com/index.cgi?board=ann" target="_blank" class="extLink">News</a><img class="extLink" src="images/externalLinkGraphic.gif" alt="">
	</div>
	<div id="menu-end">
		<span <?=$display_when_logged_in;?>>
			<a href="index.php?logout=true">LOGOUT <img class="logout-stop" src="images/stop_square.png" alt="[]"></a>
		</span>
		<span <?=$display_when_logout_occurs;?>>
			You are now logged out.
		</span>
	</div>
</div>

<?php if($login_error){ ?>
<div class="error">
	That password/username combination was incorrect.  Be aware that usernames are case sensitive.
	Or request help with
	<a target='_blank' href='http://ninjawars.proboards.com/index.cgi?board=bug&amp;action=display&amp;thread=1051'>login issues</a>
	on the forum.
</div>
<?php } ?>

<div class="three-columns">

  <!-- LEFT COLUMN -->
<div id="leftColumn" class="column">
    <div id="logo" class="boxes special">
       <a href="list_all_players.php" target="main"><img src="<?php echo IMAGE_ROOT;?>50pxShuriken.png" alt="Home"></a>
    </div>
  	<?php if($is_logged_in){ ?>
	<div id="actions" class="boxes active">
		<div class="box-title">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('actions-menu');">Actions
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>
	  	<ul class="basemenu" id="actions-menu">
	  		<li><a href="attack_player.php" target="main">Combat</a></li>
	  		<li><a href="clan.php" target="main">Clan</a></li>
	  		<li><a href="inventory.php" target="main">Inventory</a></li>
	  		<li>
	  			<ul class="submenu">
	  				<li><a href="inventory_mod.php?item=Speed%20Scroll&amp;selfTarget=1&amp;link_back=inventory" target="main">Speed</a><br></li>
	  				<li><a href="inventory_mod.php?item=Stealth%20Scroll&amp;selfTarget=1&amp;link_back=inventory" target="main">Stealth</a><br></li>
	  			</ul>
	  		</li>
	  		<li><a href="skills.php" target="main">Skills</a></li>
			<li><a href="stats.php" target="main">Stats</a></li>
	  		<li><a href="mail_read.php" target="main">Mail</a></li>
	  	</ul>
	</div>
	<div id="places" class="boxes active">
		<div class="box-title">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('places-menu');">Places
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>
	  	<ul id="places-menu">
	  		<li><a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt=""></a><br>
	  		<li><a href="dojo.php" target="main">Dojo</a><br>
	  		<li><a href="casino.php" target="main">Casino</a><br>
	  		<li><a href="work.php" target="main">Work</a><br>
	  		<li><a href="shop.php" target="main">Shop</a><br>
	  		<li><a href="shrine.php" target="main">Shrine <img src="images/shrine.png" alt=""></a><br>
	  		<li>
	  			<ul class="submenu">
	  				<li id='resurrect-link'><a href="shrine_mod.php?restore=1" target="main">Resurrect</a></li>
	  				<li id='heal-link'><a href="shrine_mod.php?max_heal=1" target="main">Heal</a></li>
	  			</ul>
	  		</li>
	  	</ul>
	</div>
	<?php } /*End of display when logged in*/ ?>

	<!-- Today's Information Section of Left Column -->
<?php
// Move these to the beginning of the file.
$sql = new DBAccess(); // *** Instantiates dbAccess, wrapper class for manipulating pdo.
$GLOBALS['sql'] = $sql; // Put sql into globals array.

$stats = membership_and_combat_stats($sql);
$vicious_killer = $stats['vicious_killer'];
$player_count = $stats['player_count'];
$players_online = $stats['players_online'];
?>
<!-- Display stats section. -->

<div id='vicious-killer' class='boxes'>
	<div class='box-title'>
		<a href='#' class='show-hide-link' onclick="toggle_visibility('vicious-killer-menu');">
			Fastest Killer: <img class='show-hide-icon' src='images/show_and_hide.png' alt='+/-'>
		</a>
	</div>
	<a id='vicious-killer-menu' href='player.php?player=<?=$vicious_killer;?>' target='main'><?=$vicious_killer;?></a>
	</div><!-- End of vicious killer div -->
	<div id='ninja-count' class='boxes passive'>
		<div class='box-title'>
			<a href='#' class='show-hide-link' onclick="toggle_visibility('ninja-count-menu');">
				Ninjas: <img class='show-hide-icon' src='images/show_and_hide.png' alt='+/-'>
			</a>
		</div>
		<p id='ninja-count-menu'><?=$players_online;?> Online <br> <?=$player_count;?> Total</p>
	</div>

	<!-- End of stats Section -->



	<div id="music" class="boxes passive">
		<div class="box-title">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('music-player');">Music
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>

		<object type="audio/x-midi" data="music/samsho.mid" id="music-player">
		  <param name="src" value="music/samsho.mid">
		  <param name="autoplay" value="true">
		  <param name="autoStart" value="0">
		    <a href="music/samsho.mid">Play <img class="play-button" src="images/bullet_triangle_green.png" alt="&gt;"></a>
		</object>
	</div>

	<div id="links" class="boxes passive">
		<div class="box-title">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('links-menu');">Links
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>
		<ul id="links-menu">
     		<li><a href="about.php" target="main">Tutorial</a></li>
			<li><a href="staff.php" target="main">Staff</a></li>
			<li><a href="duel.php" target="main">Duels</a></li>
				<!--  <a href="vote.php" target="main">Vote For NW </a><br>  -->
				<!--  <a href="http://www.cafeshops.com/ninjawars" target="_blank">Online Shop</a><br> -->
			<li><a href="http://ninjawars.proboards19.com/index.cgi?action=calendar" target="_blank" class="extLink">Calendar <img class="extLink" src="images/externalLinkGraphic.gif" alt=""></a></li>
			<!--   <a href="donate.php" target="main">Donate</a><br> -->
			<!-- <a href="members.php" target="main">Members</a><br> -->
		</ul>
	</div>
</div><!-- End of left Column div-->




<!-- Substitute image and "catchphrases" here -->

<div id="centerColumn" class="column"><!-- top menu starts here -->


	<div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
	    <iframe id="main" name="main" class="main-iframe" src="<?php echo $main_src; ?>" frameborder="0">
	    			Main Content Display Section (Frames Not Supported)
	    </iframe>
	</div><!-- End of mainFrame div -->

<!-- LOCATION FOR CONTENT UNDER THE MAIN DISPLAY SECTION -->
<!--
<div class="created-by">
	catchphrase games
</div>
-->
</div><!-- End of centerColumn div -->




<div id="rightColumn" class="column"><!-- RIGHT COLUMN -->
	<div id="player-list" class="boxes special centered">
  		<a href="list_all_players.php" target="main"><span style="color:brown;">Ni</span><span style="color:red">nj</span><span style="color:orange;">as</span> <img src="images/smallArrows.png" alt="&gt;&gt;&gt;"></a>
  	</div>
  	<div id="ninja-search" class="boxes active">
  		<div class="box-title centered">Ninja Search</div>
  		<form id="player_search" action="list_all_players.php" target="main" method="get" name="player_search">Ninja:
			<input id="searched" type="text" maxlength="50" name="searched" class="textField">
			<input id="hide" type="hidden" name="hide" value="dead">
			<input type="submit" value="find" class="formButton">
		</form>
  	</div>
  	<?php if($is_logged_in){ ?>
	<div id="quick-stats" class="boxes">
		<div class="box-title centered">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('quickstats-and-switch-stats');">Quick Stats
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>
		<div id="quickstats-and-switch-stats">
			<div class="centered">
				<a href="quickstats.php" target="quickstats">Player</a> | <a href="quickstats.php?command=viewinv" target="quickstats">Inventory</a>
			</div>
			<div id="quickstats-frame-container">
				<iframe id="quickstats" src="quickstats.php" frameborder="0" name="quickstats">
					Quick Stats Iframe Display section (Iframes Not supported by this browser)
				</iframe>
			</div>
		</div><!-- End of quickstats and switch container -->
	</div><!-- End of quickstats section. -->
	<?php } /*End of display when logged in*/ ?>
	<div id="village-chat" class="boxes active">
		<div class="box-title centered">
			<a href="#" class="show-hide-link" onclick="toggle_visibility('chat-and-switch');">Chat
				<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
			</a>
		</div>
		<div id="chat-and-switch">
			<div class="chat-switch centered">
				<a href="village.php" target="main">Full Chat <img src="images/chat.png" alt=""> </a>
				<a href="mini_chat.php?chat_length=20" target="mini_chat">Refresh</a>
			</div>
			<!-- TODO: move chat submit box out here. -->
			<div id="mini-chat-frame-container" class='chat-collapsed'>
				<iframe id="mini_chat" name="mini_chat" src="mini_chat.php" frameborder="0">
					Mini Chat Iframe Display Section (Iframes not supported by this browser)
				</iframe>
			</div>
			<div id="expand-chat">
				<a href="mini_chat.php?chatlength=360" target="mini_chat">
					View more chat messages
					<img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
				</a>
			</div>
		</div>
	</div>
</div><!--- End of rightColumn div -->

</div><!-- End of columns div -->

</div><!-- End of bodyContent div -->

<!-- Validated as of Feb, 2009 with notices about self-closing br tags. -->
</body>
</html>
