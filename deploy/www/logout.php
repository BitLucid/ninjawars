<?php
init($private=false, $alive=false);

logout_user($echo=false, $redirect='login.php?logged_out=1'); // logout_user lives in lib_auth
// Logout immediately redirects to login.php to display a "you are now logged out" message.

?>
