<?php
$page = in('page'); // The requested static page.

/* LIST OF PAGES AND ANY EXTRA CALLBACKS FOR VARS*/
$pages = array(
	'tools'=>'tools'
	,'tutorial'=>array('title'=>'Helpful Info', 'template'=>'page.tutorial.tpl')
);

$vars = array('user_id'=>get_user_id());
$options = array();

/* END OF CALLBACK FUNCTIONS */

display_static_page($page, $pages, $vars, $options);
?>
