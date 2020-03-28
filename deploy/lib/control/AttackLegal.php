<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use \Constants;

/**
 * Validates that all the requirements for attacking are in a legal state.
 *
 * Simple example:
 * <code>
 * $AttackLegal = new AttackLegal($attackerObj, $target_name_or_id, $params);
 * $attack_check = $AttackLegal->check();
 * $attack_error = $AttackLegal->getError();
 * </code>
 *
 * @category    Combat
 * @package     Attack
 * @author      Roy Ronalds <roy.ronalds@gmail.com>
 */
class AttackLegal {
    /**#@+
     * @access private
     */
    /**
     * The error that comes with the illegal attack, if any.
     * @var string
     */
    private $error;
    /**
     * @var Player
     */
    private $attacker;
    /**
     * @var Player
     */
    private $target;
    /**
     * @var Player
     */
    private $params;

    /**#@-*/

    /**
     * Constructor
     *
     * Sets up the parameters for a attack legal check.
     * @param Player $p_attacker The attacker
     * @param Player $p_target   The target
     * @param array  $params     The further conditions of the attack.
     */
    public function __construct(Player $p_attacker, Player $p_target, $params = array()) {
        $this->target   = null;
        $this->error    = null;
        $defaults = ['required_turns'=>null, 'ignores_stealth'=>null, 'self_use'=>null, 'clan_forbidden'=>null];
        $this->params = array_merge($defaults, $params);

        if ($this->params['required_turns'] === null) {
            throw new \InvalidArgumentException('Error: AttackLegal required turns not specified.');
        }

        $this->attacker = $p_attacker;
        $this->target = $p_target;
    }

    /**
     * Run this after the check.
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Return true on matching ip characteristics.
     *
     * @todo cleanup the allowable IP addresses logic
     * @return bool
     */
    public function sameDomain(Player $target, Player $self): bool {
        // Get all the various ips that shouldn't be matches, and prevent them from being a problem.
        $server_addr = isset($_SERVER['SERVER_ADDR'])? $_SERVER['SERVER_ADDR'] : null;
        $host= gethostname();
        $active_ip = gethostbyname($host);
        $allowable = array_merge(['127.0.0.1', $server_addr, $active_ip], Constants::$trusted_proxies);

        $self_account = Account::findByChar($self);
        $target_account = Account::findByChar($target);

        $self_ip = $self_account->getLastIp();

        if (!$self_ip || in_array($self_ip, $allowable)) {
            return false;  // Don't have to obtain the target's ip at all if these are the case!
        } else {
            return $self_ip === $target_account->getLastIp();
        }
    }

    /**
     * Check that attack spamming isn't occurring too fast
     *
     * @return bool
     */
    private function isOverTimeLimit(): bool {
        $attackIntervalLimit = '.25'; // Originally .2
        $lastAttackQuery = "SELECT player_id FROM players
            WHERE player_id = :char_id
            AND (
                ((now() - :interval::interval) >= last_started_attack) 
                OR last_started_attack is null 
            ) LIMIT 1";

        // *** Returns a player id if the enough time has passed, or else or false/null. ***
        return (bool) query_item(
            $lastAttackQuery,
            [
                ':char_id'  => intval($this->attacker->id()),
                ':interval' => $attackIntervalLimit.' second',
            ]
        );
    }

    /**
     * Just update the last attack attempt of a player in the database.
     */
    private function updateLastAttack(Player $attacker): bool {
        // updates the timestamp of the last_attacked column to slow excessive attacks.
        $update_last_attacked = "UPDATE players SET last_started_attack = now() WHERE player_id = :pid";
        $updated = update_query($update_last_attacked, [':pid'=>$attacker->id()]);
        return (bool) $updated;
    }

    /**
     * Check whether the attacker is dead
     *
     * @return boolean
     */
    public function iAmDead(): bool{
        return $this->attacker->health < 1;
    }

    /**
     * Checks whether an attack is legal or not.
     * @param bool $update_timer
     * @return boolean
     */
    public function check(bool $update_timer = true): bool {
        $attacker = $this->attacker;
        $target   = $this->target;

        // Will also use
        if (!($this->attacker instanceof Player)) {
            $this->error = 'Only Ninja can get close enough to attack.';
            return FALSE;
        } elseif (!($this->target instanceof Player)){
            $this->error = 'No valid target was found.';
            return FALSE;
        } elseif ($this->params['required_turns'] === null){
            $this->error = 'The required number of turns was not specified.';
            return FALSE;
        }

        $timing_allowed = $this->isOverTimeLimit();

        if ($timing_allowed && $update_timer) {
            $this->updateLastAttack($attacker);
        }

        //  *** START OF ILLEGAL ATTACK ERROR LIST  ***
        if (!$timing_allowed && $update_timer) {
            $this->error = 'Even the fastest ninja cannot act more than four times a second.';
        } elseif (empty($target->uname)) {
            $this->error = 'Your target does not exist.';
        } elseif (($target->id() == $attacker->id()) && !$this->params['self_use']) {
            $this->error = 'Commiting suicide is a tactic reserved for samurai.';
        } elseif ($attacker->turns < $this->params['required_turns']) {
            $this->error = 'You don\'t have enough turns for that, wait for them to replenish and then use amanita mushrooms.';
        //} elseif (!$this->params['self_use'] && $this->sameDomain($target, $attacker)) {
        //    $this->error = 'You can not attack a ninja from the same domain.';
        } elseif ($target->active == 0) {
            $this->error = 'You can not attack an inactive ninja.';
        } elseif ($attacker->active == 0) {
            $this->error = 'You cannot attack when your ninja is retired/inactive.';
        } elseif ($attacker->health < 1) {
            $this->error = 'You are dead and must revive.';
        } elseif ($target->health < 1) {
            $this->error = "They're already dead.";
        } elseif ($target->hasStatus(STEALTH) && !$this->params['ignores_stealth']) {
            // Attacks that ignore stealth will skip this.
            $this->error = 'Your target is stealthed. You can only hit this ninja using certain techniques.';
        } elseif ($this->params['clan_forbidden'] && ($attacker->getClan() instanceof Clan) && ($target->getClan()->id == $attacker->getClan()->id) && !$this->params['self_use']) {
            $this->error = 'Your clan would outcast you if you attacked one of your own.';
        } elseif ($target->health > 0) {
            $this->error = null;
            return true;  //  ***  ATTACK IS LEGAL ***
        } else {  //  *** CATCHALL ERROR MESSAGE ***
            $this->error = 'There was a problem with your attack.';
            error_log('The problem catch-all for attackLegal object was triggered, which should not occur.');
        }

        return empty($this->error);
    }
}
