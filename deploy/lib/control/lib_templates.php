<?php
// Require the template engine.
require_once(LIB_ROOT.'third-party/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.

function display_error($p_error) {
	display_page('error.tpl', 'Error', array('error'=>$p_error));
}

// Assigns the environmental variables and then returns just a raw template object for manipulation & display.
function prep_page($template, $title=null, $local_vars=array(), $options=null) {
    // Updates the quickstat via javascript if requested.
    $quickstat = @$options['quickstat'];
    $quickstat = ($quickstat ? $quickstat : @$local_vars['quickstat']);

    // Displays headless html for javascript if requested.
    $section_only = @$options['section_only'];
    $section_only = ($section_only ? $section_only : @$local_vars['section_only']);
    $section_only = ($section_only ? $section_only : in('section_only'));

	$is_index = @$options['is_index'];

	// *** Initialize the template object ***
	$tpl = createTemplateLite();

	foreach ($local_vars as $lname => $lvalue) { // *** loop over the vars, assigning each to the template ***
		$tpl->assign($lname, $lvalue);
	}

    $user_id = get_user_id(); // Character id.

	$tpl->assign('logged_in', $user_id);
	$tpl->assign('user_id', $user_id);
	$tpl->assign('title', $title);
	$tpl->assign('is_index', $is_index);
	$tpl->assign('section_only', ($section_only === '1'));
	$tpl->assign('quickstat', $quickstat);
	$tpl->assign('main_template', $template);

	return $tpl;
}

// Final step to display a page, takes a template object with the page-level variables and performs the final display.
function display_prepped_template($p_tpl) {
	$p_tpl->display('full_template.tpl');
}


/** Displays a template wrapped in the header and footer as needed.
  *
  * Example use:
  * display_page('add.tpl', 'Homepage', get_current_vars(get_defined_vars()), array());
**/
function display_page($template, $title=null, $local_vars=array(), $options=null) {
	display_prepped_template(prep_page($template, $title, $local_vars, $options));
}

/** Will return the rendered content of the template.
  * Example use: $parts = get_certain_vars(get_defined_vars(), array('whitelisted_object');
  * echo render_template('account_issues.tpl', $parts);
**/
function render_template($template_name, $assign_vars=array()) {
	// Initialize the template object.
	$tpl = createTemplateLite();

	// loop over the vars, assigning each.
	foreach ($assign_vars as $lname => $lvalue) {
		$tpl->assign($lname, $lvalue);
	}

	// call the template
	return $tpl->fetch($template_name);
}

function createTemplateLite() {
	$tpl = new Template_Lite();

	// template directory
	$tpl->template_dir = TEMPLATE_PATH;

	// compile directory
	$tpl->compile_dir = COMPILED_TEMPLATE_PATH;

	// plugin directory
	$tpl->plugin_dir = TEMPLATE_PLUGIN_PATH;

	return $tpl;
}

function display_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = createTemplateLite();

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

function display_static_page($page, $pages, $vars=array(), $options=array()) {
	if (!isset($pages[$page])) {
		// Unlisted page requested.
		error_log('  Invalid page ('.$page.') requested on page.php.');
		display_page('404.tpl', '404');
	} else {
		if (!is_array($pages[$page])) {
			$template = "page.".$page.".tpl";
			$title = $page; // Display_page will prepend with 'Ninja Wars: '
		} else {
			$page_info = $pages[$page];
			$template = first_value(@$page_info['template'], "page.".$page.".tpl");
			$title = $page_info['title'];

			$callback = @$page_info['callback'];
			
			// TODO: Merge the vars array instead of overwriting.
			if ($callback && function_exists($callback)) {
				$vars = array_merge($callback(), $vars); // Call the callback to return the vars.
			}
		}

		display_page($template, $title, $vars, $options);
	}
}
?>
