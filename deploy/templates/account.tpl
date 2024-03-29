<h1 class='account-header'><i class="fas fa-cogs"></i> Account Info for <span class='account-identity'>{$account->identity()|escape}</span></h1>

{literal}
<style>
.account-info{
  margin-right:1em;margin-left:1em;
}
h1.account-header{
  font-family:monospace;
}
h1 .account-identity{
  font-weight:bold;
}
.n-active{
  color:green;
}
.active-area{
  padding: 1em; background-color: rgba(250, 250, 250, 0.7); box-shadow: 0 1px 4px 0 rgba(0,0,0,0.14); color: #000; margin: 1em; border-radius: 0.2em;
  font-family: monospace; font-size:larger;
}
.active-area label{
  display: inline-block; min-width: 15%; text-align: right; padding-right:1em;
}
.account-info .btn-group form{
  display:inline;
}
.account-info form + form + form{
  margin-top:2em;
}
.account-deletion {
  margin: 3rem;
}
</style>
{/literal}

<section id='content' class='account-info'>

{if $error}
  <p class='error'>{$error}</p>
{elseif $successMessage}
  <p class='notice'>{$successMessage|escape}</p>
{/if}

{if $command == 'show_confirm_delete_form' && $delete_attempts < 1}
  <div class='active-area'>
    <p>Please provide your password to confirm.</p>
    <form method="post" action="/account/delete_account" onsubmit="return confirm('Are you sure you want to delete your account?');">
      <div>
        <input id="passw" type="password" maxlength="50" name="passw" class="textField">
        <input type="submit" value="Confirm Delete" class="formButton">
      </div>
    </form>
  </div>
{elseif $command == 'show_change_password_form'}
<div class='active-area'>
  <form method="post" action="/account/change_password">
    <div>
      <div><label>Current Password: </label><input type="password" maxlength="50" name="passw" class="textField"></div>
      <div><label>New Password: </label><input type="password" maxlength="50" name="newpassw" class="textField"></div>
      <div><label>Confirm New Password: </label><input type="password" maxlength="50" name="confirmpassw" class="textField"></div>
      <input type="submit" value="Change Password" class="formButton">
    </div>
  </form>
</div>
{elseif $command == 'show_change_email_form'}
<div class='active-area'>
  <form method="post" action="/account/change_email">
    <div>
      <div><label>Current Password: </label><input id="passw" type="password" maxlength="50" name="passw" class="textField"></div>
      <div><label>New Email: </label><input type="text" maxlength="500" name="newemail" class="textField"></div>
      <div><label>Confirm New Email: </label><input type="text" maxlength="500" name="confirmemail" class="textField"></div>
      <input type="submit" value="Change Email" class="formButton">
    </div>
  </form>
</div>
{/if}

<div class='stats-avatar'>
  {include file="gravatar.tpl" gurl=$gravatar_url} &larr; (change your avatar for your account email at <a rel="noopener noreferrer" target="_blank" href='//gravatar.com'>gravatar.com</a>)
</div>

<div class='full-account-info'>
    <ul id='account-info' class='account-info'>
      <li>Account Identity: <strong>{$account->identity()}</strong></li>
      <li>Active Email: <strong>{$account->active_email|escape}</strong></li>
      <li>Account Created: <time class='timeago' datetime='{$account->created_date|escape}'>{$account->created_date|escape}</time></li>
      <li>Last Failed Login Attempt: <time class='timeago' datetime='{$account->last_login_failure|escape}'>{$account->last_login_failure|escape}</time></li>
      <li>Last IP: <strong>{$account->last_ip|escape}</strong></li>
      <li>Account Karma Gained: <strong>{$account->karma_total}</strong></li>
      <li>Account Type: <strong>{$account->type}</strong></li>
      <li>Account Id: <strong>{$account->id()}</strong></li>
      {if $oauth}<li>Single-Click login connected to: <strong>{$oauth_provider|escape}</strong></li>{/if}
    </ul>

  <div class="btn-group" role="group">
    <form action='/account/show_change_password_form' method='post'>
      <input type='submit' value='Change Your Password' class='btn btn-default formButton'>
    </form>
    <form action='/account/show_change_email_form' method='post'>
      <input type='submit' value='Change Your Email' class='btn btn-default formButton'>
    </form>
  </div>

{if $delete_attempts < 1}
    <div class='account-deletion'>
      <div>
        <form action='/account/show_confirm_delete_form' method='post'>
          <input type='submit' value='Permanently Remove Your Account' class='formButton btn btn-danger'>
        </form>
      </div>
    </div>
{else}
    <div class='error thick'>
      Deletion attempts exceeded, please contact <a href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>
    </div>
{/if}
</div>

<section>
  <h1><i class="fas fa-list"></i> Your Ninjas</h1>
  <div class='glassbox char-list' style="position:relative">
    <div class='ninja-notice' style="position:absolute;top:0;right:0;display:inline-block;">
      <a href='/stats'><i class='fas fa-heart'></i> Current ninja's info</a>
    </div>
    <ul>
    {foreach $ninjas as $ninja}
      <li>
        <span {if $ninja->active }class="n-active"{/if}><i class="fas fa-circle" ></i> </span>
        <a href='/player?player_id={$ninja->id()|escape:'url'|escape}'>{$ninja->name()|escape}</a>  
        <span class='class-name {$ninja->theme|escape}'>{$ninja->class_name|escape}</span> 
        <span class='player-level-category {$ninja->level|level_label|css_classify}'>
          {$ninja->level|level_label} <span class='ninja-level-number'>{$ninja->level|escape}</span>
        </span>
        <span class='health-bar-area' title='Max health: {$ninja->getMaxHealth()|escape}'>
          {include file="health_bar.tpl" health=$ninja->health level=$ninja->level}
        </span> 
        <span class='turns-count'>{$ninja->turns} turns</span>
      </li>
    {/foreach}
    </ul>
  </div>
</section>

</section><!-- end of .account-info -->


<footer id='stats-footer' class='navigation'>
<h3>Assistance</h3>
<p class='glassbox'>
  If you require account help email: <a href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>,
  <br>
  or just get in touch with us via any means on the <a href='/staff'>staff page</a>.
</p>

</footer>

{literal}
<!-- Google Code for View account page Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1068723773;
var google_conversion_language = "en";
var google_conversion_format = "1";
var google_conversion_color = "000000";
var google_conversion_label = "jIocCMnc_AEQvdzN_QM";
var google_conversion_value = 0;
if (0) {
  google_conversion_value = 0;
}
/* ]]> */
</script>
<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1068723773/?value=0&amp;label=jIocCMnc_AEQvdzN_QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
{/literal}
