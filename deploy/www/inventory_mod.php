<?php
require_once(LIB_ROOT."control/lib_inventory.php");
/*
 * Submission page from inventory.php to process results of item use.
 *
 * @package combat
 * @subpackage skill
 */

$private    = true;
$alive      = true;

if ($error = init($private, $alive)) {
	redirect('list.php');
} else {
$link_back  = in('link_back');
$target_id  = in('target_id');
$target     = first_value(get_char_name($target_id), in('target'));
$selfTarget = in('selfTarget');

// *** Item identifier, either it's id or internal name ***
$item_in = in('item');

$give       = in('give');
$give       = in_array($give, array('on', 'Give'));
$target_id  = in('target_id');

$item = null;

if (is_numeric($item_in)) {
	$item = $item_obj = getItemByID($item_in);
} elseif (is_string($item_in)) {
	$item = $item_obj = getItemByIdentity($item_in);
}



if (!is_object($item)) {
	throw new Exception('Invalid item identifier ('.(is_string($item_in) ? $item_in : 'non-string').') sent to page from '.(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '(no referrer)').'.');
}

$user_id    = get_char_id();
$player     = new Player($user_id);

$victim_alive   = true;
$using_item     = true;
$starting_turns = $player->vo->turns;
$username_turns = $starting_turns;
$username_level = $player->vo->level;
$ending_turns   = null;
$item_used      = true;
$turns_change   = null;

$target_id = get_char_id($target);

$item_count = item_count($user_id, $item);

if ($selfTarget) {
	$target = $username;
	$targetObj = $player;
} else if ($target_id) {
	$targetObj = new Player($target_id);
	$target = $targetObj->name();
	
	set_setting("last_item_used", $item_in); // Save last item used.
	
}

if ($targetObj->player_id) {
	$targets_turns = $targetObj->vo->turns;
	$targets_level = $targetObj->vo->level;
	$target_hp     = $targetObj->vo->health;
} else {
	$targets_turns =
	$targets_level =
	$target_hp     = null;
}

$targetName = '';
$targetHealth = '';
$targetHealthPercent = '';

//debug($item->effects());
//debug($item_obj);

$gold_mod		= NULL;
$result			= NULL;

$max_power_increase        = 10;
$level_difference          = $targets_level - $username_level;
$level_check               = $username_level - $targets_level;
$near_level_power_increase = nearLevelPowerIncrease($level_difference, $max_power_increase);

$turns_to_take = null;   // *** Take at least one turn away even on failure.

if ($give) {
	$turn_cost  = 0;
	$using_item = false;
}

// Sets the page to link back to.
if ($target_id && ($link_back == "" || $link_back == 'player') && $target_id != $user_id) {
	$return_to = 'player';
} else {
	$return_to = 'inventory';
}

//$dimMak = $speedScroll = $iceScroll = $fireScroll = $shuriken = $stealthScroll = $kampoFormula = $strangeHerb = null;

// Exceptions to the rules, using effects.

if ($item->hasEffect('wound')) {
	// Minor damage by default items.
	$item->setTargetDamage(rand(1, $item->getMaxDamage())); // DEFAULT, overwritable.

	if ($item->hasEffect('slice')) {
		// Minor piercing damage.
		$item->setTargetDamage(rand(1, $player->getStrength()) + $near_level_power_increase);
	}
	
	if ($item->hasEffect('pierce') && $item->getMaxDamage()) {
		// BUG: For some reason caltrops are doing damage is you don't run the max damage check above.
		// Minor static piercing damage, e.g. 1-50 plus the near level power increase.
		$item->setTargetDamage(rand(1, $item->getMaxDamage()) + $near_level_power_increase);
	}

	// Increased damage from damaging effects, minimum of 20.
	if ($item->hasEffect('fire')) {
		// Major fire damage
		$item->setTargetDamage(rand(20, $player->getStrength() + 20) + $near_level_power_increase);
	}
} // end of wounds section.

//debug($near_level_power_increase);

// Exclusive speed/slow turn changes.
if ($item->hasEffect('slow')) {
	$item->setTurnChange(-1*caltrop_turn_loss($targets_turns, $near_level_power_increase));
} else if ($item->hasEffect('speed')) {
	$item->setTurnChange($item->getMaxTurnChange());    
}

$turn_change = $item_obj->getTurnChange();

$itemName = $item->getName();
$itemType = $item->getType();

$article = get_indefinite_article($item_obj->getName());

if ($using_item) {
	$turn_cost = $item->getTurnCost();
}

// Attack Legal section
$attacker = $username;
$params   = array('required_turns'=>$turn_cost, 'ignores_stealth'=>$item_obj->ignoresStealth(), 'self_use'=>$item->isSelfUsable());
assert(!!$selfTarget || $attacker != $target);

$AttackLegal    = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error   = $AttackLegal->getError();
$bountyMessage = $resultMessage = $alternateResultMessage = '';
$error = false;
$suicide = $kill = $repeat = false;

// *** Any ERRORS prevent attacks happen here  ***
if (!$attack_allowed) { //Checks for error conditions before starting.
	$error = 1;
} else {
	if (is_string($item) || $target == "")  {
		$error = 2;
	} else {
		if ($item_count < 1) {
			$error = 3;
		} else {
			/**** MAIN SUCCESSFUL USE ****/
			if ($give) {
				give_item($username, $target, $item->getName());
				$alternateResultMessage = "__TARGET__ will receive your {$item->getName()}.";
			} else {
				// If it doesn't do damage or have an effect, don't use up the item.
				if(!$item->isOtherUsable()){
					$result = 'This item is not usable on __TARGET__, so it remains unused.';
					$item_used = false;
				} else {
				
					// TODO: These result messages are screwed up (e.g. what gets sent to the events mail is wrongly phrased frequently now), and need to be reworked.
			
					if ($item->hasEffect('stealth')) {
						$targetObj->addStatus(STEALTH);
						$alternateResultMessage = "__TARGET__ is now stealthed.";
						$result = false;
					}
					if ($item->hasEffect('vigor')) {
						if ($targetObj->hasStatus(STR_UP1)) {
							$result = "__TARGET__'s body cannot become more vigorous!";
							$item_used = false;
						} else {
							$targetObj->addStatus(STR_UP1);
							$result = "__TARGET__'s muscles experience a strange tingling.";
						}
					}
					if ($item->hasEffect('strength')) {
						if ($targetObj->hasStatus(STR_UP2)) {
							$result = "__TARGET__'s body cannot become any stronger!";
							$item_used = false;
						} else {
							$targetObj->addStatus(STR_UP2);
							$result = "__TARGET__ feels a surge of power!";
						}
					}
					
					
					// Slow and speed effects are exclusive.
					if ($item->hasEffect('slow')) {
						$limited_effect = false;
						if($targetObj->hasStatus(SLOW)){
							$limited_effect = true; // Already slowed, so decreased effect.
						} else {
							if ($targetObj->hasStatus(FAST)) {
								$targetObj->subtractStatus(FAST);
							} else {
								$targetObj->addStatus(SLOW);
							}
						}
						$turns_change = $item->getTurnChange();
						if($limited_effect){
							// If the effect is already in play, it will have a decreased effect.
							$turns_change = ceil($turns_change*0.3); // Decrease to partial effect.
							$alternateResultMessage = "__TARGET__ is already moving slowly.";
						}
						if ($turns_change == 0) {
							$alternateResultMessage .= "You fail to take any turns from __TARGET__.";
						}

						$result         = " lose ".abs($turns_change)." turns";
						changeTurns($target_id, $turns_change);
						
						
					} else if ($item->hasEffect('speed')) {	// Note that speed and slow effects are exclusive.
						$limited_effect = false;
						if($targetObj->hasStatus(FAST)){
							$limited_effect = true;
						} else {
							if ($targetObj->hasStatus(SLOW)) {
								$targetObj->subtractStatus(SLOW);
							} else {
								$targetObj->addStatus(FAST);
							}
						}
						$turns_change = $item->getTurnChange();
						if($limited_effect){
							// If the effect is already in play, it will have a decreased effect.
							$turns_change = ceil($turns_change*0.6); // Decrease to partial effect.
							$alternateResultMessage = "__TARGET__ is already moving quickly.";
						}
						$result         = "gain $turns_change turns.";
						changeTurns($target_id, $turns_change);
					}
			
					if ($item->getTargetDamage() > 0) { // *** HP Altering ***
						$result        .= "__TARGET__ takes ".$item->getTargetDamage()." damage.";
						$targetObj->vo->health = $victim_alive = subtractHealth($target_id, $item->getTargetDamage());
					}
					
					
					if ($item->hasEffect('death')) {
						$targetObj->vo->health = setHealth($target_id, 0);
						$victim_alive = false;
						$result = " be drained of your life-force and die!";
						$gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
					}
					
					
				}// End of check that it's usable on someone else.
			}// End of the item use (as opposed to giving) section.

			if ($result) {
				// *** Message to display based on item type ***
				if ($item->hasEffect('death')) {
					$resultMessage = "The life force drains from __TARGET__ and they drop dead before your eyes!.";
				}
				if ($turns_change !== null) {
					// Even if $turns_change is set to zero, let them know that.
					if ($turns_change <= 0) {
						$resultMessage .= "__TARGET__ has lost ".abs($turns_change)." turns!";

						if (getTurns($target_id) <= 0) { //Message when a target has no more turns to ice scroll away.
							$resultMessage .= "  __TARGET__ no longer has any turns.";
						}
					} else if ($turns_change > 0) {
						$resultMessage .= "__TARGET__ has gained $turns_change turns!";
					}
				} else {
					$resultMessage = $result;
				}

				if (!$victim_alive) { // Target was killed by the item.
					if (($target != $username)) {   // *** SUCCESSFUL KILL ***
						$attacker_id = ($player->hasStatus(STEALTH) ? "A Stealthed Ninja" : $username);

						if (!$gold_mod) {
							$gold_mod = 0.15;
						}

						$loot = round($gold_mod * get_gold($target_id));
						subtract_gold($target_id, $loot);
						add_gold($char_id, $loot);
						addKills($char_id, 1);
						$kill = true;
						$bountyMessage = runBountyExchange($username, $target);  //Rewards or increases bounty.
					} else {
						$loot = 0;
						$suicide = true;
					}
					
					// Send mails if the target was killed.
					send_kill_mails($username, $target, $attacker_id, $article, $item->getName(), $today, $loot);

				} else {
					$attacker_id = $username;
				}

				if ($target != $username) {
					$target_email_msg = "$attacker_id has used $article {$item->getName()} on you at $today and caused you to $result.";
					sendMessage($attacker_id, $target, $target_email_msg);
				}
			}

			$targetName = $targetObj->vo->uname;
			$targetHealth = $targetObj->vo->health;
			$targetHealthPercent = $targetObj->health_percent();

			$turns_to_take = 1;

			if ($item_used) {
				// *** remove Item ***
				removeItem($user_id, $item->getName(), 1); // *** Decreases the item amount by 1.
			}

			$stealthLost = false;
			// Unstealth
			if (!$item->isCovert() && !$item->hasEffect('stealth') && !$give && $player->hasStatus(STEALTH)) { //non-covert acts
				$player->subtractStatus(STEALTH);
				$stealthLost = true;
			}

			if ($victim_alive && $using_item) {
				$repeat = true;
			}
		}
	}
}

// *** Take away at least one turn even on attacks that fail to prevent page reload spamming ***
// TODO: Once attack attempt limiting works, this can be removed.
if ($turns_to_take < 1) {
	$turns_to_take = 1;
}

$ending_turns = subtractTurns($user_id, $turns_to_take);
assert($item->hasEffect('speed') || $ending_turns < $starting_turns || $starting_turns == 0);

// TODO: Add a "this is the target's resulting hitpoints bar at the end here.


display_page(
	'inventory_mod.tpl'
	, 'Item Usage'
	, get_defined_vars()
	, array(
		'quickstat' => 'player'
	)
);
}
?>
