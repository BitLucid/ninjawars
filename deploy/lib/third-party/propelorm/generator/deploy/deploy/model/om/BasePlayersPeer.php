<?php

namespace deploy\model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use deploy\model\AccountPlayersPeer;
use deploy\model\ClanPlayerPeer;
use deploy\model\ClassPeer;
use deploy\model\EnemiesPeer;
use deploy\model\InventoryPeer;
use deploy\model\LevellingLogPeer;
use deploy\model\MessagesPeer;
use deploy\model\Players;
use deploy\model\PlayersPeer;
use deploy\model\map\PlayersTableMap;

/**
 * Base static class for performing query and update operations on the 'players' table.
 *
 *
 *
 * @package propel.generator.deploy.model.om
 */
abstract class BasePlayersPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'ninjawars';

    /** the table name for this class */
    const TABLE_NAME = 'players';

    /** the related Propel class for this table */
    const OM_CLASS = 'deploy\\model\\Players';

    /** the related TableMap class for this table */
    const TM_CLASS = 'PlayersTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 30;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 30;

    /** the column name for the player_id field */
    const PLAYER_ID = 'players.player_id';

    /** the column name for the uname field */
    const UNAME = 'players.uname';

    /** the column name for the pname_backup field */
    const PNAME_BACKUP = 'players.pname_backup';

    /** the column name for the health field */
    const HEALTH = 'players.health';

    /** the column name for the strength field */
    const STRENGTH = 'players.strength';

    /** the column name for the gold field */
    const GOLD = 'players.gold';

    /** the column name for the messages field */
    const MESSAGES = 'players.messages';

    /** the column name for the kills field */
    const KILLS = 'players.kills';

    /** the column name for the turns field */
    const TURNS = 'players.turns';

    /** the column name for the verification_number field */
    const VERIFICATION_NUMBER = 'players.verification_number';

    /** the column name for the active field */
    const ACTIVE = 'players.active';

    /** the column name for the email field */
    const EMAIL = 'players.email';

    /** the column name for the level field */
    const LEVEL = 'players.level';

    /** the column name for the status field */
    const STATUS = 'players.status';

    /** the column name for the member field */
    const MEMBER = 'players.member';

    /** the column name for the days field */
    const DAYS = 'players.days';

    /** the column name for the ip field */
    const IP = 'players.ip';

    /** the column name for the bounty field */
    const BOUNTY = 'players.bounty';

    /** the column name for the created_date field */
    const CREATED_DATE = 'players.created_date';

    /** the column name for the resurrection_time field */
    const RESURRECTION_TIME = 'players.resurrection_time';

    /** the column name for the last_started_attack field */
    const LAST_STARTED_ATTACK = 'players.last_started_attack';

    /** the column name for the energy field */
    const ENERGY = 'players.energy';

    /** the column name for the avatar_type field */
    const AVATAR_TYPE = 'players.avatar_type';

    /** the column name for the _class_id field */
    const _CLASS_ID = 'players._class_id';

    /** the column name for the ki field */
    const KI = 'players.ki';

    /** the column name for the stamina field */
    const STAMINA = 'players.stamina';

    /** the column name for the speed field */
    const SPEED = 'players.speed';

    /** the column name for the karma field */
    const KARMA = 'players.karma';

    /** the column name for the kills_gained field */
    const KILLS_GAINED = 'players.kills_gained';

    /** the column name for the kills_used field */
    const KILLS_USED = 'players.kills_used';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Players objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Players[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. PlayersPeer::$fieldNames[PlayersPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('PlayerId', 'Uname', 'PnameBackup', 'Health', 'Strength', 'Gold', 'Messages', 'Kills', 'Turns', 'VerificationNumber', 'Active', 'Email', 'Level', 'Status', 'Member', 'Days', 'Ip', 'Bounty', 'CreatedDate', 'ResurrectionTime', 'LastStartedAttack', 'Energy', 'AvatarType', 'ClassId', 'Ki', 'Stamina', 'Speed', 'Karma', 'KillsGained', 'KillsUsed', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('playerId', 'uname', 'pnameBackup', 'health', 'strength', 'gold', 'messages', 'kills', 'turns', 'verificationNumber', 'active', 'email', 'level', 'status', 'member', 'days', 'ip', 'bounty', 'createdDate', 'resurrectionTime', 'lastStartedAttack', 'energy', 'avatarType', 'classId', 'ki', 'stamina', 'speed', 'karma', 'killsGained', 'killsUsed', ),
        BasePeer::TYPE_COLNAME => array (PlayersPeer::PLAYER_ID, PlayersPeer::UNAME, PlayersPeer::PNAME_BACKUP, PlayersPeer::HEALTH, PlayersPeer::STRENGTH, PlayersPeer::GOLD, PlayersPeer::MESSAGES, PlayersPeer::KILLS, PlayersPeer::TURNS, PlayersPeer::VERIFICATION_NUMBER, PlayersPeer::ACTIVE, PlayersPeer::EMAIL, PlayersPeer::LEVEL, PlayersPeer::STATUS, PlayersPeer::MEMBER, PlayersPeer::DAYS, PlayersPeer::IP, PlayersPeer::BOUNTY, PlayersPeer::CREATED_DATE, PlayersPeer::RESURRECTION_TIME, PlayersPeer::LAST_STARTED_ATTACK, PlayersPeer::ENERGY, PlayersPeer::AVATAR_TYPE, PlayersPeer::_CLASS_ID, PlayersPeer::KI, PlayersPeer::STAMINA, PlayersPeer::SPEED, PlayersPeer::KARMA, PlayersPeer::KILLS_GAINED, PlayersPeer::KILLS_USED, ),
        BasePeer::TYPE_RAW_COLNAME => array ('PLAYER_ID', 'UNAME', 'PNAME_BACKUP', 'HEALTH', 'STRENGTH', 'GOLD', 'MESSAGES', 'KILLS', 'TURNS', 'VERIFICATION_NUMBER', 'ACTIVE', 'EMAIL', 'LEVEL', 'STATUS', 'MEMBER', 'DAYS', 'IP', 'BOUNTY', 'CREATED_DATE', 'RESURRECTION_TIME', 'LAST_STARTED_ATTACK', 'ENERGY', 'AVATAR_TYPE', '_CLASS_ID', 'KI', 'STAMINA', 'SPEED', 'KARMA', 'KILLS_GAINED', 'KILLS_USED', ),
        BasePeer::TYPE_FIELDNAME => array ('player_id', 'uname', 'pname_backup', 'health', 'strength', 'gold', 'messages', 'kills', 'turns', 'verification_number', 'active', 'email', 'level', 'status', 'member', 'days', 'ip', 'bounty', 'created_date', 'resurrection_time', 'last_started_attack', 'energy', 'avatar_type', '_class_id', 'ki', 'stamina', 'speed', 'karma', 'kills_gained', 'kills_used', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. PlayersPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('PlayerId' => 0, 'Uname' => 1, 'PnameBackup' => 2, 'Health' => 3, 'Strength' => 4, 'Gold' => 5, 'Messages' => 6, 'Kills' => 7, 'Turns' => 8, 'VerificationNumber' => 9, 'Active' => 10, 'Email' => 11, 'Level' => 12, 'Status' => 13, 'Member' => 14, 'Days' => 15, 'Ip' => 16, 'Bounty' => 17, 'CreatedDate' => 18, 'ResurrectionTime' => 19, 'LastStartedAttack' => 20, 'Energy' => 21, 'AvatarType' => 22, 'ClassId' => 23, 'Ki' => 24, 'Stamina' => 25, 'Speed' => 26, 'Karma' => 27, 'KillsGained' => 28, 'KillsUsed' => 29, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('playerId' => 0, 'uname' => 1, 'pnameBackup' => 2, 'health' => 3, 'strength' => 4, 'gold' => 5, 'messages' => 6, 'kills' => 7, 'turns' => 8, 'verificationNumber' => 9, 'active' => 10, 'email' => 11, 'level' => 12, 'status' => 13, 'member' => 14, 'days' => 15, 'ip' => 16, 'bounty' => 17, 'createdDate' => 18, 'resurrectionTime' => 19, 'lastStartedAttack' => 20, 'energy' => 21, 'avatarType' => 22, 'classId' => 23, 'ki' => 24, 'stamina' => 25, 'speed' => 26, 'karma' => 27, 'killsGained' => 28, 'killsUsed' => 29, ),
        BasePeer::TYPE_COLNAME => array (PlayersPeer::PLAYER_ID => 0, PlayersPeer::UNAME => 1, PlayersPeer::PNAME_BACKUP => 2, PlayersPeer::HEALTH => 3, PlayersPeer::STRENGTH => 4, PlayersPeer::GOLD => 5, PlayersPeer::MESSAGES => 6, PlayersPeer::KILLS => 7, PlayersPeer::TURNS => 8, PlayersPeer::VERIFICATION_NUMBER => 9, PlayersPeer::ACTIVE => 10, PlayersPeer::EMAIL => 11, PlayersPeer::LEVEL => 12, PlayersPeer::STATUS => 13, PlayersPeer::MEMBER => 14, PlayersPeer::DAYS => 15, PlayersPeer::IP => 16, PlayersPeer::BOUNTY => 17, PlayersPeer::CREATED_DATE => 18, PlayersPeer::RESURRECTION_TIME => 19, PlayersPeer::LAST_STARTED_ATTACK => 20, PlayersPeer::ENERGY => 21, PlayersPeer::AVATAR_TYPE => 22, PlayersPeer::_CLASS_ID => 23, PlayersPeer::KI => 24, PlayersPeer::STAMINA => 25, PlayersPeer::SPEED => 26, PlayersPeer::KARMA => 27, PlayersPeer::KILLS_GAINED => 28, PlayersPeer::KILLS_USED => 29, ),
        BasePeer::TYPE_RAW_COLNAME => array ('PLAYER_ID' => 0, 'UNAME' => 1, 'PNAME_BACKUP' => 2, 'HEALTH' => 3, 'STRENGTH' => 4, 'GOLD' => 5, 'MESSAGES' => 6, 'KILLS' => 7, 'TURNS' => 8, 'VERIFICATION_NUMBER' => 9, 'ACTIVE' => 10, 'EMAIL' => 11, 'LEVEL' => 12, 'STATUS' => 13, 'MEMBER' => 14, 'DAYS' => 15, 'IP' => 16, 'BOUNTY' => 17, 'CREATED_DATE' => 18, 'RESURRECTION_TIME' => 19, 'LAST_STARTED_ATTACK' => 20, 'ENERGY' => 21, 'AVATAR_TYPE' => 22, '_CLASS_ID' => 23, 'KI' => 24, 'STAMINA' => 25, 'SPEED' => 26, 'KARMA' => 27, 'KILLS_GAINED' => 28, 'KILLS_USED' => 29, ),
        BasePeer::TYPE_FIELDNAME => array ('player_id' => 0, 'uname' => 1, 'pname_backup' => 2, 'health' => 3, 'strength' => 4, 'gold' => 5, 'messages' => 6, 'kills' => 7, 'turns' => 8, 'verification_number' => 9, 'active' => 10, 'email' => 11, 'level' => 12, 'status' => 13, 'member' => 14, 'days' => 15, 'ip' => 16, 'bounty' => 17, 'created_date' => 18, 'resurrection_time' => 19, 'last_started_attack' => 20, 'energy' => 21, 'avatar_type' => 22, '_class_id' => 23, 'ki' => 24, 'stamina' => 25, 'speed' => 26, 'karma' => 27, 'kills_gained' => 28, 'kills_used' => 29, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = PlayersPeer::getFieldNames($toType);
        $key = isset(PlayersPeer::$fieldKeys[$fromType][$name]) ? PlayersPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(PlayersPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, PlayersPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return PlayersPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. PlayersPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(PlayersPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(PlayersPeer::PLAYER_ID);
            $criteria->addSelectColumn(PlayersPeer::UNAME);
            $criteria->addSelectColumn(PlayersPeer::PNAME_BACKUP);
            $criteria->addSelectColumn(PlayersPeer::HEALTH);
            $criteria->addSelectColumn(PlayersPeer::STRENGTH);
            $criteria->addSelectColumn(PlayersPeer::GOLD);
            $criteria->addSelectColumn(PlayersPeer::MESSAGES);
            $criteria->addSelectColumn(PlayersPeer::KILLS);
            $criteria->addSelectColumn(PlayersPeer::TURNS);
            $criteria->addSelectColumn(PlayersPeer::VERIFICATION_NUMBER);
            $criteria->addSelectColumn(PlayersPeer::ACTIVE);
            $criteria->addSelectColumn(PlayersPeer::EMAIL);
            $criteria->addSelectColumn(PlayersPeer::LEVEL);
            $criteria->addSelectColumn(PlayersPeer::STATUS);
            $criteria->addSelectColumn(PlayersPeer::MEMBER);
            $criteria->addSelectColumn(PlayersPeer::DAYS);
            $criteria->addSelectColumn(PlayersPeer::IP);
            $criteria->addSelectColumn(PlayersPeer::BOUNTY);
            $criteria->addSelectColumn(PlayersPeer::CREATED_DATE);
            $criteria->addSelectColumn(PlayersPeer::RESURRECTION_TIME);
            $criteria->addSelectColumn(PlayersPeer::LAST_STARTED_ATTACK);
            $criteria->addSelectColumn(PlayersPeer::ENERGY);
            $criteria->addSelectColumn(PlayersPeer::AVATAR_TYPE);
            $criteria->addSelectColumn(PlayersPeer::_CLASS_ID);
            $criteria->addSelectColumn(PlayersPeer::KI);
            $criteria->addSelectColumn(PlayersPeer::STAMINA);
            $criteria->addSelectColumn(PlayersPeer::SPEED);
            $criteria->addSelectColumn(PlayersPeer::KARMA);
            $criteria->addSelectColumn(PlayersPeer::KILLS_GAINED);
            $criteria->addSelectColumn(PlayersPeer::KILLS_USED);
        } else {
            $criteria->addSelectColumn($alias . '.player_id');
            $criteria->addSelectColumn($alias . '.uname');
            $criteria->addSelectColumn($alias . '.pname_backup');
            $criteria->addSelectColumn($alias . '.health');
            $criteria->addSelectColumn($alias . '.strength');
            $criteria->addSelectColumn($alias . '.gold');
            $criteria->addSelectColumn($alias . '.messages');
            $criteria->addSelectColumn($alias . '.kills');
            $criteria->addSelectColumn($alias . '.turns');
            $criteria->addSelectColumn($alias . '.verification_number');
            $criteria->addSelectColumn($alias . '.active');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.level');
            $criteria->addSelectColumn($alias . '.status');
            $criteria->addSelectColumn($alias . '.member');
            $criteria->addSelectColumn($alias . '.days');
            $criteria->addSelectColumn($alias . '.ip');
            $criteria->addSelectColumn($alias . '.bounty');
            $criteria->addSelectColumn($alias . '.created_date');
            $criteria->addSelectColumn($alias . '.resurrection_time');
            $criteria->addSelectColumn($alias . '.last_started_attack');
            $criteria->addSelectColumn($alias . '.energy');
            $criteria->addSelectColumn($alias . '.avatar_type');
            $criteria->addSelectColumn($alias . '._class_id');
            $criteria->addSelectColumn($alias . '.ki');
            $criteria->addSelectColumn($alias . '.stamina');
            $criteria->addSelectColumn($alias . '.speed');
            $criteria->addSelectColumn($alias . '.karma');
            $criteria->addSelectColumn($alias . '.kills_gained');
            $criteria->addSelectColumn($alias . '.kills_used');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PlayersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PlayersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(PlayersPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Players
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = PlayersPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return PlayersPeer::populateObjects(PlayersPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            PlayersPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Players $obj A Players object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getPlayerId();
            } // if key === null
            PlayersPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Players object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Players) {
                $key = (string) $value->getPlayerId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Players object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(PlayersPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Players Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(PlayersPeer::$instances[$key])) {
                return PlayersPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (PlayersPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        PlayersPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to players
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in AccountPlayersPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        AccountPlayersPeer::clearInstancePool();
        // Invalidate objects in ClanPlayerPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ClanPlayerPeer::clearInstancePool();
        // Invalidate objects in EnemiesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        EnemiesPeer::clearInstancePool();
        // Invalidate objects in EnemiesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        EnemiesPeer::clearInstancePool();
        // Invalidate objects in InventoryPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        InventoryPeer::clearInstancePool();
        // Invalidate objects in LevellingLogPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        LevellingLogPeer::clearInstancePool();
        // Invalidate objects in MessagesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        MessagesPeer::clearInstancePool();
        // Invalidate objects in MessagesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        MessagesPeer::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = PlayersPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = PlayersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = PlayersPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PlayersPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Players object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = PlayersPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = PlayersPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + PlayersPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PlayersPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            PlayersPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Class table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinClass(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PlayersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PlayersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PlayersPeer::_CLASS_ID, ClassPeer::CLASS_ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Players objects pre-filled with their Class objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Players objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinClass(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PlayersPeer::DATABASE_NAME);
        }

        PlayersPeer::addSelectColumns($criteria);
        $startcol = PlayersPeer::NUM_HYDRATE_COLUMNS;
        ClassPeer::addSelectColumns($criteria);

        $criteria->addJoin(PlayersPeer::_CLASS_ID, ClassPeer::CLASS_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PlayersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PlayersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = PlayersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PlayersPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = ClassPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = ClassPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ClassPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    ClassPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Players) to $obj2 (Class)
                $obj2->addPlayers($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PlayersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PlayersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PlayersPeer::_CLASS_ID, ClassPeer::CLASS_ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Players objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Players objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PlayersPeer::DATABASE_NAME);
        }

        PlayersPeer::addSelectColumns($criteria);
        $startcol2 = PlayersPeer::NUM_HYDRATE_COLUMNS;

        ClassPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ClassPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(PlayersPeer::_CLASS_ID, ClassPeer::CLASS_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PlayersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PlayersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = PlayersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PlayersPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Class rows

            $key2 = ClassPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = ClassPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ClassPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ClassPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Players) to the collection in $obj2 (Class)
                $obj2->addPlayers($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(PlayersPeer::DATABASE_NAME)->getTable(PlayersPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BasePlayersPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BasePlayersPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new PlayersTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return PlayersPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Players or Criteria object.
     *
     * @param      mixed $values Criteria or Players object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Players object
        }

        if ($criteria->containsKey(PlayersPeer::PLAYER_ID) && $criteria->keyContainsValue(PlayersPeer::PLAYER_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PlayersPeer::PLAYER_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Players or Criteria object.
     *
     * @param      mixed $values Criteria or Players object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(PlayersPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(PlayersPeer::PLAYER_ID);
            $value = $criteria->remove(PlayersPeer::PLAYER_ID);
            if ($value) {
                $selectCriteria->add(PlayersPeer::PLAYER_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(PlayersPeer::TABLE_NAME);
            }

        } else { // $values is Players object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the players table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(PlayersPeer::TABLE_NAME, $con, PlayersPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PlayersPeer::clearInstancePool();
            PlayersPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Players or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Players object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            PlayersPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Players) { // it's a model object
            // invalidate the cache for this single object
            PlayersPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PlayersPeer::DATABASE_NAME);
            $criteria->add(PlayersPeer::PLAYER_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                PlayersPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(PlayersPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            PlayersPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Players object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Players $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(PlayersPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(PlayersPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(PlayersPeer::DATABASE_NAME, PlayersPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Players
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = PlayersPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(PlayersPeer::DATABASE_NAME);
        $criteria->add(PlayersPeer::PLAYER_ID, $pk);

        $v = PlayersPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Players[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(PlayersPeer::DATABASE_NAME);
            $criteria->add(PlayersPeer::PLAYER_ID, $pks, Criteria::IN);
            $objs = PlayersPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BasePlayersPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasePlayersPeer::buildTableMap();

