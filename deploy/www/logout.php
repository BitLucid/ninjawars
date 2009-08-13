<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Log out";

include "interface/header.php";

logout_user($echo=true, $redirect=false); // From lib_auth (for authenticate)

include "interface/footer.php";
?>

