<html>
  <body>

{include file="email.header.tpl" title='Thanks for creating a ninja on Ninja Wars' subtitle='This is your automated email for the account you created at Ninjawars.net.'}

    <p>
      Any automated emails you ask to receive from the game will come from this address.
    </p>

    <p>
      Please click on the link below to confirm your account if it isn't confirmed already:<br>
      <a href='{$smarty.const.WEB_ROOT}assistance/confirm/?aid={$account_id|escape:'url'}&amp;confirm={$confirm}'>Confirm Account</a><br>
      Or paste this link:<br>
      {$smarty.const.WEB_ROOT}assistance/confirm/?aid={$account_id|escape:'url'}&confirm={$confirm} <br>
      into your browser.
    </p>

      <h3>Account Info</h3>
    <p>
      Email/Username: <b>{$signup_email}</b><br>
      Password: <b>***yourpassword***</b>
    </p>

      <h3>Ninja Info</h3>
    <p>
      Ninja Name: <b>{$send_name}</b><br>
      Ninja Type: <b>{$send_class} Ninja</b><br>
      Level: 1
    </p>

    <p>
      If you require help use the forums at {$smarty.const.WEB_ROOT}forum/<br>
      or email the site administrators at: <a href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>
    </p>

{include file="email.footer.tpl"}

  </body>
</html>
