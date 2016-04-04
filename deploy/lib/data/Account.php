<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use \PDO;

/**
 * Player accounts and their info
 */
class Account {
    public static $fields = [
        'account_id',
        'account_identity',
        'phash',
        'active_email',
        'type',
        'operational',
        'created_data',
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
	public static function find($email_identity) {
        $account_info = query_row('select * from accounts where account_identity = :identity_email',
            [':identity_email'=>$email_identity]
        );

		return self::findById($account_info['account_id']);
	}

    /**
     * Get the account that matches an oauth id.
     *
     * @param int $oauth_id
     * @param String $provider (optional) Defaults to facebook
     * @return Account|null
     */
	public static function findAccountByOauthId($oauth_id, $provider='facebook'){
        $account_info = query_row(
            'SELECT * FROM accounts WHERE (oauth_id = :id AND oauth_provider = :provider) ORDER BY operational, type, created_date ASC LIMIT 1',
            [
                ':id'       => positive_int($oauth_id),
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
     * @return Account
     */
    public static function findByChar(Character $char) {
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
     * @return Account
     */
    public static function findByNinjaName($ninja_name) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON account_id = _account_id
            JOIN players ON player_id = _player_id
            WHERE lower(uname) = lower(:ninja_name) LIMIT 1';

        return self::findById(query_item($query, [':ninja_name'=>$ninja_name]));
    }

    /**
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

    public function info() {
        return $this->info;
    }

    public function getId() {
        return $this->account_id;
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
     * Alias for getId()
     *
     * @return int
     */
    public function id() {
        return $this->getId();
    }

    public function getActiveEmail() {
        return $this->active_email;
    }

    public function setActiveEmail($p_email) {
        if (self::emailIsValid($p_email)) {
            $this->active_email     = $p_email;
            $this->account_identity = $p_email;
        } else {
            throw new \InvalidArgumentException('The email provided does not meet validation requirements.');
        }
    }

    public function getLastLogin() {
        return $this->info['last_login'];
    }

    public function getLastLoginFailure() {
        return $this->info['last_login_failure'];
    }

    public function getKarmaTotal() {
        return $this->info['karma_total'];
    }

    public function setKarmaTotal($p_amount) {
        $this->info['karma_total'] = (int) $p_amount;
    }

    public function getLastIp() {
        return $this->info['last_ip'];
    }

    /**
     * Identity wrapper.
     */
    public function identity() {
        return $this->getIdentity();
    }

    public function getIdentity() {
        return $this->account_identity;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $cast_type = positive_int($type);

        if ($cast_type != $type) {
            throw new \Exception('Account: The account type set was inappropriate.');
        }

        $this->type = $cast_type;

        return $this->type;
    }

    public function setOauthId($id, $provider='facebook') {
        $this->oauth_id = $id;
        if($provider){
            $this->oauth_provider = $provider;
        }
        return true;
    }

    public function getOauthId($provider='facebook') {
        return $this->oauth_id;
    }

    public function getOauthProvider() {
        return $this->oauth_provider;
    }

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
     * @param String $newPassword
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

    public static function updateLastLoginFailure(Account $account) {
        $update = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
        return query($update, [':account_id' => [$account->id(), PDO::PARAM_INT]]);
    }

    public static function emailIsValid($p_email) {
        return preg_match("/^[a-z0-9!#$%&'*+?^_`{|}~=\.-]+@[a-z0-9.-]+\.[a-z]+$/i", $p_email);
    }
}
