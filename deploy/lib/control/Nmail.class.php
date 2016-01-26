<?php
/**
 * Wrapper class for nw to send out mail, currently wraps the swiftmail mail library.
 *
 * @category Mail
 * @subpackage mail
 */
class Nmail {
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
    public function __construct($to=null, $subject=null, $body=null, $from=null) {
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
    public function replace($to=null, $subject=null, $body=null, $from=null) {
        $this->__construct($to, $subject, $body, $from);
        // *** Replace the current Nmail parameters with a new mailing.
    }

    /**
     * Run checks to make sure that the mail is ready to be sent out.
     * @return boolean
     */
    public function valid() {
        return !($this->to == null || $this->subject == null || $this->body == null || $this->from == null);
    }

    /**
     * Direct mapping to allow the setting of the reply to address.
     */
    public function setReplyTo($email_or_array) {
        $this->reply_to = $email_or_array;
    }

    /**
     * Sends the mail out using the php mail() function.
     *
     * @return boolean whether the mail function accepted the inputs.
     */
    public function send() {
        // Create the Transport
        if (!(self::$transport instanceof Swift_Transport)) {
            self::$transport = Swift_MailTransport::newInstance();
        }

        $mailer = Swift_Mailer::newInstance(self::$transport);

        $this->message = Swift_Message::newInstance()

            //Give the message a subject
            ->setSubject($this->subject)

            //Set the From address with an associative array
            ->setFrom($this->from)

            //Set the To addresses with an associative array
            ->setTo($this->to)

            //Give it a body
            ->setBody($this->body)

            //And optionally an alternative/html body
            ->addPart('<p>'.$this->body.'</p>', 'text/html')

            //Optionally add any attachments
            //  ->attach(Swift_Attachment::fromPath('my-document.pdf'))
            ;

        if ($this->reply_to) {
            $this->message->setReplyTo($this->reply_to);
            $this->message->setSender($this->from); // Have to set sender when there's a different reply-to.
        }

        if (defined('DEBUG') && DEBUG) {
            error_log($this->message.PHP_EOL, 3, LOGS."emails.log");
        }

        // Send the message along.
        return (bool) $mailer->send($this->message);
    }
}
