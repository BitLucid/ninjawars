{include file="email.header.tpl" title='Your Ninjawars Confirmation' subtitle=''}

You have requested your confirmation code for the account: {$lost_uname}<br><br>

Use this link to activate your account<br><br>
<b>Account Info</b><br>
Username: {$lost_uname}<br><br>
<a href='{$smarty.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&amp;confirm={$lost_confirm}'>Activate Account</a><br><br>

Or, paste this URL into your browser.<br><br>

{$smarty.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&confirm={$lost_confirm}<br><br>
If you require any further help, email: {$smarty.const.SUPPORT_EMAIL}

{include file="email.footer.tpl"}
