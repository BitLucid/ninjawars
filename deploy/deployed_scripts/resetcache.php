<?php

namespace NinjaWars\core\extensions;

require_once(dirname(__DIR__ . '..') . '/lib/base.inc.php');

use NinjaWars\core\extensions\NWTemplate;

// setup our runtime environment
require_once(LIB_ROOT . 'environment/bootstrap.php');


/**
 * Reset the smarty cache
 */
function template_cache_reset()
{
    $view = new NWTemplate();
    $view->clearAllCache();
    echo "Smarty cache reset\n";
}

opcache_reset();
echo "opcache reset\n";
template_cache_reset();
