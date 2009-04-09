<?php
$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Duel Log";

include "interface/header.php";
?>

<span class="brownHeading">Past Duels: Reset Nightly</span>

<br /><br />

<?php

$sql->QueryRow("SELECT * FROM dueling_log ORDER BY id DESC LIMIT 500");
$row = $sql->data;

if ($sql->rows == 0)
{
  echo "Duel log has reset\n";
}

echo "<table style=\"border:1 solid #000000;\">\n";
echo "<tr>\n";
echo "  <th>\n";
echo "  Duel Log\n";
echo "  </th>\n";
echo "</tr>\n";

for ($i = 0; $i < $sql->rows; $i++)
{
  $sql->Fetch($i);
  $id = $sql->data[0];
  $attacker = $sql->data[1];
  $defender = $sql->data[2];
  $wonorlost = $sql->data[3];
  $killpoints = $sql->data[4];
  $date = $sql->data[5];

  if ($wonorlost==1) {$wonorlost="won";}
  else {$wonorlost="lost";}

  echo "<tr>\n";
  echo "  <td valign=\"top\">\n";
  echo "$attacker has dueled $defender and $wonorlost for $killpoints killpoints on $date\n";
  echo "  </td>\n";
  echo "</tr>\n";
}
echo "</table>\n";
echo "</form>\n";


include "interface/footer.php";
?>
