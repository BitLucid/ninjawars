<?php

init(); // Initializes a full environment.

$progression = render_template('progression.tpl', array());

render_page('about.tpl', 
        'About NinjaWars', 
        get_certain_vars(get_defined_vars(), array('progression')), 
        $options=array('quickstat'=>false, 'private'=>false, 'alive'=>false)); 
?>
