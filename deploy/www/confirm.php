<?php
use NinjaWars\core\data\DatabaseConnection;

$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$admin_override_pass       = 'WeAllowIt'; // Just a weak passphrase for simply confirming players.
$admin_override_request    = in('admin_override');
$acceptable_admin_override = ($admin_override_pass === $admin_override_request);
$confirm                   = in('confirm');
$aid                       = positive_int(in('aid'));

$data = query_row('
    SELECT player_id, uname, accounts.verification_number as verification_number,
    CASE WHEN active = 1 THEN 1 ELSE 0 END AS active, accounts.active_email,
    CASE WHEN accounts.confirmed = 1 THEN 1 ELSE 0 END as confirmed, status, member, days, players.created_date
    FROM accounts JOIN account_players ON _account_id = account_id JOIN players ON _player_id = player_id
    WHERE account_id = :acctId', array(':acctId'=>$aid));

if (rco($data)) {
    $check     = $data['verification_number'];
    $confirmed = $data['confirmed'];
    $active    = $data['active'];
    $username  = $data['uname'];
} else {
    $active    =
    $check     =
    $confirmed =
    $username  = null;
}

$confirmation_confirmed = false;

if ($confirmed != 1 && (($check && $confirm && $confirm == $check) || $acceptable_admin_override)) {
    // Confirmation number matches whats in the database and neither is null, or the admin override was met.
    query('UPDATE accounts SET operational = true, confirmed=1 WHERE account_id = :accountID', array(':accountID'=>$aid));

    $statement = DatabaseConnection::$pdo->prepare('UPDATE players SET active = 1 WHERE player_id in (SELECT _player_id FROM account_players WHERE _account_id = :accountID)');
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
