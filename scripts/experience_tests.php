<?php
function calculate_experience1($dmg_taken, $dmg_given, $attacker_level, $victim_level, $attacker_max_hp, $victim_max_hp, $dueling_bonus = 100, $level_bonus_alpha = 1) {
	return $dmg_taken + ($dmg_taken*(($victim_level - $attacker_level)/($attacker_level*$level_bonus_alpha))) + ($dmg_taken*($dueling_bonus/100)*pow(0,pow(0,abs($dmg_taken))));
}

function run_the_numbers($step = 5, $start = 1, $end = 50) {
	for ($attacker = $start; $attacker <= $end; $attacker = $attacker+$step)
	{
		for ($victim = $start; $victim <= $end; $victim = $victim+$step)
		{
			$rounds = calculate_average_duel_length($attacker, $victim);
			echo "Level $attacker attacks Level $victim \n";
			echo "**** DUEL (".$rounds." rounds): ".calculate_duel($attacker, $victim)."\n";
			echo "**** SINGLE * ROUNDS: ".calculate_single_attack($attacker, $victim)*$rounds."\n";
			echo "**** SINGLE: ".calculate_single_attack($attacker, $victim)."\n";
			echo "**** SINGLE w/ KILL: ".calculate_single_attack($attacker, $victim, true)."\n";
			echo "--------------------------------------------\n";
		}
	}
}

function calculate_single_attack($attacker_level, $victim_level, $attack_kills = false) {
	return calculate_experience1(calculate_average_dmg($attacker_level), calculate_average_dmg($victim_level), $attacker_level, $victim_level, max_calculate_health($attacker_level), max_calculate_health($victim_level), 0);
}

function calculate_average_duel_length($attacker_level, $victim_level) {
	return round(min(max_calculate_health($attacker_level)/calculate_average_dmg($victim_level), max_calculate_health($victim_level)/calculate_average_dmg($attacker_level)));
}

function calculate_duel($attacker_level, $victim_level) {
	$rounds = calculate_average_duel_length($attacker_level, $victim_level);

	return calculate_experience1(calculate_average_dmg($attacker_level)*$rounds, $rounds*calculate_average_dmg($victim_level), $attacker_level, $victim_level, max_calculate_health($attacker_level), max_calculate_health($victim_level), 100);
}

function calculate_strength($level) {
	return 5+(($level-1)*5);
}

function max_calculate_health($level) {
	return 150+(($level-1)*25);
}

function calculate_average_dmg($level) {
	return round(calculate_strength($level)/2);
}

run_the_numbers();
?>
