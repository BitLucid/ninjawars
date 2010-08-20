<?php
// Eventually this page will simply be rerouted through apache to the static page system.

init(false, false);

$page = 'tutorial';
$pages = array('tutorial'=>array('title'=>'FAQ', 'template'=>'tutorial.tpl'));

display_static_page($page, $pages, $vars=array('user_id'=>get_user_id()), $options=array());
?>
