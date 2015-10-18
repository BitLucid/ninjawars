<?php
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

// Determines the user information for a certain email.
function user_having_email($email) {
	$data = query_row('SELECT uname, accounts.confirmed, accounts.verification_number, account_id, CASE WHEN active::bool THEN 1 ELSE 0 END AS active
		from accounts LEFT JOIN account_players ON account_id = _account_id LEFT JOIN players on _player_id = player_id 
		WHERE active_email = :email limit 1;',
			array(':email'=>$email));
	return $data;
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
	$lost_confirm = $data['verification_number'];
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
$sent  = null;
$attemptedToSendEmail = false;

if (!$email && ($password_request || $confirmation_request)) {
	$error = 'invalidemail';
} else if ($email) {
	$data = user_having_email($email);
	$username = $data['uname'];

	if (!$data['uname']) {
		$error = 'nouser';
	} elseif ($password_request && $data['active']) {
		$sent = send_account_email($email, $data);

		$attemptedToSendEmail = true;
	} else {
		// Confirmation request.
		if ($data['confirmed']) {
			$error = 'alreadyconfirmed';
		} else {
			$attemptedToSendEmail = true;
			$sent = send_confirmation_email($email, $data);
		}
	}
}

if ($attemptedToSendEmail && !$sent) {
	$error = 'emailfail';
}

$body_classes = 'account-issues';

display_page(
	'account_issues.tpl'
	, 'Account Problems'
	, get_certain_vars(get_defined_vars(), array('data'))
	, array(
		'quickstat' => false
	)
);

}
