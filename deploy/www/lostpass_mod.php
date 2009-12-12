<?php
$page_title = "Sending Password";
$quickstat  = false;
$private    = false;
$alive      = false;

include SERVER_ROOT."interface/header.php";
?>

<h1>Retriving Password</h1>

<p>

<?php
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

/* additional headers */
$headers .= "From: NinjawarsNoreply <".SYSTEM_MESSENGER_EMAIL.">\r\n".
        'Reply-To: '.ADMIN_EMAIL."\r\n";

$lost_email = in('email', null, 'toEmail'); // The default filter allows standard emails.

$data = $sql->QueryRow("SELECT pname,uname FROM players WHERE lower(email) = lower('$lost_email')");
$lost_uname = $data[1];
$lost_pname = $data[0];

if (!$lost_email) {
	echo "<p>Invalid email.</p>";
} else { // Email was validated.
	if (!!$lost_pname && !!$lost_uname) {
		echo "Account information will be sent to your email.\n";
        $_to = "$lost_email";
        $_subject = "NinjaWars Lost Password Request";
        $_body = render_template('lostpass_email_body.tpl', array(
            'WEB_ROOT'=>WEB_ROOT,
            'SUPPORT_EMAIL'=>SUPPORT_EMAIL,
            'lost_uname'=>$lost_uname,
            'lost_pname'=>$lost_pname));
        $_from = "$headers"; // Php generated.
        $mail_obj = new Nmail($_to, $_subject, $_body, $_from);
        if(DEBUG){$mail_obj->dump = true; }
        $sent = false;
        $sent = $mail_obj->send();
		
	} else {
		echo "No user with that email exists. Please <a href=\"signup.php\">sign up</a> for an account.<br>\n";
	}
}
?>

  <hr>

  <a href="main.php">Return to Game</a>

</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
