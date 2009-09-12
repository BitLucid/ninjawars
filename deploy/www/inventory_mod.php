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

<h1 class="brownHeading">Item Use</h1>

<?php
$link_back  = in('link_back');
$target     = in('target');
$selfTarget = in('selfTarget');
$item       = in('item');
$give       = in('give');

$victim_alive   = true;
$using_item     = true;
$starting_turns = getTurns($username);
$username_turns = $starting_turns;
$username_level = getLevel($username);
$item_count     = $sql->QueryItem("SELECT sum(amount) FROM inventory WHERE owner = '$username' AND lower(item)=lower('$item')");
$ending_turns   = null;

if ($selfTarget) {
	$target = $username;
}

$targets_turns = ($target ? getTurns($target) : false);
$targets_level = ($target ? getLevel($target) : NULL);
$target_hp     = $sql->QueryItem("SELECT health FROM players WHERE uname = '$target'");
$target_status = getStatus($target);
$target_ip     = $sql->QueryItem("SELECT ip FROM players WHERE uname = '$target'");

$gold_mod		= NULL;
$result			= NULL;

$max_power_increase        = 10;
$level_difference          = $targets_level - $players_level;
$level_check               = $username_level - $targets_level;
$near_level_power_increase = nearLevelPowerIncrease($level_difference, $max_power_increase);

$turns_to_take = null;   // *** Take at least one turn away even on failure.

if ($give == "on" || $give == "Give") {
	$turn_cost   = 0;
	$using_item = false;
}

// Sets the page to link back to.
if ($target && $link_back == "") { $link_back = "<a href=\"player.php?player=".urlencode($target)."\">Player Detail</a>"; }
else { $link_back = "<a href=\"inventory.php\">Inventory</a>"; }

// This could probably be moved to some lib file for use in different places.
class Item
{
	protected $m_name;
	protected $m_ignoresStealth;
	protected $m_targetDamage;
	protected $m_turnCost;
	protected $m_turnChange;
	protected $m_covert;

	public function __construct($p_name)
	{
		$this->m_ignoresStealth = false;
		$this->m_name = trim($p_name);
		$this->m_turnCost = 1;
	}

	public function getName()
	{
		return $this->m_name;
	}

	public function setIgnoresStealth($p_ignore)
	{
		$this->m_ignoresStealth = (boolean)$p_ignore;
	}

	public function ignoresStealth()
	{
		return $this->m_ignoresStealth;
	}

	public function setTargetDamage($p_damage)
	{
		$this->m_targetDamage = (float)$p_damage;
	}

	public function getTargetDamage()
	{
		return $this->m_targetDamage;
	}

	public function getTurnCost()
	{
		return $this->m_turnCost;
	}

	public function setTurnChange($p_turns)
	{
		$this->m_turnChange = (float)$p_turns;
	}

	public function getTurnChange()
	{
		return $this->m_turnChange;
	}

	public function setCovert($p_covert)
	{
		$this->m_covert = (boolean)$p_covert;
	}

	public function isCovert()
	{
		return $this->m_covert;
	}
}
// Default could be an error later.


$dimMak = $speedScroll = $iceScroll = $fireScroll = $shuriken = $stealthScroll = null;

// These different settings should just become an array of non-defaults somewhere else.
if ($item == 'Dim Mak')
{
	$item = $dimMak = new Item('Dim Mak');
	$dimMak->setIgnoresStealth(true);
	$dimMak->setCovert(true);
}
else if ($item == 'Speed Scroll')
{
	$item = $speedScroll = new Item('Speed Scroll');
	$speedScroll->setIgnoresStealth(true);
	$speedScroll->setTurnChange(6);
	$speedScroll->setCovert(true);
}
else if ($item == 'Fire Scroll')
{
	$item = $fireScroll = new Item('Fire Scroll');
	$fireScroll->setTargetDamage(rand(20, getStrength($username) + 20) + $near_level_power_increase);
}
else if ($item == 'Shuriken')
{
	$item = $shuriken = new Item('Shuriken');
	$shuriken->setTargetDamage(rand(1, getStrength($username)) + $near_level_power_increase);
}
else if ($item == 'Ice Scroll')
{
	$item = $iceScroll = new Item('Ice Scroll');
	$iceScroll->setTurnChange(-1*ice_scroll_turns($targets_turns, $near_level_power_increase));
}
else if ($item == 'Stealth Scroll')
{
	$item = $stealthScroll = new Item('Stealth Scroll');
	$stealthScroll->setCovert(true);
}

$article = get_indefinite_article($item->getName());

if ($using_item)
{
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
		$row = $sql->data;
		if ($item_count < 1) {
			echo "You do not have".($item ? " $article ".$item->getName() : ' that item').".\n";
		} else {
			/**** MAIN SUCCESSFUL USE ****/
			echo "Preparing to use item - <br>\n";

			if ($give == "on" || $give == "Give") {
				echo render_give_item($username, $target, $item->getName());
			} else {

				// *** HP Altering ***
				if ($item->getTargetDamage() > 0) {
					$result        = "lose ".$item->getTargetDamage()." HP";
					$victim_alive  = subtractHealth($target, $item->getTargetDamage());
				} else if ($item === $stealthScroll) {
					addStatus($target, STEALTH);
					echo "<br>$target is now Stealthed.<br>\n";
					$result = false;
					$victim_alive = true;
				} else if ($item === $dimMak) {
					setHealth($target,0);
					$victim_alive = false;
					$result = "be drained of your life-force and die!";
					$gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
				} else if ($item->getTurnChange() <= 0) {

					$turns_change = $item->getTurnChange();

					if ($turns_change == 0) {
				        echo 'You fail to take any turns from '.$target.'.';
					}

					$result         = "lose ".(-1*$turns_change)." turns";
					subtractTurns($target, $turns_change);
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
				} else if ($turns_change <= 0) {
					echo "$target's turns reduced by $turns_change.<br>\n";
					if (getTurns($target) <= 0) { //Message when a target has no more turns to ice scroll away.
						echo "$target no longer has any turns.<br>\n";
					}
				} else if ($turns_change > 0) {
					echo "$target's turns increased by $turns_change.<br>\n";
				}

				if (!$victim_alive) { // Target was killed by the item.
					if (getStatus($username) && ($target != $username) ) {   // *** SUCCESSFUL KILL ***
						$attacker_id = ($status_array['Stealth'] ? "A Stealthed Ninja" : $username);

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

			$turns_to_take = 1;

			// *** remove Item ***

			echo "<br>Removing {$item->getName()} from your inventory.<br>\n";

			$sql->Update("UPDATE inventory set amount = amount-1 WHERE owner = '".$username."' AND item ='{$item->getName()}' AND amount>0");
			// *** Decreases the item amount by 1.

			// Unstealth
			if (!$item->isCovert() && $give != "on" && $give != "Give" && getStatus($username) && $status_array['Stealth']) { //non-covert acts
				subtractStatus($username,STEALTH);
				echo "Your actions have revealed you. You are no longer stealthed.<br>\n";
			}

			if ($victim_alive == true && $using_item == true) {
				$self_targetting = $selfTarget? '&amp;selfTarget=1' : '';
				echo "<br><a href=\"inventory_mod.php?item=".urlencode($item->getName())."&amp;target=$target{$self_targetting}\">Use {$item->getName()} again?</a><br>\n";  //Repeat Usage
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

<br><br>

Return to <?echo $link_back;?>

<?php
include SERVER_ROOT."interface/footer.php";
?>
