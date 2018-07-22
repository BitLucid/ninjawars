<h1>NinjaWars Login</h1>

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


<section class='login-section container'>
  <div class='outer-shade-box'>
    <div class='shade-box'>
      <form id="login-form" class="form-horizontal" action="/login/login_request" method="post">
        <input type="hidden" name="ref" value="{$referrer|escape}" />
          <div class='row'>
          <label>
            <div class='line'>
              <span class='left-side'>Email or ninja name</span>
              <div class='input-group'>
                <span class="input-group-addon"><i class="fas fa-envelope-open fa-lg" aria-hidden="true"></i></span>
                <input tabindex=1 name="user" placeholder='you@email.com or ninja' required type="text" autocomplete='username email' value='{$stored_username|escape}' class='right-side' />
              </div>
            </div>
          </label>
          </div>
          <div class='row top-buffer'>
          <label>
            <div class='line'>
              <div class='input-group'>
                <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                <input tabindex=2 name="pass" placeholder='Password' required type="password" autocomplete='current-password' class='right-side' />
              </div>
            </div>
          </label>
          </div>
          <div class='row top-buffer'>
           <div class='left-side'>
              <input tabindex=3 name="login_request" id='request-login' class='btn btn-vital' type="submit" value="Login">
            </div>
            <div class='left-side'>
              <a tabindex=4 href='/assistance'>forgot?</a>
            </div>
          </div>
      </form>
    </div>
  </div>
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
