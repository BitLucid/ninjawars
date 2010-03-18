<?php
require_once(DB_ROOT . "PlayerVO.class.php");
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(CHAR_ROOT . "Player.class.php");

/* PHP Attack Legal Check
 *
 * NAMING SCHEME: _ before private variables/functions, and not public.
 *
 * @category    Combat
 * @package     Attacks
 * @author      Roy Ronalds <roy.ronalds@gmail.com>
 * @author
 * @link        http://ninjawars.net/attack_mod.php
*/

/* Requires and require_onces.
*/

/*
 * Constant defines.
*/

/**
 * Checks that all the requirements for attacking are in a legal state.
 *
 * Simple example:
 * <code>
 * $AttackLegal = new AttackLegal($attacker_name_or_id, $target_name_or_id, $params);
 * $attack_check = $AttackLegal->check();
 * $attack_error = $AttackLegal->getError();
 * </code>
 *
 * @category    Combat
 * @package     Attack
 * @author      Roy Ronalds <roy.ronalds@gmail.com>
 */

class AttackLegal
{
	/**#@+
	* @access private
	*/
	/**
	* The error that comes with the illegal attack, if any.
	* @var string
	*/
	private $_error;
	private $attacker;
	private $target;

	/**#@-*/

	/**
	 * Constructor
	 *
	 * Sets up the parameters for a attack legal check.
	 * @param    mixed $attacker_info The attacker info, an object or else an id.
	 * @param    mixed $target_info The target info, an object or an id.
	 * @param    array $conditions The further conditions of the attack.
	 * @access public
	**/
	public function __construct($attacker_name_or_id = null, $target_name_or_id, $params = array()) {
		$this->attacker = null;
		$this->target   = null;
		$this->params   = $params;
		$this->error    = null;

		if ($attacker_name_or_id) {
			$this->attacker = new Player($attacker_name_or_id);
		} elseif ($username = get_username()) {
			$this->attacker = new Player($username);
		}

		if ($target_name_or_id) {
			$this->target = new Player($target_name_or_id);
		}
	}

	// Run this after the check.
	public function getError(){
		return $this->error;
	}

	/**
	 * Checks whether an attack is legal or not.
	 *
	 * @return boolean
	**/
	public function check() {
		$attacker = $this->attacker;
		$target = $this->target;

		$possible = array('required_turns', 'ignores_stealth', 'self_use', 'clan_forbidden');

		// *** Initializes all the possible param indexes. ***
		foreach ($possible as $loop_index) {
			$$loop_index = (isset($this->params[$loop_index]) ? $this->params[$loop_index] : NULL);
		}

		if (!is_object($this->attacker)) {
			$this->error = 'Only Ninja can get close enough to attack.';
			return FALSE;
		} elseif (!is_object($this->target)){
			$this->error = 'No valid target was found.';
			return FALSE;
		} elseif (!isset($this->params['required_turns'])){
			$this->error = 'The required number of turns was not specified.';
			return FALSE;
		}

		$target_status = $target->getStatus();

		$second_interval_limiter_on_attacks = '.25'; // Originally .2

		$sel_last_started_attack = "SELECT player_id FROM players
			WHERE player_id = :user
			AND ((now() - interval '".$second_interval_limiter_on_attacks." second') >= last_started_attack) LIMIT 1";
		// *** Returns a player id if the enough time has passed, or else or false/null. ***

		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare($sel_last_started_attack);
		$statement->bindValue(':user', intval($this->attacker->player_id));
		$statement->execute();

		$attack_later_than_limit = $statement->fetchColumn();

		if ($attack_later_than_limit) { // *** If not too soon, update the attack limit. ***
			update_last_attack_time($attacker->vo->player_id);
			// updates the timestamp of the last_attacked column to slow excessive attacks.
		}

		//  *** START OF ILLEGAL ATTACK ERROR LIST  ***
		if (!$attack_later_than_limit) {
			$this->error = "Even the fastest ninja cannot act more than four times a second.";
			return false;
		} else if ($target->vo->uname == "") {
			$this->error = "Your target does not exist.";
			return false;
		} else if (($target->player_id == $attacker->player_id) && !$self_use) {
			$this->error = "Commiting suicide is a tactic reserved for samurai.";
			return false;
		} else if ($attacker->vo->turns < $required_turns) {
			$this->error = "You don't have enough turns for that, use speed scrolls or wait for the half hour to gain more turns.";
			return false;
		} else if (isset($_SESSION) && ($target->vo->ip == $_SESSION['ip']) && ($_SESSION['ip'] != '127.0.0.1') && !$self_use) {
			$this->error = "You can not attack a ninja from the same domain.";
			return false;
		} else if ($target->vo->confirmed == 0) {
			$this->error = "You can not attack an inactive ninja.";
			return false;
		} else if ($target->vo->health < 1) {
			$this->error = "Your target is a ghost.";
			return false;
		} else if ($target_status['Stealth'] && !$ignores_stealth) {
			// Attacks that ignore stealth will skip this.
			$this->error = "Your target is stealthed. You can only hit this ninja using certain techniques.";
			return false;
		} else if ($clan_forbidden && ($target->vo->clan == $attacker->vo->clano) && ($attacker->vo->clan != "") && !$self_use) {
			$this->error = "Your clan would outcast you if you attacked one of your own.";
			return false;
		} else if ($target->vo->health > 0) {
			return true;  //  ***  ATTACK IS LEGAL ***
		} else {  //  *** CATCHALL ERROR MESSAGE ***
			$this->error = "There was a problem with your attack.";
			error_log('The problem catch-all for attackLegal object was triggered, which should not occur.');
			return false;
		}
	}
} // End Class AttackLegal

// *** Put any internal classes or other classes for this file's library here.
?>
