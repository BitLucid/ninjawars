<?php
$private    = false;
$alive      = false;
$page_title = "Staff";
$quickstat  = false;

//include SERVER_ROOT."interface/header.php";



$header = render_html_for_header('Ninjawars Staff', 'ninjawars-staff');

$footer = render_footer();

$parts = get_certain_vars(get_defined_vars());

echo render_template('staff.tpl', $parts);

?>
