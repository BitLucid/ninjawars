<?php
namespace NinjaWars\core\extensions;

use \Smarty;

/**
 * Wrap smarty and do a little bit more
 */
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