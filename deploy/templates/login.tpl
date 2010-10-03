<h1>Login</h1>

{if $logged_out}
	<div class='notice'>You logged out!  Log in again below if you want.</div>
{/if}

{if $login_error}
	<div class='error'>
		{$login_error}
	</div>
{/if}

{if $is_logged_in}
<div>
	You are already logged in!
</div>
{else}

<h2>Login info</h2>
<div style='margin: .3em auto .3em;text-align:center;'>
{include file='login-bar.tpl' referrer=$referrer stored_username=$stored_username}
</div>

{/if}
