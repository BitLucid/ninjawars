<?php
require_once(realpath(__DIR__).'/resources.php');
// This file is very raw to be about as simple as possible of an app status check
ob_start();
assert(defined('WEB_ROOT'));
assert(defined('DEBUG'));
assert(defined('ROOT'));
assert(defined('SERVER_ROOT'));
assert(defined('DATABASE_PASSWORD'));
assert('' !== WEB_ROOT);
assert('http:///' !== WEB_ROOT);

// Check for webserver
$out = ob_get_contents();
ob_end_clean();
echo $out;

require(SERVER_ROOT.'lib/base.inc.php');
require_once(VENDOR_ROOT.'autoload.php');

// Check for database
$connected = (bool) query_item('select 1 from players limit 1');
$is_superuser = (bool) query_item('select usesuper from pg_user where usename = CURRENT_USER;') === true;

function passfail($passed, $pass, $fail){
    $messaging = ($passed? '[PASSING]: Reason '.$pass : '[FAILING]: Reason '.$fail);
    echo "$messaging\n";
    return $passed;
}

// Executing and outputing checks, to try to run all before final return
$outcomes = [
    passfail(!empty($out), 'Contacted some running webserver at '.WEB_ROOT, 'Unable to get any content running at '.WEB_ROOT),
    passfail($connected, 'Able to connect and list a player from the players table of the database', 'Unable to select from players table of the database'),
    passfail(!$is_superuser, 'Connected to database as appropriate user level', 'Connected as database superuser, you want to connect as a lower permission role')
];

return (($outcomes[0] && $outcomes[1] && $outcomes[2])? 0 : 1); // Reversed logic due to linux script return values expected