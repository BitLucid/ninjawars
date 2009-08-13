<?php
$page_title = "Deleting Mail";
$quickstat  = false;
$private    = false;
$alive      = false;

include "interface/header.php";
?>
  
<span style="font-weight: bold;color: #ccb094;">Deleting Mail</span>

<br /><br />

<?php
$sql->QueryRow("SELECT send_to, count(send_to) FROM mail LEFT JOIN players ON send_to = uname WHERE confirmed = 0 OR (uname IS NULL AND send_to <> 'beagle' AND send_to <> 'tchalvak'  AND send_to <> 'SysMsg' AND send_to <> 'ChatMsg') GROUP BY send_to");

$sql2 = new DBAccess();

$sql2->Create("esfmovi_ninjawars");

for ($i = $sql->rows - 1; $i >= 0; --$i)
{
	$sql->Fetch($i);
	$username = $sql->data[0];
	//echo $username." ".$sql->data[1]."\n";
	$sql2->Delete("DELETE FROM mail WHERE send_to='".pg_escape_string($username)."'");
	echo "deleted ".$sql2->a_rows."<br />\n";
}

include "interface/footer.php";
?>

