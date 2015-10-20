<h1>Request password reset</h1>

{if $error}
	<div class='parent'>
		<div class='child error'>{$error}</div>
	</div>
{/if}
{if $message}
	<div class='parent'>
		<div class='child bg-success'>{$message}</div>
	</div>
{/if}

<form method='post' action=''>
	{* Need CSRF here! *}
	<label>Email: <input type='email' name='email' value='{$email}' placeholder='Your email'></label>
	<label>Or Ninja name: <input type='text' name='ninja_name' value='{$ninja_name}' placeholder='Your ninja name'></label>
	<input type='submit' name='request' value='Request'>
</form>