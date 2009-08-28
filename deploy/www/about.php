<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "About NinjaWars";

include SERVER_ROOT."interface/header.php";

echo render_template('about.tpl');

include SERVER_ROOT."interface/footer.php";
?>
