<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
require_once(LIB_ROOT.'control/lib_player_list.php'); // Used for member_counts()

$char_id = self_char_id();

if(!$char_id){
	require_once(SERVER_ROOT.'www/splash.php');
} else {
	// Initialize page display vars.
	$username    = null;
	$player_info = array();
	$new_player = null;
	$title       = 'Live by the Shuriken';
	$unread_message_count = 0;

	// Get the actual values of the vars.
	$player_info = self_info();
	$username = $player_info['uname'];
	$level = $player_info['level'];
	$new_player = $level<2;

	$unread_message_count = unread_message_count();
	
	$member_counts = member_counts();
	
	// Create the settings to pass to the page.
	$options = array('is_index'=>true);

	// Assign these vars to the template.
	$parts = array(
		'main_src'           => 'main.php'
		, 'body_classes'     => 'main-body'
		, 'version'          => 'NW Version 1.7.5 2010.12.05'
		, 'logged_in'        => (bool)$char_id
		, 'is_not_logged_in' => !$char_id
		, 'username'         => $username
		, 'new_player'		 => $new_player
		, 'user_id'          => $char_id
		, 'player_info'      => $player_info
		, 'unread_message_count' => $unread_message_count
		, 'members'          => $member_counts['active']
		, 'membersTotal'     => $member_counts['total']
	);
	
	// Logged in display.
	display_page('index.tpl', $title, $parts, $options);
} // End of check for displaying the splash page.
?>
