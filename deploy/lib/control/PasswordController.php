<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\NWTemplate;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\data\PasswordResetRequest;
use NinjaWars\core\data\Account;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Nmail;

class PasswordController extends AbstractController {
    const PRIV  = false;
    const ALIVE = false;

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
        $rendered = (new NWTemplate())->assign(['url'=>$url])->fetch('email.password_reset_request.tpl');

        // Construct the email with Nmail, and then just send it.
        $subject = 'NinjaWars: Your password reset request';
        $nmail = new Nmail($email, $subject, $rendered, SUPPORT_EMAIL);

        return (bool) $nmail->send();
    }

    /**
     * Display the form to request a password reset link
     *
     * @return Response
     * @TODO: Generate a csrf
     */
    public function index(Container $p_dependencies) {
        $request    = RequestWrapper::$request;
        $error      = $request->get('error');
        $message    = $request->get('message');
        $email      = $request->get('email');
        $ninja_name = $request->get('ninja_name');

        $parts = [
            'error'      => $error,
            'message'    => $message,
            'email'      => $email,
            'ninja_name' => $ninja_name,
        ];

        return new StreamedViewResponse('Request a password reset', 'reset.password.request.tpl', $parts);
    }

    /**
     * Send a reset link to a given user.
     *
     * @return Response
     * @TODO: Authenticate the csrf, which must match, from the session.
     */
    public function postEmail(Container $p_dependencies) {
        $request    = RequestWrapper::$request;
        $error      = null;
        $message    = null;
        $account    = null;
        $email      = $request->get('email');
        $ninja_name = $request->get('ninja_name');


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
                $p_request = PasswordResetRequest::generate($account);

                if ($this->sendEmail($p_request->nonce, $account)) {
                    $message = 'Your reset email was sent!';
                } else {
                    $error = 'Sorry, there was a problem sending to your account!  Please contact support.';
                }
            }
        }

        return new RedirectResponse('/password/?'
            .($message? 'message='.rawurlencode($message).'&' : '')
            .($error? 'error='.rawurlencode($error) : ''));
    }

    /**
     * Obtain token, get matching request
     *
     * @return Response
     * @todo Need a way to set the max age on the response that the form will display
     */
    public function getReset(Container $p_dependencies) {
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

            return new StreamedViewResponse('Reset your password', 'reset.password.tpl', $parts);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @return Response
     */
    public function postReset(Container $p_dependencies) {
        $request              = RequestWrapper::$request;
        $token                = $request->get('token');
        $newPassword          = $request->get('new_password');
        $passwordConfirmation = $request->get('password_confirmation');

        if ($passwordConfirmation === null || $passwordConfirmation !== $newPassword) {
            return $this->renderError('Password Confirmation did not match.', $token);
        }

        if (!$token) {
            return $this->renderError('No Valid Token to allow for password reset! Try again.', $token);
        } else {
            $req = PasswordResetRequest::match($token);
            $account = ($req instanceof PasswordResetRequest ? $req->account() : null);

            if (!$account || !$account->id()) {
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
