<?php
use NinjaWars\core\extensions\NWTemplate;

/**
 * Will return the rendered content of the template.
 * @todo move to template class
 */
function render_template($template_name, $assign_vars=array()) {
    // Initialize the template object.
    $tpl = new NWTemplate();
    $tpl->assign($assign_vars);

    // call the template
    return $tpl->fetch($template_name);
}
