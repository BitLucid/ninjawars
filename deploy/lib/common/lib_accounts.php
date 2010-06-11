<?php


function validate_account($ninja_id, $email, $password_to_hash){
    if($ninja_id && $email && $password_to_hash && is_numeric($type) && get_ninja_name($ninja_id) && !account_by_email($email)
        && validate_password($password_to_hash) && validate_account_email($email) && !email_duplicate_check($email)){
        return true;    
    }
    return false;
}

function validate_account_email($email){
    // Add more robust email check here.
    return !!stripos($email, '@') && strlen($email)>6;
}

function email_duplicate_check($email){
    $acc_check = "select account_identity from accounts 
        where active_email = lower(:email) or account_identity = lower(:email)";
    $dupe = query_item($acc_check, array(':email'=>$email));
    return !!$dupe;
}

function create_account($ninja_id, $email, $password_to_hash, $type=0, $active=1){
    $ins = "insert into accounts (account_identity, active_email, phash, type, active) 
        values (lower(:email), lower(:email), crypt(:password, gen_salt('bf')), :type, :active)";
    query($ins, array(':email'=>$email, 
        ':password'=>$password_to_hash, 
        ':type'=>array($type, PDO::PARAM_INT), 
        ':active'=>array($active, PDO::PARAM_INT)));
    // Get last created id.
    $sel_acc = "select account_id from accounts where account_identity = lower(:email)";
    $acc_id = query($sel_acc, array(':email'=>$email));
    $ins = "insert into account_players (_account_id, _player_id) values (:acc_id, :ninja_id)";
    query($ins, array(':acc_id'=>array($acc_id, PDO::PARAM_INT), ':ninja_id'=>array($ninja_id, PDO::PARAM_INT)));
    $sel_ninja_id = "select player_id from players 
        join account_players on player_id = _player_id 
        join accounts on _account_id = account_id
        where account_id = :acc_id";
    $verify_ninja_id = query_item($sel_ninja_id, array(':acc_id'=>array($acc_id, PDO::PARAM_INT)));
    if($verify_ninja_id == $ninja_id){
        return true;
    } else {
        return false;
    }
}


function account_of_email($email){
    $sel = 'select account_id from accounts where active_email = :email';
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

function create_ninja($send_name, $params=array()){
	DatabaseConnection::getInstance();

    $send_email  = $params['send_email'];
    $send_pass   = $params['send_pass'];
    $send_class  = $params['send_class'];
    $preconfirm  = (int) $params['preconfirm'];
    $confirm     = (int) $params['confirm'];
    $referred_by = $params['referred_by'];

	// Create the initial player row.
	$playerCreationQuery= "INSERT INTO players
		 (uname, pname, health, strength, gold, messages, kills, turns, confirm, confirmed,
		  email, _class_id, level,  status, member, days, ip, bounty, created_date)
		 VALUES
		 (:username, :pass, '150', '5', '100', '', '0', '180', :confirm, :preconfirm,
		 :email, (SELECT class_id FROM class WHERE class_name = :class), '1', '1', '0', '0', '', '0', now())";
	//  ***  Inserts the choices and defaults into the player table. Status defaults to stealthed. ***
	$statement = DatabaseConnection::$pdo->prepare($playerCreationQuery);
	$statement->bindValue(':username', $send_name);
	$statement->bindValue(':pass', $send_pass);
	$statement->bindValue(':confirm', $confirm);
	$statement->bindValue(':preconfirm', $preconfirm);
	$statement->bindValue(':email', $send_email);
	$statement->bindValue(':class', $send_class);
	$statement->execute();
	return get_user_id($send_name);
}


function send_signup_email($signup_email, $signup_name, $confirm, $class){
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n";
	$headers .= "Reply-To: ".SUPPORT_EMAIL_FORMAL_NAME." <".SUPPORT_EMAIL.">\r\n";
	//  ***  Sends out the confirmation email to the chosen email address.  ***
	$_to = "$signup_email";
	$_subject = "NinjaWars Account Sign Up";
	$_body = render_template('signup_email_body.tpl', array(
	        'send_name'       => $signup_name
			, 'confirm'       => $confirm
			, 'send_class'    => $send_class
			, 'SUPPORT_EMAIL' => SUPPORT_EMAIL
		)
	);
	$_from = $headers;
	// *** Create message object.
	$message = new Nmail($_to, $_subject, $_body, $_from);
    if (DEBUG) {$message->dump = true;}
	$sent = false; // By default, assume failure.
	$sent = $message->send();
	return $sent;
}

function create_player($send_name, $params=array()) {
    $send_email  = $params['send_email'];
    $send_pass   = $params['send_pass'];
    $send_class  = $params['send_class'];
    $preconfirm  = (int) $params['preconfirm'];
    $confirm     = (int) $params['confirm'];
    $referred_by = $params['referred_by'];
    $ninja_id = create_ninja($send_name, $params);
    create_account($ninja_id, $send_email, $send_pass);
    $sent = send_signup_email($send_email, $send_name, $confirm, $send_class);
    return $sent;
}


function confirm_player($player_name, $confirmation=0, $autoconfirm=false) {
	DatabaseConnection::getInstance();
	// Preconfirmed or the email didn't send, so automatically confirm the player.
	$require_confirm = ($autoconfirm ? '' : "and confirm = :confirmation");
	$up = "update players set confirmed = 1, confirm='55555' where uname = :player ".$require_confirm;
	$statement = DatabaseConnection::$pdo->prepare($up);
	$statement->bindValue(':player', $player_name);
	if ($require_confirm) {
		$statement->bindValue(':confirmation', $confirmation);
	}
	$update_result = $statement->execute();
    return ($autoconfirm ? true : $update_result);
}


function validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass) {
	$successful = false;
	DatabaseConnection::getInstance();

	$send_name   = trim($enteredName);
	$send_pass   = $enteredPass;
	$send_class  = $enteredClass;
	$send_email  = trim($enteredEmail);
	$referred_by = $enteredReferral;

	echo "Your responses:<br> Name - $send_name,<br>
		 Password - ".((isset($send_pass) ? "***yourpassword***" : "NO PASSWORD")).",<br>
		 Class - $send_class,<br>
		 Email - $send_email,<br>
		 Site Referred By - $referred_by<br><br>\n";

	//  *** Requirement checking Section  ***

	if ($send_name != "" && $send_pass != "" && $send_email != "" && $send_class != "") {  //When everything is non-blank.
		$check_name  = 0;
		$check_email = 0;

        $check_name = !!get_user_id($send_name);
		
		$check_email = email_duplicate_check($send_email);
		
        // Validate the username symbols!
		$username_error = validate_username($send_name);

		if ($username_error) {
			echo $username_error;
		} else {  //when all the name requirement errors didn't trigger.
			$send_name = trim($send_name);  // Just cuts off any white space at the end.
			$filter = new Filter();
			$send_name = $filter->toUsername($send_name); // Filter any un-whitelisted characters.
			echo "Phase 1 Complete: Name passes requirements.<hr>\n";

			// Validate the password!
			$password_error = false && validate_password($send_pass);

			if ($password_error) {
				echo $password_error;
			} else {
				$send_pass = trim($send_pass); // *** Trims any extra space off of the password.
				echo "Phase 2 Complete: Password passes requirements.<hr>\n";

				if (FALSE) { // CURRENTLY NO BLOCKED EMAIL SERVICES strstr($send_email, "@") == "@aol.com" || strstr($send_email, "@") == "@netscape.com" || strstr($send_email, "@") == "@aim.com"
				    //Throws error if email from blocked domain.
					echo "Phase 3 Incomplete: We cannot currently accept @aol.com, @netscape.com, or @aim.com email addresses.";
				} elseif (!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", trim($send_email))) {
					echo "Phase 3 Incomplete: The email address (".htmlentities($send_email).")
					    must contain an @ symbol and a domain name to be valid.";
				} else if (!$check_name && !$check_email && $send_name != "SysMsg" && $send_name != "NewUserList") {
				    //Uses previous query to make sure name and email aren't duplicates.
					echo "Phase 3 Complete: Username and Email are unique.<br><hr>\n";

					$statement = DatabaseConnection::$pdo->query('SELECT class_name FROM class WHERE class_active');
					$statement->execute();

					$legalClasess = array();
					while ($classData = $statement->fetch()) {
						$legalClasses[] = $classData['class_name'];
					}

					if (!in_array($send_class, $legalClasses)) {
						echo "Phase 4 Incomplete: No proper class was specified.<br>";
					} else {
						echo "Phase 4 Complete: Class was specified.<br><hr>";

						// *** Signup is successful at this point  ***
						$preconfirm = 0;
						$preconfirm = preconfirm_some_emails($send_email);

						if (!$preconfirm) { /* not blacklisted by, so require a normal email confirmation */
							echo "Phase 5: When you receive an email from SysMsg,
							 it will describe how to activate your account.<br><br>\n";
						}

						// The preconfirmation message occurs later.
						$confirm = rand(1000,9999); //generate confirmation code

						// Use the function from lib_player
						$player_params = array(
						    'send_email'    => $send_email
							, 'send_pass'   => $send_pass
							, 'send_class'  => $send_class
							, 'preconfirm'  => $preconfirm
							, 'confirm'     => $confirm
							, 'referred_by' => $referred_by
						);

						$successful = create_player($send_name, $player_params); // Create the player.

						if (!$successful) {
						    echo "There was a problem with creating a player account.  Please contact us as below: ";
						} else {
    						if (!$preconfirm) {
    							//  *** Continues the page display ***
    							echo "Confirmation email has been sent to <b>".$send_email."</b>.  <br>
    				  					Be sure to also check for the email in any \"Junk Mail\" or \"Spam\" folders.
    				  					Delivery typically takes less than 15 minutes.";
    						} else {
    						    // Use the confirm function from lib_player.
    						    confirm_player($send_name, false, true); // name, no confirm #, just autoconfirm.
    							echo "<p>Account with the login name \"".$send_name
    							."\" is now confirmed!  Please login on the login bar of the ninjawars.net page.</p>";
    						}

    						echo "<p>Only one account per person is allowed.</p>";
						}

						echo "If you require help use the forums at <a href='".WEB_ROOT."forum/'>"
						    .WEB_ROOT."forum/</a> or email: ".SUPPORT_EMAIL;
					}	// *** End of class checking.
				} else {	// Default, displays when the username or email are not unique.
					$what = ($check_email != 0 ? "Email" : "Username");

					echo "Phase 3 Incomplete: That $what is already in use. Please choose a different one.\n";
				}
			}
		}
	} else {  //  ***  Response for when nothing was submitted.  ***
		echo "Phase 1 Incomplete: You did not correctly fill out all the necessary information.\n";
	}

	//This is the final signup return section, which only shows if a successful
	//insert and confirmation number has -not- been acheived.

	echo "<br><br>";
	return $successful;
} // *** End of validate_signup() function.


?>
