<?php
$page_title = "Account Problems";
$quickstat  = false;
$private    = false;
$alive      = false;

include SERVER_ROOT."interface/header.php";

// Determines the user information for a certain email.
function user_having_email($email) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT pname, uname, confirmed, confirm FROM players WHERE lower(email) = lower(:email)");
	$statement->bindValue(':email', $email);
	$statement->execute();

	return $statement->fetch();
}

// Sends an email for the user's account data.
function send_account_email($email, $data) {
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n".
	        'Reply-To: '.SUPPORT_EMAIL."\r\n";
	/* additional headers */
	$_to = "$email";
	$_subject = "NinjaWars Lost Password Request";
	$_body = render_template('lostpass_email_body.tpl', array(
	    'WEB_ROOT'=>WEB_ROOT,
	    'SUPPORT_EMAIL'=>SUPPORT_EMAIL,
	    'lost_uname'=>$data['uname'],
	    'lost_pname'=>$data['pname'],
	    'confirmed'=>$data['confirmed']));
	$_from = "$headers"; // Php generated.
	$mail_obj = new Nmail($_to, $_subject, $_body, $_from);

	if (DEBUG) { $mail_obj->dump = true; }

	$sent = false;
	$sent = $mail_obj->send();

	return $sent;
}

// Sends the account confirmation email.
function send_confirmation_email($email, $data) {
	$lost_confirm = $data['confirm'];
	$lost_uname   = $data['uname'];
	$confirmed = $data['confirmed'];

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n".
	        'Reply-To: '.SUPPORT_EMAIL."\r\n";
	$_to = "$email";
	$_subject = "NinjaWars Confirmation Info";
	$_body = render_template('lostconfirm_email_body.tpl', array(
	    'WEB_ROOT'=>WEB_ROOT,
	    'SUPPORT_EMAIL'=>SUPPORT_EMAIL,
	    'lost_uname'=>$lost_uname,
	    'lost_confirm'=>$lost_confirm));
	$_from = "$headers"; // Php generated.
	$mail_obj = new Nmail($_to, $_subject, $_body, $_from);

	if (DEBUG) { $mail_obj->dump = true; }

	$sent = false;
	$sent = $mail_obj->send();

	return $sent;
}

$email = in('email', null, 'toEmail'); // The default filter allows standard emails.
$password_request = in('password_request');
$confirmation_request = in('confirmation_request');

$error = null;

if (!$email && ($password_request || $confirmation_request)) {
	$error = 'The submitted email was invalid.';
} else if ($email) {
	$data = user_having_email($email);
	$username = $data['uname'];

	if (!$data['uname'] || !$data['pname']) {
	    $error = 'No user with that email exists. Please <a href="signup.php">sign up</a> for an account,
	    or <a href="staff.php">contact us</a> if you have other account issues.';
	} elseif ($password_request) {
	    $sent = send_account_email($email, $data);

	    if (!$sent) {
	        $error = "There was a problem sending to that email, please allow a few minutes for the server load to go down,
	        or else <a href='staff.php'>contact us</a> if the problem persists.";   
	    }
	} else {
	    // Confirmation request.
	    if (!$data['confirmed']) {
	        $error = "That account is already confirmed.  If you are having problems logging in, please <a href='staff.php'>Contact Us</a>.";
	    } else {
	        $sent = send_confirmation_email($email, $data);
	        if (!$sent) {
	            $error = "There was a problem sending to that email, please allow a few minutes for the server load to go down,
	             or else <a href='staff.php'>contact us</a> if the problem persists.";   
	        }
	    }
	}
}

$parts = get_certain_vars(get_defined_vars(), array('data'));
echo render_template('account_issues.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
