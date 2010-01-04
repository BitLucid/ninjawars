<?php
require_once(LIB_ROOT."specific/lib_player_list.php");
require_once(LIB_ROOT."specific/lib_player.php");

$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Ninja List";

include SERVER_ROOT."interface/header.php";

echo render_player_tags();

include SERVER_ROOT."interface/footer.php";


?>
