You have requested your password for the account: {$lost_uname}.<br>\n<br>\n
           
           <b>Account Info</b><br>\n
           Username: {$lost_uname}<br>\n
           Password: {$lost_pname}<br>\n<br>\n
           {if !$confirmed}
           
           Your account has not yet been confirmed, request a reconfirmation email at:
           <a href='{$WEB_ROOT}account_issues.php'>Account Issue Page</a>, or copy and paste the url:
           {$WEB_ROOT}account_issues.php
           
           {/if}
           If you require any further help, email: {$SUPPORT_EMAIL}
           
           
If you did not request this reminder email, please contact us at {$SUPPORT_EMAIL} .
