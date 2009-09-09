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
$deleteAccount     = ($in_delete_account && $in_delete_account == 1 ? 1 : null);

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

if ($deleteAccount) {
	$verify = false;
	$verify = is_authentic($username, $passW);
	if ($verify == true) {// *** To check that there's only 1 match for that username and password.
		pauseAccount($username); // This may redirect and stuff?
	} else {
	    $confirm_delete = true;
	}
} else if ($changeprofile == 1) {
	if ($newprofile != "") {
		$sql->Update("UPDATE players SET messages = '".sql($newprofile)."' WHERE uname = '".sql($username)."'");
		$affected_rows = $sql->a_rows;
		$profile_changed = true;
	} else {
		$error = "Can not enter a blank profile.";
	}
}


$status_list = render_status_section();
$avatar_display = render_avatar_section($player['player_id']);// include and render from player.php
$rank_display = get_rank($username, $sql); // rank display.

$profile_editable = $player['messages'];
$profile_display = out($profile_editable);


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
    'SUPPORT_EMAIL' => SUPPORT_EMAIL,
    'WEB_ROOT' => WEB_ROOT,
);
echo render_template("stats.tpl", $parts);

include SERVER_ROOT."interface/footer.php";
?>
