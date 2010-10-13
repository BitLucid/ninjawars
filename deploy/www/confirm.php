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
$aid                       = in('aid');

DatabaseConnection::getInstance();

$statement = DatabaseConnection::$pdo->prepare('SELECT player_id, uname, confirm, confirmed, CASE WHEN active THEN 1 ELSE 0 END AS active, email, status, member, days, ip, players.created_date FROM accounts JOIN account_players ON _account_id = account_id JOIN players ON _player_id = player_id WHERE account_id = :acctID');
$statement->bindValue(':acctID', $aid);
$statement->execute();

if ($data = $statement->fetch()) {
	$check     = $data['confirm'];
	$confirmed = $data['active'];
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
	$statement->bindValue(':accountID', $aid);
	$statement->execute();	// todo - test for success

	$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET confirmed = 1 WHERE player_id in (SELECT _player_id FROM account_players WHERE _account_id = :accountID)');
	$statement->bindValue(':accountID', $aid);
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
