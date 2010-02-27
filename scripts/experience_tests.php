<?php

// *** First attempt at exp function ***
function calculate_experience1($dmg_taken, $dmg_given, $attacker_level, $victim_level, $attacker_max_hp, $victim_max_hp, $dueling_bonus = 5, $level_bonus_alpha = 1) {
	return $dmg_taken + ($dmg_taken*(($victim_level - $attacker_level)/($attacker_level*$level_bonus_alpha))) + ($dmg_taken*($dueling_bonus/100)*pow(0,pow(0,abs($dmg_taken))));
}

// *** Second attempt at exp function ***
function calculate_experience2($dmg_taken, $dmg_given, $attacker_level, $victim_level, $attacker_max_hp, $victim_max_hp, $dueling_bonus = 5, $level_bonus_alpha = 1) {
//	echo "Level Mod: ",(1 + ( (($victim_level - $attacker_level+.1) / abs($victim_level - $attacker_level+.1)) * (pow($victim_level - $attacker_level, 2)/($attacker_level*$level_bonus_alpha)))),"\n";

	return (
				(($dmg_taken/$attacker_max_hp) * $dmg_taken)		// *** Base Experience ***
				* min(max( 1 + (
					(($victim_level - $attacker_level+.1) / abs($victim_level - $attacker_level+.1))	// *** Determine bonus/penalty
					* (pow($victim_level - $attacker_level, 2)/($attacker_level*$level_bonus_alpha))	// *** Level difference mod
				  ), 0), 3)
				* (1 + (($dueling_bonus/100)*pow(0,pow(0,abs($dmg_taken)))))	// *** Dueling bonus
			);
}

// *** Main() ***
function run_the_numbers($argv, $step = 5, $start = 1, $end = 40) {
	define('DEFAULT_DBONUS', 5);	// *** default dueling bonus expressed as % ***
	define('DEFAULT_LALPHA', 25);	// *** default Importance of Level Difference expressed as 1/a ***
	define('STARTING_STR', 10);		// *** Level 1 STR ***
	define('STR_PER_LVL', 5);		// *** STR Added per level ***
	define('STARTING_HP', 150);		// *** Level 1 HP ***
	define('HP_PER_LVL', 25);		// *** HP Added per level ***

	$funcs = array("1", "2");		// *** suffixes of calculate_experience functions ***

	$defaultFunc = end($funcs);		// *** last function listed is default because it is most likely the newest ***

	if (isset($argv) && $argv[1] == '-d') {	// *** Run this script with -d to use all default values
		$funcChoice = $defaultFunc;
		$dbonus = DEFAULT_DBONUS;
		$alpha = DEFAULT_LALPHA;
	} else {	// *** Otherwise, important values are determined interactively ***
		echo "Run this script with -d to automatically use all default values\n";
		$funcChoice = null;
		echo "Please choose an exp function (", implode(',', $funcs),") [", $defaultFunc,"]: ";
		fscanf(STDIN, "%d\n", $funcChoice);
		if (!in_array($funcChoice, $funcs)) { $funcChoice = $defaultFunc; }

		echo "Please specify a importance of level difference 1/[",DEFAULT_LALPHA,"] : ";
		fscanf(STDIN, "%d\n", $alpha);
		if (!is_numeric($alpha)) { $alpha = DEFAULT_LALPHA; }

		echo "Please specify dueling bonus [",DEFAULT_DBONUS,"]% :";
		fscanf(STDIN, "%d\n", $dbonus);
		if (!is_numeric($dbonus)) { $dbonus = DEFAULT_DBONUS; }
	}

	define('DUELING_BONUS', $dbonus);
	define('LEVEL_MOD_ALPHA', ($alpha <= 0 ? 1 : $alpha));
	$func = "calculate_experience".$funcChoice;

	for ($attacker = $start; $attacker <= $end; $attacker = $attacker+$step) {
		for ($victim = $start; $victim <= $end; $victim = $victim+$step) {
			$rounds = calculate_average_duel_length($attacker, $victim);
			echo "Level $attacker (",max_calculate_health($attacker),"HP, ",calculate_average_dmg($victim) ,"DMG-IN, ",calculate_average_dmg($attacker),"DMG-OUT) attacks Level $victim (",max_calculate_health($victim),"HP)\n";
			echo "**** DUEL (".$rounds." rounds): ".calculate_duel($func, $attacker, $victim)."\n";
			echo "**** SINGLE * ROUNDS: ".calculate_single_attack($func, $attacker, $victim)*$rounds."\n";
			echo "**** SINGLE: ".calculate_single_attack($func, $attacker, $victim)."\n";
			//echo "**** SINGLE w/ KILL: ".(calculate_single_attack($func, $attacker, $victim, true)*10)."\n";
			echo "--------------------------------------------\n";
		}
	}
}

function calculate_single_attack($func, $attacker_level, $victim_level, $attack_kills = false) {
	return round($func(min(calculate_average_dmg($victim_level), max_calculate_health($victim_level)), min(calculate_average_dmg($attacker_level), max_calculate_health($attacker_level)), $attacker_level, $victim_level, max_calculate_health($attacker_level), max_calculate_health($victim_level), 0, LEVEL_MOD_ALPHA), 2);
}

function calculate_average_duel_length($attacker_level, $victim_level) {
	return ceil(min(max_calculate_health($attacker_level)/calculate_average_dmg($victim_level), max_calculate_health($victim_level)/calculate_average_dmg($attacker_level)));
}

function calculate_duel($func, $attacker_level, $victim_level) {
	$rounds = calculate_average_duel_length($attacker_level, $victim_level);

	return round($func(min(calculate_average_dmg($victim_level)*$rounds, max_calculate_health($victim_level)), min($rounds*calculate_average_dmg($attacker_level), max_calculate_health($attacker_level)), $attacker_level, $victim_level, max_calculate_health($attacker_level), max_calculate_health($victim_level), DUELING_BONUS, LEVEL_MOD_ALPHA), 2);
}

function calculate_strength($level) {
	return STARTING_STR+(($level-1)*STR_PER_LVL);
}

function max_calculate_health($level) {
	return STARTING_HP+(($level-1)*HP_PER_LVL);
}

function calculate_average_dmg($level) {
	return round(calculate_strength($level)/2);
}


run_the_numbers($argv);
?>
