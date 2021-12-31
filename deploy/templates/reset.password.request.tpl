<style>
footer.password-reset{
	margin-top:5em;
}

</style>
<section class='password-reset-page'>
<h1>Request password reset</h1>

{if $error}
	<div class='parent'>
		<div class='child error glassbox thick'>{$error|escape}</div>
	</div>
{/if}
{if $message}
	<div class='parent'>
		<div class='child bg-success glassbox thick'>{$message|escape}</div>
	</div>
{/if}
<div class='glassbox'>
	<form method='post' action='/password/post_email'>
		{* Need CSRF here! *}
		<label>Email: <input type='email' name='email' value='{$email|default:''|escape}' placeholder='Your email'></label>
		<label>Or Ninja name: <input type='text' name='ninja_name' value='{$ninja_name|default:''|escape}' placeholder='Your ninja name'></label>
		<input type='submit' name='request' value='Request'>
	</form>
</div>
</section>

<footer class='password-reset'>
	<ul>
		<li><a href='/staff'>Contact Support</a></li>
		<li><a href='/assistance'>Other Account Issues</a></li>
	</ul>
</footer>
