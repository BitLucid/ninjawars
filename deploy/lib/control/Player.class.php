<?php
require_once(CORE . "control/Character.php");
require_once(CORE . "control/lib_status.php");
require_once(CORE . "control/lib_player.php");
require_once(CORE . "data/AccountFactory.php");
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(DB_ROOT . "PlayerVO.class.php");

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Clan;

/* Ninja (actually character) behavior object.
 *
 * This file should make use of a private PlayerVO.class.php and PlayerDAO.class.php
 * to propagate and save its data.
 *
 * @category    Template
 * @package     char
 * @subpackage	player
 * @author      Tchalvak <ninjawarsTchalvak@gmail.com>
 * @author
 * @link        http://ninjawars.net/player.php?player=tchalvak
*/

class Player implements Character {
	public $player_id;
	public $vo;
	public $status;
	public $ip;
	public $avatar_url;

	public function __construct($player_id_or_username=null) {
		if (!empty($player_id_or_username)) {
			if (!is_numeric($player_id_or_username) && is_string($player_id_or_username)) {
				$sel = "SELECT player_id FROM players WHERE lower(uname) = lower(:uname) LIMIT 1";
				$this->player_id = query_item($sel, array(':uname'=>array($player_id_or_username, PDO::PARAM_INT)));
			} else {
				$this->player_id = (int) $player_id_or_username;
			}

			$dao = new PlayerDAO();
			if (!($this->vo = $dao->get($this->player_id))) {
				$this->vo = new PlayerVO();
				$this->avatar_url = null;
			}
		} else {
			$this->vo = new PlayerVO();
			$this->avatar_url = null;
		}
	}

	public function __toString() {
		return $this->name();
	}

	public function __get($name) {
		return $this->vo->$name;
	}

	public function name() {
		return $this->vo->uname;
	}

	public function id() {
		return $this->vo->player_id;
	}

	public function level() {
		return $this->vo->level;
	}

	public function description() {
		return $this->vo->description;
	}

	/**
	 * Get out of character message
	**/
	public function message() {
		return $this->vo->messages;
	}

	public function beliefs() {
		return $this->vo->beliefs;
	}

	public function instincts() {
		return $this->vo->instincts;
	}

	public function goals() {
		return $this->vo->goals;
	}

	// Return simple, comma separated string of traits
	public function traits() {
		return $this->vo->traits;
	}

	// Store new goals
	public function set_goals($goals){
		$this->vo->goals = $goals;
	}

	public function set_description($desc){
		$this->vo->description = $desc;
	}

	/**
	 * Out of character message
	**/
	public function set_message($message){
		$this->vo->messages = $message;
	}

	public function set_instincts($in){
		$this->vo->instincts = $in;
	}

	public function set_beliefs($be){
		$this->vo->beliefs = $be;
	}

	public function set_traits($traits){
		$this->vo->traits = $traits;
	}

	// Actively pulls the latest status data from the db.
	protected function queryStatus() {
		$id = $this->id();
		if ($id) {
			return query_item("SELECT status FROM players WHERE player_id = :player_id", 
				array(':player_id'=>array($id, PDO::PARAM_INT)));
		} else {
			return null;
		}
	}

	protected function getStatus() {
		return $this->queryStatus();
	}

	public function addStatus($p_status) {
		if ((int)$p_status == $p_status && $p_status != 0) {
			if ($p_status < 0) {
				return $this->subtractStatus(abs($p_status));
			} else {
				$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = status+:status1 WHERE player_id = :player AND status&:status2 = 0');
				$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
				$statement->bindValue(':status1', $p_status, PDO::PARAM_INT);
				$statement->bindValue(':status2', $p_status, PDO::PARAM_INT);
				$statement->execute();

				$this->vo->status = null; // *** Ensures that the next call to hasStatus pulls the updated status from the DB ***
			}
		}
	}

	public function resetStatus() {
		$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = 0 WHERE player_id = :player');
		$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
		$statement->execute();

		$this->vo->status = 0;
	}

	public function subtractStatus($p_status) {
		$status = valid_status($p_status); // Filter it.
		if ((int)$status == $status && $status > 0) {
			$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = status-:status1 WHERE player_id = :player AND status&:status2 <> 0');
			$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
			$statement->bindValue(':status1', $status, PDO::PARAM_INT);
			$statement->bindValue(':status2', $status, PDO::PARAM_INT);
			$statement->execute();

			$this->vo->status = null; // *** Ensures that the next call to hasStatus pulls the updated status from the DB ***
		}
	}
	
	// Standard damage output.
	public function damage(Character $enemy=null){
		return rand(1, $this->max_damage($enemy));
	}

	public function max_damage(Character $enemy=null){
		$dam = $this->strength() * 5 + $this->speed();
		return $dam;
	}

	// The maximum damage.
	public function maxDamage(){
		return $this->damage(); // Currently they're the same, though they probably shouldn't be.
	}

	/*
	public function isArmored(){
		return false;
	}*/

	// Old Wrapper function name.
	public function getStrength() {
		return $this->strength();
	}

	public function strength() {
		$str = $this->vo->strength;
		if ($this->hasStatus(WEAKENED)) {
			return max(1, $str-(ceil($str*.25))); // 75%
		} elseif ($this->hasStatus(STR_UP2)) {
			return $str+(ceil($str*.25)); // 125%
		} elseif ($this->hasStatus(STR_UP1)) {
			return $str+(ceil($str*.12)); //112%
		} else {
			return $str;
		}
	}
	
	public function setStrength($str){
		if($str < 0){
			throw new \InvalidArgumentException('Strength cannot be set as a negative.');
		}
		$this->vo->strength = $str;
	}



	public function speed() {
		$speed = $this->vo->speed;
		if ($this->hasStatus(SLOW)) {
			return $speed-(ceil($speed*.25));
		} else {
			return $speed;
		}
	}

	public function setSpeed($speed){
		if($speed < 0){
			throw new \InvalidArgumentException('Speed cannot be set as a negative.');
		}
		$this->vo->speed = $speed;
	}

	public function stamina() {
		$stam = $this->vo->stamina;
		if ($this->hasStatus(POISON)) {
			return $stam-(ceil($stam*.25));
		} else {
			return $stam;
		}
	}

	public function setStamina($stamina){
		if($stamina < 0){
			throw new \InvalidArgumentException('Stamina cannot be set as a negative.');
		}
		$this->vo->stamina = $stamina;
	}

	public function ki() {
		return $this->vo->ki;
	}

	public function set_ki($ki){
		if($ki < 0){
			throw new \InvalidArgumentException('Ki cannot be negative.');
		}
		return $this->vo->ki = $ki;
	}

	public function karma() {
		return $this->vo->karma;
	}

	public function gold() {
		return $this->vo->gold;
	}

	public function set_gold($gold) {
		if($gold < 0){
			throw new \InvalidArgumentException('Gold cannot be made negative.');
		}
		if((int) $gold != $gold){
			throw new \InvalidArgumentException('Gold must be a whole number [not '.(string)$gold.'].');
		}
		return $this->vo->gold = $gold;
	}

	public function bounty() {
		return $this->vo->bounty;
	}

	public function set_bounty($bounty) {
		if($bounty < 0){
			throw new \InvalidArgumentException('Bounty cannot be made negative ['.(string)$bounty.'].');
		}
		if((int) $bounty != $bounty){
			throw new \InvalidArgumentException('Bounty must be a whole number [not '.(string)$bounty.'].');
		}
		return $this->vo->bounty = $bounty;
	}

	public function hasStatus($p_status) {
		$status = valid_status($p_status);
		if ($status) {
			return (bool)($this->getStatus()&$status);
		} else {
			return false;
		}
	}

	/**
	 * Checks whether the character is still active.
	**/
	public function isActive() {
		return (bool) $this->vo->active;
	}

	public function isAdmin() {
		$name = strtolower($this->name());
		if ($name == 'tchalvak' || $name == 'beagle' || $name == 'suavisimo') {
			return true;
		}

		return false;
	}

	public function death() {
		$this->resetStatus();
		$this->subtractHealth($this->health());
	}

	public function email() {
		$account = account_info_by_char_id($this->id());
		return $account['active_email'];
	}

	public function turns() {
		return $this->vo->turns;
	}

	public function set_turns($turns){
		if($turns < 0){
			throw new \InvalidArgumentException('Turns cannot be made negative.');
		}
		return $this->vo->turns = $turns;
	}

	public function changeTurns($amount) {
		$diff = $amount;
		$this->set_turns($this->turns() + $diff);
		return change_turns($this->id(), $amount);
	}
	
	public function subtractTurns($amount){
		$diff = -1*abs($amount);
		$this->set_turns($this->turns() + $diff);
		return change_turns($this->id(), $diff);
	}

	// Pull the data of the player obj as an array.
	public function data($specific = null) {
		static $data;
		if (!$data) {
			$data = add_data_to_player_row($this->as_array());
			// Cache this data over the life of the player object.
		}

		if ($specific) {
			return $data[$specific];
		} else {
			return $data;
		}
	}

	public function as_vo() {
		return $this->vo;
	}

	public function as_array() {
		return (array) $this->vo;
	}

	public function getClan() {
		return ClanFactory::clanOfMember($this->id());
	}

	// Pull the class identity for a character.
	public function class_identity() {
		return char_class_identity($this->id());
	}

	// Pull the class display name for a character.
	public function class_display_name() {
		return char_class_name($this->id());
	}

	/**
	 * Heal the char with in the limits of their max
	 */
	public function heal($amount) {
		$hurt = $this->is_hurt_by();
		// Heal at most the amount hurt, or the amount requested, pick whichever is smallest.
		$heal = min($hurt, $amount);
		return $this->changeHealth($heal);
	}

	/**
	 * Do some damage to the character
	 * @note for now this immediately hits the database
	 */
	public function harm($damage) {
		// Do at most the current health in damage
		$actual_damage = min($this->health(), $damage);
		return $this->subtractHealth($actual_damage);
	}

	// Simple wrapper for changeHealth
	public function addHealth($amount) {
		return $this->changeHealth($amount);
	}

	// Simple wrapper for subtractive action.
	public function subtractHealth($amount) {
		return $this->changeHealth((-1*(int)$amount));
	}

	// To subtract just send in a negative integer.
	public function changeHealth($add_amount) {
		$amount = (int)$add_amount;
		// Only change on positive or negative changes, not zero.
		if (abs($amount) > 0) {
			$id = $this->id();
			// Set health = 0 when it's less than zero, otherwise modify it.
			$up = "UPDATE players SET health = 
				CASE WHEN health + :amount < 0 THEN 0 ELSE health + :amount2 END 
				WHERE player_id  = :player_id";
			query($up, array(':player_id'=>array($id, PDO::PARAM_INT),
				':amount'=>$amount, ':amount2'=>$amount));
			$this->vo->health = $this->vo->health + $amount;
		}
		return $this->health(); // Return the current health.
	}

	// Pull the current health.	
	public function health() {
		$sel = "SELECT health from players where player_id = :id";
		return query_item($sel, [':id'=>[$this->id(), PDO::PARAM_INT]]);
	}

	public function set_health($health){
		if($health < 0){
			throw new \InvalidArgumentException('Health cannot be made negative.');
		}
		if((int) $health != $health){
			throw new \InvalidArgumentException('Health must be a whole number.');
		}
		return $this->vo->health = $health;
	}

	/** 
	 * Return the amount below the max health (or zero).
	 * @return int
	 */
	public function is_hurt_by() {
		return max(0, 
			(int) ($this->max_health() - $this->health())
		);
	}

	// This char's max health
	public function max_health() {
		return max_health_by_level($this->level());
	}

	// Return the current percentage health.
	public function health_percent() {
		return health_percent($this->health(), $this->level());
	}


	public function ip() {
		$this->ip = isset($this->ip) && $this->ip? $this->ip : account_info_by_char_id($this->id(), 'last_ip');
		return $this->ip;
	}

	public function difficulty(){
		return 10 + $this->strength() * 2 + $this->maxDamage()/* + $this->isArmored() * 5*/;
	}

	public function getVerificationNumber(){
		return $this->vo->verification_number;
	}

	/**
	 * Get the avatar url for a pc
	**/
	public function avatarUrl(){
		if(!isset($this->avatar_url) || $this->avatar_url === null){
			$this->avatar_url = generate_gravatar_url($this);
		}
		return $this->avatar_url;
	}

	/**
	 * Save information
	 * Saves:
	 * gold
	 * turns
	 * all non-foreign key data in vo
	 * @return bool
	 */
	public function save(){
		$factory = new PlayerDAO();
		$updated = $factory->save($this->vo);
		return $this;
	}

	/**
	 * Find a player by primary key
	 * @return Player
	 */
	public static function find($id){
		if(!is_numeric($id) || !(int) $id){
			return null;
		}
		$id = (int) $id;
		$dao = new PlayerDAO();
		$data = $dao->get($id);
		if(!isset($data->player_id) || !$data->player_id){
			return null;
		}
		$player = new Player();
		$player->vo = $data;
		return $player;
	}

}
