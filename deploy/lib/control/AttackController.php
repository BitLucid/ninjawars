<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AttackLegal;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\control\Combat;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\Skill;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Event;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

class AttackController extends AbstractController {
    const ALIVE                  = true;
    const PRIV                   = true;
    const BASE_WRATH_REGAIN      = 2;
    const DEFAULT_GOLD_MOD       = 0.2;
    const DUEL_GOLD_MOD          = 0.25;
    const STEALTH_GOLD_MOD       = 0.1;
    const STEALTH_STRIKE_COST    = 1;
    const EVEN_MATCH_KI_REWARD   = 1;
    const EVEN_MATCH_ROUND_COUNT = 4;

    /**
     * @return Response
     */
    public function index(Container $p_dependencies) {
        $request = RequestWrapper::$request;
        $session = SessionFactory::getSession();

        $options = [
            'blaze'   => (bool) $request->get('blaze'),
            'deflect' => (bool) $request->get('deflect'),
            'duel'    => (bool) $request->get('duel'),
            'evade'   => (bool) $request->get('evasion'),
            'attack'  => !(bool) $request->get('duel'),
        ];

        $target   = Player::find($request->get('target'));
        $attacker = Player::find($session->get('player_id'));

        $skillListObj = new Skill();

        $ignores_stealth = false;
        $required_turns  = 0;

        foreach (array_filter($options) as $type=>$value) {
            $ignores_stealth = $ignores_stealth||$skillListObj->getIgnoreStealth($type);
            $required_turns += $skillListObj->getTurnCost($type);
        }

        $params = [
            'required_turns'  => $required_turns,
            'ignores_stealth' => $ignores_stealth,
        ];

        try {
            $rules           = new AttackLegal($attacker, $target, $params);
            $attack_is_legal = $rules->check();
            $error           = $rules->getError();
        } catch (\InvalidArgumentException $e) {
            $attack_is_legal = false;
            $error           = 'Could not determine valid target';
        }

        if (!$attack_is_legal) {
            // Take away at least one turn even on attacks that fail.
            $attacker->turns = $attacker->turns - 1;
            $attacker->save();

            $parts = [
                'target'   => $target,
                'attacker' => $attacker,
                'error'    => $error,
            ];

            return new StreamedViewResponse('Battle Status', 'attack_mod.tpl', $parts, ['quickstat' => 'player' ]);
        } else {
            return $this->combat($attacker, $target, $required_turns, $options);
        }
    }

    /**
     * @return StreamedViewResponse
     */
    private function combat(Player $attacker, Player $target, $required_turns=0, $options) {
        $error             = '';
        $stealthed_attack  = false;
        $stealth_damage    = false;
        $stealth_lost      = false;
        $bounty_result     = false;
        $rewarded_ki       = false;
        $wrath             = false;
        $loot              = 0;
        $killpoints        = 1;
        $rounds            = 1;
        $victor            = null;
        $loser             = null;
        $starting_attacker = clone $attacker;
        $starting_target   = clone $target;
        $turns_counter     = ($options['duel'] ? -1 : 1);
        $attacker_label    = $attacker->name();

        if (!$options['duel'] && $attacker->hasStatus(STEALTH)) {
            $stealthed_attack = true;
            $this->stealthStrike($attacker, $target);

            $gold_mod = self::STEALTH_GOLD_MOD;

            if ($target->health > 0) {
                $stealth_damage = true;
            } else {
                $attacker_label = 'a stealthed ninja';
                $victor = $attacker;
                $loser  = $target;
            }

            $attack_label = "attacked %s from the shadows";
        } else {
            $gold_mod = ($options['duel'] ? self::DUEL_GOLD_MOD : self::DEFAULT_GOLD_MOD);

            if ($attacker->hasStatus(STEALTH)) {
                $stealth_lost = true;
            }

            $attacker->subtractStatus(STEALTH);

            while ($turns_counter != 0 && $attacker->health > 0 && $target->health > 0) {
                $turns_counter--;
                $rounds++;

                $this->strike($attacker, $target, $options['blaze'], $options['deflect']);

                /**
                 * Evasion effect:
                 *
                 * Break off the duel/attack if less than 10% health or
                 * health is less than average of defender's strength
                 */
                if ($options['evade'] && (
                    $attacker->health < ($target->getStrength()*.5) ||
                    $attacker->health < ($attacker->health*.1))
                ) {
                    break;
                }
            }

            $attacker->turns = $attacker->turns - (max(0, $required_turns));

            $attack_label = ($options['duel'] ? 'dueled %s' : 'attacked %s');
        }

        if ($target->health > 0 && $attacker->health > 0) {
            $combat_msg = "%s $attack_label for %s damage, but they got away before you could kill them!";

            Event::create(
                $attacker->id(),
                $target->id(),
                sprintf(
                    $combat_msg,
                    $attacker->name(),
                    'you',
                    ($starting_target->health - $target->health)
                )
            );

            if ($attacker->hasStatus(STEALTH)) {
                $stealth_lost = true;
            }

            $attacker->subtractStatus(STEALTH);
        } else if ($target->health < 1 && $attacker->health < 1) {
            $loot = 0;
            $this->win($attacker, $target, $loot, $killpoints);
            $this->win($target, $attacker, $loot, 1);
            $this->lose($attacker, $target, $loot);
            $this->lose($target, $attacker, $loot);
        } else if ($target->health < 1) {
            $victor        = $attacker;
            $loser         = $target;
            $bounty_result = Combat::runBountyExchange($victor, $loser);
            $loot          = floor($gold_mod * $loser->gold);

            if ($options['duel']) {
                $killpoints = Combat::killpointsFromDueling($attacker, $target);

                $skillListObj = new Skill();
                if ($skillListObj->hasSkill('wrath', $attacker)) {
                    // They'll regain some health for the kill, at the end.
                    $attacker->heal(self::BASE_WRATH_REGAIN);
                    $wrath = true;
                }
            }

            $reporting_victor = $victor;

            if ($victor->hasStatus(STEALTH)) {
                $reporting_victor = new Player();
                $reporting_victor->uname     = 'a stealthed ninja';
                $reporting_victor->player_id = 0;
            }

            $this->lose($loser, $reporting_victor, $loot);
            $this->win($victor, $loser, $loot, $killpoints);
        } else {
            $victor = $target;
            $loser  = $attacker;
            $loot   = floor($gold_mod * $loser->gold);

            $this->lose($loser, $victor, $loot);
            $this->win($victor, $loser, $loot, $killpoints);
        }

        if ($options['duel']) {
            $this->logDuel($attacker, $target, $victor, $killpoints);
        }

        if ($rounds > self::EVEN_MATCH_ROUND_COUNT) { // Evenly matched battle! Reward some ki to the attacker, even if they die
            $rewarded_ki = self::EVEN_MATCH_KI_REWARD;

            $attacker->setKi($attacker->ki + $rewarded_ki);
        }

        $target->save();
        $attacker->save();

        return new StreamedViewResponse('Battle Status', 'attack_mod.tpl', get_defined_vars(), ['quickstat' => 'player' ]);
    }

    /**
     * @return void
     */
    private function stealthStrike(Player $attacker, Player $target) {
        $target->harm($attacker->getStrength());
        $attacker->turns = $attacker-turns - (1*self::STEALTH_STRIKE_COST);
    }

    /**
     * @return void
     */
    private function strike($attacker, $target, $blaze, $deflect) {
        $target_damage   = rand(1, $target->getStrength());
        $attacker_damage = rand(1, $attacker->getStrength());

        if ($blaze) {
            $attacker_damage = $attacker_damage*2;
        }

        if ($deflect) {
            $target_damage = floor($target_damage/2);
        }

        $target->harm($attacker_damage);
        $attacker->harm($target_damage);
    }

    /**
     * @return void
     */
    private function lose($loser, $victor, $loot) {
        $loser->setGold($loser->gold - $loot);
        $loser->death();

        $loser_msg = "DEATH: You have been killed by {$victor->name()} in combat and lost $loot gold!";
        Event::create($victor->id(), $loser->id(), $loser_msg);
    }

    /**
     * @return void
     */
    private function win($victor, $loser, $loot, $killpoints) {
        $victor_msg = "You have killed {$loser->name()} in combat and taken $loot gold.";
        Event::create($loser->id(), $victor->id(), $victor_msg);
        $victor->setGold($victor->gold + $loot);
        $victor->addKills($killpoints);
    }

    /**
     * @return void
     */
    private function logDuel($attacker, $target, $winner, $killpoints) {
        $duel_log_msg = "%s has dueled {$target->name()} and ";

        if ($attacker !== $winner) {
            $duel_log_msg .= "lost at ".date("F j, Y, g:i a");
        } else if ($killpoints > 1 || $killpoints < 0) {
            $duel_log_msg .= "won $killpoints killpoints.";
        } else {
            $duel_log_msg = '';
        }

        if ($duel_log_msg !== '') {
            Event::create(
                (int)"SysMsg",
                (int)"SysMsg",
                sprintf(
                    $duel_log_msg,
                    $attacker->name(),
                    $target->name()
                )
            );

            GameLog::sendLogOfDuel($attacker->name(), $target->name(), $attacker === $winner, $killpoints);
        }
    }
}
