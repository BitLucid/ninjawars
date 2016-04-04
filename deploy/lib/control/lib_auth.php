<?php
use NinjaWars\core\data\Account;

/**
 * Authenticate a set of credentials
 *
 * @return Array
 */
function authenticate($dirty_login, $p_pass, $limit_login_attempts=true) {
    $filter_pattern       = "/[^\w\d\s_\-\.\@\:\/]/";
    $login                = strtolower(preg_replace($filter_pattern, "", (string)$dirty_login));
	$recent_login_failure = false;
	$pass                 = (string)$p_pass;
	$rate_limit           = false;
    $account    = Account::findByLogin($login);

	if ($limit_login_attempts && $account) {
        $rate_limit = (intval($account->login_failure_interval) <= 1);
	}

	if ($login != '' && $pass != '' && !$rate_limit) {
        // Pull the account data regardless of whether the password matches,
        // but create an int about whether it does match or not.

		$sql = "SELECT account_id, account_identity, uname, player_id, accounts.confirmed as confirmed,
		    CASE WHEN phash = crypt(:pass, phash) THEN 1 ELSE 0 END AS authenticated,
		    CASE WHEN accounts.operational THEN 1 ELSE 0 END AS operational
			FROM accounts
			JOIN account_players ON account_id = _account_id
			JOIN players ON player_id = _player_id
			WHERE (active_email = :login OR lower(uname) = :login)";

		$result = query($sql, [':login' => $login, ':pass' => $pass]);

		if ($result->rowCount() < 1) {	// Username does not exist
			return [];
		} else {
			if ($result->rowCount() > 1) {
                // Just for later reference, check for duplicate usernames via:
                //select array_accum(uname), count(*) from players group by lower(trim(uname)) having count(*) > 1;
				error_log('Case-insensitive duplicate username found: '.$login);
			}

			return $result->fetch(); // account found, return results
		}
	} else {
        if ($account) {
            // Update the last login failure timestamp
            Account::updateLastLoginFailure($account);
        }

		return [];
	}
}
