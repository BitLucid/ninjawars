<?php
require_once(LIB_ROOT.'specific/lib_player.php'); // Player info display pieces.
require_once(LIB_ROOT.'specific/lib_status.php'); // Status alterations.

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
	die();
}

// *** To verify that the delete request was made.
$in_delete_account = in('deleteaccount');
$deleteAccount     = ($in_delete_account == 1 ? 1 :
    ($in_delete_account == 2 ? 2 : null)); // Stage of delete process.

$in_changePass = in('changepass');
$changePass    = ($in_changePass && $in_changePass == 1 ? 1 : null);

$newPass = in('newpass', null);
$passW   = in('passw', null); // *** To verify whether there's a password put in.

$changeprofile = in('changeprofile');
$newprofile    = in('newprofile', null, null); // Unfiltered input.

$username = get_username();
$user_id = get_user_id();

$player = get_player_info();
$confirm_delete = false;
$profile_changed = false;
$profile_max_length = 500; // Should match the limit in limitStatChars.js

$delete_attempts = (SESSION::is_set('delete_attempts') ? SESSION::get('delete_attempts') : null);

if ($deleteAccount) {
	$verify = false;
	$verify = is_authentic($username, $passW);

	if ($verify && !$delete_attempts) {
	    // *** Username&password matched, on the first attempt.
		pauseAccount($username); // This may redirect and stuff?
	} else {
	    if ($deleteAccount == 2) {
	        SESSION::set('delete_attempts', 1);
	        $error = 'Deleting of account failed, please email '.SUPPORT_EMAIL;
	    } else {
    	    $confirm_delete = true;
    	}
	}
} else if ($changeprofile == 1) {
    // Limit the profile length.
	if ($newprofile != '') {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET messages = :profile WHERE uname = :player');
		$statement->bindValue(':profile', $newprofile);
		$statement->bindValue(':player', $username);
		$statement->execute();	// todo - test for success
		$profile_changed = true;
		$player['messages'] = $newprofile;
	} else {
		$error = 'Cannot enter a blank profile.';
	}
}

$level_category   = level_category($player['level']);
$status_list      = get_status_list();
$gravatar_url     = generate_gravatar_url($player['player_id']);
$gurl = $gravatar_url;
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
