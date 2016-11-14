<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\Filter;
use \Nmail;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\extensions\NWTemplate;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * Give assistance to players and proto-players who anonymous users
 */
class AssistanceController extends AbstractController {
    const PRIV          = false;
    const ALIVE         = false;

    /**
     * Determines the user information for a certain email.
     *
     * @return array
     */
    private function userHavingEmail($email) {
        $data = query_row('SELECT uname, level, account_id, accounts.confirmed,
            accounts.verification_number, accounts.active_email,
            CASE WHEN active::bool THEN 1 ELSE 0 END AS active
            from accounts LEFT JOIN account_players ON account_id = _account_id
            LEFT JOIN players on _player_id = player_id
            WHERE trim(lower(active_email)) = trim(lower(:email)) limit 1;',
                array(':email'=>$email));
        return $data;
    }

    /**
     * Sends an email for the user's account data.
     */
    private function sendAccountEmail($email, $data) {
        $template_vars = [
            'lost_uname'   => $data['uname'],
            'active_email' => $data['active_email'],
            'confirmed'    => $data['confirmed'],
            'level'        => $data['level'],
        ];

        $_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);
        /* additional headers */
        $_to = array("$email"=>$data['uname']);
        $_subject = 'NinjaWars Account Info Request';
        $_body = (new NWTemplate())->assign($template_vars)->fetch('email.assistance.account.tpl');
        $mail_obj = new Nmail($_to, $_subject, $_body, $_from);
        // *** Set the custom replyto email. ***
        $mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));
        return $mail_obj->send();
    }

    /**
     * Sends the account confirmation email.
     *
     * @return boolean
     */
    private function sendConfirmationEmail($email, $data) {
        $template_vars = [
            'lost_uname'   => $data['uname'],
            'lost_confirm' => $data['verification_number'],
            'account_id'   => $data['account_id'],
        ];

        $_from = [SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME];
        $_to = [$email=>$data['uname']];
        $_subject = "NinjaWars Account Confirmation Info";
        $_body = (new NWTemplate())->assign($template_vars)->fetch('email.assistance.confirmation.tpl');
        $mail_obj = new Nmail($_to, $_subject, $_body, $_from);
        $mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));
        return $mail_obj->send();
    }

    /**
     * Display the assistance options to users.
     *
     * @return Response
     */
    public function index(Container $p_dependencies) {
        $request = RequestWrapper::$request;
        $email = filter_var($request->get('email', null), FILTER_SANITIZE_EMAIL);
        $password_request = $request->get('password_request');
        $confirmation_request = $request->get('confirmation_request');

        $error = null;
        $sent  = false;
        $attemptedToSendEmail = false;
        $data = null;
        $username = null;

        if (!$email && ($password_request || $confirmation_request)) {
            $error = 'invalidemail';
        } else if ($email) {
            $data = $this->userHavingEmail($email);
            $username = $data['uname'];
            if (!$data['uname']) {
                $error = 'nouser';
            } elseif ($password_request && $data['active']) {
                $sent = $this->sendAccountEmail($email, $data);
                $attemptedToSendEmail = true;
            } else {
                // Confirmation request.
                if ($data['confirmed']) {
                    $error = 'alreadyconfirmed';
                } else {
                    $sent = $this->sendConfirmationEmail($email, $data);
                    $attemptedToSendEmail = true;
                }
            }
        }

        if ($attemptedToSendEmail && !$sent) {
            $error = 'emailfail';
        }

        $parts = [
            'data'=>$data,
            'error'=>$error,
            'password_request'=>$password_request,
            'confirmation_request'=>$confirmation_request,
            'username'=>$username,
            ];

        $options = ['quickstat'=>false, 'body_classes'=>'account-issues'];

        return new StreamedViewResponse('Account Assistance', 'assistance.tpl', $parts, $options);
    }

    /**
     * Handle account email confirmation
     *
     * @return Response
     */
    public function confirm(Container $p_dependencies) {
        $request                   = RequestWrapper::$request;
        $admin_override_pass       = 'WeAllowIt'; // Just a weak passphrase for simply confirming players.
        $admin_override_request    = $request->get('admin_override');
        $acceptable_admin_override = ($admin_override_pass === $admin_override_request);
        $confirm                   = $request->get('confirm');
        $aid                       = Filter::toNonNegativeInt($request->get('aid'));

        $data = query_row('
            SELECT player_id, uname,
            accounts.verification_number as verification_number,
            CASE WHEN active = 1 THEN 1 ELSE 0 END AS active,
            accounts.active_email,
            CASE WHEN accounts.confirmed = 1 THEN 1 ELSE 0 END as confirmed,
            status, member, days, players.created_date
            FROM accounts JOIN account_players ON _account_id = account_id
            JOIN players ON _player_id = player_id
            WHERE account_id = :acctId', array(':acctId'=>$aid));

        if (count($data)) {
            $check     = $data['verification_number'];
            $confirmed = $data['confirmed'];
            $username  = $data['uname'];
        } else {
            $check     =
            $confirmed =
            $username  = null;
        }

        $confirmation_confirmed = false;

        if ($confirmed != 1 &&
            (($check && $confirm && $confirm == $check)
                || $acceptable_admin_override)) {
            // Confirmation number not null and matches
            // or the admin override was met.
            query('UPDATE accounts SET operational = true, confirmed=1
                WHERE account_id = :accountID', array(':accountID'=>$aid));

            $statement = DatabaseConnection::$pdo->prepare(
                'UPDATE players SET active = 1 WHERE player_id in
                (SELECT _player_id FROM account_players
                    WHERE _account_id = :accountID)');
            $statement->bindValue(':accountID', $aid);
            $statement->execute();  // todo - test for success

            $confirmation_confirmed = true;
        }

        $parts = [
            'confirmed'              => $confirmed,
            'username'               => $username,
            'confirmation_confirmed' => $confirmation_confirmed,
        ];

        $options = ['quickstat'=>false];

        return new StreamedViewResponse('Account Confirmation', 'assistance.confirm.tpl', $parts, $options);
    }
}
