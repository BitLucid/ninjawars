<?php
require_once(realpath(__DIR__) . '/resources.php');
// This file is very raw to be about as simple as possible of an app status check
ob_start();
assert(defined('WEB_ROOT'));
assert(defined('DEBUG'));
assert(defined('ROOT'));
assert(defined('SERVER_ROOT'));
assert(defined('DATABASE_PASSWORD'));
assert('' !== WEB_ROOT);
assert('http:///' !== WEB_ROOT);

// Check for webserver root configuration
$out = ob_get_contents();
ob_end_clean();
echo $out;

require(SERVER_ROOT . 'lib/base.inc.php');
require_once(VENDOR_ROOT . 'autoload.php');

function passfail($passed, $pass, $fail)
{
    $messaging = ($passed ? '[PASSING]: Reason ' . $pass : '[FAILING]: Reason ' . $fail);
    echo "$messaging\n";
    return $passed;
}

// Executing and outputing checks, to try to run all before final return
$outcomes = [
    passfail(empty($out), 'WEB ROOT was configured as ' . WEB_ROOT, 'No web root seems to be configured ' . WEB_ROOT),
];

return (($outcomes[0]) ? 0 : 1); // Reversed logic due to linux script return values expected
