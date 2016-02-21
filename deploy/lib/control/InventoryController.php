<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player as Player;

/**
 * Control the display of items and gold (and maybe some day armor) for a char
 */
class InventoryController {
	const PRIV  = true;
	const ALIVE = false;

	/**
	 * View items and gold of char
	 */
	public function index() {
		$char = new Player(self_char_id());

		$inv_counts = inventory_counts($char->id());
		$inventory = array();

		if ($inv_counts) {
			// Standard item info.
			$standard_items = standard_items();
			// Make the information into a single, trivially usable, array.
			foreach ($inv_counts as $item_info) {
				$l_id    = $item_info['item_type'];
				$l_name  = $item_info['name'];
				$l_count = $item_info['count'];

				if (isset($standard_items[$l_id]) && isset($l_count)) {
					// If a type of item exists and has a non-zero count, join the array of it's count with it's standard info.
					$inventory[$l_name] = array('count'=>$l_count) + $standard_items[$l_id];
				}
			}
		} else {
			$inventory = false;
		}

		$parts = [
			'gold'         => $char->gold(),
			'gold_display' => number_format($char->gold()),
			'inventory'    => $inventory,
			'username'     => $char->name(),
			'char_id'      => $char->id(),
		];

		return $this->render($parts);
	}

	public function render($parts) {
		return [
			'template' => 'inventory.tpl',
			'title'    => 'Your Inventory',
			'parts'    => $parts,
			'options'  => [
				'body_classes' => 'inventory',
				'quickstat'    => 'viewinv',
			],
		];
	}

	/**
	 * Give an object to a target
	 */
	public function give(){
		return $this->useItem(true); // Wrap standard use
	}

	/**
	 * Use an object on myself
	 */
	public function self_use(){
		return $this->useItem(false, true); // Wrap standard use
	}

	/** 
	 * Get the slugs and parameters values.
	 */
	private function parse_slugs($give=false, $self_use = false){
		$url_part = $_SERVER['REQUEST_URI'];
		$path = parse_url($url_part, PHP_URL_PATH);
		$slugs = explode('/', trim($path, '/'));
		$selfTarget = whichever(in('selfTarget'), $self_use);
		$link_back = whichever(in('link_back'), 
				($selfTarget? 'inventory' : null)
				);
		$item_in = $slugs[2];
		$in_target = isset($slugs[3])? $slugs[3] : null;
		return [
			'link_back' => $link_back,
			'item_in' => $item_in,
			'in_target' => $in_target,
			'selfTarget' => $selfTarget,
			'give' => $give,
			];
	}

	/**
	 * Use an item on a target
	 * @note /use/ is aliased to useItem externally because use is a php reserved keyword
	 */
	public function useItem($give = false, $self_use = false){

		// Formats are:
		// http://nw.local/item/self_use/amanita/
		// http://nw.local/item/use/shuriken/10/
		// http://nw.local/item/give/shuriken/10/
		// http://nw.local/item/use/shuriken/156001/

		$slugs = $this->parse_slugs($give, $self_use);

		// Pull the parsed slugs
	    $link_back  = $slugs['link_back'];
	    $selfTarget = $slugs['selfTarget'];
	    $item_in = $slugs['item_in']; // Item identifier, either it's id or internal name
	    $in_target = $slugs['in_target'];
	    $give = $slugs['give'];

	    $target    = $in_target;
	    if(positive_int($in_target)){
	    	$target_id = positive_int($target);
	    } else {
	    	$target_id = get_char_id($target);
	    }

	    $give      = in_array($give, array('on', 'Give'));

	    $player    = new Player(self_char_id());

	    $victim_alive           = true;
	    $using_item             = true;
	    $item_used              = true;
	    $stealthLost            = false;
	    $error                  = false;
	    $suicide                = false;
	    $kill                   = false;
	    $repeat                 = false;
	    $ending_turns           = null;
	    $turns_change           = null;
	    $turns_to_take          = null;
	    $gold_mod               = NULL;
	    $result                 = NULL;
	    $targetResult           = NULL; // result message to send to target of item use
	    $targetName             = '';
	    $targetHealth           = '';
	    $targetHealthPercent    = '';
	    $bountyMessage          = '';
	    $resultMessage          = '';
	    $alternateResultMessage = '';

	    if ($item_in == (int) $item_in && is_numeric($item_in)) { // Can be cast to an id.
	        $item = $item_obj = getItemByID($item_in);
	    } elseif (is_string($item_in)) {
	        $item = $item_obj = getItemByIdentity($item_in);
	    } else {
	        $item = null;
	    }

	    if (!is_object($item)) {
	    	return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
	    } else {
	        $item_count = item_count($player->id(), $item);

	        // Check whether use on self is occurring.
	        $self_use = ($selfTarget || ($target_id === $player->id()));

	        if ($self_use) {
	            $target    = $player->name();
	            $targetObj = $player;
	        } else if ($target_id) {
	            $targetObj = new Player($target_id);
	            $target    = $targetObj->name();
	        }

	        $starting_turns = $player->vo->turns;
	        $username_turns = $starting_turns;
	        $username_level = $player->vo->level;

	        if (($targetObj instanceof Player) && $targetObj->id()) {
	            $targets_turns = $targetObj->vo->turns;
	            $targets_level = $targetObj->vo->level;
	            $target_hp     = $targetObj->vo->health;
	        } else {
	            $targets_turns =
	                $targets_level =
	                $target_hp     = null;
	        }

	        $max_power_increase        = 10;
	        $level_difference          = $targets_level - $username_level;
	        $level_check               = $username_level - $targets_level;
	        $near_level_power_increase = nearLevelPowerIncrease($level_difference, $max_power_increase);

	        // Sets the page to link back to.
	        if ($target_id && ($link_back == "" || $link_back == 'player') && $target_id != $player->id()) {
	            $return_to = 'player';
	        } else {
	            $return_to = 'inventory';
	        }

	        // Exceptions to the rules, using effects.

	        if ($item->hasEffect('wound')) {
	            // Minor damage by default items.
	            $item->setTargetDamage(rand(1, $item->getMaxDamage())); // DEFAULT, overwritable.

	            // e.g. Shuriken slices, for some reason.
	            if ($item->hasEffect('slice')) {
	                // Minor slicing damage.
	                $item->setTargetDamage(rand(1, max(9, $player->getStrength()-4)) + $near_level_power_increase);
	            }

	            // Piercing weapon, and actually does any static damage.
	            if ($item->hasEffect('pierce')) {
	                // Minor static piercing damage, e.g. 1-50 plus the near level power increase.
	                $item->setTargetDamage(rand(1, $item->getMaxDamage()) + $near_level_power_increase);
	            }

	            // Increased damage from damaging effects, minimum of 20.
	            if ($item->hasEffect('fire')) {
	                // Major fire damage
	                $item->setTargetDamage(rand(20, $player->getStrength() + 20) + $near_level_power_increase);
	            }
	        } // end of wounds section.

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

	        if ($give) {
	            $turn_cost  = 1;
	            $using_item = false;
	        } else {
	            $turn_cost  = $item->getTurnCost();
	        }

	        // Attack Legal section
	        $attacker = $player->name();

	        $params = [
	            'required_turns'  => $turn_cost,
	            'ignores_stealth' => $item_obj->ignoresStealth(),
	            'self_use'        => $item->isSelfUsable(),
	        ];

	        assert(!!$selfTarget || $attacker != $target);

	        $AttackLegal    = new AttackLegal($player, $targetObj, $params);
	        $attack_allowed = $AttackLegal->check();
	        $attack_error   = $AttackLegal->getError();

	        // *** Any ERRORS prevent attacks happen here  ***
	        if (!$attack_allowed) { //Checks for error conditions before starting.
	            $error = 1;
	        } else if (is_string($item) || $target == "")  {
	            $error = 2;
	        } else if ($item_count < 1) {
	            $error = 3;
	        } else {
	            /**** MAIN SUCCESSFUL USE ****/
	            if ($give) {
	                give_item($player->name(), $target, $item->getName());
	                $alternateResultMessage = "__TARGET__ will receive your {$item->getName()}.";
	            } else if (!$item->isOtherUsable()) {
	                // If it doesn't do damage or have an effect, don't use up the item.
	                $resultMessage = $result    = 'This item is not usable on __TARGET__, so it remains unused.';
	                $item_used = false;
	                $using_item = false;
	            } else {

	                if ($item->hasEffect('stealth')) {
	                    $targetObj->addStatus(STEALTH);
	                    $alternateResultMessage = "__TARGET__ is now stealthed.";
	                    $targetResult = ' be shrouded in smoke.';
	                }

	                if ($item->hasEffect('vigor')) {
	                    if ($targetObj->hasStatus(STR_UP1)) {
	                        $result = "__TARGET__'s body cannot become more vigorous!";
	                        $item_used = false;
	                        $using_item = false;
	                    } else {
	                        $targetObj->addStatus(STR_UP1);
	                        $result = "__TARGET__'s muscles experience a strange tingling.";
	                    }
	                }

	                if ($item->hasEffect('strength')) {
	                    if ($targetObj->hasStatus(STR_UP2)) {
	                        $result = "__TARGET__'s body cannot become any stronger!";
	                        $item_used = false;
	                        $using_item = false;
	                    } else {
	                        $targetObj->addStatus(STR_UP2);
	                        $result = "__TARGET__ feels a surge of power!";
	                    }
	                }

	                // Slow and speed effects are exclusive.
	                if ($item->hasEffect('slow')) {
	                    $turns_change = $item->getTurnChange();

	                    if ($targetObj->hasStatus(SLOW)) {
	                        // If the effect is already in play, it will have a decreased effect.
	                        $turns_change = ceil($turns_change*0.3);
	                        $alternateResultMessage = "__TARGET__ is already moving slowly.";
	                    } else if ($targetObj->hasStatus(FAST)) {
	                        $targetObj->subtractStatus(FAST);
	                        $alternateResultMessage = "__TARGET__ is no longer moving quickly.";
	                    } else {
	                        $targetObj->addStatus(SLOW);
	                        $alternateResultMessage = "__TARGET__ begins to move slowly...";
	                    }

	                    if ($turns_change == 0) {
	                        $alternateResultMessage .= " You fail to take any turns from __TARGET__.";
	                    }

	                    $targetResult = " lose ".abs($turns_change)." turns.";
	                    $targetObj->subtractTurns($turns_change);
	                } else if ($item->hasEffect('speed')) {	// Note that speed and slow effects are exclusive.
	                    $turns_change = $item->getTurnChange();

	                    if ($targetObj->hasStatus(FAST)) {
	                        // If the effect is already in play, it will have a decreased effect.
	                        $turns_change = ceil($turns_change*0.5);
	                        $alternateResultMessage = "__TARGET__ is already moving quickly.";
	                    } else if ($targetObj->hasStatus(SLOW)) {
	                        $targetObj->subtractStatus(SLOW);
	                        $alternateResultMessage = "__TARGET__ is no longer moving slowly.";
	                    } else {
	                        $targetObj->addStatus(FAST);
	                        $alternateResultMessage = "__TARGET__ begins to move quickly!";
	                    }

	                    // Actual turn gain is 1 less because 1 is used each time you use an item.
	                    $targetResult = " gain $turns_change turns.";
	                    $targetObj->changeTurns($turns_change); // Still adding some turns.
	                }

	                if ($item->getTargetDamage() > 0) { // *** HP Altering ***
	                    $alternateResultMessage .= " __TARGET__ takes ".$item->getTargetDamage()." damage.";

	                    if ($self_use) {
	                        $result .= "You take ".$item->getTargetDamage()." damage!";
	                    } else {
	                        if(strlen($targetResult) > 0){
	                            $targetResult .= " You also"; // Join multiple targetResult messages.
	                        }
	                        $targetResult .= " take ".$item->getTargetDamage()." damage!";
	                    }

	                    $targetObj->vo->health = $victim_alive = $targetObj->subtractHealth($item->getTargetDamage());
	                    // This is the other location that $victim_alive is set, to determine whether the death proceedings should occur.
	                }

	                if ($item->hasEffect('death')) {
	                    $targetObj->vo->health = setHealth($target_id, 0);

	                    $resultMessage = "The life force drains from __TARGET__ and they drop dead before your eyes!";
	                    $victim_alive  = false;
	                    $targetResult  = " be drained of your life-force and die!";
	                    $gold_mod      = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
	                }

	                if ($turns_change !== null) { // Even if $turns_change is set to zero, let them know that.
	                    if ($turns_change > 0) {
	                        $resultMessage .= "__TARGET__ has gained back $turns_change turns!";
	                    } else {
	                        if ($turns_change === 0) {
	                            $resultMessage .= "__TARGET__ did not lose any turns!";
	                        } else {
	                            $resultMessage .= "__TARGET__ has lost ".abs($turns_change)." turns!";
	                        }

	                        if (get_turns($target_id) <= 0) { //Message when a target has no more turns to remove.
	                            $resultMessage .= "  __TARGET__ no longer has any turns.";
	                        }
	                    }
	                }

	                if (empty($resultMessage) && !empty($result)) {
	                    $resultMessage = $result;
	                }

	                if (!$victim_alive) { // Target was killed by the item.
	                    if (!$self_use) {   // *** SUCCESSFUL KILL, not self-use of an item ***
	                        $attacker_id = ($player->hasStatus(STEALTH) ? "A Stealthed Ninja" : $player->name());

	                        if (!$gold_mod) {
	                            $gold_mod = 0.15;
	                        }
	                        $initial_gold = $targetObj->gold();
	                        $loot = floor($gold_mod * $initial_gold);
	                        $targetObj->set_gold($initial_gold-$loot);
	                        $player->set_gold($player->gold()+$loot);
	                        $player->save();
	                        $targetObj->save();
	                        $player->addKills(1);
	                        $kill = true;
	                        $bountyMessage = runBountyExchange($player->name(), $target);  //Rewards or increases bounty.
	                    } else {
	                        $loot = 0;
	                        $suicide = true;
	                    }

	                    // Send mails if the target was killed.
	                    send_kill_mails($player->name(), $target, $attacker_id, $article, $item->getName(), $loot);
	                } else { // They weren't killed.
	                    $attacker_id = $player->name();
	                }

	                if (!$self_use && $item_used) {
	                    if (!$targetResult) {
	                        error_log('Debug: Issue 226 - An attack was made using '.$item->getName().', but no targetResult message was set.');
	                    }

	                    // Notify targets when they get an item used on them.
	                    $message_to_target = "$attacker_id has used $article {$item->getName()} on you";
	                    if($targetResult){
	                        $message_to_target .= " and caused you to $targetResult";
	                    } else {
	                        $message_to_target .= '.';
	                    }
	                    send_event($player->id(), $target_id, str_replace('  ', ' ', $message_to_target));
	                }

	                // Unstealth
	                if (!$item->isCovert() && !$item->hasEffect('stealth') && $player->hasStatus(STEALTH)) { //non-covert acts
	                    $player->subtractStatus(STEALTH);
	                    $stealthLost = true;
	                } else {
	                    $stealthLost = false;
	                }
	            }

	            $targetName          = $targetObj->vo->uname;
	            $targetHealth        = $targetObj->vo->health;
	            $targetHealthPercent = $targetObj->health_percent();

	            $turns_to_take = 1;

	            if ($item_used) { // *** remove Item ***
	                removeItem($player->id(), $item->getName(), 1); // *** Decreases the item amount by 1.
	            }

	            if ($victim_alive && $using_item) {
	                $repeat = true;
	            }
	        }

	        // *** Take away at least one turn even on attacks that fail to prevent page reload spamming ***
	        if ($turns_to_take < 1) {
	            $turns_to_take = 1;
	        }

	        $ending_turns = subtractTurns($player->id(), $turns_to_take);
	        assert($item->hasEffect('speed') || $ending_turns < $starting_turns || $starting_turns == 0);

			return [
				'template' => 'inventory_mod.tpl',
				'title'    => 'Use Item',
				'parts'    => get_defined_vars(),
				'options'  => [
					'body_classes' => 'inventory-use',
					'quickstat'    => 'player'
					],
				];
		} // Item was not valid object
	}


}
