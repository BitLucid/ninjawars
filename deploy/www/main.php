<?php
// Eventually this page will simply be rerouted through apache to the static page system.

init($private=false, $alive=false);

$show_faqs = in('show_faqs');

$page = 'main';
$pages = array('main'=>array('title'=>'Start Playing Ninjawars', 'template'=>'main.tpl'));

display_static_page($page, $pages, $vars=array('user_id'=>self_char_id(), 'show_faqs'=>$show_faqs), $options=array());
