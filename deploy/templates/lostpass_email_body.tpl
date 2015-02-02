{include file="email.header.tpl" title='Ninjawars Account Info Request' subtitle=''}

You have requested your account info for the account: {$lost_uname}.<br><br>
           
           <b>Account Info</b><br>
           Username: {$lost_uname}<br>
           Password: ***your password***<br>
           {if !$confirmed}
           
           Your account has not yet been confirmed, request a reconfirmation email at:
           <a href='{$smarty.const.WEB_ROOT}account_issues.php'>Account Issue Page</a>, or copy and paste the url:
           {$smarty.const.WEB_ROOT}account_issues.php
           
           {/if}
           If you require any further help, email: {$smarty.const.SUPPORT_EMAIL}
           
           
If you did not request this reminder email, please contact us at {$smarty.const.SUPPORT_EMAIL} .

{include file="email.footer.tpl"}	
