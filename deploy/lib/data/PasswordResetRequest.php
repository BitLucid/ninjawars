<?php

/**
 * Model that manipulates the data for a password reset request.
**/
class PasswordResetRequest{
	function __construct($request_id=null, Array $request_data=null){
		if($request_id){
			$data = PasswordResetRequest::retrieve($request_id);
		} else {
			$data = $request_data;
		}
		$this->data = $data;
	}

	/**
	 * Get account info that matches for resetting.
	 * @return int
	 **/ 
	public static function findAccount($email=null, $ninja_name=null){
		if(!$email && !$ninja_name){
			throw new Exception('Email or Ninja Name must be specified to get a password reset email!');
		} else {
			$params = [];
			if($email){
				$params[':email'] = $email;
				$params[':email2'] = $email;
				$where = 'where active_email = :email or account_identity = :email2 ';
				if($ninja_name){
					$params[':ninja_name'] = $ninja_name;
					$where .= ' or uname = :ninja_name ';
				}
			} else { // Assume ninja name alone.
				$params[':ninja_name'] = $ninja_name;
				$where = ' where uname = :ninja_name ';
			}

			return (int)query_item('select account_id from accounts left join account_players on account_id = _account_id left join players on player_id = _player_id
				'.$where, $params);
		}
	}

	/**
	 * Reset a password forcibly.  Validation must be done separately.
	**/
	public static function reset($account_id, $new_pass){
		$account_exists = query_item('select 1 from accounts where account_id = :account_id',
			[':account_id'=>$account_id]);
		var_dump($account_exists);
		if(!$account_exists){
			return false;
		}
		$updated = update_query("update accounts set phash = crypt(:password, gen_salt('bf', 10)) where account_id = :account_id",
			[':account_id'=>$account_id, ':password'=>$new_pass]);
		return $updated;
	}


	/**
	 * Check for matching nonces.
	**/
	public static function match($nonce){
		if(defined('DEBUG') && DEBUG){
			$data = ['request_id'=>1, '_account_id'=>1, 'nonce'=>$nonce,
				'requested_on_datetime'=>'2015-09-16 15:22:37.931326-04', 'used'=>false];
		}
		$data = query_row('select request_id, _account_id, nonce, requested_on_datetime, used 
			from password_reset_requests where nonce = :nonce', [':nonce'=>$nonce]);
		return $data;
	}

	/**
	 * Retrieve an individual request with it's data.
	**/
	public static function retrieve($request_id){
		if(defined('DEBUG') && DEBUG){
			// While debugging, always return mock data: 
			$data = ['request_id'=>$request_id, '_account_id'=>1, 'nonce'=>'130fad31f318e36990db6feaf438a6f998db9881664a653155bd0c3316b9651ba772dab4758a4f3c0fa790d7d976bdd1856e6e3db92481e75a3bb5213bd6a231',
			'requested_on_datetime'=>'2015-09-16 15:22:37.931326-04', 'used'=>false];
		}
		$data = query_row('select request_id, _account_id, nonce, requested_on_datetime, used 
				from password_reset_requests where request_id = :request_id', [':request_id'=>$request_id]);

		return $data;
	}


	/**
	 * Create a password reset request.
	**/
	public static function request($account_id, $nonce=null){
		$nonce = $nonce? $nonce : nonce();
		update_query('update password_reset_requests set used = true where _account_id = :account_id',
			[':account_id'=>$account_id]); // Deactivate any previous requests.
		insert_query('insert into password_reset_requests 
			(_account_id, nonce, requested_on_datetime, used) values 
			(:account_id, :nonce, now(), false)', [':account_id'=>$account_id, ':nonce'=>$nonce], 'password_reset_requests_request_id_seq');
		return true;
	}


}