<?php
// Require the template engine.
require_once(LIB_ROOT.'template_library/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.

function display_error($p_error) {
	display_page('error.tpl', 'Error', array('error'=>$p_error));
}

/** Displays a template wrapped in the header and footer as needed.
  *
  * Example use:
  * display_page('add.tpl', 'Homepage', get_current_vars(get_defined_vars()), array());
**/
function display_page($template, $title=null, $local_vars=array(), $options=null) {
    // Updates the quickstat via javascript if requested.
    $quickstat = @$options['quickstat'];
    $quickstat = ($quickstat ? $quickstat : @$local_vars['quickstat']);

    // Displays headless html for javascript if requested.
    $section_only = @$options['section_only'];
    $section_only = ($section_only ? $section_only : @$local_vars['section_only']);
    $section_only = ($section_only ? $section_only : in('section_only'));

	$is_index = @$options['is_index'];

	$tpl               = new Template_Lite;      // *** Initialize the template object ***
	$tpl->template_dir = TEMPLATE_PATH;          // *** template directory ***
	$tpl->compile_dir  = COMPILED_TEMPLATE_PATH; // *** compile directory ***

	foreach ($local_vars as $lname => $lvalue) { // *** loop over the vars, assigning each to the template ***
		$tpl->assign($lname, $lvalue);
	}

	$tpl->assign('logged_in', get_user_id());
	$tpl->assign('user_id', get_user_id());
	$tpl->assign('title', $title);
	$tpl->assign('is_index', $is_index);
	$tpl->assign('section_only', ($section_only === '1'));
	$tpl->assign('quickstat', $quickstat);
	$tpl->assign('main_template', $template);

	// the template
	$tpl->display('full_template.tpl');
}

/** Will return the rendered content of the template.
  * Example use: $parts = get_certain_vars(get_defined_vars(), array('whitelisted_object');
  * echo render_template('account_issues.tpl', $parts);
**/
function render_template($template_name, $assign_vars=array()) {
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

	return $non_arrays;
}
?>
