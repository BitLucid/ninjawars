<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\Filter;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\NWTemplate;
use NinjaWars\core\extensions\StreamedViewResponse;
use Symfony\Component\HttpFoundation\Request;
// use ReCaptcha\ReCaptcha; // Fully referenced down below?
use Nmail;

/**
 * Implements user actions for creating an account
 *
 * @note
 * Emailing of confirmation email is still not stable. Consider removing all
 * email confirmation requirements and creating a one-button ninja creation
 * system.
 */
class SignupController extends AbstractController
{
    public const ALIVE    = false;
    public const PRIV     = false;
    public const TEMPLATE = 'signup.tpl';
    public const TITLE    = 'New Game';

    private $classes;

    public function __construct()
    {
        $this->classes = $this->class_choices();
    }

    /**
     * The default action gets a simple view
     *
     * @return Response
     */
    public function index(Container $p_dependencies)
    {
        RequestWrapper::init();
        $request = RequestWrapper::$request;
        $signupRequest = $this->buildSignupRequest($request);
        if (!RECAPTCHA_SITE_KEY) {
            throw new \RuntimeException('ERROR: 8324: Recaptcha site key not set, please set it in resources.template.php', 0);
        }

        $parts = [
            'submit_successful' => false,
            'submitted'         => false,
            'error'             => false,
            'classes'           => $this->classes,
            'signupRequest'     => $signupRequest,
        ];

        $options = ['quickstat' => false, 'body_classes' => 'signup-page'];

        return new StreamedViewResponse(self::TITLE, self::TEMPLATE, $parts, $options);
    }

    /**
     * The signup action processes a signup request and creates new accounts
     *
     * @return Response
     */
    public function signup(Container $p_dependencies)
    {
        $request = RequestWrapper::$request;
        $signupRequest = $this->buildSignupRequest($request);

        try {
            $this->validateSignupRequest($signupRequest); // guard method
            // Recaptcha section

            $gRecaptchaResponse = $request->get('token-reponse');
            $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
            $resp = $recaptcha
                // ->setExpectedHostname('www.ninjawars.net')
                // Above is needed if "domain/package name validation" disabled at
                // https://www.google.com/recaptcha/admin/site/352364760
                ->verify($gRecaptchaResponse, $request->getClientIp());
            error_log('Signup form client had a Recaptcha response: ' . print_r($gRecaptchaResponse, true) . print_r($resp, true));
            // compare a random number against the recaptcha quotient to
            // see if recaptcha even gets used
            $divisor = defined('RECAPTCHA_DIVISOR') ? RECAPTCHA_DIVISOR : 1;
            if ($resp->isSuccess() !== true && ($divisor === 1 || rand(1, $divisor) === 1)) {
                error_log('Signup form client had a Recaptcha failure: ' . print_r($resp->getErrorCodes(), true));
                throw new \RuntimeException('There was a problem with the submission, please wait 10 seconds and try again.', 0);
            }
            // Recaptcha always gets run, but the validation isn't always utilized,
            // mainly it's ignored mostly in local development environments
            $response = $this->doWork($signupRequest);
        } catch (\RuntimeException $e) {
            $response = $this->renderException($e, $signupRequest);
        }

        return $response;
    }

    /**
     * Implementation of the signup logic after validation
     * Creates the account and ninja, sends the confirmation email
     * for example, so only should be done after the initial request
     * is fully vetted as valid.
     *
     * @param SignupRequest $p_request
     * @return Response
     * @throw \Runtimeexception account creation failed
     */
    private function doWork($p_request)
    {
        $preconfirm = self::preconfirm_some_emails($p_request->enteredEmail);
        $confirm = rand(1000, 9999); //generate confirmation code

        $player_params = [
            'send_name'   => $p_request->enteredName,
            'send_email'  => $p_request->enteredEmail,
            'send_pass'   => $p_request->enteredPass,
            'send_class'  => $p_request->enteredClass,
            'preconfirm'  => $preconfirm,
            'verification_number'     => $confirm,
            'referred_by' => $p_request->enteredReferral,
            'ip'          => $p_request->clientIP,
        ];

        // Create the player
        $account_id = $this->createAccountAndNinja($player_params);

        if ($account_id) {
            if (!$preconfirm && (defined('SIGNUP_EMAIL_SENDOUT_DIVISOR') && (rand(1, SIGNUP_EMAIL_SENDOUT_DIVISOR) === 1))) {
                // Send the email (if it's not preconfirmed, it's sent in the confirmation step
                $sent = $this->sendSignupEmail($account_id, $p_request->enteredEmail, $p_request->enteredName, $confirm, $p_request->enteredClass);

                if (!$sent && defined('DEBUG') && !DEBUG) {
                    throw new \RuntimeException('There was a problem sending your signup to that email address.', 4);
                }
            } else {
                error_log('Signup email not sent, preconfirm: ' . $preconfirm . ' and SIGNUP_EMAIL_SENDOUT_DIVISOR: ' . SIGNUP_EMAIL_SENDOUT_DIVISOR);
            }
        } else {
            throw new \RuntimeException('No account_id came back from creation', 4);
        }

        if ($preconfirm) { // Some emails get auto-confirmed.
            $completedPhase = 4;

            $account = Account::findById($account_id);
            $account->setConfirmed(true);
            $account->setOperational(true);
            $account->save();

            $player = Player::findByName($p_request->enteredName);
            $player->active = 1;
            $player->save();

            $confirmed = true;
        } else {
            $completedPhase = 5;
            $confirmed = false;
        }

        $parts = [
            'classes'           => $this->classes,
            'class_display'     => $this->classDisplayNameFromIdentity($p_request->enteredClass),
            'signupRequest'     => $p_request,
            'submit_successful' => true,
            'completedPhase'    => $completedPhase,
            'confirmed'         => $confirmed,
            'submitted'         => true,
            'error'             => '',
        ];

        $options = ['quickstat' => false, 'body_classes' => 'signup-page'];

        return new StreamedViewResponse(self::TITLE, self::TEMPLATE, $parts, $options);
    }

    /**
     * Guard method, validates a signup request and throws on any failure
     *
     * @param SignupRequest $p_request
     * @return boolean
     * @throws \RuntimeException SignUp request invalid
     */
    private function validateSignupRequest($p_request)
    {
        if ($p_request->enteredPass !== $p_request->enteredCPass) {
            throw new \RuntimeException('Your password entries did not match. Please try again.', 0);
        }

        if (!$this->validate_signup_phase0($p_request)) {
            throw new \RuntimeException('Phase 1 Incomplete: You did not correctly fill out all the necessary information.', 0);
        }

        if ($p_request->enteredPass !== Filter::toSimple($p_request->enteredPass)) {
            throw new \RuntimeException("Sorry, there seem to be some very special non-standard characters in your password that we don't allow.");
        }

        if ($error = $this->validate_username($p_request->enteredName)) {
            throw new \RuntimeException($error, 0);
        }

        if ($error = $this->validate_password($p_request->enteredPass)) {
            throw new \RuntimeException($error, 1);
        }

        if ($error = $this->validate_signup_phase3($p_request->enteredName, $p_request->enteredEmail)) {
            throw new \RuntimeException($error, 2);
        }

        if (!$this->validate_signup_phase4($p_request->enteredClass)) {
            throw new \RuntimeException('Phase 4 Incomplete: No proper class was specified.', 3);
        }

        return true;
    }

    /**
     * Takes an HTTP Request and creates a normalized request object for signup
     *
     * @param Request $p_request
     * @return SignupRequest
     */
    private function buildSignupRequest($p_request)
    {
        $signupRequest                    = new \stdClass();
        $signupRequest->enteredName       = Filter::toSimple(trim($p_request->get('send_name') ?? ''));
        $signupRequest->enteredEmail      = Filter::toSimple(trim($p_request->get('send_email') ?? ''));
        $signupRequest->enteredClass      = Filter::toSimple(strtolower(trim($p_request->get('send_class') ?? '')));
        $signupRequest->enteredReferral   = trim($p_request->get('referred_by', $p_request->get('referrer')) ?? '');
        $signupRequest->enteredPass       = Filter::toSimple($p_request->get('key') ?? '');
        $signupRequest->enteredCPass      = Filter::toSimple($p_request->get('cpass') ?? '');
        $signupRequest->clientIP          = $p_request->getClientIp();

        return $signupRequest;
    }

    /**
     * Creates a Response from an exception
     *
     * @param \Exception    $p_exception
     * @param SignupRequest $p_request
     * @return Response
     */
    private function renderException(\Exception $p_exception, $p_request)
    {
        $parts = [
            'classes'           => $this->class_choices(),
            'submitted'         => true,
            'error'             => $p_exception->getMessage(),
            'completedPhase'    => $p_exception->getCode(),
            'submit_successful' => false,
            'class_display'     => $this->classDisplayNameFromIdentity(strtolower($p_request->enteredClass)),
            'signupRequest'     => $p_request,
        ];

        $options = ['quickstat' => false, 'body_classes' => 'signup-page'];

        return new StreamedViewResponse(self::TITLE, self::TEMPLATE, $parts, $options);
    }

    /**
     * Pull the class choices.
     *
     * @todo Move this to a model
     * @return String[][] An array of class attributes indexed by class key
     */
    private function class_choices(): array
    {
        $activeClasses = query_array('SELECT 
            identity, class_name, class_note AS expertise
             FROM class WHERE class_active'); // Filters out classes like mantis perhaps
        $classes = [];

        foreach ($activeClasses as $loopClass) {
            $classes[$loopClass['identity']] = [
                'name'      => $loopClass['class_name'],
                'expertise' => $loopClass['expertise'],
            ];
        }

        return $classes;
    }

    /**
     * Checks that required variables are set
     *
     * @param SignupRequest $p_request
     * @return boolean
     */
    private function validate_signup_phase0($p_request)
    {
        return (
            $p_request->enteredName &&
            $p_request->enteredPass &&
            $p_request->enteredEmail &&
            $p_request->enteredClass
        );
    }

    /**
     * Checks email validity and that the email and username are not taken
     *
     * @param String $enteredName
     * @param String $enteredEmail
     * @return String|null
     */
    private function validate_signup_phase3($enteredName, $enteredEmail)
    {
        $name_available  = $this->ninjaNameAvailable($enteredName);
        $email_error     = $this->validateEmail($enteredEmail);

        if ($email_error !== null && $email_error !== false) {
            return $email_error;
        } elseif (!$name_available) {
            return 'Phase 3 Incomplete: That ninja name is already in use.';
        } else {
            return null;
        }
    }

    /**
     * Validates that the class selected is a legal class
     *
     * @param String $enteredClass
     * @return boolean
     */
    private function validate_signup_phase4($enteredClass)
    {
        return (bool) $this->classDisplayNameFromIdentity($enteredClass);
    }

    /**
     * Return 1 if the email is a blacklisted email, 0 otherwise.
     */
    public static function preconfirm_some_emails($email)
    {
        // Made the default be to auto-confirm players.
        $blacklisted_by = self::getBlacklistedEmails();
        $whitelisted_by = self::getWhitelistedEmails();

        // Blacklist only exists because emails beyond the first might not get through if we don't confirm.
        foreach ($blacklisted_by as $loop_domain) {
            if (strpos(strtolower($email), $loop_domain) !== false) {
                return 0;
            }
        }

        foreach ($whitelisted_by as $loop_domain) {
            if (strpos(strtolower($email), $loop_domain) !== false) {
                return 1;
            }
        }

        return 1;
    }

    /**
     * Check that the password format fits.
     *
     * @return String|null
     */
    private function validate_password($password_to_hash)
    {
        $error = null;

        if (strlen($password_to_hash) < 3 || strlen($password_to_hash) > 500) {    // *** Why is there a max length to passwords? ***
            $error = 'Phase 2 Incomplete: Passwords must be at least 3 characters long.';
        }

        return $error;
    }

    /**
     * Return the reasons that a username isn't acceptable.
     *
     * @param String $send_name
     * @return String|null
     */
    private function validate_username($send_name)
    {
        $error = null;
        $format_error = Account::usernameIsValid($send_name);

        if ($format_error !== null && $format_error !== false) {
            $error = 'Phase 1 Incomplete: Ninja name: ' . $error;
        }

        return $error;
    }

    /**
     * Gives whitelisted emails
     *
     * @todo move to a model
     * @return String[]
     */
    public static function getWhitelistedEmails()
    {
        return ['@gmail.com'];
    }

    /**
     * Gives the blacklisted emails
     *
     * @todo move to a model
     * @return String[]
     */
    public static function getBlacklistedEmails()
    {
        return [
            '@hotmail.com',
            '@hotmail.co.uk',
            '@msn.com',
            '@live.com',
            '@aol.com',
            '@aim.com',
            '@yahoo.com',
            '@yahoo.co.uk'
        ];
    }

    private function validateEmail($email)
    {
        $error = null;
        if (!Account::emailIsValid($email)) {
            $error = 'Phase 3 Incomplete: The email address ('
                . htmlentities($email) . ') must be valid, including no spaces, have an @ symbol and domain name.';
        } elseif (Account::findByEmail($email) !== null) {
            $error = 'Phase 3 Incomplete: There is already an account using that email.  If that account is yours, you can request a password reset to gain access again.';
        }

        return $error;
    }

    /**
     * Check for reserved or already in use by another player.
     */
    private function ninjaNameAvailable($ninja_name)
    {
        $reserved = [
            'SysMsg',
            'NewUserList',
            'Admin',
            'Administrator',
            'A Stealthed Ninja',
            'Tchalvak',
            'Beagle',
        ];

        foreach ($reserved as $l_names) {
            if (strtolower($ninja_name) == strtolower($l_names)) {
                return false;
            }
        }

        return (!Player::findByName($ninja_name));
    }

    /**
     * Sends out the confirmation email to the chosen email address.
     */
    private function sendSignupEmail($account_id, $signup_email, $signup_name, $confirm, $class_identity)
    {
        $class_display = $this->classDisplayNameFromIdentity($class_identity);

        $template_vars = [
            'send_name'     => $signup_name,
            'signup_email'  => $signup_email,
            'confirm'       => $confirm,
            'send_class'    => $class_display,
            'SUPPORT_EMAIL' => SUPPORT_EMAIL,
            'account_id'    => $account_id,
        ];

        $_to           = [$signup_email => $signup_name];
        $_subject      = 'NinjaWars Account Sign Up';
        $_body         = (new NWTemplate())->assign($template_vars)->fetch('signup_email_body.tpl');

        $_from = [SYSTEM_EMAIL => SYSTEM_EMAIL_NAME];

        // *** Create message object. ***
        $message = new Nmail($_to, $_subject, $_body, $_from);

        // *** Set replyto address. ***
        $message->setReplyTo([SUPPORT_EMAIL => SUPPORT_EMAIL_NAME]);

        return $message->send();
    }

    /**
     * Create the account and the initial ninja for that account.
     */
    private function createAccountAndNinja($params = [])
    {
        $verification_number = (int) $params['verification_number'];
        $confirmed = (int) $params['preconfirm'];
        $ip      = (isset($params['ip']) ? $params['ip'] : null);

        $class_id = query_item(
            'SELECT class_id FROM class WHERE identity = :class_identity',
            [':class_identity' => $params['send_class']]
        );

        $ninja = new Player();
        $ninja->uname               = $params['send_name'];
        $ninja->verification_number = $verification_number;
        $ninja->active              = (int) $params['preconfirm'];
        $ninja->_class_id           = $class_id;
        $ninja->email               = $params['send_email'];
        $ninja->save();

        return Account::create(
            $ninja->id(), // Ninja id, so it's okay
            $params['send_email'],
            $params['send_pass'],
            $verification_number,
            $confirmed,
            0,
            1,
            $ip
        );
    }

    /**
     * Get the display name from the identity.
     */
    private function classDisplayNameFromIdentity(string $identity): string|null
    {
        $classes = $this->class_choices();
        if (!isset($classes[$identity])) {
            return null;
        } else {
            return $classes[$identity]['name'];
        }
    }
}
