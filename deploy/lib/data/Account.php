<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Player;
use PDO;
use stdClass;

/**
 * Player accounts and their info
 * @property array info
 * @property-read int account_id
 * @property-read string account_identity
 * @property-read string phash
 * @property-read string active_email
 * @property-read int type
 * @property-read boolean operational
 * @property-read string created_date
 * @property-read string last_login
 * @property-read string last_login_failure
 * @property-read string login_failure_interval
 * @property-read int karma_total
 * @property-read string last_ip
 * @property-read boolean confirmed
 * @property-read int verification_number
 * @property-read string oauth_provider
 * @property-read int oauth_id
 */
class Account extends stdClass
{
    public static $fields = [
        'account_id',
        'account_identity',
        'phash',
        'active_email',
        'type',
        'operational',
        'created_date',
        'last_login',
        'last_login_failure',
        'login_failure_interval',
        'karma_total',
        'last_ip',
        'confirmed',
        'verification_number',
        'oauth_provider',
        'oauth_id',
    ];

    /**
     * Takes raw db column data and sets properties each from the field list
     */
    public function __construct($data = [])
    {
        $this->info = $data;

        foreach (self::$fields as $field) {
            $this->$field = (isset($data[$field]) ? $data[$field] : null);
        }
    }

    /**
     * Get an account object by id
     *
     * @param int $account_id
     * @return Account|null
     */
    public static function findById(?int $account_id): ?Account
    {
        $data = self::accountInfo($account_id);

        if (isset($data['account_identity']) && !empty($data['account_identity'])) {
            return new Account($data);
        } else {
            return null;
        }
    }

    /**
     * Get an account object by email
     *
     * @param String $email_identity
     * @return Account|null
     */
    public static function findByIdentity($email_identity): ?Account
    {
        $account_info = query_row(
            "select account_id from accounts where account_identity = :identity_email",
            [':identity_email' => $email_identity]
        );

        return self::findById($account_info['account_id']);
    }

    /**
     * Get the account that matches an oauth id.
     *
     * @param int    $oauth_id
     * @param String $provider (optional) Defaults to facebook
     * @todo oauth_id should probably be made a string to avoid overflow problems.
     * @return Account|null
     */
    public static function findAccountByOauthId($oauth_id, $provider = 'facebook'): ?Account
    {
        $account_info = query_row(
            "SELECT account_id FROM accounts WHERE (oauth_id = :id AND oauth_provider = :provider) ORDER BY operational, type, created_date ASC LIMIT 1",
            [
                ':id'       => Filter::toNonNegativeInt($oauth_id),
                ':provider' => $provider,
            ]
        );

        if (empty($account_info) || !$account_info['account_id']) {
            return null;
        } else {
            return self::findById($account_info['account_id']);
        }
    }

    /**
     * Partially obsfucate an email address for display
     */
    private static function redactEmail($email): string
    {
        // Redact the email by removing the center of the first part, and the center of the domain
        return substr($email, 0, 5) . '...@.....' . substr($email, -5);
    }

    public static function redact($account): Account
    {
        $account->active_email = self::redactEmail($account->active_email);
        $account->account_identity = self::redactEmail($account->account_identity);
        $info = $account->info;
        $info['active_email'] = self::redactEmail($account->active_email);
        $info['account_identity'] = self::redactEmail($account->account_identity);
        $account->info = $info;
        $account->verification_number = null;
        return $account;
    }

    /**
     * Get an account for a character
     *
     * @param Character $char
     * @return Account|null
     */
    public static function findByChar(Player $char): ?Account
    {
        $query =
            'SELECT account_id FROM accounts
                    WHERE account_id = (
                        select _account_id from account_players where _player_id = :pid
                        )';
        $id = query_item($query, [':pid' => $char->id()]);

        return $id ? self::findById($id) : null;
    }

    /**
     * Find account by active_email (as opposed to identity)
     *
     * @param String $email
     * @return Account|null
     */
    public static function findByEmail($email): ?Account
    {
        $normalized_email = strtolower(trim($email));

        if ($normalized_email === '') {
            return null;
        }

        $query = 'SELECT account_id FROM accounts WHERE lower(active_email) = lower(:email) LIMIT 1';

        return self::findById(query_item($query, [':email' => $normalized_email]));
    }

    /**
     * Get the Account by a ninja name (aka player.uname).
     *
     * @param String $ninja_name
     * @return Account|null
     */
    public static function findByNinjaName($ninja_name): ?Account
    {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON account_id = _account_id
            JOIN players ON player_id = _player_id
            WHERE lower(uname) = lower(:ninja_name) LIMIT 1';

        return self::findById(query_item($query, [':ninja_name' => $ninja_name]));
    }

    /**
     * @param string $username
     * @return Account|null
     */
    public static function findByLogin($username): ?Account
    {
        $query = 'SELECT account_id FROM accounts WHERE active_email = :login1
            UNION
            SELECT _account_id AS account_id FROM players
            JOIN account_players ON player_id = _player_id
            WHERE lower(uname) = :login2';

        $params = [
            ':login1' => strtolower($username),
            ':login2' => strtolower($username),
        ];

        return self::findById(query_item($query, $params));
    }

    /**
     * Pull account record from database
     *
     * @param int $account_id
     * @return Array
     */
    public static function accountInfo($account_id): array | bool
    {
        return query_row(
            "SELECT *, date_part('epoch', now() - coalesce(last_login_failure, '1999-01-01')) AS login_failure_interval FROM accounts WHERE account_id = :account_id",
            [':account_id' => [$account_id, PDO::PARAM_INT]]
        );
    }

    /**
     * Create a new account
     * @return int|false
     */
    public static function create(int $ninja_id, string $email, string $password_to_hash, int $verification_number, int $confirmed, int $type = 0, int $active = 1, $ip = null): int | false
    {
        DatabaseConnection::getInstance();

        $new_account_sequence_id = query_item("SELECT nextval('accounts_account_id_seq')");

        $ins = "INSERT INTO accounts (account_id, account_identity, active_email, phash, type, operational, verification_number, confirmed, last_ip)
            VALUES (:acc_id, :email, :email2, crypt(:password, gen_salt('bf', 10)), :type, :operational, :verification_number, :confirmed, :ip)";

        $email = strtolower($email);

        $statement = DatabaseConnection::$pdo->prepare($ins);
        $statement->bindParam(':acc_id', $new_account_sequence_id);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':email2', $email);
        $statement->bindParam(':password', $password_to_hash);
        $statement->bindParam(':type', $type, PDO::PARAM_INT);
        $statement->bindParam(':operational', $active, PDO::PARAM_INT);
        $statement->bindParam(':verification_number', $verification_number, PDO::PARAM_INT);
        $statement->bindParam(':confirmed', $confirmed, PDO::PARAM_INT);
        $statement->bindParam(':ip', $ip);
        $statement->execute();

        // Create the link between account and player.
        $link_ninja = 'INSERT INTO account_players (_account_id, _player_id, last_login) VALUES (:acc_id, :ninja_id, default)';

        $statement = DatabaseConnection::$pdo->prepare($link_ninja);
        $statement->bindParam(':acc_id', $new_account_sequence_id, PDO::PARAM_INT);
        $statement->bindParam(':ninja_id', $ninja_id, PDO::PARAM_INT);
        $statement->execute();

        $sel_ninja_id = 'SELECT player_id FROM players
            JOIN account_players ON player_id = _player_id
            JOIN accounts ON _account_id = account_id
            WHERE account_id = :acc_id ORDER BY level DESC LIMIT 1';

        $verify_ninja_id = query_item($sel_ninja_id, [':acc_id' => [$new_account_sequence_id, PDO::PARAM_INT]]);

        return ($verify_ninja_id != $ninja_id ? false : $new_account_sequence_id);
    }

    /**
     * The array of account data as pulled from the database
     * @return array
     */
    public function info(): array
    {
        return $this->info;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->account_id;
    }

    /**
     * Alias for getId()
     *
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->getId();
    }

    /**
     * Simple wrapper function for getting email from accounts
     *
     * @return string email of the account
     */
    public function email(): string
    {
        return $this->getActiveEmail();
    }

    /**
     * This is the currently sent-to email, whereas the identity becomes
     * immutably fixed as the signup email
     * @return string
     */
    public function getActiveEmail(): string
    {
        return $this->active_email;
    }

    /**
     * Set new email to send to.
     */
    public function setActiveEmail($p_email): void
    {
        if (self::emailIsValid($p_email)) {
            $this->active_email     = $p_email;
            $this->account_identity = $p_email;
        } else {
            throw new \InvalidArgumentException('The email provided does not meet validation requirements.');
        }
    }

    /**
     * @return string|null Time of last login
     */
    public function getLastLogin(): ?string
    {
        return $this->info['last_login'];
    }

    /**
     * @return string|null Time of last failed login attempt
     */
    public function getLastLoginFailure(): ?string
    {
        return $this->info['last_login_failure'];
    }

    /**
     * The total karma that the account ever gained,
     * though some of the karma may have been spent per player
     * This should only ever increment upwards.
     * @return int
     */
    public function getKarmaTotal(): int
    {
        return $this->info['karma_total'];
    }

    /**
     * @param int $p_amount
     */
    public function setKarmaTotal($p_amount): void
    {
        $this->info['karma_total'] = (int) $p_amount;
    }

    /**
     * @return string|null
     */
    public function getLastIp(): ?string
    {
        return $this->info['last_ip'];
    }

    /**
     * Identity wrapper.
     * @return string The initial signup email is the identity, generally.
     */
    public function identity(): string
    {
        return $this->getIdentity();
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->account_identity;
    }

    /**
     * Type, ostensibly used for "member" "admin" or other roles.
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type): int
    {
        $cast_type = Filter::toNonNegativeInt($type);

        if ($cast_type != $type) {
            throw new \Exception('Account: The account type set was inappropriate.');
        }

        $this->type = $cast_type;

        return $this->type;
    }

    /**
     * Numeric Id for a oauth login provider
     * facebook, google+, etc etc
     * @param $id int|string|null Ids can be strings because of their length against the integer overflow limit
     */
    public function setOauthId($id, $provider = 'facebook'): bool
    {
        $this->oauth_id = $id;
        if ($provider) {
            $this->oauth_provider = $provider;
        }
        return true;
    }

    /**
     * @return int|string|null
     * Getting ids per provider not implemented yet.
     */
    public function getOauthId($provider = 'facebook'): int|string|null
    {
        return $this->oauth_id;
    }

    /**
     * @return string|null
     */
    public function getOauthProvider(): ?string
    {
        return $this->oauth_provider;
    }

    /**
     * @param string $provider
     */
    public function setOauthProvider($provider): string
    {
        $this->oauth_provider = $provider;
        return $this->oauth_provider;
    }

    /**
     * Check operational status of account
     *
     * @return boolean
     */
    public function isOperational(): bool
    {
        return ($this->operational === true);
    }

    /**
     * @return void
     */
    public function setOperational(bool $p_operational): void
    {
        $this->operational = (bool) $p_operational;
    }

    /**
     * @return void
     */
    public function setConfirmed(bool $p_confirmed): void
    {
        $this->confirmed = (bool) $p_confirmed;
    }

    /**
     * Check whether an account is confirmed.
     */
    public function isConfirmed(): bool
    {
        return (bool) $this->confirmed;
    }

    /**
     * Change the account password
     *
     * @param string $new_password
     * @return int Number of rows updated
     */
    public function changePassword($new_password): int
    {
        $query = "UPDATE accounts SET phash = crypt(:password, gen_salt('bf', 10)) WHERE account_id = :account_id";

        return update_query(
            $query,
            [
                ':account_id' => $this->getId(),
                ':password'   => $new_password,
            ]
        );
    }

    /**
     * A partial save of account information.
     */
    public function save(): int
    {
        $params = [
            ':identity'       => $this->getIdentity(),
            ':active_email'   => $this->getActiveEmail(),
            ':type'           => $this->getType(),
            ':oauth_provider' => $this->getOauthProvider(),
            ':oauth_id'       => (string)$this->getOauthId($this->getOauthProvider()),
            ':account_id'     => $this->getId(),
            ':karma_total'    => $this->getKarmaTotal(),
            ':operational'    => [$this->isOperational(), \PDO::PARAM_BOOL],
            ':confirmed'      => [$this->isConfirmed() ? 1 : 0, \PDO::PARAM_INT],
        ];

        $updated = update_query('UPDATE accounts SET
            account_identity = :identity, active_email = :active_email, type = :type, oauth_provider = :oauth_provider,
            oauth_id = :oauth_id, karma_total = :karma_total, operational = :operational, confirmed = :confirmed
            WHERE account_id = :account_id', $params);

        return $updated;
    }

    /**
     * Delete the current account
     */
    public function delete(): int
    {
        $query = 'DELETE FROM accounts WHERE account_id = :account_id';
        $id = $this->getId();

        update_query($query, [':account_id' => $id]);
        return $id;
    }

    /**
     * Update the time of last failed login.
     */
    public static function updateLastLoginFailure(Account $account): int
    {
        $update = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
        return update_query($update, [':account_id' => [$account->id(), PDO::PARAM_INT]]);
    }

    /**
     * Very rough check that am email is approximately correct & allowable
     * It should be very non-strict overall.
     */
    public static function emailIsValid($p_email): bool
    {
        return preg_match("/^[a-z0-9!#$%&'*+?^_`{|}~=\.-]+@[a-z0-9.-]+\.[a-z]+$/i", $p_email);
    }

    /**
     * Return the error reason for a username not validating, if it doesn't.
     *
     * Username requirements:
     * A username must start with a lower-case or upper-case letter
     * A username can contain only letters, numbers, underscores, or dashes.
     * A username must be from 3 to 24 characters long
     * A username cannot end in an underscore or dash
     * A username cannot contain 2 consecutive special characters
     *
     * @return string|boolean
     */
    public static function usernameIsValid($username): string | bool
    {
        $error = false;
        $username = (string) $username;

        if (mb_strlen($username) > UNAME_UPPER_LENGTH) {
            $error = 'Name too long. Must be 3 to 24 characters. ';
        } elseif (mb_strlen($username) < UNAME_LOWER_LENGTH) {
            $error = 'Name too short. Must be 3 to 24 characters. ';
        }

        if (mb_substr($username, 0, 1, 'utf-8') === '_') {
            $error .= 'Name cannot start with an underscore. ';
        }

        if (mb_substr($username, 0, 1, 'utf-8') === ' ') {
            $error .= 'Name cannot start with an space. ';
        }

        if (mb_substr($username, -1, null, 'utf-8') === '_') {
            $error .= 'Name cannot end in an underscore. ';
        }

        if (!preg_match("#^[a-z]+#i", $username)) {
            $error .= 'Name must start with a letter. ';
        }

        if (!preg_match("#[\da-z\-_]*[a-z0-9]$#i", $username)) {
            $error .= 'Name must end with a letter or number. ';
        }

        if (preg_match("#[\-_]{2}#", $username)) {
            $error .= 'More than two special characters in a row are not allowed in name. ';
        }

        if (!preg_match("#[\da-z\-_]#i", $username)) {
            $error .= 'No special characters except - dash and _ underscore, please. ';
        }

        if (!preg_match("#^[a-z]+([\da-z\-_]+[a-z0-9])?$#iD", $username)) {
            $error .= 'Name can only contain letters and numbers, with a dash or underscore or two. ';
        }

        return $error;
    }

    /**
     * Authenticate against an already-matched account
     * @note that this does not check for operational or confirmed.
     * @return boolean
     */
    public function authenticate($password): bool
    {
        $sql = "SELECT account_id,
		    CASE WHEN phash = crypt(:pass, phash) THEN 1 ELSE 0 END AS authenticated
			FROM accounts
			WHERE account_id = :account";
        $result = query($sql, [':account' => $this->id(), ':pass' => $password]);
        if ($result->rowCount() === 1) {
            $row = $result->fetch();
            return (intval($row['authenticated']) === 1);
        } else {
            return false;
        }
    }

    /**
     * Get the Ninjas belonging to an account
     * @return Player[] The ninjas for the account
     */
    public function getCharacters(): array
    {
        $pcs = query(
            'select player_id from players p 
            join account_players ap on ap._player_id = p.player_id
            join accounts a on a.account_id = ap._account_id
            where a.account_id = :aid',
            [':aid' => [$this->account_id, PDO::PARAM_INT]]
        );
        $ninjas = [];
        foreach ($pcs as $pc) {
            $ninja = Player::find($pc['player_id']);
            $ninjas[$ninja->name()] = $ninja;
        }
        return $ninjas;
    }

    /**
     * Deactivate an account by id
     */
    public static function deactivate(Account $account): int
    {
        $deactivated = update_query(
            'UPDATE accounts SET operational = false WHERE account_id = :account_id',
            [':account_id' => [$account->id(), PDO::PARAM_INT]]
        );
        return $deactivated;
    }

    /**
     * Activate an account by id
     */
    public static function activate(Account $account): int
    {
        $activated = update_query(
            'UPDATE accounts SET operational = true WHERE account_id = :account_id',
            [':account_id' => [$account->id(), PDO::PARAM_INT]]
        );
        return $activated;
    }

    /**
     * Deactivate an account by it's player
     */
    public static function deactivateByCharacter(Player $char): int
    {
        $deactivated = update_query(
            'UPDATE accounts
            SET operational = false
            WHERE account_id = (
                SELECT ap._account_id
                FROM account_players ap
                WHERE ap._player_id = :player_id
            )',
            [':player_id' => [$char->id(), PDO::PARAM_INT]]
        );
        return $deactivated;
    }

    /**
     * Reactivate an account by it's player
     */
    public static function reactivateByCharacter(Player $char): int
    {
        $reactivated = update_query(
            'UPDATE accounts
            SET operational = true WHERE account_id = (
                select ap._account_id from account_players ap where ap._player_id = :player_id
            )',
            [':player_id' => [$char->id(), PDO::PARAM_INT]]
        );
        return $reactivated;
    }

    /**
     * Deactivate a single player character
     */
    public static function deactivateSingleCharacter(Player $char): int
    {
        return query_item(
            'UPDATE players SET active = :status WHERE player_id = :id',
            [
                ':status' => 0,
                ':id' => $char->id()
            ]
        );
    }

    /**
     * Activate a single player character
     */
    public static function reactivateSingleCharacter(Player $char): int
    {
        return query_item(
            'UPDATE players SET active = :status WHERE player_id = :id',
            [
                ':status' => 1,
                ':id' => $char->id()
            ]
        );
    }
}
