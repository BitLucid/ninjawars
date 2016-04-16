<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\Filter;
use \PDO;

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
class Account {
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
    public function __construct($data = []) {
        $this->info = $data;

        foreach (self::$fields AS $field) {
            $this->$field = (isset($data[$field]) ? $data[$field] : null);
        }
    }

    /**
     * Get an account object by id
     *
     * @param int $account_id
     * @return Account|null
     */
    public static function findById($account_id) {
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
	public static function findByIdentity($email_identity) {
        $account_info = query_row("select account_id from accounts where account_identity = :identity_email",
            [':identity_email'=>$email_identity]
        );

		return self::findById($account_info['account_id']);
	}

    /**
     * Get the account that matches an oauth id.
     *
     * @param int $oauth_id
     * @param String $provider (optional) Defaults to facebook
     * @todo oauth_id should probably be made a string to avoid overflow problems.
     * @return Account|null
     */
	public static function findAccountByOauthId($oauth_id, $provider='facebook'){
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
     * Get an account for a character
     *
     * @param Character $char
     * @return Account|null
     */
    public static function findByChar(Player $char) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON _account_id = account_id
            JOIN players ON _player_id = player_id
            WHERE players.player_id = :pid';

        return self::findById(query_item($query, [':pid' => $char->id()]));
    }

    /**
     * Find account by active_email (as opposed to identity)
     *
     * @param String $email
     * @return Account|null
     */
    public static function findByEmail($email) {
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
    public static function findByNinjaName($ninja_name) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON account_id = _account_id
            JOIN players ON player_id = _player_id
            WHERE lower(uname) = lower(:ninja_name) LIMIT 1';

        return self::findById(query_item($query, [':ninja_name'=>$ninja_name]));
    }

    /**
     * @param string $username
     * @return Account|null
     */
    public static function findByLogin($username) {
        $query = 'SELECT account_id FROM accounts WHERE active_email = :login1
            UNION
            SELECT _account_id AS account_id FROM players
            JOIN account_players ON player_id = _player_id
            WHERE lower(uname) = :login2';

        $params = [
            ':login1'=>strtolower($username),
            ':login2'=>strtolower($username),
        ];

        return self::findById(query_item($query, $params));
    }

    /**
     * Pull account record from database
     *
     * @param int $account_id
     * @return Array
     */
    public static function accountInfo($account_id) {
        return query_row(
            "SELECT *, date_part('epoch', now() - coalesce(last_login_failure, '1999-01-01')) AS login_failure_interval FROM accounts WHERE account_id = :account_id",
            [':account_id'=>[$account_id, PDO::PARAM_INT]]
        );
    }

    /**
     * Create a new account
     * @return int|false
     */
    public static function create($ninja_id, $email, $password_to_hash, $confirm, $type=0, $active=1, $ip=null) {
        DatabaseConnection::getInstance();

        $newID = query_item("SELECT nextval('accounts_account_id_seq')");

        $ins = "INSERT INTO accounts (account_id, account_identity, active_email, phash, type, operational, verification_number, last_ip)
            VALUES (:acc_id, :email, :email2, crypt(:password, gen_salt('bf', 10)), :type, :operational, :verification_number, :ip)";

        $email = strtolower($email);

        $statement = DatabaseConnection::$pdo->prepare($ins);
        $statement->bindParam(':acc_id', $newID);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':email2', $email);
        $statement->bindParam(':password', $password_to_hash);
        $statement->bindParam(':type', $type, PDO::PARAM_INT);
        $statement->bindParam(':operational', $active, PDO::PARAM_INT);
        $statement->bindParam(':verification_number', $confirm);
        $statement->bindParam(':ip', $ip);
        $statement->execute();

        // Create the link between account and player.
        $link_ninja = 'INSERT INTO account_players (_account_id, _player_id, last_login) VALUES (:acc_id, :ninja_id, default)';

        $statement = DatabaseConnection::$pdo->prepare($link_ninja);
        $statement->bindParam(':acc_id', $newID, PDO::PARAM_INT);
        $statement->bindParam(':ninja_id', $ninja_id, PDO::PARAM_INT);
        $statement->execute();

        $sel_ninja_id = 'SELECT player_id FROM players
            JOIN account_players ON player_id = _player_id
            JOIN accounts ON _account_id = account_id
            WHERE account_id = :acc_id ORDER BY level DESC LIMIT 1';

        $verify_ninja_id = query_item($sel_ninja_id, array(':acc_id'=>array($newID, PDO::PARAM_INT)));

        return ($verify_ninja_id != $ninja_id ? false : $newID);
    }

    /**
     * The array of account data as pulled from the database
     * @return array
     */
    public function info() {
        return $this->info;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->account_id;
    }

    /**
     * Alias for getId()
     *
     * @return int
     */
    public function id() {
        return $this->getId();
    }

    /**
     * Simple wrapper function for getting email from accounts
     *
     * @return String email of the account
     */
    public function email() {
        return $this->getActiveEmail();
    }

    /**
     * This is the currently sent-to email, whereas the identity becomes 
     * immutably fixed as the signup email
     * @return string
     */
    public function getActiveEmail() {
        return $this->active_email;
    }

    /**
     * Set new email to send to.
     */
    public function setActiveEmail($p_email) {
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
    public function getLastLogin() {
        return $this->info['last_login'];
    }

    /**
     * @return string|null Time of last failed login attempt
     */
    public function getLastLoginFailure() {
        return $this->info['last_login_failure'];
    }

    /**
     * The total karma that the account ever gained, 
     * though some of the karma may have been spent per player
     * This should only ever increment upwards.
     * @return int
     */
    public function getKarmaTotal() {
        return $this->info['karma_total'];
    }

    /**
     * @param int $p_amount
     */
    public function setKarmaTotal($p_amount) {
        $this->info['karma_total'] = (int) $p_amount;
    }

    /**
     * @return string|null
     */
    public function getLastIp() {
        return $this->info['last_ip'];
    }

    /**
     * Identity wrapper.
     * @return string The initial signup email is the identity, generally.
     */
    public function identity() {
        return $this->getIdentity();
    }

    /**
     * @return string
     */
    public function getIdentity() {
        return $this->account_identity;
    }

    /**
     * Type, ostensibly used for "member" "admin" or other roles.
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type) {
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
     */
    public function setOauthId($id, $provider='facebook') {
        $this->oauth_id = $id;
        if($provider){
            $this->oauth_provider = $provider;
        }
        return true;
    }

    /**
     * @return int
     */
    public function getOauthId($provider='facebook') {
        return $this->oauth_id;
    }

    /**
     * @return string
     */
    public function getOauthProvider() {
        return $this->oauth_provider;
    }

    /**
     * @param string $provider
     */
    public function setOauthProvider($provider) {
        return ($this->oauth_provider = $provider);
    }

    /**
     * Check operational status of account
     *
     * @return boolean
     */
    public function isOperational() {
        return ($this->operational === true);
    }

    /**
     * @return void
     */
    public function setOperational($p_operational) {
        $this->operational = (bool) $p_operational;
    }

    /**
     * Check whether an account is confirmed.
     */
    public function isConfirmed() {
        return ($this->confirmed === 1);
    }

    /**
     * Change the account password
     *
     * @param string $new_password
     * @return int Number of rows updated
     */
    public function changePassword($new_password) {
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
    public function save() {
        $params = [
            ':identity'       => $this->getIdentity(),
            ':active_email'   => $this->getActiveEmail(),
            ':type'           => $this->getType(),
            ':oauth_provider' => $this->getOauthProvider(),
            ':oauth_id'       => (string)$this->getOauthId($this->getOauthProvider()),
            ':account_id'     => $this->getId(),
            ':karma_total'    => $this->getKarmaTotal(),
            ':operational'    => [$this->isOperational(), \PDO::PARAM_BOOL],
            ':confirmed'      => [(int) $this->isConfirmed(), \PDO::PARAM_INT],
        ];

        $updated = update_query('UPDATE accounts SET
            account_identity = :identity, active_email = :active_email, type = :type, oauth_provider = :oauth_provider,
            oauth_id = :oauth_id, karma_total = :karma_total, operational = :operational, confirmed = :confirmed
            WHERE account_id = :account_id', $params);

        return $updated;
    }

    /**
     * Update the time of last failed login.
     */
    public static function updateLastLoginFailure(Account $account) {
        $update = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
        return query($update, [':account_id' => [$account->id(), PDO::PARAM_INT]]);
    }

    /**
     * Very rough check that am email is approximately correct & allowable
     * It should be very non-strict overall.
     */
    public static function emailIsValid($p_email) {
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
    public static function usernameIsValid($username) {
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
    public function authenticate($password) {
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
    public function getCharacters(){
        $pcs = query('select player_id from players p 
            join account_players ap on ap._player_id = p.player_id
            join accounts a on a.account_id = ap._account_id
            where a.account_id = :aid', 
            [':aid'=>[$this->account_id, PDO::PARAM_INT]]);
        $ninjas = [];
        foreach($pcs as $pc){
            $ninja = Player::find($pc['player_id']);
            $ninjas[$ninja->name()] = $ninja;
        }
        return $ninjas;
    }
}
