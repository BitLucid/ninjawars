<?php
namespace NinjaWars\core\data;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use NinjaWars\core\data\Account;
use \Nmail as Nmail;

/**
 * Model that manipulates the data for a password reset request.
 * @property $nonce
 * @property $request_id
 * @property $_account_id
 * @property $used
 */
class PasswordResetRequest extends Model {
    protected $primaryKey = 'request_id';
    protected $table = 'password_reset_requests';
    public $timestamps = false;

    //protected $guarded = ['request_id', 'created_at'];
    protected $fillable = ['_account_id', 'nonce', 'used'];

    /**
     * Custom initialization of `created_at` field, since this model only keeps one
     */
    public static function boot() {
        static::creating(function($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * Special case method to get the id regardless of what it's actually called in the database
     */
    public function id() {
        return $this->request_id;
    }

    /**
     * Get the account in a reliable manner.
     */
    public function account() {
        assert($this->_account_id);
        return Account::findById($this->_account_id);
    }

    /**
     * Compare whether the entity is current set to active.
     */
    public function isActive() {
        $fourHours = Carbon::now()->subHour(4);
        $id = PasswordResetRequest::select('request_id')
            ->where('created_at', '>', $fourHours)
            ->where('request_id', $this->id())
            ->where('used', false)
            ->lists('request_id');

        return (bool) $id;
    }

    /**
     * Reset a password when a request exists.
     *
     * @return boolean
     */
    public static function reset(Account $account, $new_pass) {
        if (!$account->id() || !$account->isOperational() || $new_pass === null || $new_pass === '') {
            return false;
        }

        // Get request by account id.
        $request = PasswordResetRequest::where('_account_id', '=', $account->id())->first();

        if (!($request instanceof PasswordResetRequest) || !$request->isActive()) {
            return false;
        } else {
            $account->changePassword($new_pass);
            // Mark all prior requests as "used"
            PasswordResetRequest::where('_account_id', '=', $account->id())->update(['used' => true]);
            return static::sendResetNotification($account->getActiveEmail());
        }
    }

    /**
     * Send a notice that a password was reset.
     *
     * @return boolean
     */
    public static function sendResetNotification($email) {
        $body = '
            Your password on ninjawars.net was reset.  
            Please contact '.SUPPORT_EMAIL_NAME.' via '.SUPPORT_EMAIL.' if this was an error.
        ';

        $nmail = new Nmail($email, 'NinjaWars.net: Your password was reset.', $body, SUPPORT_EMAIL);

        return (bool) $nmail->send();
    }

    /**
     * Check for matching token, and a matching interval period
     *
     * @return PasswordResetRequest
     */
    public static function match($token) {
        $request = PasswordResetRequest::where('nonce', $token)
            ->where('used', false)
            ->first();

        if ($request instanceof PasswordResetRequest && $request->isActive()) {
            return $request;
        } else {
            return null;
        }
    }

    /**
     * Generate a full password reset request for an account
     *
     * @param Account $account
     * @return PasswordResetRequest
     */
    public static function generate(Account $account, $nonce=null) {
        $nonce = ($nonce !== null ? $nonce : nonce());
        return PasswordResetRequest::create(['_account_id'=>$account->id(), 'nonce'=>$nonce]);
    }
}
