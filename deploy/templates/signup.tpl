{if !$submit_successful}
	<h1 role='heading' aria-label='Signup header'>Become a Ninja!</h1>
{else}
	<h1 role='heading' aria-label='Complete header'>You are almost ready to be a ninja!</h1>
{/if}


{if $submitted}
<!-- A breakdown of the signup process results so far -->
<section id='signup-process' class='glassbox'>
	<h3>Your Choices</h3>
    <div class='stamp'>
	  Email - <strong><em>{if $signupRequest}{$signupRequest->enteredEmail|escape}{/if}</em></strong><br>
	  Password - {if $signupRequest && $signupRequest->enteredPass}<span class='success'>***yourpassword***</span>{else}<span class='failure'>NO PASSWORD</span>{/if}<br>
	  Ninja Name - <strong class='char-name'>{if $signupRequest}{$signupRequest->enteredName|escape}{/if}</strong><br>
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
		  <p>Account with the login email "{if $signupRequest}{$signupRequest->enteredEmail|escape}{/if}" is now confirmed!</p>
		  <div class='glassbox'>
		  	You can 
			<div class='glassbox' style='width:40%'>
				<a href='/login'><button class='btn btn-primary btn-lg'>LOGIN NOW!</button></a>
			</div>
		  </div>
		{else}
		  Phase 5: <span style='color:green'>When you receive an email from ninjawars ({$smarty.const.SYSTEM_EMAIL}), click the confirmation link to activate your account.</span>
		  <br><br>
		Confirmation email has been sent to <strong>{if $signupRequest}{$signupRequest->enteredEmail|escape}{/if}</strong>.
		  <br>
		  Be sure to also check for the email in any "Junk Mail" or "Spam" folders. Delivery typically takes less than 15 minutes.
		{/if}
	</div><!-- End of .completion-steps -->

	{/if}

	{if !$error}
		<!-- Success! -->
  		<small class='de-em'><em>Only one account per person is allowed.</em></small>
		{include file='signup.success.tpl'}
		<!-- Signup.success generally just displays the google analytics conversion tracking for successful signup -->
	{/if}
{/if}
</section>
{/if}{* End of if submitted *}

	{if $error}
	  <p class='signup-page error' role='alert'>{$error|escape}</p>
	  <div class='glassbox'>
		<nav>
		  <p class>Lost Your Password ? <a href="/assistance">Resend Account Info Email</a> / Didn't get your confirmation code ? <a href="/assistance">Resend Confirmation Email</a></p>
		  <p>If you need help, email: <a href="mailto:{$smarty.const.SUPPORT_EMAIL}">{$smarty.const.SUPPORT_EMAIL}</a> or use the
		  <a href="https://www.facebook.com/ninjawars.net/">facebook page</a></p>
		</nav>
	  </div>
	{/if}


{if !$submit_successful}
	{* Do not change this without changing the recaptcha in signup.js *}
	<form id='signup' action="/signup/signup" onSubmit='recFormSubmit' method="post">

    <fieldset>
     <legend>Create Your Login Info</legend>
     <div>
     	<label for='send_email'>Email Address:</label>
		<input id="send_email" required type="email" autocomplete='username' name="send_email" class="textField" placeholder='you@example.com' value="{if $signupRequest}{$signupRequest->enteredEmail|escape}{/if}">
		  <small>
		    (email never spammed, never shared)
		  </small>
	 </div>
	 <div>
	  <label for='key'>Password:</label>
	  <input id="key" required type="password" autocomplete='new-password' maxlength="50" name="key" class="textField" value='{$signupRequest->enteredPass|escape}'>
	 </div>
	 <div>
	  <label for='cpass'>Confirm&nbsp;Pass:</label>
	  <input id="cpass" required type="password" autocomplete='new-password' maxlength="50" name="cpass" class='textField' value='{$signupRequest->enteredCPass|escape}'>
	 </div>
	</fieldset>

	<fieldset class='ninja-char-creation'>
	 <legend>Create Your Ninja Info</legend>
	 <section>
		<label for='send_name'>Ninja Name:</label>
	 	<input id="send_name" required autofocus type="text" pattern='{literal}^[a-zA-Z][a-zA-Z0-9\-_\.]{1,23}${/literal}'
	 	title='Your ninja name can only contain letters, numbers and underscores, and must be from 2 to 24 characters long.'
		name="send_name" maxlength="50" class="textField" value="{if $signupRequest}{$signupRequest->enteredName|escape}{/if}">
	 	<small>
	   	(letters, numbers and underscores only)
	   </small>
	 </section>
	 <section>
	  <div class='block'>
	  	<strong>Animal Style &amp; Expertise:&nbsp;</strong>
	  </div>
	  <style>
	  {literal}
	  	.ninja-picker-container {
	  		display: flex;
	  		flex-wrap: wrap;
	  		justify-content: space-between;
	  	}
		.ninja-picker-container label.class-desc {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			color: #333;
			font-weight: bold;
			font-size: smaller;
			width: 20vw;
			height: 20vw;
			margin-left: 0.5vw;
			margin-right: 0.5vw;
			margin-bottom:2rem;
			padding-bottom:0.7rem;
			border: 2px solid #ccc;
			border-radius: 0.5rem;
			background-color: #eee;
			cursor: pointer;
		}
		/*for small mobile screens */
		@media (max-width: 600px) {
			.ninja-picker-container label.class-desc {
				width: 100%;
				height: auto;
			}
		}
		.ninja-picker-container img{
			max-width:100%;
		}
		.ninja-picker-container label:hover {
			background-color: #ddd;
			border-color: #aaa;
		}
		{/literal}
	  </style>
	  <div class='ninja-picker-container'>
  	{foreach from=$classes item='class' key='identity'}
		<label class='class-desc inline-block'>
			<img src='/images/characters/{$identity}_ninja.jpg' alt='{$identity} icon' class='class-icon quarter-max'>
			<input type='radio' name='send_class' value='{$identity}' required='required'
				{if $signupRequest}
					{* user selected case *}
					{if $signupRequest->enteredClass eq $identity}checked='checked'{/if}
					{* unfilled form case *}
					{if !$signupRequest->enteredClass && $identity eq 'viper'}checked='checked'{/if}
				{/if}
				>
				{$class.name} - {$class.expertise}
			</label>
  	{/foreach}
      </div>
	  <div class='glassbox'>
	  	<small>
			Change type easily in-game, or check <a href="http://ninjawars.pbworks.com/" target="_blank" class='extLink'>the Wiki</a> for more info.
		</small>
	  </div>
	  </section>
	</fieldset>

      <section style='min-heigth:5rem'>
	  {* This section is used by signup.js and should only be changed in concert with that script below *}
	  {* It is also tested via the cypress signup.cy.js script, so changes should be checked by running that *}
	  	<div style='min-height:6rem' class='centered'>
			<button
				class="btn btn-vital" 
				id='become-a-ninja' 
				type="submit" 
				name="submit"
			>
			Become A Ninja!
			</button>
			<input type='hidden' name='g-recaptcha-response' id='g-recaptcha-response' value=''>
		</div>
	    <div class='text-centered'>
	    	<small>
				* Note: Add <strong>{$smarty.const.SYSTEM_EMAIL}</strong> to your safe email list<br />
				 to ensure you get your confirmation email! 
			</small>
	    </div>


      </section>
	</form>

{/if}


<style>
{literal}
/* Signup page footer specific styles */
.help-list {
	list-style-type: disc;
}
{/literal}
</style>

	<footer>
		<section class='glassbox'>
			<h3>Problems?</h3>
			<div class='hero'>

				<ul class='help-list'>
					<li>
					Lost Your Password? <a href="/assistance">go to reset password</a>
					</li>
					<li>
						Already a ninja? <a href='/login'>login instead</a>
					</li>
					<li>
						Didn't get your confirmation code? <a href="/assistance">Resend Confirmation Email</a>
					</li>
					<li>
					Get more info about Ninja type &amp; the game:
						<a href="http://ninjawars.pbworks.com/" target="_blank" class='extLink'>on the Wiki</a>.
					</li>
					<li>
					Info on privacy and terms of service <a href="https://www.google.com/intl/en/policies/privacy/" target="_blank" style="">Privacy</a>
					 - <a href="https://www.google.com/intl/en/policies/terms/" target="_blank" style="">Terms</a>
					</li>
					<li>
					Or <a href='/staff'>Contact Us</a>.
					</li>

				</ul>
			</div>

		</section>
	</footer>

	<style>
	{literal}
		.grecaptcha-badge { 
			visibility: hidden;
		}
	{/literal}
	</style>
	{* see https://www.google.com/recaptcha/admin/site/692084162/settings *}
	<!-- See staff page for policy information. -->
	<script src="https://www.recaptcha.net/recaptcha/api.js?render={$smarty.const.RECAPTCHA_SITE_KEY}"></script>
	
	<script src='/js/signup.js'></script>
	<script>
	const recaptchaSiteKey = '{$smarty.const.RECAPTCHA_SITE_KEY}';
	{literal}

		function recFormSubmit(e){
			e.preventDefault();
			e.stopPropagation();
			console.debug('Running grecaptcha.execute')
			grecaptcha.ready(function() {
				grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
					console.debug('grecaptcha.execute token', token);
					// Add your logic to submit to your backend server here.
					$('#g-recaptcha-response').val(token);
					$('#signup').submit();
				});
			});
		}
		// Currently in form onSubmit
	{/literal}
	</script>

