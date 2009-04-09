<?php
include "interface/header.php";
?>
  
<span style="font-weight: bold;color: red;">Deity Patch</span>

<?php
$sql->Update("OPTIMIZE TABLE `mail`");
$sql->Update("OPTIMIZE TABLE `players`");

include "interface/footer.php";
?>

