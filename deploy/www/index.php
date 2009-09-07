<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
ob_start(null, 1); // Buffer and output it in chunks.

$login        = (in('action') == 'login' ? true : false); // Request to login.
$logout       = in('logout');
$is_logged_in = is_logged_in();
$login_error  = false;
$logout_occurred = false;

// Logout/already logged in/login

if ($logout) { 
    // When a logout action is requested
	logout(); // essentially just kill the session, and don't redirect.
	$logout_occurred = true;
} elseif ($is_logged_in) { 
    // When user is already logged in.
	$logged_in['success'] = $is_logged_in;
} elseif ($login) { 
	// Specially escaped password input, put into login.
	$logged_in    = login_user(in('user'), in('pass', null, 'toPassword'));
	$is_logged_in = $logged_in['success'];
	if(!$is_logged_in){
    	// Login was attempted, but failed, so show an error.
    	$login_error = true;
	}
}


$display_when_logged_in     = display_when('logged_in');
$display_when_logged_out    = display_when('logged_out');
$display_when_logout_occurs = display_when('logout_occurs');

$username = get_username();

//var_dump($logout, $is_logged_in, $logout, $login_error, $username); die();

$referrer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

// Display main page unless logged in.
$main_src = 'main.php';
if ($is_logged_in) {
	$main_src = 'list_all_players.php';
}

// Today's Information Section of Left Column 
$sql = new DBAccess(); // *** Instantiates wrapper class for manipulating pdo.
$GLOBALS['sql'] = $sql; // Put sql into globals array. :(

$stats          = membership_and_combat_stats($sql);
$vicious_killer = $stats['vicious_killer'];
//TODO:  Get the player id instead of the actual name.
$player_count   = $stats['player_count'];
$players_online = $stats['players_online'];

$header = render_html_for_header('Ninja Wars: Live By the Sword', 'main-body');
// render_html_for_header Writes out the html,head,meta,title,css,js.

$parts = array(
    'display_when_logged_in' => $display_when_logged_in,
    'display_when_logged_out' => $display_when_logged_out,
    'display_when_logout_occurs' => $display_when_logout_occurs,
    'is_logged_in' => $is_logged_in,
    'is_not_logged_in' => !$is_logged_in,
    'username' => $username,
    'WEB_ROOT' => WEB_ROOT,
    'IMAGE_ROOT' => IMAGE_ROOT,
    'referrer' => $referrer,
    'vicious_killer' => $vicious_killer,
    'player_count' => $player_count,
    'players_online' => $players_online,
    'main_src' => $main_src,
    'is_logged_in' => $is_logged_in,
    'header' => $header,
);
echo render_template('index.tpl', $parts);
// Username still exists here.

// TODO: Abstract the display or don't display toggles to just be booleans or integers.
// TODO: Change which items get toggled expanded when login occurs with the javascript.
// TODO: Make sure that all the password modifying changes are secure.

?>
