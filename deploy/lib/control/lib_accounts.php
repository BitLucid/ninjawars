<?php
use NinjaWars\core\data\DatabaseConnection;
/**
 * Account creation and validation.
 */

// Pull account data in a * like manner.
function account_info($account_id, $specific=null){
	$res = query_row('select * from accounts where account_id = :account_id', array(':account_id'=>array($account_id, PDO::PARAM_INT)));
	if($specific){
		if(isset($res[$specific])){
			$res = $res[$specific];
		} else {
			$res = null;
		}
	}
	return $res;
}

// Get the account linked with a character.
function account_info_by_char_id($char_id, $specific=null){
	$res = query_row('select * from accounts join account_players on account_id = _account_id where _player_id = :char_id', 
		array(':char_id'=>array($char_id, PDO::PARAM_INT)));
	if($specific){
		if(isset($res[$specific])){
			$res = $res[$specific];
		} else {
			$res = null;
		}
	}
	return $res;
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
		WHERE :email IN (account_identity, active_email)';
	$dupe = query_item($acc_check, array(':email'=>strtolower($email)));

	return !empty($dupe);
}

function create_account($ninja_id, $email, $password_to_hash, $confirm, $type=0, $active=1, array $data=null) {
	$ip = $data['ip']?:null;
	DatabaseConnection::getInstance();

	$newID = query_item("SELECT nextval('accounts_account_id_seq')");

	$ins = "INSERT INTO accounts (account_id, account_identity, active_email, phash, type, operational, verification_number, last_ip)
		VALUES (:acc_id, :email, :email2, crypt(:password, gen_salt('bf', 10)), :type, :operational, :verification_number, :ip)";

	$email = strtolower($email);

	$statement = DatabaseConnection::$pdo->prepare($ins);
	$statement->bindParam(':acc_id', $newID);
	$statement->bindParam(':email', $email);
	$statement->bindParam(':email2', $email);
	$statement->bindParam(':password', $password_to_hash);
	$statement->bindParam(':type', $type, PDO::PARAM_INT);
	$statement->bindParam(':operational', $active, PDO::PARAM_INT);
	$statement->bindParam(':verification_number', $confirm);
	$statement->bindParam(':ip', $ip);
	$statement->execute();

	// Create the link between account and player.
	$link_ninja = 'INSERT INTO account_players (_account_id, _player_id, last_login) VALUES (:acc_id, :ninja_id, default)';

	$statement = DatabaseConnection::$pdo->prepare($link_ninja);
	$statement->bindParam(':acc_id', $newID, PDO::PARAM_INT);
	$statement->bindParam(':ninja_id', $ninja_id, PDO::PARAM_INT);
	$statement->execute();

	$sel_ninja_id = 'SELECT player_id FROM players
		JOIN account_players ON player_id = _player_id
		JOIN accounts ON _account_id = account_id
		WHERE account_id = :acc_id ORDER BY level DESC LIMIT 1';

	$verify_ninja_id = query_item($sel_ninja_id, array(':acc_id'=>array($newID, PDO::PARAM_INT)));

	return ($verify_ninja_id != $ninja_id ? false : $newID);
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
		if (strpos(strtolower($email), $loop_domain) !== false) {
			return 0;
		}
	}

	foreach ($whitelisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain) !== false) {
			return 1;
		}
	}

	return $res;
}

// Ninja and account creation functions.

// Create a ninja
function create_ninja($send_name, $params=array()) {
	DatabaseConnection::getInstance();

	$class_identity  = $params['send_class'];
	$preconfirm  = (int) $params['preconfirm'];
	$confirm     = (int) $params['confirm'];

	// Create the initial player row.
	$playerCreationQuery= "INSERT INTO players
		 (uname, health, strength, speed, stamina, gold, messages, kills, turns, verification_number, active,
		  _class_id, level,  status, member, days, bounty, created_date)
		 VALUES
		 (:username, '150', '5', '5', '5', '100', '', '0', '180', :verification_number, :active,
		 (SELECT class_id FROM class WHERE identity = :class_identity), '1', '1', '0', '0', '0', now())";
	//  ***  Inserts the choices and defaults into the player table. Status defaults to stealthed. ***
	$statement = DatabaseConnection::$pdo->prepare($playerCreationQuery);
	$statement->bindValue(':username', $send_name);
	$statement->bindValue(':verification_number', $confirm);
	$statement->bindValue(':active', $preconfirm);
	$statement->bindValue(':class_identity', $class_identity);
	$statement->execute();
	return get_char_id($send_name);
}

function send_signup_email($account_id, $signup_email, $signup_name, $confirm, $class_identity) {
	//  ***  Sends out the confirmation email to the chosen email address.  ***

	$class_display = class_display_name_from_identity($class_identity);

	$_to = array("$signup_email"=>$signup_name);
	$_subject = 'NinjaWars Account Sign Up';
	$_body = render_template('signup_email_body.tpl', array(
			'send_name'       => $signup_name
			, 'signup_email'       => $signup_email
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
	return $message->send();
}

// Create the account and the initial ninja for that account.
function create_account_and_ninja($send_name, $params=array()) {
	$send_email  = $params['send_email'];
	$send_pass   = $params['send_pass'];
	$class_identity  = $params['send_class'];
	$confirm     = (int) $params['confirm'];
	$error       = false;
	$data['ip'] = isset($params['ip'])? $params['ip'] : null;
	$ninja_id    = create_ninja($send_name, $params);
	$account_id  = create_account($ninja_id, $send_email, $send_pass, $confirm, 0, 1, $data);

	if ($account_id) {
		$sent = send_signup_email($account_id, $send_email, $send_name, $confirm, $class_identity);

		if (!$sent && !DEBUG) {
			$error = 'There was a problem sending your signup to that email address.';
		}
	}

	return $error;
}

// Confirm a player if they completely match.
function confirm_player($char_name, $confirmation=0, $autoconfirm=false) {
	DatabaseConnection::getInstance();
	// Preconfirmed or the email didn't send, so automatically confirm the player.
	$require_confirm = ($autoconfirm ? '' : ' AND 
			(account.verification_number = :confirmation OR players.verification_number = :confirmation2) ');
	// Get the account_id for a player 
	$params = array(':char_name'=>$char_name);
	if($require_confirm){
		$params[':confirmation'] = $confirmation;
		$params[':confirmation2'] = $confirmation;
	}
	$info = query_row('select account_id, player_id from players 
		join account_players on _player_id = player_id 
		join accounts on account_id = _account_id 
		 where uname = :char_name '.$require_confirm, 
			$params);
	if(empty($info)){
		return false;
	} else {
		$account_id = $info['account_id'];
		$player_id = $info['player_id'];
		if(!$account_id || !$player_id){
			return false;
		}
	}

	query('update players set active = 1 where player_id = :player_id',
		array(':player_id'=>$player_id));

	$up = "UPDATE accounts set operational = true, confirmed = 1 where account_id = :account_id";
	$params = array(':account_id'=>$account_id);
	return (bool) rco(query($up, $params));
}

// Check for reserved or already in use by another player.
function ninja_name_available($ninja_name) {
	$reserved = array('SysMsg', 'NewUserList', 'Admin', 'Administrator', 'A Stealthed Ninja', 'Tchalvak', 'Beagle');

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

// Render a ninja inactive, until they log in.
function inactivate_ninja($char_id){
	query('update players set active = 0 where player_id = :char_id', array(':char_id'=>$char_id)); // Toggle the active bit off until they login.
}

/**
 * Check that the password format fits.
 */
function validate_password($password_to_hash) {
	$error = null;
	if (strlen($password_to_hash) < 3 || strlen($password_to_hash) > 500) {	// *** Why is there a max length to passwords? ***
		$error = 'Phase 2 Incomplete: Passwords must be at least 3 characters long.';
	}

	return $error;
}

/**
 * Function for account creation to return the reasons that a username isn't acceptable.
 */
function validate_username($send_name) {
	$error = null;
	$format_error = username_format_validate($send_name);
	if($format_error){
		$error = 'Phase 1 Incomplete: Ninja name: '.$error;
	}
	return $error;
}
