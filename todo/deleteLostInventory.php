<?php
$page_title = "Deleting Items";
$quickstat  = false;
$private    = false;
$alive      = false;

include "interface/header.php";
?>
  
<span style="font-weight: bold;color: #ccb094;">Deleting Items</span>

<br /><br />

<?php
$sql->QueryRow("SELECT owner FROM inventory LEFT JOIN players ON owner = uname WHERE confirmed = 0 OR uname is null GROUP BY owner");

$sql2 = new DBAccess();

$sql2->Create("esfmovi_ninjawars");

for ($i = $sql->rows - 1; $i >= 0; --$i)
{
	$sql->Fetch($i);
	$username = $sql->data[0];
	//echo $username." ".$sql->data[2]."\n";
	$sql2->Delete("DELETE FROM inventory WHERE owner='".pg_escape_string($username)."'");
	echo "deleted ".$sql2->a_rows."<br />\n";
}

include "interface/footer.php";
?>

