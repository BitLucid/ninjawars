<?php
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

// Determines the user information for a certain email.
function user_having_email($email) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare('SELECT pname, uname, confirmed, confirm, account_id, CASE WHEN active THEN 1 ELSE 0 END AS active FROM players JOIN account_players ON _player_id = player_id JOIN accounts ON account_id = _account_id WHERE lower(email) = lower(:email)');
	$statement->bindValue(':email', $email);
	$statement->execute();

	return $statement->fetch();
}

// Sends an email for the user's account data.
function send_account_email($email, $data) {
	/*$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= 'Reply-To: '.SUPPORT_EMAIL."\r\n";*/

	$_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);

	/* additional headers */
	$_to = array("$email"=>$data['uname']);
	$_subject = 'NinjaWars Lost Password Request';
	$_body = render_template('lostpass_email_body.tpl', array(
		    'lost_uname'   => $data['uname']
			, 'lost_pname' => $data['pname']
			, 'confirmed'  => $data['confirmed']
		)
	);

	$mail_obj = new Nmail($_to, $_subject, $_body, $_from);

    // *** Set the custom replyto email. ***
    $mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));
	if (DEBUG) { $mail_obj->dump = true; }

	$sent = false;
	$sent = $mail_obj->send();

	return $sent;
}

// Sends the account confirmation email.
function send_confirmation_email($email, $data) {
	$lost_confirm = $data['confirm'];
	$lost_uname   = $data['uname'];
	$confirmed    = $data['confirmed'];

	/*$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= 'Reply-To: '.SUPPORT_EMAIL."\r\n";*/

	$_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);
	$_to = array("$email"=>$data['uname']);
	$_subject = "NinjaWars Account Confirmation Info";
	$_body = render_template('lostconfirm_email_body.tpl', array(
	    	'lost_uname'     => $lost_uname
			, 'lost_confirm' => $lost_confirm
			, 'account_id'   => $data['account_id']
		)
	);

	$mail_obj = new Nmail($_to, $_subject, $_body, $_from);
	$mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));

	if (DEBUG) { $mail_obj->dump = true; }

	$sent = false;
	$sent = $mail_obj->send();

	return $sent;
}

$email = in('email', null, 'sanitize_to_email'); // The default filter allows standard emails.
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
	} elseif ($password_request && $data['active']) {
	    $sent = send_account_email($email, $data);

	    if (!$sent) {
	        $error = 'There was a problem sending to that email, please allow a few minutes for the server load to go down,
	        or else <a href="staff.php">contact us</a> if the problem persists.';
	    }
	} else {
	    // Confirmation request.
	    if (!$data['confirmed']) {
	        $error = 'That account is already confirmed.  If you are having problems logging in, please <a href="staff.php">Contact Us</a>.';
	    } else {
	        $sent = send_confirmation_email($email, $data);
	        if (!$sent) {
	            $error = 'There was a problem sending to that email, please allow a few minutes for the server load to go down,
	             or else <a href="staff.php">contact us</a> if the problem persists.';
	        }
	    }
	}
}

display_page(
	'account_issues.tpl'
	, 'Account Problems'
	, get_certain_vars(get_defined_vars(), array('data'))
	, array(
		'quickstat' => false
	)
);

}
?>
