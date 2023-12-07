<?php

use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

/**
 * Library for creating and destroying test-only accounts, for use in their various ways in testing.
 */
class TestAccountCreateAndDestroy
{
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
    public static function purge_test_accounts(?string $test_ninja_nm = null, ?string $test_email = null)
    {
        $test_ninja_name = $test_ninja_nm ?? self::$test_ninja_name;
        $active_email = $test_email ?? self::$test_email;
        $alt_active_email = self::$alt_test_email;

        $account_ids = query_array(
            'select account_id from accounts where active_email = :active_email or account_identity= :ae2',
            [
                ':active_email' => $active_email,
                ':ae2'          => $alt_active_email,
            ]
        );


        $player_ids = query_array(
            'select player_id from players where uname = :uname or uname = :uname2 or uname = :uname3
                or email = :active_email or email= :ae2 or email = :ae3',
            [
                ':uname'        => $test_ninja_name,
                ':uname2'       => self::$test_ninja_name,
                ':uname3'       => self::$alt_test_ninja_name,
                ':active_email' => $active_email,
                ':ae2'          => $alt_active_email,
                ':ae3'          => self::$alt_test_email,
            ]
        );

        if (count($account_ids)) {
            // delete from accounts where the ids are in the list of account ids
            query(
                'delete from accounts where account_id in (' .
                implode(',', array_map(function ($account) {
                    return (int) $account['account_id'];
                }, $account_ids)) .
                    ')'
            );
        }

        if (count($player_ids)) {
            // Delete from players where the ids are in the player_id list
            query(
                'delete from players where player_id in (' .
                implode(',', array_map(function ($player) {
                    return (int) $player['player_id'];
                }, $player_ids)) .
                    ')'
            );
        }

        if (count($account_ids) || count($player_ids)) {
            if (count($account_ids)) {
                // delete from account_players where the ids are in the list of account ids
                $del_account_ids = implode(',', array_map(function ($account) {
                    return (int) $account['account_id'];
                }, $account_ids));
                query(
                    'delete from account_players where _account_id in (' . $del_account_ids . ')'
                );
            }

            if (count($player_ids)) {
                // delete from account_players where the ids are in the list of player ids
                $del_player_ids = implode(',', array_map(function ($player) {
                    return (int) $player['player_id'];
                }, $player_ids));
                query(
                    'delete from account_players where ' .
                    '_player_id in (' . $del_player_ids . ')'
                );
            }
        }

        // Rest of the function down to return continues here

        /*

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
            'delete from accounts where active_email = :active_email or account_identity= :ae2 
                or active_email = :alt_active_email 
                or account_identity = :alt_active_email2 
                or account_identity = :alt_active_email3
                or account_identity = \'' . self::$test_email . '\'
                or account_identity = \'' . self::$alt_test_email . '\'
                ',
            [
                ':active_email'      => $active_email,
                ':ae2'               => $active_email,
                ':alt_active_email'  => $alt_active_email,
                ':alt_active_email2' => $alt_active_email,
                ':alt_active_email3' => $test_email,
            ]
        ); // Finally, delete the test account.

        */

        return true;
    }

    /**
     * More memorable wrapper to the purge_test_accounts functionality.
     */
    public static function destroy(?string $test_name = null, ?string $test_email = null)
    {
        static::purge_test_accounts($test_name, $test_email);
    }

    /**
     * Create a testing account
     */
    public static function create_testing_account(?bool $confirm = false, ?array $overrides = null)
    {
        self::purge_test_accounts();
        return self::createAccount($overrides['name'] ?? self::$test_ninja_name, self::$test_email, 'tiger');
    }

    public static function deleteAccountByEmail($email)
    {
        $account = Account::findByEmail($email);

        if ($account) {
            $account->delete();
        }
    }

    /**
     * Create a separate, second testing account
     */
    public static function create_alternate_testing_account(bool $confirm = false)
    {
        return self::createAccount(self::$alt_test_ninja_name, self::$alt_test_email, 'dragon');
    }

    /**
     * Create an account, requires specifying a class and other non optional arguments
     */
    public static function createAccount(string $ninja_name, string $email, string $class_identity)
    {
        $found = Player::findByName($ninja_name);
        $found_account = Account::findByEmail($email);

        if ($found) {
            throw new Exception("Test user found [$found] with name [$ninja_name] already exists");
        }
        if ($found_account) {
            throw new Exception("Test account found [$found_account] with email [$email] already exists");
        }

        $ip = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

        // Create test user, unconfirmed, whatever the default is for activity.
        $confirm = rand(1000, 9999); //generate confirmation code

        $class_id = query_item(
            'SELECT class_id FROM class WHERE identity = :class_identity',
            [':class_identity' => $class_identity]
        );

        $ninja = new Player();
        // Sets vo fields via magic methods, unfortunately
        $created_date = $ninja->created_date;
        $ninja->uname               = $ninja_name;
        $ninja->verification_number = $confirm;
        $ninja->active              = 1;
        $ninja->_class_id           = $class_id;
        $ninja->email = $email;
        $ninja->created_date = $created_date; // Have to refresh this due to vo magic methods 
        $up_ninja = $ninja->save();


        if (!$up_ninja->id()) {
            throw new Exception("Test user [$ninja_name] failed to save");
        }

        // debug($up_ninja, $email, self::$test_password, $confirm, 0, 1, $ip);
        // throw new Exception('Reached');

        $account_id = Account::create($up_ninja->id(), $email, self::$test_password, $confirm, 0, 1, $ip);
        if (!$account_id) {
            throw new Exception("Test account [$account_id] with email [$email] and name [$ninja_name] failed to save");
        }
        // get account by id
        $account = Account::findById($account_id);
        if (!$account) {
            throw new Exception("Test account [$account_id] with email [$email] and name [$ninja_name] failed to save");
        }


        if ($confirm) {
            $up_ninja->active = 1;
            $up_ninja->save();

            $account = Account::findByChar($ninja);
            $account->confirmed = 1; // hack to set confirmed though it is readonly
            $account->setOperational(true);
            $account->save();
        }

        return $up_ninja->id();
    }

    /**
     * Convenience wrapper for the above, but confirms the account and returns the account id.
     */
    public static function create_complete_test_account_and_return_id()
    {
        $player_mock = new Player();
        $player_mock->player_id = self::create_testing_account(true);
        $account = Account::findByChar($player_mock);
        return $account->id();
    }

    /**
     * Just return a character wholesale
     */
    public static function char()
    {
        return Player::find(self::char_id());
    }

    /**
     * Return alternate character
     */
    public static function char_2()
    {
        return Player::find(self::char_id_2());
    }

    /**
     * Alias for create_testing_account but clearer.
     */
    public static function char_id($confirm = false)
    {
        return self::create_testing_account($confirm);
    }

    public static function char_id_2($confirm = false)
    {
        return self::create_alternate_testing_account($confirm);
    }

    /**
     * Alias to get an account id, to prevent clash, only use in isolation from char_id
     */
    public static function account_id()
    {
        return self::create_complete_test_account_and_return_id();
    }
}
