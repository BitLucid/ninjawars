<style>
footer.password-reset{
	margin-top:5em;
}

</style>
<section class='password-reset-page'>
<h1>Request a password reset</h1>

{if $error}
	<div class='parent'>
		<div class='child error glassbox thick fade-in'>{$error|escape}</div>
	</div>
{/if}
{if $message}
	<div class='parent'>
		<div class='child bg-success glassbox thick fade-in'>{$message|escape}</div>
	</div>
{/if}
<div class='glassbox'>
	<form method='post' action='/password/post_email'>
		{* Need CSRF here! *}
		<label>Your email: <input type='email' name='email' value='{$email|default:''|escape}' placeholder='Your email'></label>
		<span style='padding:0 7rem'><label>Or your ninja name: <input type='text' name='ninja_name' value='{$ninja_name|default:''|escape}' placeholder='Your ninja name'></label></span>
		<div class='glassbox'><input class='btn btn-primary' type='submit' name='request' value='Request'></div>
	</form>
</div>
</section>

<footer class='password-reset'>
	<ul>
		<li><a href='/staff'>Contact Staff For Further Support</a></li>
		<li><a href='/assistance'>Other Account Issues</a></li>
	</ul>
</footer>
