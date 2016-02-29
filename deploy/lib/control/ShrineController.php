<?php
namespace NinjaWars\core\control;

require_once(CORE.'control/Skill.php');
require_once(CORE.'control/lib_inventory.php');

use \Player as Player;
use \Skill as Skill;

/**
 * Controller for actions taken in the Healing Shrine
 */
class ShrineController { //extends controller
	const ALIVE                = false;
	const PRIV                 = true;
	const FREE_RES_LEVEL_LIMIT = 6;
	const FREE_RES_KILL_LIMIT  = 25;
	const HEAL_POINT_COST      = 1;
	const RES_COST_TURNS       = 10;
	const RES_COST_KILLS       = 1;
	const BASE_RES_HP          = 100;
	const CURE_COST_GOLD       = 100;
	const RES_COST_TYPE_FREE   = 1;
	const RES_COST_TYPE_KILL   = 2;
	const RES_COST_TYPE_TURN   = 3;

	public static $resurrectResultViews = [
		self::RES_COST_TYPE_KILL => 'result-resurrect-kill',
		self::RES_COST_TYPE_TURN => 'result-resurrect-turn',
		self::RES_COST_TYPE_FREE => 'result-resurrect-free',
	];

	/**
	 * Renders the initial view of the Shrine with forms based on player state
	 *
	 * @return Array
	 * @see servicesNeeded
	 */
	public function index() {
		$player = new Player(self_char_id());

		$pageParts = $this->servicesNeeded($player);

		if (empty($pageParts)) {
			return $this->renderError('You have no need of healing.', $player);
		}

		array_unshift($pageParts, 'entrance');

		return $this->render([
			'pageParts'        => $pageParts,
			'player'           => $player,
			'freeResurrection' => $this->isResurrectFree($player),
		]);
	}

	/**
	 * Command to resurrect (if dead) and heal the maximum possible amount (always)
	 *
	 * @return Array
	 * @see _heal
	 * @see _resurrect
	 */
	public function healAndResurrect() {
		$skillController = new Skill();

		$player = new Player(self_char_id());

		try {
			$pageParts = [];

			if ($player->health() <= 0) {
				$costType = $this->_resurrect($player);

				$pageParts[] ='result-resurrect';
				$pageParts[] = self::$resurrectResultViews[$costType];
			}

			$healAmount = $this->calculateMaxHeal($player);

			if ($healAmount > 0 && $player->is_hurt_by() > 0) {
				$this->_heal($player, $healAmount);
				$pageParts[] = 'result-heal';
			}

			if (empty($pageParts)) {
				$pageParts[] = 'entrance';
			}

			$pageParts = array_merge($pageParts, $this->servicesNeeded($player));

			return $this->render([
				'player'         => $player,
				'pageParts'      => $pageParts,
				'killCost'       => self::RES_COST_KILLS,
				'turnCost'       => self::RES_COST_TURNS,
				'has_chi'        => $skillController->hasSkill('Chi', $player->name()),
			]);
		} catch (\RuntimeException $e) {
			return $this->renderError($e->getMessage(), $player);
		} catch (\InvalidArgumentException $e) {
			return $this->renderError($e->getMessage(), $player);
		}
	}

	/**
	 * Command to resurrect the current player, if dead.
	 *
	 * @return Array
	 * @see _resurrect
	 */
	public function resurrect() {
		$player = new Player(self_char_id());

		try {
			$costType = $this->_resurrect($player);

			$pageParts = array_merge(
				[
					'result-resurrect',
					self::$resurrectResultViews[$costType],
				],
				$this->servicesNeeded($player)
			);

			return $this->render([
				'pageParts' => $pageParts,
				'player'    => $player,
				'killCost'  => self::RES_COST_KILLS,
				'turnCost'  => self::RES_COST_TURNS,
			]);
		} catch (\RuntimeException $e) {
			return $this->renderError($e->getMessage(), $player);
		}
	}

	/**
	 * Command to heal the current player by the specified amount
	 *
	 * @param heal_points Mixed The amount of healing desires as an int or the special value 'max'
	 * @return Array
	 * @see _heal
	 */
	public function heal() {
		$skillController = new Skill();

		$player = new Player(self_char_id()); // get current player

		$healAmount = in('heal_points');

		if ($healAmount === 'max') {
			$healAmount = max(1, $this->calculateMaxHeal($player));
		}

		try {
			$this->_heal($player, (int)$healAmount);

			$pageParts = $this->servicesNeeded($player);
			array_unshift($pageParts, 'result-heal');

			return $this->render([
				'pageParts'      => $pageParts,
				'player'         => $player,
				'has_chi'        => $skillController->hasSkill('Chi', $player->name()),
			]);
		} catch (\RuntimeException $e) {
			return $this->renderError($e->getMessage(), $player);
		} catch (\InvalidArgumentException $e) {
			return $this->renderError($e->getMessage(), $player);
		}
	}

	/**
	 * Command to remove the POISON status from the current player, if poisoned
	 *
	 * @return Array
	 * @par Side Effects:
	 * On success, status attribute of $p_player is modified in memory and database
	 * On success, gold attribute of $p_player is modified in memory and database
	 */
	public function cure() {
		$player = new Player(self_char_id()); // get current player

		if ($player->health() <= 0) {
			return $this->renderError('You must resurrect before you can heal.', $player);
		} else if ($player->gold < self::CURE_COST_GOLD) {
			return $this->renderError('You need more gold to remove poison.', $player);
		} else if (!$player->hasStatus(POISON)) {
			return $this->renderError('You are not ill.', $player);
		} else {
			$player->subtractStatus(POISON);
			$player->vo->gold = subtract_gold($player->id(), self::CURE_COST_GOLD);

			$pageParts = [
				'chant',
				'result-cure',
			];

			return $this->render([
				'pageParts' => array_merge($pageParts, $this->servicesNeeded($player)),
			]);
		}
	}

	/**
	 * Determine the list of page parts to be shown based on Player state
	 *
	 * @param p_player Player
	 * @return Array
	 * @note
	 * An empty array denotes that no services are needed.
	 * Currently being reminded that you have max HP is a service
	 */
	private function servicesNeeded($p_player) {
		$services = [];

		if ($p_player->id()) {
			if ($p_player->health()) {
				if ($p_player->health() < $p_player->max_health()) {
					$services[] = 'form-heal';
				} else {
					$services[] = 'reminder-full-hp';
				}

				if ($p_player->hasStatus(POISON)) {
					$services[] = 'form-cure';
				}
			} else {
				$services[] = 'form-resurrect';

                try {
                    if ($this->calculateResurrectionCost($p_player)) {
                        $services[] = 'reminder-resurrect-cost';
                    }
                } catch (\RuntimeException $e) {
                    // intentionally squash validation exception -ajv-
                }
			}
		}

		return $services;
	}

	/**
	 * End-user resurrect operation, incurs costs and revives the dead player
	 *
	 * @param p_player Player the player to resurrect
	 * @return int The value of the resurrect cost type
	 * @throws RuntimeException Player is not dead
	 *
	 * @par Side Effects:
	 * The health attribute of $p_player is changed in memory and database
	 *
	 * @par Preconditions:
	 * Player must be dead to resurrect
	 *
	 * @note
	 * If the Player qualifies for enhanced resurrection effects, enhancedResurrect will be called
	 *
	 * @see enhancedResurrect
	 */
	private function _resurrect($p_player) {
		if ($p_player->health() <= 0) {
			$costType = $this->calculateResurrectionCost($p_player);

			if ($costType === self::RES_COST_TYPE_KILL) {
				$this->enhancedResurrect($p_player);
			} else  {
				$p_player->death();
				$p_player->heal($this->calculateResurrectionHP($p_player));
			}

			if ($costType === self::RES_COST_TYPE_KILL) {
				$p_player->vo->kills = $p_player->subtractKills(self::RES_COST_KILLS);
			} else if ($costType === self::RES_COST_TYPE_TURN) {
				$p_player->subtractTurns(min(self::RES_COST_TURNS, $p_player->turns));
			}

			return $costType;
		} else {
			throw new \RuntimeException('You are not dead.');
		}
	}

	/**
	 * The enhanced resurrection operation adds level-based health and status effects
	 *
	 * @param p_player Player The player to perform enhanced resurrection on
	 * @return Void
	 * @par Side Effects:
	 * The health attribute of $p_player is changed in memory and database
	 * The status attribute of $p_player may be changed in memory and database
	 *
	 * @note
	 * The Chi skill triples base health after resurrection
	 * The Hidden Resurrect skill stealths player after non-free resurrection
	 */
	private function enhancedResurrect($p_player) {
		$p_player->death();

		$skillController = new Skill(); // Instantiate Skill interrogator

		$normalHP = $this->calculateResurrectionHP($p_player);

		$enhancedHP = $normalHP + (($p_player->level-1)*10);

		$p_player->heal($enhancedHP);

		if ($skillController->hasSkill('hidden resurrect', $p_player->name())) {
			$p_player->addStatus(STEALTH);
		}
	}

	/**
	 * Cacluates the starting health for a player when resurrected based on player state
	 *
	 * @param p_player Player The player object to interrogate
	 * @return int
	 */
	private function calculateResurrectionHP($p_player) {
		$skillController = new Skill(); // Instantiate Skill interrogator

		// chi triples base health after res
		if ($skillController->hasSkill('Chi', $p_player->name())) {
			$hpMultiplier = 3;
		} else {
			$hpMultiplier = 1;
		}

		$maxHP = self::BASE_RES_HP*$hpMultiplier;

		return min($maxHP, $p_player->max_health());
	}

	/**
	 * Returns a Cost Type constant based on the state of p_player
	 *
	 * If a player qualifies for a free resurrection then resurrection is free.
	 * If a player does not but does have kills then the resurrection costs kills.
	 * If a player does not meet the above criteria but has turns the resurrection costs turns.
	 * If none of these are true a RuntimeException is thrown.
	 *
	 * @param p_player Player The player object to interrogate
	 * @return int
	 * @throws RuntimeException When no appropriate cost can be found
	 * @see _resurrect
	 */
	private function calculateResurrectionCost($p_player) {
		if ($this->isResurrectFree($p_player)) {
			return self::RES_COST_TYPE_FREE;
		} else if ($p_player->kills > 0) {
			return self::RES_COST_TYPE_KILL;
		} else if ($p_player->turns > 0) {
			return self::RES_COST_TYPE_TURN;
		} else {
			throw new \RuntimeException('You have no kills or turns. You must wait to regain turns before you can return to life.');
		}
	}

	/**
	 * Determine if player is eligible for a free resurrect
	 *
	 * Resurrect is free if the player both is low enough level and has few enough kills
	 *
	 * @param p_player Player
	 * @return boolean
	 * @see _resurrect
	 */
	private function isResurrectFree($p_player) {
		return (
			$p_player->level < self::FREE_RES_LEVEL_LIMIT
			&&
			$p_player->kills < self::FREE_RES_KILL_LIMIT
		);
	}

	/**
	 * Operation to modify a player object by increasing health and decreasing gold
	 *
	 * @param p_player Player The player object to operate on
	 * @param p_amount int The amount of health to add to the player object
	 * @return void
	 * @throws InvalidArgumentException Heal amount must be an integer greater than 0
	 * @throws RuntimeException Player is dead
	 * @throws RuntimeException Player does not need healing
	 * @throws RuntimeException Player does not enough gold for the healing requested
	 * @par Preconditions:
	 * Player must be alive to heal
	 *
	 * @par Side Effects:
	 * The gold attribute of $p_player is modified in memory and database
	 * The health attribute of $p_player is modified in memory and database
	 *
	 * @note
	 * Chi reduces cost per heal point by 50%
	 *
	 * @see calculateHealCost
	 */
	private function _heal($p_player, $p_amount) {
		if ($p_amount < 1) {
			throw new \InvalidArgumentException('Invalid input for heal amount.');
		} else if ($p_player->health() <= 0) {
			throw new \RuntimeException('You must resurrect before you can heal.');
		} else if ($p_player->is_hurt_by() <= 0) {
			throw new \RuntimeException('You are at full health.');
		}

		$amount = min($p_amount, $p_player->is_hurt_by());

		$totalCost = ceil($amount * $this->calculateHealCost($p_player));

		if ($totalCost > $p_player->gold) {
			throw new \RuntimeException('You do not have enough gold for that much healing');
		}

		$p_player->vo->gold = subtract_gold($p_player->id(), $totalCost);

		$p_player->heal($amount);
	}

	/**
	 * Calculates the maximum heal possible based on the player state
	 *
	 * @param p_player Player The player object to interrogate
	 * @return int
	 *
	 * @see calculateHealCost
	 */
	private function calculateMaxHeal($p_player) {
		return (int)((2*$p_player->gold)/(2*$this->calculateHealCost($p_player)));
	}

	/**
	 * Calculates the cost of healing based on player state
	 *
	 * @param p_player Player The player object to interrogate
	 * @return int
	 */
	private function calculateHealCost($p_player) {
		// Chi reduces the cost of healing by half, rounded up
		$skillController = new Skill();

		if ($skillController->hasSkill('Chi', $p_player->name())) {
			$costOfHealPoint = self::HEAL_POINT_COST/2;
		} else {
			$costOfHealPoint = self::HEAL_POINT_COST;
		}

		return $costOfHealPoint;
	}

	/**
	 * Generate the return value for public controller methods to send to the view
	 *
	 * @param p_parts Array Hash of values to be added to the default
	 * @return Array
	 */
	private function render($p_parts) {
        return [
            'template'       => 'shrine.tpl',
            'title'          => 'Shrine',
            'options'        => [
                'body_classes' => 'shrine',
                'quickstat'  => 'player',
            ],
            'parts'          => array_merge(
                [
                    'action_message' => null,
                    'error'          => null,
                ],
                $p_parts
            ),
        ];
	}

	/**
	 * Generate the return value for public controller methods in error state
	 *
	 * @param p_player Player The player object to pass to the view for rendering
	 * @return Array
	 */
	private function renderError($p_message, Player $p_player) {
		$pageParts = $this->servicesNeeded($p_player);
		array_unshift($pageParts, 'entrance');

        return $this->render([
                'pageParts' => $pageParts,
                'player'    => $p_player,
                'error'     => $p_message,
            ]);
    }
}
