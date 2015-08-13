<?php

/**
 * Create account objects via Factory(ok, actually Repository) object
 *
 *
**/
class AccountFactory{

	public static function make($account_id){
		return new Account($account_id);
	}

	public static function first(){
		$account_id = query_item('select account_id from accounts where operational limit 1');
		return new Account($account_id);
	}

	/*
	public static function create($email, $type){
		// Create a new account.
		$account_id = create_account($ninja_id, $email, $password_to_hash, $confirm, $type=0, $active=1)
	}*/

	public static function find($email_identity){
		$account_info = account_info_by_identity($email_identity);
		return new Account($account_info['account_id']);
	}

	public static function findByIdentity($identity_email){
		$info = account_info_by_identity($identity_email);
		return new Account($info['account_id']);
	}

	public static function findAccountByOauthId($id, $provider='facebook'){
		$account_info = find_account_info_by_oauth($id, $provider);
		return new Account($account_info['account_id']);
	}

	public static function save($account){
		$params = [':identity'=>$account->getIdentity(), ':active_email'=>$account->getActiveEmail(), ':type'=>$account->getType(),
			':oauth_id'=>$account->getOauthId(), ':oauth_provider'=>$account->getOauthProvider(), ':account_id'=>$account->getId()
			];
		$updated = query('update accounts set 
				account_identity = :identity, active_email = :active_email, type = :type, oauth_id = :oauth_id, oauth_provider = :oauth_provider 
				where account_id = :account_id', $params);
		return $updated;
	}
}