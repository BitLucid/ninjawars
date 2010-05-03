<?php
$private    = false;
$alive      = false;
$page_title = "Staff";
$quickstat  = false;

function mailtolink($email, $formal=null, $subject=null){
    $formal_sec = ($formal? "'$formal' " : "");
    return "<a href=\"mailto:$formal_sec<$email>".($subject? '?subject='.rawurlencode($subject) : '')."\">".out($formal)." &lt;$email&gt;</a>";
}

$mailto = mailtolink(SUPPORT_EMAIL, SUPPORT_EMAIL_FORMAL_NAME, 'NinjaWars question: ');

render_page('staff.tpl', 'Ninjawars Staff', get_certain_vars(get_defined_vars(), array()), $options=array(
        'skip_quickstat'=>true,
        'alive'=>false,
        'private'=>false,
        'quickstat'=>false
));
?>
