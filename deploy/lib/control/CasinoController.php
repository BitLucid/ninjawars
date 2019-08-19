<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Handles all user commands for the in-game Casino
 */
class CasinoController extends AbstractController {
	const PRIV      = false;
	const ALIVE     = true;
	const REWARD    = 'phosphor';
	const MAX_BET   = 3000;
    const CHEAT_DMG = 99;

	/**
	 * Displays the initial casino view
	 *
     * @param Container
	 * @return Array
	 */
	public function index(Container $p_dependencies) {
		$player = $p_dependencies['current_player'] ?? new Player();

		$error = RequestWrapper::getPostOrGet('error');

		return $this->render(
			[
				'pageParts' => [],
				'player'    => $player,
				'error'     => $error,
			]
		);
	}

	/**
	 * User command for betting on the coin toss game in the casino
	 *
     * @param Container
	 * @param bet int The amount of money to bet on the coin toss game
	 * @return RedirectResponse | StreamedViewResponse
	 *
	 * @note
     * If the player bets within ~1% of the maximum bet, they will receive a
     * reward item
	 */
	public function bet(Container $p_dependencies) {
        $player = $p_dependencies['current_player'];

		if (!$player) {
			return new RedirectResponse('/casino/?error='.rawurlencode('Become a ninja first!'));
		}

		$bet       = intval(RequestWrapper::getPostOrGet('bet'));
		$pageParts = ['reminder-max-bet'];

		if ($bet < 0) {
			$pageParts = ['result-cheat'];
			$player->harm(self::CHEAT_DMG);
		} else if ($bet > $player->gold) {
			$pageParts = ['result-no-gold'];
		} else if ($bet > 0 && $bet <= self::MAX_BET) {
			if (rand(0, 1) === 1) {
				$pageParts = ['result-win'];

				$player->setGold($player->gold + $bet);

				if ($bet >= round(self::MAX_BET*0.99)) {
					// within about 1% of the max bet & you win, you get a reward item.
                    $inventory = new Inventory($player);
					$inventory->add(self::REWARD, 1);
				}
			} else {
				$player->setGold($player->gold - $bet);
				$pageParts = ['result-lose'];
			}
		}

        $player->save();

		return $this->render(
			[
				'pageParts' => $pageParts,
				'player'    => $player,
			]
		);
	}

	/**
	 * Returns a view spec for rendering a template
	 *
	 * @param p_parts Array Hash of variables to render
	 * @return Response
	 */
	private function render($p_parts) {
		$p_parts['maxBet'] = self::MAX_BET;

		return new StreamedViewResponse('Casino', 'casino.tpl', $p_parts, [ 'quickstat' => 'player' ]);
	}
}
