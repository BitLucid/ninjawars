<?php
namespace NinjaWars\core\control;

require_once(CORE.'/control/lib_player.php');

use Symfony\Component\HttpFoundation\Request;
use \Constants;

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
        $request = Request::createFromGlobals();
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
        if ($error = create_account_and_ninja($p_request->enteredName, $player_params)) {
            throw new \RuntimeException($error, 4);
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
        $name_available  = ninja_name_available($enteredName);
        $duplicate_email = email_is_duplicate($enteredEmail);
        $email_error     = validate_email($enteredEmail);

        if ($email_error) {
            return $email_error;
        } elseif (!$name_available) {
            return 'Phase 3 Incomplete: That ninja name is already in use.';
        } elseif ($duplicate_email) {
            return 'Phase 3 Incomplete: That account email is already in use. You can send a password reset request below if that email is your correct email.';
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
        $blacklisted_by = self::get_blacklisted_emails();
        $whitelisted_by = self::get_whitelisted_emails();

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
    public static function get_whitelisted_emails() {
        return ['@gmail.com'];
    }

    /**
     * Gives the blacklisted emails
     *
     * @todo move to a model
     * @return String[]
     */
    public static function get_blacklisted_emails() {
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
}
