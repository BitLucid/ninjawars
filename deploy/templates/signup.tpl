	<h1>Sign Up</h1>
	
	
	<p>
	  Please add <strong>{$SYSTEM_MESSENGER_EMAIL}</strong> to the safe email 
	  senders list of your email account before signing up, so you can receive 
	  your confirmation email.
	</p>
	
	<form id='signup' action="signup.php" method="post">

    <label for='send_email'>Email Address:</label>  <input id="send_email" type="text" name="send_email" class="textField" value='{$enteredEmail}'>

	 <label for='send_name'>Username:</label>  <input id="send_name" type="text" name="send_name" maxlength="50" class="textField" value='{$enteredName}'>
	   <div class="description">
	         Your ninja name can only contain letters, numbers and underscores.
	    </div>

	  <label for='password'>Password:</label>  <input id="key" type="password" maxlength="50" name="key" class="textField">
	    <div class="description">
			Letters, numbers, underscores and spaces.  Starting or ending spaces are not allowed.
		</div>

	  <label for='send_class'>Ninja Type:</label>  {$class_select}
	  <div class="description"> 
	  Change class easily within the game, or see the 
	  <a href="http://ninjawars.pbworks.com/" target="_blank">wiki</a> for more information.
	  </div>

	    <input type="submit" name="submit" value="Become a Ninja!" class="formButton">

	</form>
	
	
	<hr>
	
	<p>A valid email address is required for this game, confirmation will be sent to the address you provide.<br><br>
	Lost Your Password ? <a href="lostpass.php">Retrieve Password</a><br><br>
	
	
	Didn't get your confirmation code ? <a href="lostconfirm.php">Activate Account</a>
	</p>
	<p>
	More information can be found on 
	    <a href="http://ninjawars.pbworks.com/" target="_blank">the Wiki</a>
	    <img src="images/externalLinkGraphic.gif" alt="">.
	</p>

	<hr>
