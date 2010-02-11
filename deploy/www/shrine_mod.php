<?php
$page_title = "Shrine";
$alive      = false;
$private    = true;
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";
include(OBJ_ROOT."Skill.php");
$skillsListObj = new Skill();
?>

<h1>Shrine Effects</h1>

<hr>

<div id='heal-result' style="margin-top: 10px;">

<?php
$healed             = in('healed');
$poisoned           = in('poisoned');
$restore            = in('restore');
$max_heal           = in('max_heal');
$heal_and_resurrect = in('heal_and_resurrect');
$userLevel          = getLevel($username);
$startingHealth     = getHealth($username);
$startingGold       = getGold($username);
$startingKills      = getKills($username);
$startingTurns      = getTurns($username);
$level              = getLevel($username);
$heal_points        = (in('heal_points') ? intval(in('heal_points')) : null);  // The pointwise healing method.
$freeResLevelLimit  = 6;
$freeResKillLimit   = 25;
$lostTurns          = 10; // *** Default turns lost when the player has no kills.
$has_chi            = $skillsListObj->hasSkill('Chi'); // Extra healing benefits from chi, by level.

// *** A True or False as to whether resurrection will be free.
$freeResurrection = ($userLevel < $freeResLevelLimit && $startingKills < $freeResKillLimit);

if ($heal_and_resurrect) {
	// Set resurrect if needed.
	if ($startingHealth < 1) {
		$restore = 1;
	}

	// Set heal always.
	$max_heal = 1;
}

if ($restore == 1) {	//  *** RESURRECTION SECTION ***
	if ($startingHealth > 0) {
		echo "<div style='margin-bottom: 10px;'>You are not dead.</div>\n";
	} else if ($startingKills > 1 || $freeResurrection) {	// If you're dead, and a newbie, or dead and have kills.
		//  *** FREE RESURRECTION DETERMINATION ***

		if (!($freeResurrection)) { // 1 kill point cost for resurrection above level 5 or 25 kills.
			subtractKills($username, 1);
		}

		setHealth($username, 100);
		subtractStatus($username, STEALTH+POISON+FROZEN+CLASS_STATE);

		// *** FREE STEALTHING FOR BLACK CLASS UPON NON-FREE RESURRECTION
		if ($players_class == "Black" && (!$freeResurrection)) {
			addStatus($username, STEALTH);
		}

		echo "<div>What once was dead shall rise again.</div>\n";
		echo "<div>Current Kills: ".$startingKills."</div>\n";
		echo "<div>Adjusted Kills after returning to life: ".getKills($username)."</div>\n";
	} elseif ($startingTurns > 0) { // Dead and no killpoints left, and not a newbie.
		echo "<div>What once was dead shall rise again.</div>\n";

		if ($startingTurns < $lostTurns && $startingTurns > 0) { // *** From 9 to 1 turns.
			$lostTurns = $startingTurns;
		}

		subtractTurns($username, $lostTurns); // *** Takes away necessary turns.
		setHealth($username, 100);
		subtractStatus($username, STEALTH+POISON+FROZEN+CLASS_STATE);

		echo "<div>Since you have no kills, your resurrection will cost you part of your life time.</div>";
		echo "<div>Current Turns: ".$startingTurns."</div>\n";
		echo "<div>Adjusted Turns after returning to life: ".getTurns($username)."</div>\n";
	} else { // *** No kills, no turns, and too high of a level.
		echo "<div>You must wait for time to pass before you can return to life.</div>";
		echo "<div>Current Turns: ".$startingTurns."</div>\n";
	}
} // *** end of resurrection ***

if ($healed == 1 || $max_heal == 1) {  //If the user tried to heal themselves.
    //  ***  HEALING SECTION  ***
	$max_health = (150 + (($userLevel - 1) * 25));

	if ($max_heal == 1) {
	    // Sets the heal_points when the heal-all button was hit.
	    $heal_points = $startingGold;
	}  

	if ($startingHealth > 0) {  //Requires the user to be resurrected first.
		if ($heal_points && $heal_points > 0) {  // Requires a heal number, and a positive one.
			if ($heal_points <= $startingGold) {   //If there's enough money for the amount that they want to heal.
				if (($startingHealth + $heal_points) > $max_health){  // Allows numeric healing to "round off" at the max.
					$heal_points = ($max_health-$startingHealth);  //Rounds off.
				}

				subtractGold($username,$heal_points);
				// Having chi increases the amount healed in all cases.
				addHealth($username,($has_chi? round($heal_points*1.5) : $heal_points));
				$finalHealth = getHealth($username);
				echo "<p>A monk tends to your wounds and you are ".(($max_health <= $finalHealth) ? "fully healed" : "healed to $finalHealth hitpoints").".</p>\n";
			} else {
				echo "<div>You do not have enough gold for this amount of healing.</div>\n";
			}
		} else {
			echo "<div>You cannot heal with zero gold.</div>\n";
		}
	} else {
		echo "<div>You must resurrect before you can heal.</div>\n";
	}
} else if ($poisoned == 1) {	//  *** POISON SECTION ***
	if (getHealth($username) > 0) {
		$cost = 50;  //  the cost of curing poison is set here.

		if ($startingGold >= $cost) {
			if (getStatus($username) && $status_array['Poison']) {
				subtractGold($username,$cost);
				subtractStatus($username,POISON);
				echo "<div>You have been cured!</div>\n";
			} else {
				echo "<div style='margin-bottom: 10px;'>You are not ill.</div>\n";
			}
		} else {
			echo "<div>You need more gold to remove poison.</div>\n";
		}
	} else {
		echo "<div>You must resurrect before you can heal.</div>\n";
	}
}
?>
</div> <!-- End of heal-result div -->

<a href="shrine.php">Heal Again?</a>

<?php
include SERVER_ROOT."interface/footer.php";
?>
