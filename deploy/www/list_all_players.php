<?php
$query = $_SERVER['QUERY_STRING'];
redirect('list.php'.($query? '?'.$query : ''));

?>
