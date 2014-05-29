<?php
// Require the template engine.
//require_once(LIB_ROOT.'third-party/template_lite/src/class.template.php');
// See: http://templatelite.sourceforge.net/docs/index.html for the docs, it's a smarty-like syntax.

class NWTemplate extends Smarty {
	public function __construct() {
		parent::__construct(); // Extending smarty somewhat, not even sure why we do this any more...

		// template directory
		$this->addTemplateDir(TEMPLATE_PATH);

		// compile directory
		$this->setCompileDir(COMPILED_TEMPLATE_PATH);
		$this->setCacheDir(COMPILED_TEMPLATE_PATH); // Also keep cached files in the compiled directory.

		// plugin directory
		$this->addPluginsDir(TEMPLATE_PLUGIN_PATH);

		$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
		//$this->debugging = defined('DEBUG') && DEBUG? true : false;
		// Unused config directory
		//$this->addConfigDir(TEMPLATE_PATH."config/");
		// No configs directory set, because we don't actually use it.
		//$this->testInstall();die();
	}

	public function fullDisplay() {
		$this->display('full_template.tpl');
	}

	public function assignArray($p_vars) {
		foreach ($p_vars as $lname => $lvalue) { // *** loop over the vars, assigning each to the template ***
			$this->assign($lname, $lvalue);
		}
	}
}

// Displays blocking states like not logged in, death, frozen, etc.
function display_error($p_error) {
	display_page('error.tpl', 'There is an obstacle to your progress...', array('error'=>$p_error));
}

// Assigns the environmental variables and then returns just a raw template object for manipulation & display.
function prep_page($template, $title=null, $local_vars=array(), $options=null) {
    // Updates the quickstat via javascript if requested.
    $quickstat = @$options['quickstat'];
    $quickstat = ($quickstat ? $quickstat : @$local_vars['quickstat']);

	$is_index = @$options['is_index'];

	// *** Initialize the template object ***
	$tpl = new NWTemplate();
	$tpl->assignArray($local_vars);

    $user_id = self_char_id(); // Character id.
    $public_char_info = public_self_info(); // Char info to pass to javascript.

	$tpl->assign('logged_in', $user_id);
	$tpl->assign('user_id', $user_id);
	$tpl->assign('title', $title);
	$tpl->assign('quickstat', $quickstat);
	$tpl->assign('is_index', $is_index);
	$tpl->assign('json_public_char_info', ($public_char_info ? json_encode($public_char_info) : null));
	$tpl->assign('main_template', $template);

	return $tpl;
}

/** Displays a template wrapped in the header and footer as needed.
  *
  * Example use:
  * display_page('add.tpl', 'Homepage', get_current_vars(get_defined_vars()), array());
**/
function display_page($template, $title=null, $local_vars=array(), $options=null) {
	prep_page($template, $title, $local_vars, $options)->fullDisplay();
}

/** Will return the rendered content of the template.
  * Example use: $parts = get_certain_vars(get_defined_vars(), array('whitelisted_object');
  * echo render_template('account_issues.tpl', $parts);
**/
function render_template($template_name, $assign_vars=array()) {
	// Initialize the template object.
	$tpl = new NWTemplate();
	$tpl->assignArray($assign_vars);

	// call the template
	return $tpl->fetch($template_name);
}

function display_template($template_name, $assign_vars=array()){
	// Initialize the template object.
	$tpl = new NWTemplate();
	$tpl->assignArray($assign_vars);

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
		cache_headers(24); // 24 hour caching.
		display_page($template, $title, $vars, $options);
	}
}

// Put out the headers to allow a few hours of
function cache_headers($hours = 2, $revalidate=false){
	// Enable short number-of-hours caching of the index page.
	// seconds, minutes, hours, days
	$expires = 60*60*$hours;
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires.($revalidate? ", must-revalidate" : ''));
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
}
?>
