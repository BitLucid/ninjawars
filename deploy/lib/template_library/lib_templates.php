<?php
// Require the template engine.
require_once(LIB_ROOT.'template_library/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.


/** Displays a template wrapped in the header and footer.
  *
  * Example use:
  * echo render_page('add.tpl', 'Homepage', get_current_vars(get_defined_vars()));
**/
function display_page($template, $title=null, $local_vars=array(), $options=null) {
    // Updates the quickstat via javascript if requested.
    $quickstat = @$options['quickstat'];
    $quickstat = ($quickstat ? $quickstat : @$local_vars['quickstat']);
    
    // Displays headless html for javascript if requested.
    $section_only = @$options['section_only'];
    $section_only = ($section_only ? $section_only : @$local_vars['section_only']);
    $section_only = ($section_only ? $section_only : in('section_only'));
    
    // Display header and footer only if not section_only.

    if (!$section_only) {
        display_header($title); // Displays the template instead of echoing a rendered template.
    }

	// Initialize the template object.
	$tpl = new Template_Lite;

	// template directory 
	$tpl->template_dir = TEMPLATE_PATH;

	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach ($local_vars as $lname => $lvalue) {
		$tpl->assign($lname, $lvalue);
	}

	$tpl->assign('main_template', $template);
	$tpl->assign('quickstat', $quickstat);

	// the template
	$tpl->display('full_template.tpl');
}


// Wrapper around the display_page function.
function render_page($template, $title=null, $local_vars=array(), $options=null) {
    ob_start();
    display_page($template, $title, $local_vars, $options);
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}


function transitional_display_full_template($template_name, $assigned_vars = array()) {
	// Initialize the template object.
	$tpl = new Template_Lite;

	// template directory 
	$tpl->template_dir = TEMPLATE_PATH;

	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach ($assigned_vars as $lname => $lvalue) {
		$tpl->assign($lname, $lvalue);
	}

	$tpl->assign('main_template', $template_name);
	// the template
	$tpl->display('full_template.tpl');
}


/** Will return the rendered content of the template.
  * Example use: $parts = get_certain_vars(get_defined_vars(), array('whitelisted_object');
  * echo render_template('account_issues.tpl', $parts);
**/
function render_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = new Template_Lite;

	// template directory 
	$tpl->template_dir = TEMPLATE_PATH;

	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach ($assign_vars as $lname => $lvalue) {
		$tpl->assign($lname, $lvalue);
	}

	// call the template
	return $tpl->fetch($template_name);
}

function display_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = new Template_Lite;

	// template directory 
	$tpl->template_dir = TEMPLATE_PATH;

	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// loop over the vars, assigning each.
	foreach ($assign_vars as $lname => $lvalue) {
		$tpl->assign($lname, $lvalue);
	}

	// display the template
	return $tpl->display($template_name);
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

// Get the user defined constants like WEB_ROOT
function get_user_constants() {
	$temp = get_defined_constants(true);
	return $temp['user'];
}
?>
