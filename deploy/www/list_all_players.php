<?php
// Use a permanent redirect here.
error_log('Deprecated linking to attack_npc url performed from referrer: '.$_SERVER['HTTP_REFERER']);
$query = $_SERVER['QUERY_STRING'];
$new_url = 'list.php'.$query;
permanent_redirect($new_url);

?>
