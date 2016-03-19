<?php

namespace NinjaWars\core\control;

use \Nmail;

/**
 * Give assistance to players and pre-player-users
 */
class AssistanceController{
    const PRIV          = false;
    const ALIVE         = false;

        /**
         * Determines the user information for a certain email.
         */
        private function userHavingEmail($email) {
            $data = query_row('SELECT uname, level, accounts.confirmed, accounts.verification_number, accounts.active_email, account_id, CASE WHEN active::bool THEN 1 ELSE 0 END AS active
                from accounts LEFT JOIN account_players ON account_id = _account_id LEFT JOIN players on _player_id = player_id 
                WHERE active_email = :email limit 1;',
                    array(':email'=>$email));
            return $data;
        }

        /**
         * Sends an email for the user's account data.
         */
        private function sendAccountEmail($email, $data) {
            $_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);

            /* additional headers */
            $_to = array("$email"=>$data['uname']);
            $_subject = 'NinjaWars Account Info Request';
            $_body = render_template('email.assistance.account.tpl', [
                    'lost_uname'   => $data['uname'],
                    'active_email' => $data['active_email'],
                    'confirmed'  => $data['confirmed'],
                    'level' => $data['level']
                ]
            );

            $mail_obj = new Nmail($_to, $_subject, $_body, $_from);

            // *** Set the custom replyto email. ***
            $mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));

            return $mail_obj->send();
        }

        /**
         * Sends the account confirmation email.
         */
        private function sendConfirmationEmail($email, $data) {
            $lost_confirm = $data['verification_number'];
            $lost_uname   = $data['uname'];

            $_from = array(SYSTEM_EMAIL=>SYSTEM_EMAIL_NAME);
            $_to = array("$email"=>$data['uname']);
            $_subject = "NinjaWars Account Confirmation Info";
            $_body = render_template('email.assistance.confirmation.tpl', array(
                    'lost_uname'     => $lost_uname
                    , 'lost_confirm' => $lost_confirm
                    , 'account_id'   => $data['account_id']
                )
            );

            $mail_obj = new Nmail($_to, $_subject, $_body, $_from);
            $mail_obj->setReplyTo(array(SUPPORT_EMAIL=>SUPPORT_EMAIL_NAME));

            return $mail_obj->send();
        }


    /**
     * Display the assistance options to users.
     */
    public function index(){
        $email = in('email', null, 'sanitize_to_email'); // The default filter allows standard emails.
        $password_request = in('password_request');
        $confirmation_request = in('confirmation_request');

        $error = null;
        $sent  = null;
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
                    $attemptedToSendEmail = true;
                    $sent = $this->sendConfirmationEmail($email, $data);
                }
            }
        }

        if ($attemptedToSendEmail && !$sent) {
            $error = 'emailfail';
        }

        $body_classes = 'account-issues';


        $parts = [
            'data'=>$data,
            'error'=>$error,
            'password_request'=>$password_request,
            'confirmation_request'=>$confirmation_request,
            'username'=>$username,
            ];
        return [
            'template'=>'account_issues.tpl',
            'title'=>'Account Problems',
            'parts'=>$parts,
            'options'=>['quickstat'=>false]
            ];
    }
}