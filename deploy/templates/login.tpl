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
	font-size:2em;width:100%;
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
  <form id="login-form" action="login.php" method="post">
	  <input type="hidden" name="ref" value="{$referrer|escape}">
	    <div class='outer-outer-box'>
	  	<div class='outer-box'>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Email or Ninja Name</span>
	      <input tabindex=1 name="user" type="text" value='{$stored_username|escape}' class='right-side'>
		  </div>
	    </label>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Password <a tabindex=4 href='account_issues.php'>(Forgot?)</a></span>
	      <input tabindex=2 name="pass" type="password" class='right-side'>
	      </div>
	    </label>
	    <div class='left-side'>
		    <input tabindex=3 name="login_request" id='request-login' type="submit" value="Login">
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
{if $debug}

<div id="fb-root"></div>

<div id='facebook-login' class='glassbox'>

  <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
  </fb:login-button>

  <div id="status">
  </div>

  <div id='progress'>
  <progress></progress>
  </div>

  <p>
    <small>We never post anything to facebook without your express permission.</small>
  </p>

</div>

{/if}

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




<script>
var debug = {if $debug}true{else}false{/if};
{literal}

// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response) {
  console.log('statusChangeCallback');
  console.log(response);
  // The response object is returned with a status field that lets the
  // app know the current login status of the person.
  // Full docs on the response object can be found in the documentation
  // for FB.getLoginStatus().
  if (response.status === 'connected') {
    // Logged into your app and Facebook.
    syncToNW();
  } else if (response.status === 'not_authorized') {
    // The person is logged into Facebook, but not your app.
    document.getElementById('status').innerHTML = 'Please try again to log into the facebook ninjawars app.';
  } else {
    // The person is not logged into Facebook, so we're not sure if
    // they are logged into this app or not.
    document.getElementById('status').innerHTML = 'Please try again to log into Facebook.';
  }
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState() {
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}

window.fbAsyncInit = function() {
  FB.init({
    appId      : '30479872633',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.0' // use version 2.0
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
};

if(debug){
  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
}

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
// Logged in to the other services, so try to sync up via facebook account id as well.
function syncToNW() {
  console.log('Welcome!  Fetching your information.... ');
  FB.api('/me', function(response) {
    console.log('Successful login for: ' + response.name);
    document.getElementById('status').innerHTML =
      "You're logged in to facebook, " + response.name + ', so we\'re now trying to log you in to this site!';
    // Redirect to homepage after delay.
    console.log(response.id, response.email, response.name);
    console.log(response);
    if(response){
      var url = '/api.php?type=facebook_login_sync&callback=?';
      $.getJSON(url, function(json){
        var logged_in = json.logged_in;
        var error = json.error;
        var redirect = json.redirect;
        console.log('Results of JSON call: ', json, logged_in, error, redirect);
        if(logged_in && !error && redirect){
          window.location.href = redirect;
        } else {
          document.getElementById('status').innerHTML =
      "Error A77: Sorry, there was a problem logging you in with facebook, please refresh the page. ";
        }
      });
    }
  });
}
</script>
{/literal}