<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
$private    = false; // Show -something- for the casino even when not logged in.
$alive      = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$char_id = get_char_id();
$incoming_bet = in('bet');

define('CASINO_DEFAULT', 0);
define('CASINO_NO_GOLD', 1);
define('CASINO_WIN', 2);
define('CASINO_LOSE', 3);

// Determine the results of a casino bet and reward the char their rewards.
function casino_results($char_id, $incoming_bet){
    $bet = whichever(intval($incoming_bet), get_setting('bet'));
    $reward = "phosphor";// High roller reward.
    $username = get_char_name($char_id);
    set_setting('bet', $bet);
    
    $current_gold = first_value(get_gold($char_id), 0);
    
    define('MAX_BET', 3000);
    $state = CASINO_DEFAULT;
    if ($incoming_bet && $bet && $bet <= MAX_BET) {
    	if ($bet <= $current_gold) {
    		$answer = rand(1, 2);

    		if ($answer == 1) {
    			$state = CASINO_WIN;
    			$current_gold = add_gold($char_id, $bet);

    			if ($bet >= round(MAX_BET*0.99)) {
    			    // within about 1% of the max bet & you win, you get a reward item.
    				add_item($char_id, $reward, 1);
    			}
    		} else if ($answer == 2) {
    			$current_gold = subtract_gold($char_id, $bet);
    			$state = CASINO_LOSE;
    		}
    	} else {
    		$state = CASINO_NO_GOLD;
    	}
    }
    return array($state, $bet, $current_gold);
}

// Pull the casino results.
list($state, $bet, $current_gold) = casino_results($char_id, $incoming_bet);

display_page(
	'casino.tpl'
	, 'Casino'
	, get_certain_vars(get_defined_vars())
	, array(
		'quickstat' => 'player'
	)
);

}
?>
