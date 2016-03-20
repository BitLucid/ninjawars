<style>
.assistance-area{
    margin-left:1em;
}
</style>
<h1>Account Assistance</h1>
<section class='assistance-area'>
{if $error}
    <p class="error">
    {if $error eq 'invalidemail'}
        The submitted email was invalid.
    {elseif $error eq 'nouser'}
        No user with that email exists.
        Please <a href="/signup">sign up</a> for an account, or <a href="/staff">contact us</a> if you have other account issues.
    {elseif $error eq 'alreadyconfirmed'}
        That account is already confirmed. If you are having problems logging in, please <a href="/staff">Contact Us</a>.
    {elseif $error eq 'emailfail'}
        There was a problem sending to that email.
    {/if}
    </p>
{elseif $password_request}
        <h2>Sending Account Information Email</h2>
        <div class='parent'>
            <div class='child bg-success inline-block'>
            Your account information has been resent to your email.
            </div>
        </div>
{elseif $confirmation_request}
        <h2>Resending Confirmation Email</h2>
        <div class='parent'>
            <p class='child bg-success inline-block'>A confirmation email for {$username} has been resent to your email address.</p>
        </div>
{/if}

{if !$password_request && !confirmation_request}
    <p class='notice'>
        Please add <strong>{$smarty.const.SYSTEM_EMAIL}</strong> to the safe email senders list
        of your email account before resending these email requests, to ensure they won't be caught as spam.
    </p>
{/if}

    <h2>Need your password reset?</h2>
    <div class='parent'>
        <div class='child'>
            <a href='/resetpassword.php' class='btn btn-primary'>Request A Password Reset</a>
        </div>
    </div>


    <h2>Resend Confirmation Email</h2>
    <div class='parent'>
        <form class='child' action="/assistance" method="post">
            <p>Submit your email address and we will resend your confirmation email:</p>
            <input id="email" type="email" title="Your account email" placeholder='you@gmail.com' maxlength="50" name="email" class="textField">
            <input type='hidden' name='confirmation_request' value='1'>
            <button type="submit" value="Resend Confirm Code" class="formButton">Resend Confirm Code</button>
        </form>
    </div>


    <h2>Resend Account Information</h2>
    <div class='parent'>
        <form class='child' action="/assistance" method="post">
            <p>Submit your account email to check for existing ninja name, class, and level information:</p>
            <input id="email" type="email" title="Please enter the email you set your account up with" maxlength="50" name="email" class="textField" placeholder='you@gmail.com'>
            <input type='hidden' name='password_request' value='1'>
            <button type="submit" value="Resend Account Info" class="formButton">Resend Account Info</button>
        </form>
    </div>

    <nav>
        <a href="/assistance">Return to the Account Assistance Page</a><br>
        <a href="/">Return to the Ninjawars Homepage</a>
    </nav>
    <h3>For Other Issues</h3>
        <p>For other issues, please <a href='/staff'>contact us</a>.<p>

</section>
