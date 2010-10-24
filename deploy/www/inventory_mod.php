<?php
require_once(LIB_ROOT."specific/lib_inventory.php");
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
$target     = in('target');
$selfTarget = in('selfTarget');

// Possible identifiers of the item.
$item_type  = in('item_type');
$item_identity = in('item_identity');
$item       = in('item');

$give       = in('give');
$target_id  = in('target_id');

if(is_numeric($item_type)){
    $item = $item_obj = new Item($item_type);
} elseif($item_identity) {
    $item = $item_obj = new Item(item_info_from_identity($item_identity, 'item_id'));
}

if(!is_object($item)){
    throw new Exception('Item sent to page from '.$_SERVER['HTTP_REFERER'].' as item display name instead of item id.');
}

if ($target_id) {
	$target = get_char_name($target_id);
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

$target_id = get_char_id($target);

$item_count = item_count($user_id, $item);

if ($selfTarget) {
	$target = $username;
	$targetObj = $player;
} else if ($target) {
	$targetObj = new Player($target);
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

if (in_array($give, array("on", "Give"))) {
	$turn_cost  = 0;
	$using_item = false;
}

// Sets the page to link back to.
if ($target_id && ($link_back == "" || $link_back == 'player') && $target_id != $user_id) {
	$return_to = 'player';
	$link_back = "<a href=\"player.php?player_id=".urlencode($target_id)."\">Ninja Detail</a>";
} else {
	$return_to = 'inventory';
	$link_back = "<a href=\"inventory.php\">Inventory</a>";
}

//$dimMak = $speedScroll = $iceScroll = $fireScroll = $shuriken = $stealthScroll = $kampoFormula = $strangeHerb = null;

if ($item->hasEffect('wound') && $item->hasEffect('fire')) {
	// Major fire damage
	$item->setTargetDamage(rand(20, $player->getStrength() + 20) + $near_level_power_increase);
}

if ($item->hasEffect('wound') && $item->hasEffect('slice')) {
	// Minor piercing damage.
	$item->setTargetDamage(rand(1, $player->getStrength()) + $near_level_power_increase);
}

if ($item->hasEffect('slow')) {
	$item->setTurnChange(-1*caltrop_turn_loss($targets_turns, $near_level_power_increase));
}

if ($item->hasEffect('speed')) {
	$item->setTurnChange($item->getMaxTurnChange());    
}

$turn_change = $item_obj->getTurnChange();

if (!is_object($item_obj)) {
	echo 'No such item.';
	die(); // hack to avoid fatal error, proper checking for items should be done.
} else {
	$itemName = $item->getName();
	$itemType = $item->getType();
}

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
			if ($give == "on" || $give == "Give") {
				give_item($username, $target, $item->getName());
				$alternateResultMessage = "$target will receive your {$item->getName()}.<br>\n";
			} else {
				if ($item->getTargetDamage() > 0) { // *** HP Altering ***
					$result        = "lose ".$item->getTargetDamage()." HP";
					$targetObj->vo->health = $victim_alive  = subtractHealth($target, $item->getTargetDamage());
				} else if ($item->hasEffect('stealth')) {
					$targetObj->addStatus(STEALTH);
					$alternateResultMessage = "<br>$target is now Stealthed.<br>\n";
					$result = false;
					$victim_alive = true;
				} else if ($item->hasEffect('death')) {
					$targetObj->vo->health = setHealth($target,0);
					$victim_alive = false;
					$result = "be drained of your life-force and die!";
					$gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
				} else if ($item->hasEffect('vigor')) {
					if ($targetObj->hasStatus(STR_UP1)) {
						$result = "$target's body cannot withstand any more Ginseng Root!<br>\n";
						$item_used = false;
					} else {
						$targetObj->addStatus(STR_UP1);
						$result = "$target's muscles experience a strange tingling.<br>\n";
					}
				} else if ($item->hasEffect('strength')) {
					if ($targetObj->hasStatus(STR_UP2)) {
						$result = "$target's body cannot withstand any more Tiger Salve!<br>\n";
						$item_used = false;
					} else {
						$targetObj->addStatus(STR_UP2);
						$result = "$target feels a surge of power!<br>\n";
					}
				} else if ($item->hasEffect('slow')) {

					$turns_change = $item->getTurnChange();

					if ($turns_change == 0) {
				        $alternateResultMessage = 'You fail to take any turns from '.$target.'.';
					}

					$result         = "lose ".(-1*$turns_change)." turns";
					changeTurns($target, $turns_change);
					$victim_alive = true;
				} else if ($item->hasEffect('speed')) {
					$turns_change = $item->getTurnChange();
					$result         = "gain $turns_change turns";
					changeTurns($target, $turns_change);
					$victim_alive = true;
				}
			}

			if ($result) {
				// *** Message to display based on item type ***
				if ($item->getTargetDamage() > 0) {
					$resultMessage = "$target takes {$item->getTargetDamage()} damage from your attack!<br><br>\n";
				} else if ($item->hasEffect('death')) {
					$resultMessage = "The life force drains from $target and they drop dead before your eyes!.<br>\n";
				} else if ($item->getTurnChange() !== null) {
					if ($turns_change <= 0) {
						$resultMessage = "$target has lost ".(0-$turns_change)." turns!<br>\n";

						if (getTurns($target) <= 0) { //Message when a target has no more turns to ice scroll away.
							$resultMessage .= "$target no longer has any turns.<br>\n";
						}
					} else if ($turns_change > 0) {
						$resultMessage = "$target has gained $turns_change turns!<br>\n";
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

						$loot = round($gold_mod * getGold($target));
						subtractGold($target,$loot);
						addGold($username,$loot);
						addKills($username,1);
						$kill = true;
						$bountyMessage = runBountyExchange($username, $target);  //Rewards or increases bounty.
					} else {
						$loot = 0;
						$suicide = true;
					}

					send_kill_mails($username, $target, $attacker_id, $article, $item->getName(), $today, $loot);

				} else {
					$attacker_id = $username;
				}

				if ($target != $username) {
					$target_email_msg = "$attacker_id has used $article {$item->getName()} on you at $today and caused you to $result.";
					sendMessage($attacker_id, $target, $target_email_msg);

					$targetName = $targetObj->vo->uname;
					$targetHealth = $targetObj->vo->health;
					$targetHealthPercent = $targetObj->health_percent();
				}
			}

			$turns_to_take = 1;

			if ($item_used) {
				// *** remove Item ***
				removeItem($user_id, $item->getName(), 1); // *** Decreases the item amount by 1.
			}

			$stealthLost = false;
			// Unstealth
			if (!$item->isCovert() && !$item->hasEffect('stealth') && $give != "on" && $give != "Give" && $player->hasStatus(STEALTH)) { //non-covert acts
				$player->subtractStatus(STEALTH);
				$stealthLost = true;
			}

			if ($victim_alive == true && $using_item == true) {
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

$ending_turns = subtractTurns($username, $turns_to_take);
assert($item->hasEffect('speed') || $ending_turns < $starting_turns || $starting_turns == 0);

// TODO: Add a "this is the target's resulting hitpoints bar at the end here.

display_page(
	'inventory_mod.tpl'
	, 'Item Usage'
	, get_defined_vars()
	, array(
		'quickstat' => 'viewinv'
	)
);
}
?>
