<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Account;
use NinjaWars\core\data\Character;

/**
 * Create account objects via Factory(ok, actually Repository) object
 */
class AccountFactory{
	/**
	 * A partial save of account information.
	 */
    public static function save($account) {
        $params = [
            ':identity'       => $account->getIdentity(),
            ':active_email'   => $account->getActiveEmail(),
            ':type'           => $account->getType(),
            ':oauth_provider' => $account->getOauthProvider(),
            ':oauth_id'       => (string)$account->getOauthId($account->getOauthProvider()),
            ':account_id'     => $account->getId(),
            ':karma_total'    => $account->getKarmaTotal(),
        ];

        $updated = update_query('update accounts set
            account_identity = :identity, active_email = :active_email, type = :type, oauth_provider = :oauth_provider,
            oauth_id = :oauth_id, karma_total = :karma_total
            where account_id = :account_id', $params);

        return $updated;
    }
}
