<?php
// Eventually this page will simply be rerouted through apache to the static page system.

init(false, false);

$show_faqs = in('show_faqs');

$page = 'main';
$pages = array('main'=>array('title'=>'Live by the Sword', 'template'=>'main.tpl'));

display_static_page($page, $pages, $vars=array('user_id'=>get_user_id(), 'show_faqs'=>$show_faqs), $options=array());
?>
