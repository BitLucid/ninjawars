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
    public static function purge_test_accounts($test_ninja_nm = null, $test_email = null) {
        $test_ninja_name = $test_ninja_nm ? $test_ninja_nm : self::$test_ninja_name;
        $active_email = $test_email ?? self::$test_email;
        $alt_active_email = self::$alt_test_email;


        // TODO: Throw exceptions on invalid arguments

        query(
            'delete from players where player_id in ' .
                '(select player_id from players ' .
                'left join account_players on _player_id = player_id ' .
                'left join accounts on _account_id = account_id ' .
                'where active_email = :active_email or account_identity= :ae2 ' .
                'or players.uname = :uname or active_email = :alt_active_email ' .
                'or account_identity = :alt_active_email2)',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':uname'             => $test_ninja_name,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
            ]
        ); // Delete the players

        query(
            'delete from account_players where _account_id in ' .
                '(select account_id from accounts ' .
                'where active_email = :active_email or account_identity= :ae2 ' .
                'or active_email = :alt_active_email ' .
                'or account_identity = :alt_active_email2)',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
            ]
        ); // Delete the account_players linkage.

        $query = query(
            'delete from accounts ' .
                'where active_email = :active_email or account_identity= :ae2 ' .
                'or active_email = :alt_active_email ' .
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
    public static function destroy($test_name = null, $test_email = null) {
        if ($test_name !== null && str_contains($test_name, '@')) {
            throw new InvalidArgumentException('Test user teardown (destroy) function takes a username as the first argument, not an email with an @ symbol in it, you passed [' . $test_name . ']');
        }
        if ($test_email !== null && !str_contains($test_email, '@')) {
            throw new InvalidArgumentException('Test user teardown (destroy) function takes a username as the first argument and a password as a second, you passed a second argument of [' . $test_email . ']');
        }
        static::purge_test_accounts($test_name, $test_email);
    }

    /**
     * Use to create a temporary account
     */
    public static function create_testing_account($confirm = false, $overrides = null) {
        self::purge_test_accounts();
        return self::createAccount($overrides['name'] ?? self::$test_ninja_name, self::$test_email, 'tiger');
    }

    /**
     * Create a separate, second testing account
     */
    public static function create_alternate_testing_account($confirm = false) {
        return self::createAccount(self::$alt_test_ninja_name, self::$alt_test_email, 'dragon');
    }

    public static function createAccount($ninja_name, $email, $class_identity) {
        $found = Player::findByName($ninja_name);

        if ($found) {
            throw new Exception("Test user found [$found] with name [$ninja_name] already exists");
        }

        $ip = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

        // Create test user, unconfirmed, whatever the default is for activity.
        $confirm = rand(1000, 9999); //generate confirmation code

        $class_id = query_item(
            'SELECT class_id FROM class WHERE identity = :class_identity',
            [':class_identity' => $class_identity]
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
    public static function create_complete_test_account_and_return_id() {
        $player_mock = new Player();
        $player_mock->player_id = self::create_testing_account(true);
        $account = Account::findByChar($player_mock);
        return $account->id();
    }

    /**
     * Just return a character wholesale
     */
    public static function char() {
        return Player::find(self::char_id());
    }

    /**
     * Return alternate character
     */
    public static function char_2() {
        return Player::find(self::char_id_2());
    }

    /**
     * Alias for create_testing_account but clearer.
     */
    public static function char_id($confirm = false) {
        return self::create_testing_account($confirm);
    }

    public static function char_id_2($confirm = false) {
        return self::create_alternate_testing_account($confirm);
    }

    /**
     * Alias to get an account id, to prevent clash, only use in isolation from char_id
     */
    public static function account_id() {
        return self::create_complete_test_account_and_return_id();
    }
}
