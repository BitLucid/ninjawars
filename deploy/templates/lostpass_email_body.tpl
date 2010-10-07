{include file="email.header.tpl" title='Ninjawars Account Password Request' subtitle=''}

You have requested your password for the account: {$lost_uname}.<br><br>
           
           <b>Account Info</b><br>
           Username: {$lost_uname}<br>
           Password: {$lost_pname}<br><br>
           {if !$confirmed}
           
           Your account has not yet been confirmed, request a reconfirmation email at:
           <a href='{$templatelite.const.WEB_ROOT}account_issues.php'>Account Issue Page</a>, or copy and paste the url:
           {$templatelite.const.WEB_ROOT}account_issues.php
           
           {/if}
           If you require any further help, email: {$templatelite.const.SUPPORT_EMAIL}
           
           
If you did not request this reminder email, please contact us at {$templatelite.const.SUPPORT_EMAIL} .

{include file="email.footer.tpl"}	
