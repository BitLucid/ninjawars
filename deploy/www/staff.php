<?php
$private    = false;
$alive      = false;
$page_title = "Staff";
$quickstat  = false;

//include SERVER_ROOT."interface/header.php";


function mailtolink($email, $formal=null, $subject=null){
    $formal_sec = ($formal? "'$formal' " : "");
    return "<a href=\"mailto:$formal_sec<$email>".($subject? '?subject='.rawurlencode($subject) : '')."\">".out($formal)." &lt;$email&gt;</a>";
}

$mailto = mailtolink(SUPPORT_EMAIL, SUPPORT_EMAIL_FORMAL_NAME, 'NinjaWars question: ');

$header = render_html_for_header('Ninjawars Staff', 'ninjawars-staff');

$footer = render_footer();

$parts = get_certain_vars(get_defined_vars());

echo render_template('staff.tpl', $parts);

?>
