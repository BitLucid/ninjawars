<?php
$page_title = "Lost Password";
$quickstat  = false;
$private    = false;
$alive      = false;

include "interface/header.php";
?>
 
<span class="brownHeading">Lost Password</span>

<p>

<form action="lostpass_mod.php" method="post">
Please submit your email and your password will be sent to you:<br />
<input id="email" type="text" maxlength="50" name="email" class="textField" /><br />
<input type="submit" value="Get Password" class="formButton" />
</form>

<hr />

</p>

<?php
include "interface/footer.php";
?>

