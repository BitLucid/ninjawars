<?php
// Use a permanent redirect here.
error_log('Deprecated linking to attack_npc url performed '. (isset($_SERVER['HTTP_REFERER']) ? ' from referrer: '.$_SERVER['HTTP_REFERER'] : ' no referer was provided by the UA.'));
$query = $_SERVER['QUERY_STRING'];
$new_url = 'list.php'.$query;
permanent_redirect($new_url);
?>
