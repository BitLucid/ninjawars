<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Account;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;
use \Constants;
use \Nmail;

/**
 * Implements user actions for creating an account
 *
 * @note
 * Emailing of confirmation email is still not stable. Consider removing all
 * email confirmation requirements and creating a one-button ninja creation
 * system.
 */
class SignupController {
    const ALIVE    = false;
    const PRIV     = false;
    const TEMPLATE = 'signup.tpl';
    const TITLE    = 'Become a Ninja';

    private $classes;

    public function __construct() {
        $this->classes = $this->class_choices();
    }

    /**
     * The default action gets a simple view
     *
     * @return ViewSpec
     */
    public function index() {
        Request::setTrustedProxies(Constants::$trusted_proxies);
        RequestWrapper::init();
        $request = RequestWrapper::$request;
        $signupRequest = $this->buildSignupRequest($request);

        return [
            'template' => self::TEMPLATE,
            'title'    => self::TITLE,
            'parts'    => [
                'submit_successful' => false,
                'submitted'         => false,
                'error'             => false,
                'classes'           => $this->classes,
                'signupRequest'     => $signupRequest,
            ],
            'options'  => ['quickstat' => false],
        ];
    }

    /**
     * The signup action processes a signup request and creates new accounts
     *
     * @return ViewSpec
     */
    public function signup() {
        Request::setTrustedProxies(Constants::$trusted_proxies);
        $request = Request::createFromGlobals();
        $signupRequest = $this->buildSignupRequest($request);

        try {
            $this->validateSignupRequest($signupRequest); // guard method
            $viewSpec = $this->doWork($signupRequest);
        } catch (\RuntimeException $e) {
            $viewSpec = $this->renderException($e, $signupRequest);
        }

        return $viewSpec;
    }

    /**
     * Implementation of the signup logic.
     *
     * @param SignupRequest $p_request
     * @return ViewSpec
     * @throw \Runtimeexception account creation failed
     */
    private function doWork($p_request) {
        $preconfirm = self::preconfirm_some_emails($p_request->enteredEmail);
        $confirm = rand(1000, 9999); //generate confirmation code

        $player_params = [
            'send_email'  => $p_request->enteredEmail,
            'send_pass'   => $p_request->enteredPass,
            'send_class'  => $p_request->enteredClass,
            'preconfirm'  => $preconfirm,
            'confirm'     => $confirm,
            'referred_by' => $p_request->enteredReferral,
            'ip'          => $p_request->clientIP,
        ];

        // Create the player
        $account_id = create_account_and_ninja($p_request->enteredName, $player_params);

        if ($account_id) {
            $sent = $this->sendSignupEmail($account_id, $p_request->enteredEmail, $p_request->enteredName, $confirm, $p_request->enteredClass);

            if (!$sent && !DEBUG) {
                throw new \RuntimeException('There was a problem sending your signup to that email address.', 4);
            }
        } else {
            throw new \RuntimeException('No account_id came back from creation', 4);
        }

        if ($preconfirm) {
            $completedPhase = 4;
            confirm_player($p_request->enteredName, false, true); // name, no confirm #, just autoconfirm.
            $confirmed = true;
        } else {
            $completedPhase = 5;
            $confirmed = false;
        }

        return [
            'template' => self::TEMPLATE,
            'title'    => self::TITLE,
            'parts'    => [
                'classes'           => $this->classes,
                'class_display'     => class_display_name_from_identity($p_request->enteredClass),
                'signupRequest'     => $p_request,
                'submit_successful' => true,
                'completedPhase'    => $completedPhase,
                'confirmed'         => $confirmed,
                'submitted'         => true,
                'error'             => '',
            ],
            'options'  => ['quickstat' => false],
        ];
    }

    /**
     * Guard method, validates a signup request and throws on any failure
     *
     * @param SignupRequest $p_request
     * @return boolean
     * @throws \RuntimeException SignUp request invalid
     */
    private function validateSignupRequest($p_request) {
        if ($p_request->enteredPass !== $p_request->enteredCPass) {
            throw new \RuntimeException('Your password entries did not match. Please try again.', 0);
        }

        if (!$this->validate_signup_phase0($p_request)) {
            throw new \RuntimeException('Phase 1 Incomplete: You did not correctly fill out all the necessary information.', 0);
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
     * @param Symfony\Component\HttpFoundation\Request $p_request
     * @return SignupRequest
     */
    private function buildSignupRequest($p_request) {
        $signupRequest                    = new \stdClass();
        $signupRequest->enteredName       = trim(in('send_name', '', 'toText'));
        $signupRequest->enteredEmail      = trim(in('send_email', '', 'toText'));
        $signupRequest->enteredClass      = strtolower(trim(in('send_class', '')));
        $signupRequest->enteredReferral   = trim(in('referred_by', in('referrer')));
        $signupRequest->enteredPass       = in('key', null, 'toText');
        $signupRequest->enteredCPass      = in('cpass', null, 'toText');
        $signupRequest->clientIP          = $p_request->getClientIp();

        if (!$signupRequest->enteredClass) {
            $signupRequest->enteredClass = key($this->classes);
        }

        return $signupRequest;
    }

    /**
     * Creates a viewspec from an exception
     *
     * @param \Exception $p_exception
     * @param SignupRequest $p_request
     * @return ViewSpec
     */
    private function renderException(\Exception $p_exception, $p_request) {
        return [
            'template' => self::TEMPLATE,
            'title'    => self::TITLE,
            'parts'    => [
                'classes'           => $this->class_choices(),
                'submitted'         => true,
                'error'             => $p_exception->getMessage(),
                'completedPhase'    => $p_exception->getCode(),
                'submit_successful' => false,
                'class_display'     => '',
                'signupRequest'     => $p_request,
            ],
            'options'  => ['quickstat' => false],
        ];
    }

    /**
     * Pull the class choices.
     *
     * @todo Move this to a model
     * @return String[][] An array of class attributes indexed by class key
     */
    private function class_choices() {
        $activeClasses = query_array('SELECT identity, class_name, class_note AS expertise FROM class WHERE class_active');
        $classes = array();

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
    private function validate_signup_phase0($p_request) {
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
    private function validate_signup_phase3($enteredName, $enteredEmail) {
        $name_available  = $this->ninjaNameAvailable($enteredName);
        $email_error     = $this->validateEmail($enteredEmail);

        if ($email_error) {
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
    private function validate_signup_phase4($enteredClass) {
        return (boolean)query_item('SELECT identity FROM class WHERE class_active AND identity = :id', array(':id'=>$enteredClass));
    }

    /**
     * Return 1 if the email is a blacklisted email, 0 otherwise.
     */
    public static function preconfirm_some_emails($email) {
        // Made the default be to auto-confirm players.
        $res = 1;
        $blacklisted_by = self::getBlacklistedEmails();
        $whitelisted_by = self::getWhitelistedEmails();

        // Blacklist only exists because emails beyond the first might not get through if we don't confirm.
        foreach ($blacklisted_by AS $loop_domain) {
            if (strpos(strtolower($email), $loop_domain) !== false) {
                return 0;
            }
        }

        foreach ($whitelisted_by AS $loop_domain) {
            if (strpos(strtolower($email), $loop_domain) !== false) {
                return 1;
            }
        }

        return $res;
    }

    /**
     * Check that the password format fits.
     *
     * @return String|null
     */
    private function validate_password($password_to_hash) {
        $error = null;

        if (strlen($password_to_hash) < 3 || strlen($password_to_hash) > 500) {	// *** Why is there a max length to passwords? ***
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
    private function validate_username($send_name) {
        $error = null;
        $format_error = username_format_validate($send_name);

        if ($format_error) {
            $error = 'Phase 1 Incomplete: Ninja name: '.$error;
        }

        return $error;
    }

    /**
     * Gives whitelisted emails
     *
     * @todo move to a model
     * @return String[]
     */
    public static function getWhitelistedEmails() {
        return ['@gmail.com'];
    }

    /**
     * Gives the blacklisted emails
     *
     * @todo move to a model
     * @return String[]
     */
    public static function getBlacklistedEmails() {
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

    private function validateEmail($email) {
        $error = null;
        if (!Account::emailIsValid($email)) {
            $error = 'Phase 3 Incomplete: The email address ('
                .htmlentities($email).') must not contain spaces and must contain an @ symbol and a domain name to be valid.';
        } elseif (Account::findByEmail($email) !== null) {
            $error = 'Phase 3 Incomplete: There is already an account using that email.  If that account is yours, you can request a password reset to gain access again.';
        }

        return $error;
    }

    /**
     * Check for reserved or already in use by another player.
     */
    private function ninjaNameAvailable($ninja_name) {
        $reserved = array('SysMsg', 'NewUserList', 'Admin', 'Administrator', 'A Stealthed Ninja', 'Tchalvak', 'Beagle');

        foreach ($reserved as $l_names) {
            if (strtolower($ninja_name) == strtolower($l_names)) {
                return false;
            }
        }

        return (!get_user_id($ninja_name));
    }

    /**
     * Sends out the confirmation email to the chosen email address.
     */
    private function sendSignupEmail($account_id, $signup_email, $signup_name, $confirm, $class_identity) {
        $class_display = class_display_name_from_identity($class_identity);
        $_to           = [$signup_email => $signup_name];
        $_subject      = 'NinjaWars Account Sign Up';
        $_body         = render_template(
            'signup_email_body.tpl',
            [
                'send_name'     => $signup_name,
                'signup_email'  => $signup_email,
                'confirm'       => $confirm,
                'send_class'    => $class_display,
                'SUPPORT_EMAIL' => SUPPORT_EMAIL,
                'account_id'    => $account_id,
            ]
        );

        $_from = [SYSTEM_EMAIL => SYSTEM_EMAIL_NAME];

        // *** Create message object. ***
        $message = new Nmail($_to, $_subject, $_body, $_from);

        // *** Set replyto address. ***
        $message->setReplyTo([SUPPORT_EMAIL => SUPPORT_EMAIL_NAME]);

        return $message->send();
    }
}
