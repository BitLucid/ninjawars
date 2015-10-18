<?php
// Deprecated script for ferretting out continued bad url usages.
error_log('Deprecated linking to attack_npc url performed from referrer: '.$_SERVER['HTTP_REFERER']);
$query = $_SERVER['QUERY_STRING'];
$new_url = 'npc.php?'.$query;
permanent_redirect($new_url);
