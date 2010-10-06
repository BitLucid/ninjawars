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
$deleteAccount     = (in_array($in_delete_account, array(1,2)) ? $in_delete_account : null); // Stage of delete process.

$in_changePass = in('changepass');
$change_pass   = (in_array($in_changePass, array(1,2)) ? $in_changePass : null); // Stage of password change process

$in_oldPass     = in('oldpassw');
$in_newPass     = trim(in('newpassw'));
$in_confirmPass = trim(in('confirmpassw'));

$in_changeEmail = in('changeemail');
$change_email   = (in_array($in_changeEmail, array(1,2)) ? $in_changeEmail : null); // Stage of email change process

$in_newEmail     = trim(in('newemail'));
$in_confirmEmail = trim(in('confirmemail'));

$passW = in('passw', null); // *** To verify whether there's a password put in.

$changeprofile = in('changeprofile');
$newprofile    = trim(in('newprofile', null, null)); // Unfiltered input.

$username = get_username();
$user_id  = get_user_id();

$confirm_delete     = false;
$profile_changed    = false;
$profile_max_length = 500; // Should match the limit in limitStatChars.js - ajv: No, limitStatChars.js should be dynamically generated with this number from a common location -

$delete_attempts = (SESSION::is_set('delete_attempts') ? SESSION::get('delete_attempts') : null);

$successMessage = null;

if ($deleteAccount) {
	$verify = false;
	$verify = is_authentic($username, $passW);

	if ($verify && !$delete_attempts) {
	    // *** Username & password matched, on the first attempt.
		pauseAccount($user_id); // This may redirect and stuff?
		logout_user();
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
	} else {
		$error = 'Cannot enter a blank profile.';
	}
} else if ($change_email) {
	if ($change_email == 2) {
		$verify = is_authentic($username, $passW);

		if ($verify) {
			if ($in_newEmail === $in_confirmEmail) {
				if (!email_is_duplicate($in_newEmail)) {
					if (email_fits_pattern($in_newEmail)) {
						changeEmail($user_id, $in_newEmail);
						$change_email = 0;
						$successMessage = 'Your email has been updated.';
					} else {
						$error = 'Your email must be a valid email address containing a domain name and no spaces.';
					}
				} else {
					$error = 'The email you provided is already in use.';
				}
			} else {
				$error = 'Your new emails did not match.';
			}
		} else {
			$error = 'You did not provide the correct current password.';
		}
	}
} else if ($change_pass) {
	if ($change_pass == 2) {
		$verify = is_authentic($username, $passW);

		if ($verify) {
			if ($in_newPass === $in_confirmPass) {
				changePassword($user_id, $in_newPass);
				$change_pass = 0;
				$successMessage = 'Your password has been updated.';
			} else {
				$error = 'Your new passwords did not match.';
			}
		} else {
			$error = 'You did not provide the correct current password.';
		}
	}
}

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
