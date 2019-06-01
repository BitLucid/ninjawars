<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\Filter;
use NinjaWars\core\data\PlayerDAO;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Event;
use NinjaWars\core\extensions\SessionFactory;
use \model\Status;
use \PDO;
use \RuntimeException;

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
 * @property int health
 * @property int strength
 * @property int speed
 * @property int stamina
 * @property int energy
 * @property int kills
 * @property int gold
 * @property int level
 * @property int turns
 * @property int bounty
 * @property int days
 * @property int member
 * @property string last_started_attack
 * @property int avatar_type
 * @property int ki
 * @property int karma
 * @property int active
 * @property string identity Identity of the character class
 * @property string class_name
 * @property string goals
 * @property string description
 * @property string messages
 * @property string instincts
 * @property string beliefs
 * @property string traits
 * @property string uname Deprecated in favor of ->name() method
 * @property int status
 */
class Player implements Character {
    const HEALTH_PER_STAMINA = 2;
	public $ip;
	public $avatar_url;
    private $data;
	private $vo;

    /**
     * Creates a new level 1 player object
     */
    public function __construct() {
        $level = 1;

        $this->vo                  = new PlayerVO();
        $this->avatar_url          = null;
        $this->uname               = null;
        $this->health              = self::maxHealthByLevel($level);
        $this->strength            = self::baseStrengthByLevel($level);
        $this->speed               = self::baseSpeedByLevel($level);
        $this->stamina             = self::baseStaminaByLevel($level);
        $this->level               = $level;
        $this->gold                = 100;
        $this->turns               = 180;
        $this->kills               = 0;
        $this->status              = 0;
        $this->member              = 0;
        $this->days                = 0;
        $this->bounty              = 0;
        $this->energy              = 0;
        $this->ki                  = 0;
        $this->karma               = 0;
        $this->avatar_type         = 1;
        $this->messages            = '';
        $this->description         = '';
        $this->instincts           = '';
        $this->traits              = '';
        $this->beliefs             = '';
        $this->goals               = '';
        $this->last_started_attack = null;
    }

    /**
     * @return string
     */
    public function __toString(): string {
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
     * Magic method to provide mutators for properties
     *
     * @return mixed
     */
    public function __set($member_field, $value) {
        return $this->vo->$member_field = $value;
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
     *
     */
    public function __clone() {
        $this->vo = clone $this->vo;
    }

    /**
     * Get the character's name
     */
    public function name(): ?string {
        return $this->vo->uname;
    }

    /**
     * @return int
     */
	public function id(): ?int {
		return $this->vo->player_id;
	}

    /**
     * Adds a defined numeric status constant to the binary string of statuses
     */
    public function addStatus($p_status): void {
        $status = self::validStatus($p_status);

        if ($status > 0 && !$this->hasStatus($status)) {
            if (gettype($this->status | $status) !== 'integer') {
                throw new \InvalidArgumentException('invalid type for status');
            }

            $this->status = ($this->status | $status);
        }
    }

    /**
     * Remove a numeric status from the binary string of status toggles.
     */
    public function subtractStatus($p_status): void {
        $status = self::validStatus($p_status);

        if ($status > 0 && $this->hasStatus($status)) {
            if (gettype($this->status & ~$status) !== 'integer') {
                throw new \InvalidArgumentException('invalid type for status');
            }

            $this->status = ($this->status & ~$status);
        }
    }

    /**
     * Resets the binary status info to 0/none
     */
	public function resetStatus(): void {
		$this->status = 0;
	}

    /**
     * Determine whether a pc is effected by a certain status
     * @param string|int $p_status
     * @return boolean
     */
	public function hasStatus(int $p_status): bool {
        $status = self::validStatus($p_status);

        return ((bool)$status && (bool)($this->status & $status));
    }

    /**
     * Determine whether a pc is effected by a certain status
     * @param string|int $p_status
     * @return boolean
     */
	public function hasTextStatus(string $p_status): bool {
        return (bool) Status::queryStatusEffect($p_status, $this);
    }
    
    /**
     * Add a string status to a character
     * @return int|bool
     */
    public function addTextStatus(string $status_name, int $sec_duration, bool $refresh=false): int {
        return Status::refreshStatusEffect($status_name, $this, $sec_duration, $refresh);
    }

    /**
     * Standard damage output from 1 to max
     * @return int
     */
	public function damage(Character $enemy=null): int {
		return rand(1, $this->maxDamage($enemy));
	}

    /**
     * Max damage capability of a character
     *
     * @return int
     */
	public function maxDamage(Character $enemy=null): int {
        return (int) ($this->getStrength() * 5 + $this->getSpeed());
    }

    /**
     * @return int
     */
	public function getStrength(): int {
        $str = NEW_PLAYER_INITIAL_STATS + (($this->level-1) * LEVEL_UP_STAT_RAISE);
        if($this->hasStatus(STALKING)){
            $str = (int) max(1, floor($str*1.4));
        }
        if ($this->hasStatus(STEALTH)) {
            $str = (int) max(1, floor($str*0.7));
        }
		if ($this->hasStatus(WEAKENED)) {
			return (int) max(1, $str-(ceil($str*.25))); // 75%
		} elseif ($this->hasStatus(STR_UP2)) {
			return (int) ($str+(ceil($str*.50))); // 150%
		} elseif ($this->hasStatus(STR_UP1)) {
			return (int) ($str+(ceil($str*.25))); //125%
		} else {
			return (int) $str;
		}
	}

	public function setStrength(int $str): int {
		if($str < 0){
			throw new \InvalidArgumentException('Strength cannot be set as a negative.');
		}
		return $this->vo->strength = $str;
	}

    /**
     * @return int
     */
	public function getSpeed(): int {
        $speed = NEW_PLAYER_INITIAL_STATS + (($this->level -1) * LEVEL_UP_STAT_RAISE);
        if($this->hasStatus(STALKING)){
            $speed = (int) max(1, floor($speed*0.7));
        }
        if ($this->hasStatus(STEALTH)) {
            $speed = (int) max(1, ceil($speed*1.3));
        }
		if ($this->hasStatus(SLOW)) {
			return (int) ($speed-(ceil($speed*.25)));
		} else {
			return (int) $speed;
		}
	}

	public function setSpeed(int $speed): int{
		if($speed < 0){
			throw new \InvalidArgumentException('Speed cannot be set as a negative.');
		}
		return $this->vo->speed = $speed;
	}

    /**
     * @return int
     */
	public function getStamina(): int {
		$stam = NEW_PLAYER_INITIAL_STATS + (($this->level -1) * LEVEL_UP_STAT_RAISE);
        if($this->hasStatus(STALKING)){
            $stam = (int) max(1, floor($stam*0.9));
        }
        if ($this->hasStatus(STEALTH)) {
            $stam = (int) max(1, ceil($stam*1.3));
        }
		if ($this->hasStatus(POISON)) {
			return (int) ($stam-(ceil($stam*.25)));
		} else {
			return (int) $stam;
		}
	}

	public function setStamina(int $stamina): int {
		if($stamina < 0){
			throw new \InvalidArgumentException('Stamina cannot be set as a negative.');
		}
		return $this->vo->stamina = $stamina;
	}

    /**
     * @return int
     */
	public function setKi($ki): int {
		if($ki < 0){
			throw new \InvalidArgumentException('Ki cannot be negative.');
		}
		return $this->vo->ki = $ki;
	}

    /**
     * @return int
     */
	public function setGold($gold): int {
		if ($gold < 0) {
			throw new \InvalidArgumentException('Gold cannot be made negative.');
		}

		if (is_numeric($gold) && (int) $gold != $gold) {
			throw new \InvalidArgumentException('Gold must be a whole number [not '.(string)$gold.'].');
		}

		return $this->vo->gold = $gold;
	}

    /**
     * @return int
     */
	public function setBounty($bounty): int {
		if($bounty < 0){
			throw new \InvalidArgumentException('Bounty cannot be made negative ['.(string)$bounty.'].');
		}
		if((int) $bounty != $bounty){
			throw new \InvalidArgumentException('Bounty must be a whole number [not '.(string)$bounty.'].');
		}
		return $this->vo->bounty = $bounty;
	}

	/**
	 * Checks whether the character is still active.
     *
     * @return boolean
	 */
	public function isActive(): bool {
		return (bool) $this->vo->active;
	}

    /**
     * @return boolean
     * hardcoded hack at the moment
     * @note To be replaced by an in-database account toggle eventually
     */
	public function isAdmin(): bool {
		$name = strtolower($this->name());
		if ($name == 'tchalvak' || $name == 'beagle' || $name == 'suavisimo') {
			return true;
		}

		return false;
	}

    /**
     * Cleanup player to death state
     *
     * @return void
     * @note
     * This method writes the player object to the database
     */
	public function death(): void {
		$this->resetStatus();
        $this->setHealth(0);
        $this->save();
	}

    /**
     * Changes the turns propety of the player object
     *
     * @param int $turns
     * @return int The number of turns the player object now has
     * @throws InvalidArgumentException $turns cannot be negative
     */
    public function setTurns($turns): int {
        if ($turns < 0) {
            throw new \InvalidArgumentException('Turns cannot be made negative.');
        }

        return $this->vo->turns = $turns;
    }

    /**
     * @deprecated
     */
    public function changeTurns($amount): int {
        return $this->setTurns($this->turns + (int) $amount);
    }

    /**
     * @return integer
     */
    public function getMaxHealth(): int {
        return NEW_PLAYER_INITIAL_HEALTH + ($this->getStamina()*static::HEALTH_PER_STAMINA);
    }

    /**
     * Manipulates the data from the vo into the $this itself
     *
     * @return array
     */
    public function data(): array {
		if (!$this->data) {
            $clan = $this->id() ? $this->getClan() : null;
            $this->data = (array) $this->vo;
            $this->data['next_level']    = $this->killsRequiredForNextLevel();
            $this->data['max_health']    = $this->getMaxHealth();
            $this->data['hp_percent']    = $this->health_percent();
            $this->data['strength']      = $this->getStrength();
            $this->data['speed']         = $this->getSpeed();
            $this->data['stamina']       = $this->getStamina();
            $this->data['max_turns']     = 100;
            $this->data['turns_percent'] = min(100, round($this->data['turns']/$this->data['max_turns']*100));
            $this->data['exp_percent']   = min(100, round(($this->data['kills']/$this->data['next_level'])*100));
            $this->data['status_list']   = implode(', ', self::getStatusList($this->id()));
            $this->data['hash']          = md5(implode($this->data));
            $this->data['class_name']    = ucfirst($this->data['identity']); // A misnomer, identity is actually the class label
            $this->data['clan_id']       = ($clan ? $clan->id : null);

            unset($this->data['pname']);
        }

        return $this->data;
    }

    /**
     * Return the data that should be publicly readable to javascript or the api while the player is logged in.
     *
     * @return array
     */
    public function publicData(): array {
        $char_info = $this->data();
        unset($char_info['ip'], $char_info['member'], $char_info['pname'], $char_info['verification_number'], $char_info['confirmed']);

        return $char_info;
    }

    /**
     * @return Clan
     */
    public function getClan(): ?Clan {
        return Clan::findByMember($this);
    }

	/**
	 * Heal the char with in the limits of their max
     *
     * @return int
	 */
	public function heal($amount): int {
		// do not heal above max health
        $heal = min($this->is_hurt_by(), $amount);
        return $this->setHealth($this->health + $heal);
	}

	/**
	 * Do some damage to the character
     *
     * @param int $damage
     * @return int
	 */
	public function harm($damage): int {
		// Do not allow negative health
		$actual_damage = min($this->health, (int) $damage);
		return $this->setHealth($this->health - $actual_damage);
	}

    /**
     * @return int
     */
	public function getHealth(): int {
        return $this->health;
	}

    /**
     * @return int
     */
	public function setHealth($health): int {
		if ($health < 0) {
			throw new \InvalidArgumentException('Health cannot be made negative.');
		}

		if ((int) $health != $health) {
			throw new \InvalidArgumentException('Health must be a whole number.');
		}

		return $this->vo->health = (int) max(0, $health);
	}

	/**
	 * Return the amount below the max health (or zero).
	 * @return int
	 */
	public function is_hurt_by(): int {
		return max(0,
			(int) ($this->getMaxHealth() - $this->health)
		);
	}

    /**
     * Return the current percentage of the maximum health that a character could have.
     * @return int
     */
	public function health_percent(): int {
        return min(100, round(($this->health/$this->getMaxHealth())*100));
	}

    /**
     * @return int difficulty rating
     */
	public function difficulty(): int{
		return (int) ( 10 + $this->getStrength() * 2 + $this->maxDamage());
	}

    /**
     * @return int random private number unique to character
     */
	public function getVerificationNumber(): int{
		return $this->vo->verification_number;
	}

    /**
     * @return string url for the gravatar of pc
     */
    public function avatarUrl(): string {
        if (!isset($this->avatar_url) || $this->avatar_url === null) {
            $this->avatar_url = $this->generateGravatarUrl();
        }

        return $this->avatar_url;
    }

    /**
     * Generate a hash from email and pass that for a gravatar
     */
    private function generateGravatarUrl(): string {
        $account = Account::findByChar($this);

        if (OFFLINE) {
            return IMAGE_ROOT.'default_avatar.png';
        } else if (!$this->vo || !$this->vo->avatar_type || !$account || !$account->email()) {
            return '';
        } else {
            $email       = $account->email();

            $def         = 'monsterid'; // Default image or image class.
            // other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
            $base        = "https://www.gravatar.com/avatar/";
            $hash        = md5(trim(strtolower($email)));
            $no_gravatar = "d=".urlencode($def);
            $size        = 80;
            $rating      = "r=x";
            $res         = $base.$hash."?".implode('&', [$no_gravatar, $size, $rating]);

            return $res;
        }
	}

	/**
	 * Persist object to database
     *
	 * @return Player
	 */
	public function save(): Player {
		$factory = new PlayerDAO();
		$factory->save($this->vo);

		return $this;
	}

     /**
     * Check whether the player is the leader of their clan.
     * @return boolean
     */
    public function isClanLeader(): bool {
        return (($clan = Clan::findByMember($this)) && $this->id() == $clan->getLeaderID());
    }


    /**
     * Get the information for a single class' data, generally the characters
     * @param string $class_identity
     * @return array of class data
     */
    private function obtainSingleClassData(string $class_identity): array{
            return query_row(
                'select class_id, identity, class_name, theme, class_note, class_tier, class_desc, class_icon from class where class.identity = :class',
                [':class' => $class_identity]
            );
    }

    /**
     * Set the character's class, using the identity.
     * @return string|null error string if fails
     */
    public function setClass(string $new_class): ?string {
        $class_data = $this->obtainSingleClassData(strtolower($new_class));
        if($class_data === false || $class_data === null){
            return "That class was not an option to change into.";
        } else {
            // Update the only place in the database where a players class is determined
            $up = "UPDATE players SET _class_id = :class_id WHERE player_id = :char_id";
            query($up, [
                ':class_id' => $class_data['class_id'],
                ':char_id'  => $this->id(),
            ]);

            $this->class_name    = $class_data['class_name'];
            $this->theme         = $class_data['theme'];
            $this->vo->identity  = $class_data['identity'];
            $this->vo->_class_id = $class_data['class_id'];

            return null;
        }
    }

    /**
     * Get the ninja's class's name.
     * @return string
     */
    public function getClassName(): string {
        return $this->vo->class_name;
    }

    /**
     * The number of kills needed to level up to the next level.
     *
     * 5 more kills in cost for every level you go up.
     * @return int
     */
    public function killsRequiredForNextLevel(): int {
       return $this->level*5;
    }

    /**
     * Takes in a Character and adds kills to that character.
     * @return int
     */
    public function addKills(int $amount): int {
        return $this->changeKills((int)abs($amount));
    }

    /**
     * Takes in a Character and removes kills from that character.
     * @return int
     */
    public function subtractKills(int $amount): int {
        return $this->changeKills(-1*((int)abs($amount)));
    }

    /**
     * Change the kills amount of a char, and levels them up when necessary.
     * @return int
     */
    private function changeKills($amount): int {
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
    public function levelUp(): bool {
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
                $this->setHealth($this->health + $health_to_add);
                $this->setTurns($this->turns   + $turns_to_give);
                $this->setKi($this->ki         + $ki_to_give);

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

                $account = Account::findByChar($this);
                $account->setKarmaTotal($account->getKarmaTotal() + $karma_to_give);
                $account->save();

                // Send a level-up message, for those times when auto-levelling happens.
                Event::create($this->id(), $this->id(),
                    "You levelled up! Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by $karma_to_give, and your Ki raised $ki_to_give! You gained some health and turns, as well! You are now a level {$this->level} ninja! Go kill some stuff.");
                return true;
            } else {
                return false;
            }
        }
    }

	/**
	 * Find a player by primary key
     * @param int|string|null $id
	 * @return Player|null
	 */
	public static function find($id): ?Player {
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
     */
    public static function findByName(string $name): ?Player{
        $id = query_item('select player_id from players where lower(uname) = lower(:name) limit 1', [':name'=>$name]);
        return self::find($id);
    }

    /**
     * Find a char by playable for account
     * @param int|null $account_id
     * @return Player|null
     */
    public static function findPlayable(?int $account_id): ?Player{
        // Two db calls for now
        $pid = query_item('select player_id from players p 
            join account_players ap on p.player_id = ap._player_id
            join accounts a on a.account_id = ap._account_id
            where account_id = :aid
            order by p.created_date asc, a.last_login desc
            limit 1', [':aid'=>[$account_id, PDO::PARAM_INT]]);
        return self::find($pid);
    }

    /**
     * query the recently active players
     * @return array Array of data not of player objects
     */
    public static function findActive(int $limit=5, bool $alive_only=true): array {
        $where_cond = ($alive_only ? ' AND health > 0' : '');
        $sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
        $active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
        return $active_ninjas;
    }

    /**
     * @return integer|null
     * @note this needs review overall, as nonexistent high int statuses will false positive
     */
    public static function validStatus($dirty): ?int {
        if (is_numeric($dirty) && (int)$dirty == $dirty) {
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

    /**
     * Get the different statuses a character is affected by.
     *
     * @param int|null $target the target id, username if self targetting.
     * @return string[]
     * @todo Refactor this so that it doesn't show own status by default, as that is error prone
     *
     */
    public static function getStatusList(?int $target=null): array {
        $states = array();
        $target = (isset($target) && (int)$target == $target ? $target : SessionFactory::getSession()->get('player_id'));

        // Default to showing own status.
        $target = self::find($target);

        if (!$target || $target->health < 1) {
            $states[] = 'Dead';
        } else { // *** Other statuses only display if not dead.
            if ($target->health < 80) {
                $states[] = 'Injured';
            } else {
                $states[] = 'Healthy';
            }

            // The visibly viewable statuses.
            if ($target->hasStatus(STEALTH)) { $states[] = 'Stealthed'; }
            if ($target->hasStatus(POISON)) { $states[] = 'Poisoned'; }
            if ($target->hasStatus(WEAKENED)) { $states[] = 'Weakened'; }
            if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
            if ($target->hasStatus(STR_UP1)) { $states[] = 'Buff'; }
            if ($target->hasStatus(STR_UP2)) { $states[] = 'Strength+'; }

            // If any of the shield skills are up, show a single status state for any.
            if ($target->hasStatus(FIRE_RESISTING) || $target->hasStatus(INSULATED) || $target->hasStatus(GROUNDED)
                || $target->hasStatus(BLESSED) || $target->hasStatus(IMMUNIZED)
                || $target->hasStatus(ACID_RESISTING)) {
                $states[] = 'Shielded';
            }
        }

        return $states;
    }

    /**
     * Calculate a max health by a level, this is actually only the base maximum
     * since changes in stamina can change the current player's maximum
     *
     * @return integer The health points
     */
    public static function maxHealthByLevel(int $level): int {
        return (int) NEW_PLAYER_INITIAL_HEALTH + (int) (self::baseStaminaByLevel($level) * self::HEALTH_PER_STAMINA);
    }

    /**
     * Calculate a base str by level
     *
     * @return integer strength
     */
    public static function baseStrengthByLevel(int $level): int {
        return (int) NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base speed by level
     *
     * @return integer speed
     */
    public static function baseSpeedByLevel(int $level): int {
        return (int) NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base stamina by level
     *
     * @return integer speed
     */
    public static function baseStaminaByLevel(int $level): int {
        return (int) NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }
}
