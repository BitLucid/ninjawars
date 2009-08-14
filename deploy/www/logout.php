<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Log out";

include SERVER_ROOT."interface/header.php";

logout_user($echo=true, $redirect=false); // From lib_auth (for authenticate)

include SERVER_ROOT."interface/footer.php";
?>

