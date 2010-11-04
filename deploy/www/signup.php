<?php
/*
 * This file is used for players signing up to ninjawars.
 *
 * @package account
 * @subpackage signup
 */

// Emailing of confirmation email is still not stable...
// Consider removing all email confirmation requirements...
// And creating a one-button ninja creation system.
require_once(LIB_ROOT."specific/lib_player.php");

$alive             = false;
$private           = false;
$quickstat         = false;
$page_title        = "Become a Ninja";

$starting_referral = in('referrer');
$enteredName       = trim(in('send_name', '', 'toText'));
$enteredEmail      = trim(in('send_email', '', 'toText'));
$enteredClass      = strtolower(trim(in('send_class', '')));
$enteredReferral   = trim(in('referred_by', $starting_referral));
$enteredPass       = in('key', null, 'toText');
$enteredCPass      = in('cpass', null, 'toText');
$submitted         = in('submit');

$submit_successful = false; // *** Default.
$error = null;

include SERVER_ROOT."interface/header.php";

if ($submitted) {
	$completedPhase = 0;

	if ($enteredPass == $enteredCPass) {
		if (!validate_signup_phase0($enteredName, $enteredEmail, $enteredClass, $enteredPass)) {
			$error = 'Phase 1 Incomplete: You did not correctly fill out all the necessary information.';
		} else {
			$phase1 = validate_signup_phase1($enteredName);
			if ($phase1) {
				$error = $phase1;
			} else {
				$completedPhase = 1;

				$phase2 = validate_signup_phase2($enteredPass);
				if ($phase2) {
					$error = $phase2;
				} else {
					$completedPhase = 2;

					$phase3 = validate_signup_phase3($enteredName, $enteredEmail);
					if ($phase3) {
						$error = $phase3;
					} else {
						$completedPhase = 3;

						if (!validate_signup_phase4($enteredClass)) {
							$error = 'Phase 4 Incomplete: No proper class was specified.';
						} else {
							$completedPhase = 4;

							$preconfirm = preconfirm_some_emails($enteredEmail);
							$confirm = rand(1000,9999); //generate confirmation code

							// Use the function from lib_player
							$player_params = array(
								'send_email'    => $enteredEmail
								, 'send_pass'   => $enteredPass
								, 'send_class'  => $enteredClass
								, 'preconfirm'  => $preconfirm
								, 'confirm'     => $confirm
								, 'referred_by' => $enteredReferral
							);

							if (create_account_and_ninja($enteredName, $player_params)) { // Create the player.
								$error = 'There was a problem with creating a player account. Please contact us as mentioned below: ';
							} else {
								$submit_successful = true;

								if ($preconfirm) {
									// Use the confirm function from lib_player.
									confirm_player($enteredName, false, true); // name, no confirm #, just autoconfirm.
								} else {	/* not blacklisted by, so require a normal email confirmation */
									$completedPhase = 5;
								}
							}
						}	// phase 4
					}	// phase 3
				}	// phase 2
			}	// phase 1
		}	// phase 0
	} else {
		$error = 'Your password entries did not match. Please try again.';
	}
} // *** Validates submission.

if (!$submit_successful) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->query('SELECT identity, class_name||\' - \'||class_note AS class_label FROM class WHERE class_active');
	$classes = array(''=>'Pick Ninja Color');

    // Create the options array from the class data in the database.
	while ($data = $statement->fetch()) {
		$classes[$data['identity']] = $data['class_label'];
	}
} // *** Displays form.

$quickstat         = false;
$page_title        = "Become a Ninja";

if ($submitted) {
	display_template('signup-submit-intro.tpl', array(
			'send_name'       => $enteredName
			, 'send_pass'     => $enteredPass
			, 'send_email'    => $enteredEmail
			, 'send_class'    => $enteredClass
			, 'class_display' => class_display_name_from_identity($enteredClass)
			, 'referred_by'   => $enteredReferral
			, 'success'       => $submit_successful
			, 'confirmed'     => ($submit_successful && is_confirmed($enteredName))
			, 'completedPhase'=> $completedPhase
			, 'error'         => $error
		)
	);
}

if (!$submit_successful) {
	display_template('signup.tpl', array(
			'enteredName'              => $enteredName
			, 'enteredEmail'           => $enteredEmail
			, 'enteredClass'           => $enteredClass
			, 'enteredReferral'        => $enteredReferral
			, 'classes'                => $classes
//			, 'error'                  => $error
		)
	);
}

include SERVER_ROOT."interface/footer.php";
?>
