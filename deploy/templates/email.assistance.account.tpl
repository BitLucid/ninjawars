{include file="email.header.tpl" title='Ninjawars Account Info Request' subtitle=''}

You have requested your info for the account with email: {$active_email}.<br><br>

           <b>Account Info</b><br>
           NinjaName: {$lost_uname}<br>
           Level: {$level}<br>
           You can use either your email or ninja name to log in with your password.<br>
           (<a href='{$smarty.const.WEB_ROOT}assistance'>Forgot your password?  Reset it</a>)<br>
           {if !$confirmed}
           Your account has not yet been confirmed, request a reconfirmation email at:
           <a href='{$smarty.const.WEB_ROOT}assistance'>The Account Assistance Page</a>, or copy and paste the url:
           {$smarty.const.WEB_ROOT}assistance/
           <br>
           {/if}

           If you require any further help, email: {$smarty.const.SUPPORT_EMAIL}
           <br>
           <br>
If you did not request this reminder email, please contact us at {$smarty.const.SUPPORT_EMAIL} .

{include file="email.footer.tpl"}
