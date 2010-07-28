<?php
$error = init($private, $alive); // Creates some starting objects&vars, puts player info into global namespace (sadly), updates activity, starts buffering.

// **************** OUTPUT SECTION *******************************//

display_template('header.tpl', array('quickstat'=>$quickstat, 'title'=>$page_title, 'logged_in'=>get_user_id(), 'section_only'=>(in('section_only')==='1')));

if ($error) {
	display_template("error.tpl", array('error'=>$error));
	display_template('footer.tpl'); // Display the bottom of the error page, refresh the quickstat view if necessary.
    die(); // Do not display any further on the page.
}
?>
