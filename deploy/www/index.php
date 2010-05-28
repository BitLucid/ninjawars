<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
$action          = in('action');
$login           = !empty($action); // A request to login.
$logout          = in('logout');
$is_logged_in    = is_logged_in();
$login_error     = false;
$just_logged_out = false;
$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
$player_info     = array();

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

$is_not_logged_in = !$is_logged_in;
$username         = get_username();
$user_id          = get_user_id();
$player_info      = get_player_info();

// Player counts.
$stats          = membership_and_combat_stats();
$player_count   = $stats['player_count'];
$players_online = $stats['players_online'];

$header = render_template('header.tpl', array('title'=>'Live By the Sword', 'body_classes'=>'main-body', 'is_index'=>true, 'logged_in'=>get_user_id(), 'section_only'=>in('section_only'))); // Writes out the html,head,meta,title,css,js.

$version = 'NW Version 1.7.1 2009.11.22';

// Display main iframe page unless logged in.
$main_src = 'main.php';
if ($is_logged_in) {
    $level = getLevel($username);
    $main_src = 'list_all_players.php';

    if ($level == 1) {
    	$main_src = 'tutorial.php';
    } elseif ($level < 6) {
    	$main_src = 'attack_player.php';
    }
}

$parts = get_certain_vars(get_defined_vars(), $whitelist=array());

if (!$is_logged_in) {
    echo render_template('splash.tpl', $parts); // Non-logged in template.
} else {
    echo render_template('index.tpl', $parts); // Logged in template.
}

echo render_template('footer.tpl', array('quickstat'=>null));
?>
