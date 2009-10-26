<?php
$page_title = "Confirmation Resend";
$alive      = false;
$private    = false;
$quickstat  = false;

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Retriving Confirm Code</span>

<p>
<?php
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

/* additional headers */
$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n";

$lost_email   = in('email');

$data = $sql->QueryRow("SELECT confirm,uname FROM players WHERE email = '$lost_email'");

$lost_confirm = $data[0];
$lost_uname   = $data[1];

echo "Retriving Confirm Code: It will be sent to your email.<br>\n";

if ($lost_uname == ""){
	echo "There are no accounts with that email. Please <a href=\"signup.php\">sign up</a> for an account.<br>\n";
	die();
}


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
    echo "Confirmation code sent.";
} else {
    echo "NinjaWars was unable to send to that email address, please try again later.";
}

?>
  <hr>
  <a href="main.php">Return to Game</a>
</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
