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

class AttackController extends AbstractController {
    const ALIVE = true;
    const PRIV  = true;
    const BASE_WRATH_REGAIN = 2;

    /**
     * @return Response
     */
    public function index() {
        $target  = in('target');
        $duel    = (in('duel')    ? true : NULL);
        $blaze   = (in('blaze')   ? true : NULL);
        $deflect = (in('deflect') ? true : NULL);
        $evade   = (in('evasion') ? true : NULL);

        // Template vars.
        $stealthed_attack = $stealth_damage = $stealth_lost =
            $pre_battle_stats = $rounds = $combat_final_results =
            $killed_target = $attacker_died = $bounty_result = $rewarded_ki =
            $wrath_regain = false;

        // *** Attack System Initialization ***
        $killpoints       = 0; // Starting state for killpoints
        $attack_turns     = 1; // Default cost, will go to zero if an error prevents combat
        $required_turns   = 0;
        $what             = ''; // This will be the attack type string, e.g. "duel"
        $loot             = 0;
        $simultaneousKill = NULL; // Not simultaneous by default
        $turns_to_take    = NULL; // Even on failure take at least one turn
        $attack_type      = [];
        $attack_again     = false;

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

        $target_player    = Player::find($target);
        $attacking_player = Player::find(SessionFactory::getSession()->get('player_id'));

        $skillListObj = new Skill();

        $ignores_stealth = false;

        foreach ($attack_type as $type) {
            $ignores_stealth = $ignores_stealth||$skillListObj->getIgnoreStealth($type);
            $required_turns += $skillListObj->getTurnCost($type);
        }

        // *** Attack Legal section ***
        $params = [
            'required_turns'  => $required_turns,
            'ignores_stealth' => $ignores_stealth,
        ];

        try {
            $attack_legal = new AttackLegal($attacking_player, $target_player, $params);
            $attack_is_legal = $attack_legal->check();
            $attack_error = $attack_legal->getError();
        } catch (\InvalidArgumentException $e) {
            $attack_is_legal = false;
            $attack_error = 'Could not determine valid target';
        }

        // ***  MAIN BATTLE ALGORITHM  ***
        if ($attack_is_legal) {
            // *** Target's stats. ***
            $target_health = $target_player->health;
            $target_level  = $target_player->level;
            $target_str    = $target_player->getStrength();

            // *** Attacker's stats. ***
            $attacker_health = $attacking_player->health;
            $attacker_level  = $attacking_player->level;
            $attacker_turns  = $attacking_player->turns;
            $attacker_str    = $attacking_player->getStrength();

            $starting_target_health = $target_health;
            $starting_turns         = $attacker_turns;
            $stealthAttackDamage    = $attacker_str;
            $level_check            = $attacker_level - $target_level;

            $loot   = 0;
            $victor = null;
            $loser  = null;

            // *** ATTACKING + STEALTHED SECTION  ***
            if (!$duel && $attacking_player->hasStatus(STEALTH)) { // *** Not dueling, and attacking from stealth ***
                // TODO: This area seems to be the area that contains the broken stealth-kill-without-reporting bug
                // https://github.com/BitLucid/ninjawars/issues/273
                $attacking_player->subtractStatus(STEALTH);
                $turns_to_take = 1;

                $stealthed_attack = true;
                $target_health = $target_player->harm($stealthAttackDamage);

                if (0 > $target_health) { // *** if Stealth attack of whatever damage kills target. ***
                    $victor = $attacking_player;
                    $loser  = $target_player;

                    $gold_mod     = .1;
                    $loot         = floor($gold_mod * $target_player->gold);

                    $target_msg   = "DEATH: You have been killed by a stealthed ninja in combat and lost $loot gold!";
                    $attacker_msg = "You have killed {$target_player->name()} in combat and taken $loot gold.";

                    $target_player->death();
                    Event::create((int)"A Stealthed Ninja", $target_player->id(), $target_msg);
                    Event::create($target_player->id(), $attacking_player->id(), $attacker_msg);
                    $bounty_result = Combat::runBountyExchange($attacking_player, $target_player); // *** Determines the bounty for normal attacking. ***

                    $stealth_kill = true;
                } else {	// *** if damage from stealth only hurts the target. ***
                    $stealth_damage = true;

                    Event::create($attacking_player->id(), $target_player->id(), $attacking_player->name()." has attacked you from the shadows for $stealthAttackDamage damage.");
                }
            } else {	// *** If the attacker is purely dueling or attacking, even if stealthed, though stealth is broken by dueling. ***
                // *** MAIN DUELING SECTION ***
                if ($attacking_player->hasStatus(STEALTH)) { // *** Remove their stealth if they duel instead of preventing dueling.
                    $attacking_player->subtractStatus(STEALTH);
                    $stealth_lost = true;
                }

                // *** PRE-BATTLE STATS - Template Vars ***
                $pre_battle_stats  = true;
                $pbs_attacker_str  = $attacking_player->getStrength();
                $pbs_attacker_hp   = $attacking_player->health();
                $pbs_target_str    = $target_player->getStrength();
                $pbs_target_hp     = $target_player->health();

                // *** BEGINNING OF MAIN BATTLE ALGORITHM ***

                $turns_counter         = $attack_turns;
                $total_target_damage   = 0;
                $total_attacker_damage = 0;
                $target_damage         = 0;
                $attacker_damage       = 0;

                // *** Combat Calculations ***
                $round = 1;
                $rounds = 0;

                while ($turns_counter > 0 && $total_target_damage < $attacker_health && $total_attacker_damage < $target_health) {
                    $turns_counter -= (!$duel ? 1 : 0);// *** SWITCH BETWEEN DUELING LOOP AND SINGLE ATTACK ***

                    $target_damage   = rand (1, $target_str);
                    $attacker_damage = rand (1, $attacker_str);

                    if ($blaze) {	// *** Blaze does double damage. ***
                        $attacker_damage = $attacker_damage*2;
                    }

                    if ($deflect) {
                        $target_damage = floor($target_damage/2);
                    }

                    $total_target_damage   += $target_damage;
                    $total_attacker_damage += $attacker_damage;
                    $rounds++;	// *** Increases the number of rounds that has occured and restarts the while loop. ***

                    if ($evade) {
                        // Evasion effect:
                        // Check current level of damage.
                        $testValue = ($attacker_health - $total_target_damage);
                        // Break off the duel/attack if less than 10% health or health is less than average of defender's strength
                        if ($testValue < ($target_str*.5) || $testValue < ($attacker_health*.1)) {
                            break;
                        }
                    }
                }

                // *** END OF MAIN BATTLE ALGORITHM ***

                $combat_final_results = true;
                $finalizedHealth = ($attacker_health-$total_target_damage);

                // *** RESULTING PLAYER MODIFICATION ***

                $gold_mod = 0.20;

                $turns_to_take = $required_turns;

                if ($duel) {
                    $gold_mod = 0.25;
                    $what     = "duel";
                }

                //  *** Let the victim know who hit them ***
                $attack_label = ($duel ? 'dueled' : 'attacked');

                $defenderHealthRemaining = $target_player->harm($total_attacker_damage);
                $attackerHealthRemaining = $attacking_player->harm($total_target_damage);

                if ($defenderHealthRemaining && $attackerHealthRemaining) {
                    $combat_msg = "You have been $attack_label by {$attacking_player->name()} for $total_attacker_damage, but they got away before you could kill them!";
                } else {
                    $combat_msg = "You have been $attack_label by {$attacking_player->name()} for $total_attacker_damage!";
                }

                Event::create($attacking_player->id(), $target_player->id(), $combat_msg);

                if ($defenderHealthRemaining < 1 || $attackerHealthRemaining < 1) { // A kill occurred.
                    if ($defenderHealthRemaining < 1) { // ATTACKER KILLS DEFENDER!
                        $simultaneousKill = ($attackerHealthRemaining < 1);

                        if (!$simultaneousKill) {
                            $victor = $attacking_player;
                            $loser  = $target_player;
                        }

                        $killed_target = true;

                        $killpoints = 1; // Changes killpoints from zero to one.

                        if ($duel) {
                            // Changes killpoints amount by dueling equation.
                            $killpoints = Combat::killpointsFromDueling($attacking_player, $target_player);

                            $duel_log_msg = $attacking_player->name()." has dueled {$target_player->name()} and won $killpoints killpoints.";

                            // Only log duels if they're better than 1 or if they're a failure.
                            if ($killpoints > 1 || $killpoints < 0) {
                                // Make a WIN record in the dueling log.
                                GameLog::sendLogOfDuel($attacking_player->name(), $target_player->name(), 1, $killpoints);
                            }

                            if ($skillListObj->hasSkill('wrath', $attacking_player)) {
                                // They'll retain 10 health for the kill, at the end.
                                $wrath_regain = self::BASE_WRATH_REGAIN;
                            }
                        }

                        $attacking_player->addKills($killpoints); // Attacker gains their killpoints.
                        $target_player->death();

                        if (!$simultaneousKill)	{
                            // This stuff only happens if you don't die also.
                            $loot = floor($gold_mod * $target_player->gold);

                            // Add the wrath health regain to the attacker.
                            if (isset($wrath_regain)) {
                                $attacking_player->heal($wrath_regain);
                            }
                        }

                        $target_msg = "DEATH: You've been killed by {$attacking_player->name()} and lost $loot gold!";
                        Event::create($attacking_player->id(), $target_player->id(), $target_msg);
                        // Stopped telling attackers when they win a duel.

                        $bounty_result = Combat::runBountyExchange($attacking_player, $target_player);	// *** Determines bounty for dueling. ***
                    }

                    if ($attackerHealthRemaining < 1) { // *** DEFENDER KILLS ATTACKER! ***
                        $simultaneousKill = ($attackerHealthRemaining < 1);

                        if (!$simultaneousKill)	{
                            $victor = $target_player;
                            $loser  = $attacking_player;
                        }

                        $attacker_died = true;

                        $defenderKillpoints = 1;

                        if ($duel) { // *** if they were dueling when they died ***
                            $duel_log_msg = $attacking_player->name()." has dueled {$target_player->name()} and lost at ".date("F j, Y, g:i a");
                            Event::create((int)"SysMsg", (int)"SysMsg", $duel_log_msg);
                            GameLog::sendLogOfDuel($attacking_player->name(), $target_player->name(), 0, $killpoints);	// *** Makes a loss in the duel log. ***
                        }

                        $target_player->addKills($defenderKillpoints); // Adds a kill for the defender
                        $attacking_player->death();

                        if (!$simultaneousKill) {
                            $loot = floor($gold_mod * $attacking_player->gold); //Loot for defender if he lives.
                        }

                        $target_msg = "You have killed {$attacking_player->name()} in combat and taken $loot gold.";

                        $attacker_msg = "DEATH: You've been killed by {$target_player->name()} and lost $loot gold!";

                        Event::create($attacking_player->id(), $target_player->id(), $target_msg);
                        Event::create($target_player->id(), $attacking_player->id(), $attacker_msg);
                    }
                }

                // *** END MAIN ATTACK AND DUELING SECTION ***
            }

            if ($loot) {
                $victor->set_gold($victor->gold + $loot);
                $loser->set_gold($loser->gold - $loot);
            }

            if ($rounds > 4) { // Evenly matched battle! Reward some ki to the attacker, even if they die
                $rewarded_ki = 1;

                $attacking_player->set_ki($attacking_player->ki + $rewarded_ki);
            }

            $attack_again = (isset($target_player) && $attacking_player->health() > 0 && $target_player->health() > 0);

            $target_ending_health = $target_player->health();
            $target_player->save();
        }

        // *** Take away at least one turn even on attacks that fail. ***
        if ($turns_to_take < 1) {
            $turns_to_take = 1;
        }

        $ending_turns = $attacking_player->changeTurns(-1*$turns_to_take);
        $attacking_player->save();

        return new StreamedViewResponse('Battle Status', 'attack_mod.tpl', get_defined_vars(), ['quickstat' => 'player' ]);
    }
}
