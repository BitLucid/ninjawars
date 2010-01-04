<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "Settings";

include SERVER_ROOT."interface/header.php";

$settings = get_settings();
//var_dump($settings);
// TODO: Add a "don't use javascript" setting, mainly for the chat iframe.

$parts = get_certain_vars(get_defined_vars(), array());
echo render_template('settings.tpl', $parts);

include SERVER_ROOT."interface/footer.php";

?>
