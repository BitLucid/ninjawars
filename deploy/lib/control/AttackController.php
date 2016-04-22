<?php
namespace NinjaWars\core\control;

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
    const ALIVE = true;
    const PRIV  = true;
    const BASE_WRATH_REGAIN = 2;
    const DEFAULT_GOLD_MOD  = 0.2;
    const DUEL_GOLD_MOD     = 0.25;

    /**
     * @return Response
     */
    public function index() {
        $request = RequestWrapper::$request;

        $duel    = (bool) $request->get('duel');
        $blaze   = (bool) $request->get('blaze');
        $deflect = (bool) $request->get('deflect');
        $evade   = (bool) $request->get('evasion');

        // Template vars.
        $stealthed_attack = $stealth_damage = $stealth_lost =
            $pre_battle_stats = $rounds = $combat_final_results =
            $killed_target = $attacker_died = $bounty_result = $rewarded_ki =
            $wrath_regain = false;

        $killpoints       = 0; // Starting state for killpoints
        $attack_turns     = 1; // Default cost, will go to zero if an error prevents combat
        $required_turns   = 0;
        $loot             = 0;
        $simultaneousKill = NULL; // Not simultaneous by default
        $turns_to_take    = NULL; // Even on failure take at least one turn
        $attack_type      = [];
        $attack_again     = false;
        $victor           = null;
        $loser            = null;

        if ($blaze) {
            $attack_type['blaze'] = 'blaze';
        }

        if ($deflect) {
            $attack_type['deflect'] = 'deflect';
        }

        if ($evade) {
            $attack_type['evade'] = 'evade';
        }

        if ($duel) {
            $attack_type['duel'] = 'duel';
        } else {
            $attack_type['attack'] = 'attack';
        }

        $target   = Player::find($request->get('target'));
        $attacker = Player::find(SessionFactory::getSession()->get('player_id'));

        $skillListObj = new Skill();

        $ignores_stealth = false;

        foreach ($attack_type as $type=>$value) {
            $ignores_stealth = $ignores_stealth||$skillListObj->getIgnoreStealth($type);
            $required_turns += $skillListObj->getTurnCost($type);
        }

        // *** Attack Legal section ***
        $params = [
            'required_turns'  => $required_turns,
            'ignores_stealth' => $ignores_stealth,
        ];

        try {
            $attack_legal    = new AttackLegal($attacker, $target, $params);
            $attack_is_legal = $attack_legal->check();
            $attack_error    = $attack_legal->getError();
        } catch (\InvalidArgumentException $e) {
            $attack_is_legal = false;
            $attack_error    = 'Could not determine valid target';
        }

        // ***  MAIN BATTLE ALGORITHM  ***
        if ($attack_is_legal) {
            if (!$duel && $attacker->hasStatus(STEALTH)) { // *** Not dueling, and attacking from stealth ***
                // TODO: This area seems to be the area that contains the broken stealth-kill-without-reporting bug
                // https://github.com/BitLucid/ninjawars/issues/273
                $stealthAttackDamage = $attacker->getStrength();
                $stealthed_attack = true;
                $target->harm($stealthAttackDamage);

                $attacker->subtractStatus(STEALTH);
                $turns_to_take = 1;

                if (0 > $target->health) { // *** if Stealth attack of whatever damage kills target. ***
                    $victor       = $attacker;
                    $loser        = $target;
                    $gold_mod     = .1;
                    $loot         = floor($gold_mod * $target->gold);
                    $target_msg   = "DEATH: You have been killed by a stealthed ninja in combat and lost $loot gold!";
                    $attacker_msg = "You have killed {$target->name()} in combat and taken $loot gold.";

                    $target->death();
                    Event::create((int)"A Stealthed Ninja", $target->id(), $target_msg);
                    Event::create($target->id(), $attacker->id(), $attacker_msg);
                    $bounty_result = Combat::runBountyExchange($attacker, $target); // *** Determines the bounty for normal attacking. ***

                    $stealth_kill = true;
                } else {	// *** if damage from stealth only hurts the target. ***
                    $stealth_damage = true;

                    Event::create($attacker->id(), $target->id(), $attacker->name()." has attacked you from the shadows for $stealthAttackDamage damage.");
                }
            } else {	// *** If the attacker is purely dueling or attacking, even if stealthed, though stealth is broken by dueling. ***
                // *** MAIN DUELING SECTION ***
                if ($attacker->hasStatus(STEALTH)) { // *** Remove their stealth if they duel instead of preventing dueling.
                    $attacker->subtractStatus(STEALTH);
                    $stealth_lost = true;
                }

                // *** PRE-BATTLE STATS - Template Vars ***
                $pre_battle_stats  = true;
                $pbs_attacker_str  = $attacker->getStrength();
                $pbs_attacker_hp   = $attacker->health();
                $pbs_target_str    = $target->getStrength();
                $pbs_target_hp     = $target->health();

                // *** BEGINNING OF MAIN BATTLE ALGORITHM ***

                $turns_counter         = $attack_turns;
                $total_target_damage   = 0;
                $total_attacker_damage = 0;
                $target_damage         = 0;
                $attacker_damage       = 0;

                // *** Combat Calculations ***
                $round = 1;
                $rounds = 0;

                while ($turns_counter > 0 && $attacker->health > 0 && $target->health > 0) {
                    $turns_counter -= (!$duel ? 1 : 0);// *** SWITCH BETWEEN DUELING LOOP AND SINGLE ATTACK ***

                    $target_damage   = rand(1, $target->getStrength());
                    $attacker_damage = rand(1, $attacker->getStrength());

                    if ($blaze) {	// *** Blaze does double damage. ***
                        $attacker_damage = $attacker_damage*2;
                    }

                    if ($deflect) {
                        $target_damage = floor($target_damage/2);
                    }

                    $total_target_damage   += $target_damage;
                    $total_attacker_damage += $attacker_damage;

                    $target->harm($attacker_damage);
                    $attacker->harm($target_damage);

                    $rounds++;	// *** Increases the number of rounds that has occured and restarts the while loop. ***

                    if ($evade) {
                        // Evasion effect:
                        // Check current level of damage.
                        $testValue = ($attacker->health - $total_target_damage);
                        // Break off the duel/attack if less than 10% health or health is less than average of defender's strength
                        if ($testValue < ($target->getStrength()*.5) || $testValue < ($attacker->health*.1)) {
                            break;
                        }
                    }
                }

                // *** END OF MAIN BATTLE ALGORITHM ***

                $combat_final_results = true;
                $finalizedHealth = ($attacker->health);

                // *** RESULTING PLAYER MODIFICATION ***

                $gold_mod = ($duel ? self::DUEL_GOLD_MOD : self::DEFAULT_GOLD_MOD);

                $turns_to_take = $required_turns;

                //  *** Let the victim know who hit them ***
                $attack_label = ($duel ? 'dueled' : 'attacked');

                if ($target->health && $attacker->health) {
                    $combat_msg = "You have been $attack_label by {$attacker->name()} for $total_attacker_damage, but they got away before you could kill them!";
                } else {
                    $combat_msg = "You have been $attack_label by {$attacker->name()} for $total_attacker_damage!";
                }

                Event::create($attacker->id(), $target->id(), $combat_msg);

                if ($target->health < 1 || $attacker->health < 1) { // A kill occurred.
                    if ($target->health < 1) { // ATTACKER KILLS DEFENDER!
                        $simultaneousKill = ($attacker->health < 1);

                        if (!$simultaneousKill) {
                            $victor = $attacker;
                            $loser  = $target;
                        }

                        $killed_target = true;

                        $killpoints = 1; // Changes killpoints from zero to one.

                        if ($duel) {
                            // Changes killpoints amount by dueling equation.
                            $killpoints = Combat::killpointsFromDueling($attacker, $target);

                            $duel_log_msg = $attacker->name()." has dueled {$target->name()} and won $killpoints killpoints.";

                            // Only log duels if they're better than 1 or if they're a failure.
                            if ($killpoints > 1 || $killpoints < 0) {
                                // Make a WIN record in the dueling log.
                                GameLog::sendLogOfDuel($attacker->name(), $target->name(), 1, $killpoints);
                            }

                            if ($skillListObj->hasSkill('wrath', $attacker)) {
                                // They'll retain 10 health for the kill, at the end.
                                $wrath_regain = self::BASE_WRATH_REGAIN;
                            }
                        }

                        $attacker->addKills($killpoints); // Attacker gains their killpoints.
                        $target->death();

                        if (!$simultaneousKill)	{
                            // This stuff only happens if you don't die also.
                            $loot = floor($gold_mod * $target->gold);

                            // Add the wrath health regain to the attacker.
                            if (isset($wrath_regain)) {
                                $attacker->heal($wrath_regain);
                            }
                        }

                        $target_msg = "DEATH: You've been killed by {$attacker->name()} and lost $loot gold!";
                        Event::create($attacker->id(), $target->id(), $target_msg);
                        // Stopped telling attackers when they win a duel.

                        $bounty_result = Combat::runBountyExchange($attacker, $target);	// *** Determines bounty for dueling. ***
                    }

                    if ($attacker->health < 1) { // *** DEFENDER KILLS ATTACKER! ***
                        $simultaneousKill = ($target->health < 1);

                        if (!$simultaneousKill)	{
                            $victor = $target;
                            $loser  = $attacker;
                        }

                        $attacker_died = true;

                        $defenderKillpoints = 1;

                        if ($duel) { // *** if they were dueling when they died ***
                            $duel_log_msg = $attacker->name()." has dueled {$target->name()} and lost at ".date("F j, Y, g:i a");
                            Event::create((int)"SysMsg", (int)"SysMsg", $duel_log_msg);
                            GameLog::sendLogOfDuel($attacker->name(), $target->name(), 0, $killpoints);	// *** Makes a loss in the duel log. ***
                        }

                        $target->addKills($defenderKillpoints); // Adds a kill for the defender
                        $attacker->death();

                        if (!$simultaneousKill) {
                            $loot = floor($gold_mod * $attacker->gold); //Loot for defender if he lives.
                        }

                        $target_msg = "You have killed {$attacker->name()} in combat and taken $loot gold.";

                        $attacker_msg = "DEATH: You've been killed by {$target->name()} and lost $loot gold!";

                        Event::create($attacker->id(), $target->id(), $target_msg);
                        Event::create($target->id(), $attacker->id(), $attacker_msg);
                    }
                }
            }

            if ($loot) {
                $victor->set_gold($victor->gold + $loot);
                $loser->set_gold($loser->gold - $loot);
            }

            if ($rounds > 4) { // Evenly matched battle! Reward some ki to the attacker, even if they die
                $rewarded_ki = 1;

                $attacker->set_ki($attacker->ki + $rewarded_ki);
            }

            $attack_again = (isset($target) && $attacker->health() > 0 && $target->health() > 0);

            $target_ending_health = $target->health();
            $target->save();
        }

        // *** Take away at least one turn even on attacks that fail. ***
        if ($turns_to_take < 1) {
            $turns_to_take = 1;
        }

        $attacker->changeTurns(-1*$turns_to_take);
        $attacker->save();

        return new StreamedViewResponse('Battle Status', 'attack_mod.tpl', get_defined_vars(), ['quickstat' => 'player' ]);
    }
}
