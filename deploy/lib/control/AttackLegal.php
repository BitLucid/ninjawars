<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT . 'control/Player.class.php');

use NinjaWars\core\control\Clan;
use \Player;
use \Constants;

/**
 * Checks that all the requirements for attacking are in a legal state.
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
    private $attacker;
    private $target;
    private $params;

    /**#@-*/

    /**
     * Constructor
     *
     * Sets up the parameters for a attack legal check.
     * @param Player $p_attacker The attacker
     * @param Player $p_target The target
     * @param array $params The further conditions of the attack.
     * @todo Soon this should dependency-inject the attacker only
     */
    public function __construct($p_attacker, $p_target, $params = array()) {
        $this->target   = null;
        $this->error    = null;
        $defaults = ['required_turns'=>null, 'ignores_stealth'=>null, 'self_use'=>null, 'clan_forbidden'=>null];
        $this->params = array_merge($defaults, $params);

        if ($this->params['required_turns'] === null) {
            throw new \Exception('Error: AttackLegal required turns not specified.');
        }

        if (!($p_attacker instanceof Player)) {
            throw new \InvalidArgumentException('$p_attacker must be a Player object');
        }

        if (!($p_target instanceof Player)) {
            throw new \InvalidArgumentException('$p_target must be a Player object');
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
     * @return boolean
     */
    public function sameDomain(Player $target, Player $self) {
        // Get all the various ips that shouldn't be matches, and prevent them from being a problem.
        $server_addr = isset($_SERVER['SERVER_ADDR'])? $_SERVER['SERVER_ADDR'] : null;
        $host= gethostname();
        $active_ip = gethostbyname($host);
        $allowable = array_merge(['127.0.0.1', $server_addr, $active_ip], Constants::$trusted_proxies);
        $self_ip = $self->ip();

        if (!$self_ip || in_array($self_ip, $allowable) ) {
            return false;  // Don't have to obtain the target's ip at all if these are the case!
        } else {
            return $self_ip === $target->ip();
        }
    }

    /**
     * Check that attack spamming isn't occurring too fast
     *
     * @return bool
     */
    private function isOverTimeLimit() {
        $attackIntervalLimit = '.25'; // Originally .2
        $lastAttackQuery = "SELECT player_id FROM players
            WHERE player_id = :char_id
            AND ((now() - :interval::interval) >= last_started_attack) LIMIT 1";

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
    private function updateLastAttack(Player $attacker) {
        // updates the timestamp of the last_attacked column to slow excessive attacks.
        $update_last_attacked = "UPDATE players SET last_started_attack = now() WHERE player_id = :pid";
        $updated = update_query($update_last_attacked, [':pid'=>$attacker->id()]);
        return (bool) $updated;
    }

    /**
     * Checks whether an attack is legal or not.
     *
     * @return boolean
     */
    public function check($update_timer = true) {
        $attacker = $this->attacker;
        $target   = $this->target;

        // Will also use
        if (!is_object($this->attacker)) {
            $this->error = 'Only Ninja can get close enough to attack.';
            return FALSE;
        } elseif (!is_object($this->target)){
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
        if (!$timing_allowed) {
            $this->error = 'Even the fastest ninja cannot act more than four times a second.';
        } else if (empty($target->vo->uname)) {
            $this->error = 'Your target does not exist.';
        } else if (($target->id() == $attacker->id()) && !$this->params['self_use']) {
            $this->error = 'Commiting suicide is a tactic reserved for samurai.';
        } else if ($attacker->vo->turns < $this->params['required_turns']) {
            $this->error = 'You don\'t have enough turns for that, wait for the half hour or use amanita mushrooms to gain more turns.';
        } else if (!$this->params['self_use'] && $this->sameDomain($target, $attacker)) {
            $this->error = 'You can not attack a ninja from the same domain.';
        } else if ($target->vo->active == 0) {
            $this->error = 'You can not attack an inactive ninja.';
        } else if ($attacker->vo->active == 0) {
            $this->error = 'You cannot attack when your ninja is retired/inactive.';
        } else if ($target->health() < 1) {
            $this->error = "They're already dead.";
        } else if ($target->hasStatus(STEALTH) && !$this->params['ignores_stealth']) {
            // Attacks that ignore stealth will skip this.
            $this->error = 'Your target is stealthed. You can only hit this ninja using certain techniques.';
        } else if ($this->params['clan_forbidden'] && ($attacker->getClan() instanceof Clan) && ($target->getClan()->getID() == $attacker->getClan()->getID()) && !$this->params['self_use']) {
            $this->error = 'Your clan would outcast you if you attacked one of your own.';
        } else if ($target->health() > 0) {
            $this->error = null;
            return true;  //  ***  ATTACK IS LEGAL ***
        } else {  //  *** CATCHALL ERROR MESSAGE ***
            $this->error = 'There was a problem with your attack.';
            error_log('The problem catch-all for attackLegal object was triggered, which should not occur.');
        }

        return empty($this->error);
    }
}
