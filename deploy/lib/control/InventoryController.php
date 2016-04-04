<?php
namespace NinjaWars\core\control;

use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\Item;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\control\Combat;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Event;
use \PDO;

/**
 * Control the display of items and gold (and maybe some day armor) for a char
 */
class InventoryController {
	const PRIV          = true;
	const ALIVE         = false;
    const GIVE_COST     = 1;
    const GIVE_QUANTITY = 1;
    const MAX_BONUS     = 10;

	/**
	 * View items and gold of char
     *
     * @return ViewSpec
	 */
	public function index() {
		$char       = Player::find(self_char_id());
		$inv = Inventory::of($char, 'self');
		$inventory  = [];
        $error = in('error');
        if($error === 'noitem'){
            $error = 'No such item';
        }
		foreach($inv as $item){
			// Special format for display and looping
			$item['display'] = $item['item_display_name'].$item['plural'];
			$item['self_use'] = (bool) $item['self_use'];
			$inventory[$item['item_id']] = $item;
		}

		$parts = [
            'error'        => $error,
			'gold'         => $char->gold,
			'gold_display' => number_format($char->gold),
			'inventory'    => $inventory,
			'username'     => $char->name(),
			'char_id'      => $char->id(),
		];

		return $this->render($parts);
	}

	/**
	 * Give an object to a target
     *
     * http://nw.local/item/give/shuriken/10/
     *
     * @return ViewSpec
	 */
    public function give() {
        $slugs  = $this->parseSlugs();
        $player = Player::find(self_char_id());
        $target = $this->findPlayer($slugs['in_target']);

        try {
            $item    = $this->findItem($slugs['item_in']);
            $article = self::getIndefiniteArticle($item->getName());
        } catch (\InvalidArgumentException $e) {
            return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
        }

        if (empty($target)) {
            $error = 2;
        } else if ($this->itemCount($player, $item) < 1) {
            $error = 3;
        } else {
            $error = 0;

            $display_message = "__TARGET__ will receive your ".$item->getName().".";
            $mail_message    = "You have been given $article $item by $player.";

            $this->transferOwnership($player, $target, $item, self::GIVE_QUANTITY);

            $player->subtractTurns(self::GIVE_COST);
            $player->save();

            Event::create($player->id(), $target->id(), $mail_message);
        }

        return $this->renderUse([
            'error'                  => $error,
            'target'                 => $target,
            'resultMessage'          => $display_message,
            'alternateResultMessage' => null,
            'stealthLost'            => false,
            'repeat'                 => false,
            'return_to'              => 'player',
            'item'                   => $item,
            'bountyMessage'          => null,
            'article'                => $article,
        ]);
    }

	/**
	 * Use an object on myself
     *
     * http://nw.local/item/self_use/amanita/
     *
     * @return ViewSpec
	 */
    public function selfUse() {
        $slugs           = $this->parseSlugs();
        $player          = Player::find(self_char_id());
        $inventory       = new Inventory($player);
        $had_stealth     = $player->hasStatus(STEALTH);
        $turns_to_take   = 1; // Take away one turn even on attacks that fail to prevent page reload spamming
        $display_message = 'This item cannot be used on yourself!';
        $error           = 1;
        $extra_message   = null;

        try {
            $item = $this->findItem($slugs['item_in']);
            $article = self::getIndefiniteArticle($item->getName());
        } catch (\InvalidArgumentException $e) {
            return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
        }

        if ($this->itemCount($player, $item) < 1) {
            $error = 3;
        } else if ($item->isSelfUsable()) {
            $params = [
                'required_turns'  => $item->getTurnCost(),
                'ignores_stealth' => $item->ignoresStealth(),
                'self_use'        => true,
            ];

            $attack_legal = new AttackLegal($player, $player, $params);

            if (!$attack_legal->check()) {
                $error           = 1;
                $display_message = $attack_legal->getError();
            } else {
                $error  = null;
                $result = $this->applyItemEffects($player, $player, $item);

                if ($result['success']) {
                    $inventory->remove($item->identity(), 1);

                    if ($player->health() <= 0) {
                        $this->sendKillMails($player, $player, $player->name(), $article, $item->getName(), 0);
                    }
                }

                $display_message = $result['message'];
                $extra_message   = $result['extra_message'];
            }
        }

        if($turns_to_take > 0 && ($player->turns - $turns_to_take >= 0)){
            $player->subtractTurns($turns_to_take);
        }
        $player->save();

        return $this->renderUse([
            'error'                  => $error,
            'target'                 => $player,
            'resultMessage'          => $display_message,
            'alternateResultMessage' => $extra_message,
            'stealthLost'            => $had_stealth && !$player->hasStatus(STEALTH),
            'repeat'                 => ($player->health() > 0),
            'return_to'              => 'inventory',
            'item'                   => $item,
            'action'                 => 'self_use',
            'bountyMessage'          => null,
            'loot'                   => 0,
            'article'                => $article,
        ]);
    }

	/**
	 * Use an item on a target
     *
     * http://nw.local/item/use/shuriken/10/
     *
     * @return ViewSpec
     * @note
     * /use/ is aliased to useItem externally because use is a php reserved keyword
	 */
    public function useItem() {
        $slugs           = $this->parseSlugs();
        $target          = $this->findPlayer($slugs['in_target']);
        $player          = Player::find(self_char_id());
        $inventory       = new Inventory($player);
        $had_stealth     = $player->hasStatus(STEALTH);
        $error           = false;
        $turns_to_take   = 1; // Take away one turn even on attacks that fail to prevent page reload spamming
        $result          = null;
        $bounty_message  = '';
        $display_message = '';
        $extra_message   = '';
        $attacker_label  = $player->name();
        $loot            = null;

        try {
            $item    = $this->findItem($slugs['item_in']);
            $article = $item? self::getIndefiniteArticle($item->getName()) : '';
        } catch (\InvalidArgumentException $e) {
            return new RedirectResponse(WEB_ROOT.'inventory?error=noitem');
        }

        if (empty($target)) {
            $error = 2;
        } else if ($this->itemCount($player, $item) < 1) {
            $error = 3;
        } else if ($target->id() === $player->id()) {
            return $this->selfUse();
        } else {
            $params = [
                'required_turns'  => $item->getTurnCost(),
                'ignores_stealth' => $item->ignoresStealth(),
            ];

            $attack_legal = new AttackLegal($player, $target, $params);

            if (!$attack_legal->check()) {
                $error           = 1;
                $display_message = $attack_legal->getError();
            } else if (!$item->isOtherUsable()) {
                $error           = 1;
                $display_message = 'This item cannot be used on others!';
            } else {
                $result = $this->applyItemEffects($player, $target, $item);

                if ($result['success']) {
                    $message_to_target = "$attacker_label has used $article ".$item->getName()." on you$result[notice]";
                    Event::create($player->id(), $target->id(), str_replace('  ', ' ', $message_to_target));
                    $inventory->remove($item->identity(), 1);

                    if ($target->health() <= 0) { // Target was killed by the item
                        $attacker_label = ($player->hasStatus(STEALTH) ? "A Stealthed Ninja" : $player->name());

                        $gold_mod = ($item->hasEffect('death') ?  0.25 : 0.15);
                        $loot     = floor($gold_mod * $target->gold);

                        $target->set_gold($target->gold - $loot);

                        $player->set_gold($player->gold + $loot);
                        $player->addKills(1);

                        $bounty_message = Combat::runBountyExchange($player, $target);  //Rewards or increases bounty.

                        $this->sendKillMails($player, $target, $attacker_label, $article, $item->getName(), $loot);
                    }
                }

                $display_message = $result['message'];
                $extra_message   = $result['extra_message'];
            }

            $player->subtractTurns($turns_to_take);

            $target->save();
            $player->save();
        }

        return $this->renderUse([
            'action'                 => 'use',
            'return_to'              => (in_array(in('link_back'), ['', 'player']) ? 'player' : 'inventory'),
            'error'                  => $error,
            'target'                 => $target,
            'resultMessage'          => $display_message,
            'alternateResultMessage' => $extra_message,
            'stealthLost'            => ($had_stealth && $player->hasStatus(STEALTH)),
            'repeat'                 => (($target->health() > 0) && empty($error)),
            'item'                   => $item,
            'bountyMessage'          => $bounty_message,
            'article'                => $article,
            'loot'                   => $loot,
        ]);
    }

    /**
     * @return void
     */
    private function transferOwnership(Player $giver, Player $recipient, Item $item, $quantity) {
        $giver_inventory = new Inventory($giver);
        $taker_inventory = new Inventory($recipient);
        $taker_inventory->add($item->identity(), $quantity);
        $giver_inventory->remove($item->identity(), $quantity);
    }

    /**
     * Given 2 players and an item, mutate player states based on item effects
     *
     * @return Array
     * @note
     * Slow and speed effects are exclusive.
     */
    private function applyItemEffects(Player $user, Player $target, Item $item) {
        $success       = true;
        $notice        = null;
        $message       = '';
        $extra_message = '';
        $turns_change  = null;

        $bonus = $this->calculateBonus($user, $target);

        if ($item->hasEffect('wound')) {
            $item->setTargetDamage(rand(1, $item->getMaxDamage()));

            if ($item->hasEffect('slice')) {
                $item->setTargetDamage(rand(1, max(9, $user->getStrength() - 4)) + $bonus);
            }

            if ($item->hasEffect('pierce')) {
                $item->setTargetDamage(rand(1, $item->getMaxDamage()) + $bonus);
            }

            if ($item->hasEffect('fire')) {
                $item->setTargetDamage(rand(20, $user->getStrength() + 20) + $bonus);
            }
        }

        if ($item->hasEffect('stealth')) {
            $target->addStatus(STEALTH);
            $extra_message = "__TARGET__ is now stealthed.";
            $notice = ' be shrouded in smoke.';
        }

        if ($item->hasEffect('vigor')) {
            if ($target->hasStatus(STR_UP1)) {
                $message = "__TARGET__'s body cannot become more vigorous!";
                $success = false;
            } else {
                $target->addStatus(STR_UP1);
                $message = "__TARGET__'s muscles experience a strange tingling.";
            }
        }

        if ($item->hasEffect('strength')) {
            if ($target->hasStatus(STR_UP2)) {
                $message = "__TARGET__'s body cannot become any stronger!";
                $success = false;
            } else {
                $target->addStatus(STR_UP2);
                $message = "__TARGET__ feels a surge of power!";
            }
        }

        if ($item->hasEffect('slow')) {
            $item->setTurnChange(-1*$this->caltropTurnLoss($target, $bonus));
            $turns_change = $item->getTurnChange();

            if ($target->hasStatus(SLOW)) {
                // If the effect is already in play, it will have a decreased effect.
                $turns_change = ceil($turns_change*0.3);
                $extra_message = "__TARGET__ is already moving slowly.";
            } else if ($target->hasStatus(FAST)) {
                $target->subtractStatus(FAST);
                $extra_message = "__TARGET__ is no longer moving quickly.";
            } else {
                $target->addStatus(SLOW);
                $extra_message = "__TARGET__ begins to move slowly...";
            }

            if ($turns_change == 0) {
                $extra_message .= " You fail to take any turns from __TARGET__.";
            }

            $notice = " lose ".abs($turns_change)." turns.";
            $target->subtractTurns($turns_change);
        } else if ($item->hasEffect('speed')) {
            $item->setTurnChange($item->getMaxTurnChange());
            $turns_change = $item->getTurnChange();

            if ($target->hasStatus(FAST)) {
                // If the effect is already in play, it will have a decreased effect.
                $turns_change = ceil($turns_change*0.5);
                $extra_message = "__TARGET__ is already moving quickly.";
            } else if ($target->hasStatus(SLOW)) {
                $target->subtractStatus(SLOW);
                $extra_message = "__TARGET__ is no longer moving slowly.";
            } else {
                $target->addStatus(FAST);
                $extra_message = "__TARGET__ begins to move quickly!";
            }

            $notice = " gain $turns_change turns.";
            $target->changeTurns($turns_change);
        }

        if ($item->getTargetDamage() > 0) { // HP Altering
            $extra_message .= " __TARGET__ takes ".$item->getTargetDamage()." damage.";

            if ($user->id() === $target->id()) {
                $message .= "You take ".$item->getTargetDamage()." damage!";
            } else {
                if (strlen($notice) > 0) {
                    $notice .= " You also"; // Join multiple targetResult messages.
                }

                $notice .= " take ".$item->getTargetDamage()." damage!";
            }

            $target->subtractHealth($item->getTargetDamage());
        }

        // if the item was meant to affect turns, even if the net change was 0
        if ($turns_change !== null) {
            if ($turns_change > 0) {
                $message .= "__TARGET__ has gained back $turns_change turns!";
            } else {
                if ($turns_change === 0) {
                    $message .= "__TARGET__ did not lose any turns!";
                } else {
                    $message .= "__TARGET__ has lost ".abs($turns_change)." turns!";
                }

                if ($target->turns <= 0) {
                    // Message when a target has no more turns to remove.
                    $message .= "  __TARGET__ no longer has any turns.";
                }
            }
        }

        if ($item->hasEffect('death')) {
            $target->death();

            $message = "The life force drains from __TARGET__ and they drop dead before your eyes!";
            $notice  = " be drained of your life-force and die!";
        }

        // Unstealth
        if (!$item->isCovert() && !$item->hasEffect('stealth') && $user->hasStatus(STEALTH)) { //non-covert acts
            $user->subtractStatus(STEALTH);
        }

        $target->save();
        $user->save();

        if ($notice) {
            $notice = " and caused you to $notice.";
        } else {
            $notice = '.';
        }

        return [
            'success'       => $success,
            'message'       => $message,
            'extra_message' => $extra_message,
            'notice'        => $notice,
        ];
    }

	/**
	 * Get the slugs and parameter values.
     *
     * @return Array
	 */
    private function parseSlugs() {
        $url_part = $_SERVER['REQUEST_URI'];
        $path     = parse_url($url_part, PHP_URL_PATH);
        $slugs    = explode('/', trim($path, '/'));

        return [
            'item_in'    => $slugs[2],
            'in_target'  => (isset($slugs[3]) ? $slugs[3] : null),
        ];
    }

    /**
     * Send out the killed messages.
     *
     * @return void
     */
    private function sendKillMails(Player $attacker, Player $target, $attacker_label, $article, $item, $loot) {
        $target_email_msg = "You have been killed by $attacker_label with $article $item and lost $loot gold.";
        Event::create(($attacker->name() === $attacker_label ? $attacker->id() : 0), $target->id(), $target_email_msg);

        $user_email_msg = "You have killed ".$target->name()." with $article $item and received $loot gold.";
        Event::create($target->id(), $attacker->id(), $user_email_msg);
    }

    /**
     * Get the count of how many of an item a player has.
     *
     * @return int
     */
    private function itemCount(Player $player, Item $item) {
        $statement = query("SELECT sum(amount) FROM inventory WHERE item_type = :item AND owner = :owner",
            [
                ':owner' => $player->id(),
                ':item'  => $item->id(),
            ]
        );


        return $statement->fetchColumn();
    }

    /**
     * Benefits for near-equivalent levels.
     *
     * @return int
     */
    private function calculateBonus(Player $user, Player $target) {
        $bonus    = 0;
        $distance = abs($target->level - $user->level);

        if ($distance < self::MAX_BONUS) {
            $bonus = self::MAX_BONUS - $distance;
        }

        return $bonus;
    }

    /**
     * Determine the turns for caltrops
     *
     * @return int
     * @note
     * Caltrops used to be ice scrolls.
     */
    private function caltropTurnLoss(Player $target, $bonus) {
        $min = 1;
        $max = 0;

        if ($target->turns > 50) {
            $max = 8;
        } elseif ($target->turns > 20) {
            $max = 5;
        } elseif ($target->turns > 3) {
            $max = 2;
            $bonus = ($bonus > 0 ? 1 : 0);
        } else { // no effect when low on turns
            $min = 0;
            $bonus = 0;
        }

        return rand($min, $max) + $bonus;
    }

    /**
     * Helper to find a player by either id or name
     *
     * @return Player
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
     *
     * @return Item
     */
    private function findItem($token) {
	    if ($token == (int) $token && is_numeric($token) && $token) {
	        $item = Item::find($token);
	    } elseif (is_string($token) && $token) {
            $item = Item::findByIdentity($token);
	    } else {
            throw new \InvalidArgumentException('Invalid item identity requested.');
	    }

        return $item;
    }

    /**
     * @return ViewSpec
     */
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
     * @return ViewSpec
     */
    private function renderUse($parts) {
        return [
            'template' => 'inventory_mod.tpl',
            'title'    => 'Use Item',
            'parts'    => $parts,
            'options'  => [
                'body_classes' => 'inventory-use',
                'quickstat'    => 'player'
            ],
        ];
    }

    /**
     * @return String
     */
    public static function getIndefiniteArticle($p_noun) {
        return str_replace(' '.$p_noun, '', shell_exec('perl '.LIB_ROOT.'third-party/lingua-a.pl "'.escapeshellcmd($p_noun).'"'));
    }
}
