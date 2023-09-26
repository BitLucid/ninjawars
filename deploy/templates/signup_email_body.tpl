<html>

<body>

  {include file="email.header.tpl" title='Thanks for creating a ninja on Ninja Wars' subtitle='This is your automated email for the account you created at Ninjawars.net.'}

  <p>
    <div><em class='subtitle'>First things first, confirm your account!</em></div>
    Please click on the link below to confirm your account if it isn't confirmed already:<br>
    <a href='{$smarty.const.WEB_ROOT}assistance/confirm/?aid={$account_id|escape:'url'}&amp;confirm={$confirm}'>Confirm
      Account</a><br>
    Or paste this link:<br>
    {$smarty.const.WEB_ROOT}assistance/confirm/?aid={$account_id|escape:'url'}&amp;confirm={$confirm} <br>
    into your browser.
  </p>

  <p>
    Any automated emails you ask to receive from the game will come from this address.
  </p>

  <div class='centered padded' style='text-align:center;padding:0.5rem 2rem;'>
    <div class='mostly' style='width:90%;'>

      <h2>Account Info</h2>
      <p>
        Email/Username: <b>{$signup_email}</b><br>
        Password: <b>***your password***</b>
      </p>

    </div>
  </div>

  <div class='centered padded' style='text-align:center;padding:0.5rem 2rem;'>
    <div class='mostly' style='width:90%;'>
      <h2>Ninja Info</h2>
      <p>
        Ninja Name: <b>{$send_name}</b><br>
        Ninja Type: <b>{$send_class} Ninja</b><br>
        Level: 1
      </p>
    </div>
  </div>

  <p>
    If you require help use the forum at <a
      href='https://www.facebook.com/ninjawars.net/'>https://www.facebook.com/ninjawars.net/</a><br>
    or email the site administrators at: <a
      href='mailto:{$smarty.const.SUPPORT_EMAIL}'>{$smarty.const.SUPPORT_EMAIL}</a>
  </p>

  <p>
    Have fun, and see you in the game.
  </p>

  {include file="email.footer.tpl"}

</body>

</html>
