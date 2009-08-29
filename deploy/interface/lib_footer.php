<?php

// Returns the rendered footer template.
function render_footer($specific_quickstat=null, $skip_quickstat=null){
	global $global_quickstat; // Pull in global quickstat if any.
	$quickstat = null;
	if(!$skip_quickstat){
		$quickstat = (isset($specific_quickstat)? $specific_quickstat : $global_quickstat);
	}

	return render_template('footer.tpl', array("quickstat"=>$quickstat));
}

?>
