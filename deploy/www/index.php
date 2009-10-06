<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
ob_start(null, 1); // Buffer and output it in chunks.

$login        = (in('action') == 'login' ? true : false); // Request to login.
$logout       = in('logout');
$is_logged_in = is_logged_in();
$login_error  = false;
$just_logged_out = false;
$referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

$sql = new DBAccess(); // *** Instantiates wrapper class for manipulating pdo.
$GLOBALS['sql'] = $sql; // Put sql into globals array. :(

// Logout/already logged in/login

if ($logout) { // on logout, kill the session and don't redirect. 
	logout(); 
	$just_logged_out = true;
} elseif ($is_logged_in) {     // When user is already logged in.
	$logged_in['success'] = $is_logged_in; 
} elseif ($login) { 	// Specially escaped password input, put into login.
	$logged_in    = login_user(in('user'), in('pass', null, 'toPassword'));
	$is_logged_in = $logged_in['success'];
	if(!$is_logged_in){ // Login was attempted, but failed, so display an error.
    	$login_error = true;
	} else {
	    header("Location: index.php"); 
	    exit(); // Login redirect to prevent the refresh postback problem.
	}
}
$username = get_username();
$user_id = get_user_id();

// Today's Information Section of Left Column 

$stats          = membership_and_combat_stats($sql);
$vicious_killer = $stats['vicious_killer'];
$player_count   = $stats['player_count'];
$players_online = $stats['players_online'];
// TODO: fix how vicious killer is only using duels as a criteria right now.


$header = render_html_for_header('Live By the Sword', 'main-body');
// render_html_for_header Writes out the html,head,meta,title,css,js.

$version = 'NW Version 1.6.0 2009.09.06';
$is_not_logged_in = !$is_logged_in;


// Display main iframe page unless logged in.
$main_src = 'main.php';
if ($is_logged_in) {
	$main_src = 'list_all_players.php';
}

$parts = get_certain_vars(get_defined_vars(), array('vicious_killer'));
if(!$is_logged_in){
    echo render_template('splash.tpl', $parts); // Non-logged in template.
} else {
    echo render_template('index.tpl', $parts); // Logged in template.
}

// TODO: Make sure that all password modifying changes are secure.

?>
