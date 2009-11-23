<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Main";
require_once(LIB_ROOT."specific/lib_player_list.php");
$header = render_html_for_header('Welcome to Ninjawars', 'main-intro');

$footer = render_footer($quickstat);

$progression = render_template('progression.tpl', array('WEB_ROOT'=>WEB_ROOT, 'IMAGE_ROOT'=>IMAGE_ROOT));

$parts = get_certain_vars(get_defined_vars());
echo render_template('main.tpl', $parts);

?>
