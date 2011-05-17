<?php
// Deprecated script for backwards compatibility.
error_log('Deprecated linking to attack_player.php url performed from referrer: '.$_SERVER['HTTP_REFERER']);
$query = $_SERVER['QUERY_STRING'];
$new_url = 'map.php'.$query;
permanent_redirect($new_url);
?>
