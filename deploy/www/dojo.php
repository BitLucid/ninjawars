<?php
require_once(LIB_ROOT."control/lib_player.php");
/*
 *** IMPORTANT MAINTENANCE NOTES ***
 * To disable class change code: set $classChangeAllowed to boolean false
 * To change order of class change cycling: Update $class_array, key = starting class, value = next class in cycle
 */
$private    = false;
$alive      = false;



$level_chart = 1;
$kills_chart = 0;
$str_chart   = 5;
$speed_chart   = 5;
$stamina_chart   = 5;
$hp_chart    = 150;
$max_level   = maximum_level()+1;
$max_hp      = max_health_by_level($max_level);


if ($error = init($private, $alive)) {
	display_error($error);
} else {




$msg            = '';

$dim_mak_cost     = 70; // In turns.
$dim_mak_strength_requirement = 50; // Must have at least a strength of 50 to get and use DimMak, usually level 10.

$class_change_cost     = 50; // *** Cost of class change in turns.

$classes = classes_info();

$in_upgrade = in('upgrade'); // Level up request.
$dimmak_sequence     = in('dimmak_sequence', '');
$classChangeSequence = in('classChangeSequence');
$current_class_untrusted       = in('current_class'); 
    // Untrusted courtesy check to prevent doubled class change in the event of a refresh.
$requested_identity     = in('requested_identity$level_chart = 1;
$kills_chart = 0;
$str_chart   = 5;
$speed_chart   = 5;
$stamina_chart   = 5;
$hp_chart    = 150;
$max_level   = maximum_level()+1;
$max_hp      = max_health_by_level($max_level);'); // Untrusted class identity request.


if (is_logged_in()) {

    // Get the character data.
    $char_id = get_char_id();
	$char = $player    = new Player($char_id);
	$userLevel = $player->vo->level;
	$userKills = $player->vo->kills;
	$user_turns = $player->turns();
	$userClass = $player->class_identity();
	$user_class_display = $player->class_display_name();
	$user_class_theme = class_theme($userClass);
		
	// Pull info for the class requested to change to.
	$destination_class_identity = isset($requested_identity) && isset($classes[$requested_identity])? $requested_identity : null;


	$destination_class_info = $destination_class_display = $destination_class_theme = null;
	if($destination_class_identity){
		$destination_class_info = $classes[$destination_class_identity];
		$destination_class_display = $destination_class_info['class_name'];
		$destination_class_theme = $destination_class_info['theme'];
	}
	
	// Pull the number of kills required to get to the next level.
	$required_kills = required_kills_to_level($player->level());


    $current_class_untrusted = whichever($current_class_untrusted, $userClass);
    // Initialize the class record to prevent double change on refresh.

	// Requirement functions.

// Returns an error if there's an obstacle to changing classes.
function class_change_reqs($char_obj, $turn_req){

	$error = null;
	if($char_obj->turns() < $turn_req){
		// Check the turns, return the error if it's too below.
		$error = "You don't have enough turns to change your class.";
	}
	return $error;
}

// Returns an error if the requirements for getting a dim mak aren't met.
function dim_mak_reqs($char_obj, $turn_req, $str_req){
	$error = null;
	if ($char_obj->turns()<$turn_req){
		$error = "You don't have enough turns to get a Dim Mak.";
	}
	if($char_obj->strength()<$str_req){
		$error = "You don't have enough strength to get a Dim Mak.";
	}
	return $error;
}


    // Check that they can do one action or another.
	$max_level = maximum_level(); // For display in the template.
	$nextLevel = $userLevel + 1;

	$class_change_requirement_error = class_change_reqs($char, $class_change_cost);
	$dim_mak_requirement_error = dim_mak_reqs($char, $dim_mak_cost, $dim_mak_strength_requirement);


    // DIM MAK BUY
	if (!$dim_mak_requirement_error && $dimmak_sequence == 2) {
	    // *** Start of Dim Mak Code, A dim mak didn't error and was requested. ***
		$char_turns = $char->changeTurns((-1)*$dim_mak_cost);
		add_item($char_id, 'dimmak', 1);
	}	// *** End of Dim Mak Code. ***

    // Class Change Buy
    $class_change_error = null;

    if($classChangeSequence == 2 && $current_class_untrusted == $userClass && $destination_class_identity){
        // Class change requested, not a page refresh, and requested class is existant.
        if(!$class_change_requirement_error){
            // Class change conditions are ok, so:
            // subtract the cost in turns
            // ...and change the class.
    		$class_change_error = set_class($char_id, $destination_class_identity);       
    		if(!$class_change_error){
    			$char->changeTurns((-1)*$class_change_cost);
    		}
        }
    }
	
	$possibly_changed_class = char_class_identity($char_id);
	$possibly_changed_class_name = char_class_name($char_id);
	$possibly_changed_class_theme = class_theme($possibly_changed_class);

    
	$upgrade_requested = ($in_upgrade && $in_upgrade == 1);
	$levelled = false;

	if ($upgrade_requested) {
	    // Try to level up.
	    $levelled = level_up_if_possible($char_id);
	    $char = $player = new Player($char_id);
	    $userLevel = $char->level();
	    $char_data = $char->data();
	    
	    // Get the info for the next level, especially if that has changed.
	    $nextLevel = $userLevel+1;
	    $userKills = char_kills($char_id);
    	$required_kills = required_kills_to_level($userLevel);
	}

} // End of the logged in processing.


display_page(
	'dojo.tpl'
	, 'Dojo'
	, get_defined_vars()
	, array('quickstat'=>'player')
);
}
?>
