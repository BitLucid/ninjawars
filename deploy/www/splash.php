<?php
require_once(LIB_ROOT.'control/lib_player_list.php'); // Used for member_counts()

$title       = 'Live by the Sword';
$unread_message_count = 0;

$options = array('is_index'=>true);

$member_counts = member_counts();

// Assign these vars to the template.
$parts = array(
	'main_src'           => 'main.php'
	, 'body_classes'     => 'main-body splash'
	, 'version'          => 'NW Version 1.7.5 2010.12.05'
	, 'members'          => $member_counts['active']
	, 'membersTotal'     => $member_counts['total']
);

$parts['body_classes'] = 'main-body splash';
display_page('splash.tpl', $title, $parts, $options);
?>
