<?php

// Eventually this page will simply be rerouted through apache to the static page system.

init(false, false);

$page = 'tutorial';
$pages = array('tutorial'=>array('title'=>'FAQ', 'callback'=>'tutorial_page_vars', 'template'=>'tutorial.tpl'));


/* CALLBACK FUNCTIONS */
function tutorial_page_vars(){
     $progression = render_template('progression.tpl', array('user_id'=>get_user_id()));
     return array('progression'=>$progression);
}



display_static_page($page, $pages, $vars=array(), $options=array());

?>
