<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "FAQ";

init();


$progression = render_template('progression.tpl', array('user_id'=>get_user_id()));

// Gets passed to the later template.

display_page('tutorial.tpl', $page_title, get_certain_vars(get_defined_vars()), $options=array('quickstat'=>$quickstat, 'alive'=>$alive, 'private'=>$private))

?>
