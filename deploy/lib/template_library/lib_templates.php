<?php
// Require the template engine.
require_once(LIB_ROOT.'template_library/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.


/** Displays a template wrapped in the header and footer.
  *
  * Example use:
  * echo render_page('add.tpl', get_current_vars(get_defined_vars()), 'Homepage');
**/
function render_page($template, $local_vars, $title=null){
    if($title == null){
        $title = '';
    }
    $res = render_header($title, $title);
    $res .= render_template($template, $local_vars);
    $res .= render_footer();
    return $res;
}



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




/*
 * Pulls out standard vars except arrays and objects.
 * $var_list is get_defined_vars()
 * $whitelist is an array with string names of arrays/objects to allow.
 */
function get_certain_vars($var_list, $whitelist=array())
{
	$non_arrays = array();

	foreach ($var_list as $loop_var_name => $loop_variable) {
		if ( 
			(!is_array($loop_variable) && !is_object($loop_variable)) 
			|| in_array($loop_var_name, $whitelist)) {
			$non_arrays[$loop_var_name] = $loop_variable;
		}
	}

	$constants = get_user_constants();

	// Add in the user defined constants too.
	return $non_arrays + $constants;
}

function get_user_constants() {
	$temp = get_defined_constants(true);
	return $temp['user'];
}
?>
