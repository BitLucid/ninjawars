<?php


/**
 * Update the information of a viewing observer, or player.
**/
function update_activity_info(){
	$sql = new DBAccess();
	// ******************** Usage Information of the browser *********************
	$remoteAddress = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL);
	$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL);
	$referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);

	// ************** Setting anonymous and player usage information

	if (!SESSION::is_set('online')) { 	// *** Completely new session, update latest activity log.
	    if($remoteAddress) { // *** Delete prior to trying to re-insert into the people online.
			$sql->query("DELETE FROM ppl_online WHERE ip_address='".$_SERVER['REMOTE_ADDR']."'
				 or session_id = '".session_id()."'");
	    }
	    // Update viewer data.
	  	$sql->query("INSERT INTO ppl_online (session_id, activity, ip_address, refurl, user_agent) ".
		         "VALUES ('".session_id()."', now(), '$remoteAddress', '$referer', '$userAgent')");
	 	SESSION::set('online', true);
	} else { // An already existing session.
		if (is_logged_in()) { // A logged in player, update their activity time
		    $sql->query("UPDATE ppl_online SET activity=now(), member='y' WHERE session_id='".session_id()."'");
		} else { // Un-logged-in observer, as login has not yet occurred.
			$sql->query("UPDATE ppl_online SET activity=now() WHERE session_id='".session_id()."'");
		}
	}
}



/**
 * Checks for the requirements on pages where you have to be alive to view/act within them.
**/
function render_error_if_dead($alive_required, $players_health, $status_array){
	if ($alive_required) { // *** That page requires the player to be alive to view it.
		if (!$players_health) {
			return "<span class='ninja-notice'>You are a ghost.
				  You must resurrect before you may act again. 
				  Go to the <a href='shrine.php'>shrine</a> 
				  for the monks to bring you back to life.</span>";
		} else {
			if ($status_array['Frozen']) {
				return "<span class='ninja-notice'>You are currently 
					<span style='skyBlue'>frozen</span>.
					 You must wait to thaw before you may continue.</span>";
	    	}
		}
    }
    return null; // When there isn't any error.
}



/**
 * Writes out the header for all the pages.
 * Will need a "don't write header" option for jQuery iframes.
**/
function write_html_for_header($title=null, $body_classes=''){ 
    $title_html = '';
    if($title){$title_html = "<title>$title</title>";}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="keywords" content="ninjawars, ninja wars, ninja, samurai, free online game, 
	free games, this here is not your mommas naruto game">
	<meta name="description" content="Ninjawars: battle other ninja for survival.">
	<?=$title_html?>
    <link rel="stylesheet" type="text/css" href="<?=WEB_ROOT?>css/style.css">		
	<!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="<?=WEB_ROOT?>css/ie-6.css">
	<![endif]-->
	<!-- [if gte IE 7]>
	<link rel="stylesheet" type="text/css" href="<?=WEB_ROOT?>css/ie.css">
	<![endif]-->
	<style type="text/css">
	<!-- Temporary location for NEW CSS -->
	</style>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/lib_refresh.js"></script>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/showHide.js"></script>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/body_init.js"></script>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/expand_chat.js"></script>
	<?php if(DEBUG) { ?>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/var_dump.js"></script>
	<script type="text/javascript" src="<?=WEB_ROOT?>js/print_r.js"></script>
	<?php } ?>
</head>
<body class='<?=$body_classes?>'>
	<?php
}


/**
 * Returns the state of the player from the database,
 * defaults to the currently logged in player, but can act on any player
 * if another username is passed in.
**/
function get_player_info($username=null){
	$sql = new DBAccess();
	$player_data = null;
	$username = either($username, $_SESSION['username']); // Default to current.
	$sel_player = "select * from players where uname = '".$username."' limit 1";
	$player_data = $sql->QueryRowAssoc($sel_player);
	// TODO: Make this return a player object instead of a player array. Or also a player obj?
	return $player_data;
}



/* Potential solution for hash-based in-iframe navigation.
function hash_page_name($page_title=null){
	$page = basename(__FILE__, ".php");
	if ($page && file_exists($page)){
	$page = urlencode($page);
	var_dump($page);
	echo
	<<< EOT
	 <script type="text/javascript">
			if(document.location.hash){
				document.location.hash = '$page';
			}
			</script>
EOT;
	}
}

hash_page_name($page_title);
*/

?>
