<h1>Login</h1>

{if $logged_out}
<div class='notice'>You logged out! Log in again below if you want.</div>
{/if}

{if $login_error_message}
	  <!-- This section only gets displayed in the event of an incorrect login -->
      <div id='login-error' class="error">
      	{* Unescaped error to allow for links. *}
        {$login_error_message}
      </div>
{/if}

{if $authenticated}
<div class='glassbox'>
  You are already logged in! <a href='/'>Go Fight!</a>
</div>
{else}


<section class='login-page'>
  <form id="login-form" action="/login/login_request" method="post">
	  <input type="hidden" name="ref" value="{$referrer|escape}">
	    <div class='outer-outer-box'>
	  	<div class='outer-box'>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Email or Ninja Name</span>
	      <input tabindex=1 name="user" required type="text" value='{$stored_username|escape}' class='right-side'>
		  </div>
	    </label>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Password <a tabindex=4 href='/assistance'>(Forgot?)</a></span>
	      <input tabindex=2 name="pass" required type="password" class='right-side'>
	      </div>
	    </label>
	    <div class='left-side'>
		    <input tabindex=3 name="login_request" id='request-login' class='btn btn-vital' type="submit" value="Login">
		</div>
	    </div>
	    </div>
	</form>
</section>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->
{/if}


<div id='login-bottom-bar-container'>
	<div id="login-problems-resources">
	  <span class="signup-link">
		<a target="main" href="/signup?referrer={$referrer|escape}">Become a Ninja!</a> |
	  </span>
	  <span>
		<a href="/assistance/" target="main" class="blend side">Login or Signup Problems?</a>
	  </span>
	</div>
</div>
