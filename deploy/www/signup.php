<?php
/*
 * This file is used for players signing up to ninjawars.
 *
 * @package account
 * @subpackage signup
 */

// Emailing of confirmation email is still not stable...
// Consider removing all email confirmation requirements...
// And creating a one-button ninja creation system.

$alive             = false;
$private           = false;
$quickstat         = false;
$page_title        = "Sign Up";
$starting_referral = in('referrer');
$enteredName       = in('send_name', '', 'toText');
$enteredEmail      = in('send_email', '', 'toText');
$enteredClass      = in('send_class', '');
$enteredReferral   = in('referred_by', $starting_referral);
$enteredPass       = in('key', null, 'toText');
$submitted         = in('submit');

include SERVER_ROOT."interface/header.php";

$submit_successful = false; // *** Default.
/*if (DEBUG) {
$sql->Delete("delete from players where uname = 'Bobosa'");
 $submitted = true;
  $enteredName="Bobosa";
   $enteredEmail="jimmy@bob.com";
    $enteredClass="Red";
     $enteredPass="james";
      $enteredReferral="Sometimes I wonder whether it's productive to even try any more.";
} // *** DEBUG THE VALIDATION.
*/

if ($submitted) {
	$submit_successful = validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass);
} // *** Validates submission.

if (!$submit_successful) {
	display_signup_form($enteredName, $enteredEmail, $enteredClass, $enteredReferral);
} // *** Displays form.


// Gives the blacklisted emails, should eventually be from a table.
function get_blacklisted_emails(){
	return array('@hotmail.com', '@hotmail.co.uk', '@msn.com', '@live.com', '@aol.com', '@aim.com', '@yahoo.com', '@yahoo.co.uk');
}

// Gives whitelisted emails, make a table eventually.
function get_whitelisted_emails() {
	return array('@gmail.com');
}

// Return 1 if the email is a blacklisted email, 0 otherwise.
function preconfirm_some_emails($email) {
	// Made the default be to auto-confirm players.
	$res = 1;
	$blacklisted_by = get_blacklisted_emails();
	$whitelisted_by = get_whitelisted_emails();

	foreach ($blacklisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 1;
		}
	}

	foreach ($whitelisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 0;
		}
	}

	return $res;
}

function display_class_select($current) {
?>
	  <select id="send_class" name="send_class">
	    <option value="">Pick Ninja Color</option>
	    <option value="Red" <?php if($current=='Red') { echo 'selected="selected"'; } ?>>Red</option>
	    <option value="Blue" <?php if($current=='Blue') { echo 'selected="selected"'; } ?>>Blue</option>
	    <option value="White" <?php if($current=='White') { echo 'selected="selected"'; } ?>>White</option>
	    <option value="Black" <?php if($current=='Black') { echo 'selected="selected"'; } ?>>Black</option>
	  </select>
	<?php
}

function display_signup_form($enteredName, $enteredEmail, $enteredClass, $enteredReferral) {
	// ************************* START OF SIGNUP FORM ********************************
?>

	<span class="brownHeading">Sign Up</span>
	<br><br>
	Please add <strong><?php echo SYSTEM_MESSENGER_EMAIL; ?></strong> to the safe email senders list of your email account before signing up, so you can receive your confirmation email.
	<br><br>
	<form action="signup.php" method="post">
	<div class="FormField">
	 Username:  <input id="send_name" type="text" name="send_name" maxlength="50" class="textField" value='<?php echo $enteredName;?>'>
	   <div class="description">
	         Your ninja name can only contain letters, numbers and underscores.
	    </div>
	</div>
	<div class="FormField">
	  Password:  <input id="key" type="password" maxlength="50" name="key" class="textField">
	    <div class="description">
			Your password can only contain letters, numbers, underscores, and interior spaces.  Spaces at the beginning or end will be removed.
		</div>
	</div>
	<div class="FormField">
	  Ninja Type:  <?php display_class_select($enteredClass); ?>

	  <div class="description">
	        See the link to the Wiki below for more class information or just change your class easily within the game.
	  </div>
	</div>
	<div class="FormField" style="padding-bottom:2em">
	    Email Address:  <input id="send_email" type="text" name="send_email" class="textField" value='<?php echo $enteredEmail; ?>'>
	</div>
	<div class="FormField">
	  <span style="font-style:italic">Optional:</span> &nbsp; Website that linked you to Ninjawars:
	      <input id="referred_by" type="text" name="referred_by" class="textField" value='<?php echo $enteredReferral; ?>'>
	</div>
	<div class="submit" style="padding-top:2em">
	    <input type="submit" name="submit" value="Create New Account" class="formButton">
	</div>
	</form>
	<hr>
	A valid email address is required for this game, confirmation will be sent to the address you provide.<br><br>
	Lost Your Password ? <a href="lostpass.php">Retrieve Password</a><br><br>
	Didn't get your confirmation code ? <a href="lostconfirm.php">Activate Account</a>

	More information can be found on <a href="http://ninjawars.pbwiki.com/" target="_blank">the Wiki</a><img src="images/externalLinkGraphic.gif" alt="">.

	<hr>

	<?php
} // *** End of function display_signup_form().


function create_player_account() { // *** TODO:  Seperate out the creation of the player accounts.
}

function email_account_confirmation() { // *** TODO:
}

function validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass) {
	$successful = false;
	$sql = $GLOBALS['sql'];
	assert($sql);
	$send_name  = $enteredName;
	$send_pass  = $enteredPass;
	$send_class = $enteredClass;
	$send_email = $enteredEmail;
	$referred_by = $enteredReferral;

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n";
	$headers .= "Reply-To: ".SUPPORT_EMAIL_FORMAL_NAME." <".SUPPORT_EMAIL.">\r\n";

	echo "Your responses:<br> Name - $send_name,<br>
		 Password - ".(isset($send_pass)? "***yourpassword***" : "NO PASSWORD").",<br>
		 Class - $send_class,<br>
		 Email - $send_email,<br>
		 Site Referred By - $referred_by<br><br>\n";

	//  *** Requirement checking Section  ***

	if ($send_name != "" && $send_pass != "" && $send_email != "" && $send_class != "")  //When everything is non-blank.
	{
		$check_name = 0;
		$check_email = 0;
		$sql->QueryItem("SELECT uname FROM players WHERE uname = '$send_name'");
		$check_name  = $sql->getRowCount();
		$sql->QueryItem("SELECT email FROM players WHERE email = '$send_email'");
		$check_email = $sql->getRowCount();

        // Validate the username!
		$username_error = validate_username($send_name);

		if ($username_error) {
			echo $username_error;
		}
		else  //when all the name requirement errors didn't trigger.
		{
			$send_name = trim($send_name);  // Just cuts off any white space at the end.
			$filter = new Filter();
			$send_name = $filter->toUsername($send_name); // Filter any un-whitelisted characters.
			echo "Phase 1 Complete: Name passes requirements.<hr>\n";

			// Validate the password!
			$password_error = validate_password($send_pass);

			if ($password_error) {
				echo $password_error;
			} else {
				$send_pass = trim($send_pass); // *** Trims any extra space off of the password.
				$send_pass = $filter->toPassword($send_pass); // Filter any un-whitelisted characters.
				echo "Phase 2 Complete: Password passes requirements.<hr>\n";

				if (FALSE/* CURRENTLY NO BLOCKED EMAIL SERVICES strstr($send_email, "@") == "@aol.com" || strstr($send_email, "@") == "@netscape.com" || strstr($send_email, "@") == "@aim.com"*/) //Throws error if email from blocked domain.
				{
					echo "Phase 3 Incomplete: We cannot currently accept @aol.com, @netscape.com, or @aim.com email addresses.";
				}
				elseif (!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", trim($send_email)))
				{
					echo "Phase 3 Incomplete: The email address (".htmlentities($send_email).") must contain an @ symbol and a domain name to be valid.";
				}
				else if ($check_name == 0 && $check_email == 0 && $send_name != "SysMsg" && $send_name != "NewUserList")  //Uses previous query to make sure name and email aren't duplicates.
				{
					echo "Phase 3 Complete: Username and Email are unique.<br><hr>\n";
					if ($send_class != 'Red' && $send_class != 'Blue' && $send_class != 'White' && $send_class != 'Black')
					{
						echo "Phase 4 Incomplete: No proper class was specified.<br>";
					}
					else
					{
						echo "Phase 4 Complete: Class was specified.<br><hr>";
						// *** Signup is successful at this point  ***
						$preconfirm = 0;
						$preconfirm = preconfirm_some_emails($send_email);

						if (!$preconfirm) { /* not blacklisted by, so require a normal email confirmation */
							echo "Phase 5: When you receive an email from SysMsg, it will describe how to activate your account.<br><br>\n";
						}
						// The preconfirmation message occurs later.
						$confirm = rand(1000,9999); //generate confirmation code

						//  ***  Query is split up into two column groups of ten  ***
						$playerCreationQuery= "INSERT INTO players".
							" (uname, pname, health, strength, gold, messages, kills, turns, confirm,".
							" confirmed, email, class, level,  status, member, days, ip, bounty, clan, clan_long_name, created_date)".
							" VALUES".
							" ('$send_name','$send_pass','150','5','100','','0','80','$confirm','$preconfirm',".
							" '$send_email','$send_class','1','1','0','0','','0','','', now())";
						//  ***  Inserts the choices and defaults into the player table. Status defaults to stealthed. ***
						$sql->Insert($playerCreationQuery);
						$successful = TRUE; // *** SET THE FUNCTION AS A SUCCESS HERE ***

						assert($send_email && $headers && $send_name && $confirm && $send_pass && $send_class && $headers);
						//  ***  Sends out the confirmation email to the chosen email address.  ***
						$_to = "$send_email";
						$_subject = "NinjaWars Account Sign Up";
						$_body =
								"Thank you for signing up for Ninja Wars.<br>
								This message is from SysMsg, the AUTOMATED email system for NinjaWars. <br>
								Any emails you receive from the game will come from this address.
								Please click on the link below to confirm your account.<br><br>
								<a href=\"".WEB_ROOT."confirm.php?username=".urlencode($send_name)."&confirm=$confirm\">Confirm Account</a><br>
								Or paste this link:<br>".WEB_ROOT."confirm.php?username=".urlencode($send_name)."&confirm=$confirm <br>
								into your browser.<br><br>
								If you require help use the forums at ".WEB_ROOT."forum/<br>
								or email: ".SUPPORT_EMAIL."<br><br><b>
								Account Info</b><br>
								Username: $send_name<br>
								Level: 1<br>
								Password: $send_pass<br>
								Class: $send_class Ninja";
						$_from = "$headers";
						// *** Create message object.
						$Message = new Nmail($_to, $_subject, $_body, $_from);
						$sent = false;
						$sent = $Message->send();

						//  *** Add the signup to the "New User List"  ***
						sendMessage($send_name,'NewUserList',"Username: $send_name , Email: $send_email , Class: $send_class , Date: ".date('r')." Referred by: $referred_by");
						if ($sent && !$preconfirm) {
							//  *** Continues the page display ***
							echo "Confirmation email has been sent to <b>".$send_email."</b>.  <br>
				  					Be sure to also check for the email in any \"Junk Mail\" or \"Spam\" folders.
				  					Delivery typically takes less than 15 minutes.";
						} else {
							// Preconfirmed or the email didn't send, so automatically confirm the player.
							$up = "update players set confirmed = 1, confirm='55555' where uname = '".$send_name."'";
							$sql->Update($up);
							echo "Account with the login name \"".$send_name."\" is now confirmed!  Please <a href='#' onclick='refreshToLogin()'>login on the login bar</a> of the index page.";
						}

						echo "<br><br>Only one account per person is allowed.<br>\n";
						echo "If you require help use the forums at <a href='".WEB_ROOT."forum/'>".WEB_ROOT."forum/</a> or email: ".SUPPORT_EMAIL."\n";
					}	// *** End of class checking.
				}
				else	// Default, displays when the username or email are not unique.
				{
					$what = ($check_email != 0 ? "Email" : "Username");

					echo "Phase 3 Incomplete: That $what is already in use. Please choose a different one.\n";
				}
			}
		}
	}
	else  //  ***  Response for when nothing was submitted.  ***
	{
		echo "Phase 1 Incomplete: You did not correctly fill out all the necessary information.\n";
	}

	//This is the final signup return section, which only shows if a successful insert and confirmation number has -not- been acheived.

	echo "<br><br>";
	/*
	if (!isset($confirm))
	{
		echo "Return to <a href='signup.php?enteredEmail=$send_email&amp;enteredName=$send_name&amp;enteredClass=$send_class&amp;enteredReferral=$referred_by'>Sign Up page.</a>";
	}*/
	return $successful;
} // *** End of validate_signup() function.

include SERVER_ROOT."interface/footer.php";
?>
