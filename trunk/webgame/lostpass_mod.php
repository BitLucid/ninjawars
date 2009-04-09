<?php
$page_title = "Sending Password";
$quickstat  = false;
$private    = false;
$alive      = false;

include "interface/header.php";
?>

<span class="brownHeading">Retriving Password</span>

<p>

<?php
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

/* additional headers */
$headers .= "From: SysMsg <SysMsg@NinjaWars.net>\r\n";

$lost_email = in('email', 'toEmail'); // The default filter allows standard emails.

$data = $sql->QueryRow("SELECT pname,uname FROM players WHERE lower(email) = lower('$lost_email')");
$lost_uname = $data[1];
$lost_pname = $data[0];
if(!$lost_email){
    echo "<p>Invalid email.</p>";
} else { // Email was validated.
    if($lost_uname){
        echo "Retriving password for: $lost_uname.<br />\n";
    }

    if ($lost_pname)
    {
      echo "Your password will be sent to your email.\n";
      mail("$lost_email", "NinjaWars Lost Password", "You have requested your password for the account: $lost_uname.<br />\n<br />\n<b>Account Info</b><br />\nUsername: $lost_uname<br />\nPassword: $lost_pname<br />\n<br />\nIf you require any further help, email: Admin@NinjaWars.net","$headers");
    }
    else
    {
      echo "No user with that email exists. Please <a href=\"signup.php\">sign up</a> for an account.<br />\n";
    }
}
?>

<hr />

<a href="main.php">Return to Game</a>

</p>

<?php
include "interface/footer.php";
?>

