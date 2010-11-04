<?php
/**
 * Account creation and validation.
**/

// Pull account data in a * like manner.
function account_info($account_id){
	return query_row('select * from accounts where account_id = :account_id', array(':account_id'=>array($account_id, PDO::PARAM_INT)));
}

function validate_account($ninja_id, $email, $password_to_hash) {
	return ($ninja_id && $email && $password_to_hash
			&& is_numeric($type) && get_ninja_name($ninja_id) && !account_by_email($email)
			&& validate_password($password_to_hash) && validate_email($email));
}

function email_fits_pattern($p_email) {
	return preg_match("/^[a-z0-9!#$%&'*+?^_`{|}~=\.-]+@[a-z0-9.-]+\.[a-z]+$/i", $p_email);
}

function validate_email($email) {
	$error = null;

	if (FALSE) {
		// CURRENTLY NO BLOCKED EMAIL SERVICES
		//strstr($send_email, '@') == '@aol.com' || strstr($send_email, '@') == '@netscape.com' || strstr($send_email, '@') == '@aim.com'
		//Throws error if email from blocked domain.
		$error = 'Phase 3 Incomplete: We cannot currently accept @aol.com, @netscape.com, or @aim.com email addresses.';
	} elseif (!email_fits_pattern($email)) {
		$error = 'Phase 3 Incomplete: The email address ('
				.htmlentities($email).') must not contain spaces and must contain an @ symbol and a domain name to be valid.';
	} elseif (email_is_duplicate($email)) {
		$error = 'Phase 3 Incomplete: There is already an account using that email.  If that account is yours, you can request a password reset to gain access again.';
	}

	return $error;
}

function email_is_duplicate($email) {
	$acc_check = 'SELECT account_identity FROM accounts
		WHERE lower(:email) IN (account_identity, active_email)';
	$dupe = query_item($acc_check, array(':email'=>$email));

	return !empty($dupe);
}

function create_account($ninja_id, $email, $password_to_hash, $type=0, $active=1) {
	DatabaseConnection::getInstance();

	$newID = query_item("SELECT nextval('accounts_account_id_seq')");

	$ins = "INSERT INTO accounts (account_id, account_identity, active_email, phash, type, active)
		VALUES (:acc_id, :email, :email2, crypt(:password, gen_salt('bf', 8)), :type, :active)";

	$email = strtolower($email);

	$statement = DatabaseConnection::$pdo->prepare($ins);
	$statement->bindParam(':acc_id', $newID);
	$statement->bindParam(':email', $email);
	$statement->bindParam(':email2', $email);
	$statement->bindParam(':password', $password_to_hash);
	$statement->bindParam(':type', $type, PDO::PARAM_INT);
	$statement->bindParam(':active', $active, PDO::PARAM_INT);
	$statement->execute();

/*
	query($ins, array(
			':email'      => strtolower($email)
			, ':password' => $password_to_hash
			, ':type'     => array($type, PDO::PARAM_INT)
			, ':active'   => array($active, PDO::PARAM_INT)
		)
	);
*/

	// Create the link between account and player.
	$link_ninja = 'INSERT INTO account_players (_account_id, _player_id, last_login) VALUES (:acc_id, :ninja_id, default)';

	//query($link_ninja, array(":acc_id"=>array($acc_id, PDO::PARAM_INT), ":ninja_id"=>array($ninja_id, PDO::PARAM_INT)));

	$statement = DatabaseConnection::$pdo->prepare($link_ninja);
	$statement->bindParam(':acc_id', $newID, PDO::PARAM_INT);
	$statement->bindParam(':ninja_id', $ninja_id, PDO::PARAM_INT);
	$statement->execute();

	//$ins = "insert into account_players (_account_id, _player_id) values (:acc_id, :ninja_id)";
	//query($ins, array(':acc_id'=>array($acc_id, PDO::PARAM_INT), ':ninja_id'=>array($ninja_id, PDO::PARAM_INT)));
	$sel_ninja_id = 'SELECT player_id FROM players
		JOIN account_players ON player_id = _player_id
		JOIN accounts ON _account_id = account_id
		WHERE account_id = :acc_id ORDER BY level DESC LIMIT 1';

	$verify_ninja_id = query_item($sel_ninja_id, array(':acc_id'=>array($newID, PDO::PARAM_INT)));

	return ($verify_ninja_id != $ninja_id ? false : $newID);
}

function account_of_email($email) {
	$sel = 'SELECT account_id FROM accounts WHERE active_email = :email';
	$existing_account = query_item($sel, array(':email'=>$email));

	return !!$existing_account;
}

// Gives the blacklisted emails, should eventually be from a table.
function get_blacklisted_emails() {
	return array('@hotmail.com', '@hotmail.co.uk', '@msn.com', '@live.com', '@aol.com', '@aim.com', '@yahoo.com', '@yahoo.co.uk');
}

// Gives whitelisted emails, make a table eventually.
function get_whitelisted_emails() {
	return array('@gmail.com');
}

// Return 1 if the email is a blacklisted email, 0 otherwise.
function preconfirm_some_emails($email) {
	// Made the default be to auto-confirm players.
	$res = 1;
	$blacklisted_by = get_blacklisted_emails();
	$whitelisted_by = get_whitelisted_emails();

	// Blacklist only exists because emails beyond the first might not get through if we don't confirm.
	foreach ($blacklisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 1;
		}
	}

	foreach ($whitelisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 0;
		}
	}

	return $res;
}

// Ninja and account creation functions.

// Create a ninja
function create_ninja($send_name, $params=array()) {
	DatabaseConnection::getInstance();

	$send_email  = $params['send_email'];
	$send_pass   = $params['send_pass'];
	$class_identity  = $params['send_class'];
	$preconfirm  = (int) $params['preconfirm'];
	$confirm     = (int) $params['confirm'];
	$referred_by = $params['referred_by'];

	// Create the initial player row.
	$playerCreationQuery= "INSERT INTO players
		 (uname, pname, health, strength, gold, messages, kills, turns, confirm, confirmed,
		  email, _class_id, level,  status, member, days, ip, bounty, created_date)
		 VALUES
		 (:username, :pass, '150', '5', '100', '', '0', '180', :confirm, :preconfirm,
		 :email, (SELECT class_id FROM class WHERE identity = :class_identity), '1', '1', '0', '0', '', '0', now())";
	//  ***  Inserts the choices and defaults into the player table. Status defaults to stealthed. ***
	$statement = DatabaseConnection::$pdo->prepare($playerCreationQuery);
	$statement->bindValue(':username', $send_name);
	$statement->bindValue(':pass', $send_pass);
	$statement->bindValue(':confirm', $confirm);
	$statement->bindValue(':preconfirm', $preconfirm);
	$statement->bindValue(':email', $send_email);
	$statement->bindValue(':class_identity', $class_identity);
	$statement->execute();
	return get_char_id($send_name);
}

function send_signup_email($account_id, $signup_email, $signup_name, $confirm, $class_identity) {
	/*$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_EMAIL_NAME." <".SYSTEM__EMAIL.">\r\n";
	$headers .= "Reply-To: ".SUPPORT_EMAIL_NAME." <".SUPPORT_EMAIL.">\r\n";*/
	//  ***  Sends out the confirmation email to the chosen email address.  ***

	$class_display = class_display_name_from_identity($class_identity);

	$_to = array("$signup_email"=>$signup_name);
	$_subject = 'NinjaWars Account Sign Up';
	$_body = render_template('signup_email_body.tpl', array(
			'send_name'       => $signup_name
			, 'confirm'       => $confirm
			, 'send_class'    => $class_display
			, 'SUPPORT_EMAIL' => SUPPORT_EMAIL
			, 'account_id'    => $account_id
		)
	);

	$_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);

	// *** Create message object. ***
	$message = new Nmail($_to, $_subject, $_body, $_from);

	// *** Set replyto address. ***
	$message->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));
	if (DEBUG) {$message->dump = true;}
	$sent = false; // By default, assume failure.
	$sent = $message->send();

	return $sent;
}

function create_account_and_ninja($send_name, $params=array()) {
	$send_email  = $params['send_email'];
	$send_pass   = $params['send_pass'];
	$class_identity  = $params['send_class'];
	$confirm     = (int) $params['confirm'];
	$error       = false;
	$ninja_id    = create_ninja($send_name, $params);
	$account_id  = create_account($ninja_id, $send_email, $send_pass);

	if ($account_id) {
		$sent = send_signup_email($account_id, $send_email, $send_name, $confirm, $class_identity);

		if (!$sent) {
			$error = 'There was a problem sending your signup to that email address.';
		}
	}

	return $error;
}

function confirm_player($player_name, $confirmation=0, $autoconfirm=false) {
	DatabaseConnection::getInstance();
	// Preconfirmed or the email didn't send, so automatically confirm the player.
	$require_confirm = ($autoconfirm ? '' : ' AND confirm = :confirmation ');
	$up = "UPDATE players SET confirmed = 1, confirm = 55555 WHERE uname = :player $require_confirm";
	$statement = DatabaseConnection::$pdo->prepare($up);
	$statement->bindValue(':player', $player_name);

	if ($require_confirm) {
		$statement->bindValue(':confirmation', $confirmation);
	}

	$update_result = $statement->execute();

	return ($autoconfirm ? true : $update_result);
}

// Check for reserved or already in use by another player.
function ninja_name_available($ninja_name) {
	$reserved = array('SysMsg', 'NewUserList', 'Admin', 'Administrator', 'A Stealthed Ninja');

	foreach ($reserved as $l_names) {
		if (strtolower($ninja_name) == strtolower($l_names)) {
			return false;
		}
	}

	return (!get_user_id($ninja_name));
}

// Get the display name from the identity.
function class_display_name_from_identity($identity) {
	return query_item('SELECT class_name from class where identity = :identity', array(':identity'=>$identity));
}

function validate_signup_phase0($enteredName, $enteredEmail, $class_identity, $enteredPass) {
	return ($enteredName && $enteredPass && $enteredEmail && $class_identity);
}

function validate_signup_phase1($enteredName) {
	return validate_username($enteredName);
}

function validate_signup_phase2($enteredPass) {
	// Validate the password!
	return (false && validate_password($enteredPass));
}

function validate_signup_phase3($enteredName, $enteredEmail) {
	$name_available  = ninja_name_available($enteredName);
	$duplicate_email = email_is_duplicate($enteredEmail);
	$email_error     = validate_email($enteredEmail);

	if ($email_error) {
		return $email_error;
	} elseif (!$name_available) {
		return 'Phase 3 Incomplete: That ninja name is already in use.';
	} elseif ($duplicate_email) {
		return 'Phase 3 Incomplete: That account email is already in use. You can send a password reset request below if that email is your correct email.';
	} else {
		return null;
	}
}

function validate_signup_phase4($enteredClass) {
	return (boolean)query_item('SELECT identity FROM class WHERE class_active AND identity = :id', array(':id'=>$enteredClass));
}

function pauseAccount($p_playerID) {
	$accountActiveQuery = 'UPDATE accounts SET active = false WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)';
	$playerConfirmedQuery = 'UPDATE players SET confirmed = 0 WHERE player_id = :pid';

	$statement = DatabaseConnection::$pdo->prepare($playerConfirmedQuery);
	$statement->bindValue(':pid', $p_playerID);
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare($accountActiveQuery);
	$statement->bindValue(':pid', $p_playerID);
	$statement->execute();
}

function changePassword($p_playerID, $p_newPassword) {
	$changePasswordQuery = "UPDATE accounts SET phash = crypt(:password, gen_salt('bf', 8)) WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)";

	$statement = DatabaseConnection::$pdo->prepare($changePasswordQuery);
	$statement->bindValue(':pid', $p_playerID);
	$statement->bindValue(':password', $p_newPassword);
	$statement->execute();
}

function changeEmail($p_playerID, $p_newEmail) {
	$changeEmailQuery1 = "UPDATE accounts SET account_identity = :identity, active_email = :email WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)";
	$changeEmailQuery2 = "UPDATE players SET email = :email WHERE player_id = :id";

	$statement = DatabaseConnection::$pdo->prepare($changeEmailQuery1);
	$statement->bindValue(':pid', $p_playerID);
	$statement->bindValue(':identity', $p_newEmail);
	$statement->bindValue(':email', $p_newEmail);
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare($changeEmailQuery2);
	$statement->bindValue(':id', $p_playerID);
	$statement->bindValue(':email', $p_newEmail);
	$statement->execute();
}
?>
