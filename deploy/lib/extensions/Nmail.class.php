<?php

use function NinjaWars\core\events\generateEventbridgeClient as generateEventbridgeClient;
use function NinjaWars\core\events\sendCommandNWEmailRequest as sendCommandNWEmailRequest;

/**
 * Wrapper class for nw to send out mail, currently wraps the swiftmail mail library.
 *
 * @category Mail
 * @subpackage mail
 */
class Nmail
{
    /**
     * The to is a simple email address or an array of emails or a mixed array of email-indexed formal names and/or simple email values.
     * For example: 'bob@gmail.com' or array('bob@gmail.com')
     * or array('bob@gmail.com'=>'Robert') or array('bob@gmail.com'=>'Robert', 'william@gmail.com')
     * @var mixed
     */
    public $to;

    public $subject;

    public $body;

    public $reply_to;

    /**
     * The From is a simple email address or an email-indexed array of emails and formal names as with the to above.
     * @var mixed
     */
    public $from;

    public $message = null;  // Swiftmail sending mechanism.

    public static $transport;

    /**
     * Constructor
     *
     * Just sets the fields to use for the mail() function, defaults null.
     * @param $from string or array of email-indexed from addresses
     * @access public
     */
    public function __construct(array|string $to = null, string $subject = null, string $body = null, array|string $from = null, ?array $extras = null, $transport = null)
    {
        $this->to      = $to;
        $this->subject = $subject;
        $this->body    = $body;
        $this->from    = $from;
        // destructure extras to replyto
        list($this->reply_to) = $extras ?? [null];
        self::$transport  = $transport ?? self::$transport ?? generateEventbridgeClient([
            'region' => 'us-east-1',
            'version' => '2015-10-07'
        ]);
    }

    /**
     * Replace the mail settings with completely new ones, reusing the constructor.
     *
     * @return void
     */
    public function replace($to = null, $subject = null, $body = null, $from = null)
    {
        $this->__construct($to, $subject, $body, $from);
        // *** Replace the current Nmail parameters with a new mailing.
    }

    /**
     * Run checks to make sure that the mail is ready to be sent out.
     * @return boolean
     */
    public function valid()
    {
        return !($this->to == null || $this->subject == null || $this->body == null || $this->from == null);
    }

    /**
     * Direct mapping to allow the setting of the reply to address.
     */
    public function setReplyTo($email_or_array)
    {
        $this->reply_to = $email_or_array;
    }

    /**
     * Sends the mail out using the php mail() function.
     *
     * @return boolean whether the mail function accepted the inputs.
     */
    public function send($debug_override = false)
    {
        if (!$this->valid()) {
            return false;
        }
        $params = ([
            'from' => $this->from,
            'subject' => $this->subject,
            'text' => $this->body,
            'html' => '<div>' . $this->body . '</div>',
        ] + ($this->reply_to ? ['replyto' => $this->reply_to] : []));
        // Optionally add reply to only if it got set and defined
        $result = sendCommandNWEmailRequest(self::$transport, $this->to, $params);
        if ($debug_override || defined('DEBUG') && DEBUG) {
            error_log('Email sendout' . print_r($params, true) . PHP_EOL, 3, LOGS . "emails.log");
        }
        return $result !== false;
    }
}
