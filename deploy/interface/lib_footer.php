<?php

// Returns the rendered footer template.
function render_footer($specific_quickstat=null, $skip_quickstat=null){
	$quickstat = null;

	if (!$skip_quickstat) {
		$quickstat = (isset($specific_quickstat) ? $specific_quickstat : null);
	}

	return render_template('footer.tpl', array("quickstat"=>$quickstat));
}


?>
