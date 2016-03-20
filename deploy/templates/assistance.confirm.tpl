<style>
#account-confirmation{
	border: 1px solid #000000;font-weight: bold;padding:1em 2em;
}
#account-confirmation nav{
	margin:4em 0 2em;
}
</style>

<h1>Account Confirmation</h1>

<div id='account-confirmation'>

{if $confirmed eq 1}
  That ninja ({$username|escape}) is already confirmed in our system.
  <br><br>Please <a class='btn btn-primary' href='/login' target='main'>Log In</a> on the main page or contact <a href='/staff'>the game administrators</a> if you have further issues.
{elseif $confirmation_confirmed}
  Confirmation Successful
  <p>You may now <a class='btn btn-primary' href='/login' target='main'>Log In</a> from the main page.</p>
{else}
  <p>
    This account can not be verified or the account was deactivated.
    Please contact <a href='mailto:{$smarty.const.SUPPORT_EMAIL|escape}'>{$smarty.const.SUPPORT_EMAIL|escape}</a> if you require more information.
  </p>
{/if}
	<nav><a target='main' href="/">Return to Main?</a></nav>
</div>
