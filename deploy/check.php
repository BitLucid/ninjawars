<?php
require_once(realpath(__DIR__).'/resources.php');
ob_start();
assert(defined('WEB_ROOT'));
assert(defined('DEBUG'));
assert(defined('ROOT'));
assert(defined('SERVER_ROOT'));
assert(defined('DATABASE_PASSWORD'));
assert('' !== WEB_ROOT);
assert('http:///' !== WEB_ROOT);
$out = ob_get_contents();
ob_end_clean();
// Reverse return values
if($out){
    return 1;
} else {
    echo "Success";
    return 0;
}
