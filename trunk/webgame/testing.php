<?php
if(!DEBUG) {die();}
$nextlevel = in('nextlevel');
$attacker_level = in('attacker_level');
$starting_attackee_health = in('starting_attackee_health');
$killpoints = in('killpoints');
$duel = in('duel');


echo "<a href=\"chart.php\">Upgrade Chart</a><hr />\n";

$MAX_LEVEL = 250;

$nextlevel = getLevel($username) + 1;

if ($upgrade == 1)  // *** If they requested an upgrade ***
{
  if ($nextlevel>$MAX_LEVEL)
    {
      $msg =  "There are no trainers that can teach you beyond your current skill. You are revered among the ninja.<br />\n";
    }
  else if (getKills($username)>=getLevel($username)*5)
    {
      subtractKills($username,(getLevel($username)*5));
      addLevel($username,1);
      addStrength($username,5);
      addTurns($username, 50);
      addHealth($username, 100);
    }
  else
    {
      echo "You do not have enough kills to proceed at this time.<br />\n";
    }
}
?>
