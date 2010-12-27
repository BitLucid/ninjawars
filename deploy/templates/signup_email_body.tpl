<html>
  <body>

{include file="email.header.tpl" title='Thanks for creating a ninja on Ninja Wars' subtitle='This is your automated email for the account you created at Ninjawars.net.'}

    <p>
      Any automated emails you ask to receive from the game will come from this address.
    </p>

    <p>
      Please click on the link below to confirm your account if it isn't confirmed already:<br>
      <a href='{$smarty.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&amp;confirm={$confirm}'>Confirm Account</a><br>
      Or paste this link:<br>
      {$smarty.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&confirm={$confirm} <br>
      into your browser.
    </p>

    <p>
      If you require help use the forums at {$smarty.const.WEB_ROOT}forum/<br>
      or email the site administrators at: {$smarty.const.SUPPORT_EMAIL}
    </p>

    <p>
      <b>Account Info</b><br>
      Username: {$send_name}<br>
      Level: 1<br>
      Password: ***yourpassword***<br>
      Class: {$send_class} Ninja
    </p>

{include file="email.footer.tpl"}

  </body>
</html>
