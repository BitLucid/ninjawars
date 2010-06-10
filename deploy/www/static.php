<?php

$page = in('page'); // The requested static page.


/* LIST OF PAGES AND ANY EXTRA CALLBACKS FOR VARS*/
$pages = array(
        'tools'=>'tools'
        ,'tutorial'=>array('title'=>'Helpful Info', 'callback'=>'tutorial_page_vars', 'template'=>'page.tutorial.tpl')
    );

$vars = array();
$options = array();


/* CALLBACK FUNCTIONS */
function tutorial_page_vars(){
     $progression = render_progression();
     return array('progression'=>$progression);
}

/* END OF CALLBACK FUNCTIONS */

display_static_page($page, $pages, $vars, $options);

?>
