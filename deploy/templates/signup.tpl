{if !$submit_successful}
	<h1>Become a Ninja!</h1>
{else}
	<h1>You are almost ready to be a ninja!</h1>
{/if}

{if $submitted} {* Display theresults of the submitted signup form. *}
<style type='text/css'>
{literal}
.green-border{
	border:green .3em solid;
	padding:.2em;
	margin: .3em auto;
}

{/literal}
</style>
<div id='signup-process'>
	<span class='green-border' style='display:inline-block;'>Your Choices</span>
    <div class='green-border'>
	  Name - <strong class='char-name'>{$enteredName|escape}</strong><br>
	  Password - {if $enteredPass}<span style='color:green'>***yourpassword***</span>{else}<span style='color:red'>NO PASSWORD</span>{/if}<br>
	  Class - {$class_display|escape}<br>
	  Email - <strong><em>{$enteredEmail|escape}</em></strong>
     
    </div>
  
{if isset($completedPhase)}

	<div class='completion-steps' style='clear:both'>

	{if $completedPhase gte 1}
  Phase 1 <span style='color:green'>Complete:</span> Name passes requirements.
  <hr>
	{/if}
	{if $completedPhase gte 2}
  Phase 2 <span style='color:green'>Complete:</span> Password passes requirements.
  <hr>
	{/if}
	{if $completedPhase gte 3}
  Phase 3 <span style='color:green'>Complete:</span> Username and Email are unique.
  <hr>
	{/if}
	{if $completedPhase gte 4}
  Phase 4 <span style='color:green'>Complete:</span> Class was specified.
  <hr>
	{/if}

	{if $submit_successful}
		{if $confirmed}
  <p>
  	Account with the login name "{$enteredName|escape}" is now confirmed! <strong>You can now <a href='login.php'>login!</a></strong>
  </p>
		{else}
  Phase 5: When you receive an email from ninjawars ({$smarty.const.SYSTEM_EMAIL}), it will describe how to activate your account.
  <br><br>
  Confirmation email has been sent to <strong>{$enteredEmail|escape}</strong>.
  <br>
  Be sure to also check for the email in any "Junk Mail" or "Spam" folders.
  Delivery typically takes less than 15 minutes.
		{/if}
		
	</div>
		
	{/if}

	{if !$error}
  <p>Only one account per person is allowed.</p>
	{include file='signup.success.tpl'}
	<!-- Display the conversion tracking for successful signup -->
	{/if}
{/if}
</div>


{/if}{* End of if submitted *}


{if $error}
  <p style='border:.3em rgb(250, 15, 15) solid;padding:1em;margin:2em'>{$error|escape}</p>
  <p>If you need help, email: <a href="mailto:{$smarty.const.SUPPORT_EMAIL}">{$smarty.const.SUPPORT_EMAIL}</a> or use the forums at
  <a href="{$smarty.const.WEB_ROOT}forum/">{$smarty.const.WEB_ROOT}forum/</a></p>
{/if}



{if !$submit_successful}
	<form id='signup' action="signup.php" method="post">

    <fieldset>
     <legend>Login Info</legend>
     <div>
	 <label for='send_name'>Ninja Name:</label>
	 <input id="send_name" type="text" name="send_name" maxlength="50" class="textField" value="{$enteredName|escape}">
	 </div>
	 <div>
	   Your ninja name can only contain letters, numbers and underscores.
	 </div>
	 <div>
	  <label for='key'>Password:</label>
	  <input id="key" type="password" maxlength="50" name="key" class="textField">
	 </div>
	 <div style='margin-top:1.5em'>
	  <label for='cpass'>Re-type Password:</label>
	  <input id="cpass" type="password" maxlength="50" name="cpass" class='textField'>
	 </div>
	</fieldset>

	<fieldset>
	 <legend>Ninja Info</legend>
      <div>
	  <label for='send_class'>Ninja Type:</label>

	  <div style='margin-left:17%;width:70%'>
  	{foreach from=$classes item='class' key='identity'}
  		<div>
  		<input type='radio' name='send_class' value='{$identity}' {if $enteredClass eq $identity}checked='true'{/if} style=''>
  			{$class.name} - {$class.expertise}
  		</input>
  		</div>
  	{/foreach}
      </div>
	  <div>
	  Change class easily within the game, or click the "wiki" link below for more information.
	  </div>
	  </div>
	</fieldset>

	<fieldset>
	 <legend>Confirmation Info</legend>
	  <div>
	  <label for='send_email'>Email Address:</label>
	  <input id="send_email" type="text" name="send_email" class="textField" value="{$enteredEmail|escape}">
		  <div>
		  	Please add <strong>{$smarty.const.SYSTEM_EMAIL}</strong> to your safe email sender list to make sure you get the confirmation email! <br>
		    This email address will only be used for confirmation purposes, <strong>never spammed, never shared.</strong>
		  </div>
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
	More information on the ninja classes and their skills can be found on
	    <a href="http://ninjawars.pbworks.com/" target="_blank" class='extLink'>the Wiki</a> or on the "skills" page once you begin playing.
	</p>
	<p>
	  Otherwise, just <a href='staff.php'>Contact Us</a>.
	</p>
{/if}
