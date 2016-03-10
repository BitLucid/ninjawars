<?php
namespace NinjaWars\core\control;

use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\control\Combat;
use \Player;
use \PDO;

/**
 * Control the display of items and gold (and maybe some day armor) for a char
 */
class InventoryController {
	const PRIV  = true;
	const ALIVE = false;
    const GIVE_COST = 1;
    const GIVE_QUANTITY = 1;

	/**
	 * View items and gold of char
	 */
	public function index() {
		$char = new Player(self_char_id());

		$inv_counts = inventory_counts($char->id());
		$inventory = array();

		if ($inv_counts) {
			// Standard item info.
			$standard_items = $this->standardItems();
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

	private function render($parts) {
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
    public function give() {
        $slugs     = $this->parse_slugs($self_use);
        $link_back = $slugs['link_back'];
        $player    = Player::find(self_char_id());
        $target    = $this->findPlayer($slugs['in_target']);

        try {
            $item = $this->findItem($slugs['item_in']);
        } catch (\InvalidArgumentException $e) {
            return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
        }

        $article         = self::getIndefiniteArticle($item->getName());
        $display_message = "__TARGET__ will receive your ".$item->getName().".";
        $mail_message    = "You have been given $article $item by $player.";

        if ($this->itemCount($player->id(), $item) < 1) {
            $error = 3;
        } else {
            $error = 0;
            add_item($target->id(), $item->identity(), self::GIVE_QUANTITY);
            removeItem($player->id(), $item->getName(), self::GIVE_QUANTITY);
            sendMessage($player->name(), $target->name(), $mail_message);
            $player->subtractTurns(self::GIVE_COST);
        }

        return [
            'template' => 'inventory_mod.tpl',
            'title'    => 'Use Item',
            'parts'    => [
                'error'                  => $error,
                'targetObj'              => $target,
                'resultMessage'          => $display_message,
                'alternateResultMessage' => null,
                'kill'                   => false,
                'stealthLost'            => false,
                'suicide'                => false,
                'repeat'                 => false,
                'return_to'              => 'player',
            ],
            'options'  => [
                'body_classes' => 'inventory-use',
                'quickstat'    => 'player'
            ],
        ];
    }

    /**
     * Helper to find a player by either id or name
     */
    private function findPlayer($token) {
        if (positive_int($token)) {
            $target = Player::find(positive_int($token));
        } else {
            $target = Player::findByName($token);
        }

        return $target;
    }

    /**
     * Helper to find an item by either id or identity
     */
    private function findItem($token) {
	    if ($token == (int) $token && is_numeric($token)) {
	        $item = getItemByID($token);
	    } elseif (is_string($token)) {
	        $item = $this->getItemByIdentity($token);
	    } else {
            throw new \InvalidArgumentException('');
	    }

        return $item;
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
    private function parse_slugs($self_use = false) {
        $url_part   = $_SERVER['REQUEST_URI'];
        $path       = parse_url($url_part, PHP_URL_PATH);
        $slugs      = explode('/', trim($path, '/'));
        $selfTarget = whichever(in('selfTarget'), $self_use);
        $item_in    = $slugs[2];
        $in_target  = (isset($slugs[3])? $slugs[3] : null);
        $link_back  = whichever(in('link_back'),
            ($selfTarget? 'inventory' : null)
        );

        return [
            'link_back'  => $link_back,
            'item_in'    => $item_in,
            'in_target'  => $in_target,
            'selfTarget' => $selfTarget,
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

        $slugs                  = $this->parse_slugs($self_use);
        $link_back              = $slugs['link_back'];
        $selfTarget             = $slugs['selfTarget'];
        $item_in                = $slugs['item_in']; // Item identifier, either it's id or internal name
        $in_target              = $slugs['in_target'];
        $target                 = $in_target;
        $give                   = in_array($give, array('on', 'Give'));
        $player                 = Player::find(self_char_id());
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
        $bountyMessage          = '';
        $resultMessage          = '';
        $alternateResultMessage = '';

        if (positive_int($in_target)) {
            $target_id = positive_int($in_target);
        } else {
            $target_id = get_char_id($in_target);
        }

        try {
            $item = $this->findItem($slugs['item_in']);
        } catch (\InvalidArgumentException $e) {
            return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
        }

        $item_count = $this->itemCount($player->id(), $item);

        // Check whether use on self is occurring.
        $self_use = ($selfTarget || ($target_id === $player->id()));

        if ($self_use) {
            $target    = $player->name();
            $targetObj = $player;
        } else if ($target_id) {
            $targetObj = Player::find($target_id);
            $target    = $targetObj->name();
        }

        $starting_turns = $player->turns;
        $username_turns = $starting_turns;
        $username_level = $player->level;

        if (($targetObj instanceof Player) && $targetObj->id()) {
            $targets_turns = $targetObj->turns;
            $targets_level = $targetObj->level;
            $target_hp     = $targetObj->health;
        } else {
            $targets_turns =
                $targets_level =
                $target_hp     = null;
        }

        $max_power_increase        = 10;
        $level_difference          = $targets_level - $username_level;
        $level_check               = $username_level - $targets_level;
        $near_level_power_increase = $this->nearLevelPowerIncrease($level_difference, $max_power_increase);

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
            $item->setTurnChange(-1*$this->caltropTurnLoss($targets_turns, $near_level_power_increase));
        } else if ($item->hasEffect('speed')) {
            $item->setTurnChange($item->getMaxTurnChange());
        }

        $turn_change = $item->getTurnChange();

        $itemName = $item->getName();
        $itemType = $item->getType();

        $article = self::getIndefiniteArticle($item->getName());

        $turn_cost  = $item->getTurnCost();

        // Attack Legal section
        $attacker = $player->name();

        $params = [
            'required_turns'  => $turn_cost,
            'ignores_stealth' => $item->ignoresStealth(),
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
        } else if (!$item->isOtherUsable()) {
            // If it doesn't do damage or have an effect, don't use up the item.
            $resultMessage = $result    = 'This item is not usable on __TARGET__, so it remains unused.';
            $item_used = false;
            $using_item = false;
        } else {
            /**** MAIN SUCCESSFUL USE ****/
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

                $victim_alive = $targetObj->subtractHealth($item->getTargetDamage());
                // This is the other location that $victim_alive is set, to determine whether the death proceedings should occur.
            }

            if ($item->hasEffect('death')) {
                $targetObj->death();

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

                    if ($targetObj->turns <= 0) {
                        // Message when a target has no more turns to remove.
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
                    $bountyMessage = Combat::runBountyExchange($player->name(), $target);  //Rewards or increases bounty.
                } else {
                    $loot = 0;
                    $suicide = true;
                }

                // Send mails if the target was killed.
                $this->sendKillMails($player->name(), $target, $attacker_id, $article, $item->getName(), $loot);
            } else { // They weren't killed.
                $attacker_id = $player->name();
            }

            if (!$self_use && $item_used) {
                if (!$targetResult) {
                    error_log('Debug: Issue 226 - An attack was made using '.$item->getName().', but no targetResult message was set.');
                }

                // Notify targets when they get an item used on them.
                $message_to_target = "$attacker_id has used $article {$item->getName()} on you";

                if ($targetResult) {
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

            $targetName   = $targetObj->uname;
            $targetHealth = $targetObj->health;

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

        $ending_turns = $player->subtractTurns($turns_to_take);
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
    }

    /**
     * Send out the killed messages.
     */
    private function sendKillMails($username, $target, $attacker_id, $article, $item, $loot) {
        $target_email_msg   = "You have been killed by $attacker_id with $article $item and lost $loot gold.";
        sendMessage($attacker_id,$target,$target_email_msg);

        $user_email_msg     = "You have killed $target with $article $item and received $loot gold.";
        sendMessage($target,$username,$user_email_msg);
    }

    /**
     * Item data for the inventory.
     */
    private function standardItems() {
        // Codename means it can have a link to be used, apparently...
        // Pull this from the database.
        $it = query('select * from item');

        $res = array();
        // Format the items for display on the inventory.
        foreach ($it as $item) {
            $item['codename'] = $item['item_display_name'];
            $item['display'] = $item['item_display_name'].$item['plural'];
            $res[$item['item_id']] = $item;
        }

        return $res;
    }

    /**
     * Get an item by it's item identity
     */
    private function getItemByIdentity($p_itemIdentity) {
        return buildItem(item_info_from_identity($p_itemIdentity));
    }

    /**
     * Get the count of how many of an item a player has.
     */
    private function itemCount($user_id, $item_display_name) {
        $statement = query("SELECT sum(amount) FROM inventory join item on inventory.item_type = item.item_id WHERE owner = :owner AND lower(item_display_name) = lower(:item)",
            array(':owner'=>array($user_id, PDO::PARAM_INT), ':item'=>strtolower($item_display_name)));
        return $statement->fetchColumn();
    }

    /**
     * Benefits for near-equivalent levels.
     */
    private function nearLevelPowerIncrease($level_difference, $max_increase) {
        $res = 0;
        $coeff = abs($level_difference);
        if ($coeff < $max_increase) {
            $res = $max_increase-$coeff;
        }

        return $res;
    }

    /**
     * Determine the turns for caltrops, which was once ice scrolls.
     */
    private function caltropTurnLoss($targets_turns, $near_level_power_increase) {
        if ($targets_turns>50) {
            $turns_decrease = rand(1,8)+$near_level_power_increase; // *** 1-11 + 0-10
        } elseif ($targets_turns>20) {
            $turns_decrease = rand(1, 5)+$near_level_power_increase;
        } elseif ($targets_turns>3) {
            $turns_decrease = rand(1, 2)+($near_level_power_increase? 1 : 0);
        } else { // *** Players are always left with 1 or two turns.
            $turns_decrease = 0;
        }

        return $turns_decrease;
    }

    /**
     * Get an input display name and turn it into the internal name for use in the actual script.
     */
    private function itemIdentityFromDisplayName($item_display_name){
        return item_info(item_id_from_display_name($item_display_name), 'item_internal_name');
    }

    public static function getIndefiniteArticle($p_noun) {
        return str_replace(' '.$p_noun, '', shell_exec('perl '.LIB_ROOT.'third-party/lingua-a.pl "'.escapeshellcmd($p_noun).'"'));
    }
}
