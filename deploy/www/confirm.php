<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Game Confirmation";

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Game Confirmation</span>

<hr>

<?php

$admin_override_pass = 'WeAllowIt'; // Just a weak passphrase for simply confirming players.
$admin_override_request = in('admin_override');
$acceptable_admin_override = ($admin_override_pass === $admin_override_request ? true : false);
$confirm   = in('confirm');
$user_to_confirm = in('username');


$sql->QueryRow("SELECT player_id, uname, health, strength, gold, messages, kills, turns, confirm, confirmed, email, class, level, status, member, days, ip, bounty, clan, clan_long_name, created_date FROM players WHERE uname = '".$user_to_confirm."'");
$check     = $sql->data['confirm'];
//var_dump($check);
$confirmed = $sql->data['confirmed'];
$username = $sql->data['uname'];
//var_dump($confirmed);

echo "<div style=\"border: 1 solid #000000;font-weight: bold;\">\n";
if ($confirmed == 1) {
    // Confirmation state from the database is already confirmed.
	echo "That player username (".$username.") is already confirmed in our system.";
	echo "<br><br><a href=\"".WEB_ROOT."\">Please log in</a> or contact <a href='staff.php'>the game administrators</a> if you have further issues.\n";
}
elseif (($confirm == $check  && $check != "" && $confirm != "") || $acceptable_admin_override){
    // Confirmation number matches whats in the dabase and neither is null, or the admin override was met.
  echo "Confirmation Confirmed\n";
  $sql->Update("UPDATE players SET confirmed = 1  WHERE uname = '".$username."'");
  $affected_rows = $sql->a_rows;
  echo "<br><br><a href=\"".WEB_ROOT."\">You may now login.</a>\n";
}
else
{
  echo "This account can not be verified or the account was deactivated.  Please contact ".SUPPORT_EMAIL." if you require more information.\n";
}
?>

<br><br>

<a href="<?php echo WEB_ROOT; ?>">Return to Main ?</a>
</div>

<?php
include SERVER_ROOT."interface/footer.php";
?>
