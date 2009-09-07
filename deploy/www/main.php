<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Main";

$header = render_html_for_header('Main', 'main-intro');
$footer = render_footer($quickstat);
echo render_template('main.tpl', array('WEB_ROOT' => WEB_ROOT, 'header'=>$header, 'footer'=>$footer));

?>
