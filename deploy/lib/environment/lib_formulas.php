<?php

// Categorize ninja ranks by level.
function level_category($level){
	$res = '';
	switch (true) {
		case($level<2):
			$res= 'Novice';
			break;
		case($level<6):
			$res= 'Acolyte';
			break;
		case($level<31):
			$res= 'Ninja';
			break;
		case($level<51):
			$res= 'Elder Ninja';
			break;
		case($level<101):
			$res= 'Master Ninja';
			break;
		default:
			$res= 'Shadow Master';
			break;
	}

	return array('display' => $res,
		'css' => strtolower(str_replace(" ", "-", $res)));
}

// Standard location for the formula to determine max health.
function determine_max_health($level) {
    return max_health_by_level($level);
}

/** Calculate a max health by a level, will be used in dojo.php and calculating experience.**/
function max_health_by_level($level) {
	$health_per_level = 25;
	return 150 + round($health_per_level*($level-1));
}
?>
