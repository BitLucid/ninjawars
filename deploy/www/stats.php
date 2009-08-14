<?php
$page_title = "Your Stats";
$private    = true;
$alive      = false;
$quickstat  = "viewinv";

include SERVER_ROOT."interface/header.php";

// *** To verify that the delete request was made.
$in_delete_account = in('deleteaccount');
$deleteAccount = ($in_delete_account && $in_delete_account==1? 1 : null);

$in_changePass = in('changepass');
$changePass = ($in_changePass && $in_changePass == 1? 1 : null);

$newPass = in('newpass', null, 'toPassword');
$passW = in('passw', null, 'toPassword'); // *** To verify whether there's a password put in.
$changeprofile = in('changeprofile');
$newprofile = in('newprofile', null, 'toMessage');
$username = get_username();

echo "<span class=\"brownHeading\">Your Stats</span>\n";

echo "<p>\n";

/*if  ($changePass){
  if (trim($newPass) != "") { // *** To enforce non-blank passwords.
    $sql->Update("UPDATE players SET pname = '$newPass' WHERE uname = '$username'");
    echo "Password has been changed.\n";
  } else {
    echo "Can not enter a blank password.\n";
  }
} else
*/
if  ($deleteAccount) {
  $verify = false;
  $verify = is_authentic($username, $passW);
  if ($verify == true) {// *** To check that there's only 1 match for that username and password.
      pauseAccount($username);
    } else {
      echo "Please provide your password to confirm.<br>\n";
      echo "<form method=\"POST\" action=\"stats.php\">\n";
      echo "<input id=\"passw\" type=\"password\" maxlength=\"50\" name=\"passw\" class=\"textField\">\n";
      echo "<input type=\"hidden\" name=\"deleteaccount\" value=\"1\">\n";
      echo "<input type=\"submit\" value=\"Confirm Delete\" class=\"formButton\">\n";
      echo "</form>\n";
    }
} else if ($changeprofile == 1){
  if ($newprofile != ""){
      $sql->Update("UPDATE players SET messages = '".pg_escape_string($newprofile)."' WHERE uname = '$username'");
      $affected_rows = $sql->a_rows;
      echo "Profile has been changed.\n";
    } else {
      echo "Can not enter a blank profile.\n";
    }
}

$msg      = $sql->QueryItem("SELECT messages FROM players WHERE uname = '$username'");
$email    = $sql->QueryItem("SELECT email FROM players WHERE uname = '$username'");
$member   = $sql->QueryItem("SELECT member FROM players WHERE uname = '$username'");

/*
// Change password option removed as unnecessary, at this point.
echo "<form action=\"stats.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"changepass\" value=\"1\"><br>\n";
//echo "Password: <input type=\"password\" name=\"newpass\" class=\"textField\">
//    <input type=\"submit\" value=\"<== Change Password\" class=\"formButton\">\n";
echo "</form><br>\n";
*/

echo "<form action=\"stats.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"changeprofile\" value=\"1\">\n";

echo "Account Info: $username<br>\n";
echo "Health: ".getHealth($username)."<br>\n";
echo "Strength: ".getStrength($username)."<br>\n";
echo "Gold: ".getGold($username)."<br>\n";
echo "Kills: ".getKills($username)."<br>\n";
echo "Turns: ".getTurns($username)."<br>\n";
echo "Email: $email<br>\n";
echo "Class: ".getClass($username)."<br>\n";
echo "Level: ".getLevel($username)."<br>\n";
echo "Bounty: ".getBounty($username)." gold<br>\n";
echo "Clan: ".getClan($username)."<br>\n";

$status   = getStatus($username);
echo "Status: ";

$status_output = array();
if ($status['Stealth']) {$status_output[count($status_output)]="Stealthed";}
if ($status['Poison'])  {$status_output[count($status_output)]="Poisoned";}
if ($status['Frozen'])  {$status_output[count($status_output)]="Frozen";}
if (!isset($status_output[0])) {
  if (getHealth($username) == 0) {echo "Dead<br>\n";}
  else if (getHealth($username) < 75) {echo "Injured<br>\n";}
  else {echo "Healthy<br>\n";}
} else { // Display the statuses.
  $i=0;
  for ($i=0;$i<count($status_output)-1;$i++){
      echo $status_output[$i].", ";
    }
  echo  $status_output[$i]."<br>\n";
}

echo "Membership: ";
if ($member == 0) {echo "Free Member<br>\n";}
else if ($member == 1) {echo "Paid Member<br>\n";}

echo "Profile: <br><textarea name=\"newprofile\" cols=\"45\" rows=\"10\" class=\"textField\">$msg</textarea><br>\n";
echo "<input type=\"submit\" value=\"Change Profile\" class=\"formButton\"> (400 Character limit)\n";
echo "</form>\n";

echo "<hr>If you require account help email: <a href=\"mailto:ninjawarsTchalvak@gmail.com\">NinjawarsTchalvak@gmail.com</a></a><hr>\n";
echo "WARNING: Clicking on the button below will terminate your account.<br>\n";
echo "<form action=\"stats.php\" method=\"POST\">\n";
echo "<input type=\"hidden\" name=\"deleteaccount\" value=\"1\">\n";
echo "<input type=\"submit\" value=\"Permanently Remove Your Account\" class=\"formButton\">\n";
echo "</form>\n";

echo "</p>";

include SERVER_ROOT."interface/footer.php";
?>
