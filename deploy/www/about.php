<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "About NinjaWars";

include SERVER_ROOT."interface/header.php";

$progression = render_template('progression.tpl', array('WEB_ROOT'=>WEB_ROOT, 'IMAGE_ROOT'=>IMAGE_ROOT));

echo render_template('about.tpl', array($progression));

echo render_footer();
?>
