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
.char-list.ninja-notice a{
  font:30px/34px 'GazelleFLFRegular', "edding-780-1","edding-780-2", Charcoal, serif;
}
</style>
{/literal}

<h1 class='account-header'>Account Info for <span class='account-identity'>{$account_info.account_identity|escape}</span></h1>


<div class='char-list ninja-notice clearfix'>
  <a href='stats.php'>View your ninja's info</a>
</div>

<div id='content' class='account-info'>

{if $error}
  <p class='error'>{$error}</p>
{elseif $successMessage}
  <p class='notice'>{$successMessage|escape}</p>
{/if}

{if $command == 'show_confirm_delete_form'}
  <p>Please provide your password to confirm.</p>
  <form method="post" action="account.php" onsubmit="return confirm('Are you sure you want to delete your account?');">
    <div>
      <input id="passw" type="password" maxlength="50" name="passw" class="textField">
      <input type="hidden" name="command" value="delete_account">
      <input type="submit" value="Confirm Delete" class="formButton">
    </div>
  </form>
{elseif $command == 'show_change_password_form'}
<form method="post" action="account.php">
  <div>
    <div>Current Password: <input type="password" maxlength="50" name="passw" class="textField"></div>
    <div>New Password: <input type="password" maxlength="50" name="newpassw" class="textField"></div>
    <div>Confirm New Password: <input type="password" maxlength="50" name="confirmpassw" class="textField"></div>
    <input type="hidden" name="command" value="change_password">
    <input type="submit" value="Change Password" class="formButton">
  </div>
</form>
{elseif $command == 'show_change_email_form'}
<form method="post" action="account.php">
  <div>
    <div>Current Password: <input id="passw" type="password" maxlength="50" name="passw" class="textField"></div>
    <div>New Email: <input type="text" maxlength="500" name="newemail" class="textField"></div>
    <div>Confirm New Email: <input type="text" maxlength="500" name="confirmemail" class="textField"></div>
    <input type="hidden" name="command" value="change_email">
    <input type="submit" value="Change Email" class="formButton">
  </div>
</form>
{/if}

<div class='stats-avatar'>
  Avatar: (change your avatar for your account email at <a href='http://gravatar.com'>gravatar.com</a>) &rarr;
  {include file="gravatar.tpl" gurl=$gravatar_url}
</div>

<div class='full-account-info'>
    <ul id='account-info' class='account-info'>
      <li>Active Email: <strong>{$account_info.active_email|escape}</strong></li>
      <li>Account Created: <time class='timeago' datetime='{$account_info.created_date|escape}'>{$account_info.created_date|escape}</time></li>
      <li>Last Failed Login Attempt: <time class='timeago' datetime='{$account_info.last_login_failure|escape}'>{$account_info.last_login_failure|escape}</time></li>
      <li>Last IP: {$account_info.last_ip|escape}</li>
      {if $oauth}<li>Single-Click login connected to: <b>{$oauth_provider|escape}</b></li>{/if}
    </ul>
</div>

<hr>
<form action='account.php' method='post'>
  <div>
    <input type='hidden' name='command' value='show_change_password_form'>
    <input type='submit' value='Change Your Password' class='formButton'>
  </div>
</form>
<hr>
<form action='account.php' method='post'>
  <div>
    <input type='hidden' name='command' value='show_change_email_form'>
    <input type='submit' value='Change Your Email' class='formButton'>
  </div>
</form>
<hr>

{if !$delete_attempts}
<p><span class='error'>WARNING:</span> Clicking on the button below will terminate your account.</p>
<form action='account.php' method='post'>
  <div>
    <input type='hidden' name='command' value='show_confirm_delete_form'>
    <input type='submit' value='Permanently Remove Your Account' class='formButton'>
  </div>
</form>
{/if}

</div>


<footer id='stats-footer' class='navigation'>
<h3>Assistance</h3>
<p class='glassbox'>
  If you require account help email: <a href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>, 
  <br>
  or just get in touch with us via any means on the <a href='staff.php'>staff page</a>.
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
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1068723773/?value=0&amp;label=jIocCMnc_AEQvdzN_QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
{/literal}
