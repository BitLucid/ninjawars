<?php
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

/**
 * Library for creating and destroying test-only accounts, for use in their various ways in testing.
 */
class TestAccountCreateAndDestroy {
    public static $test_email = 'testphpunit@example.com';
    public static $alt_test_email = 'testphpunit2@example.com';
    public static $test_password = 'password';
    public static $test_ninja_name = 'phpunit_ninja_name';
    public static $alt_test_ninja_name = 'phpunit_alt_ninja';

    /**
     *
     * @note For manual deletion
     * delete from players where player_id in
     * (select player_id from players
     * left join account_players on _player_id = player_id
     * left join accounts on _account_id = account_id
     * where active_email = 'testphpunit@example.com' or
     * account_identity='testphpunit@example.com');
     * delete from account_players where _account_id in
     * (select account_id from accounts
     * where active_email = 'testphpunit@example.com'
     * or account_identity='testphpunit@example.com');
     * delete from accounts where active_email = 'testphpunit@example.com'
     * or account_identity='testphpunit@example.com';
     * @param string $test_ninja_nm Newly created ninja name to delete from
     */
    public static function purge_test_accounts($test_ninja_nm = null, $options = []): int
    {
        $test_ninja_name = $test_ninja_nm ?? self::$test_ninja_name;
        $alt_test_ninja_name = self::$alt_test_ninja_name;
        $active_email = $options['email'] ?? self::$test_email;
        $alt_active_email = self::$alt_test_email;

        query('delete from players where player_id in '.
            '(select player_id from players '.
            'left join account_players on _player_id = player_id '.
            'left join accounts on _account_id = account_id '.
            'where active_email = :active_email or account_identity= :ae2 '.
                'or players.uname = :uname or players.uname = :uname2 or active_email = :alt_active_email ' .
            'or account_identity = :alt_active_email2)',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':uname'             => $test_ninja_name,
                ':uname2'             => $alt_test_ninja_name,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
            ]
        ); // Delete the players

        query('delete from account_players where _account_id in '.
            '(select account_id from accounts '.
            'where active_email = :active_email or account_identity= :ae2 '.
            'or active_email = :alt_active_email '.
            'or account_identity = :alt_active_email2)',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
            ]
        ); // Delete the account_players linkage.

        $query = query('delete from accounts '.
            'where active_email = :active_email or account_identity= :ae2 '.
            'or active_email = :alt_active_email '.
            'or account_identity = :alt_active_email2',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
            ]
        ); // Finally, delete the test account.

        return ($query->rowCount() > 0);
    }

    /**
     * More memorable wrapper to the purge_test_accounts functionality.
     */
    public static function destroy($test_ninja_name = null, $options = []): void
    {
        static::purge_test_accounts($test_ninja_name, $options);
    }

    /**
     * Create a testing player entry (here called account)
     */
    public static function create_testing_account($confirm = false, $overrides = null): int
    {
        $name_u = $overrides['name'] ?? self::$test_ninja_name;
        $email_u = $overrides['email'] ?? self::$test_email;
        $class_u = $overrides['class'] ?? 'tiger';
        return self::createAccount($name_u, $email_u, $class_u);
    }

    /**
     * Create a separate, second testing player entry (here called account)
     */
    public static function create_alternate_testing_account($confirm = false): int
    {
        return self::createAccount(self::$alt_test_ninja_name, self::$alt_test_email, 'viper');
    }

    public static function createAccount($ninja_name, $email, $class_identity): int
    {
        $found = Player::findByName($ninja_name);

        if ($found) {
            throw new Exception("Cannot create test user as duplicate found [$found] with id [" . $found->id() . "]");
        }

        $ip = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

        // Create test user, unconfirmed, whatever the default is for activity.
        $confirm = rand(1000,9999); //generate confirmation code

        $class_id = query_item(
            'SELECT class_id FROM class WHERE identity = :class_identity',
            [ ':class_identity' => $class_identity ]
        );

        $ninja = new Player();
        $ninja->uname               = $ninja_name;
        $ninja->verification_number = $confirm;
        $ninja->active              = 1;
        $ninja->_class_id           = $class_id;
        $ninja->save();

        Account::create($ninja->id(), $email, self::$test_password, $confirm, 0, 1, $ip);

        if ($confirm) {
            $ninja->active = 1;
            $ninja->save();

            $account = Account::findByChar($ninja);
            $account->confirmed = 1;
            $account->setOperational(true);
            $account->save();
        }

        return $ninja->id();
    }

    /**
     * Convenience wrapper for the above, but confirms the account and returns the account id.
     */
    public static function create_complete_test_account_and_return_id($options = []): int
    {
        $pid = self::create_testing_account(true, $options);
        $player = Player::find($pid);
        $account = Account::findByChar($player);
        return $account->id();
    }

    /**
     * Just return a character wholesale
     */
    public static function char(): Player
    {
        return Player::find(self::char_id());
    }

    /**
     * Return alternate character
     */
    public static function char_2(): Player
    {
        return Player::find(self::char_id_2());
    }

    /**
     * Alias for create_testing_account but clearer.
     */
    public static function char_id($confirm = false): int
    {
        return self::create_testing_account($confirm);
    }

    public static function char_id_2($confirm = false): int
    {
        return self::create_alternate_testing_account($confirm);
    }

    /**
     * Alias to get an account id
     */
    public static function account_id($options = []): int
    {
        return self::create_complete_test_account_and_return_id($options);
    }
}
