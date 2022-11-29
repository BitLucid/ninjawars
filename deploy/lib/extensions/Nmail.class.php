<?php


use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;



/**
 * Wrapper class for nw to send out mail
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

    /**
     * Mechanism like ses, sendmail, etc configuration
     */
    public static $transport = null;


    /**
     * Constructor
     *
     * Just sets the fields to use for the mail() function, defaults null.
     * @param $from string or array of email-indexed from addresses
     * @access public
     */
    public function __construct($to = null, $subject = null, $body = null, $from = null)
    {
        $this->to      = $to;
        $this->subject = $subject;
        $this->body    = $body;
        $this->from    = $from;
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
    public function send()
    {
        // Create the Transport
        if (null === self::$transport) {
            self::$transport = Transport::fromDsn(MAILER_DSN);
        }


        $mailer = new Mailer(self::$transport);
        $email = (new Email())
            ->from($this->from)
            ->to($this->to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->subject)
            ->text($this->body)
            ->html('<p>' . $this->body . '</p>');


        if ($this->reply_to) {
            $email->setReplyTo($this->reply_to);
            $email->setSender($this->from); // Have to set sender when there's a different reply-to.
        }

        if (defined('DEBUG') && DEBUG) {
            error_log($this->body . PHP_EOL, 3, LOGS . "emails.log");
        }

        return (bool) $mailer->send($email);
    }
}
