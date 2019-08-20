<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Event;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * Handles all user requests for the in-game Doshin Office
 */
class DoshinController extends AbstractController {
    const ALIVE          = true;
    const PRIV           = false;
    const MAX_BOUNTY     = 5000;
    const MIN_BRIBE      = 0;
    const DOSHIN_CUT     = .8;
    const SAFE_WEALTH    = 1000;
    const RICH_REDUCTION = .7;
    const BRIBERY_DIVISOR = 2;
    const FAILED_BRIBERY_PAIN = .2;

    /**
     * Displays the initial Doshin Office view
     *
     * @param Container
     * @param target String (Optional) Pre-load the bounty form with the specified target
     * @return StreamedViewResponse
     */
    public function index(Container $p_dependencies) {
        $target = RequestWrapper::getPostOrGet('target');
        $authenticated = (bool)$this->getAccountId();

        return $this->render(
            [
                'quickstat' => true,
                'location'  => 0,
                'error'     => 0,
                'authenticated' => $authenticated,
                'command'   => 'index',
                'amount'    => 0,
                'target'    => $target,
            ], 
            $p_dependencies
        );
    }

    /**
     * Command for the current user to offer their money as bounty on another player
     *
     * @param Container
     * @param target String The username of the player to offer a bounty on
     * @param amount int The amount of gold to spend on offering the bounty
     * @return StreamedViewResponse
     *
     * @TODO simplify the conditional branching
     */
    public function offerBounty(Container $p_dependencies) {
        $request    = RequestWrapper::$request;
        $targetName = $request->get('target');
        $char       = $p_dependencies['current_player'];
        $target     = $targetName !== null? Player::findByName($targetName) : null;
        $amountIn   = $request->get('amount');
        $amount     = (intval($amountIn) !== 0 ? intval($amountIn) : null);
        $quickstat  = false;
        $success    = false;
        $authenticated = ($char !== null);

        if(!$char){
            $error = 2; // You don't have enough gold.
        } elseif (!$target) {
            $error = 1; // Target not found
        } elseif ($target->id() === $char->id()) {
            $error = 6; // Can't put a bounty on yourself.
        } else {
            $error = $this->validateBountyOffer($char, $target->id(), $amount);

            $amount = self::calculateMaxOffer($target->bounty, $amount);

            if (!$error) {
                $char->setGold($char->gold - $amount); // Subtract the gold.
                $target->setBounty($target->bounty + $amount);
                $target->save();
                $char = $char->save();

                Event::create($char->id(), $target->id(), $char->name()." has offered ".$amount." gold in reward for your head!");

                $success = true;
                $quickstat = 'player';
            }
        }

        return $this->render(
            [
                'error'     => $error,
                'authenticated' => $authenticated,
                'success'   => $success,
                'quickstat' => $quickstat,
                'amount_in' => $amountIn,
                'amount'    => $amount,
                'command'   => 'offer',
                'location'  => 0,
                'target'    => $target,
            ],
            $p_dependencies
        );
    }

    /**
     * Given the current bounty and an amount offered, return the max allowed
     *
     * @param int $p_currentBounty
     * @param int $p_offer
     * @return int
     */
    private static function calculateMaxOffer($p_currentBounty, $p_offer) {
        // Cap possible bounty amount
        if (($p_currentBounty + $p_offer) > self::MAX_BOUNTY) {
            $amount = (self::MAX_BOUNTY - $p_currentBounty);
        } else {
            $amount = $p_offer;
        }

        return $amount;
    }

    /**
     * Make sure a bounty offer is valid, constrain it by allowable bounty and available gold
     *
     * @param Player $char
     * @param int    $p_targetId
     * @param int    $p_amount
     * @return int
     */
    private function validateBountyOffer(Player $char, $p_targetId, $p_amount) {
        $error = 0;
        $target = Player::find($p_targetId);

        if (!$target) { // Test that target exists
            $error = 1;
        } else {
            $amount = self::calculateMaxOffer($target->bounty, $p_amount);

            if ($char->gold < $amount) {
                $error = 2;
            }

            if ($amount <= 0) {
                $error = 3;
            }

            if ($target->bounty >= 5000) {
                $error = 4;
            }
        }

        return $error;
    }

    /**
     * Command for a user to reduce their bounty by paying their own gold
     *
     * @param Container $p_dependencies
     * @param int       $bribe          The amount to spend on reducing bounty
     * @return StreamedViewResponse
     */
    public function bribe(Container $p_dependencies) {
        $bribe     = intval(RequestWrapper::getPostOrGet('bribe'));
        $char      = Player::findPlayable($this->getAccountId());
        $error     = 0;
        $quickstat = false;

        if ($bribe <= $char->gold && $bribe > 0) {
            $char->setGold($char->gold - $bribe);
            $char->setBounty(max(
                0,
                ($char->bounty - floor($bribe/self::BRIBERY_DIVISOR))
            ));
            $char->save();
            $location = 1;
            $quickstat = 'viewinv';
        } elseif ($bribe < self::MIN_BRIBE) {
            $this->doshinAttack($char);
            $location = 2;
            $quickstat = 'player';
        } else {
            $location = 0;
            $error = 5;
        }

        return $this->render(
            [
                'error'         => $error,
                'quickstat'     => $quickstat,
                'location'      => $location,
                'authenticated' => (bool) ($char !== null),
                'command'       => 'bribe',
            ],
            $p_dependencies
        );
    }

    /**
     * If you try to bribe with a negative bounty, the doshin beat you up and take your money!
     *
     * @param Player $char To get attacked by the doshin
     * @return Player
     * @note
     * If the player loses a substantial enough amount, the doshin will actually decrease the bounty.
     */
    private function doshinAttack(Player $char) {
        $current_bounty = $char->bounty;
        $doshin_takes = floor($char->gold * self::DOSHIN_CUT);
        // If the doshin take a lot of money, they'll
        // actually reduce the bounty somewhat.

        $bounty_reduction = (int) min(
            $current_bounty,
            (($doshin_takes > self::SAFE_WEALTH)? $doshin_takes/self::BRIBERY_DIVISOR : 0)
        );

        if (0 < $bounty_reduction) {
            $char->setBounty($char->bounty - $bounty_reduction);
        }

        // Do fractional damage to the char
        $char->setHealth(
            $char->health -
            floor($char->health * self::FAILED_BRIBERY_PAIN)
        );

        // Regardless, you lose some gold.
        $char->setGold($char->gold - $doshin_takes);
        return $char->save();
    }

    /**
     * Returns a view spec hash for rendering a template
     *
     * @param Array     $parts Hash of variables to pass to the view
     * @param Container $deps  Pass the pimple container
     * @return StreamedViewResponse
     */
    private function render(array $parts, Container $deps=null): StreamedViewResponse {
        $char     = $deps? $deps['current_player'] : null;

        if (!$char) {
            $char = new Player();
        }

        $myBounty = $char->bounty;

        // Pulling the bounties.
        $bounties = query_array("SELECT player_id, uname, bounty, class_name AS class, level, clan_id, clan_name
            FROM players JOIN class ON class_id = _class_id LEFT JOIN clan_player ON player_id = _player_id
            LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND active = 1 and health > 0 ORDER BY bounty DESC");

        $parts['bounties']     = $bounties;
        $parts['myBounty']     = $myBounty;
        $parts['char']         = $char;
        $parts['display_gold'] = number_format($char->gold);

        $quickstat = $parts['quickstat'];

        return new StreamedViewResponse('Doshin Office', 'doshin.tpl', $parts, [ 'quickstat' => $quickstat, 'body_classes'=>'doshin' ]);
    }
}
