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
echo $out;
// How about some includes
require(SERVER_ROOT.'lib/base.inc.php');
require_once(VENDOR_ROOT.'autoload.php');
$connected = (bool) query_item('select 1 from players limit 1');
$is_superuser = (bool) query_item('select usesuper from pg_user where usename = CURRENT_USER;') === true;
if($is_superuser){
	echo 'Running as superuser!  This will hide problems later!';
}
if(!$connected){
	echo 'Unable to select from players table of database!';
}

if((bool)$out || !$connected || $is_superuser){
	echo "Failure";
    return 1;
} else {
    echo "Success";
    return 0;
}
