<?php
$private    = true;
$alive      = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$bet = intval(in('bet'));
$reward = "Fire Scroll";

$current_gold = getGold($username);

define('CASINO_DEFAULT', 0);
define('CASINO_NO_GOLD', 1);
define('CASINO_WIN', 2);
define('CASINO_LOSE', 3);

if ($bet >= 5 && $bet <= 1000) {
	if ($bet <= $current_gold) {
		$answer = rand(1, 2);

		if ($answer == 1) {
			$state = CASINO_WIN;
			$current_gold = addGold($username, $bet);

			if ($bet == 1000) {
				addItem($username, $reward, 1);
			}
		} else if ($answer == 2) {
			$current_gold = subtractGold($username, $bet);
			$state = CASINO_LOSE;
		}
	} else {
		$state = CASINO_NO_GOLD;
	}
} else {
	$state = CASINO_DEFAULT;
}

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
