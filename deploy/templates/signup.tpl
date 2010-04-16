	<h1>Become a Ninja!</h1>

	<form id='signup' action="signup.php" method="post">
    
    <fieldset>
     <legend>Login Info</legend>
     <div>
	 <label for='send_name'>Ninja Name:</label>  
	 <input id="send_name" type="text" name="send_name" maxlength="50" class="textField" value='{$enteredName|escape}'>
	 </div>
	 <div>
	   Your ninja name can only contain letters, numbers and underscores.
	 </div>
	 <div>
	  <label for='key'>Password:</label>  
	  <input id="key" type="password" maxlength="50" name="key" class="textField">
	 </div>
	</fieldset>
	
	<fieldset>
	 <legend>Ninja Info</legend>
      <div>
	  <label for='send_class'>Ninja Type:</label>  
	  {$class_select}
	  <span>
	  Change class easily within the game, or click the "wiki" link below for more information.
	  </span>
	  </div>
	</fieldset>
	
	<fieldset>
	 <legend>Confirmation Info</legend>
	  <div>
	  <label for='send_email'>Email Address:</label>  
	  <input id="send_email" type="text" name="send_email" class="textField" value='{$enteredEmail|escape}'>
      <span>
        Please add <strong>{$SYSTEM_MESSENGER_EMAIL}</strong> to the safe email senders list of your email account to guarantee you receive your account and confirmation email.  This email address will only be used for confirmation purposes, never spammed, never shared.
      </span>
      </div>  
    </fieldset>

      <div>
	    <input id='become-a-ninja' type="submit" name="submit" value="Become a Ninja!" class="formButton">
      </div>
	</form>
	
	<h3>Problems?</h3>
	<p>
	Lost Your Password ? <a href="account_issues.php">Resend Account Info Email</a> / Didn't get your confirmation code ? <a href="account_issues.php">Resend Confirmation Email</a>
	</p>
	<p>
	More information on classes and skills can be found on 
	    <a href="http://ninjawars.pbworks.com/" target="_blank" class='extLink'>the Wiki</a>.
	</p>
	<p>
	  Otherwise, just <a href='staff.php'>Contact Us</a>.
	</p>
	<hr>
