<?php
$error = init($private, $alive); // Creates some starting objects&vars, puts player info into global namespace (sadly), updates activity, starts buffering.

// **************** OUTPUT SECTION *******************************//

echo render_template('header.tpl', array('title'=>$page_title, 'logged_in'=>get_user_id(), 'section_only'=>(in('section_only')==='1')));

if ($error) {
	echo render_template("error.tpl", array('error'=>$error));
	echo render_template('footer.tpl', array("quickstat"=>(isset($quickstat) ? $quickstat : null))); // Display the bottom of the error page, refresh the quickstat view if necessary.
    die(); // Do not display any further on the page.
}
?>
