<?php
// Require the template engine.
require_once(LIB_ROOT.'templates/template_lite/src/class.template.php');

// Will return the rendered content of the template.
function render_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = new Template_Lite;
	// have to set the proper permissions on these directories.
	// template directory TODO: add to derived constants or resources
	$tpl->template_dir = TEMPLATE_PATH;
	// compile directory TODO: add to derived constants or resources
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach($assign_vars as $lname => $lvalue){
		$tpl->assign($lname, $lvalue);
	}
	// Start buffering.
	ob_start();
	// call the template
	$tpl->display($template_name);
	// End buffering & return the rendered template.
	$rendered = ob_get_contents();
	ob_end_clean();
	return $rendered;
}
?>
