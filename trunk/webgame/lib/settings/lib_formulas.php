<?php


function get_score_formula(){
	$score = '(level*1000 + gold/100 + kills*3 - days*5)';
	return $score;
}


// Categorize by level.
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







?>
