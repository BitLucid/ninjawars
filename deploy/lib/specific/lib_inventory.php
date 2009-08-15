<?php
// lib_inventory.php


// FUNCTIONS

// Benefits for near-equivalent levels.
function nearLevelPowerIncrease($level_difference, $max_increase) {
	$res = 0;
	$coeff = abs($level_difference);
	if ($coeff<$max_increase) {
		$res = $max_increase-$coeff;
	}
	return $res;
}


// Give the item and return a message to show the user.
function render_give_item($username, $target, $item){
    addItem($target,$item,1);
    $give_msg = "You have been given a $item by $username.";
    sendMessage($username,$target,$give_msg);
    return "$target will receive your $item.<br>\n";
}


// Determine the turns for ice scroll.
function ice_scroll_turns($targets_turns, $near_level_power_increase){
    if ($targets_turns>50) {
    	$turns_decrease = rand(1,11)+$near_level_power_increase; // *** 1-11 + 0-10
    } elseif ($targets_turns>10) {
    	$turns_decrease = rand(1, 5)+$near_level_power_increase;
    } elseif ($targets_turns>2) {
    	$turns_decrease = rand(1, 2)+($near_level_power_increase? 1 : 0);
    } else { // *** Players are always left with 1 or two turns.
    	$turns_decrease = '0';
    } // End of turn checks.
    return $turns_decrease;
}


// Send out the killed messages.
function send_kill_mails($username, $target, $attacker_id, $article, $item, $today, $loot){
    $target_email_msg   = "You have been killed by $attacker_id with $article $item at $today and lost $loot gold.";
    sendMessage($attacker_id,$target,$target_email_msg);

    $user_email_msg     = "You have killed $target with $article $item at $today and received $loot gold.";
    sendMessage($target,$username,$user_email_msg);
}



// END OF FUNCTIONS


?>
