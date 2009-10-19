<?php
$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Duel Log";

include SERVER_ROOT."interface/header.php";

$stats          = membership_and_combat_stats($sql);
$vicious_killer = $stats['vicious_killer'];
?>

<h2>Today's Duels: Reset Nightly</h2>

<div id='vicious-killer'>
    Current Fastest Killer: 
    <a id='vicious-killer-menu href='player.php?player=<?php echo $vicious_killer; ?>'>
        <?php echo $vicious_killer; ?>
    </a>
</div>

<?php

$sql->QueryRow("SELECT * FROM dueling_log ORDER BY id DESC LIMIT 500");
$row = $sql->data;

if ($sql->rows == 0)
{
  echo "<p>Duel log has reset</p>";
}

echo "  <h3>Duel Log</h3>";

echo "<ul id='duel-log' style='list-style-type:circle;'>";
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

  echo "<li>";
  echo "$attacker has dueled $defender and $wonorlost for $killpoints killpoints on $date\n";
  echo "</li>";
}
echo "</ul>";


include SERVER_ROOT."interface/footer.php";
?>
