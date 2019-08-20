<?php
namespace NinjaWars\core\control\util;

require_once(dirname(__DIR__).'../../../lib/base.inc.php');

use \Smarty;

// Utility procedural function that runs a clear of the smarty template cache.
function clear_cache(){
	$smarty = new \Smarty;
	return $smarty->clearAllCache();
}

clear_cache();