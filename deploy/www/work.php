<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
$private   = false;
$alive     = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

/**
 * The controller for effects of a work request and the default index display of the page and initial form
**/
class WorkController{

	/**
	 * Take in a url parameter of work and try to convert it to gold
	 * 
	**/
	public function requestWork(){
		// Initialize variables to pass to the template.
		$work_multiplier        = 30;
		$worked                 = null;
		$new_gold               = null;
		$not_enough_energy      = null;
		$use_second_description = null;
		$recommended_to_work    = null;
		$is_logged_in           = is_logged_in();


		$worked = intval(in('worked')); // An amount of work requested

		// TODO: refactor & move the rest of the code from the body of work.php and get it working without error here
		// For now, just throw a debug error:
		debug('ERROR: TODO: Fill in the functionality of the requestWork action');

		$response = [];
		$response['parts'] = [
			'recommended_to_work'=>$recommended_to_work, 
			'work_multiplier'=>$work_multiplier,
			'is_logged_in'=>$is_logged_in,
			'gold_display'=>$gold_display,
			'worked'=>$worked,
			'new_gold'=>$new_gold,
			'not_enough_energy'=>$not_enough_energy,
			'use_second_description'=>$use_second_description
			];
		$response['template'] = 'work.tpl';
		$response['title'] = 'Working in the Village';
		$response['options'] = ['quickstat' => 'player'];
		return $response;
	}

	/**
	 * Get the last turns worked by a pc, and pass it to display the default page with form
	**/
	public function index(){
		// Initialize variables to pass to the template.
		$work_multiplier        = 30;
		$worked                 = null;
		$new_gold               = null;
		$not_enough_energy      = null;
		$use_second_description = null;
		$is_logged_in           = is_logged_in();


		// Fill out some of the variables.
		$char_id = self_char_id();
		$player_char = new Player($char_id);
		$last_worked = get_setting('turns_worked');
		$recommended_to_work = $last_worked? $last_worked : 10;
		$gold  = get_gold($char_id); // Get the current/final gold.
		$gold_display = number_format($gold);

		$response = [];
		$response['parts'] = [
			'recommended_to_work'=>$recommended_to_work, 
			'work_multiplier'=>$work_multiplier,
			'is_logged_in'=>$is_logged_in,
			'gold_display'=>$gold_display,
			'worked'=>$worked,
			'new_gold'=>$new_gold,
			'not_enough_energy'=>$not_enough_energy,
			'use_second_description'=>$use_second_description
			];
		$response['template'] = 'work.tpl';
		$response['title'] = 'Working in the Village';
		$response['options'] = ['quickstat' => 'player'];
		return $response;
	}

}

$command = in('command');

$controller = new WorkController();

// Switch between the different controller methods.
switch(true){
	case($_SERVER['REQUEST_METHOD'] == 'POST' && $command='request_work'):
		$response = $controller->requestWork();
	break;
	case($command = 'index'):
	default:
		$command = 'index';
		$response = $controller->index();
	break;
}

// Display the page with the template, title or header vars, template parts, and page options
display_page($response['template'], $response['title'], $response['parts'], $response['options']);


} // End of the no-error area
/*

$work_multiplier        = 30;
$worked                 =
$new_gold               =
$not_enough_energy      =
$use_second_description = null;
$is_logged_in           = is_logged_in();

$worked = intval(in('worked'));

$recommended_to_work = 10;

// Store or retrieve the last value of turns worked.
if ($worked && is_numeric($worked)) {
	set_setting('turns_worked', $worked);
	$recommended_to_work = $worked;
} else {
	$last_worked = get_setting('turns_worked');
	$recommended_to_work = $last_worked? $last_worked : 10;
}

// Work only if the work was requested, not just if the setting was set.
if ($worked > 0) {
	$turns = get_turns($char_id);

	if ($worked > $turns) {
	    $not_enough_energy = true;
	} else {
		$new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

		add_gold($char_id, $new_gold);
		$turns = subtractTurns($char_id, $worked);

		$use_second_description = true;
	}
}

$gold  = get_gold($char_id); // Get the current/final gold.
$gold_display = number_format($gold);

display_page(
	'work.tpl' // *** Main Template ***
	, 'Working in the Village' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => 'player'
	)
);
}

*/
