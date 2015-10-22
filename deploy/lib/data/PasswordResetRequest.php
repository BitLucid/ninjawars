<?php

namespace app\data;

use Illuminate\Database\Eloquent\Model;

class Color extends Model{
	protected $primaryKey = 'ColorId';
	//protected $table = 'Colors'; // Automatically mapped to plural of classname
	//protected $dates = ['DateCreated', 'DateStatus'];
    //public $timestamps = false;
    const CREATED_AT = 'DateCreated';
    const UPDATED_AT = 'DateStatus';
    // The overridden database column updated_at/created_at settings.
    // Set fields that can be mass specified upon ::create()
    protected $fillable = array('Code', 'Name', 'ColorGroup', 'Descr', 'ImageURL', 'DisplayOrder', 'FlagStatus');
    // Excludes the date and primary key fields above

    /**
     * Special case method to get the id regardless of what it's actually called in the database
    **/
    public function id(){
    	return $this->ColorId;
    }

    /**
     * Compare whether the entity is current set to active.
    **/
    public function isActive(){
    	return $this->FlagStatus === 'A';
    }

	/**
	 * Raw array collection of all color info.
	**/
	public static function allRaw(){
		return query_array('select ColorId, Code, Name, ColorGroup, Descr, ImageURL, DisplayOrder, FlagStatus, FlagStatus = \'A\' as active, DateCreated, DateStatus from colors order by flagstatus = "A" desc, displayorder');
	}
}

/**
 * Model that manipulates the data for a password reset request.
**/
class PasswordResetRequest extends Model{

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

			$account_id = query_item('select account_id from accounts 
					left join account_players on account_id = _account_id 
					left join players on player_id = _player_id
					'.$where, $params);
			return AccountFactory::findById($account_id);
		}
	}

	/**
	 * Send out the password reset email to a requested account's email.
	**/
	public static function send($token, $email, $debug_allowed=true){
		// Email body contents will be: Click here to reset your password: {{ url('password/reset/'.$token) }}
		$url = WEB_ROOT.'resetpassword.php?token='.url($token);
		$rendered = render_template('email.password_reset_request.tpl', ['url'=>$url]);
		// Construct the email with Nmail, and then just send it.
		$nmail = new Nmail($email, $subject='NinjaWars: Your password reset request', $rendered, SUPPORT_EMAIL);
		if($debug_allowed && defined('DEBUG')) {
			$nmail->dump = DEBUG;
			$nmail->die_after_dump = DEBUG_ALL_ERRORS;
			$nmail->try_to_send = !DEBUG_ALL_ERRORS;
		}
		$passfail = $nmail->send();
		return $passfail;
	}

	/**
	 * Reset a password forcibly.  Validation must be done separately.
	 * @return boolean
	**/
	public static function reset(Account $account, $new_pass, $debug_allowed=true){
		$account_exists = query_item('select 1 from accounts where account_id = :account_id',
			[':account_id'=>$account->getId()]);
		if(!$account_exists || $new_pass === null || $new_pass === ''){
			return false;
		}
		$updated = update_query("update accounts set phash = crypt(:password, gen_salt('bf', 10)) where account_id = :account_id",
			[':account_id'=>$account->getId(), ':password'=>$new_pass]);
		$passfail = (bool) $updated;
		// Update the password reset request in the database to mark it as used.
		update_query('update password_reset_requests set used = true where _account_id = :id', [':id'=>($account->getId())]);
		$body = '
		Your password was reset.  Please contact '.SUPPORT_EMAIL_NAME.' via '.SUPPORT_EMAIL.' if this was an error.
		';
		$nmail = new Nmail($account->getActiveEmail(), $subject='NinjaWars: Your password was reset.', $body, SUPPORT_EMAIL);
		if($debug_allowed && defined('DEBUG')) {
			$nmail->dump = DEBUG;
			$nmail->die_after_dump = DEBUG_ALL_ERRORS;
			$nmail->try_to_send = !DEBUG_ALL_ERRORS;
		}
		$succeeded = $nmail->send();
		return (bool) $succeeded;
	}


	/**
	 * Check for matching token, and a matching interval period
	**/
	public static function match($token, $interval='4 hours'){
		$data = query_row('select request_id, accounts.account_id as account_id, accounts.active_email as email, nonce, requested_on_datetime, used 
			from password_reset_requests
			left join accounts on account_id = _account_id
			 where nonce = :token and used = false and (requested_on_datetime > (now()- interval \''.$interval.'\'))', [':token'=>$token]);
		return $data;
	}

	/**
	 * Retrieve an individual request with it's data.
	**/
	public static function retrieve($request_id){
		$data = query_row('select request_id, _account_id as account_id, nonce, requested_on_datetime, used 
				from password_reset_requests where request_id = :request_id', [':request_id'=>$request_id]);

		return $data;
	}


	/**
	 * Create a password reset request.
	**/
	public static function request($account_id, $nonce=null){
		$nonce = $nonce !== null? $nonce : nonce();
		// Deactivate any previous requests.
		update_query('update password_reset_requests set used = true where _account_id = :account_id',
			[':account_id'=>$account_id]);
		insert_query('insert into password_reset_requests 
			(_account_id, nonce, requested_on_datetime, used) values 
			(:account_id, :nonce, now(), false)', [':account_id'=>$account_id, ':nonce'=>$nonce], 'password_reset_requests_request_id_seq');
		return $nonce;
	}


}