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
$enteredReferral   = in('referred_by', $starting_referral);
$enteredPass       = in('key', null, 'toText');
$enteredCPass      = in('cpass', null, 'toText');
$submitted         = in('submit');

include SERVER_ROOT."interface/header.php";

$submit_successful = false; // *** Default.
$error = '';

if ($submitted) {
	if ($enteredPass == $enteredPass) {
		$submit_successful = validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass);

		display_template('signup-submit-intro.tpl', array(
				'send_name'       => $enteredName
				, 'send_pass'     => $enteredPass
				, 'send_email'    => $enteredEmail
				, 'send_class'    => $enteredClass
				, 'class_display' => class_display_name_from_identity($enteredClass)
				, 'referred_by'   => $enteredReferral
				, 'success'       => $submit_successful
				, 'confirmed'     => is_confirmed($enteredName)
			)
		);
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

	echo render_template('signup.tpl', array(
			'enteredName'              => $enteredName
			, 'enteredEmail'           => $enteredEmail
			, 'enteredClass'           => $enteredClass
			, 'enteredReferral'        => $enteredReferral
			, 'classes'                => $classes
			, 'error'                  => $error
		)
	);
} // *** Displays form.

include SERVER_ROOT."interface/footer.php";
?>
