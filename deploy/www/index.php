<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
$action          = in('action');
$login           = !empty($action); // A request to login.
$logout          = in('logout');
$login_error     = false;
$just_logged_out = false;
$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

$is_logged_in    = is_logged_in();

// Logout/already logged in/login behaviors
if ($logout) { // on logout, kill the session and don't redirect. 
	logout(); 
	$just_logged_out = true;
} elseif ($is_logged_in) {     // When user is already logged in.
	$logged_in['success'] = $is_logged_in; 
} elseif ($login) { 	// Specially escaped password input, put into login.
	$logged_in    = login_user(in('user'), in('pass', null, null));
	$is_logged_in = $logged_in['success'];

	if (!$is_logged_in) { // Login was attempted, but failed, so display an error.
		$login_error = true;
	} else {
		header("Location: index.php"); 
		exit(); // Login redirect to prevent the refresh postback problem.
	}
}

$username    = null;
$player_info = array();
$level       = null;
$main_src    = 'main.php'; // Display main iframe page unless logged in.
$title       = 'Live by the Sword';

$user_id = get_user_id();

if ($user_id) {
	// Only bother trying to change these if logged in.
	$level       = getLevel($username);
	$username    = get_username();
	$player_info = get_player_info();

	$main_src = 'list_all_players.php';

	if ($level == 1) {
		$main_src = 'tutorial.php';
	} elseif ($level < 6) {
		$main_src = 'attack_player.php';
	}
}

// Player counts.
$stats          = membership_and_combat_stats();
$player_count   = $stats['player_count'];
$players_online = $stats['players_online'];

$options = array(/*'section_only'=>in('section_only'), */'is_index'=>true);

// Assign these vars to the template.
$parts = array(
	'main_src'           => $main_src
	, 'body_classes'     => 'main-body'
	, 'version'          => 'NW Version 1.7.2 2010.06.01'
	, 'logged_in'        => (bool)$user_id
	, 'is_not_logged_in' => !$user_id
	, 'username'         => $username
	, 'user_id'          => $user_id
	, 'player_info'      => $player_info
	, 'player_count'     => $player_count
	, 'players_online'   => $players_online
	, 'level'            => $level
	, 'stats'            => $stats
	, 'login_error'      => $login_error
	, 'referrer'         => $referrer
);

if (!$user_id) {
	// Non-logged in display.
	display_page('splash.tpl', $title, $parts, $options);
} else {
	// Logged in display.
	display_page('index.tpl', $title, $parts, $options);
}
?>
