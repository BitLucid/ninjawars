<?php

/**
 * Wrapper class around the swiftmail mail library.
 *
 * @category Mail
 * @package obj
 * @subpackage mail
 * @link  http://ninjawars.net/signup.php/
 */
class Nmail {

   /**#@+
    * @access public
    */
    
    
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
    * Boolean result of whether the mail() function accepted the send input.
    * @var boolean
    */
	public $success;

	/**
	 * During debugging, can be turned on to dump the mail contents instead of
	 * trying to send them,
	 * use if(DEBUG) {$Nmail->dump = true; } //to dump email.
	 * @var boolean
	 */
	public $dump = false;

	/**
	 * During debugging, can be turned on to die and stop after the mail dump.
	 * @var boolean
	 */
	public $die_after_dump = false;

	/**
	 * During debugging, can be turned to false to not try to send the email.
	 * @var boolean
	 */
	public $try_to_send = true;
	
	
	public $message = null;  // Swiftmail sending mechanism.

   /**#@-*/

	/**
    * Constructor
    *
    * Just sets the fields to use for the mail() function, defaults null.
    * @param $from string or array of email-indexed from addresses
    * @access public
	**/
	function __construct($to=null, $subject=null, $body=null, $from=null) {
		$this->to = $to;
		$this->subject = $subject;
		$this->body = $body;
		$this->from = $from;
	}

	/**
	 * Replace the mail settings with completely new ones, reusing the constructor.
	 * @return void
	 **/
	function replace($to=null, $subject=null, $body=null, $from=null) {
	    $this->__construct($to, $subject, $body, $from);
		// *** Replace the current Nmail parameters with a new mailing.
	}

	/**
	 * Run checks to make sure that the mail is ready to be sent out.
	 * @return boolean
	 */
	function valid() {
		return !($this->to == null || $this->subject == null || $this->body == null || $this->from == null);
	}
	
	// Direct mapping to allow the setting of the reply to address.
	function setReplyTo($email_or_array){
	    $this->reply_to = $email_or_array;
	}

	/**
	 * Sends the mail out using the php mail() function.
	 * @return boolean whether the mail function accepted the inputs.
	 **/
	function send() {
		$this->success = null;


        // Only send email on systems where an email system exists and sending attempts are actually requested.
		if ($this->try_to_send) {
		
            //Create the Transport
            /* SMTP Example for later ease of reference 
            $transport = Swift_SmtpTransport::newInstance('smtp.example.org', 25)
              ->setUsername('your username')
              ->setPassword('your password')
              ;*/
		
		    if(!isset($this->mailer) || !is_object($this->mailer)){
    		    $transport = Swift_MailTransport::newInstance();
    		    $this->mailer = Swift_Mailer::newInstance($transport);
    		}
		
            //Create the message using the mail() function!
            // This is a chained object syntax.
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
              
              if($this->reply_to){
                $this->message->setReplyTo($this->reply_to);
                $this->message->setSender($this->from); // Have to set sender when there's a different reply-to.
              }

			$this->success = $this->mailer->send($this->message);
			// Send the message along.
		}

        // When debugging, simply dump the full contents of this object instead of sending email.
		if ($this->dump) {
			// *** TODO: Eventually make this create a javascript popup so
			// *** header Redirection works still.
			print_r($this->body);
			var_dump($this);
		}
        
        // If the page would forward to another page, this will kill any further processing.
		if ($this->die_after_dump) {
			die();
		}

		return $this->success;
	}
}
