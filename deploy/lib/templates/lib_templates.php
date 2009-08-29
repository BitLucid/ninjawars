<?php
// Require the template engine.
require_once(LIB_ROOT.'templates/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.

// Will return the rendered content of the template.
function render_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = new Template_Lite;
	// template directory 
	$tpl->template_dir = TEMPLATE_PATH;
	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach($assign_vars as $lname => $lvalue){
		$tpl->assign($lname, $lvalue);
	}
	// call the template
	$rendered = $tpl->fetch($template_name);

	return $rendered;
}
?>
