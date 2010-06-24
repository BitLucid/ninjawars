<html><body>


<h1>Thank you for creating a ninja in Ninja Wars.</h1>

<h2>This your automated email for the account you created at Ninjawars.net. </h2>

	<p>
	Any automated emails you ask to receive from the game will come from this address.
	</p>
	
	<p>
	Please click on the link below to confirm your account if it isn't confirmed already:<br>
	<a href='{$templatelite.const.WEB_ROOT}confirm.php?username={$send_name|escape:'url'}&confirm={$confirm}'>Confirm Account</a><br>
	Or paste this link:<br>
	{$templatelite.const.WEB_ROOT}confirm.php?username={$send_name|escape:'url'}&confirm={$confirm} <br>
	into your browser.
	</p>
	
	<p>
	If you require help use the forums at {$templatelite.const.WEB_ROOT}forum/<br>
	or email the site administrators at: {$templatelite.const.SUPPORT_EMAIL}
	</p>
	
	<p>
	<b>Account Info</b><br>
	Username: {$send_name}<br>
	Level: 1<br>
	Password: ***yourpassword***<br>
	Class: {$send_class} Ninja
	</p>
	
	<div style='background-color:black'>
	    <img alt='NinjaWars' src='http://www.ninjawars.net/images/ninjawars_title.png'>
	</div>
	
	
</html>
</body>
