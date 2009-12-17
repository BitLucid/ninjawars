<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Lost Confirmation";

include SERVER_ROOT."interface/header.php";
?>

<h1>Lost Confirm</h1>

  <form action="lostconfirm_mod.php" method="post">
      <p>Please add <strong><?php echo SYSTEM_MESSENGER_EMAIL; ?></strong> to your safe email senders list of your email account before requesting the confirmation code again.</p>
      
      <p>Please submit your email address and we will resend a confirmation.</p>
      <input id="email" type="text" name="email" class="textField">
      <button type="submit" value="Resend Confirm Code" class="formButton">Resend Confirm Code</button>
  </form>

<?php
include SERVER_ROOT."interface/footer.php";
?>
