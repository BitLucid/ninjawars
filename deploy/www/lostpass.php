<?php
$page_title = "Lost Password";
$quickstat  = false;
$private    = false;
$alive      = false;

include SERVER_ROOT."interface/header.php";
?>
 
<span class="brownHeading">Lost Password</span>

<p>
Please add <strong><?php echo SYSTEM_MESSENGER_EMAIL; ?></strong> 
to the safe email senders list of your email account before signing up, so you can receive your account email.
<form action="lostpass_mod.php" method="post">
Please submit your email and your account information will be sent to you:<br>
<input id="email" type="text" maxlength="50" name="email" class="textField"><br>
<input type="submit" value="Get Password" class="formButton">
</form>

<hr>

</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>

