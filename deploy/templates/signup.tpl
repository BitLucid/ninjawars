	<h1>Sign Up</h1>
	
	
	<p>
	Please add <strong>{$SYSTEM_MESSENGER_EMAIL}</strong> to the safe email 
	senders list of your email account before signing up, so you can receive 
	your confirmation email.
	</p>
	
	<form action="signup.php" method="post">
	<div class="FormField">
	 Username:  <input id="send_name" type="text" name="send_name" maxlength="50" class="textField" value='{$enteredName}'>
	   <div class="description">
	         Your ninja name can only contain letters, numbers and underscores.
	    </div>
	</div>
	<div class="FormField">
	  Password:  <input id="key" type="password" maxlength="50" name="key" class="textField">
	    <div class="description">
			Your password can only contain letters, numbers, underscores, and interior spaces.
			  Spaces at the beginning or end will be removed.
		</div>
	</div>
	<div class="FormField">
	  Ninja Type:  {$class_select}

	  <div class="description">
	    See the link to the Wiki below for more class information or 
	    just change your class easily within the game.
	  </div>
	</div>
	<div class="FormField" style="padding-bottom:2em">
	    Email Address:  <input id="send_email" type="text" name="send_email" class="textField" value='{$enteredEmail}'>
	</div>
	<div class="FormField">
	  <span style="font-style:italic">Optional:</span> 
	  &nbsp; Website that linked you to Ninjawars:
	    <input id="referred_by" type="text" name="referred_by" class="textField" value='{$enteredReferral}'>
	</div>
	<div class="submit" style="padding-top:2em">
	    <input type="submit" name="submit" value="Create New Account" class="formButton">
	</div>
	</form>
	
	
	<hr>
	
	<p>A valid email address is required for this game, confirmation will be sent to the address you provide.<br><br>
	Lost Your Password ? <a href="lostpass.php">Retrieve Password</a><br><br>
	
	
	Didn't get your confirmation code ? <a href="lostconfirm.php">Activate Account</a>
	</p>
	<p>
	More information can be found on 
	    <a href="http://ninjawars.pbwiki.com/" target="_blank">the Wiki</a>
	    <img src="images/externalLinkGraphic.gif" alt="">.
	</p>

	<hr>
