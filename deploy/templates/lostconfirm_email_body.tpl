{include file="email.header.tpl" title='Your Ninjawars Confirmation' subtitle=''}

You have requested your confirmation code for the account: {$lost_uname}<br>\n<br>\n

Use this link to activate your account<br>\n<br>\n
<b>Account Info</b><br>\n
Username: {$lost_uname}<br>\n<br>\n
<a href='{$templatelite.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&amp;confirm={$lost_confirm}'>Activate Account</a><br>\n<br>\n

Or, paste this URL into your browser.<br>\n<br>\n

{$templatelite.const.WEB_ROOT}confirm.php?aid={$account_id|escape:'url'}&confirm={$lost_confirm}<br>\n<br>\n
If you require any further help, email: {$templatelite.const.SUPPORT_EMAIL}

{include file="email.footer.tpl"}
