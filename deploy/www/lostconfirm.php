<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Lost Confirmation";

include SERVER_ROOT."interface/header.php";
?>
  
<span class="brownHeading">Lost Confirm</span>

<p>

<form action="lostconfirm_mod.php" method="post">
Please add <strong><?php echo SYSTEM_MESSENGER_EMAIL; ?></strong> to your safe email senders list of your email account before requesting the confirmation code again.<br/>
Please submit your email address and we will resend a confirmation.
<input id="email" type="text" name="email" class="textField">
<br>
<input type="submit" value="Resend Confirm Code" class="formButton">
</form>

<hr>

<?php
include SERVER_ROOT."interface/footer.php";
?>

