<?php

$char_id = self_char_id();

// Initialize page display vars.
$player_info = array();
$title       = 'Mini-chat Solo Page';
$unread_message_count = 0;

// Get the actual values of the vars.
$player_info = self_info();
$username = $player_info['uname'];

// Create the settings to pass to the page.
$options = array('is_index'=>true);

// Assign these vars to the template.
$parts = array(
	'body_classes'     => 'mini-chat-solo-page'
	, 'version'          => 'NW Version 1.7.5 2010.12.05'
	, 'logged_in'        => (bool)$char_id
	, 'is_not_logged_in' => !$char_id
	, 'username'         => $username
);

// Logged in display.
display_page('mini-chat.section.tpl', $title, $parts, $options);