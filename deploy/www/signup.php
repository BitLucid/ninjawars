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
$enteredName       = in('send_name', '', 'toText');
$enteredEmail      = in('send_email', '', 'toText');
$enteredClass      = in('send_class', '');
$enteredReferral   = in('referred_by', $starting_referral);
$enteredPass       = in('key', null, 'toText');
$submitted         = in('submit');

include SERVER_ROOT."interface/header.php";

$submit_successful = false; // *** Default.

if ($submitted) {
	$submit_successful = validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass);
} // *** Validates submission.

if (!$submit_successful) {
	display_signup_form($enteredName, $enteredEmail, $enteredClass, $enteredReferral);
} // *** Displays form.


include SERVER_ROOT."interface/footer.php";
?>
