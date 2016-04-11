<?php
use NinjaWars\core\control\ApiController;
use NinjaWars\core\Router;

// How to call:  http://nw.local/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10
// http://nw.local/api.php?type=facebook_login_sync&jsoncallback=alert
// Can actually just use a script source for this, e.g.:
// <script src="/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10"></script>

$api = new ApiController();

$result = $api->nw_json(in('type'), first_value(in('jsoncallback'), in('callback')));

Router::render($result);
