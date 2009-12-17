<?php
$page_title = "Confirmation Resend";
$alive      = false;
$private    = false;
$quickstat  = false;

include SERVER_ROOT."interface/header.php";
?>

<h1>Retriving Confirm Code</h1>

<p>
<?php
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

/* additional headers */
$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n";

$lost_email   = in('email');

$data = $sql->QueryRow("SELECT confirm,uname,confirmed FROM players WHERE lower(email) = lower('".sql($lost_email)."')");

$lost_confirm = $data[0];
$lost_uname   = $data[1];
$confirmed = $data[2];

echo "<p>Checking confirmation code... </p>";

if ($lost_uname == ""){
	echo "There are no accounts with that email. Please <a href=\"signup.php\">sign up</a> for an account.<br>\n";
	die();
} elseif ($confirmed){
    echo "That account is already confirmed.  If you are having problems logging in, please <a href='staff.php'>Contact Us</a>.";
} else {

    echo "Confirmation code being sent for account: $lost_uname ...\n";
    $_to = "$lost_email";
    $_subject = "NinjaWars Confirmation Code";
    $_body = render_template('lostconfirm_email_body.tpl', array(
        'WEB_ROOT'=>WEB_ROOT,
        'SUPPORT_EMAIL'=>SUPPORT_EMAIL,
        'lost_uname'=>$lost_uname,
        'lost_confirm'=>$lost_confirm));
    $_from = "$headers"; // Php generated.
    $mail_obj = new Nmail($_to, $_subject, $_body, $_from);
    if(DEBUG){$mail_obj->dump = true; }
    $sent = false;
    $sent = $mail_obj->send();
    if($sent){
        echo "Confirmation code sent to the email address for that account..";
    } else {
        echo "NinjaWars was unable to send to that email address, please try again later.";
    }
}
?>
  <hr>
  <a href="main.php">Return to the Ninjawars Intro</a>
</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
