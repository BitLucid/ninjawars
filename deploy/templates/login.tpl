<style>
{literal}
  .three-bar{
    min-height:100vh;
    display:flex;
    flex-direction:column;
    justify-content: space-between;
  }
  .three-bar > div, .three-bar > section{
    flex:1;
  }
  .grecaptcha-badge { 
    visibility: hidden; 
  }
{/literal}
</style>
<div class='three-bar'>
  <div>
    <h1>NinjaWars Login</h1>

    {if $login_error_message}
        <!-- This section only gets displayed in the event of an incorrect login -->
          <div id='login-error' class="error fade-in" role='alert'>
            {* Unescaped error to allow for links. *}
            {$login_error_message}
          </div>
    {/if}

    {if $authenticated}
    <div class='glassbox'>
      You are already logged in! <a href='/'>Go Fight!</a>
    </div>
    {else}
  </div>


  <section class='login-section container'>
    <div class='outer-shade-box'>
      <div class='shade-box'>
        <form id="login-form" class="form-horizontal" action="/login/login_request" method="post">
          <input type="hidden" name="ref" value="{isset($referrer) && $referrer|escape}" />
            <div class='row'>
            <label>
              <div class='line'>
                <span class='left-side'>Email or ninja name</span>
                <div class='input-group'>
                  <span class="input-group-addon"><i class="fas fa-envelope fa-lg" aria-hidden="true"></i></span>
                  <input 
                    tabindex=1 
                    name="user" 
                    placeholder='you@email.com or ninja' 
                    required 
                    type="text" 
                    autocomplete='username' 
                    value='{isset($stored_username) && $stored_username|escape}' 
                    class='right-side' />
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
              <div class='centered'>
                <input tabindex=3 name="login_request" id='request-login' class='btn btn-vital' type="submit" value="Login">
              </div>
              <div class='centered my-thick'>
                <a tabindex=4 href='/assistance'>forgot?</a>
              </div>
            </div>
        </form>
      </div>
    </div>
  </section>

  {/if}


  <footer id='login-bottom-bar-container'>
    <div id="login-problems-resources">
      <span class="signup-link">
      <a target="main" href="/signup?referrer={isset($referrer) && $referrer|escape}">Become a Ninja!</a> |
      </span>
      <span>
      <a href="/assistance/" target="main" class="blend side">Login or Signup Problems?</a>
      </span>
    </div>
  </footer>
  {* see https://www.google.com/recaptcha/admin/site/692084162/settings *}
  <!-- See staff page for policy information. -->
  <script src="https://www.recaptcha.net/recaptcha/api.js?render={$smarty.const.RECAPTCHA_SITE_KEY}"></script>
  <script src='/js/login.js'></script>
</div>
