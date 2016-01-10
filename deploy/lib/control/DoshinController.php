<?php
namespace NinjaWars\core\control;

require_once(CORE.'/control/lib_inventory.php');

use \Player as Player;

/**
 * Handles all user requests for the in-game Doshin Office
 */
class DoshinController { //extends controller
    const ALIVE          = true;
    const PRIV           = false;
    const MAX_BOUNTY     = 5000;
    const MIN_BRIBE      = 0;
    const DOSHIN_CUT     = .8;
    const SAFE_WEALTH    = 1000;
    const RICH_REDUCTION = .7;
    const BRIBERY_DIVISOR = 2;
    const FAILED_BRIBERY_PAIN = .2;

    protected $data = [];

    /**
     * Gathers data from session and makes it available to internal methods
     *
     * @return DoshinController
     */
    public function __construct($char=null) {
        $char = $char instanceof Player? $char : new Player(self_char_id());
        $this->data = [
            'char'     => $char
        ];
    }

    /**
     * Displays the initial Doshin Office view
     *
     * @param target String (Optional) Pre-load the bounty form with the specified target
     * @return Array
     */
    public function index() {
        $target = in('target');

        return $this->render(
            [
                'quickstat' => true,
                'location'  => 0,
                'error'     => 0,
                'command'   => 'index',
                'target'    => $target,
            ]
        );
    }

    /**
     * Command for the current user to offer their money as bounty on another player
     *
     * @param target String The username of the player to offer a bounty on
     * @param amount int The amount of gold to spend on offering the bounty
     * @return Array
     *
     * @TODO simplify the conditional branching
     */
    public function offerBounty() {
        $target_in    = in('target');
        $char = $this->data['char'];
        $target = new Player($target_in);
        $amount_in = in('amount');
        $amount    = intval($amount_in) !== 0? intval($amount_in) : null;
        $quickstat = false;
        $success   = false;

        if(!$target->id()){
            $error = 1; // Target not found
        } else {
            $error = $this->validateBountyOffer($char, $target->id(), $amount);

            $amount = self::calculateMaxOffer($target->bounty(), $amount);

            if ($error === null) {
                $char->set_gold($char->gold() - $amount); // Subtract the gold.
                addBounty($target->id(), $amount); // Add the bounty to the person being bountied upon.  How the hell did this break?
                $char = $char->save();

                send_event($char->id(), $target->id(), $char->name()." has offered ".$amount." gold in reward for your head!");

                $success = true;
                $quickstat = 'player';
            }
        }

        return $this->render(
            [
                'error'     => $error,
                'success'   => $success,
                'quickstat' => $quickstat,
                'amount_in' => $amount_in,
                'amount'    => $amount,
                'command'   => 'offer',
                'location'  => 0,
                'target'    => $target,
            ]
        );
    }

    /**
     */
    public static function calculateMaxOffer($p_currentBounty, $p_offer) {
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
     */
    private function validateBountyOffer(Player $char, $p_targetId, $p_amount) {
        $error = null;

        if (!$p_targetId) { // Test that target exists
            $error = 1;
        } else {
            $target_bounty = getBounty($p_targetId);

            $amount = self::calculateMaxOffer($target_bounty, $p_amount);

            if ($char->gold() < $amount) {
                $error = 2;
            }

            if ($amount <= 0) {
                $error = 3;
            }

            if ($target_bounty >= 5000) {
                $error = 4;
            }
        }

        return $error;
    }

    /**
     * Command for a user to reduce their bounty by paying their own gold
     *
     * @param bribe int The amount to spend on reducing bounty
     * @return Array
     */
    public function bribe() {
        $bribe = intval(in('bribe'));
        $char = $this->data['char'];
        $error = 0;
        $quickstat = false;

        if ($bribe <= $char->gold() && $bribe > 0) {
            $char->set_gold($char->gold() - $bribe);
            subtractBounty($char->id(), floor($bribe/self::BRIBERY_DIVISOR));
            $location = 1;
            $quickstat = 'viewinv';
        } else if ($bribe < self::MIN_BRIBE) {
            $this->data['char'] = $this->doshinAttack($char);
            $location = 2;
            $quickstat = 'player';
        } else {
            $location = 0;
            $error = 5;
        }

        return $this->render(
            [
                'error'     => $error,
                'quickstat' => $quickstat,
                'location'  => $location,
                'command'   => 'bribe',
            ]
        );
    }

    /**
     * If you try to bribe with a negative bounty, the doshin beat you up and take your money!
     * @return Player
     * @note
     * If the player loses a substantial enough amount, the doshin will actually decrease the bounty.
     */
    private function doshinAttack(Player $char) {
        $current_bounty = $char->bounty();
        $doshin_takes = floor($char->gold() * self::DOSHIN_CUT);
        // If the doshin take a lot of money, they'll
        // actually reduce the bounty somewhat.

        $bounty_reduction = (int) min($current_bounty, 
            (($doshin_takes > self::SAFE_WEALTH)? $doshin_takes/self::BRIBERY_DIVISOR : 0)
            );
        if(0 < $bounty_reduction){
            $char->set_bounty($char->bounty() - $bounty_reduction);
        }

        // Do fractional damage to the char
        $char->set_health(
                $char->health() - 
                floor($char->health()*self::FAILED_BRIBERY_PAIN)
            );

        // Regardless, you lose some gold.
        $char->set_gold($char->gold() - $doshin_takes);
        return $char->save();
    }

    /**
     * Returns a view spec hash for rendering a template
     *
     * @param p_data Array Hash of variables to pass to the view
     * @return Array
     */
    private function render($parts) {
        $char = $this->data['char'];
        $char_id = $char->id();
        $myBounty = getBounty($char_id);

        // Pulling the bounties.
        $bounties = query_array("SELECT player_id, uname, bounty, class_name AS class, level, clan_id, clan_name 
            FROM players JOIN class ON class_id = _class_id LEFT JOIN clan_player ON player_id = _player_id 
            LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND active = 1 and health > 0 ORDER BY bounty DESC");

        $parts['bounties'] = $bounties;
        $parts['myBounty'] = $myBounty;
        $parts['char']     = $char;
        $parts['display_gold'] = number_format($char->gold());

        $quickstat = $parts['quickstat'];

        return [
            'template' => 'doshin.tpl',
            'title'    => 'Doshin Office',
            'parts'    => $parts,
            'options'  => [
                'quickstat' => $quickstat,
            ],
        ];
    }
}
