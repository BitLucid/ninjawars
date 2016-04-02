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
        $ip = isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        TestAccountCreateAndDestroy::purge_test_accounts();

        $found = get_char_id(TestAccountCreateAndDestroy::$test_ninja_name);
        if ((bool)$found) {
            throw new Exception('Test user found ['.$found.'] with name ['.TestAccountCreateAndDestroy::$test_ninja_name.'] already exists');
        }

        // Create test user, unconfirmed, whatever the default is for activity.
        $confirm = rand(1000,9999); //generate confirmation code

        $player_params = [
            'send_email'  => TestAccountCreateAndDestroy::$test_email,
            'send_pass'   => TestAccountCreateAndDestroy::$test_password,
            'send_class'  => 'tiger',
            'preconfirm'  => true,
            'confirm'     => $confirm,
            'referred_by' => 'ninjawars.net',
            'ip'          => $ip,
        ];

        create_account_and_ninja(TestAccountCreateAndDestroy::$test_ninja_name, $player_params);

        if ($confirm) {
            confirm_player(TestAccountCreateAndDestroy::$test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
        }

        return get_char_id(TestAccountCreateAndDestroy::$test_ninja_name);
    }

    /**
     * Create a separate, second testing account
     */
    public static function create_alternate_testing_account($confirm=false) {
        $ip = isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        $found = get_char_id(TestAccountCreateAndDestroy::$alt_test_ninja_name);
        if ((bool)$found) {
            throw new Exception('Test user found ['.$found.'] with name ['.TestAccountCreateAndDestroy::$alt_test_ninja_name.'] already exists');
        }

        // Create test user, unconfirmed, whatever the default is for activity.
        $confirm = rand(1000,9999); //generate confirmation code

        $player_params = array(
            'send_email'    => TestAccountCreateAndDestroy::$alt_test_email
            , 'send_pass'   => TestAccountCreateAndDestroy::$test_password
            , 'send_class'  => 'dragon'
            , 'preconfirm'  => true
            , 'confirm'     => $confirm
            , 'referred_by' => 'ninjawars.net'
            , 'ip'			=> $ip
        );

        ob_start(); // Skip extra output
        create_account_and_ninja(TestAccountCreateAndDestroy::$alt_test_ninja_name, $player_params);
        ob_end_clean();

        if ($confirm) {
            confirm_player(TestAccountCreateAndDestroy::$alt_test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
        }

        return get_char_id(TestAccountCreateAndDestroy::$alt_test_ninja_name);
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
