<?php
/**
 * Combat behaviors during standard attacking.
**/
namespace app\combat;

use \Player;

class Combat{

	/**
	 * Take an attacker and target, and return the killpoints
	 * return int
	**/
	public static function killpointsFromDueling(Player $attacker, Player $target){
		$levelDifference = ($target->level() - $attacker->level());

		if ($levelDifference > 10) {
			$levelDifferenceMultiplier = 5;
		} else if ($levelDifference > 0) {
			$levelDifferenceMultiplier = ceil($levelDifference/2);  //killpoint return of half the level difference.
		} else {
			$levelDifferenceMultiplier = 0;
		}

		return 1+$levelDifferenceMultiplier;
	}
}