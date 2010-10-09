<?php
require_once(LIB_ROOT.'specific/lib_player.php'); // Player info display pieces.
require_once(LIB_ROOT.'specific/lib_status.php'); // Status alterations.

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
	die();
}

$changeprofile = in('changeprofile');
$newprofile    = trim(in('newprofile', null, null)); // Unfiltered input.

$username = get_username();
$user_id  = get_user_id();

$profile_changed    = false;
$profile_max_length = 500; // Should match the limit in limitStatChars.js - ajv: No, limitStatChars.js should be dynamically generated with this number from a common location -

$successMessage = null;
if ($changeprofile == 1) {
    // Limit the profile length.
	if ($newprofile != '') {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET messages = :profile WHERE uname = :player');
		$statement->bindValue(':profile', $newprofile);
		$statement->bindValue(':player', $username);
		$statement->execute();	// todo - test for success

		$profile_changed = true;
	} else {
		$error = 'Cannot enter a blank profile.';
	}
}

// Password and email changing systems exist in account.php (& account.tpl).


$char_obj         = new Player($user_id);
$player           = get_player_info();
$class_theme      = class_theme($char_obj->class_identity());
$level_category   = level_category($player['level']);
$status_list      = get_status_list();
$gravatar_url     = generate_gravatar_url($player['player_id']);
$gurl             = $gravatar_url;
$rank_display     = get_rank($username); // rank display.
$profile_editable = $player['messages'];
$profile_display  = out($profile_editable);

$parts = get_certain_vars(get_defined_vars(), array('player', 'level_category', 'status_list'));

// Set the parts array's player clan if any is found.
if ($parts['player_clan'] = get_clan_by_player_id($user_id)) {
    // Set the char clan name and id for later usage.
	$parts['clan_name'] = $parts['player_clan']->getName();
	$parts['clan_id']   = $parts['player_clan']->getID();
}

display_page(
	'stats.tpl'
	, 'Your Stats'
	, $parts
	, array(
		'quickstat' => 'player'
	)
);
?>
