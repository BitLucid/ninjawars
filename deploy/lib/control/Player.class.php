<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\PlayerDAO;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\AccountFactory;

/**
 * Ninja (actually character) behavior object.
 *
 * This file should make use of a private PlayerVO.class.php and PlayerDAO.class.php
 * to propagate and save its data.
 *
 * @package     char
 * @subpackage	player
 * @author      Tchalvak <ninjawarsTchalvak@gmail.com>
 * @link        http://ninjawars.net/player.php?player=tchalvak
 * @property-read int health
 * @property-read int kills
 * @property-read int gold
 * @property-read int karma
 * @property-read int level
 */
class Player implements Character {
	public $player_id;
	public $vo;
	public $status;
	public $ip;
	public $avatar_url;
    private $data;

    /**
     * Accepts a userid or username, though null will initialize a blank
     */
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

    /**
     * @return string
     */
	public function __toString() {
		return $this->name();
	}

    /**
     * Magic method to provide accessors for properties
     *
     * @return mixed
     */
	public function __get($member_field) {
		return $this->vo->$member_field;
	}

    /**
     * Magic method to handle isset() and empty() calls against properties
     *
     * @return boolean
     */
    public function __isset($member_field) {
        return isset($this->vo->$member_field);
    }

    /**
     * @return string
     */
	public function name() {
		return $this->vo->uname;
	}

    /**
     * @return int
     */
	public function id() {
		return $this->vo->player_id;
	}

    /**
     * @return int
     */
	public function description() {
		return $this->vo->description;
	}

	/**
	 * Get out of character message
     * @return string
	**/
	public function message() {
		return $this->vo->messages;
	}

    /**
     * @return string
     */
	public function beliefs() {
		return $this->vo->beliefs;
	}

    /**
     * @return string
     */
	public function instincts() {
		return $this->vo->instincts;
	}

    /**
     * @return string
     */
	public function goals() {
		return $this->vo->goals;
	}

    /**
     * Return simple, comma separated string of traits
     * @return string
     */
	public function traits() {
		return $this->vo->traits;
	}

    /**
     * Store new goals
     */
	public function set_goals($goals){
		$this->vo->goals = $goals;
	}

    /**
     * In-character char description
     */
	public function set_description($desc){
		$this->vo->description = $desc;
	}

	/**
	 * Out of character message
	**/
	public function set_message($message){
		$this->vo->messages = $message;
	}

    /**
     * In-character instincts
     */
	public function set_instincts($in){
		$this->vo->instincts = $in;
	}

    /**
     * In-character beliefs
     */
	public function set_beliefs($be){
		$this->vo->beliefs = $be;
	}

    /**
     * Pass in traits as a raw comma separated string
     */
	public function set_traits($traits){
		$this->vo->traits = $traits;
	}

    /**
     * Actively pulls the latest status data from the db.
     */
	protected function queryStatus() {
		if ($this->id()) {
			return (int) query_item("SELECT status FROM players WHERE player_id = :player_id", 
				array(':player_id'=>array($this->id(), PDO::PARAM_INT)));
		} else {
			return null;
		}
	}

	protected function getStatus() {
		return $this->queryStatus();
	}

    /**
     * Adds a defined numeric status constant to the binary string of statuses
     */
	public function addStatus($p_status) {
        $status = self::validStatus($p_status); // Filter it.
        if($status !== null){
            // Binary add to current status, doing nothing if already set, e.g. 000 | 010 = 010
            $this->vo->status = $this->vo->status | $status;
            if(gettype($this->vo->status) !== 'integer'){
                throw new Exception('invalid type for status');
            }
            update_query('UPDATE players SET status = :status WHERE player_id = :player_id',
                    [
                    ':player_id'=>[$this->id(), PDO::PARAM_INT],
                    ':status'=>$this->vo->status
                    ]
                );
		}
	}

    /**
     * Remove a numeric status from the binary string of status toggles.
     */
    public function subtractStatus($p_status) {
        $status = self::validStatus($p_status); // Filter it.
        if ($status !== null) {
            // Remove current status from binary representation, e.g. 111 & ~010 = 101
            $current = $this->vo->status & ~$status;
            $this->vo->status = $current; // Store as int.
            update_query('UPDATE players SET status = :status WHERE player_id = :player_id',
                    [
                    ':player_id'=>[$this->id(), PDO::PARAM_INT],
                    ':status'=>$this->vo->status
                    ]
                );
        }
    }

    /**
     * Resets the binary status info to 0/none
     */
	public function resetStatus() {
		$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = 0 WHERE player_id = :player');
		$statement->bindValue(':player', $this->id(), PDO::PARAM_INT);
		$statement->execute();

		$this->vo->status = 0;
	}

    /**
     * Standard damage output from 1 to max
     * @return int
     */
	public function damage(Character $enemy=null){
		return rand(1, $this->max_damage($enemy));
	}

    /**
     * Max damage capability of a character
     * @return int
     */
	public function max_damage(Character $enemy=null){
		$dam = $this->strength() * 5 + $this->speed();
		return $dam;
	}

    /**
     * The maximum damage.
     * @return int
     */
	public function maxDamage() {
		return $this->max_damage();
	}

    /**
     * @return int
     */
	public function getStrength() {
		return $this->strength();
	}

    /**
     * @return int
     */
	public function strength() {
		$str = $this->vo->strength;
		if ($this->hasStatus(WEAKENED)) {
			return (int) max(1, $str-(ceil($str*.25))); // 75%
		} elseif ($this->hasStatus(STR_UP2)) {
			return (int) $str+(ceil($str*.50)); // 150%
		} elseif ($this->hasStatus(STR_UP1)) {
			return (int) $str+(ceil($str*.25)); //125%
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
			return (int) $speed-(ceil($speed*.25));
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
			return (int) $stam-(ceil($stam*.25));
		} else {
			return (int) $stam;
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

	public function gold() {
		return $this->vo->gold;
	}

	public function set_gold($gold) {
		if ($gold < 0) {
			throw new \InvalidArgumentException('Gold cannot be made negative.');
		}

		if ((int) $gold != $gold) {
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
		$status = self::validStatus($p_status);
		if ($status) {
			return (bool)($this->getStatus()&$status);
		} else {
			return false;
		}
	}

	/**
	 * Checks whether the character is still active.
     * @return boolean
	**/
	public function isActive() {
		return (bool) $this->vo->active;
	}

    /**
     * @return boolean
     * hardcoded hack at the moment
     * @note To be replaced by an in-database account toggle eventually
     */
	public function isAdmin() {
		$name = strtolower($this->name());
		if ($name == 'tchalvak' || $name == 'beagle' || $name == 'suavisimo') {
			return true;
		}

		return false;
	}

    /**
     *
     * Cleanup player to death state
     */
	public function death() {
		$this->resetStatus();
        $this->set_health(0);
        $this->save();
	}

    /**
     * @deprecated email for account of player
     */
	public function email() {
		$account = account_info_by_char_id($this->id());
		return $account['active_email'];
	}

	public function turns() {
		return $this->vo->turns;
	}

    public function set_turns($turns) {
        if ($turns < 0) {
            throw new \InvalidArgumentException('Turns cannot be made negative.');
        }

        return $this->vo->turns = $turns;
    }

    /**
     * @deprecated
     */
    public function changeTurns($amount) {
        $amount = (int) $amount;

        $this->set_turns($this->turns() + $amount);

        if ($amount) { // Ignore zero
            // These PDO parameters must be split into amount1 and amount2 because otherwise PDO gets confused.  See github issue 147.
            query("UPDATE players set turns = (CASE WHEN turns + :amount < 0 THEN 0 ELSE turns + :amount2 END) where player_id = :char_id",
                array(':amount'=>array($amount, PDO::PARAM_INT), ':amount2'=>array($amount, PDO::PARAM_INT), ':char_id'=>$this->id()));
        }

        return $this->turns;
    }

    /**
     * @deprecated
     */
    public function subtractTurns($amount) {
        $diff = -1*abs($amount);

        return $this->changeTurns($diff);
    }

    /**
     * @return integer
     */
    public function getMaxHealth() {
        return self::maxHealthByLevel($this->level);
    }

    /**
     * @return integer
     */
    public function max_health() {
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
            $this->data['status_list']   = implode(', ', get_status_list($this->id()));
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

    public function as_array() {
        return (array) $this->vo;
    }

    /**
     * @return Clan
     */
    public function getClan() {
        return ClanFactory::clanOfMember($this->id());
    }

	/**
	 * Heal the char with in the limits of their max
     * @return int
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
     * @return int
     * @param int $damage
	 */
	public function harm($damage) {
		// Do at most the current health in damage
        $current_health = $this->health();
		$actual_damage = min($current_health, (int) $damage);
		return $this->set_health($current_health - $actual_damage);
	}

    /**
     * Simple wrapper for subtractive action.
     * @return int
     * @deprecated use Player::harm() instead
     */
	public function subtractHealth($amount) {
		return $this->changeHealth((-1*(int)$amount));
	}

    /**
     * To subtract just send in a negative integer
     * @deprecated use set_health instead
     * @return int
     */
	public function changeHealth($delta) {
		$amount = (int)$delta;

		if (abs($amount) > 0) { // Only change on non-zero input
			$this->vo->health = max(0, $this->vo->health + $amount);

            query(
                "UPDATE players SET health = :amount WHERE player_id  = :player_id",
                [
                    ':player_id' => [$this->id(), PDO::PARAM_INT],
                    ':amount'    => $this->vo->health,
                ]
            );
        }

        return $this->vo->health;
    }

    /**
     * Pull the current health.
     * @return int
     */
	public function health() {
		$sel = "SELECT health from players where player_id = :id";
		return max(0, query_item($sel, [':id'=>[$this->id(), PDO::PARAM_INT]]));
	}

    /**
     * @return int
     */
	public function set_health($health) {
		if ($health < 0) {
			throw new \InvalidArgumentException('Health cannot be made negative.');
		}

		if ((int) $health != $health) {
			throw new \InvalidArgumentException('Health must be a whole number.');
		}

		return $this->vo->health = max(0, $health);
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

    /**
     * Return the current percentage of the maximum health that a character could have.
     * @return int
     */
	public function health_percent() {
        return min(100, round(($this->health/$this->getMaxHealth())*100));
	}

	public function ip() {
		$this->ip = isset($this->ip) && $this->ip? $this->ip : account_info_by_char_id($this->id(), 'last_ip');
		return $this->ip;
	}

    /**
     * @return int difficulty rating
     */
	public function difficulty(){
		return 10 + $this->strength() * 2 + $this->maxDamage()/* + $this->isArmored() * 5*/;
	}

    /**
     * @return int random private number unique to character
     */
	public function getVerificationNumber(){
		return $this->vo->verification_number;
	}

    /**
     * @return string url for the gravatar of pc
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
	 * @return Player|null
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
     * Find player by name
     * @return Player|null
     *
     */
    public static function findByName($name){
        $id = query_item('select player_id from players where lower(uname) = lower(:name) limit 1', [':name'=>$name]);
        if(!$id){
            return null;
        } else {
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

    /**
     * query the recently active players
     * @return array
     */
    public static function findActive($limit=5, $alive_only=true) {
        $where_cond = ($alive_only ? ' AND health > 0' : '');
        $sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
        $active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
        return $active_ninjas;
    }

     /**
     * Check whether the player is the leader of their clan.
     * @return boolean
     */
    public function isClanLeader() {
        return (($clan = ClanFactory::clanOfMember($this->id())) && $this->id() == $clan->getLeaderID());
    }

    /**
     * Set the character's class, using the identity.
     * @return string|null error string if fails
     */
    public function setClass($new_class) {
        if (!$this->isValidClass(strtolower($new_class))) {
            return "That class was not an option to change into.";
        } else {
            $class_id = query_item(
                "SELECT class_id FROM class WHERE class.identity = :class",
                [':class' => strtolower($new_class)]
            );

            $up = "UPDATE players SET _class_id = :class_id WHERE player_id = :char_id";

            query($up, [
                ':class_id' => $class_id,
                ':char_id'  => $this->id(),
            ]);

            $this->vo->identity  = $new_class;
            $this->vo->_class_id = $class_id;

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
     * @return integer
     */
    public static function maxHealthByLevel($level) {
        return (int) NEW_PLAYER_INITIAL_HEALTH + round(LEVEL_UP_HP_RAISE*($level-1));
    }

    /**
     * Calculate a base str by level
     */
    public static function baseStrengthByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base speed by level
     */
    public static function baseSpeedByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base stamina by level
     */
    public static function baseStaminaByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * The number of kills needed to level up to the next level.
     *
     * 5 more kills in cost for every level you go up.
     * @return int
     */
    public function killsRequiredForNextLevel() {
       return $this->level*5;
    }

    /**
     * Takes in a Character and adds kills to that character.
     * @return int
     */
    public function addKills($amount) {
        return $this->changeKills((int)abs($amount));
    }

    /**
     * Takes in a Character and removes kills from that character.
     * @return int
     */
    public function subtractKills($amount) {
        return $this->changeKills(-1*((int)abs($amount)));
    }

    /**
     * Change the kills amount of a char, and levels them up when necessary.
     * @return int
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
                    ':player_id' => $this->id(),
                ]
            );
        }

        return $this->vo->kills = query_item(
            "SELECT kills FROM players WHERE player_id = :player_id",
            [
                ':player_id' => [$this->id(), PDO::PARAM_INT],
            ]
        );
    }

    /**
     * Leveling up Function
     *
     * @return boolean
     */
    public function levelUp() {
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
                ($this->level + 1 <= MAX_PLAYER_LEVEL) &&
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
                $this->vo->karma = ($this->karma + $karma_to_give);
                $this->vo->level = ($this->level + 1);

                $this->save();

                GameLog::recordLevelUp($this->id());

                $account = AccountFactory::findByChar($this);
                $account->setKarmaTotal($account->getKarmaTotal() + $karma_to_give);
                AccountFactory::save($account);

                // Send a level-up message, for those times when auto-levelling happens.
                send_event($this->id(), $this->id(),
                    "You levelled up! Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by $karma_to_give, and your Ki raised $ki_to_give! You gained some health and turns, as well! You are now a level {$this->level} ninja! Go kill some stuff.");
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return integer|null
     * @note this needs review overall, as nonexistent high int statuses will false positive
     */
    public static function validStatus($dirty) {
        if ((int)$dirty == $dirty) {
            return (int) $dirty;
        } elseif (is_string($dirty)) {
            $status = strtoupper($dirty);

            if (defined($status)) {
                return (int) constant($status);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
