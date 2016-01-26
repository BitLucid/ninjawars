<?php
namespace NinjaWars\core\control;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\PasswordResetRequest;
use \AccountFactory;
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

        // Email body contents will be: Click here to reset your password: {{ url('password/reset/'.$token) }}
        $url = WEB_ROOT.'resetpassword.php?command=reset&token='.url($token);
        $rendered = render_template('email.password_reset_request.tpl', ['url'=>$url]);

        // Construct the email with Nmail, and then just send it.
        $subject = 'NinjaWars: Your password reset request';
        $nmail = new Nmail($email, $subject, $rendered, SUPPORT_EMAIL);

        return (bool) $nmail->send();
    }

    /**
     * Display the form to request a password reset link
     *
     * @param Request $request
     * @return array
     * @TODO: Generate a csrf
     */
    public function index(Request $request) {
        $error      = $request->query->get('error');
        $message    = $request->query->get('message');
        $email      = $request->query->get('email');
        $ninja_name = $request->query->get('ninja_name');

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
     * @param Request $request
     * @return RedirectResponse
     * @TODO: Authenticate the csrf, which must match, from the session.
     */
    public function postEmail(Request $request) {
        $error      = null;
        $message    = null;
        $account = null;
        $email      = $request->get('email');
        $ninja_name = $request->get('ninja_name');


        if (!$email && !$ninja_name) {
            $error = 'You must specify either an email or a ninja name!';
        } else {
            if ($email) {
                $account = AccountFactory::findByEmail($email);
            }

            if (!isset($account)) {
                $account = AccountFactory::findByNinjaName($ninja_name);
            }

            if ($account === null || !$account->id()) {
                $error = 'Sorry, unable to find a matching account!';
            } else {
                // PWR created with default nonce
                $request = PasswordResetRequest::generate($account);

                if (empty($request)) {
                    throw new \RuntimeException('Password reset not created properly');
                }

                if ($this->sendEmail($request->nonce, $account)) {
                    $message = 'Your reset email was sent!';
                } else {
                    $error = 'Sorry, there was a problem sending to your account!  Please contact support.';
                }
            }
        }

        return new RedirectResponse('/resetpassword.php?'
            .($message? 'message='.url($message).'&' : '')
            .($error? 'error='.url($error) : '')
        );
    }

    /**
     * Obtain token, get matching request
     *
     * @param Request $request
     * @return array|RedirectResponse
     * @todo Need a way to set the max age on the response that the form will display
     */
    public function getReset(Request $request) {
        $token = $request->query->get('token');
        $req   = ($token ? PasswordResetRequest::match($token) : null);
        $error = null;

        if (!$req) {
            $error = 'No match for your password reset found or time expired, please request again.';
            return new RedirectResponse('/resetpassword.php?'.($error? 'error='.url($error) : ''));
        } else {
            $account = $req->account();

            if (!$account || !$account->getActiveEmail()) {
                throw new Exception('No account found for password reset request');
            }

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
     * @param Request $request
     * @return RedirectResponse
     */
    public function postReset(Request $request) {
        $token                = $request->request->get('token', null);
        $newPassword          = $request->request->get('new_password');
        $passwordConfirmation = $request->request->get('password_confirmation');

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
                    return new RedirectResponse('/resetpassword.php?message='.url('Password reset!'));                    
                }
            }
        }
    }

    private function renderError($p_error, $p_token) {
        return new RedirectResponse('/resetpassword.php?token='.url($p_token).'&error='.url($p_error));
    }
}
