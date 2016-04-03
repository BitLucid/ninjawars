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
     */
    public static function purge_test_accounts($test=null) {
        $test_ninja_name = $test? $test : TestAccountCreateAndDestroy::$test_ninja_name;
        $active_email = TestAccountCreateAndDestroy::$test_email;
        $alt_active_email = TestAccountCreateAndDestroy::$alt_test_email;

        query('delete from players where player_id in '.
            '(select player_id from players '.
            'left join account_players on _player_id = player_id '.
            'left join accounts on _account_id = account_id '.
            'where active_email = :active_email or account_identity= :ae2 '.
            'or players.uname = :uname or active_email = :alt_active_email '.
            'or account_identity = :alt_active_email2)',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':uname'             => $test_ninja_name,
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
    public static function destroy($test=null) {
        static::purge_test_accounts($test);
    }

    /**
     * Create a testing account
     */
    public static function create_testing_account($confirm=false) {
        TestAccountCreateAndDestroy::purge_test_accounts();
        return self::createAccount(TestAccountCreateAndDestroy::$test_ninja_name, TestAccountCreateAndDestroy::$test_email, 'tiger');
    }

    /**
     * Create a separate, second testing account
     */
    public static function create_alternate_testing_account($confirm=false) {
        return self::createAccount(TestAccountCreateAndDestroy::$alt_test_ninja_name, TestAccountCreateAndDestroy::$alt_test_email, 'dragon');
    }

    public static function createAccount($ninja_name, $email, $class_identity) {
        $found = Player::findByName($ninja_name);

        if ($found) {
            throw new Exception("Test user found [$found] with name [$ninja_name] already exists");
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

        Account::create($ninja->id(), $email, TestAccountCreateAndDestroy::$test_password, $confirm, 0, 1, $ip);

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
        $player_mock->player_id = TestAccountCreateAndDestroy::create_testing_account(true);
        $account = Account::findByChar($player_mock);
        return $account->id();
    }

    /**
     * Just return a character wholesale
     */
    public static function char() {
        return Player::find(TestAccountCreateAndDestroy::char_id());
    }

    /**
     * Alias for create_testing_account but clearer.
     */
    public static function char_id($confirm=false) {
        return TestAccountCreateAndDestroy::create_testing_account($confirm);
    }

    public static function char_id_2($confirm=false) {
        return TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
    }

    /**
     * Alias to get an account id
     */
    public static function account_id() {
        return TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    }
}
