<?php
require_once(LIB_ROOT."specific/lib_inventory.php");
/*
 * Submission page from inventory.php to process results of item use.
 *
 * @package combat
 * @subpackage skill
 */

$quickstat  = "viewinv";
$private    = true;
$alive      = true;
$page_title = "Item Usage";

include SERVER_ROOT."interface/header.php";
?>

<h1>Item Use</h1>

<?php
$link_back  = in('link_back');
$target     = in('target');
$selfTarget = in('selfTarget');
$item       = in('item');
$give       = in('give');
$item_type  = in('item_type');
$target_id  = in('target_id');

if($item_type){
    $item = item_display_name($item_type);
}

if($target_id){
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
if ($target_id && $link_back == "") {
	$link_back = "<a href=\"player.php?player_id=".urlencode($target_id)."\">Ninja Detail</a>";
} else {
	$link_back = "<a href=\"inventory.php\">Inventory</a>";
}

$dimMak = $speedScroll = $iceScroll = $fireScroll = $shuriken = $stealthScroll = $kampoFormula = $strangeHerb = null;

// These different settings should just become an array of non-defaults somewhere else.
if ($item == 'Dim Mak') {
	$item = $dimMak = new Item('Dim Mak');
	$dimMak->setIgnoresStealth(true);
	$dimMak->setCovert(true);
} else if ($item == 'Amanita Mushroom') {
	$item = $speedScroll = new Item('Amanita Mushroom');
	$speedScroll->setIgnoresStealth(true);
	$speedScroll->setTurnChange(6);
	$speedScroll->setCovert(true);
} else if ($item == 'Phosphor Powder') {
	$item = $fireScroll = new Item('Phosphor Powder');
	$item->setTargetDamage(rand(20, $player->getStrength() + 20) + $near_level_power_increase);
} else if ($item == 'Shuriken') {
	$item = $shuriken = new Item('Shuriken');
	$item->setTargetDamage(rand(1, $player->getStrength()) + $near_level_power_increase);
} else if ($item == 'Caltrops') {
	$item = $caltrops = new Item('Caltrops');
	$item->setTurnChange(-1*caltrop_turn_loss($targets_turns, $near_level_power_increase));
	// ice scroll turns comes out negative already, apparently.
} else if ($item == 'Smoke Bomb') {
	$item = $stealthScroll = new Item('Smoke Bomb');
	$stealthScroll->setCovert(true);
} else if ($item == 'Ginseng Root') {
	$item = $strangeHerb = new Item('Ginseng Root');
	$strangeHerb->setCovert(true);
	$strangeHerb->setIgnoresStealth(true);
} else if ($item == 'Tiger Salve') {
	$item = $kampoFormula = new Item('Tiger Salve');
	$kampoFormula->setCovert(true);
	$kampoFormula->setIgnoresStealth(true);
}

if (!is_object($item)) {
    echo 'No such item.';
    die(); // hack to avoid fatal error, proper checking for items should be done.
}

$article = get_indefinite_article($item->getName());

if ($using_item) {
	$turn_cost = $item->getTurnCost();
}

// Attack Legal section
$attacker = $username;
$params   = array('required_turns'=>$turn_cost, 'ignores_stealth'=>$item->ignoresStealth(), 'self_use'=>$selfTarget);
assert(!!$selfTarget || $attacker != $target);

$AttackLegal    = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error   = $AttackLegal->getError();

// *** Any ERRORS prevent attacks happen here  ***
if (!$attack_allowed) { //Checks for error conditions before starting.
	echo "<div class='ninja-error centered'>$attack_error</div>"; // Display the reason the attack failed.
} else {
	if (is_string($item) || $target == "")  {
		echo "You didn't choose an item/victim.\n";
	} else {
		if ($item_count < 1) {
			echo "You do not have".($item ? " $article ".$item->getName() : ' that item').".\n";
		} else {
			/**** MAIN SUCCESSFUL USE ****/
			echo "<div class='usage-mod-result'>";

			if ($give == "on" || $give == "Give") {
				echo render_give_item($username, $target, $item->getName());
			} else {
				if ($item->getTargetDamage() > 0) { // *** HP Altering ***
					$result        = "lose ".$item->getTargetDamage()." HP";
					$victim_alive  = subtractHealth($target, $item->getTargetDamage());
				} else if ($item === $stealthScroll) {
					$targetObj->addStatus(STEALTH);
					echo "<br>$target is now Stealthed.<br>\n";
					$result = false;
					$victim_alive = true;
				} else if ($item === $dimMak) {
					setHealth($target,0);
					$victim_alive = false;
					$result = "be drained of your life-force and die!";
					$gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
				} else if ($item === $strangeHerb) {
					if ($targetObj->hasStatus(STR_UP1)) {
						$result = "$target's body cannot withstand any more Ginseng Root!<br>\n";
						$item_used = false;
					} else {
						$targetObj->addStatus(STR_UP1);
						$result = "$target's muscles experience a strange tingling.<br>\n";
					}
				} else if ($item === $kampoFormula) {
					if ($targetObj->hasStatus(STR_UP2)) {
						$result = "$target's body cannot withstand any more Tiger Salve!<br>\n";
						$item_used = false;
					} else {
						$targetObj->addStatus(STR_UP2);
						$result = "$target feels a surge of power!<br>\n";
					}
				} else if ($item->getTurnChange() <= 0) {

					$turns_change = $item->getTurnChange();

					if ($turns_change == 0) {
				        echo 'You fail to take any turns from '.$target.'.';
					}

					$result         = "lose ".(-1*$turns_change)." turns";
					changeTurns($target, $turns_change);
					$victim_alive = true;
				} else if ($item->getTurnChange() > 0) {
					$turns_change = $item->getTurnChange();
					$result         = "gain $turns_change turns";
					changeTurns($target, $turns_change);
					$victim_alive = true;
				}
			}

			if ($result) {
				// *** Message to display based on item type ***
				if ($item->getTargetDamage() > 0) {
					echo "$target takes {$item->getTargetDamage()} damage from your attack!<br><br>\n";
				} else if ($item === $dimMak) {
					echo "The life force drains from $target and they drop dead before your eyes!.<br>\n";
				} else if ($item->getTurnChange() !== null) {
					if ($turns_change <= 0) {
						echo "$target has lost ".(0-$turns_change)." turns!<br>\n";
						if (getTurns($target) <= 0) { //Message when a target has no more turns to ice scroll away.
							echo "$target no longer has any turns.<br>\n";
						}
					} else if ($turns_change > 0) {
						echo "$target has gained $turns_change turns!<br>\n";
					}
				} else {
					echo $result;
				}

				if (!$victim_alive) { // Target was killed by the item.
					if (($target != $username) ) {   // *** SUCCESSFUL KILL ***
						$attacker_id = ($player->hasStatus(STEALTH) ? "A Stealthed Ninja" : $username);

						if (!$gold_mod) {
							$gold_mod = 0.15;
						}

						$loot = round($gold_mod * getGold($target));
						subtractGold($target,$loot);
						addGold($username,$loot);
						addKills($username,1);
						echo "You have killed $target with $article {$item->getName()}!<br>\n";
						echo "You receive $loot gold from $target.<br>\n";
						runBountyExchange($username, $target);  //Rewards or increases bounty.
					} else {
						$loot = 0;
						echo "You have comitted suicide!<br>\n";
					}

					send_kill_mails($username, $target, $attacker_id, $article, $item->getName(), $today, $loot);

				} else {
					$attacker_id = $username;
				}

				if ($target != $username) {
					$target_email_msg   = "$attacker_id has used $article {$item->getName()} on you at $today and caused you to $result.";
					sendMessage($attacker_id, $target, $target_email_msg);
				}
			}

			echo "</div>";

			$turns_to_take = 1;

			if ($item_used) {
				// *** remove Item ***
				removeItem($user_id, $item->getName(), 1); // *** Decreases the item amount by 1.

				echo "<br>Removing {$item->getName()} from your inventory.<br>\n";
			}

			// Unstealth
			if (!$item->isCovert() && $give != "on" && $give != "Give" && $player->hasStatus(STEALTH)) { //non-covert acts
				$player->subtractStatus(STEALTH);
				echo "Your actions have revealed you. You are no longer stealthed.<br>\n";
			}

			if ($victim_alive == true && $using_item == true) {
				$self_targetting = ($selfTarget ? '&amp;selfTarget=1' : '');
                echo "<br><a href=\"inventory_mod.php?item_type=".urlencode($item->getType())."&amp;target_id=$target_id{$self_targetting}\">Use {$item->getName()} again?</a><br>\n";  //Repeat Usage
			}
		}
	}
}

// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take<1) {
	$turns_to_take = 1;
}

$ending_turns = subtractTurns($username, $turns_to_take);
assert($item === $speedScroll || $ending_turns < $starting_turns || $starting_turns == 0);
?>

<p>
Return to <?php echo ($link_back? $link_back : "<a href='combat.php'>Combat</a>");?>
</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
