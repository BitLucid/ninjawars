<style type='text/css'>
{literal}
.solo-page form#signup{
	font-size:larger;padding-left:1em; padding-right:1em;
}

form#signup label{
	height:auto;text-align:left;
}
form#signup .success{
	color:green;
}
form#signup .failure{
	color:red;
}
form#signup label.class-desc{
	display:block;font-weight:normal;padding:0;margin:0;
}
form#signup .ninja-info .inline-block{
	vertical-align:top;
}
form#signup small{
	color:grey;
}

.signup-page.error{
	border:0.3em rgb(250, 15, 15) solid;padding:0.5em;margin:1em;
}
#become-a-ninja{
	display:inline-block;margin:0.5em auto 0.3em;
}

form#signup .ninja-info section + section{
	margin-top:1em;
}

{/literal}
</style>

{if !$submit_successful}
	<h1>Become a Ninja!</h1>
{else}
	<h1>You are almost ready to be a ninja!</h1>
{/if}


{if $submitted}
<!-- A breakdown of the signup process results so far -->
<section id='signup-process' class='glassbox'>
	<h3>Your Choices</h3>
    <div class='stamp'>
      Email - <strong><em>{$enteredEmail|escape}</em></strong><br>
	  Password - {if $enteredPass}<span class='success'>***yourpassword***</span>{else}<span class='failure'>NO PASSWORD</span>{/if}<br>
	  Ninja Name - <strong class='char-name'>{$enteredName|escape}</strong><br>
	  Ninja Type - {$class_display|escape}<br>
    </div>
  
{if isset($completedPhase)}
	<div class='completion-steps thick' style='clear:both'>
	{if $completedPhase gte 1}
  		Phase 1 <span style='color:green'>Complete:</span> Ninja Name has a valid format.
  		<hr>
	{/if}
	{if $completedPhase gte 2}
  		Phase 2 <span style='color:green'>Complete:</span> Password passes requirements.
  		<hr>
	{/if}
	{if $completedPhase gte 3}
  		Phase 3 <span style='color:green'>Complete:</span> Ninja Name and Email are unique.
  		<hr>
	{/if}
	{if $completedPhase gte 4}
  		Phase 4 <span style='color:green'>Complete:</span> Ninja Type was chosen.
  		<hr>
	{/if}

	{if $submit_successful}
		{if $confirmed}
		  <p>Account with the login email "{$enteredEmail|escape}" is now confirmed! <strong>You can now <a href='login.php'>login!</a></strong></p>
		{else}
		  Phase 5: When you receive an email from ninjawars ({$smarty.const.SYSTEM_EMAIL}), click the confirmation link to activate your account.
		  <br><br>
		  Confirmation email has been sent to <strong>{$enteredEmail|escape}</strong>.
		  <br>
		  Be sure to also check for the email in any "Junk Mail" or "Spam" folders. Delivery typically takes less than 15 minutes.
		{/if}
	</div><!-- End of .completion-steps -->
		
	{/if}

	{if !$error}
		<!-- Success! -->
  		<small>Only one account per person is allowed.</small>
		{include file='signup.success.tpl'}
		<!-- Signup.success generally just displays the google analytics conversion tracking for successful signup -->
	{/if}
{/if}
</section>
{/if}{* End of if submitted *}

	{if $error}
	  <p class='signup-page error'>{$error|escape}</p>
	  <div class='glassbox'>
		  <p class>Lost Your Password ? <a href="account_issues.php">Resend Account Info Email</a> / Didn't get your confirmation code ? <a href="account_issues.php">Resend Confirmation Email</a></p>
		  <p>If you need help, email: <a href="mailto:{$smarty.const.SUPPORT_EMAIL}">{$smarty.const.SUPPORT_EMAIL}</a> or use the forums at
		  <a href="{$smarty.const.WEB_ROOT}forum/">{$smarty.const.WEB_ROOT}forum/</a></p>
	  </div>
	{/if}


{if !$submit_successful}
	<form id='signup' action="signup.php" method="post">


    <fieldset>
     <legend>Login Info</legend>
     <div>
     	<label for='send_email'>Email Address:</label>
		<input id="send_email" required type="email" name="send_email" class="textField" placeholder='you@example.com' value="{$enteredEmail|escape}">
		  <small>
		    This email address will only be used for account &amp; confirmation purposes, <strong>never spammed, never shared.</strong>
		  </small>
	 </div>
	 <div>
	  <label for='key'>Password:</label>
	  <input id="key" required type="password" maxlength="50" name="key" class="textField" value='{$enteredPass|escape}'>
	 </div>
	 <div>
	  <label for='cpass'>Confirm&nbsp;Pass:</label>
	  <input id="cpass" required type="password" maxlength="50" name="cpass" class='textField' value='{$enteredCPass|escape}'>
	 </div>
	</fieldset>



	<fieldset class='ninja-info'>
	 <legend>Ninja Info</legend>
	 <section>
		<label for='send_name'>Ninja Name:</label>
	 	<input id="send_name" required autofocus type="text" pattern='{literal}^[a-zA-Z][a-zA-Z0-9-_\.]{1,23}${/literal}' 
	 	title='Your ninja name can only contain letters, numbers and underscores, and must be from 2 to 24 characters long.'
	 	name="send_name" maxlength="50" class="textField" value="{$enteredName|escape}">
	 	<small>
	   	Your ninja name can only contain letters, numbers and underscores.
	   </small>
	 </section>
	 <section>
	  <div class='inline-block'>
	  	<strong>Ninja Type:</strong>
	  </div>
	  <div class='inline-block'>
  	{foreach from=$classes item='class' key='identity'}
		<label class='class-desc block'>
			<input type='radio' name='send_class' value='{$identity}' 
				{if $enteredClass eq $identity}checked='checked'{/if}> {$class.name} - {$class.expertise}
		</label>
  	{/foreach}
      </div>
	  <div class='glassbox'>
	  	<small>Change type easily within the game, or click the "wiki" link below for more information.</small>
	  </div>
	  </section>
	</fieldset>

      <section>
	    <input id='become-a-ninja' type="submit" name="submit" value="Become a Ninja!" class="formButton">
	    <div>
	    	<small>Please add <strong>{$smarty.const.SYSTEM_EMAIL}</strong> to your safe email sender list to make sure you get the confirmation email! </small>
	    </div>


      </section>
	</form>

{/if}

	<h3>Problems?</h3>

	<section class='glassbox'>

	<p>
	Lost Your Password ? <a href="account_issues.php">Resend Account Info Email</a> / Didn't get your confirmation code ? <a href="account_issues.php">Resend Confirmation Email</a>
	</p>
	<small>
	More information on the ninja classes and their skills can be found on
	    <a href="http://ninjawars.pbworks.com/" target="_blank" class='extLink'>the Wiki</a> or on the "skills" page once you begin playing.
	</small>
	<p>
	  For any other questions, just <a href='staff.php'>Contact Us</a>.
	</p>

	</section>

{literal}
<script>
if (top.location != location) { // Framebreak on the signup page as well.
  top.location.href = document.location.href ;
}
</script>
{/literal}