<?php
class Skill
{

// *** Constructor should eventually get a specific skill's stats from the database.

	

	function getTurnCost($type)
	{
		$skillsTypeToTurns = array('cold steal' => 2, 'ice bolt' => 3, 'sight' => 2,
		 'deflect' => 4, 'blaze' => 4, 'duel' => 2, 'attack' => 1, 'fire bolt' => 3,
		  'poison touch' => 1, 'stealth' => 2, 'unstealth' => 0, 'steal' => 1);
		 $res = 1; // default
		 if (isset($skillsTypeToTurns[strtolower($type)])){
		 	$res = $skillsTypeToTurns[strtolower($type)];
		 }
		return $res; // *** Throws back the turns cost.
	}
	
	function getSelfUse($type)
	{
		$skillsTypeToSelf = array('stealth' => true, 'unstealth' => true);
		$res = false; // default
		if (isset($skillsTypeToSelf[strtolower($type)])){
			$res = true;
		}
		return $res;
	}
	
	// Whether the skill is usable on someone other than self.
	function getUsableOnTarget($type)
	{
		$skillsUsableOnTarget = array('stealth' => false, 'unstealth' => false);
		$res = true; // default
		if (isset($skillsUsableOnTarget[strtolower($type)])){
			$res = false;
		}
		return $res;
	}
	
	function getIgnoreStealth($type)
	{
		$skillsThatIgnoreStealth = array('sight' => true,
		 'deflect' => true, 'blaze' => true, 'poison touch' => true, 'unstealth' => true,
		 'ice bolt' => true, 'fire bolt' => true, 'cold steal' => true);
		$res = false; // default
		if (isset($skillsThatIgnoreStealth[strtolower($type)])){
			$res = true;
		}
		return $res;
	}

 	//public static $skillsNumbers = array(1 => 'cold steal', 2 => 'ice bolt', 3 => 'sight', 4 => 'deflect', 5 => 'fire bolt', 6 => 'blaze', 7 => 'poison touch', 8 => 'stealth', 9 => 'unstealth', 10 => 'steal');
	// *** Eventually, should be able to get the skill ID from the database, not from that array above.
}
?>
