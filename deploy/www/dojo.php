<?php
/*
 *** IMPORTANT MAINTENANCE NOTES ***
 * To disable class change code: set $classChangeAllowed to boolean false
 * To change order of class change cycling: Update $class_array, key = starting class, value = next class in cycle
 */
$quickstat  = "player";
$private    = false;
$alive      = false;
$page_title = "Dojo";

include(SERVER_ROOT."interface/header.php");

$msg            = '';
$dimMakCost     = 40;
$dimMakLevelReq = 10;

$classChangeCost     = 20; // *** Cost of class change in Kills
$classChangeLevelReq = 6;

$class_array = array(
// *** START  => NEXT ***
	'Black'   => 'Red'
	, 'Red'   => 'White'
	, 'White' => 'Gray'
	, 'Gray'  => 'Blue'
	, 'Blue'  => 'Black'
);

$dimmak_sequence     = in('dimmak_sequence', '');
$classChangeSequence = in('classChangeSequence');

display_template('dojo.tpl');

if (!isset($username)) {
	echo "<p>The guards at the door block your way, saying \"Stranger, go on your way, you haven't the skill to enter here.\"</p>";
} else {
	$userLevel = getLevel($username);
	$userKills = getKills($username);
	$classChangeAllowed = ($userLevel >= $classChangeLevelReq && $userKills >= $classChangeCost);

	if ($userLevel >= $dimMakLevelReq && $userKills >= $dimMakCost) {	// *** Start of Dim Mak Code, 20 kills. ***
		if ($dimmak_sequence != 2) {
			echo "A black-robed monk stands near the entrance to the dojo.";

			if ($dimmak_sequence != 1) {	// *** Link to start the Dim Mak sequence ***
				echo "  The black monk approaches you and offers to give you <a href=\"dojo.php?dimmak_sequence=1\">power over life and death,</a> at the cost of some of your memories.\n";
			} else {
				echo "  The black monk offers to give you power over life and death, at the cost of some of your memories.\n";	// *** Strips the link after it's been clicked. ***
			}

			echo "<br>";
		}

		if ($dimmak_sequence == 1) {
			//*
			echo "<form id=\"Buy_DimMak\" action=\"dojo.php?dimmak_sequence=2\" method=\"post\" name=\"buy_dimmak\">\n";
			echo "<div style='margin-top: 10px;margin-bottom: 10px;'>\n";
			echo "Trade your memories of ".$dimMakCost." kills for the DimMak Scroll?  \n";
			echo "<input id=\"dimmak_sequence\" type=\"hidden\" value=\"2\" name=\"obtainscroll\">\n";
			echo "<input type=\"submit\" value=\"Obtain Dim Mak\" class=\"formButton\">\n";
			echo "</div>\n";
			echo "</form>\n";
			//*/
		}

		if ($dimmak_sequence == 2) {
			$userKills = subtractKills($username, $dimMakCost);
			additem($username, 'Dim Mak', 1);
			echo "The monk meditates for a moment, then passes his hand over your forehead.  You feel a moment of dizziness.  \n";
			echo "He hands you a pure black scroll.<br>\n";
			$dimmak_sequence = '';
		}

		echo "<hr>\n";	// *** End of Dim Mak Code. ***
	}

	if ($classChangeAllowed) {
		if ($classChangeSequence != 2) {
			echo "A white-robed monk stands near the entrance to the dojo.";

			if ($classChangeSequence != 1) {	// *** Link to start the Class Change sequence ***
				echo "  The white monk approaches you and offers to give you <a href=\"dojo.php?classChangeSequence=1\">the knowledge of your enemies</a> at the cost of your own memories.</a>\n";
			} else {
				echo "  The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.\n";                            //Strips the link after it's been clicked.
			}

			echo "<br>";
		}

		if ($classChangeSequence == 1) {
			echo "<form id=\"Buy_classChange\" action=\"dojo.php?classChangeSequence=2\" method=\"post\" name=\"changeofclass\">\n";
			echo "<div style='margin-top: 10px;margin-bottom: 10px;'>\n";
			echo "Trade your memories of ".$classChangeCost." kills to change your skills to those of the ".$class_array[$players_class]." ninja?\n";
			echo "<input id=\"classchangeSequence\" type=\"hidden\" value=\"2\" name=\"wantanewclass\">\n";
			echo "<input type=\"submit\" value=\"Become A ".$class_array[$players_class]." Ninja\" class=\"formButton\">\n";
			echo "</div>\n";
			echo "</form>\n";
		}

		if ($classChangeSequence == 2) {
			if ($class_array[$players_class]) { // *** Already also checks that they have sufficient kills.
				$userKills = subtractKills($username, $classChangeCost);
				setClass($username, $class_array[$players_class]);
				echo "The monk tosses white powder in your face.  You blink at the pain, and when you open your eyes, everything looks different somehow.  <br>\n";
				echo "The white monk grins at you and walks slowly back to the dojo.<br>\n";
				$classChangeSequence == '';	// *** What is this? I don't even... ***
			}
		}

		echo"<hr><br>\n";	// *** End of Class Changing Code. ***
	}

	echo "<a href=\"chart.php\">Upgrade Chart</a><hr>\n";

	$MAX_LEVEL = 250;

	$nextlevel  = $userLevel + 1;
	$in_upgrade = in('upgrade');

	if ($in_upgrade && $in_upgrade == 1) {  // *** If they requested an upgrade ***
		if ($nextlevel > $MAX_LEVEL) {
			$msg =  "<div>There are no trainers that can teach you beyond your current skill. You are legendary among the ninja.</div>\n";
		} else if ($userKills >= $userLevel * 5) {
			$userKills = subtractKills($username, ($userLevel * 5));
			$userLevel = addLevel($username, 1);
			addStrength($username, 5);
			addTurns($username, 50);
			addHealth($username, 100);
		} else {
			echo "<div>You do not have enough kills to proceed at this time.</div>\n";
		}
	} else if ($nextlevel > $MAX_LEVEL) {  // *** If they just entered the dojo ***
		$msg = "<div>You enter the dojo as one of the elite ninja. No trainer has anything left to teach you.</div>\n";
	} else if ($userKills < ($userLevel * 5)) {
		$msg = "<div>Your trainer finds you lacking. You are instructed to prove your might against more ninja before you return.</div>\n";
	} else {
		echo "<form id=\"level_up\" action=\"dojo.php\" method=\"post\" name=\"level_up\">\n";
		echo "<div style='margin-top: 10px;margin-bottom: 10px;'>\n";
		echo "<div>Do you wish to upgrade to level " . $nextlevel."?</div>\n";
		echo "<input id=\"upgrade\" type=\"hidden\" value=\"1\" name=\"upgrade\">\n";
		echo "<input type=\"submit\" value=\"Upgrade\" class=\"formButton\">\n";
		echo "</div>\n";
		echo "</form>\n";
	}

	echo "<div>Your current level is ".$userLevel.".  </div><div style='margin-bottom: 10px;'>Your current kills are ".$userKills.".</div>\n";
	echo "<div style='margin-bottom: 10px;'>Level ".($userLevel + 1)." requires ".($userLevel * 5)." kills.</div>\n";
	echo $msg;
}

include SERVER_ROOT."interface/footer.php";
?>
