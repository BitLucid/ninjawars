<?php

namespace deploy\model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use deploy\model\AccountPlayers;
use deploy\model\AccountPlayersQuery;
use deploy\model\ClanPlayer;
use deploy\model\ClanPlayerQuery;
use deploy\model\Class;
use deploy\model\ClassQuery;
use deploy\model\Enemies;
use deploy\model\EnemiesQuery;
use deploy\model\Inventory;
use deploy\model\InventoryQuery;
use deploy\model\LevellingLog;
use deploy\model\LevellingLogQuery;
use deploy\model\Messages;
use deploy\model\MessagesQuery;
use deploy\model\Players;
use deploy\model\PlayersPeer;
use deploy\model\PlayersQuery;

/**
 * Base class that represents a row from the 'players' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePlayers extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\PlayersPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PlayersPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the player_id field.
     * @var        int
     */
    protected $player_id;

    /**
     * The value for the uname field.
     * @var        string
     */
    protected $uname;

    /**
     * The value for the pname_backup field.
     * @var        string
     */
    protected $pname_backup;

    /**
     * The value for the health field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $health;

    /**
     * The value for the strength field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $strength;

    /**
     * The value for the gold field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $gold;

    /**
     * The value for the messages field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $messages;

    /**
     * The value for the kills field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $kills;

    /**
     * The value for the turns field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $turns;

    /**
     * The value for the verification_number field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $verification_number;

    /**
     * The value for the active field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $active;

    /**
     * The value for the email field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $email;

    /**
     * The value for the level field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $level;

    /**
     * The value for the status field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $status;

    /**
     * The value for the member field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $member;

    /**
     * The value for the days field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $days;

    /**
     * The value for the ip field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $ip;

    /**
     * The value for the bounty field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $bounty;

    /**
     * The value for the created_date field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $created_date;

    /**
     * The value for the resurrection_time field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $resurrection_time;

    /**
     * The value for the last_started_attack field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $last_started_attack;

    /**
     * The value for the energy field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $energy;

    /**
     * The value for the avatar_type field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $avatar_type;

    /**
     * The value for the _class_id field.
     * @var        int
     */
    protected $_class_id;

    /**
     * The value for the ki field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $ki;

    /**
     * The value for the stamina field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $stamina;

    /**
     * The value for the speed field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $speed;

    /**
     * The value for the karma field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $karma;

    /**
     * The value for the kills_gained field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $kills_gained;

    /**
     * The value for the kills_used field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $kills_used;

    /**
     * @var        Class
     */
    protected $aClass;

    /**
     * @var        PropelObjectCollection|AccountPlayers[] Collection to store aggregation of AccountPlayers objects.
     */
    protected $collAccountPlayerss;
    protected $collAccountPlayerssPartial;

    /**
     * @var        PropelObjectCollection|ClanPlayer[] Collection to store aggregation of ClanPlayer objects.
     */
    protected $collClanPlayers;
    protected $collClanPlayersPartial;

    /**
     * @var        PropelObjectCollection|Enemies[] Collection to store aggregation of Enemies objects.
     */
    protected $collEnemiessRelatedByEnemyId;
    protected $collEnemiessRelatedByEnemyIdPartial;

    /**
     * @var        PropelObjectCollection|Enemies[] Collection to store aggregation of Enemies objects.
     */
    protected $collEnemiessRelatedByPlayerId;
    protected $collEnemiessRelatedByPlayerIdPartial;

    /**
     * @var        PropelObjectCollection|Inventory[] Collection to store aggregation of Inventory objects.
     */
    protected $collInventorys;
    protected $collInventorysPartial;

    /**
     * @var        PropelObjectCollection|LevellingLog[] Collection to store aggregation of LevellingLog objects.
     */
    protected $collLevellingLogs;
    protected $collLevellingLogsPartial;

    /**
     * @var        PropelObjectCollection|Messages[] Collection to store aggregation of Messages objects.
     */
    protected $collMessagessRelatedBySendFrom;
    protected $collMessagessRelatedBySendFromPartial;

    /**
     * @var        PropelObjectCollection|Messages[] Collection to store aggregation of Messages objects.
     */
    protected $collMessagessRelatedBySendTo;
    protected $collMessagessRelatedBySendToPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $accountPlayerssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $clanPlayersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $enemiessRelatedByEnemyIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $enemiessRelatedByPlayerIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $inventorysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $levellingLogsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $messagessRelatedBySendFromScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $messagessRelatedBySendToScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->health = 0;
        $this->strength = 0;
        $this->gold = 0;
        $this->messages = '';
        $this->kills = 0;
        $this->turns = 0;
        $this->verification_number = 0;
        $this->active = 0;
        $this->email = '';
        $this->level = 0;
        $this->status = 0;
        $this->member = 0;
        $this->days = 0;
        $this->ip = '';
        $this->bounty = 0;
        $this->resurrection_time = 0;
        $this->energy = 0;
        $this->avatar_type = 1;
        $this->ki = 0;
        $this->stamina = 0;
        $this->speed = 0;
        $this->karma = 0;
        $this->kills_gained = 0;
        $this->kills_used = 0;
    }

    /**
     * Initializes internal state of BasePlayers object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [player_id] column value.
     *
     * @return int
     */
    public function getPlayerId()
    {

        return $this->player_id;
    }

    /**
     * Get the [uname] column value.
     *
     * @return string
     */
    public function getUname()
    {

        return $this->uname;
    }

    /**
     * Get the [pname_backup] column value.
     *
     * @return string
     */
    public function getPnameBackup()
    {

        return $this->pname_backup;
    }

    /**
     * Get the [health] column value.
     *
     * @return int
     */
    public function getHealth()
    {

        return $this->health;
    }

    /**
     * Get the [strength] column value.
     *
     * @return int
     */
    public function getStrength()
    {

        return $this->strength;
    }

    /**
     * Get the [gold] column value.
     *
     * @return int
     */
    public function getGold()
    {

        return $this->gold;
    }

    /**
     * Get the [messages] column value.
     *
     * @return string
     */
    public function getMessages()
    {

        return $this->messages;
    }

    /**
     * Get the [kills] column value.
     *
     * @return int
     */
    public function getKills()
    {

        return $this->kills;
    }

    /**
     * Get the [turns] column value.
     *
     * @return int
     */
    public function getTurns()
    {

        return $this->turns;
    }

    /**
     * Get the [verification_number] column value.
     *
     * @return int
     */
    public function getVerificationNumber()
    {

        return $this->verification_number;
    }

    /**
     * Get the [active] column value.
     *
     * @return int
     */
    public function getActive()
    {

        return $this->active;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [level] column value.
     *
     * @return int
     */
    public function getLevel()
    {

        return $this->level;
    }

    /**
     * Get the [status] column value.
     *
     * @return int
     */
    public function getStatus()
    {

        return $this->status;
    }

    /**
     * Get the [member] column value.
     *
     * @return int
     */
    public function getMember()
    {

        return $this->member;
    }

    /**
     * Get the [days] column value.
     *
     * @return int
     */
    public function getDays()
    {

        return $this->days;
    }

    /**
     * Get the [ip] column value.
     *
     * @return string
     */
    public function getIp()
    {

        return $this->ip;
    }

    /**
     * Get the [bounty] column value.
     *
     * @return int
     */
    public function getBounty()
    {

        return $this->bounty;
    }

    /**
     * Get the [optionally formatted] temporal [created_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedDate($format = 'Y-m-d H:i:s')
    {
        if ($this->created_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->created_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [resurrection_time] column value.
     *
     * @return int
     */
    public function getResurrectionTime()
    {

        return $this->resurrection_time;
    }

    /**
     * Get the [optionally formatted] temporal [last_started_attack] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastStartedAttack($format = 'Y-m-d H:i:s')
    {
        if ($this->last_started_attack === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_started_attack);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_started_attack, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [energy] column value.
     *
     * @return int
     */
    public function getEnergy()
    {

        return $this->energy;
    }

    /**
     * Get the [avatar_type] column value.
     *
     * @return int
     */
    public function getAvatarType()
    {

        return $this->avatar_type;
    }

    /**
     * Get the [_class_id] column value.
     *
     * @return int
     */
    public function getClassId()
    {

        return $this->_class_id;
    }

    /**
     * Get the [ki] column value.
     *
     * @return int
     */
    public function getKi()
    {

        return $this->ki;
    }

    /**
     * Get the [stamina] column value.
     *
     * @return int
     */
    public function getStamina()
    {

        return $this->stamina;
    }

    /**
     * Get the [speed] column value.
     *
     * @return int
     */
    public function getSpeed()
    {

        return $this->speed;
    }

    /**
     * Get the [karma] column value.
     *
     * @return int
     */
    public function getKarma()
    {

        return $this->karma;
    }

    /**
     * Get the [kills_gained] column value.
     *
     * @return int
     */
    public function getKillsGained()
    {

        return $this->kills_gained;
    }

    /**
     * Get the [kills_used] column value.
     *
     * @return int
     */
    public function getKillsUsed()
    {

        return $this->kills_used;
    }

    /**
     * Set the value of [player_id] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setPlayerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->player_id !== $v) {
            $this->player_id = $v;
            $this->modifiedColumns[] = PlayersPeer::PLAYER_ID;
        }


        return $this;
    } // setPlayerId()

    /**
     * Set the value of [uname] column.
     *
     * @param  string $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setUname($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->uname !== $v) {
            $this->uname = $v;
            $this->modifiedColumns[] = PlayersPeer::UNAME;
        }


        return $this;
    } // setUname()

    /**
     * Set the value of [pname_backup] column.
     *
     * @param  string $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setPnameBackup($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->pname_backup !== $v) {
            $this->pname_backup = $v;
            $this->modifiedColumns[] = PlayersPeer::PNAME_BACKUP;
        }


        return $this;
    } // setPnameBackup()

    /**
     * Set the value of [health] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setHealth($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->health !== $v) {
            $this->health = $v;
            $this->modifiedColumns[] = PlayersPeer::HEALTH;
        }


        return $this;
    } // setHealth()

    /**
     * Set the value of [strength] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setStrength($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->strength !== $v) {
            $this->strength = $v;
            $this->modifiedColumns[] = PlayersPeer::STRENGTH;
        }


        return $this;
    } // setStrength()

    /**
     * Set the value of [gold] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setGold($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->gold !== $v) {
            $this->gold = $v;
            $this->modifiedColumns[] = PlayersPeer::GOLD;
        }


        return $this;
    } // setGold()

    /**
     * Set the value of [messages] column.
     *
     * @param  string $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setMessages($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->messages !== $v) {
            $this->messages = $v;
            $this->modifiedColumns[] = PlayersPeer::MESSAGES;
        }


        return $this;
    } // setMessages()

    /**
     * Set the value of [kills] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setKills($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->kills !== $v) {
            $this->kills = $v;
            $this->modifiedColumns[] = PlayersPeer::KILLS;
        }


        return $this;
    } // setKills()

    /**
     * Set the value of [turns] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setTurns($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->turns !== $v) {
            $this->turns = $v;
            $this->modifiedColumns[] = PlayersPeer::TURNS;
        }


        return $this;
    } // setTurns()

    /**
     * Set the value of [verification_number] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setVerificationNumber($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->verification_number !== $v) {
            $this->verification_number = $v;
            $this->modifiedColumns[] = PlayersPeer::VERIFICATION_NUMBER;
        }


        return $this;
    } // setVerificationNumber()

    /**
     * Set the value of [active] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setActive($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->active !== $v) {
            $this->active = $v;
            $this->modifiedColumns[] = PlayersPeer::ACTIVE;
        }


        return $this;
    } // setActive()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = PlayersPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [level] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setLevel($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->level !== $v) {
            $this->level = $v;
            $this->modifiedColumns[] = PlayersPeer::LEVEL;
        }


        return $this;
    } // setLevel()

    /**
     * Set the value of [status] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setStatus($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->status !== $v) {
            $this->status = $v;
            $this->modifiedColumns[] = PlayersPeer::STATUS;
        }


        return $this;
    } // setStatus()

    /**
     * Set the value of [member] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setMember($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->member !== $v) {
            $this->member = $v;
            $this->modifiedColumns[] = PlayersPeer::MEMBER;
        }


        return $this;
    } // setMember()

    /**
     * Set the value of [days] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setDays($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->days !== $v) {
            $this->days = $v;
            $this->modifiedColumns[] = PlayersPeer::DAYS;
        }


        return $this;
    } // setDays()

    /**
     * Set the value of [ip] column.
     *
     * @param  string $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setIp($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->ip !== $v) {
            $this->ip = $v;
            $this->modifiedColumns[] = PlayersPeer::IP;
        }


        return $this;
    } // setIp()

    /**
     * Set the value of [bounty] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setBounty($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->bounty !== $v) {
            $this->bounty = $v;
            $this->modifiedColumns[] = PlayersPeer::BOUNTY;
        }


        return $this;
    } // setBounty()

    /**
     * Sets the value of [created_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Players The current object (for fluent API support)
     */
    public function setCreatedDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_date !== null || $dt !== null) {
            $currentDateAsString = ($this->created_date !== null && $tmpDt = new DateTime($this->created_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_date = $newDateAsString;
                $this->modifiedColumns[] = PlayersPeer::CREATED_DATE;
            }
        } // if either are not null


        return $this;
    } // setCreatedDate()

    /**
     * Set the value of [resurrection_time] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setResurrectionTime($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->resurrection_time !== $v) {
            $this->resurrection_time = $v;
            $this->modifiedColumns[] = PlayersPeer::RESURRECTION_TIME;
        }


        return $this;
    } // setResurrectionTime()

    /**
     * Sets the value of [last_started_attack] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Players The current object (for fluent API support)
     */
    public function setLastStartedAttack($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_started_attack !== null || $dt !== null) {
            $currentDateAsString = ($this->last_started_attack !== null && $tmpDt = new DateTime($this->last_started_attack)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_started_attack = $newDateAsString;
                $this->modifiedColumns[] = PlayersPeer::LAST_STARTED_ATTACK;
            }
        } // if either are not null


        return $this;
    } // setLastStartedAttack()

    /**
     * Set the value of [energy] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setEnergy($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->energy !== $v) {
            $this->energy = $v;
            $this->modifiedColumns[] = PlayersPeer::ENERGY;
        }


        return $this;
    } // setEnergy()

    /**
     * Set the value of [avatar_type] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setAvatarType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->avatar_type !== $v) {
            $this->avatar_type = $v;
            $this->modifiedColumns[] = PlayersPeer::AVATAR_TYPE;
        }


        return $this;
    } // setAvatarType()

    /**
     * Set the value of [_class_id] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setClassId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->_class_id !== $v) {
            $this->_class_id = $v;
            $this->modifiedColumns[] = PlayersPeer::_CLASS_ID;
        }

        if ($this->aClass !== null && $this->aClass->getClassId() !== $v) {
            $this->aClass = null;
        }


        return $this;
    } // setClassId()

    /**
     * Set the value of [ki] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setKi($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->ki !== $v) {
            $this->ki = $v;
            $this->modifiedColumns[] = PlayersPeer::KI;
        }


        return $this;
    } // setKi()

    /**
     * Set the value of [stamina] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setStamina($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->stamina !== $v) {
            $this->stamina = $v;
            $this->modifiedColumns[] = PlayersPeer::STAMINA;
        }


        return $this;
    } // setStamina()

    /**
     * Set the value of [speed] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setSpeed($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->speed !== $v) {
            $this->speed = $v;
            $this->modifiedColumns[] = PlayersPeer::SPEED;
        }


        return $this;
    } // setSpeed()

    /**
     * Set the value of [karma] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setKarma($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->karma !== $v) {
            $this->karma = $v;
            $this->modifiedColumns[] = PlayersPeer::KARMA;
        }


        return $this;
    } // setKarma()

    /**
     * Set the value of [kills_gained] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setKillsGained($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->kills_gained !== $v) {
            $this->kills_gained = $v;
            $this->modifiedColumns[] = PlayersPeer::KILLS_GAINED;
        }


        return $this;
    } // setKillsGained()

    /**
     * Set the value of [kills_used] column.
     *
     * @param  int $v new value
     * @return Players The current object (for fluent API support)
     */
    public function setKillsUsed($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->kills_used !== $v) {
            $this->kills_used = $v;
            $this->modifiedColumns[] = PlayersPeer::KILLS_USED;
        }


        return $this;
    } // setKillsUsed()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->health !== 0) {
                return false;
            }

            if ($this->strength !== 0) {
                return false;
            }

            if ($this->gold !== 0) {
                return false;
            }

            if ($this->messages !== '') {
                return false;
            }

            if ($this->kills !== 0) {
                return false;
            }

            if ($this->turns !== 0) {
                return false;
            }

            if ($this->verification_number !== 0) {
                return false;
            }

            if ($this->active !== 0) {
                return false;
            }

            if ($this->email !== '') {
                return false;
            }

            if ($this->level !== 0) {
                return false;
            }

            if ($this->status !== 0) {
                return false;
            }

            if ($this->member !== 0) {
                return false;
            }

            if ($this->days !== 0) {
                return false;
            }

            if ($this->ip !== '') {
                return false;
            }

            if ($this->bounty !== 0) {
                return false;
            }

            if ($this->resurrection_time !== 0) {
                return false;
            }

            if ($this->energy !== 0) {
                return false;
            }

            if ($this->avatar_type !== 1) {
                return false;
            }

            if ($this->ki !== 0) {
                return false;
            }

            if ($this->stamina !== 0) {
                return false;
            }

            if ($this->speed !== 0) {
                return false;
            }

            if ($this->karma !== 0) {
                return false;
            }

            if ($this->kills_gained !== 0) {
                return false;
            }

            if ($this->kills_used !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->player_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->uname = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->pname_backup = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->health = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->strength = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->gold = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->messages = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->kills = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->turns = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->verification_number = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->active = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->email = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->level = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->status = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->member = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->days = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
            $this->ip = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
            $this->bounty = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
            $this->created_date = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->resurrection_time = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
            $this->last_started_attack = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
            $this->energy = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
            $this->avatar_type = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
            $this->_class_id = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
            $this->ki = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
            $this->stamina = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
            $this->speed = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
            $this->karma = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
            $this->kills_gained = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
            $this->kills_used = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 30; // 30 = PlayersPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Players object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aClass !== null && $this->_class_id !== $this->aClass->getClassId()) {
            $this->aClass = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PlayersPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aClass = null;
            $this->collAccountPlayerss = null;

            $this->collClanPlayers = null;

            $this->collEnemiessRelatedByEnemyId = null;

            $this->collEnemiessRelatedByPlayerId = null;

            $this->collInventorys = null;

            $this->collLevellingLogs = null;

            $this->collMessagessRelatedBySendFrom = null;

            $this->collMessagessRelatedBySendTo = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PlayersQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                PlayersPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aClass !== null) {
                if ($this->aClass->isModified() || $this->aClass->isNew()) {
                    $affectedRows += $this->aClass->save($con);
                }
                $this->setClass($this->aClass);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->accountPlayerssScheduledForDeletion !== null) {
                if (!$this->accountPlayerssScheduledForDeletion->isEmpty()) {
                    AccountPlayersQuery::create()
                        ->filterByPrimaryKeys($this->accountPlayerssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountPlayerssScheduledForDeletion = null;
                }
            }

            if ($this->collAccountPlayerss !== null) {
                foreach ($this->collAccountPlayerss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->clanPlayersScheduledForDeletion !== null) {
                if (!$this->clanPlayersScheduledForDeletion->isEmpty()) {
                    ClanPlayerQuery::create()
                        ->filterByPrimaryKeys($this->clanPlayersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->clanPlayersScheduledForDeletion = null;
                }
            }

            if ($this->collClanPlayers !== null) {
                foreach ($this->collClanPlayers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->enemiessRelatedByEnemyIdScheduledForDeletion !== null) {
                if (!$this->enemiessRelatedByEnemyIdScheduledForDeletion->isEmpty()) {
                    EnemiesQuery::create()
                        ->filterByPrimaryKeys($this->enemiessRelatedByEnemyIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->enemiessRelatedByEnemyIdScheduledForDeletion = null;
                }
            }

            if ($this->collEnemiessRelatedByEnemyId !== null) {
                foreach ($this->collEnemiessRelatedByEnemyId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->enemiessRelatedByPlayerIdScheduledForDeletion !== null) {
                if (!$this->enemiessRelatedByPlayerIdScheduledForDeletion->isEmpty()) {
                    EnemiesQuery::create()
                        ->filterByPrimaryKeys($this->enemiessRelatedByPlayerIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->enemiessRelatedByPlayerIdScheduledForDeletion = null;
                }
            }

            if ($this->collEnemiessRelatedByPlayerId !== null) {
                foreach ($this->collEnemiessRelatedByPlayerId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->inventorysScheduledForDeletion !== null) {
                if (!$this->inventorysScheduledForDeletion->isEmpty()) {
                    InventoryQuery::create()
                        ->filterByPrimaryKeys($this->inventorysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->inventorysScheduledForDeletion = null;
                }
            }

            if ($this->collInventorys !== null) {
                foreach ($this->collInventorys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->levellingLogsScheduledForDeletion !== null) {
                if (!$this->levellingLogsScheduledForDeletion->isEmpty()) {
                    LevellingLogQuery::create()
                        ->filterByPrimaryKeys($this->levellingLogsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->levellingLogsScheduledForDeletion = null;
                }
            }

            if ($this->collLevellingLogs !== null) {
                foreach ($this->collLevellingLogs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->messagessRelatedBySendFromScheduledForDeletion !== null) {
                if (!$this->messagessRelatedBySendFromScheduledForDeletion->isEmpty()) {
                    MessagesQuery::create()
                        ->filterByPrimaryKeys($this->messagessRelatedBySendFromScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->messagessRelatedBySendFromScheduledForDeletion = null;
                }
            }

            if ($this->collMessagessRelatedBySendFrom !== null) {
                foreach ($this->collMessagessRelatedBySendFrom as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->messagessRelatedBySendToScheduledForDeletion !== null) {
                if (!$this->messagessRelatedBySendToScheduledForDeletion->isEmpty()) {
                    MessagesQuery::create()
                        ->filterByPrimaryKeys($this->messagessRelatedBySendToScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->messagessRelatedBySendToScheduledForDeletion = null;
                }
            }

            if ($this->collMessagessRelatedBySendTo !== null) {
                foreach ($this->collMessagessRelatedBySendTo as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = PlayersPeer::PLAYER_ID;
        if (null !== $this->player_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PlayersPeer::PLAYER_ID . ')');
        }
        if (null === $this->player_id) {
            try {
                $stmt = $con->query("SELECT nextval('players_player_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->player_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PlayersPeer::PLAYER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"player_id"';
        }
        if ($this->isColumnModified(PlayersPeer::UNAME)) {
            $modifiedColumns[':p' . $index++]  = '"uname"';
        }
        if ($this->isColumnModified(PlayersPeer::PNAME_BACKUP)) {
            $modifiedColumns[':p' . $index++]  = '"pname_backup"';
        }
        if ($this->isColumnModified(PlayersPeer::HEALTH)) {
            $modifiedColumns[':p' . $index++]  = '"health"';
        }
        if ($this->isColumnModified(PlayersPeer::STRENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"strength"';
        }
        if ($this->isColumnModified(PlayersPeer::GOLD)) {
            $modifiedColumns[':p' . $index++]  = '"gold"';
        }
        if ($this->isColumnModified(PlayersPeer::MESSAGES)) {
            $modifiedColumns[':p' . $index++]  = '"messages"';
        }
        if ($this->isColumnModified(PlayersPeer::KILLS)) {
            $modifiedColumns[':p' . $index++]  = '"kills"';
        }
        if ($this->isColumnModified(PlayersPeer::TURNS)) {
            $modifiedColumns[':p' . $index++]  = '"turns"';
        }
        if ($this->isColumnModified(PlayersPeer::VERIFICATION_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"verification_number"';
        }
        if ($this->isColumnModified(PlayersPeer::ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '"active"';
        }
        if ($this->isColumnModified(PlayersPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '"email"';
        }
        if ($this->isColumnModified(PlayersPeer::LEVEL)) {
            $modifiedColumns[':p' . $index++]  = '"level"';
        }
        if ($this->isColumnModified(PlayersPeer::STATUS)) {
            $modifiedColumns[':p' . $index++]  = '"status"';
        }
        if ($this->isColumnModified(PlayersPeer::MEMBER)) {
            $modifiedColumns[':p' . $index++]  = '"member"';
        }
        if ($this->isColumnModified(PlayersPeer::DAYS)) {
            $modifiedColumns[':p' . $index++]  = '"days"';
        }
        if ($this->isColumnModified(PlayersPeer::IP)) {
            $modifiedColumns[':p' . $index++]  = '"ip"';
        }
        if ($this->isColumnModified(PlayersPeer::BOUNTY)) {
            $modifiedColumns[':p' . $index++]  = '"bounty"';
        }
        if ($this->isColumnModified(PlayersPeer::CREATED_DATE)) {
            $modifiedColumns[':p' . $index++]  = '"created_date"';
        }
        if ($this->isColumnModified(PlayersPeer::RESURRECTION_TIME)) {
            $modifiedColumns[':p' . $index++]  = '"resurrection_time"';
        }
        if ($this->isColumnModified(PlayersPeer::LAST_STARTED_ATTACK)) {
            $modifiedColumns[':p' . $index++]  = '"last_started_attack"';
        }
        if ($this->isColumnModified(PlayersPeer::ENERGY)) {
            $modifiedColumns[':p' . $index++]  = '"energy"';
        }
        if ($this->isColumnModified(PlayersPeer::AVATAR_TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"avatar_type"';
        }
        if ($this->isColumnModified(PlayersPeer::_CLASS_ID)) {
            $modifiedColumns[':p' . $index++]  = '"_class_id"';
        }
        if ($this->isColumnModified(PlayersPeer::KI)) {
            $modifiedColumns[':p' . $index++]  = '"ki"';
        }
        if ($this->isColumnModified(PlayersPeer::STAMINA)) {
            $modifiedColumns[':p' . $index++]  = '"stamina"';
        }
        if ($this->isColumnModified(PlayersPeer::SPEED)) {
            $modifiedColumns[':p' . $index++]  = '"speed"';
        }
        if ($this->isColumnModified(PlayersPeer::KARMA)) {
            $modifiedColumns[':p' . $index++]  = '"karma"';
        }
        if ($this->isColumnModified(PlayersPeer::KILLS_GAINED)) {
            $modifiedColumns[':p' . $index++]  = '"kills_gained"';
        }
        if ($this->isColumnModified(PlayersPeer::KILLS_USED)) {
            $modifiedColumns[':p' . $index++]  = '"kills_used"';
        }

        $sql = sprintf(
            'INSERT INTO "players" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"player_id"':
                        $stmt->bindValue($identifier, $this->player_id, PDO::PARAM_INT);
                        break;
                    case '"uname"':
                        $stmt->bindValue($identifier, $this->uname, PDO::PARAM_STR);
                        break;
                    case '"pname_backup"':
                        $stmt->bindValue($identifier, $this->pname_backup, PDO::PARAM_STR);
                        break;
                    case '"health"':
                        $stmt->bindValue($identifier, $this->health, PDO::PARAM_INT);
                        break;
                    case '"strength"':
                        $stmt->bindValue($identifier, $this->strength, PDO::PARAM_INT);
                        break;
                    case '"gold"':
                        $stmt->bindValue($identifier, $this->gold, PDO::PARAM_INT);
                        break;
                    case '"messages"':
                        $stmt->bindValue($identifier, $this->messages, PDO::PARAM_STR);
                        break;
                    case '"kills"':
                        $stmt->bindValue($identifier, $this->kills, PDO::PARAM_INT);
                        break;
                    case '"turns"':
                        $stmt->bindValue($identifier, $this->turns, PDO::PARAM_INT);
                        break;
                    case '"verification_number"':
                        $stmt->bindValue($identifier, $this->verification_number, PDO::PARAM_INT);
                        break;
                    case '"active"':
                        $stmt->bindValue($identifier, $this->active, PDO::PARAM_INT);
                        break;
                    case '"email"':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '"level"':
                        $stmt->bindValue($identifier, $this->level, PDO::PARAM_INT);
                        break;
                    case '"status"':
                        $stmt->bindValue($identifier, $this->status, PDO::PARAM_INT);
                        break;
                    case '"member"':
                        $stmt->bindValue($identifier, $this->member, PDO::PARAM_INT);
                        break;
                    case '"days"':
                        $stmt->bindValue($identifier, $this->days, PDO::PARAM_INT);
                        break;
                    case '"ip"':
                        $stmt->bindValue($identifier, $this->ip, PDO::PARAM_STR);
                        break;
                    case '"bounty"':
                        $stmt->bindValue($identifier, $this->bounty, PDO::PARAM_INT);
                        break;
                    case '"created_date"':
                        $stmt->bindValue($identifier, $this->created_date, PDO::PARAM_STR);
                        break;
                    case '"resurrection_time"':
                        $stmt->bindValue($identifier, $this->resurrection_time, PDO::PARAM_INT);
                        break;
                    case '"last_started_attack"':
                        $stmt->bindValue($identifier, $this->last_started_attack, PDO::PARAM_STR);
                        break;
                    case '"energy"':
                        $stmt->bindValue($identifier, $this->energy, PDO::PARAM_INT);
                        break;
                    case '"avatar_type"':
                        $stmt->bindValue($identifier, $this->avatar_type, PDO::PARAM_INT);
                        break;
                    case '"_class_id"':
                        $stmt->bindValue($identifier, $this->_class_id, PDO::PARAM_INT);
                        break;
                    case '"ki"':
                        $stmt->bindValue($identifier, $this->ki, PDO::PARAM_INT);
                        break;
                    case '"stamina"':
                        $stmt->bindValue($identifier, $this->stamina, PDO::PARAM_INT);
                        break;
                    case '"speed"':
                        $stmt->bindValue($identifier, $this->speed, PDO::PARAM_INT);
                        break;
                    case '"karma"':
                        $stmt->bindValue($identifier, $this->karma, PDO::PARAM_INT);
                        break;
                    case '"kills_gained"':
                        $stmt->bindValue($identifier, $this->kills_gained, PDO::PARAM_INT);
                        break;
                    case '"kills_used"':
                        $stmt->bindValue($identifier, $this->kills_used, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aClass !== null) {
                if (!$this->aClass->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aClass->getValidationFailures());
                }
            }


            if (($retval = PlayersPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAccountPlayerss !== null) {
                    foreach ($this->collAccountPlayerss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collClanPlayers !== null) {
                    foreach ($this->collClanPlayers as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collEnemiessRelatedByEnemyId !== null) {
                    foreach ($this->collEnemiessRelatedByEnemyId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collEnemiessRelatedByPlayerId !== null) {
                    foreach ($this->collEnemiessRelatedByPlayerId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collInventorys !== null) {
                    foreach ($this->collInventorys as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collLevellingLogs !== null) {
                    foreach ($this->collLevellingLogs as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collMessagessRelatedBySendFrom !== null) {
                    foreach ($this->collMessagessRelatedBySendFrom as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collMessagessRelatedBySendTo !== null) {
                    foreach ($this->collMessagessRelatedBySendTo as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PlayersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getPlayerId();
                break;
            case 1:
                return $this->getUname();
                break;
            case 2:
                return $this->getPnameBackup();
                break;
            case 3:
                return $this->getHealth();
                break;
            case 4:
                return $this->getStrength();
                break;
            case 5:
                return $this->getGold();
                break;
            case 6:
                return $this->getMessages();
                break;
            case 7:
                return $this->getKills();
                break;
            case 8:
                return $this->getTurns();
                break;
            case 9:
                return $this->getVerificationNumber();
                break;
            case 10:
                return $this->getActive();
                break;
            case 11:
                return $this->getEmail();
                break;
            case 12:
                return $this->getLevel();
                break;
            case 13:
                return $this->getStatus();
                break;
            case 14:
                return $this->getMember();
                break;
            case 15:
                return $this->getDays();
                break;
            case 16:
                return $this->getIp();
                break;
            case 17:
                return $this->getBounty();
                break;
            case 18:
                return $this->getCreatedDate();
                break;
            case 19:
                return $this->getResurrectionTime();
                break;
            case 20:
                return $this->getLastStartedAttack();
                break;
            case 21:
                return $this->getEnergy();
                break;
            case 22:
                return $this->getAvatarType();
                break;
            case 23:
                return $this->getClassId();
                break;
            case 24:
                return $this->getKi();
                break;
            case 25:
                return $this->getStamina();
                break;
            case 26:
                return $this->getSpeed();
                break;
            case 27:
                return $this->getKarma();
                break;
            case 28:
                return $this->getKillsGained();
                break;
            case 29:
                return $this->getKillsUsed();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Players'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Players'][$this->getPrimaryKey()] = true;
        $keys = PlayersPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getPlayerId(),
            $keys[1] => $this->getUname(),
            $keys[2] => $this->getPnameBackup(),
            $keys[3] => $this->getHealth(),
            $keys[4] => $this->getStrength(),
            $keys[5] => $this->getGold(),
            $keys[6] => $this->getMessages(),
            $keys[7] => $this->getKills(),
            $keys[8] => $this->getTurns(),
            $keys[9] => $this->getVerificationNumber(),
            $keys[10] => $this->getActive(),
            $keys[11] => $this->getEmail(),
            $keys[12] => $this->getLevel(),
            $keys[13] => $this->getStatus(),
            $keys[14] => $this->getMember(),
            $keys[15] => $this->getDays(),
            $keys[16] => $this->getIp(),
            $keys[17] => $this->getBounty(),
            $keys[18] => $this->getCreatedDate(),
            $keys[19] => $this->getResurrectionTime(),
            $keys[20] => $this->getLastStartedAttack(),
            $keys[21] => $this->getEnergy(),
            $keys[22] => $this->getAvatarType(),
            $keys[23] => $this->getClassId(),
            $keys[24] => $this->getKi(),
            $keys[25] => $this->getStamina(),
            $keys[26] => $this->getSpeed(),
            $keys[27] => $this->getKarma(),
            $keys[28] => $this->getKillsGained(),
            $keys[29] => $this->getKillsUsed(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aClass) {
                $result['Class'] = $this->aClass->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collAccountPlayerss) {
                $result['AccountPlayerss'] = $this->collAccountPlayerss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collClanPlayers) {
                $result['ClanPlayers'] = $this->collClanPlayers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collEnemiessRelatedByEnemyId) {
                $result['EnemiessRelatedByEnemyId'] = $this->collEnemiessRelatedByEnemyId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collEnemiessRelatedByPlayerId) {
                $result['EnemiessRelatedByPlayerId'] = $this->collEnemiessRelatedByPlayerId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collInventorys) {
                $result['Inventorys'] = $this->collInventorys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collLevellingLogs) {
                $result['LevellingLogs'] = $this->collLevellingLogs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMessagessRelatedBySendFrom) {
                $result['MessagessRelatedBySendFrom'] = $this->collMessagessRelatedBySendFrom->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMessagessRelatedBySendTo) {
                $result['MessagessRelatedBySendTo'] = $this->collMessagessRelatedBySendTo->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PlayersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setPlayerId($value);
                break;
            case 1:
                $this->setUname($value);
                break;
            case 2:
                $this->setPnameBackup($value);
                break;
            case 3:
                $this->setHealth($value);
                break;
            case 4:
                $this->setStrength($value);
                break;
            case 5:
                $this->setGold($value);
                break;
            case 6:
                $this->setMessages($value);
                break;
            case 7:
                $this->setKills($value);
                break;
            case 8:
                $this->setTurns($value);
                break;
            case 9:
                $this->setVerificationNumber($value);
                break;
            case 10:
                $this->setActive($value);
                break;
            case 11:
                $this->setEmail($value);
                break;
            case 12:
                $this->setLevel($value);
                break;
            case 13:
                $this->setStatus($value);
                break;
            case 14:
                $this->setMember($value);
                break;
            case 15:
                $this->setDays($value);
                break;
            case 16:
                $this->setIp($value);
                break;
            case 17:
                $this->setBounty($value);
                break;
            case 18:
                $this->setCreatedDate($value);
                break;
            case 19:
                $this->setResurrectionTime($value);
                break;
            case 20:
                $this->setLastStartedAttack($value);
                break;
            case 21:
                $this->setEnergy($value);
                break;
            case 22:
                $this->setAvatarType($value);
                break;
            case 23:
                $this->setClassId($value);
                break;
            case 24:
                $this->setKi($value);
                break;
            case 25:
                $this->setStamina($value);
                break;
            case 26:
                $this->setSpeed($value);
                break;
            case 27:
                $this->setKarma($value);
                break;
            case 28:
                $this->setKillsGained($value);
                break;
            case 29:
                $this->setKillsUsed($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = PlayersPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setPlayerId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUname($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPnameBackup($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setHealth($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setStrength($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setGold($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setMessages($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setKills($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setTurns($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setVerificationNumber($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setActive($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setEmail($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setLevel($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setStatus($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setMember($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setDays($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setIp($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setBounty($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setCreatedDate($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setResurrectionTime($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setLastStartedAttack($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setEnergy($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setAvatarType($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setClassId($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setKi($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setStamina($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setSpeed($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setKarma($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setKillsGained($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setKillsUsed($arr[$keys[29]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PlayersPeer::DATABASE_NAME);

        if ($this->isColumnModified(PlayersPeer::PLAYER_ID)) $criteria->add(PlayersPeer::PLAYER_ID, $this->player_id);
        if ($this->isColumnModified(PlayersPeer::UNAME)) $criteria->add(PlayersPeer::UNAME, $this->uname);
        if ($this->isColumnModified(PlayersPeer::PNAME_BACKUP)) $criteria->add(PlayersPeer::PNAME_BACKUP, $this->pname_backup);
        if ($this->isColumnModified(PlayersPeer::HEALTH)) $criteria->add(PlayersPeer::HEALTH, $this->health);
        if ($this->isColumnModified(PlayersPeer::STRENGTH)) $criteria->add(PlayersPeer::STRENGTH, $this->strength);
        if ($this->isColumnModified(PlayersPeer::GOLD)) $criteria->add(PlayersPeer::GOLD, $this->gold);
        if ($this->isColumnModified(PlayersPeer::MESSAGES)) $criteria->add(PlayersPeer::MESSAGES, $this->messages);
        if ($this->isColumnModified(PlayersPeer::KILLS)) $criteria->add(PlayersPeer::KILLS, $this->kills);
        if ($this->isColumnModified(PlayersPeer::TURNS)) $criteria->add(PlayersPeer::TURNS, $this->turns);
        if ($this->isColumnModified(PlayersPeer::VERIFICATION_NUMBER)) $criteria->add(PlayersPeer::VERIFICATION_NUMBER, $this->verification_number);
        if ($this->isColumnModified(PlayersPeer::ACTIVE)) $criteria->add(PlayersPeer::ACTIVE, $this->active);
        if ($this->isColumnModified(PlayersPeer::EMAIL)) $criteria->add(PlayersPeer::EMAIL, $this->email);
        if ($this->isColumnModified(PlayersPeer::LEVEL)) $criteria->add(PlayersPeer::LEVEL, $this->level);
        if ($this->isColumnModified(PlayersPeer::STATUS)) $criteria->add(PlayersPeer::STATUS, $this->status);
        if ($this->isColumnModified(PlayersPeer::MEMBER)) $criteria->add(PlayersPeer::MEMBER, $this->member);
        if ($this->isColumnModified(PlayersPeer::DAYS)) $criteria->add(PlayersPeer::DAYS, $this->days);
        if ($this->isColumnModified(PlayersPeer::IP)) $criteria->add(PlayersPeer::IP, $this->ip);
        if ($this->isColumnModified(PlayersPeer::BOUNTY)) $criteria->add(PlayersPeer::BOUNTY, $this->bounty);
        if ($this->isColumnModified(PlayersPeer::CREATED_DATE)) $criteria->add(PlayersPeer::CREATED_DATE, $this->created_date);
        if ($this->isColumnModified(PlayersPeer::RESURRECTION_TIME)) $criteria->add(PlayersPeer::RESURRECTION_TIME, $this->resurrection_time);
        if ($this->isColumnModified(PlayersPeer::LAST_STARTED_ATTACK)) $criteria->add(PlayersPeer::LAST_STARTED_ATTACK, $this->last_started_attack);
        if ($this->isColumnModified(PlayersPeer::ENERGY)) $criteria->add(PlayersPeer::ENERGY, $this->energy);
        if ($this->isColumnModified(PlayersPeer::AVATAR_TYPE)) $criteria->add(PlayersPeer::AVATAR_TYPE, $this->avatar_type);
        if ($this->isColumnModified(PlayersPeer::_CLASS_ID)) $criteria->add(PlayersPeer::_CLASS_ID, $this->_class_id);
        if ($this->isColumnModified(PlayersPeer::KI)) $criteria->add(PlayersPeer::KI, $this->ki);
        if ($this->isColumnModified(PlayersPeer::STAMINA)) $criteria->add(PlayersPeer::STAMINA, $this->stamina);
        if ($this->isColumnModified(PlayersPeer::SPEED)) $criteria->add(PlayersPeer::SPEED, $this->speed);
        if ($this->isColumnModified(PlayersPeer::KARMA)) $criteria->add(PlayersPeer::KARMA, $this->karma);
        if ($this->isColumnModified(PlayersPeer::KILLS_GAINED)) $criteria->add(PlayersPeer::KILLS_GAINED, $this->kills_gained);
        if ($this->isColumnModified(PlayersPeer::KILLS_USED)) $criteria->add(PlayersPeer::KILLS_USED, $this->kills_used);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(PlayersPeer::DATABASE_NAME);
        $criteria->add(PlayersPeer::PLAYER_ID, $this->player_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getPlayerId();
    }

    /**
     * Generic method to set the primary key (player_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setPlayerId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getPlayerId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Players (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUname($this->getUname());
        $copyObj->setPnameBackup($this->getPnameBackup());
        $copyObj->setHealth($this->getHealth());
        $copyObj->setStrength($this->getStrength());
        $copyObj->setGold($this->getGold());
        $copyObj->setMessages($this->getMessages());
        $copyObj->setKills($this->getKills());
        $copyObj->setTurns($this->getTurns());
        $copyObj->setVerificationNumber($this->getVerificationNumber());
        $copyObj->setActive($this->getActive());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setLevel($this->getLevel());
        $copyObj->setStatus($this->getStatus());
        $copyObj->setMember($this->getMember());
        $copyObj->setDays($this->getDays());
        $copyObj->setIp($this->getIp());
        $copyObj->setBounty($this->getBounty());
        $copyObj->setCreatedDate($this->getCreatedDate());
        $copyObj->setResurrectionTime($this->getResurrectionTime());
        $copyObj->setLastStartedAttack($this->getLastStartedAttack());
        $copyObj->setEnergy($this->getEnergy());
        $copyObj->setAvatarType($this->getAvatarType());
        $copyObj->setClassId($this->getClassId());
        $copyObj->setKi($this->getKi());
        $copyObj->setStamina($this->getStamina());
        $copyObj->setSpeed($this->getSpeed());
        $copyObj->setKarma($this->getKarma());
        $copyObj->setKillsGained($this->getKillsGained());
        $copyObj->setKillsUsed($this->getKillsUsed());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getAccountPlayerss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountPlayers($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getClanPlayers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClanPlayer($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getEnemiessRelatedByEnemyId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEnemiesRelatedByEnemyId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getEnemiessRelatedByPlayerId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEnemiesRelatedByPlayerId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getInventorys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addInventory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getLevellingLogs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLevellingLog($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMessagessRelatedBySendFrom() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMessagesRelatedBySendFrom($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMessagessRelatedBySendTo() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMessagesRelatedBySendTo($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setPlayerId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Players Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return PlayersPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PlayersPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Class object.
     *
     * @param                  Class $v
     * @return Players The current object (for fluent API support)
     * @throws PropelException
     */
    public function setClass(Class $v = null)
    {
        if ($v === null) {
            $this->setClassId(NULL);
        } else {
            $this->setClassId($v->getClassId());
        }

        $this->aClass = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Class object, it will not be re-added.
        if ($v !== null) {
            $v->addPlayers($this);
        }


        return $this;
    }


    /**
     * Get the associated Class object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Class The associated Class object.
     * @throws PropelException
     */
    public function getClass(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aClass === null && ($this->_class_id !== null) && $doQuery) {
            $this->aClass = ClassQuery::create()->findPk($this->_class_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aClass->addPlayerss($this);
             */
        }

        return $this->aClass;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('AccountPlayers' == $relationName) {
            $this->initAccountPlayerss();
        }
        if ('ClanPlayer' == $relationName) {
            $this->initClanPlayers();
        }
        if ('EnemiesRelatedByEnemyId' == $relationName) {
            $this->initEnemiessRelatedByEnemyId();
        }
        if ('EnemiesRelatedByPlayerId' == $relationName) {
            $this->initEnemiessRelatedByPlayerId();
        }
        if ('Inventory' == $relationName) {
            $this->initInventorys();
        }
        if ('LevellingLog' == $relationName) {
            $this->initLevellingLogs();
        }
        if ('MessagesRelatedBySendFrom' == $relationName) {
            $this->initMessagessRelatedBySendFrom();
        }
        if ('MessagesRelatedBySendTo' == $relationName) {
            $this->initMessagessRelatedBySendTo();
        }
    }

    /**
     * Clears out the collAccountPlayerss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addAccountPlayerss()
     */
    public function clearAccountPlayerss()
    {
        $this->collAccountPlayerss = null; // important to set this to null since that means it is uninitialized
        $this->collAccountPlayerssPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountPlayerss collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountPlayerss($v = true)
    {
        $this->collAccountPlayerssPartial = $v;
    }

    /**
     * Initializes the collAccountPlayerss collection.
     *
     * By default this just sets the collAccountPlayerss collection to an empty array (like clearcollAccountPlayerss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountPlayerss($overrideExisting = true)
    {
        if (null !== $this->collAccountPlayerss && !$overrideExisting) {
            return;
        }
        $this->collAccountPlayerss = new PropelObjectCollection();
        $this->collAccountPlayerss->setModel('AccountPlayers');
    }

    /**
     * Gets an array of AccountPlayers objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AccountPlayers[] List of AccountPlayers objects
     * @throws PropelException
     */
    public function getAccountPlayerss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountPlayerssPartial && !$this->isNew();
        if (null === $this->collAccountPlayerss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountPlayerss) {
                // return empty collection
                $this->initAccountPlayerss();
            } else {
                $collAccountPlayerss = AccountPlayersQuery::create(null, $criteria)
                    ->filterByPlayers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountPlayerssPartial && count($collAccountPlayerss)) {
                      $this->initAccountPlayerss(false);

                      foreach ($collAccountPlayerss as $obj) {
                        if (false == $this->collAccountPlayerss->contains($obj)) {
                          $this->collAccountPlayerss->append($obj);
                        }
                      }

                      $this->collAccountPlayerssPartial = true;
                    }

                    $collAccountPlayerss->getInternalIterator()->rewind();

                    return $collAccountPlayerss;
                }

                if ($partial && $this->collAccountPlayerss) {
                    foreach ($this->collAccountPlayerss as $obj) {
                        if ($obj->isNew()) {
                            $collAccountPlayerss[] = $obj;
                        }
                    }
                }

                $this->collAccountPlayerss = $collAccountPlayerss;
                $this->collAccountPlayerssPartial = false;
            }
        }

        return $this->collAccountPlayerss;
    }

    /**
     * Sets a collection of AccountPlayers objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountPlayerss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setAccountPlayerss(PropelCollection $accountPlayerss, PropelPDO $con = null)
    {
        $accountPlayerssToDelete = $this->getAccountPlayerss(new Criteria(), $con)->diff($accountPlayerss);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->accountPlayerssScheduledForDeletion = clone $accountPlayerssToDelete;

        foreach ($accountPlayerssToDelete as $accountPlayersRemoved) {
            $accountPlayersRemoved->setPlayers(null);
        }

        $this->collAccountPlayerss = null;
        foreach ($accountPlayerss as $accountPlayers) {
            $this->addAccountPlayers($accountPlayers);
        }

        $this->collAccountPlayerss = $accountPlayerss;
        $this->collAccountPlayerssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountPlayers objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AccountPlayers objects.
     * @throws PropelException
     */
    public function countAccountPlayerss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountPlayerssPartial && !$this->isNew();
        if (null === $this->collAccountPlayerss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountPlayerss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountPlayerss());
            }
            $query = AccountPlayersQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayers($this)
                ->count($con);
        }

        return count($this->collAccountPlayerss);
    }

    /**
     * Method called to associate a AccountPlayers object to this object
     * through the AccountPlayers foreign key attribute.
     *
     * @param    AccountPlayers $l AccountPlayers
     * @return Players The current object (for fluent API support)
     */
    public function addAccountPlayers(AccountPlayers $l)
    {
        if ($this->collAccountPlayerss === null) {
            $this->initAccountPlayerss();
            $this->collAccountPlayerssPartial = true;
        }
        if (!in_array($l, $this->collAccountPlayerss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountPlayers($l);
        }

        return $this;
    }

    /**
     * @param	AccountPlayers $accountPlayers The accountPlayers object to add.
     */
    protected function doAddAccountPlayers($accountPlayers)
    {
        $this->collAccountPlayerss[]= $accountPlayers;
        $accountPlayers->setPlayers($this);
    }

    /**
     * @param	AccountPlayers $accountPlayers The accountPlayers object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeAccountPlayers($accountPlayers)
    {
        if ($this->getAccountPlayerss()->contains($accountPlayers)) {
            $this->collAccountPlayerss->remove($this->collAccountPlayerss->search($accountPlayers));
            if (null === $this->accountPlayerssScheduledForDeletion) {
                $this->accountPlayerssScheduledForDeletion = clone $this->collAccountPlayerss;
                $this->accountPlayerssScheduledForDeletion->clear();
            }
            $this->accountPlayerssScheduledForDeletion[]= clone $accountPlayers;
            $accountPlayers->setPlayers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Players is new, it will return
     * an empty collection; or if this Players has previously
     * been saved, it will retrieve related AccountPlayerss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Players.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AccountPlayers[] List of AccountPlayers objects
     */
    public function getAccountPlayerssJoinAccounts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountPlayersQuery::create(null, $criteria);
        $query->joinWith('Accounts', $join_behavior);

        return $this->getAccountPlayerss($query, $con);
    }

    /**
     * Clears out the collClanPlayers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addClanPlayers()
     */
    public function clearClanPlayers()
    {
        $this->collClanPlayers = null; // important to set this to null since that means it is uninitialized
        $this->collClanPlayersPartial = null;

        return $this;
    }

    /**
     * reset is the collClanPlayers collection loaded partially
     *
     * @return void
     */
    public function resetPartialClanPlayers($v = true)
    {
        $this->collClanPlayersPartial = $v;
    }

    /**
     * Initializes the collClanPlayers collection.
     *
     * By default this just sets the collClanPlayers collection to an empty array (like clearcollClanPlayers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClanPlayers($overrideExisting = true)
    {
        if (null !== $this->collClanPlayers && !$overrideExisting) {
            return;
        }
        $this->collClanPlayers = new PropelObjectCollection();
        $this->collClanPlayers->setModel('ClanPlayer');
    }

    /**
     * Gets an array of ClanPlayer objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ClanPlayer[] List of ClanPlayer objects
     * @throws PropelException
     */
    public function getClanPlayers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collClanPlayersPartial && !$this->isNew();
        if (null === $this->collClanPlayers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClanPlayers) {
                // return empty collection
                $this->initClanPlayers();
            } else {
                $collClanPlayers = ClanPlayerQuery::create(null, $criteria)
                    ->filterByPlayers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collClanPlayersPartial && count($collClanPlayers)) {
                      $this->initClanPlayers(false);

                      foreach ($collClanPlayers as $obj) {
                        if (false == $this->collClanPlayers->contains($obj)) {
                          $this->collClanPlayers->append($obj);
                        }
                      }

                      $this->collClanPlayersPartial = true;
                    }

                    $collClanPlayers->getInternalIterator()->rewind();

                    return $collClanPlayers;
                }

                if ($partial && $this->collClanPlayers) {
                    foreach ($this->collClanPlayers as $obj) {
                        if ($obj->isNew()) {
                            $collClanPlayers[] = $obj;
                        }
                    }
                }

                $this->collClanPlayers = $collClanPlayers;
                $this->collClanPlayersPartial = false;
            }
        }

        return $this->collClanPlayers;
    }

    /**
     * Sets a collection of ClanPlayer objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $clanPlayers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setClanPlayers(PropelCollection $clanPlayers, PropelPDO $con = null)
    {
        $clanPlayersToDelete = $this->getClanPlayers(new Criteria(), $con)->diff($clanPlayers);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->clanPlayersScheduledForDeletion = clone $clanPlayersToDelete;

        foreach ($clanPlayersToDelete as $clanPlayerRemoved) {
            $clanPlayerRemoved->setPlayers(null);
        }

        $this->collClanPlayers = null;
        foreach ($clanPlayers as $clanPlayer) {
            $this->addClanPlayer($clanPlayer);
        }

        $this->collClanPlayers = $clanPlayers;
        $this->collClanPlayersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ClanPlayer objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ClanPlayer objects.
     * @throws PropelException
     */
    public function countClanPlayers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collClanPlayersPartial && !$this->isNew();
        if (null === $this->collClanPlayers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClanPlayers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClanPlayers());
            }
            $query = ClanPlayerQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayers($this)
                ->count($con);
        }

        return count($this->collClanPlayers);
    }

    /**
     * Method called to associate a ClanPlayer object to this object
     * through the ClanPlayer foreign key attribute.
     *
     * @param    ClanPlayer $l ClanPlayer
     * @return Players The current object (for fluent API support)
     */
    public function addClanPlayer(ClanPlayer $l)
    {
        if ($this->collClanPlayers === null) {
            $this->initClanPlayers();
            $this->collClanPlayersPartial = true;
        }
        if (!in_array($l, $this->collClanPlayers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClanPlayer($l);
        }

        return $this;
    }

    /**
     * @param	ClanPlayer $clanPlayer The clanPlayer object to add.
     */
    protected function doAddClanPlayer($clanPlayer)
    {
        $this->collClanPlayers[]= $clanPlayer;
        $clanPlayer->setPlayers($this);
    }

    /**
     * @param	ClanPlayer $clanPlayer The clanPlayer object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeClanPlayer($clanPlayer)
    {
        if ($this->getClanPlayers()->contains($clanPlayer)) {
            $this->collClanPlayers->remove($this->collClanPlayers->search($clanPlayer));
            if (null === $this->clanPlayersScheduledForDeletion) {
                $this->clanPlayersScheduledForDeletion = clone $this->collClanPlayers;
                $this->clanPlayersScheduledForDeletion->clear();
            }
            $this->clanPlayersScheduledForDeletion[]= clone $clanPlayer;
            $clanPlayer->setPlayers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Players is new, it will return
     * an empty collection; or if this Players has previously
     * been saved, it will retrieve related ClanPlayers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Players.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ClanPlayer[] List of ClanPlayer objects
     */
    public function getClanPlayersJoinClan($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ClanPlayerQuery::create(null, $criteria);
        $query->joinWith('Clan', $join_behavior);

        return $this->getClanPlayers($query, $con);
    }

    /**
     * Clears out the collEnemiessRelatedByEnemyId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addEnemiessRelatedByEnemyId()
     */
    public function clearEnemiessRelatedByEnemyId()
    {
        $this->collEnemiessRelatedByEnemyId = null; // important to set this to null since that means it is uninitialized
        $this->collEnemiessRelatedByEnemyIdPartial = null;

        return $this;
    }

    /**
     * reset is the collEnemiessRelatedByEnemyId collection loaded partially
     *
     * @return void
     */
    public function resetPartialEnemiessRelatedByEnemyId($v = true)
    {
        $this->collEnemiessRelatedByEnemyIdPartial = $v;
    }

    /**
     * Initializes the collEnemiessRelatedByEnemyId collection.
     *
     * By default this just sets the collEnemiessRelatedByEnemyId collection to an empty array (like clearcollEnemiessRelatedByEnemyId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEnemiessRelatedByEnemyId($overrideExisting = true)
    {
        if (null !== $this->collEnemiessRelatedByEnemyId && !$overrideExisting) {
            return;
        }
        $this->collEnemiessRelatedByEnemyId = new PropelObjectCollection();
        $this->collEnemiessRelatedByEnemyId->setModel('Enemies');
    }

    /**
     * Gets an array of Enemies objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Enemies[] List of Enemies objects
     * @throws PropelException
     */
    public function getEnemiessRelatedByEnemyId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collEnemiessRelatedByEnemyIdPartial && !$this->isNew();
        if (null === $this->collEnemiessRelatedByEnemyId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEnemiessRelatedByEnemyId) {
                // return empty collection
                $this->initEnemiessRelatedByEnemyId();
            } else {
                $collEnemiessRelatedByEnemyId = EnemiesQuery::create(null, $criteria)
                    ->filterByPlayersRelatedByEnemyId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collEnemiessRelatedByEnemyIdPartial && count($collEnemiessRelatedByEnemyId)) {
                      $this->initEnemiessRelatedByEnemyId(false);

                      foreach ($collEnemiessRelatedByEnemyId as $obj) {
                        if (false == $this->collEnemiessRelatedByEnemyId->contains($obj)) {
                          $this->collEnemiessRelatedByEnemyId->append($obj);
                        }
                      }

                      $this->collEnemiessRelatedByEnemyIdPartial = true;
                    }

                    $collEnemiessRelatedByEnemyId->getInternalIterator()->rewind();

                    return $collEnemiessRelatedByEnemyId;
                }

                if ($partial && $this->collEnemiessRelatedByEnemyId) {
                    foreach ($this->collEnemiessRelatedByEnemyId as $obj) {
                        if ($obj->isNew()) {
                            $collEnemiessRelatedByEnemyId[] = $obj;
                        }
                    }
                }

                $this->collEnemiessRelatedByEnemyId = $collEnemiessRelatedByEnemyId;
                $this->collEnemiessRelatedByEnemyIdPartial = false;
            }
        }

        return $this->collEnemiessRelatedByEnemyId;
    }

    /**
     * Sets a collection of EnemiesRelatedByEnemyId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $enemiessRelatedByEnemyId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setEnemiessRelatedByEnemyId(PropelCollection $enemiessRelatedByEnemyId, PropelPDO $con = null)
    {
        $enemiessRelatedByEnemyIdToDelete = $this->getEnemiessRelatedByEnemyId(new Criteria(), $con)->diff($enemiessRelatedByEnemyId);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->enemiessRelatedByEnemyIdScheduledForDeletion = clone $enemiessRelatedByEnemyIdToDelete;

        foreach ($enemiessRelatedByEnemyIdToDelete as $enemiesRelatedByEnemyIdRemoved) {
            $enemiesRelatedByEnemyIdRemoved->setPlayersRelatedByEnemyId(null);
        }

        $this->collEnemiessRelatedByEnemyId = null;
        foreach ($enemiessRelatedByEnemyId as $enemiesRelatedByEnemyId) {
            $this->addEnemiesRelatedByEnemyId($enemiesRelatedByEnemyId);
        }

        $this->collEnemiessRelatedByEnemyId = $enemiessRelatedByEnemyId;
        $this->collEnemiessRelatedByEnemyIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Enemies objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Enemies objects.
     * @throws PropelException
     */
    public function countEnemiessRelatedByEnemyId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collEnemiessRelatedByEnemyIdPartial && !$this->isNew();
        if (null === $this->collEnemiessRelatedByEnemyId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEnemiessRelatedByEnemyId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getEnemiessRelatedByEnemyId());
            }
            $query = EnemiesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayersRelatedByEnemyId($this)
                ->count($con);
        }

        return count($this->collEnemiessRelatedByEnemyId);
    }

    /**
     * Method called to associate a Enemies object to this object
     * through the Enemies foreign key attribute.
     *
     * @param    Enemies $l Enemies
     * @return Players The current object (for fluent API support)
     */
    public function addEnemiesRelatedByEnemyId(Enemies $l)
    {
        if ($this->collEnemiessRelatedByEnemyId === null) {
            $this->initEnemiessRelatedByEnemyId();
            $this->collEnemiessRelatedByEnemyIdPartial = true;
        }
        if (!in_array($l, $this->collEnemiessRelatedByEnemyId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddEnemiesRelatedByEnemyId($l);
        }

        return $this;
    }

    /**
     * @param	EnemiesRelatedByEnemyId $enemiesRelatedByEnemyId The enemiesRelatedByEnemyId object to add.
     */
    protected function doAddEnemiesRelatedByEnemyId($enemiesRelatedByEnemyId)
    {
        $this->collEnemiessRelatedByEnemyId[]= $enemiesRelatedByEnemyId;
        $enemiesRelatedByEnemyId->setPlayersRelatedByEnemyId($this);
    }

    /**
     * @param	EnemiesRelatedByEnemyId $enemiesRelatedByEnemyId The enemiesRelatedByEnemyId object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeEnemiesRelatedByEnemyId($enemiesRelatedByEnemyId)
    {
        if ($this->getEnemiessRelatedByEnemyId()->contains($enemiesRelatedByEnemyId)) {
            $this->collEnemiessRelatedByEnemyId->remove($this->collEnemiessRelatedByEnemyId->search($enemiesRelatedByEnemyId));
            if (null === $this->enemiessRelatedByEnemyIdScheduledForDeletion) {
                $this->enemiessRelatedByEnemyIdScheduledForDeletion = clone $this->collEnemiessRelatedByEnemyId;
                $this->enemiessRelatedByEnemyIdScheduledForDeletion->clear();
            }
            $this->enemiessRelatedByEnemyIdScheduledForDeletion[]= clone $enemiesRelatedByEnemyId;
            $enemiesRelatedByEnemyId->setPlayersRelatedByEnemyId(null);
        }

        return $this;
    }

    /**
     * Clears out the collEnemiessRelatedByPlayerId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addEnemiessRelatedByPlayerId()
     */
    public function clearEnemiessRelatedByPlayerId()
    {
        $this->collEnemiessRelatedByPlayerId = null; // important to set this to null since that means it is uninitialized
        $this->collEnemiessRelatedByPlayerIdPartial = null;

        return $this;
    }

    /**
     * reset is the collEnemiessRelatedByPlayerId collection loaded partially
     *
     * @return void
     */
    public function resetPartialEnemiessRelatedByPlayerId($v = true)
    {
        $this->collEnemiessRelatedByPlayerIdPartial = $v;
    }

    /**
     * Initializes the collEnemiessRelatedByPlayerId collection.
     *
     * By default this just sets the collEnemiessRelatedByPlayerId collection to an empty array (like clearcollEnemiessRelatedByPlayerId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEnemiessRelatedByPlayerId($overrideExisting = true)
    {
        if (null !== $this->collEnemiessRelatedByPlayerId && !$overrideExisting) {
            return;
        }
        $this->collEnemiessRelatedByPlayerId = new PropelObjectCollection();
        $this->collEnemiessRelatedByPlayerId->setModel('Enemies');
    }

    /**
     * Gets an array of Enemies objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Enemies[] List of Enemies objects
     * @throws PropelException
     */
    public function getEnemiessRelatedByPlayerId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collEnemiessRelatedByPlayerIdPartial && !$this->isNew();
        if (null === $this->collEnemiessRelatedByPlayerId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEnemiessRelatedByPlayerId) {
                // return empty collection
                $this->initEnemiessRelatedByPlayerId();
            } else {
                $collEnemiessRelatedByPlayerId = EnemiesQuery::create(null, $criteria)
                    ->filterByPlayersRelatedByPlayerId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collEnemiessRelatedByPlayerIdPartial && count($collEnemiessRelatedByPlayerId)) {
                      $this->initEnemiessRelatedByPlayerId(false);

                      foreach ($collEnemiessRelatedByPlayerId as $obj) {
                        if (false == $this->collEnemiessRelatedByPlayerId->contains($obj)) {
                          $this->collEnemiessRelatedByPlayerId->append($obj);
                        }
                      }

                      $this->collEnemiessRelatedByPlayerIdPartial = true;
                    }

                    $collEnemiessRelatedByPlayerId->getInternalIterator()->rewind();

                    return $collEnemiessRelatedByPlayerId;
                }

                if ($partial && $this->collEnemiessRelatedByPlayerId) {
                    foreach ($this->collEnemiessRelatedByPlayerId as $obj) {
                        if ($obj->isNew()) {
                            $collEnemiessRelatedByPlayerId[] = $obj;
                        }
                    }
                }

                $this->collEnemiessRelatedByPlayerId = $collEnemiessRelatedByPlayerId;
                $this->collEnemiessRelatedByPlayerIdPartial = false;
            }
        }

        return $this->collEnemiessRelatedByPlayerId;
    }

    /**
     * Sets a collection of EnemiesRelatedByPlayerId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $enemiessRelatedByPlayerId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setEnemiessRelatedByPlayerId(PropelCollection $enemiessRelatedByPlayerId, PropelPDO $con = null)
    {
        $enemiessRelatedByPlayerIdToDelete = $this->getEnemiessRelatedByPlayerId(new Criteria(), $con)->diff($enemiessRelatedByPlayerId);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->enemiessRelatedByPlayerIdScheduledForDeletion = clone $enemiessRelatedByPlayerIdToDelete;

        foreach ($enemiessRelatedByPlayerIdToDelete as $enemiesRelatedByPlayerIdRemoved) {
            $enemiesRelatedByPlayerIdRemoved->setPlayersRelatedByPlayerId(null);
        }

        $this->collEnemiessRelatedByPlayerId = null;
        foreach ($enemiessRelatedByPlayerId as $enemiesRelatedByPlayerId) {
            $this->addEnemiesRelatedByPlayerId($enemiesRelatedByPlayerId);
        }

        $this->collEnemiessRelatedByPlayerId = $enemiessRelatedByPlayerId;
        $this->collEnemiessRelatedByPlayerIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Enemies objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Enemies objects.
     * @throws PropelException
     */
    public function countEnemiessRelatedByPlayerId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collEnemiessRelatedByPlayerIdPartial && !$this->isNew();
        if (null === $this->collEnemiessRelatedByPlayerId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEnemiessRelatedByPlayerId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getEnemiessRelatedByPlayerId());
            }
            $query = EnemiesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayersRelatedByPlayerId($this)
                ->count($con);
        }

        return count($this->collEnemiessRelatedByPlayerId);
    }

    /**
     * Method called to associate a Enemies object to this object
     * through the Enemies foreign key attribute.
     *
     * @param    Enemies $l Enemies
     * @return Players The current object (for fluent API support)
     */
    public function addEnemiesRelatedByPlayerId(Enemies $l)
    {
        if ($this->collEnemiessRelatedByPlayerId === null) {
            $this->initEnemiessRelatedByPlayerId();
            $this->collEnemiessRelatedByPlayerIdPartial = true;
        }
        if (!in_array($l, $this->collEnemiessRelatedByPlayerId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddEnemiesRelatedByPlayerId($l);
        }

        return $this;
    }

    /**
     * @param	EnemiesRelatedByPlayerId $enemiesRelatedByPlayerId The enemiesRelatedByPlayerId object to add.
     */
    protected function doAddEnemiesRelatedByPlayerId($enemiesRelatedByPlayerId)
    {
        $this->collEnemiessRelatedByPlayerId[]= $enemiesRelatedByPlayerId;
        $enemiesRelatedByPlayerId->setPlayersRelatedByPlayerId($this);
    }

    /**
     * @param	EnemiesRelatedByPlayerId $enemiesRelatedByPlayerId The enemiesRelatedByPlayerId object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeEnemiesRelatedByPlayerId($enemiesRelatedByPlayerId)
    {
        if ($this->getEnemiessRelatedByPlayerId()->contains($enemiesRelatedByPlayerId)) {
            $this->collEnemiessRelatedByPlayerId->remove($this->collEnemiessRelatedByPlayerId->search($enemiesRelatedByPlayerId));
            if (null === $this->enemiessRelatedByPlayerIdScheduledForDeletion) {
                $this->enemiessRelatedByPlayerIdScheduledForDeletion = clone $this->collEnemiessRelatedByPlayerId;
                $this->enemiessRelatedByPlayerIdScheduledForDeletion->clear();
            }
            $this->enemiessRelatedByPlayerIdScheduledForDeletion[]= clone $enemiesRelatedByPlayerId;
            $enemiesRelatedByPlayerId->setPlayersRelatedByPlayerId(null);
        }

        return $this;
    }

    /**
     * Clears out the collInventorys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addInventorys()
     */
    public function clearInventorys()
    {
        $this->collInventorys = null; // important to set this to null since that means it is uninitialized
        $this->collInventorysPartial = null;

        return $this;
    }

    /**
     * reset is the collInventorys collection loaded partially
     *
     * @return void
     */
    public function resetPartialInventorys($v = true)
    {
        $this->collInventorysPartial = $v;
    }

    /**
     * Initializes the collInventorys collection.
     *
     * By default this just sets the collInventorys collection to an empty array (like clearcollInventorys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initInventorys($overrideExisting = true)
    {
        if (null !== $this->collInventorys && !$overrideExisting) {
            return;
        }
        $this->collInventorys = new PropelObjectCollection();
        $this->collInventorys->setModel('Inventory');
    }

    /**
     * Gets an array of Inventory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Inventory[] List of Inventory objects
     * @throws PropelException
     */
    public function getInventorys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collInventorysPartial && !$this->isNew();
        if (null === $this->collInventorys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collInventorys) {
                // return empty collection
                $this->initInventorys();
            } else {
                $collInventorys = InventoryQuery::create(null, $criteria)
                    ->filterByPlayers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collInventorysPartial && count($collInventorys)) {
                      $this->initInventorys(false);

                      foreach ($collInventorys as $obj) {
                        if (false == $this->collInventorys->contains($obj)) {
                          $this->collInventorys->append($obj);
                        }
                      }

                      $this->collInventorysPartial = true;
                    }

                    $collInventorys->getInternalIterator()->rewind();

                    return $collInventorys;
                }

                if ($partial && $this->collInventorys) {
                    foreach ($this->collInventorys as $obj) {
                        if ($obj->isNew()) {
                            $collInventorys[] = $obj;
                        }
                    }
                }

                $this->collInventorys = $collInventorys;
                $this->collInventorysPartial = false;
            }
        }

        return $this->collInventorys;
    }

    /**
     * Sets a collection of Inventory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $inventorys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setInventorys(PropelCollection $inventorys, PropelPDO $con = null)
    {
        $inventorysToDelete = $this->getInventorys(new Criteria(), $con)->diff($inventorys);


        $this->inventorysScheduledForDeletion = $inventorysToDelete;

        foreach ($inventorysToDelete as $inventoryRemoved) {
            $inventoryRemoved->setPlayers(null);
        }

        $this->collInventorys = null;
        foreach ($inventorys as $inventory) {
            $this->addInventory($inventory);
        }

        $this->collInventorys = $inventorys;
        $this->collInventorysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Inventory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Inventory objects.
     * @throws PropelException
     */
    public function countInventorys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collInventorysPartial && !$this->isNew();
        if (null === $this->collInventorys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collInventorys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getInventorys());
            }
            $query = InventoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayers($this)
                ->count($con);
        }

        return count($this->collInventorys);
    }

    /**
     * Method called to associate a Inventory object to this object
     * through the Inventory foreign key attribute.
     *
     * @param    Inventory $l Inventory
     * @return Players The current object (for fluent API support)
     */
    public function addInventory(Inventory $l)
    {
        if ($this->collInventorys === null) {
            $this->initInventorys();
            $this->collInventorysPartial = true;
        }
        if (!in_array($l, $this->collInventorys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddInventory($l);
        }

        return $this;
    }

    /**
     * @param	Inventory $inventory The inventory object to add.
     */
    protected function doAddInventory($inventory)
    {
        $this->collInventorys[]= $inventory;
        $inventory->setPlayers($this);
    }

    /**
     * @param	Inventory $inventory The inventory object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeInventory($inventory)
    {
        if ($this->getInventorys()->contains($inventory)) {
            $this->collInventorys->remove($this->collInventorys->search($inventory));
            if (null === $this->inventorysScheduledForDeletion) {
                $this->inventorysScheduledForDeletion = clone $this->collInventorys;
                $this->inventorysScheduledForDeletion->clear();
            }
            $this->inventorysScheduledForDeletion[]= clone $inventory;
            $inventory->setPlayers(null);
        }

        return $this;
    }

    /**
     * Clears out the collLevellingLogs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addLevellingLogs()
     */
    public function clearLevellingLogs()
    {
        $this->collLevellingLogs = null; // important to set this to null since that means it is uninitialized
        $this->collLevellingLogsPartial = null;

        return $this;
    }

    /**
     * reset is the collLevellingLogs collection loaded partially
     *
     * @return void
     */
    public function resetPartialLevellingLogs($v = true)
    {
        $this->collLevellingLogsPartial = $v;
    }

    /**
     * Initializes the collLevellingLogs collection.
     *
     * By default this just sets the collLevellingLogs collection to an empty array (like clearcollLevellingLogs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLevellingLogs($overrideExisting = true)
    {
        if (null !== $this->collLevellingLogs && !$overrideExisting) {
            return;
        }
        $this->collLevellingLogs = new PropelObjectCollection();
        $this->collLevellingLogs->setModel('LevellingLog');
    }

    /**
     * Gets an array of LevellingLog objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|LevellingLog[] List of LevellingLog objects
     * @throws PropelException
     */
    public function getLevellingLogs($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collLevellingLogsPartial && !$this->isNew();
        if (null === $this->collLevellingLogs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLevellingLogs) {
                // return empty collection
                $this->initLevellingLogs();
            } else {
                $collLevellingLogs = LevellingLogQuery::create(null, $criteria)
                    ->filterByPlayers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collLevellingLogsPartial && count($collLevellingLogs)) {
                      $this->initLevellingLogs(false);

                      foreach ($collLevellingLogs as $obj) {
                        if (false == $this->collLevellingLogs->contains($obj)) {
                          $this->collLevellingLogs->append($obj);
                        }
                      }

                      $this->collLevellingLogsPartial = true;
                    }

                    $collLevellingLogs->getInternalIterator()->rewind();

                    return $collLevellingLogs;
                }

                if ($partial && $this->collLevellingLogs) {
                    foreach ($this->collLevellingLogs as $obj) {
                        if ($obj->isNew()) {
                            $collLevellingLogs[] = $obj;
                        }
                    }
                }

                $this->collLevellingLogs = $collLevellingLogs;
                $this->collLevellingLogsPartial = false;
            }
        }

        return $this->collLevellingLogs;
    }

    /**
     * Sets a collection of LevellingLog objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $levellingLogs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setLevellingLogs(PropelCollection $levellingLogs, PropelPDO $con = null)
    {
        $levellingLogsToDelete = $this->getLevellingLogs(new Criteria(), $con)->diff($levellingLogs);


        $this->levellingLogsScheduledForDeletion = $levellingLogsToDelete;

        foreach ($levellingLogsToDelete as $levellingLogRemoved) {
            $levellingLogRemoved->setPlayers(null);
        }

        $this->collLevellingLogs = null;
        foreach ($levellingLogs as $levellingLog) {
            $this->addLevellingLog($levellingLog);
        }

        $this->collLevellingLogs = $levellingLogs;
        $this->collLevellingLogsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related LevellingLog objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related LevellingLog objects.
     * @throws PropelException
     */
    public function countLevellingLogs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collLevellingLogsPartial && !$this->isNew();
        if (null === $this->collLevellingLogs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLevellingLogs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLevellingLogs());
            }
            $query = LevellingLogQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayers($this)
                ->count($con);
        }

        return count($this->collLevellingLogs);
    }

    /**
     * Method called to associate a LevellingLog object to this object
     * through the LevellingLog foreign key attribute.
     *
     * @param    LevellingLog $l LevellingLog
     * @return Players The current object (for fluent API support)
     */
    public function addLevellingLog(LevellingLog $l)
    {
        if ($this->collLevellingLogs === null) {
            $this->initLevellingLogs();
            $this->collLevellingLogsPartial = true;
        }
        if (!in_array($l, $this->collLevellingLogs->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLevellingLog($l);
        }

        return $this;
    }

    /**
     * @param	LevellingLog $levellingLog The levellingLog object to add.
     */
    protected function doAddLevellingLog($levellingLog)
    {
        $this->collLevellingLogs[]= $levellingLog;
        $levellingLog->setPlayers($this);
    }

    /**
     * @param	LevellingLog $levellingLog The levellingLog object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeLevellingLog($levellingLog)
    {
        if ($this->getLevellingLogs()->contains($levellingLog)) {
            $this->collLevellingLogs->remove($this->collLevellingLogs->search($levellingLog));
            if (null === $this->levellingLogsScheduledForDeletion) {
                $this->levellingLogsScheduledForDeletion = clone $this->collLevellingLogs;
                $this->levellingLogsScheduledForDeletion->clear();
            }
            $this->levellingLogsScheduledForDeletion[]= clone $levellingLog;
            $levellingLog->setPlayers(null);
        }

        return $this;
    }

    /**
     * Clears out the collMessagessRelatedBySendFrom collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addMessagessRelatedBySendFrom()
     */
    public function clearMessagessRelatedBySendFrom()
    {
        $this->collMessagessRelatedBySendFrom = null; // important to set this to null since that means it is uninitialized
        $this->collMessagessRelatedBySendFromPartial = null;

        return $this;
    }

    /**
     * reset is the collMessagessRelatedBySendFrom collection loaded partially
     *
     * @return void
     */
    public function resetPartialMessagessRelatedBySendFrom($v = true)
    {
        $this->collMessagessRelatedBySendFromPartial = $v;
    }

    /**
     * Initializes the collMessagessRelatedBySendFrom collection.
     *
     * By default this just sets the collMessagessRelatedBySendFrom collection to an empty array (like clearcollMessagessRelatedBySendFrom());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMessagessRelatedBySendFrom($overrideExisting = true)
    {
        if (null !== $this->collMessagessRelatedBySendFrom && !$overrideExisting) {
            return;
        }
        $this->collMessagessRelatedBySendFrom = new PropelObjectCollection();
        $this->collMessagessRelatedBySendFrom->setModel('Messages');
    }

    /**
     * Gets an array of Messages objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Messages[] List of Messages objects
     * @throws PropelException
     */
    public function getMessagessRelatedBySendFrom($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collMessagessRelatedBySendFromPartial && !$this->isNew();
        if (null === $this->collMessagessRelatedBySendFrom || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMessagessRelatedBySendFrom) {
                // return empty collection
                $this->initMessagessRelatedBySendFrom();
            } else {
                $collMessagessRelatedBySendFrom = MessagesQuery::create(null, $criteria)
                    ->filterByPlayersRelatedBySendFrom($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collMessagessRelatedBySendFromPartial && count($collMessagessRelatedBySendFrom)) {
                      $this->initMessagessRelatedBySendFrom(false);

                      foreach ($collMessagessRelatedBySendFrom as $obj) {
                        if (false == $this->collMessagessRelatedBySendFrom->contains($obj)) {
                          $this->collMessagessRelatedBySendFrom->append($obj);
                        }
                      }

                      $this->collMessagessRelatedBySendFromPartial = true;
                    }

                    $collMessagessRelatedBySendFrom->getInternalIterator()->rewind();

                    return $collMessagessRelatedBySendFrom;
                }

                if ($partial && $this->collMessagessRelatedBySendFrom) {
                    foreach ($this->collMessagessRelatedBySendFrom as $obj) {
                        if ($obj->isNew()) {
                            $collMessagessRelatedBySendFrom[] = $obj;
                        }
                    }
                }

                $this->collMessagessRelatedBySendFrom = $collMessagessRelatedBySendFrom;
                $this->collMessagessRelatedBySendFromPartial = false;
            }
        }

        return $this->collMessagessRelatedBySendFrom;
    }

    /**
     * Sets a collection of MessagesRelatedBySendFrom objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $messagessRelatedBySendFrom A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setMessagessRelatedBySendFrom(PropelCollection $messagessRelatedBySendFrom, PropelPDO $con = null)
    {
        $messagessRelatedBySendFromToDelete = $this->getMessagessRelatedBySendFrom(new Criteria(), $con)->diff($messagessRelatedBySendFrom);


        $this->messagessRelatedBySendFromScheduledForDeletion = $messagessRelatedBySendFromToDelete;

        foreach ($messagessRelatedBySendFromToDelete as $messagesRelatedBySendFromRemoved) {
            $messagesRelatedBySendFromRemoved->setPlayersRelatedBySendFrom(null);
        }

        $this->collMessagessRelatedBySendFrom = null;
        foreach ($messagessRelatedBySendFrom as $messagesRelatedBySendFrom) {
            $this->addMessagesRelatedBySendFrom($messagesRelatedBySendFrom);
        }

        $this->collMessagessRelatedBySendFrom = $messagessRelatedBySendFrom;
        $this->collMessagessRelatedBySendFromPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Messages objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Messages objects.
     * @throws PropelException
     */
    public function countMessagessRelatedBySendFrom(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collMessagessRelatedBySendFromPartial && !$this->isNew();
        if (null === $this->collMessagessRelatedBySendFrom || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMessagessRelatedBySendFrom) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMessagessRelatedBySendFrom());
            }
            $query = MessagesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayersRelatedBySendFrom($this)
                ->count($con);
        }

        return count($this->collMessagessRelatedBySendFrom);
    }

    /**
     * Method called to associate a Messages object to this object
     * through the Messages foreign key attribute.
     *
     * @param    Messages $l Messages
     * @return Players The current object (for fluent API support)
     */
    public function addMessagesRelatedBySendFrom(Messages $l)
    {
        if ($this->collMessagessRelatedBySendFrom === null) {
            $this->initMessagessRelatedBySendFrom();
            $this->collMessagessRelatedBySendFromPartial = true;
        }
        if (!in_array($l, $this->collMessagessRelatedBySendFrom->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMessagesRelatedBySendFrom($l);
        }

        return $this;
    }

    /**
     * @param	MessagesRelatedBySendFrom $messagesRelatedBySendFrom The messagesRelatedBySendFrom object to add.
     */
    protected function doAddMessagesRelatedBySendFrom($messagesRelatedBySendFrom)
    {
        $this->collMessagessRelatedBySendFrom[]= $messagesRelatedBySendFrom;
        $messagesRelatedBySendFrom->setPlayersRelatedBySendFrom($this);
    }

    /**
     * @param	MessagesRelatedBySendFrom $messagesRelatedBySendFrom The messagesRelatedBySendFrom object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeMessagesRelatedBySendFrom($messagesRelatedBySendFrom)
    {
        if ($this->getMessagessRelatedBySendFrom()->contains($messagesRelatedBySendFrom)) {
            $this->collMessagessRelatedBySendFrom->remove($this->collMessagessRelatedBySendFrom->search($messagesRelatedBySendFrom));
            if (null === $this->messagessRelatedBySendFromScheduledForDeletion) {
                $this->messagessRelatedBySendFromScheduledForDeletion = clone $this->collMessagessRelatedBySendFrom;
                $this->messagessRelatedBySendFromScheduledForDeletion->clear();
            }
            $this->messagessRelatedBySendFromScheduledForDeletion[]= $messagesRelatedBySendFrom;
            $messagesRelatedBySendFrom->setPlayersRelatedBySendFrom(null);
        }

        return $this;
    }

    /**
     * Clears out the collMessagessRelatedBySendTo collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Players The current object (for fluent API support)
     * @see        addMessagessRelatedBySendTo()
     */
    public function clearMessagessRelatedBySendTo()
    {
        $this->collMessagessRelatedBySendTo = null; // important to set this to null since that means it is uninitialized
        $this->collMessagessRelatedBySendToPartial = null;

        return $this;
    }

    /**
     * reset is the collMessagessRelatedBySendTo collection loaded partially
     *
     * @return void
     */
    public function resetPartialMessagessRelatedBySendTo($v = true)
    {
        $this->collMessagessRelatedBySendToPartial = $v;
    }

    /**
     * Initializes the collMessagessRelatedBySendTo collection.
     *
     * By default this just sets the collMessagessRelatedBySendTo collection to an empty array (like clearcollMessagessRelatedBySendTo());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMessagessRelatedBySendTo($overrideExisting = true)
    {
        if (null !== $this->collMessagessRelatedBySendTo && !$overrideExisting) {
            return;
        }
        $this->collMessagessRelatedBySendTo = new PropelObjectCollection();
        $this->collMessagessRelatedBySendTo->setModel('Messages');
    }

    /**
     * Gets an array of Messages objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Players is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Messages[] List of Messages objects
     * @throws PropelException
     */
    public function getMessagessRelatedBySendTo($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collMessagessRelatedBySendToPartial && !$this->isNew();
        if (null === $this->collMessagessRelatedBySendTo || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMessagessRelatedBySendTo) {
                // return empty collection
                $this->initMessagessRelatedBySendTo();
            } else {
                $collMessagessRelatedBySendTo = MessagesQuery::create(null, $criteria)
                    ->filterByPlayersRelatedBySendTo($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collMessagessRelatedBySendToPartial && count($collMessagessRelatedBySendTo)) {
                      $this->initMessagessRelatedBySendTo(false);

                      foreach ($collMessagessRelatedBySendTo as $obj) {
                        if (false == $this->collMessagessRelatedBySendTo->contains($obj)) {
                          $this->collMessagessRelatedBySendTo->append($obj);
                        }
                      }

                      $this->collMessagessRelatedBySendToPartial = true;
                    }

                    $collMessagessRelatedBySendTo->getInternalIterator()->rewind();

                    return $collMessagessRelatedBySendTo;
                }

                if ($partial && $this->collMessagessRelatedBySendTo) {
                    foreach ($this->collMessagessRelatedBySendTo as $obj) {
                        if ($obj->isNew()) {
                            $collMessagessRelatedBySendTo[] = $obj;
                        }
                    }
                }

                $this->collMessagessRelatedBySendTo = $collMessagessRelatedBySendTo;
                $this->collMessagessRelatedBySendToPartial = false;
            }
        }

        return $this->collMessagessRelatedBySendTo;
    }

    /**
     * Sets a collection of MessagesRelatedBySendTo objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $messagessRelatedBySendTo A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Players The current object (for fluent API support)
     */
    public function setMessagessRelatedBySendTo(PropelCollection $messagessRelatedBySendTo, PropelPDO $con = null)
    {
        $messagessRelatedBySendToToDelete = $this->getMessagessRelatedBySendTo(new Criteria(), $con)->diff($messagessRelatedBySendTo);


        $this->messagessRelatedBySendToScheduledForDeletion = $messagessRelatedBySendToToDelete;

        foreach ($messagessRelatedBySendToToDelete as $messagesRelatedBySendToRemoved) {
            $messagesRelatedBySendToRemoved->setPlayersRelatedBySendTo(null);
        }

        $this->collMessagessRelatedBySendTo = null;
        foreach ($messagessRelatedBySendTo as $messagesRelatedBySendTo) {
            $this->addMessagesRelatedBySendTo($messagesRelatedBySendTo);
        }

        $this->collMessagessRelatedBySendTo = $messagessRelatedBySendTo;
        $this->collMessagessRelatedBySendToPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Messages objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Messages objects.
     * @throws PropelException
     */
    public function countMessagessRelatedBySendTo(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collMessagessRelatedBySendToPartial && !$this->isNew();
        if (null === $this->collMessagessRelatedBySendTo || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMessagessRelatedBySendTo) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMessagessRelatedBySendTo());
            }
            $query = MessagesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayersRelatedBySendTo($this)
                ->count($con);
        }

        return count($this->collMessagessRelatedBySendTo);
    }

    /**
     * Method called to associate a Messages object to this object
     * through the Messages foreign key attribute.
     *
     * @param    Messages $l Messages
     * @return Players The current object (for fluent API support)
     */
    public function addMessagesRelatedBySendTo(Messages $l)
    {
        if ($this->collMessagessRelatedBySendTo === null) {
            $this->initMessagessRelatedBySendTo();
            $this->collMessagessRelatedBySendToPartial = true;
        }
        if (!in_array($l, $this->collMessagessRelatedBySendTo->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMessagesRelatedBySendTo($l);
        }

        return $this;
    }

    /**
     * @param	MessagesRelatedBySendTo $messagesRelatedBySendTo The messagesRelatedBySendTo object to add.
     */
    protected function doAddMessagesRelatedBySendTo($messagesRelatedBySendTo)
    {
        $this->collMessagessRelatedBySendTo[]= $messagesRelatedBySendTo;
        $messagesRelatedBySendTo->setPlayersRelatedBySendTo($this);
    }

    /**
     * @param	MessagesRelatedBySendTo $messagesRelatedBySendTo The messagesRelatedBySendTo object to remove.
     * @return Players The current object (for fluent API support)
     */
    public function removeMessagesRelatedBySendTo($messagesRelatedBySendTo)
    {
        if ($this->getMessagessRelatedBySendTo()->contains($messagesRelatedBySendTo)) {
            $this->collMessagessRelatedBySendTo->remove($this->collMessagessRelatedBySendTo->search($messagesRelatedBySendTo));
            if (null === $this->messagessRelatedBySendToScheduledForDeletion) {
                $this->messagessRelatedBySendToScheduledForDeletion = clone $this->collMessagessRelatedBySendTo;
                $this->messagessRelatedBySendToScheduledForDeletion->clear();
            }
            $this->messagessRelatedBySendToScheduledForDeletion[]= $messagesRelatedBySendTo;
            $messagesRelatedBySendTo->setPlayersRelatedBySendTo(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->player_id = null;
        $this->uname = null;
        $this->pname_backup = null;
        $this->health = null;
        $this->strength = null;
        $this->gold = null;
        $this->messages = null;
        $this->kills = null;
        $this->turns = null;
        $this->verification_number = null;
        $this->active = null;
        $this->email = null;
        $this->level = null;
        $this->status = null;
        $this->member = null;
        $this->days = null;
        $this->ip = null;
        $this->bounty = null;
        $this->created_date = null;
        $this->resurrection_time = null;
        $this->last_started_attack = null;
        $this->energy = null;
        $this->avatar_type = null;
        $this->_class_id = null;
        $this->ki = null;
        $this->stamina = null;
        $this->speed = null;
        $this->karma = null;
        $this->kills_gained = null;
        $this->kills_used = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collAccountPlayerss) {
                foreach ($this->collAccountPlayerss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collClanPlayers) {
                foreach ($this->collClanPlayers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collEnemiessRelatedByEnemyId) {
                foreach ($this->collEnemiessRelatedByEnemyId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collEnemiessRelatedByPlayerId) {
                foreach ($this->collEnemiessRelatedByPlayerId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collInventorys) {
                foreach ($this->collInventorys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collLevellingLogs) {
                foreach ($this->collLevellingLogs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMessagessRelatedBySendFrom) {
                foreach ($this->collMessagessRelatedBySendFrom as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMessagessRelatedBySendTo) {
                foreach ($this->collMessagessRelatedBySendTo as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aClass instanceof Persistent) {
              $this->aClass->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAccountPlayerss instanceof PropelCollection) {
            $this->collAccountPlayerss->clearIterator();
        }
        $this->collAccountPlayerss = null;
        if ($this->collClanPlayers instanceof PropelCollection) {
            $this->collClanPlayers->clearIterator();
        }
        $this->collClanPlayers = null;
        if ($this->collEnemiessRelatedByEnemyId instanceof PropelCollection) {
            $this->collEnemiessRelatedByEnemyId->clearIterator();
        }
        $this->collEnemiessRelatedByEnemyId = null;
        if ($this->collEnemiessRelatedByPlayerId instanceof PropelCollection) {
            $this->collEnemiessRelatedByPlayerId->clearIterator();
        }
        $this->collEnemiessRelatedByPlayerId = null;
        if ($this->collInventorys instanceof PropelCollection) {
            $this->collInventorys->clearIterator();
        }
        $this->collInventorys = null;
        if ($this->collLevellingLogs instanceof PropelCollection) {
            $this->collLevellingLogs->clearIterator();
        }
        $this->collLevellingLogs = null;
        if ($this->collMessagessRelatedBySendFrom instanceof PropelCollection) {
            $this->collMessagessRelatedBySendFrom->clearIterator();
        }
        $this->collMessagessRelatedBySendFrom = null;
        if ($this->collMessagessRelatedBySendTo instanceof PropelCollection) {
            $this->collMessagessRelatedBySendTo->clearIterator();
        }
        $this->collMessagessRelatedBySendTo = null;
        $this->aClass = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PlayersPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
