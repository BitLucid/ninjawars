<?php
require_once(LIB_ROOT.'control/lib_player_list.php');
require_once(LIB_ROOT.'control/lib_api.php');
// How to call:  http://nw.local/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10
// Can actually just use a scrypt source for this, e.g.: 
// <script src="/api.php?type=char_search&jsoncallback=alert&term=tchalvak&limit=10"></script>

// All the functions used by api.php are now in control/lib_api.php

// Json P headers
header('Content-Type: text/javascript; charset=utf8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Max-Age: 3628800');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
$type = in('type');
$dirty_jsoncallback = in('jsoncallback');
echo nw_json($type, $dirty_jsoncallback); // Types are whitelisted, the callback is filtered

// Make sure to default to private, just as a security reminder.