<?php
require_once(LIB_ROOT."specific/lib_player_list.php");

$progression = render_template('progression.tpl', array('user_id'=>get_user_id()));

render_page('main.tpl', 'Welcome to Ninjawars', get_certain_vars(get_defined_vars(), array()), $options=array(
        'skip_quickstat'=>true,
        'alive'=>false,
        'private'=>false,
        'quickstat'=>false
));
?>
