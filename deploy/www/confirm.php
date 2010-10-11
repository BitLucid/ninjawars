<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$admin_override_pass       = 'WeAllowIt'; // Just a weak passphrase for simply confirming players.
$admin_override_request    = in('admin_override');
$acceptable_admin_override = ($admin_override_pass === $admin_override_request);
$confirm                   = in('confirm');
$user_to_confirm           = in('username');

DatabaseConnection::getInstance();

$statement = DatabaseConnection::$pdo->prepare('SELECT player_id, uname, confirm, confirmed, email, status, member, days, ip, created_date FROM players WHERE uname = :player');
$statement->bindValue(':player', $user_to_confirm);
$statement->execute();

if ($data = $statement->fetch()) {
	$check     = $data['confirm'];
	$confirmed = $data['confirmed'];
	$username  = $data['uname'];
} else {
	$check     =
	$confirmed =
	$username  = null;
}

$confirmation_confirmed = false;

if ($confirmed == 1) {
	// Confirmation state from the database is already confirmed.
} else if (($confirm == $check && $check != '' && $confirm != '') || $acceptable_admin_override) {
	// Confirmation number matches whats in the dabase and neither is null, or the admin override was met.
	$statement = DatabaseConnection::$pdo->prepare('UPDATE accounts SET active = true WHERE account_id = :accountID');
	$statement->bindValue(':player', $username);
	$statement->execute();	// todo - test for success

	$confirmation_confirmed = true;
}

display_page(
	'confirm.tpl'
	, 'Game Confirmation'
	, get_certain_vars(get_defined_vars())
	, array(
		'quickstat' => false
	)
);

}
?>
