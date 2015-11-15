{literal}
<style type='text/css'>
.right-side{
	display:block;
	text-align:left;
}
.left-side{
	display:block;
	text-align:left;
}
.central{
	text-align:center;
}
.left-side a{
	font-size:.8em;
}
.line{
	margin-bottom: .5em;
	font-size: 1.4em;
}
.outer-box{
	display:inline-block;
	margin-left:auto;
	margin-right:auto;
	border: 10px rgb(20, 20, 20) solid;
	background-color:rgb(50, 50, 50);
	padding:1em 4em;
}
.outer-outer-box{
	display:inline-block;
	border: 5px rgb(10, 10, 10) solid;
}

section.login-page{
	margin:0.3em auto 0.3em;text-align:center;
}

#request-login{
	font-size:larger;width:100%;
}
#login-bottom-bar-container{
	margin: 5em auto .5em;width:96%;padding:.2em;border: 1px solid #993300;
}
#login-problems{
	padding: 0 auto 0;text-align:center;background-color: rgba(30, 30, 30, 0.70);
}
</style>
{/literal}

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

{if $is_logged_in}
<div class='glassbox'>
  You are already logged in! <a href='/index.php'>Go Fight!</a>
</div>
{else}


<section class='login-page'>
  <form id="login-form" action="/login.php" method="post">
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
	      <span class='left-side'>Password <a tabindex=4 href='account_issues.php'>(Forgot?)</a></span>
	      <input tabindex=2 name="pass" required type="password" class='right-side'>
	      </div>
	    </label>
	    <div class='left-side'>
	    	<input type='hidden' name='command' value='login_request'>
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
	<div id="login-problems">
	  <span class="signup-link">
		<a target="main" href="signup.php?referrer={$referrer|escape}">Become a Ninja!</a> |
	  </span>
	  <span>
		<a href="account_issues.php" target="main" class="blend side">Login or Signup Problems?</a>
	  </span>
	</div>
</div>
