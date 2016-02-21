<?php
require_once(CORE . "control/lib_status.php");
require_once(CORE . "control/lib_player.php");
require_once(CORE . "control/lib_accounts.php");

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\PlayerDAO;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\AccountFactory;

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
    private $data;

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

    /**
     * Old Wrapper function name
     */
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

    /**
     * @return int
     */
    public function getMaxHealth() {
        return self::maxHealthByLevel($this->level);
    }

    /**
     * Pull the data of the player obj as an array.
     *
     * @note
     * This function lazy loads the data only once per instance
     */
	public function data($specific = null) {
		if (!$this->data) {
            $this->data = $this->as_array();
            $this->data['next_level']    = $this->killsRequiredForNextLevel();
            $this->data['max_health']    = $this->getMaxHealth();
            $this->data['hp_percent']    = $this->health_percent();
            $this->data['max_turns']     = 100;
            $this->data['turns_percent'] = min(100, round($this->data['turns']/$this->data['max_turns']*100));
            $this->data['exp_percent']   = min(100, round(($this->data['kills']/$this->data['next_level'])*100));
            $this->data['status_list']   = implode(', ', get_status_list($this->data['player_id']));
            $this->data['hash']          = md5(implode($this->data));

            unset($this->data['pname']);
        }

        if ($specific) {
			return $this->data[$specific];
		} else {
			return $this->data;
		}
	}

    /**
     * Returns the state of the player from the database,
     */
    public function dataWithClan() {
        $player_data = $this->data();
        $player_data['clan_id'] = ($this->getClan() ? $this->getClan()->getID() : null);

        return $player_data;
    }

    /**
     * Return the data that should be publicly readable to javascript or the api while the player is logged in.
     */
    public function publicData() {
        $char_info = $this->dataWithClan();
        unset($char_info['ip'], $char_info['member'], $char_info['pname'], $char_info['pname_backup'], $char_info['verification_number'], $char_info['confirmed']);

        return $char_info;
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
		return self::maxHealthByLevel($this->level());
	}

    /**
     * Return the current percentage of the maximum health that a character could have.
     */
	public function health_percent() {
        return min(100, round(($this->health/$this->getMaxHealth())*100));
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
     */
    public function avatarUrl() {
        if (!isset($this->avatar_url) || $this->avatar_url === null) {
            $this->avatar_url = $this->generateGravatarUrl();
        }

        return $this->avatar_url;
    }

    private function generateGravatarUrl() {
        if (OFFLINE) {
            return IMAGE_ROOT.'default_avatar.png';
        } else if (!$this->vo || !$this->vo->avatar_type || !$this->email()) {
            return '';
        } else {	// Otherwise, use the player info for creating a gravatar.
            $email       = $this->email();

            $def         = 'monsterid'; // Default image or image class.
            // other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
            $base        = "http://www.gravatar.com/avatar/";
            $hash        = md5(trim(strtolower($email)));
            $no_gravatar = "d=".urlencode($def);
            $size        = 80;
            $rating      = "r=x";
            $res         = $base.$hash."?".implode('&', [$no_gravatar, $size, $rating]);

            return $res;
        }
	}

	/**
	 * Save information
     *
	 * Saves:
	 * gold
	 * turns
	 * all non-foreign key data in vo
     *
	 * @return Player
	 */
	public function save() {
		$factory = new PlayerDAO();
		$factory->save($this->vo);

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

    /**
     * query the recently active players
     */
    public static function findActive($limit=5, $alive_only=true) {
        $where_cond = ($alive_only ? ' AND health > 0' : '');
        $sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
        $active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
        return $active_ninjas;
    }

     /**
     * Check whether the player is the leader of their clan.
     */
    public function isClanLeader() {
        return (($clan = ClanFactory::clanOfMember($this->id())) && $this->id() == $clan->getLeaderID());
    }

    /**
     * Set the character's class, using the identity.
     */
    public function setClass($new_class) {
        if (!$this->isValidClass(strtolower($new_class))) {
            return "That class was not an option to change into.";
        } else {
            $up = "UPDATE players SET _class_id = (select class_id FROM class WHERE class.identity = :class) WHERE player_id = :char_id";
            query($up, array(':class'=>strtolower($new_class), ':char_id'=>$this->id()));

            return null;
        }
    }

    /**
     * Check that a class matches against the class identities available in the database.
     *
     * @return boolean
     */
    private function isValidClass($candidate_identity) {
        return (boolean) query_item(
            "SELECT identity FROM class WHERE identity = :candidate",
            [':candidate' => $candidate_identity]
        );
    }

    /**
     * Calculate a max health by a level
     */
    public static function maxHealthByLevel($level) {
        $health_per_level = 25;
        return 150 + round($health_per_level*($level-1));
    }

    /**
     * The number of kills needed to level up to the next level.
     *
     * 5 more kills in cost for every level you go up.
     */
    public function killsRequiredForNextLevel() {
       return $this->level()*5;
    }

    /**
     * Takes in a Character and adds kills to that character.
     */
    public function addKills($amount) {
        return $this->changeKills((int)abs($amount));
    }

    /**
     * Takes in a Character and removes kills from that character.
     */
    public function subtractKills($amount) {
        return $this->changeKills(-1*((int)abs($amount)));
    }

    /**
     * Change the kills amount of a char, and levels them up when necessary.
     */
    private function changeKills($amount) {
        $amount = (int)$amount;

        GameLog::updateLevellingLog($this->id(), $amount);

        if ($amount !== 0) { // Ignore changes that amount to zero.
            if ($amount > 0) { // when adding kills, check if levelling occurs
                $this->levelUp();
            }

            query(
                "UPDATE players SET kills = kills + CASE WHEN kills + :amount1 < 0 THEN kills*(-1) ELSE :amount2 END WHERE player_id = :player_id",
                [
                    ':amount1'   => [$amount, PDO::PARAM_INT],
                    ':amount2'   => [$amount, PDO::PARAM_INT],
                    ':player_id' => $this->id()
                ]
            );
        }

        return query_item(
            "SELECT kills FROM players WHERE player_id = :player_id",
            [
                ':player_id' => [$this->id(), PDO::PARAM_INT]
            ]
        );
    }

    /**
     * Leveling up Function
     */
    public function levelUp() {
        // Setup values:
        $health_to_add     = 100;
        $turns_to_give     = 50;
        $ki_to_give        = 50;
        $stat_value_to_add = 5;
        $karma_to_give     = 1;

        if ($this->isAdmin()) { // If the character is an admin, do not auto-level
            return false;
        } else { // For normal characters, do auto-level
            // Have to be under the max level and have enough kills.
            $level_up_possible = (
                ($this->level() + 1 <= MAX_PLAYER_LEVEL) &&
                ($this->kills >= $this->killsRequiredForNextLevel())
            );

            if ($level_up_possible) { // Perform the level up actions
                $this->set_health($this->health() + $health_to_add);
                $this->set_turns($this->turns()   + $turns_to_give);
                $this->set_ki($this->ki()         + $ki_to_give);

                // Must read from VO for these as accessors return modified values
                $this->setStamina($this->vo->stamina   + $stat_value_to_add);
                $this->setStrength($this->vo->strength + $stat_value_to_add);
                $this->setSpeed($this->vo->speed       + $stat_value_to_add);

                // no mutator for these yet
                $this->vo->kills = max(0, $this->kills - $this->killsRequiredForNextLevel());
                $this->vo->karma = ($this->karma() + $karma_to_give);
                $this->vo->level = ($this->level() + 1);

                $this->save();

                GameLog::recordLevelUp($this->id());

                $account = AccountFactory::findByChar($this);
                $account->setKarmaTotal($account->getKarmaTotal() + $karma_to_give);
                AccountFactory::save($account);

                // Send a level-up message, for those times when auto-levelling happens.
                send_event($this->id(), $this->id(),
                    "You levelled up! Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by $karma_to_give, and your Ki raised $ki_to_give! You gained some health and turns, as well! You are now a level {$this->level()} ninja! Go kill some stuff.");
                return true;
            } else {
                return false;
            }
        }
    }

}
