<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT.'control/lib_inventory.php');

use \Player as Player;

/**
 * Handles all user commands for the in-game Casino
 */
class CasinoController { //extends controller
	const PRIV           = false;
	const ALIVE          = true;
	const REWARD         = 'phosphor';
	const MAX_BET        = 3000;

	/**
	 * Displays the initial casino view
	 *
	 * @return Array
	 */
	public function index() {
		$player = new Player(self_char_id());

		return $this->render(
			[
				'pageParts' => [],
				'player'    => $player,
				'bet'       => 1,
			]
		);
	}

	/**
	 * User command for betting on the coin toss game in the casino
	 *
	 * @param bet int The amount of money to bet on the coin toss game
	 * @return Array
	 *
	 * @note
     * If the player bets within ~1% of the maximum bet, they will receive a
     * reward item
	 */
	public function bet() {
		$player = new Player(self_char_id());
		$bet    = intval(in('bet'));

		$negative = ($bet < 0);

		$pageParts = ['reminder-max-bet'];

		if ($negative) {
			$pageParts = ['result-cheat'];
			$player->vo->health = subtractHealth($player->id(), 99);
		} else if ($bet > $player->vo->gold) {
			$pageParts = ['result-no-gold'];
		} else if ($bet > 0 && $bet <= self::MAX_BET) {
			if (rand(0, 1) === 1) {
				$pageParts = ['result-win'];

				$player->vo->gold = add_gold($player->id(), $bet);

				if ($bet >= round(self::MAX_BET*0.99)) {
					// within about 1% of the max bet & you win, you get a reward item.
					add_item($player->id(), self::REWARD, 1);
				}
			} else {
				$player->vo->gold = subtract_gold($player->id(), $bet);
				$pageParts = ['result-lose'];
			}
		} // End of not cheating check.

		return $this->render(
			[
				'pageParts' => $pageParts,
				'player'    => $player,
				'bet'       => $bet,
			]
		);
	}

	/**
	 * Returns a view spec for rendering a template
	 *
	 * @param p_parts Array Hash of variables to render
	 * @return Array
	 */
	private function render($p_parts) {
		$p_parts['maxBet'] = self::MAX_BET;

		return [
			'template' => 'casino.tpl',
			'title'    => 'Casino',
			'parts'    => $p_parts,
			'options'  => [ 'quickstat' => 'player' ],
		];
	}
}
