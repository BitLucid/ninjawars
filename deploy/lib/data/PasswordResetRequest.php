<?php

namespace app\data;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use \Account as Account;
use \Nmail as Nmail;

/**
 * Model that manipulates the data for a password reset request.
**/
class PasswordResetRequest extends Model{

	protected $primaryKey = 'request_id';
	protected $table = 'password_reset_requests';
    public $timestamps = false;

    //protected $guarded = ['request_id', 'created_at'];
    protected $fillable = ['_account_id', 'nonce', 'used'];

    /**
     * Custom initialization of `created_at` field, since this model only keeps one
    **/
    public static function boot(){
        static::creating(function($model){
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * Special case method to get the id regardless of what it's actually called in the database
    **/
    public function id(){
    	return $this->request_id;
    }

    /**
     * Compare whether the entity is current set to active.
    **/
    public function isActive(){
    	$fourHours = Carbon::now()->subHour(4);
		$id = PasswordResetRequest::select('request_id')->where('created_at', '>', $fourHours)->where('request_id', '=', $this->id())->lists('request_id');
		$not_used = !(bool) $this->used;
		return ($not_used && (bool) $id);
    }

	/**
	 * Reset a password when a request exists.
	 * @return boolean
	**/
	public static function reset(\Account $account, $new_pass, $debug_allowed=false){
		if(!$account->id() || !$account->isOperational() || $new_pass === null || $new_pass === ''){
			return false;
		}
		$passfail = $account->changePassword($new_pass);
		// Get request by account id.
		$request = PasswordResetRequest::where('_account_id', '=', $account->id())->first();
		if(!($request instanceof PasswordResetRequest) || !$request->isActive()){
			return false;
		} else {
			// Mark all prior requests as "used"
			PasswordResetRequest::where('_account_id', '=', $account->id())->update(['used' => 1]);
			$passfail = static::sendResetNotification($account->getActiveEmail(), $debug_allowed);
			return $passfail;
		}
	}

	/**
	 * Send a notice that a password was reset.
	 * @return boolean
	**/ 
	private static function sendResetNotification($email, $debug_allowed=false){
		$body = '
			Your password on ninjawars.net was reset.  Please contact '.SUPPORT_EMAIL_NAME.' via '.SUPPORT_EMAIL.' if this was an error.
		';
		$nmail = new Nmail($email, $subject='NinjaWars.net: Your password was reset.', $body, SUPPORT_EMAIL);
		if($debug_allowed && defined('DEBUG')) {
			$nmail->dump = DEBUG;
			$nmail->die_after_dump = DEBUG_ALL_ERRORS;
			$nmail->try_to_send = !DEBUG_ALL_ERRORS;
		}
		$passfail = $nmail->send();
		return (bool) $passfail;
	}


	/**
	 * Check for matching token, and a matching interval period
	 * @return PasswordResetRequest
	**/
	public static function match($token){ 
		$request = PasswordResetRequest::where('nonce', '=', $token)->first();
		if($request instanceof PasswordResetRequest && $request->isActive()){
			return $request;
		} else {
			return null;
		}
	}

	/**
	 * Generate a full password reset request for an account
	 * @param Account $account
	 * @returns PasswordResetRequest
	**/
	public static function generate(Account $account, $nonce=null){
		$nonce = $nonce !== null? $nonce : nonce();
		return PasswordResetRequest::create(['_account_id'=>$account->id(), 'nonce'=>$nonce]);
	}


}