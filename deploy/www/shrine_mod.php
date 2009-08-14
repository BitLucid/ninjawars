<?php
$page_title = "Shrine";
$alive      = false;
$private    = true;
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Shrine Effects</span>

<hr>

<?php
$healed      = in('healed');
$poisoned    = in('poisoned');
$restore     = in('restore');
$max_heal    = in('max_heal');
$userLevel    =getLevel($username);
$startingHealth  =getHealth($username);
$startingGold  =getGold($username);
$startingKills  = getKills($username);
$startingTurns  = getTurns($username);
$level  = getLevel($username);
$heal_points = (in('heal_points') ? intval(in('heal_points')) : null);  // The pointwise healing method.

// ***
$freeResLevelLimit = 6;
$freeResKillLimit = 25;
$lostTurns=10; // *** Default turns lost when the player has no kills.

// *** A True or False as to whether resurrection will be free.
$freeResurrection = ($userLevel<$freeResLevelLimit && $startingKills<$freeResKillLimit);
echo "<br>\n";

//  *** RESURRECTION SECTION ***
if ($restore == 1)
{
  if ($startingHealth>0)
  {
	 echo "You are not dead.<br><br>\n";
  }
   // If you're dead, and a newbie, or dead and have kills.
  else if ($startingKills>1 || $freeResurrection)
    {
      echo "What once was dead shall rise again.<br>\n";
      echo "Current Kills: ".$startingKills."<br>\n";
	  //  *** FREE RESURRECTION DETERMINATION ***
      if (!($freeResurrection)) // 1 kill point cost for resurrection above level 5 or 25 kills.
	  {
  	    subtractKills($username,1);
	  }
      echo "Adjusted Kills after returning to life: ".getKills($username)."<br>\n";
      setHealth($username,100);
      subtractStatus($username,STEALTH+POISON+FROZEN+CLASS_STATE);
	  // *** FREE STEALTHING FOR BLACK CLASS UPON NON-FREE RESURRECTION
	  if($players_class == "Black" && (!$freeResurrection))
	  {
		addStatus($username, STEALTH);
	  }
	}
    elseif ($startingTurns>0) // Dead and no killpoints left, and not a newbie.
   {
   	  echo "What once was dead shall rise again.<br>\n";
   	  if ($startingTurns<$lostTurns && $startingTurns>0) // *** From 9 to 1 turns.
   	  {
   	  	$lostTurns = $startingTurns;
   	  }
   	  echo "Since you have no kills, your resurrection will cost you part of your life time.";
      echo "Current Turns: ".$startingTurns."<br>\n";
      subtractTurns($username, $lostTurns); // *** Takes away necessary turns.
      echo "Adjusted Turns after returning to life: ".getTurns($username)."<br>\n";
      setHealth($username,100);
      subtractStatus($username,STEALTH+POISON+FROZEN+CLASS_STATE);
   }
   else // *** No kills, no turns, and too high of a level.
   {
   	echo "You must wait for time to pass before you can return to life.";
    echo "Current Turns: ".$startingTurns."<br>\n";
   }
} // *** end of resurrection ***

//  ***  HEALING SECTION  ***
else if ($healed == 1 || $max_heal==1)  //If the user tried to heal themselves.
{
  $max_health = (150+(($userLevel-1)*25));
  if ($max_heal==1) {$heal_points=$startingGold;}  // Sets the heal_points when the heal-all button was hit.

  if ($startingHealth > 0)  //Requires the user to be resurrected first.
    {
      if ($heal_points && $heal_points>0)  // Requires a heal number, and a positive one.
	{
	  if ($heal_points <= $startingGold)   //If there's enough money for the amount that they want to heal.
	    {
		    if (($startingHealth+$heal_points)>$max_health)  // Allows numeric healing to "round off" at the max.
		    {
		 	   $heal_points = ($max_health-$startingHealth);  //Rounds off.
			}
			subtractGold($username,$heal_points);
			addHealth($username,$heal_points);
			$finalHealth=getHealth($username);
			echo "A monk tends to your wounds and you are ".(($max_health==$finalHealth)?"fully healed":"healed to $finalHealth hitpoints").".<br><br>\n";
	    }
	    else
	    {
	      echo "You do not have enough gold for this amount of healing.<br>\n";
	    }
	}
      else
		{
		  echo "Please enter a valid number of hit points to heal.<br>\n";
		}
    }
  else
    {
      echo "You must resurrect before you can heal.<br>\n";
    }
}

//  *** POISON SECTION ***
else if ($poisoned == 1)
{
    if (getHealth($username) > 0)
    {
      $cost = 50;  //  the cost of curing poison is set here.
      if ($startingGold >= $cost)
	  {
	    if (getStatus($username) && $status_array['Poison'])
	    {
	      subtractGold($username,$cost);
	      subtractStatus($username,POISON);
	      echo "You have been cured!<br>\n";
	    }
	    else
	    {
	      echo "You are not ill.<br><br>\n";
	    }
	  }
      else
	  {
	    echo "You need more gold to remove poison.<br>\n";
	  }
    }
  else
    {
      echo "You must resurrect before you can heal.<br>\n";
    }
}

?>

<a href="shrine.php">Heal Again ?</a>

<?php
include SERVER_ROOT."interface/footer.php";
?>
