<?php
// Licensed under the creative commons license.  See the staff.php page for more detail.
require_once(LIB_ROOT.'control/lib_player_list.php'); // Used for member_counts()

// Tag: megaman
$char_id = self_char_id();

if(!$char_id){
	require_once(SERVER_ROOT.'www/splash.php');
} else {
	// Initialize page display vars.
	$player_info = array();
	$unread_message_count = 0;

	// Get the actual values of the vars.
	$player_info = self_info();
	$ninja = new Player(self_char_id());

	$unread_message_count = unread_message_count();
	
	$member_counts = member_counts();
	
	// Create the settings to pass to the page.
	$options = array('is_index'=>true);

	// Assign these vars to the template.
	$parts = array(
		'main_src'           => 'main.php'
		, 'body_classes'     => 'main-body'
		, 'version'          => 'NW Version 1.7.5 2010.12.05'
		, 'ninja'			 => $ninja
		, 'player_info'      => $player_info
		, 'unread_message_count' => $unread_message_count
		, 'members'          => $member_counts['active']
		, 'membersTotal'     => $member_counts['total']
	);
	
	// Logged-in only.
	$title       = 'Live by the Shuriken';
	display_page('index.tpl', $title, $parts, $options);
} // End of check for displaying the splash page.
