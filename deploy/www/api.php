<?php
use NinjaWars\core\control\ApiController;

// How to call:  http://nw.local/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10
// http://nw.local/api.php?type=facebook_login_sync&jsoncallback=alert
// Can actually just use a script source for this, e.g.:
// <script src="/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10"></script>

$api = new ApiController();
$api->sendHeaders();

$result = $api->nw_json(in('type'), first_value(in('jsoncallback'), in('callback')));

// This is needed to keep output code out of the controller
if ($result === json_encode(false)) {
    header('Content-Type: application/json; charset=utf8');
}

echo $result;
