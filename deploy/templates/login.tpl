<h1>Login</h1>

{if $logged_out}
<div class='notice'>You logged out! Log in again below if you want.</div>
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

<div id='login-bottom-bar-container' style='margin: 5em auto .5em;width:100%;padding:.2em;border-top: 1px solid #993300;border-bottom:1px solid #993300;'>
	<div id="login-problems" style='padding: 0 auto 0;text-align:center;background-color: rgba(30, 30, 30, 0.70);'>
	  <span class="signup-link">
		<a target="main" href="signup.php?referrer={$referrer|escape}">Become a Ninja!</a> |
	  </span>
	  <span>
		<a href="account_issues.php" target="main" class="blend side">Login or Signup Problems?</a>
	  </span>
	</div>
</div>
