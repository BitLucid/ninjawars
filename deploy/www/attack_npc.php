<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "NPC Battle Status";

include SERVER_ROOT."interface/header.php";
?>

<h1>Battle Status</h1>

<hr>

<?php
$turn_cost = 1;
$attacked  = in('attacked');
$victim    = in('victim');
$random_encounter = rand(1, 200) == 200;

if (getTurns($username) > 0) {
	if ($attacked == 1) { // *** Bit to expect that it comes from the form. ***
		echo "<p>Attacking ...</p>\n";

		if (getStatus($username) && $status_array['Stealth']) {
			subtractStatus($username, STEALTH);
		}

		$attacker_str    = getStrength($username);
		$attacker_health = getHealth($username);
		$attacker_gold   = getGold($username);

		if ($random_encounter == true) { // *** ONI, Mothafucka! ***

			// **********************************************************
			// *** Oni attack! Yay!                                   ***
			// *** They take turns and a kill and do a little damage. ***
			// **********************************************************

	    	$victim          = "Oni";
			$oni_turn_loss   = 10;
			$oni_health_loss = rand(1, 20);
			$oni_kill_loss   = 1;
			$player_turns    = subtractTurns($username, $oni_turn_loss);
			$attacker_health = subtractHealth($username, $oni_health_loss);
			$attacker_kills  = subtractKills($username, $oni_kill_loss);
			$oni_killed      = false;

			if ($player_turns > 50 && $attacker_health > 0) { // *** If the turns are high/you are energetic, and you survive, you can kill them. ***
				$oni_killed = true;
				addItem($username, "Dim Mak", 1);
			}

			echo "<div class='ninja-error'>An Oni attacks you as you wander!</div>
			<img src='images/scenes/Oni_pelted_by_beans.jpg' style='width:450px'>
			<p>The Oni saps some of your soul before "
			.($oni_killed ? "you kill it." : "it escapes into the wilderness.")."</p>";
		} else if ($victim == "" ) {
			echo "You attack the air.\n";
		} else if ($victim == "villager") { // *** VILLAGER ***
			$villager_attack = rand(0, 10); // *** Villager Damage ***
			$just_villager = rand(0, 20);
			echo "<p>The villager sees you and prepares to defend!</p>\n";
			
            if($just_villager){
    			echo "<img src=\"images/characters/fighter.png\" alt=\"Villager\">";
            } else {
    			echo "<img src=\"images/characters/ninja.png\" alt=\"Ninja\">";
            }
			if (!subtractHealth($username, $villager_attack)) {
				echo "<p>The villager has slain you!</p>\n";
				echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.\n";
			} else {

				$villager_gold = rand(0, 20);	// *** Vilager Gold ***
				addGold($username, $villager_gold);
				echo "<p>The villager is no match for you!</p>\n";
				echo "Villager does $villager_attack points of damage.<br>\n";
				echo "You have gained $villager_gold gold.<br>\n";

				// *** Bounty or no bounty ***
				if (getLevel($username) > 5) {
					if (getLevel($username) > 20) {
						echo "You slay the villager easily, leaving no trace behind!<br>\n";
					} else {
						$added_bounty = floor(getLevel($username) / 3);
						echo "You have unjustly slain a commoner! A bounty of ".($added_bounty)." gold has been placed on your head!<br>\n";
						addBounty($username, ($added_bounty));
					}
				}	// *** End of if > 5 ***

				if (!$just_villager) { // *** Something beyond just a villager, drop a shuriken. ***
					addItem($username, 'Shuriken', $quantity = 1);
					echo "The villager dropped a Shuriken.\n";
				}
			}
		} else if ($victim == "samurai") {
			$turn_cost = 1;
			echo "<img src=\"images/characters/samurai.png\" alt=\"Samurai\">\n";

			if (getLevel($username) < 6) {
				echo "You are too weak to take on the Samurai.<br>\n";
				$turn_cost = 0;
			} else if (getKills($username) < 1) {
				echo "You are too exhausted to take on the Samurai.<br>\n";
				$turn_cost = 0;
			} else {
				echo "The Samurai was waiting for your attack.<br><br>\n";

				$ninja_str               = getStrength($username);
				$ninja_health            = getHealth($username);
				$samurai_damage_array[1] = rand(1, $ninja_str);
				$samurai_damage_array[2] = rand(10, 10 + round($ninja_str * 1.2));
				$does_ninja_succeed      = rand(1, 2);

				if ($does_ninja_succeed == 1) {
					$samurai_damage_array[3] = rand(30 + round($ninja_str * 0.2), 30 + round($ninja_str * 1.7));
				} else {
					$samurai_damage_array[3] = ($ninja_health - $samurai_damage_array[1] - $samurai_damage_array[2]);  //Instant death.
				}

				$samurai_attack[1] = "The Samurai cuts you for ".$samurai_damage_array[1]." damage.<br>\n";
				$samurai_attack[2] = "The Samurai slashes you mercilessly for ".$samurai_damage_array[2]." damage.<br>\n";
				$samurai_attack[3] = "The Samurai thrusts his katana into you for ".$samurai_damage_array[3]." damage.<br>\n";

				for ($i = 1; $i < 4 && $ninja_health > 0; ++$i)
				{
					echo "$samurai_attack[$i]\n";
					$ninja_health = $ninja_health-$samurai_damage_array[$i];

					if ($ninja_health < 1) {
						echo "<br>The Samurai has slain you!<br>\n";
						/*echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br>\n";*/
					}
				}

				if ($ninja_health > 0) {	// *** Ninja still has health after all three attacks. ***
					$samurai_gold = rand(50, 50 + $samurai_damage_array[3] + $samurai_damage_array[2]);
					addGold($username, $samurai_gold);
					addKills($username, 1);

					echo "You use an ancient ninja strike upon the Samurai, slaying him instantly!<br><br>\n";
					echo "You have gained $samurai_gold gold.<br>\n";
					echo "You gain a kill point.<br>\n";

					if ($samurai_damage_array[3] > 100) {	// *** If samurai damage was over 100, but the ninja lived, give a speed scroll. ***
						addItem($username, 'Speed Scroll', $quantity = 1);
						echo "The Samurai had a speed scroll on him. You have a new Speed Scroll in your inventory.\n";
					}

					if ($samurai_damage_array[3] == $ninja_str * 3) {	// *** If the final damage was the exact max damage... ***
						addItem($username, "Dim Mak", 1);
						echo "You have gained a Dim Mak from the Samurai.<br>\n";
					}

					setHealth($username, $ninja_health);
				} else {	// *** Cheaty trickery from the samurai kills the ninja. ***
					setHealth($username, 0);
				}	// *** End samurai trickery ***
			}	// *** End valid turns and kills for the attack. ***
		} else if ($victim == "merchant") {
			echo "Merchant sees you and prepares to defend!<br><br>\n";
			echo "<img src=\"images/characters/merchant.png\" alt=\"Merchant\">";

			$merchant_attack = rand(15, 35);  // *** Merchant Damage ***

			if (!subtractHealth($username, $merchant_attack)) {
				echo "The Merchant has slain you!<br>\n";
				echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br>\n";
			} else { // *** Ninja won the fight. ***
				$merchant_gold   = rand(20, 70);  // *** Merchant Gold   ***
				addGold($username, $merchant_gold);

				echo "The merchant is defeated.<br>\n";
				echo "The Merchant did $merchant_attack points of damage.<br>\n";
				echo "You have gained $merchant_gold gold.<br>\n";

				if ($merchant_attack > 34) {
					addItem($username, 'Fire Scroll', $quantity = 1);
					echo "The Merchant has dropped a Fire Scroll. You have a new Fire Scroll in your inventory.\n";
				}

				if (getLevel($username) > 10) {
					$added_bounty = floor((getLevel($username) - 5) / 3);
					addBounty($username, ($added_bounty * 5));
					echo "You have slain a member of the village!  A bounty of ".($added_bounty * 5)." gold has been placed on your head!<br>\n";
				}
			} // End of if ninja won.
		} else if ($victim == "guard") {
			echo "The Guard sees you and prepares to defend!<br><br>\n";
			echo "<img src=\"images/characters/guard.png\" alt=\"Guard\">\n";

			$guard_attack = rand(1, $attacker_str + 10);  // *** Guard Damage ***

			if (!subtractHealth($username, $guard_attack)) {
				echo "The Guard has slain you!<br>\n";
				echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br>\n";
			} else {
				$guard_gold   = rand(1, $attacker_str + 40);	// *** Guard Gold ***
				addGold($username, $guard_gold);

				echo "The guard is defeated!<br>\n";
				echo "Guard does $guard_attack points of damage.<br>\n";
				echo "You have gained $guard_gold gold.<br>\n";

				if (getLevel($username) > 15) {
					$added_bounty = floor((getLevel($username) - 10) / 5);
					echo "You have slain a member of the military!  A bounty of ".($added_bounty * 10)." gold has been placed on your head!<br>\n";
					addBounty($username, ($added_bounty*10));
				}
			}
		} else if ($victim == "thief") {
			// Check the counter to see whether they've attacked a thief multiple times in a row.
			if(SESSION::is_set('counter')){
			  $counter = SESSION::get('counter');
			} else {
			  $counter = 1;
			}
			$counter = $counter + 1;
			SESSION::set('counter', $counter); // Save the current state of the counter.
			
			if ($counter>20 && rand(1, 3) == 3) { 
				// Only after many attacks do you have the chance to be attacked back by the group of theives.
				SESSION::set('counter', 0); // Reset the counter to zero.
				echo "<img src='images/scenes/KunitsunaTrainingWithTengu.jpg' alt='' style='width:1000px'>";

				echo "<p>A group of tengu thieves is waiting for you. They seem to be angered by your attacks on their brethren.</p>";
				$group_attack= rand(50, 150);

				if (!subtractHealth($username, $group_attack)) { // If the den of theives killed the attacker.
					echo "<p>The group of theives does $group_attack damage to you!</p>";
					echo "<p>The group of thieves have avenged their brotherhood and beaten you to a bloody pulp.</p>";
		            echo "<p>Go to the <a href=\"shrine.php\">shrine</a> to resurrect.</p>";
				} else { // The den of thieves didn't accomplish their goal
				    $group_gold = rand(100, 300);
					if ($group_attack > 120) { // Powerful attack gives an additional disadvantage
						echo "<p>You overpowered the swine, but the blow to the head they gave you before they ran made you lose some of your memories!</p>";
						subtractKills($username, 1);
					}
					echo "<p>The group of theives does $group_attack damage to you, but you rout them in the end!</p>";
					echo "<p>You have gained $group_gold gold.</p> <p>You have found a firescroll on the body of one of the thieves!</p>";
					addGold($username, $group_gold);
					addItem($username, 'Fire Scroll', $quantity = 1);
				}
			} else { // Normal attack on a single thief.
				echo "Thief sees you and prepares to defend!<br><br>\n";
				echo "<img src=\"images/characters/thief.png\" alt=\"Thief\">\n";

				$thief_attack = rand(0, 35);  // *** Thief Damage  ***

				if (!subtractHealth($username, $thief_attack)) {
					echo "Thief has slain you!<br>\n";
					echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br>\n";
				} else {
					$thief_gold = rand(0, 40);  // *** Thief Gold ***

					if ($thief_attack > 30) {
						echo "Thief escaped and stole $thief_gold pieces of your gold!\n";
						subtractGold($username, $thief_gold);
					}else if ($thief_attack < 30){
						echo "The Thief is injured!<br>\n";
						echo "Thief does $thief_attack points of damage!<br>\n";
						echo "You have gained $thief_gold gold.<br> You have found a Shuriken on the thief!\n";

						addGold($username, $thief_gold);
						addItem($username, 'Shuriken', $quantity = 1);
					}

					echo "<br>\n";
					echo "Beware the Ninja Thieves, they have entered this world to steal from all!<br>\n";
				}
			}
		}

		if (getHealth($username) <= 0){
			sendMessage("SysMsg", $username, "DEATH: You have been killed by a non-player character at $today");
            echo "<p>Go to the <a href=\"shrine.php\">shrine</a> to resurrect.</p>";
		}
		subtractTurns($username, $turn_cost);

		if ($victim && !$random_encounter) {
			echo "<a href=\"attack_npc.php?attacked=1&amp;victim=$victim\">Attack $victim again</a>\n";
			echo "<br>\n";
		}

		echo "<a href=\"attack_player.php\">Return to Combat</a>\n";
	}
}
else
{
	echo "You have no turns left today. Buy a speed scroll or wait for your turns to replenish.\n";
}

include SERVER_ROOT."interface/footer.php";
?>
