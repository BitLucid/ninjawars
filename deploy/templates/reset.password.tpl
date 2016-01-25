<section class='glassbox'>
<h1>Reset your password</h1>

{if $error}
    <div class='parent'>
        <div class='child error'>{$error}</div>
    </div>
{/if}

    <form name='new_password_form' method="POST" action="">
        {* // TODO needs csrf field *}
        <input type="hidden" name="token" value="{$token}">
        <!-- Presence of this token when submitted acts as a request to change -->

        <div>
            Resetting password for account with email: <strong>{$verified_email}</strong>
        </div>

        <div>
            <label>Password
            <input type="password" name="new_password" value='{$new_password|default:''}' required=required minlength=4 title='At least 4 characters' placeholder='Your new password' >
            </label>
        </div>

        <div>
            <label>Confirm Password
            <input type="password" name="password_confirmation" value='{$password_confirmation|default:''}' required=required minlength=4 title='At least 4 characters' placeholder='Repeat new password'>
            </label>
        </div>

        <div>
            <input type='hidden' name='command' value='post_reset'>
            <input type='submit' name='reset-password' class='btn btn-primary' value='Reset Password'>
        </div>
    </form>
</section>
<script src='/js/passwords.js'></script>
