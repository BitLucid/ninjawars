<?php
namespace NinjaWars\core\data;

/**
 * Player Accounts and their info
 */
class Account {
	public function __construct($account_id) {
		$this->account_id = $account_id;
		$this->info = account_info($account_id);
		$this->oauth_id = $this->info['oauth_id'];
		$this->oauth_provider = $this->info['oauth_provider'];
		$this->active_email = $this->info['active_email'];
		$this->account_identity = $this->info['account_identity'];
		$this->type = $this->info['type'];
	}

	public function info(){
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

	public function getActiveEmail(){
		return $this->active_email;
	}

	public function getLastLogin(){
		return $this->info['last_login'];
	}

	public function getLastLoginFailure(){
		return $this->info['last_login_failure'];
	}

	public function getKarmaTotal(){
		return $this->info['karma_total'];
	}

    public function setKarmaTotal($p_amount) {
        $this->info['karma_total'] = (int) $p_amount;
    }

	public function getLastIp(){
		return $this->info['last_ip'];
	}

	/**
	 * Identity wrapper.
	**/
	public function identity(){
		return $this->getIdentity();
	}

	public function getIdentity(){
		return $this->account_identity;
	}

	public function getType(){
		return $this->type;
	}

	public function setType($type){
		$cast_type = positive_int($type);
		if($cast_type != $type){
			throw new \Exception('Account: The account type set was inappropriate.');
		}
		$this->type = $cast_type;
		return $this->type;
	}

	public function setOauthId($id, $provider='facebook'){
		$this->oauth_id = $id;
		if($provider){
			$this->oauth_provider = $provider;
		}
		return true;
	}

	public function getOauthId($provider='facebook'){
		return $this->oauth_id;
	}

	public function getOauthProvider(){
		return $this->oauth_provider;
	}

	public function setOauthProvider($provider){
		return ($this->oauth_provider = $provider);
	}

	/**
	 * Check operational status of account
	**/
	public function isOperational(){
		return (bool) ($this->info['operational'] === true);
	}

	/**
	 * Check whether an account is confirmed.
	**/
	public function isConfirmed(){
		return (bool) ($this->info['confirmed'] === 1);
	}

    /**
     * Change the account password
     *
     * @param String $newPassword
     * @return int Number of rows updated
     */
    public function changePassword($newPassword) {
        $query = "UPDATE accounts SET phash = crypt(:password, gen_salt('bf', 10)) WHERE account_id = :account_id";

        return update_query(
            $query,
            [
                ':account_id' => $this->getId(),
                ':password'   => $newPassword,
            ]
        );
    }

}
