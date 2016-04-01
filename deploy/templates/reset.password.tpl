<section class='glassbox'>
<h1>Reset your password</h1>

{if $error}
    <div class='parent'>
        <div class='child error'>{$error|escape}</div>
    </div>
{/if}

    <form name='new_password_form' method="POST" action="/password/post_reset/">
        {* // TODO needs csrf field *}
        <input type="hidden" name="token" value="{$token|escape}">
        <!-- Presence of this token when submitted acts as a request to change -->

        <div>
            Resetting password for account with email: <strong>{$verified_email|escape}</strong>
        </div>

        <div>
            <label>Password
            <input type="password" name="new_password" value='{$new_password|default:''|escape}' required=required minlength=4 title='At least 4 characters' placeholder='Your new password' >
            </label>
        </div>

        <div>
            <label>Confirm Password
            <input type="password" name="password_confirmation" value='{$password_confirmation|default:''|escape}' required=required minlength=4 title='At least 4 characters' placeholder='Repeat new password'>
            </label>
        </div>

        <div>
            <input type='submit' name='reset-password' class='btn btn-primary' value='Reset Password'>
        </div>
    </form>
</section>
<script src='/js/passwords.js'></script>
