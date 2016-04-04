<?php
namespace NinjaWars\core\control;

use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\PasswordResetRequest;
use NinjaWars\core\data\Account;
use \Nmail;

class PasswordController {
    const PRIV           = false;
    const ALIVE          = false;

    /**
     * Private functionality to send out the reset email.
     *
     * @return bool
     */
    private function sendEmail($token, $account) {
        $email = $account->getActiveEmail();

        if (!$email) {
            return false;
        }

        $url = WEB_ROOT.'password/reset/?token='.rawurlencode($token);
        $rendered = render_template('email.password_reset_request.tpl', ['url'=>$url]);

        // Construct the email with Nmail, and then just send it.
        $subject = 'NinjaWars: Your password reset request';
        $nmail = new Nmail($email, $subject, $rendered, SUPPORT_EMAIL);

        return (bool) $nmail->send();
    }

    /**
     * Display the form to request a password reset link
     *
     * @return array
     * @TODO: Generate a csrf
     */
    public function index() {
        $error      = RequestWrapper::get('error');
        $message    = RequestWrapper::get('message');
        $email      = RequestWrapper::get('email');
        $ninja_name = RequestWrapper::get('ninja_name');

        $parts = [
            'error'      => $error,
            'message'    => $message,
            'email'      => $email,
            'ninja_name' => $ninja_name,
        ];

        $response = [
            'title'    => 'Request a password reset',
            'template' => 'reset.password.request.tpl',
            'parts'    => $parts,
            'options'  => [],
        ];

        return $response;
    }

    /**
     * Send a reset link to a given user.
     *
     * @return RedirectResponse
     * @TODO: Authenticate the csrf, which must match, from the session.
     */
    public function postEmail() {
        $error      = null;
        $message    = null;
        $account = null;
        $email      = RequestWrapper::getPost('email');
        $ninja_name = RequestWrapper::getPost('ninja_name');


        if (!$email && !$ninja_name) {
            $error = 'You must specify either an email or a ninja name!';
        } else {
            if ($email) {
                $account = Account::findByEmail($email);
            }

            if (!isset($account)) {
                $account = Account::findByNinjaName($ninja_name);
            }

            if ($account === null || !$account->id()) {
                $error = 'Sorry, unable to find a matching account!';
            } else {
                // PWR created with default nonce
                $request = PasswordResetRequest::generate($account);

                if ($this->sendEmail($request->nonce, $account)) {
                    $message = 'Your reset email was sent!';
                } else {
                    $error = 'Sorry, there was a problem sending to your account!  Please contact support.';
                }
            }
        }

        return new RedirectResponse('/password/?'
            .($message? 'message='.rawurlencode($message).'&' : '')
            .($error? 'error='.rawurlencode($error) : '')
        );
    }

    /**
     * Obtain token, get matching request
     *
     * @return array|RedirectResponse
     * @todo Need a way to set the max age on the response that the form will display
     */
    public function getReset() {
        $token = RequestWrapper::get('token');
        $req   = ($token ? PasswordResetRequest::match($token) : null);
        $error = null;

        if (!$req) {
            $error = 'No match for your password reset found or time expired, please request again.';
            return new RedirectResponse('/password/?'.($error? 'error='.rawurlencode($error) : ''));
        } else {
            $account = $req->account();

            $parts = [
                'token'          => $token,
                'verified_email' => $account->getActiveEmail(),
                'error'          => $error,
            ];

            $response = [
                'title'    => 'Reset your password',
                'template' => 'reset.password.tpl',
                'parts'    => $parts,
                'options'  => [],

            ];

            return $response;
        }
    }

    /**
     * Reset the given user's password.
     *
     * @return RedirectResponse
     */
    public function postReset() {
        $token                = RequestWrapper::getPost('token', null);
        $newPassword          = RequestWrapper::getPost('new_password');
        $passwordConfirmation = RequestWrapper::getPost('password_confirmation');

        if ($passwordConfirmation === null || $passwordConfirmation !== $newPassword) {
            return $this->renderError('Password Confirmation did not match.', $token);
        }

        if (!$token) {
            return $this->renderError('No Valid Token to allow for password reset! Try again.', $token);
        } else {
            $req = PasswordResetRequest::match($token);
            $account = ($req instanceof PasswordResetRequest ? $req->account() : null);

            if(!$account || !$account->id()){
                return $this->renderError('Token was invalid or expired! Please reset again.', $token);
            } else {
                if (strlen(trim($newPassword)) < 4 || $newPassword !== $passwordConfirmation) {
                    return $this->renderError('Password not long enough or does not match password confirmation!', $token);
                } else {
                    PasswordResetRequest::reset($account, $newPassword);
                    return new RedirectResponse('/password/?message='.rawurlencode('Password reset!'));                    
                }
            }
        }
    }

    /**
     * @return RedirectResponse
     */
    private function renderError($p_error, $p_token) {
        return new RedirectResponse('/password/?token='.rawurlencode($p_token).'&error='.rawurlencode($p_error));
    }
}
