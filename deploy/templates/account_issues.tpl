{if $error}
    <p class='error'>{$error}</p>
{elseif $password_request}
        <h1>Resending Account Information Email</h1>
        <p>Your account information has been resent to your email.</p>
    <hr>
    <a href="main.php">Return to the Ninjawars Intro</a>
{elseif $confirmation_request}
        <h1>Resending Confirmation Email</h1>
        <p>A confirmation email for {$username} has been resent to your email address.</p>        
    <hr>
    <a href="main.php">Return to the Ninjawars Intro</a>
{else}
    <h1>Account Issues</h1>

    <p class='notice'>
      Please add <strong>{$templatelite.const.SYSTEM_MESSENGER_EMAIL}</strong> to the safe email senders list 
      of your email account before resending these email requests, to ensure they won't be caught as spam.
    </p>
    <h2>Resend Account Information</h2>
    <form action="account_issues.php" method="post">
    <p>Submit your account email and your account information will be sent to it:</p>
        <input id="email" type="text" maxlength="50" name="email" class="textField">
        <input type='hidden' name='password_request' value='1'>
        <button type="submit" value="Resend Account Info" class="formButton">Resend Account Info</button>
    </form>
    <h2>Resend Confirmation Email</h2>

    <form action="account_issues.php" method="post">
        <p>Submit your email address and we will resend your confirmation email:</p>
        <input id="email" type="text" maxlength="50" name="email" class="textField">
        <input type='hidden' name='confirmation_request' value='1'>
        <button type="submit" value="Resend Confirm Code" class="formButton">Resend Confirm Code</button>
    </form>

{/if}

    <h3>For Other Issues</h3>
        <p>For other issues, please <a href='{$templatelite.const.WEB_ROOT}staff.php'>contact us</a>.<p>
