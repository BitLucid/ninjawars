<?php
require_once(LIB_ROOT."specific/lib_player.php"); // Player info display pieces.
require_once(LIB_ROOT."specific/lib_status.php"); // Status alterations.

$page_title = "Your Stats";
$private    = true;
$alive      = false;
$quickstat  = "viewinv";

include SERVER_ROOT."interface/header.php"; // Not sure whether this has to come first still or not.

// *** To verify that the delete request was made.
$in_delete_account = in('deleteaccount');
$deleteAccount     = ($in_delete_account == 1 ? 1 :
    ($in_delete_account == 2 ? 2 : null)); // Stage of delete process.

$in_changePass = in('changepass');
$changePass    = ($in_changePass && $in_changePass == 1 ? 1 : null);

$newPass = in('newpass', null, 'toPassword');
$passW   = in('passw', null, 'toPassword'); // *** To verify whether there's a password put in.

$changeprofile = in('changeprofile');
$newprofile    = in('newprofile', null, 'toMessage');

$username = get_username();

$player = get_player_info();
$confirm_delete = false;
$profile_changed = false;
$profile_max_length = 500; // Should match the limit in limitStatChars.js

$delete_attempts = (SESSION::is_set('delete_attempts')?SESSION::get('delete_attempts') : null);

if ($deleteAccount) {
	$verify = false;
	$verify = is_authentic($username, $passW);
	if ($verify == true && !$delete_attempts) {
	    // *** Username&password matched, on the first attempt.
		pauseAccount($username); // This may redirect and stuff?
	} else {
	    if($deleteAccount == 2){
	        SESSION::set('delete_attempts', 1);
	        $error = 'Deleting of account failed, please email '.SUPPORT_EMAIL;
	    } else {
    	    $confirm_delete = true;
    	}
	}
} else if ($changeprofile == 1) {
    // Limit the profile length.
	if ($newprofile != "") {
		$sql->Update("UPDATE players SET messages = '".sql($newprofile)."' WHERE uname = '".sql($username)."'");
		$affected_rows = $sql->a_rows;
		$profile_changed = true;
	} else {
		$error = "Can not enter a blank profile.";
	}
}

$level_and_cat = render_level_and_category($player['level']);
$status_list = render_status_section();
$avatar_display = render_avatar_section($player['player_id']);// include and render from player.php
$rank_display = get_rank($username, $sql); // rank display.

$profile_editable = $player['messages'];
$profile_display = out($profile_editable);

//var_dump($profile_editable);die();

$parts = array(
    'player' => $player,
    'error' => $error,
    'confirm_delete' => $confirm_delete,
    'profile_changed' => $profile_changed,
    'username' => $username,
    'status_list' => $status_list,
    'rank_display' => $rank_display,
    'avatar_display' => $avatar_display,
    'profile_editable' => $profile_editable, // Unescaped.
    'profile_display' => $profile_display, // use out()
    'level_and_cat' => $level_and_cat,
    'delete_attempts' => $delete_attempts,
    'profile_max_length' => $profile_max_length,
    'SUPPORT_EMAIL' => SUPPORT_EMAIL,
    'WEB_ROOT' => WEB_ROOT,
    'JS_ROOT' => JS_ROOT
);
echo render_template("stats.tpl", $parts);

include SERVER_ROOT."interface/footer.php";
?>
