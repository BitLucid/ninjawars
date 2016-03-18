<?php
use NinjaWars\core\data\Player;

class NWTemplate extends Smarty {
    public function __construct() {
        parent::__construct();
        $this->caching = false; // or Smarty::CACHING_LIFETIME_CURRENT;

        $this->addTemplateDir(TEMPLATE_PATH);

        $this->setCompileDir(COMPILED_TEMPLATE_PATH);

        $this->addPluginsDir(TEMPLATE_PLUGIN_PATH);
    }

    public function fullDisplay() {
        $this->display('full_template.tpl');
    }

    public function assignArray($p_vars) {
        if ($p_vars === null) {
            return;
        }

        foreach ($p_vars as $lname => $lvalue) { // pass each var to the view
            $this->assign($lname, $lvalue);
        }
    }
}

/**
 * Displays blocking states like not logged in, death, frozen, etc.
 * @todo move to template class or the like
 */
function display_error($p_error) {
    display_page('error.tpl', 'There is an obstacle to your progress...', array('error'=>$p_error));
}

/** Displays a template wrapped in the header and footer as needed.
 *
 * Example use:
 * display_page('add.tpl', 'Homepage', get_current_vars(get_defined_vars()), array());
 * @todo move to template class
 */
function display_page($template, $title=null, $local_vars=array(), $options=null) {
    prep_page($template, $title, $local_vars, $options)->fullDisplay();
}

/** Will return the rendered content of the template.
 * Example use: $parts = get_certain_vars(get_defined_vars(), array('whitelisted_object');
 * echo render_template('account_issues.tpl', $parts);
 * @todo move to template class
 */
function render_template($template_name, $assign_vars=array()) {
    // Initialize the template object.
    $tpl = new NWTemplate();
    $tpl->assignArray($assign_vars);

    // call the template
    return $tpl->fetch($template_name);
}

/*
 * Pulls out standard vars except arrays and objects.
 * $var_list is get_defined_vars()
 * $whitelist is an array with string names of arrays/objects to allow.
 * @deprecated in favor of passing specific vars to template
 */
function get_certain_vars($var_list, $whitelist=array()) {
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

/** 
 * Put out the headers to allow a few hours of caching
 * @todo move to template class
 */
function cache_headers($hours = 2, $revalidate=false) {
    // Enable short number-of-hours caching of the index page.
    // seconds, minutes, hours, days
    $expires = 60*60*$hours;
    header("Pragma: public");
    header("Cache-Control: maxage=".$expires.($revalidate? ", must-revalidate" : ''));
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
}
