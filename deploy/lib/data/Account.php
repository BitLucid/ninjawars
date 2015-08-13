<?php

/**
 * Player Accounts and their info
 */
class Account{

	public function __construct($account_id){
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

	public function getId(){
		return $this->account_id;
	}

	public function getActiveEmail(){
		return $this->active_email;
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
			throw new Exception('Account: The account type set was inappropriate.');
		}
		$this->type = $cast_type;
		return $this->type;
	}

	public function setOauthId($id, $provider='facebook'){
		$this->oauth_id = $id;
		return true;
	}

	public function getOauthId($provider='facebook'){
		return $this->oauth_id;
	}

	public function getOauthProvider(){
		return $this->oauth_provider;
	}

	public function setOauthProvider($provider){
		$this->oauth_provider = $provider;
		return $this->oauth_provider;
	}
}