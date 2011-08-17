<?php
// Redirect to login.php, which by default logs out the user.
logout_user();
redirect("login.php?logged_out=1");
?>
