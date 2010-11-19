<?php
require_once(LIB_ROOT."control/lib_player.php");
/*
 *** IMPORTANT MAINTENANCE NOTES ***
 * To disable class change code: set $classChangeAllowed to boolean false
 * To change order of class change cycling: Update $class_array, key = starting class, value = next class in cycle
 */
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {


$msg            = '';
$dimMakCost     = 40;
$dimMakLevelReq = 10;

$classChangeCost     = 20; // *** Cost of class change in Kills
$classChangeLevelReq = 6;

$class_array = array(
// *** STARTING CLASS IDENTITY  => NEXT CLASS IDENTITY ***
	  'viper'   => 'tiger'
	, 'tiger'   => 'dragon'
	, 'dragon' => 'mantis'
	, 'mantis'  => 'crane'
	, 'crane'  => 'viper'
);

$in_upgrade = in('upgrade'); // Level up request.
$dimmak_sequence     = in('dimmak_sequence', '');
$classChangeSequence = in('classChangeSequence');
$current_class       = in('current_class'); 
    // Untrusted courtesy check to prevent doubled class change in the event of a refresh.



if (is_logged_in()) {

    // Get the character data.
    $char_id = get_char_id();
	$player    = new Player($char_id);
	$userLevel = $player->vo->level;
	$userKills = $player->vo->kills;
	$userClass = $player->class_identity();
	$user_class_display = $player->class_display_name();
	$user_class_theme = class_theme($userClass);
	
	// Pull info for the next class in line.
	$destination_class = $class_array[$userClass];
	$destination_class_display = class_display_name_from_identity($class_array[$userClass]);
	$destination_class_theme = class_theme($destination_class);
	// Pull the number of kills required to get to the next level.
	$required_kills = required_kills_to_level($player->level());


    $current_class = whichever($current_class, $userClass);
    // Initialize the class record to prevent double change on refresh.

    // Check that they can do one action or another.
    $class_change_sufficient_kills = $userKills >= $classChangeCost;
	$classChangeAllowed = ($userLevel >= $classChangeLevelReq && $class_change_sufficient_kills);
	$dimMakAllowed      = ($userLevel >= $dimMakLevelReq && $userKills >= $dimMakCost);
	$max_level = maximum_level(); // For display in the template.
	$nextLevel = $userLevel + 1;


    // DIM MAK BUY
	if ($dimMakAllowed && $dimmak_sequence == 2) {
	    // *** Start of Dim Mak Code, A dim mak was requested. ***
		$userKills = subtractKills($username, $dimMakCost);
		add_item($char_id, 'dimmak', 1);
	}	// *** End of Dim Mak Code. ***

    // Class Change Buy
    $class_change_error = null;
    if($classChangeSequence == 2 && $current_class == $userClass && $destination_class){
        // Class change requested, not a page refresh, and requested class is existant.
        if(!$class_change_sufficient_kills){
            $class_change_error = "You don't have enough kills to change your class.";
        } elseif ($classChangeAllowed){
            // Class change conditions are ok, so take the cost in kills
            // ...and change the class.
    		$userKills = subtractKills($username, $classChangeCost);
    		$class_change_error = set_class($char_id, $destination_class);            
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
	    $char = new Player($char_id);
	    $userLevel = $char->level();
	    $char_data = $char->data();
	    
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
