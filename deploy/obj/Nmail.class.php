<?php
/**
 * Wrapper class around the mail function, allows dumping/debugging of mail,
 * should eventually just wrap PEAR's Mail class.
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
	public $to;

	public $subject;

	public $body;

    /**
    * The From header is used as the first part of the "additional headers".
    * @var string
    */
	public $from;

    /**
    * Additional headers that would be tacked on the end of the from header.
    * @var string
    */
	public $cc_bcc_etc_headers;

	/**
    * Boolean result of whether the mail() function accepted the send input.
    * @var boolean
    */
	public $success;

	/**
	 * During debugging, can be turned on to dump the mail contents instead of
	 * trying to send them,
	 * use if(DEBUG) {$Zmail->dump = true; } //to dump email.
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

   /**#@-*/

	/**
    * Constructor
    *
    * Just sets the fields to use for the mail() function, defaults null.
    * @param $from string  The header using From: <address>
    * @param $cc_bcc_etc_headers string Will append to the from header.
    * @access public
	**/
	function Nmail($to=null, $subject=null, $body=null, $from=null, $cc_bcc_etc_headers=null) {
		$this->to = $to;
		$this->subject = $subject;
		$this->body = $body;
		$this->from = $from;
		$this->cc_bcc_etc_headers = $cc_bcc_etc_headers;
	}

	/**
	 * Replace the mail settings with completely new ones, reusing the constructor.
	 * @return void
	 **/
	function replace($to=null, $subject=null, $body=null, $from=null, $cc_bcc_etc_headers=null) {
		$this->Zmail($to, $subject, $body, $from, $cc_bcc_etc_headers);
		// *** Just re-call the constructor.
	}

	/**
	 * Run checks to make sure that the mail is ready to be sent out.
	 * @return boolean
	 */
	function valid() {
		if ($this->to == null || $this->subject == null || $this->body == null
			|| $this->from == null) {
			return false;
		} else {
			return true;
		}
	}

	// *** TODO: Add a addToAddresses() function that takes in an array
	// *** of addresses and Names to create formal emails.

	// *** TODO: get functions to get the email parts.



	/**
	 * Sends the mail out using the php mail() function.
	 * @return boolean whether the mail function accepted the inputs.
	 **/
	function send() {
		$this->success = null;
		if ($this->try_to_send) {
			$this->success = mail($this->to, $this->subject, $this->body, $this->from.$this->cc_bcc_etc_headers);
		}
		if($this->dump) {
			// *** TODO: Eventually make this create a javascript popup so
			// *** header Redirection works still.
			var_dump($this);
			print_r($this->body);
		}
		if ($this->die_after_dump) {
			die();
		}
		return $this->success;
	}
}

?>
