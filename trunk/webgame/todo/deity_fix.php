<?php
include "interface/header.php";
?>

<span style="font-weight: bold;color: red;">Deity Patch</span>

<?php
$l_result= $sql->Query("repair TABLE players");
$l_result= $sql->Query("repair TABLE inventory");
$l_result= $sql->Query("repair TABLE admins");
$l_result= $sql->Query("repair TABLE mail");

include "interface/footer.php";
?>

