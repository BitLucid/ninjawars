<?php

namespace deploy\model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use deploy\model\AccountPlayers;
use deploy\model\ClanPlayer;
use deploy\model\Class;
use deploy\model\Enemies;
use deploy\model\Inventory;
use deploy\model\LevellingLog;
use deploy\model\Messages;
use deploy\model\Players;
use deploy\model\PlayersPeer;
use deploy\model\PlayersQuery;

/**
 * Base class that represents a query for the 'players' table.
 *
 *
 *
 * @method PlayersQuery orderByPlayerId($order = Criteria::ASC) Order by the player_id column
 * @method PlayersQuery orderByUname($order = Criteria::ASC) Order by the uname column
 * @method PlayersQuery orderByPnameBackup($order = Criteria::ASC) Order by the pname_backup column
 * @method PlayersQuery orderByHealth($order = Criteria::ASC) Order by the health column
 * @method PlayersQuery orderByStrength($order = Criteria::ASC) Order by the strength column
 * @method PlayersQuery orderByGold($order = Criteria::ASC) Order by the gold column
 * @method PlayersQuery orderByMessages($order = Criteria::ASC) Order by the messages column
 * @method PlayersQuery orderByKills($order = Criteria::ASC) Order by the kills column
 * @method PlayersQuery orderByTurns($order = Criteria::ASC) Order by the turns column
 * @method PlayersQuery orderByVerificationNumber($order = Criteria::ASC) Order by the verification_number column
 * @method PlayersQuery orderByActive($order = Criteria::ASC) Order by the active column
 * @method PlayersQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method PlayersQuery orderByLevel($order = Criteria::ASC) Order by the level column
 * @method PlayersQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method PlayersQuery orderByMember($order = Criteria::ASC) Order by the member column
 * @method PlayersQuery orderByDays($order = Criteria::ASC) Order by the days column
 * @method PlayersQuery orderByIp($order = Criteria::ASC) Order by the ip column
 * @method PlayersQuery orderByBounty($order = Criteria::ASC) Order by the bounty column
 * @method PlayersQuery orderByCreatedDate($order = Criteria::ASC) Order by the created_date column
 * @method PlayersQuery orderByResurrectionTime($order = Criteria::ASC) Order by the resurrection_time column
 * @method PlayersQuery orderByLastStartedAttack($order = Criteria::ASC) Order by the last_started_attack column
 * @method PlayersQuery orderByEnergy($order = Criteria::ASC) Order by the energy column
 * @method PlayersQuery orderByAvatarType($order = Criteria::ASC) Order by the avatar_type column
 * @method PlayersQuery orderByClassId($order = Criteria::ASC) Order by the _class_id column
 * @method PlayersQuery orderByKi($order = Criteria::ASC) Order by the ki column
 * @method PlayersQuery orderByStamina($order = Criteria::ASC) Order by the stamina column
 * @method PlayersQuery orderBySpeed($order = Criteria::ASC) Order by the speed column
 * @method PlayersQuery orderByKarma($order = Criteria::ASC) Order by the karma column
 * @method PlayersQuery orderByKillsGained($order = Criteria::ASC) Order by the kills_gained column
 * @method PlayersQuery orderByKillsUsed($order = Criteria::ASC) Order by the kills_used column
 *
 * @method PlayersQuery groupByPlayerId() Group by the player_id column
 * @method PlayersQuery groupByUname() Group by the uname column
 * @method PlayersQuery groupByPnameBackup() Group by the pname_backup column
 * @method PlayersQuery groupByHealth() Group by the health column
 * @method PlayersQuery groupByStrength() Group by the strength column
 * @method PlayersQuery groupByGold() Group by the gold column
 * @method PlayersQuery groupByMessages() Group by the messages column
 * @method PlayersQuery groupByKills() Group by the kills column
 * @method PlayersQuery groupByTurns() Group by the turns column
 * @method PlayersQuery groupByVerificationNumber() Group by the verification_number column
 * @method PlayersQuery groupByActive() Group by the active column
 * @method PlayersQuery groupByEmail() Group by the email column
 * @method PlayersQuery groupByLevel() Group by the level column
 * @method PlayersQuery groupByStatus() Group by the status column
 * @method PlayersQuery groupByMember() Group by the member column
 * @method PlayersQuery groupByDays() Group by the days column
 * @method PlayersQuery groupByIp() Group by the ip column
 * @method PlayersQuery groupByBounty() Group by the bounty column
 * @method PlayersQuery groupByCreatedDate() Group by the created_date column
 * @method PlayersQuery groupByResurrectionTime() Group by the resurrection_time column
 * @method PlayersQuery groupByLastStartedAttack() Group by the last_started_attack column
 * @method PlayersQuery groupByEnergy() Group by the energy column
 * @method PlayersQuery groupByAvatarType() Group by the avatar_type column
 * @method PlayersQuery groupByClassId() Group by the _class_id column
 * @method PlayersQuery groupByKi() Group by the ki column
 * @method PlayersQuery groupByStamina() Group by the stamina column
 * @method PlayersQuery groupBySpeed() Group by the speed column
 * @method PlayersQuery groupByKarma() Group by the karma column
 * @method PlayersQuery groupByKillsGained() Group by the kills_gained column
 * @method PlayersQuery groupByKillsUsed() Group by the kills_used column
 *
 * @method PlayersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlayersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlayersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlayersQuery leftJoinClass($relationAlias = null) Adds a LEFT JOIN clause to the query using the Class relation
 * @method PlayersQuery rightJoinClass($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Class relation
 * @method PlayersQuery innerJoinClass($relationAlias = null) Adds a INNER JOIN clause to the query using the Class relation
 *
 * @method PlayersQuery leftJoinAccountPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountPlayers relation
 * @method PlayersQuery rightJoinAccountPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountPlayers relation
 * @method PlayersQuery innerJoinAccountPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountPlayers relation
 *
 * @method PlayersQuery leftJoinClanPlayer($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClanPlayer relation
 * @method PlayersQuery rightJoinClanPlayer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClanPlayer relation
 * @method PlayersQuery innerJoinClanPlayer($relationAlias = null) Adds a INNER JOIN clause to the query using the ClanPlayer relation
 *
 * @method PlayersQuery leftJoinEnemiesRelatedByEnemyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the EnemiesRelatedByEnemyId relation
 * @method PlayersQuery rightJoinEnemiesRelatedByEnemyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EnemiesRelatedByEnemyId relation
 * @method PlayersQuery innerJoinEnemiesRelatedByEnemyId($relationAlias = null) Adds a INNER JOIN clause to the query using the EnemiesRelatedByEnemyId relation
 *
 * @method PlayersQuery leftJoinEnemiesRelatedByPlayerId($relationAlias = null) Adds a LEFT JOIN clause to the query using the EnemiesRelatedByPlayerId relation
 * @method PlayersQuery rightJoinEnemiesRelatedByPlayerId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EnemiesRelatedByPlayerId relation
 * @method PlayersQuery innerJoinEnemiesRelatedByPlayerId($relationAlias = null) Adds a INNER JOIN clause to the query using the EnemiesRelatedByPlayerId relation
 *
 * @method PlayersQuery leftJoinInventory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Inventory relation
 * @method PlayersQuery rightJoinInventory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Inventory relation
 * @method PlayersQuery innerJoinInventory($relationAlias = null) Adds a INNER JOIN clause to the query using the Inventory relation
 *
 * @method PlayersQuery leftJoinLevellingLog($relationAlias = null) Adds a LEFT JOIN clause to the query using the LevellingLog relation
 * @method PlayersQuery rightJoinLevellingLog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LevellingLog relation
 * @method PlayersQuery innerJoinLevellingLog($relationAlias = null) Adds a INNER JOIN clause to the query using the LevellingLog relation
 *
 * @method PlayersQuery leftJoinMessagesRelatedBySendFrom($relationAlias = null) Adds a LEFT JOIN clause to the query using the MessagesRelatedBySendFrom relation
 * @method PlayersQuery rightJoinMessagesRelatedBySendFrom($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MessagesRelatedBySendFrom relation
 * @method PlayersQuery innerJoinMessagesRelatedBySendFrom($relationAlias = null) Adds a INNER JOIN clause to the query using the MessagesRelatedBySendFrom relation
 *
 * @method PlayersQuery leftJoinMessagesRelatedBySendTo($relationAlias = null) Adds a LEFT JOIN clause to the query using the MessagesRelatedBySendTo relation
 * @method PlayersQuery rightJoinMessagesRelatedBySendTo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MessagesRelatedBySendTo relation
 * @method PlayersQuery innerJoinMessagesRelatedBySendTo($relationAlias = null) Adds a INNER JOIN clause to the query using the MessagesRelatedBySendTo relation
 *
 * @method Players findOne(PropelPDO $con = null) Return the first Players matching the query
 * @method Players findOneOrCreate(PropelPDO $con = null) Return the first Players matching the query, or a new Players object populated from the query conditions when no match is found
 *
 * @method Players findOneByUname(string $uname) Return the first Players filtered by the uname column
 * @method Players findOneByPnameBackup(string $pname_backup) Return the first Players filtered by the pname_backup column
 * @method Players findOneByHealth(int $health) Return the first Players filtered by the health column
 * @method Players findOneByStrength(int $strength) Return the first Players filtered by the strength column
 * @method Players findOneByGold(int $gold) Return the first Players filtered by the gold column
 * @method Players findOneByMessages(string $messages) Return the first Players filtered by the messages column
 * @method Players findOneByKills(int $kills) Return the first Players filtered by the kills column
 * @method Players findOneByTurns(int $turns) Return the first Players filtered by the turns column
 * @method Players findOneByVerificationNumber(int $verification_number) Return the first Players filtered by the verification_number column
 * @method Players findOneByActive(int $active) Return the first Players filtered by the active column
 * @method Players findOneByEmail(string $email) Return the first Players filtered by the email column
 * @method Players findOneByLevel(int $level) Return the first Players filtered by the level column
 * @method Players findOneByStatus(int $status) Return the first Players filtered by the status column
 * @method Players findOneByMember(int $member) Return the first Players filtered by the member column
 * @method Players findOneByDays(int $days) Return the first Players filtered by the days column
 * @method Players findOneByIp(string $ip) Return the first Players filtered by the ip column
 * @method Players findOneByBounty(int $bounty) Return the first Players filtered by the bounty column
 * @method Players findOneByCreatedDate(string $created_date) Return the first Players filtered by the created_date column
 * @method Players findOneByResurrectionTime(int $resurrection_time) Return the first Players filtered by the resurrection_time column
 * @method Players findOneByLastStartedAttack(string $last_started_attack) Return the first Players filtered by the last_started_attack column
 * @method Players findOneByEnergy(int $energy) Return the first Players filtered by the energy column
 * @method Players findOneByAvatarType(int $avatar_type) Return the first Players filtered by the avatar_type column
 * @method Players findOneByClassId(int $_class_id) Return the first Players filtered by the _class_id column
 * @method Players findOneByKi(int $ki) Return the first Players filtered by the ki column
 * @method Players findOneByStamina(int $stamina) Return the first Players filtered by the stamina column
 * @method Players findOneBySpeed(int $speed) Return the first Players filtered by the speed column
 * @method Players findOneByKarma(int $karma) Return the first Players filtered by the karma column
 * @method Players findOneByKillsGained(int $kills_gained) Return the first Players filtered by the kills_gained column
 * @method Players findOneByKillsUsed(int $kills_used) Return the first Players filtered by the kills_used column
 *
 * @method array findByPlayerId(int $player_id) Return Players objects filtered by the player_id column
 * @method array findByUname(string $uname) Return Players objects filtered by the uname column
 * @method array findByPnameBackup(string $pname_backup) Return Players objects filtered by the pname_backup column
 * @method array findByHealth(int $health) Return Players objects filtered by the health column
 * @method array findByStrength(int $strength) Return Players objects filtered by the strength column
 * @method array findByGold(int $gold) Return Players objects filtered by the gold column
 * @method array findByMessages(string $messages) Return Players objects filtered by the messages column
 * @method array findByKills(int $kills) Return Players objects filtered by the kills column
 * @method array findByTurns(int $turns) Return Players objects filtered by the turns column
 * @method array findByVerificationNumber(int $verification_number) Return Players objects filtered by the verification_number column
 * @method array findByActive(int $active) Return Players objects filtered by the active column
 * @method array findByEmail(string $email) Return Players objects filtered by the email column
 * @method array findByLevel(int $level) Return Players objects filtered by the level column
 * @method array findByStatus(int $status) Return Players objects filtered by the status column
 * @method array findByMember(int $member) Return Players objects filtered by the member column
 * @method array findByDays(int $days) Return Players objects filtered by the days column
 * @method array findByIp(string $ip) Return Players objects filtered by the ip column
 * @method array findByBounty(int $bounty) Return Players objects filtered by the bounty column
 * @method array findByCreatedDate(string $created_date) Return Players objects filtered by the created_date column
 * @method array findByResurrectionTime(int $resurrection_time) Return Players objects filtered by the resurrection_time column
 * @method array findByLastStartedAttack(string $last_started_attack) Return Players objects filtered by the last_started_attack column
 * @method array findByEnergy(int $energy) Return Players objects filtered by the energy column
 * @method array findByAvatarType(int $avatar_type) Return Players objects filtered by the avatar_type column
 * @method array findByClassId(int $_class_id) Return Players objects filtered by the _class_id column
 * @method array findByKi(int $ki) Return Players objects filtered by the ki column
 * @method array findByStamina(int $stamina) Return Players objects filtered by the stamina column
 * @method array findBySpeed(int $speed) Return Players objects filtered by the speed column
 * @method array findByKarma(int $karma) Return Players objects filtered by the karma column
 * @method array findByKillsGained(int $kills_gained) Return Players objects filtered by the kills_gained column
 * @method array findByKillsUsed(int $kills_used) Return Players objects filtered by the kills_used column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePlayersQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlayersQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'ninjawars';
        }
        if (null === $modelName) {
            $modelName = 'deploy\\model\\Players';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlayersQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlayersQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlayersQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlayersQuery) {
            return $criteria;
        }
        $query = new PlayersQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Players|Players[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlayersPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Players A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPlayerId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Players A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "player_id", "uname", "pname_backup", "health", "strength", "gold", "messages", "kills", "turns", "verification_number", "active", "email", "level", "status", "member", "days", "ip", "bounty", "created_date", "resurrection_time", "last_started_attack", "energy", "avatar_type", "_class_id", "ki", "stamina", "speed", "karma", "kills_gained", "kills_used" FROM "players" WHERE "player_id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Players();
            $obj->hydrate($row);
            PlayersPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Players|Players[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Players[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlayersPeer::PLAYER_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlayersPeer::PLAYER_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the player_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerId(1234); // WHERE player_id = 1234
     * $query->filterByPlayerId(array(12, 34)); // WHERE player_id IN (12, 34)
     * $query->filterByPlayerId(array('min' => 12)); // WHERE player_id >= 12
     * $query->filterByPlayerId(array('max' => 12)); // WHERE player_id <= 12
     * </code>
     *
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(PlayersPeer::PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(PlayersPeer::PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the uname column
     *
     * Example usage:
     * <code>
     * $query->filterByUname('fooValue');   // WHERE uname = 'fooValue'
     * $query->filterByUname('%fooValue%'); // WHERE uname LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uname The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByUname($uname = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uname)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $uname)) {
                $uname = str_replace('*', '%', $uname);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::UNAME, $uname, $comparison);
    }

    /**
     * Filter the query on the pname_backup column
     *
     * Example usage:
     * <code>
     * $query->filterByPnameBackup('fooValue');   // WHERE pname_backup = 'fooValue'
     * $query->filterByPnameBackup('%fooValue%'); // WHERE pname_backup LIKE '%fooValue%'
     * </code>
     *
     * @param     string $pnameBackup The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPnameBackup($pnameBackup = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pnameBackup)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $pnameBackup)) {
                $pnameBackup = str_replace('*', '%', $pnameBackup);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::PNAME_BACKUP, $pnameBackup, $comparison);
    }

    /**
     * Filter the query on the health column
     *
     * Example usage:
     * <code>
     * $query->filterByHealth(1234); // WHERE health = 1234
     * $query->filterByHealth(array(12, 34)); // WHERE health IN (12, 34)
     * $query->filterByHealth(array('min' => 12)); // WHERE health >= 12
     * $query->filterByHealth(array('max' => 12)); // WHERE health <= 12
     * </code>
     *
     * @param     mixed $health The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByHealth($health = null, $comparison = null)
    {
        if (is_array($health)) {
            $useMinMax = false;
            if (isset($health['min'])) {
                $this->addUsingAlias(PlayersPeer::HEALTH, $health['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($health['max'])) {
                $this->addUsingAlias(PlayersPeer::HEALTH, $health['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::HEALTH, $health, $comparison);
    }

    /**
     * Filter the query on the strength column
     *
     * Example usage:
     * <code>
     * $query->filterByStrength(1234); // WHERE strength = 1234
     * $query->filterByStrength(array(12, 34)); // WHERE strength IN (12, 34)
     * $query->filterByStrength(array('min' => 12)); // WHERE strength >= 12
     * $query->filterByStrength(array('max' => 12)); // WHERE strength <= 12
     * </code>
     *
     * @param     mixed $strength The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByStrength($strength = null, $comparison = null)
    {
        if (is_array($strength)) {
            $useMinMax = false;
            if (isset($strength['min'])) {
                $this->addUsingAlias(PlayersPeer::STRENGTH, $strength['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($strength['max'])) {
                $this->addUsingAlias(PlayersPeer::STRENGTH, $strength['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::STRENGTH, $strength, $comparison);
    }

    /**
     * Filter the query on the gold column
     *
     * Example usage:
     * <code>
     * $query->filterByGold(1234); // WHERE gold = 1234
     * $query->filterByGold(array(12, 34)); // WHERE gold IN (12, 34)
     * $query->filterByGold(array('min' => 12)); // WHERE gold >= 12
     * $query->filterByGold(array('max' => 12)); // WHERE gold <= 12
     * </code>
     *
     * @param     mixed $gold The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByGold($gold = null, $comparison = null)
    {
        if (is_array($gold)) {
            $useMinMax = false;
            if (isset($gold['min'])) {
                $this->addUsingAlias(PlayersPeer::GOLD, $gold['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gold['max'])) {
                $this->addUsingAlias(PlayersPeer::GOLD, $gold['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::GOLD, $gold, $comparison);
    }

    /**
     * Filter the query on the messages column
     *
     * Example usage:
     * <code>
     * $query->filterByMessages('fooValue');   // WHERE messages = 'fooValue'
     * $query->filterByMessages('%fooValue%'); // WHERE messages LIKE '%fooValue%'
     * </code>
     *
     * @param     string $messages The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByMessages($messages = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($messages)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $messages)) {
                $messages = str_replace('*', '%', $messages);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::MESSAGES, $messages, $comparison);
    }

    /**
     * Filter the query on the kills column
     *
     * Example usage:
     * <code>
     * $query->filterByKills(1234); // WHERE kills = 1234
     * $query->filterByKills(array(12, 34)); // WHERE kills IN (12, 34)
     * $query->filterByKills(array('min' => 12)); // WHERE kills >= 12
     * $query->filterByKills(array('max' => 12)); // WHERE kills <= 12
     * </code>
     *
     * @param     mixed $kills The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByKills($kills = null, $comparison = null)
    {
        if (is_array($kills)) {
            $useMinMax = false;
            if (isset($kills['min'])) {
                $this->addUsingAlias(PlayersPeer::KILLS, $kills['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($kills['max'])) {
                $this->addUsingAlias(PlayersPeer::KILLS, $kills['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::KILLS, $kills, $comparison);
    }

    /**
     * Filter the query on the turns column
     *
     * Example usage:
     * <code>
     * $query->filterByTurns(1234); // WHERE turns = 1234
     * $query->filterByTurns(array(12, 34)); // WHERE turns IN (12, 34)
     * $query->filterByTurns(array('min' => 12)); // WHERE turns >= 12
     * $query->filterByTurns(array('max' => 12)); // WHERE turns <= 12
     * </code>
     *
     * @param     mixed $turns The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByTurns($turns = null, $comparison = null)
    {
        if (is_array($turns)) {
            $useMinMax = false;
            if (isset($turns['min'])) {
                $this->addUsingAlias(PlayersPeer::TURNS, $turns['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($turns['max'])) {
                $this->addUsingAlias(PlayersPeer::TURNS, $turns['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::TURNS, $turns, $comparison);
    }

    /**
     * Filter the query on the verification_number column
     *
     * Example usage:
     * <code>
     * $query->filterByVerificationNumber(1234); // WHERE verification_number = 1234
     * $query->filterByVerificationNumber(array(12, 34)); // WHERE verification_number IN (12, 34)
     * $query->filterByVerificationNumber(array('min' => 12)); // WHERE verification_number >= 12
     * $query->filterByVerificationNumber(array('max' => 12)); // WHERE verification_number <= 12
     * </code>
     *
     * @param     mixed $verificationNumber The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByVerificationNumber($verificationNumber = null, $comparison = null)
    {
        if (is_array($verificationNumber)) {
            $useMinMax = false;
            if (isset($verificationNumber['min'])) {
                $this->addUsingAlias(PlayersPeer::VERIFICATION_NUMBER, $verificationNumber['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($verificationNumber['max'])) {
                $this->addUsingAlias(PlayersPeer::VERIFICATION_NUMBER, $verificationNumber['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::VERIFICATION_NUMBER, $verificationNumber, $comparison);
    }

    /**
     * Filter the query on the active column
     *
     * Example usage:
     * <code>
     * $query->filterByActive(1234); // WHERE active = 1234
     * $query->filterByActive(array(12, 34)); // WHERE active IN (12, 34)
     * $query->filterByActive(array('min' => 12)); // WHERE active >= 12
     * $query->filterByActive(array('max' => 12)); // WHERE active <= 12
     * </code>
     *
     * @param     mixed $active The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByActive($active = null, $comparison = null)
    {
        if (is_array($active)) {
            $useMinMax = false;
            if (isset($active['min'])) {
                $this->addUsingAlias(PlayersPeer::ACTIVE, $active['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($active['max'])) {
                $this->addUsingAlias(PlayersPeer::ACTIVE, $active['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::ACTIVE, $active, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the level column
     *
     * Example usage:
     * <code>
     * $query->filterByLevel(1234); // WHERE level = 1234
     * $query->filterByLevel(array(12, 34)); // WHERE level IN (12, 34)
     * $query->filterByLevel(array('min' => 12)); // WHERE level >= 12
     * $query->filterByLevel(array('max' => 12)); // WHERE level <= 12
     * </code>
     *
     * @param     mixed $level The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByLevel($level = null, $comparison = null)
    {
        if (is_array($level)) {
            $useMinMax = false;
            if (isset($level['min'])) {
                $this->addUsingAlias(PlayersPeer::LEVEL, $level['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($level['max'])) {
                $this->addUsingAlias(PlayersPeer::LEVEL, $level['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::LEVEL, $level, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus(1234); // WHERE status = 1234
     * $query->filterByStatus(array(12, 34)); // WHERE status IN (12, 34)
     * $query->filterByStatus(array('min' => 12)); // WHERE status >= 12
     * $query->filterByStatus(array('max' => 12)); // WHERE status <= 12
     * </code>
     *
     * @param     mixed $status The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (is_array($status)) {
            $useMinMax = false;
            if (isset($status['min'])) {
                $this->addUsingAlias(PlayersPeer::STATUS, $status['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($status['max'])) {
                $this->addUsingAlias(PlayersPeer::STATUS, $status['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the member column
     *
     * Example usage:
     * <code>
     * $query->filterByMember(1234); // WHERE member = 1234
     * $query->filterByMember(array(12, 34)); // WHERE member IN (12, 34)
     * $query->filterByMember(array('min' => 12)); // WHERE member >= 12
     * $query->filterByMember(array('max' => 12)); // WHERE member <= 12
     * </code>
     *
     * @param     mixed $member The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByMember($member = null, $comparison = null)
    {
        if (is_array($member)) {
            $useMinMax = false;
            if (isset($member['min'])) {
                $this->addUsingAlias(PlayersPeer::MEMBER, $member['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($member['max'])) {
                $this->addUsingAlias(PlayersPeer::MEMBER, $member['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::MEMBER, $member, $comparison);
    }

    /**
     * Filter the query on the days column
     *
     * Example usage:
     * <code>
     * $query->filterByDays(1234); // WHERE days = 1234
     * $query->filterByDays(array(12, 34)); // WHERE days IN (12, 34)
     * $query->filterByDays(array('min' => 12)); // WHERE days >= 12
     * $query->filterByDays(array('max' => 12)); // WHERE days <= 12
     * </code>
     *
     * @param     mixed $days The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByDays($days = null, $comparison = null)
    {
        if (is_array($days)) {
            $useMinMax = false;
            if (isset($days['min'])) {
                $this->addUsingAlias(PlayersPeer::DAYS, $days['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($days['max'])) {
                $this->addUsingAlias(PlayersPeer::DAYS, $days['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::DAYS, $days, $comparison);
    }

    /**
     * Filter the query on the ip column
     *
     * Example usage:
     * <code>
     * $query->filterByIp('fooValue');   // WHERE ip = 'fooValue'
     * $query->filterByIp('%fooValue%'); // WHERE ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ip The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByIp($ip = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ip)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ip)) {
                $ip = str_replace('*', '%', $ip);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::IP, $ip, $comparison);
    }

    /**
     * Filter the query on the bounty column
     *
     * Example usage:
     * <code>
     * $query->filterByBounty(1234); // WHERE bounty = 1234
     * $query->filterByBounty(array(12, 34)); // WHERE bounty IN (12, 34)
     * $query->filterByBounty(array('min' => 12)); // WHERE bounty >= 12
     * $query->filterByBounty(array('max' => 12)); // WHERE bounty <= 12
     * </code>
     *
     * @param     mixed $bounty The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByBounty($bounty = null, $comparison = null)
    {
        if (is_array($bounty)) {
            $useMinMax = false;
            if (isset($bounty['min'])) {
                $this->addUsingAlias(PlayersPeer::BOUNTY, $bounty['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($bounty['max'])) {
                $this->addUsingAlias(PlayersPeer::BOUNTY, $bounty['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::BOUNTY, $bounty, $comparison);
    }

    /**
     * Filter the query on the created_date column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedDate('2011-03-14'); // WHERE created_date = '2011-03-14'
     * $query->filterByCreatedDate('now'); // WHERE created_date = '2011-03-14'
     * $query->filterByCreatedDate(array('max' => 'yesterday')); // WHERE created_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByCreatedDate($createdDate = null, $comparison = null)
    {
        if (is_array($createdDate)) {
            $useMinMax = false;
            if (isset($createdDate['min'])) {
                $this->addUsingAlias(PlayersPeer::CREATED_DATE, $createdDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdDate['max'])) {
                $this->addUsingAlias(PlayersPeer::CREATED_DATE, $createdDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::CREATED_DATE, $createdDate, $comparison);
    }

    /**
     * Filter the query on the resurrection_time column
     *
     * Example usage:
     * <code>
     * $query->filterByResurrectionTime(1234); // WHERE resurrection_time = 1234
     * $query->filterByResurrectionTime(array(12, 34)); // WHERE resurrection_time IN (12, 34)
     * $query->filterByResurrectionTime(array('min' => 12)); // WHERE resurrection_time >= 12
     * $query->filterByResurrectionTime(array('max' => 12)); // WHERE resurrection_time <= 12
     * </code>
     *
     * @param     mixed $resurrectionTime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByResurrectionTime($resurrectionTime = null, $comparison = null)
    {
        if (is_array($resurrectionTime)) {
            $useMinMax = false;
            if (isset($resurrectionTime['min'])) {
                $this->addUsingAlias(PlayersPeer::RESURRECTION_TIME, $resurrectionTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($resurrectionTime['max'])) {
                $this->addUsingAlias(PlayersPeer::RESURRECTION_TIME, $resurrectionTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::RESURRECTION_TIME, $resurrectionTime, $comparison);
    }

    /**
     * Filter the query on the last_started_attack column
     *
     * Example usage:
     * <code>
     * $query->filterByLastStartedAttack('2011-03-14'); // WHERE last_started_attack = '2011-03-14'
     * $query->filterByLastStartedAttack('now'); // WHERE last_started_attack = '2011-03-14'
     * $query->filterByLastStartedAttack(array('max' => 'yesterday')); // WHERE last_started_attack > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastStartedAttack The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByLastStartedAttack($lastStartedAttack = null, $comparison = null)
    {
        if (is_array($lastStartedAttack)) {
            $useMinMax = false;
            if (isset($lastStartedAttack['min'])) {
                $this->addUsingAlias(PlayersPeer::LAST_STARTED_ATTACK, $lastStartedAttack['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastStartedAttack['max'])) {
                $this->addUsingAlias(PlayersPeer::LAST_STARTED_ATTACK, $lastStartedAttack['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::LAST_STARTED_ATTACK, $lastStartedAttack, $comparison);
    }

    /**
     * Filter the query on the energy column
     *
     * Example usage:
     * <code>
     * $query->filterByEnergy(1234); // WHERE energy = 1234
     * $query->filterByEnergy(array(12, 34)); // WHERE energy IN (12, 34)
     * $query->filterByEnergy(array('min' => 12)); // WHERE energy >= 12
     * $query->filterByEnergy(array('max' => 12)); // WHERE energy <= 12
     * </code>
     *
     * @param     mixed $energy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByEnergy($energy = null, $comparison = null)
    {
        if (is_array($energy)) {
            $useMinMax = false;
            if (isset($energy['min'])) {
                $this->addUsingAlias(PlayersPeer::ENERGY, $energy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($energy['max'])) {
                $this->addUsingAlias(PlayersPeer::ENERGY, $energy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::ENERGY, $energy, $comparison);
    }

    /**
     * Filter the query on the avatar_type column
     *
     * Example usage:
     * <code>
     * $query->filterByAvatarType(1234); // WHERE avatar_type = 1234
     * $query->filterByAvatarType(array(12, 34)); // WHERE avatar_type IN (12, 34)
     * $query->filterByAvatarType(array('min' => 12)); // WHERE avatar_type >= 12
     * $query->filterByAvatarType(array('max' => 12)); // WHERE avatar_type <= 12
     * </code>
     *
     * @param     mixed $avatarType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByAvatarType($avatarType = null, $comparison = null)
    {
        if (is_array($avatarType)) {
            $useMinMax = false;
            if (isset($avatarType['min'])) {
                $this->addUsingAlias(PlayersPeer::AVATAR_TYPE, $avatarType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($avatarType['max'])) {
                $this->addUsingAlias(PlayersPeer::AVATAR_TYPE, $avatarType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::AVATAR_TYPE, $avatarType, $comparison);
    }

    /**
     * Filter the query on the _class_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassId(1234); // WHERE _class_id = 1234
     * $query->filterByClassId(array(12, 34)); // WHERE _class_id IN (12, 34)
     * $query->filterByClassId(array('min' => 12)); // WHERE _class_id >= 12
     * $query->filterByClassId(array('max' => 12)); // WHERE _class_id <= 12
     * </code>
     *
     * @see       filterByClass()
     *
     * @param     mixed $classId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByClassId($classId = null, $comparison = null)
    {
        if (is_array($classId)) {
            $useMinMax = false;
            if (isset($classId['min'])) {
                $this->addUsingAlias(PlayersPeer::_CLASS_ID, $classId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classId['max'])) {
                $this->addUsingAlias(PlayersPeer::_CLASS_ID, $classId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::_CLASS_ID, $classId, $comparison);
    }

    /**
     * Filter the query on the ki column
     *
     * Example usage:
     * <code>
     * $query->filterByKi(1234); // WHERE ki = 1234
     * $query->filterByKi(array(12, 34)); // WHERE ki IN (12, 34)
     * $query->filterByKi(array('min' => 12)); // WHERE ki >= 12
     * $query->filterByKi(array('max' => 12)); // WHERE ki <= 12
     * </code>
     *
     * @param     mixed $ki The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByKi($ki = null, $comparison = null)
    {
        if (is_array($ki)) {
            $useMinMax = false;
            if (isset($ki['min'])) {
                $this->addUsingAlias(PlayersPeer::KI, $ki['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ki['max'])) {
                $this->addUsingAlias(PlayersPeer::KI, $ki['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::KI, $ki, $comparison);
    }

    /**
     * Filter the query on the stamina column
     *
     * Example usage:
     * <code>
     * $query->filterByStamina(1234); // WHERE stamina = 1234
     * $query->filterByStamina(array(12, 34)); // WHERE stamina IN (12, 34)
     * $query->filterByStamina(array('min' => 12)); // WHERE stamina >= 12
     * $query->filterByStamina(array('max' => 12)); // WHERE stamina <= 12
     * </code>
     *
     * @param     mixed $stamina The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByStamina($stamina = null, $comparison = null)
    {
        if (is_array($stamina)) {
            $useMinMax = false;
            if (isset($stamina['min'])) {
                $this->addUsingAlias(PlayersPeer::STAMINA, $stamina['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($stamina['max'])) {
                $this->addUsingAlias(PlayersPeer::STAMINA, $stamina['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::STAMINA, $stamina, $comparison);
    }

    /**
     * Filter the query on the speed column
     *
     * Example usage:
     * <code>
     * $query->filterBySpeed(1234); // WHERE speed = 1234
     * $query->filterBySpeed(array(12, 34)); // WHERE speed IN (12, 34)
     * $query->filterBySpeed(array('min' => 12)); // WHERE speed >= 12
     * $query->filterBySpeed(array('max' => 12)); // WHERE speed <= 12
     * </code>
     *
     * @param     mixed $speed The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterBySpeed($speed = null, $comparison = null)
    {
        if (is_array($speed)) {
            $useMinMax = false;
            if (isset($speed['min'])) {
                $this->addUsingAlias(PlayersPeer::SPEED, $speed['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($speed['max'])) {
                $this->addUsingAlias(PlayersPeer::SPEED, $speed['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::SPEED, $speed, $comparison);
    }

    /**
     * Filter the query on the karma column
     *
     * Example usage:
     * <code>
     * $query->filterByKarma(1234); // WHERE karma = 1234
     * $query->filterByKarma(array(12, 34)); // WHERE karma IN (12, 34)
     * $query->filterByKarma(array('min' => 12)); // WHERE karma >= 12
     * $query->filterByKarma(array('max' => 12)); // WHERE karma <= 12
     * </code>
     *
     * @param     mixed $karma The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByKarma($karma = null, $comparison = null)
    {
        if (is_array($karma)) {
            $useMinMax = false;
            if (isset($karma['min'])) {
                $this->addUsingAlias(PlayersPeer::KARMA, $karma['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($karma['max'])) {
                $this->addUsingAlias(PlayersPeer::KARMA, $karma['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::KARMA, $karma, $comparison);
    }

    /**
     * Filter the query on the kills_gained column
     *
     * Example usage:
     * <code>
     * $query->filterByKillsGained(1234); // WHERE kills_gained = 1234
     * $query->filterByKillsGained(array(12, 34)); // WHERE kills_gained IN (12, 34)
     * $query->filterByKillsGained(array('min' => 12)); // WHERE kills_gained >= 12
     * $query->filterByKillsGained(array('max' => 12)); // WHERE kills_gained <= 12
     * </code>
     *
     * @param     mixed $killsGained The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByKillsGained($killsGained = null, $comparison = null)
    {
        if (is_array($killsGained)) {
            $useMinMax = false;
            if (isset($killsGained['min'])) {
                $this->addUsingAlias(PlayersPeer::KILLS_GAINED, $killsGained['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($killsGained['max'])) {
                $this->addUsingAlias(PlayersPeer::KILLS_GAINED, $killsGained['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::KILLS_GAINED, $killsGained, $comparison);
    }

    /**
     * Filter the query on the kills_used column
     *
     * Example usage:
     * <code>
     * $query->filterByKillsUsed(1234); // WHERE kills_used = 1234
     * $query->filterByKillsUsed(array(12, 34)); // WHERE kills_used IN (12, 34)
     * $query->filterByKillsUsed(array('min' => 12)); // WHERE kills_used >= 12
     * $query->filterByKillsUsed(array('max' => 12)); // WHERE kills_used <= 12
     * </code>
     *
     * @param     mixed $killsUsed The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByKillsUsed($killsUsed = null, $comparison = null)
    {
        if (is_array($killsUsed)) {
            $useMinMax = false;
            if (isset($killsUsed['min'])) {
                $this->addUsingAlias(PlayersPeer::KILLS_USED, $killsUsed['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($killsUsed['max'])) {
                $this->addUsingAlias(PlayersPeer::KILLS_USED, $killsUsed['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::KILLS_USED, $killsUsed, $comparison);
    }

    /**
     * Filter the query by a related Class object
     *
     * @param   Class|PropelObjectCollection $class The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClass($class, $comparison = null)
    {
        if ($class instanceof Class) {
            return $this
                ->addUsingAlias(PlayersPeer::_CLASS_ID, $class->getClassId(), $comparison);
        } elseif ($class instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PlayersPeer::_CLASS_ID, $class->toKeyValue('PrimaryKey', 'ClassId'), $comparison);
        } else {
            throw new PropelException('filterByClass() only accepts arguments of type Class or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Class relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinClass($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Class');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Class');
        }

        return $this;
    }

    /**
     * Use the Class relation Class object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClassQuery A secondary query class using the current class as primary query
     */
    public function useClassQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClass($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Class', '\deploy\model\ClassQuery');
    }

    /**
     * Filter the query by a related AccountPlayers object
     *
     * @param   AccountPlayers|PropelObjectCollection $accountPlayers  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountPlayers($accountPlayers, $comparison = null)
    {
        if ($accountPlayers instanceof AccountPlayers) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $accountPlayers->getPlayerId(), $comparison);
        } elseif ($accountPlayers instanceof PropelObjectCollection) {
            return $this
                ->useAccountPlayersQuery()
                ->filterByPrimaryKeys($accountPlayers->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountPlayers() only accepts arguments of type AccountPlayers or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountPlayers relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinAccountPlayers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountPlayers');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'AccountPlayers');
        }

        return $this;
    }

    /**
     * Use the AccountPlayers relation AccountPlayers object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\AccountPlayersQuery A secondary query class using the current class as primary query
     */
    public function useAccountPlayersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountPlayers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountPlayers', '\deploy\model\AccountPlayersQuery');
    }

    /**
     * Filter the query by a related ClanPlayer object
     *
     * @param   ClanPlayer|PropelObjectCollection $clanPlayer  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClanPlayer($clanPlayer, $comparison = null)
    {
        if ($clanPlayer instanceof ClanPlayer) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $clanPlayer->getPlayerId(), $comparison);
        } elseif ($clanPlayer instanceof PropelObjectCollection) {
            return $this
                ->useClanPlayerQuery()
                ->filterByPrimaryKeys($clanPlayer->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByClanPlayer() only accepts arguments of type ClanPlayer or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClanPlayer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinClanPlayer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClanPlayer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ClanPlayer');
        }

        return $this;
    }

    /**
     * Use the ClanPlayer relation ClanPlayer object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClanPlayerQuery A secondary query class using the current class as primary query
     */
    public function useClanPlayerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClanPlayer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClanPlayer', '\deploy\model\ClanPlayerQuery');
    }

    /**
     * Filter the query by a related Enemies object
     *
     * @param   Enemies|PropelObjectCollection $enemies  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByEnemiesRelatedByEnemyId($enemies, $comparison = null)
    {
        if ($enemies instanceof Enemies) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $enemies->getEnemyId(), $comparison);
        } elseif ($enemies instanceof PropelObjectCollection) {
            return $this
                ->useEnemiesRelatedByEnemyIdQuery()
                ->filterByPrimaryKeys($enemies->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByEnemiesRelatedByEnemyId() only accepts arguments of type Enemies or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the EnemiesRelatedByEnemyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinEnemiesRelatedByEnemyId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('EnemiesRelatedByEnemyId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'EnemiesRelatedByEnemyId');
        }

        return $this;
    }

    /**
     * Use the EnemiesRelatedByEnemyId relation Enemies object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\EnemiesQuery A secondary query class using the current class as primary query
     */
    public function useEnemiesRelatedByEnemyIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEnemiesRelatedByEnemyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'EnemiesRelatedByEnemyId', '\deploy\model\EnemiesQuery');
    }

    /**
     * Filter the query by a related Enemies object
     *
     * @param   Enemies|PropelObjectCollection $enemies  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByEnemiesRelatedByPlayerId($enemies, $comparison = null)
    {
        if ($enemies instanceof Enemies) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $enemies->getPlayerId(), $comparison);
        } elseif ($enemies instanceof PropelObjectCollection) {
            return $this
                ->useEnemiesRelatedByPlayerIdQuery()
                ->filterByPrimaryKeys($enemies->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByEnemiesRelatedByPlayerId() only accepts arguments of type Enemies or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the EnemiesRelatedByPlayerId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinEnemiesRelatedByPlayerId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('EnemiesRelatedByPlayerId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'EnemiesRelatedByPlayerId');
        }

        return $this;
    }

    /**
     * Use the EnemiesRelatedByPlayerId relation Enemies object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\EnemiesQuery A secondary query class using the current class as primary query
     */
    public function useEnemiesRelatedByPlayerIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEnemiesRelatedByPlayerId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'EnemiesRelatedByPlayerId', '\deploy\model\EnemiesQuery');
    }

    /**
     * Filter the query by a related Inventory object
     *
     * @param   Inventory|PropelObjectCollection $inventory  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByInventory($inventory, $comparison = null)
    {
        if ($inventory instanceof Inventory) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $inventory->getOwner(), $comparison);
        } elseif ($inventory instanceof PropelObjectCollection) {
            return $this
                ->useInventoryQuery()
                ->filterByPrimaryKeys($inventory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByInventory() only accepts arguments of type Inventory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Inventory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinInventory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Inventory');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Inventory');
        }

        return $this;
    }

    /**
     * Use the Inventory relation Inventory object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\InventoryQuery A secondary query class using the current class as primary query
     */
    public function useInventoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinInventory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Inventory', '\deploy\model\InventoryQuery');
    }

    /**
     * Filter the query by a related LevellingLog object
     *
     * @param   LevellingLog|PropelObjectCollection $levellingLog  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByLevellingLog($levellingLog, $comparison = null)
    {
        if ($levellingLog instanceof LevellingLog) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $levellingLog->getPlayerId(), $comparison);
        } elseif ($levellingLog instanceof PropelObjectCollection) {
            return $this
                ->useLevellingLogQuery()
                ->filterByPrimaryKeys($levellingLog->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByLevellingLog() only accepts arguments of type LevellingLog or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LevellingLog relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinLevellingLog($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LevellingLog');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'LevellingLog');
        }

        return $this;
    }

    /**
     * Use the LevellingLog relation LevellingLog object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\LevellingLogQuery A secondary query class using the current class as primary query
     */
    public function useLevellingLogQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLevellingLog($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LevellingLog', '\deploy\model\LevellingLogQuery');
    }

    /**
     * Filter the query by a related Messages object
     *
     * @param   Messages|PropelObjectCollection $messages  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMessagesRelatedBySendFrom($messages, $comparison = null)
    {
        if ($messages instanceof Messages) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $messages->getSendFrom(), $comparison);
        } elseif ($messages instanceof PropelObjectCollection) {
            return $this
                ->useMessagesRelatedBySendFromQuery()
                ->filterByPrimaryKeys($messages->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMessagesRelatedBySendFrom() only accepts arguments of type Messages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MessagesRelatedBySendFrom relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinMessagesRelatedBySendFrom($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MessagesRelatedBySendFrom');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'MessagesRelatedBySendFrom');
        }

        return $this;
    }

    /**
     * Use the MessagesRelatedBySendFrom relation Messages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\MessagesQuery A secondary query class using the current class as primary query
     */
    public function useMessagesRelatedBySendFromQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMessagesRelatedBySendFrom($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MessagesRelatedBySendFrom', '\deploy\model\MessagesQuery');
    }

    /**
     * Filter the query by a related Messages object
     *
     * @param   Messages|PropelObjectCollection $messages  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMessagesRelatedBySendTo($messages, $comparison = null)
    {
        if ($messages instanceof Messages) {
            return $this
                ->addUsingAlias(PlayersPeer::PLAYER_ID, $messages->getSendTo(), $comparison);
        } elseif ($messages instanceof PropelObjectCollection) {
            return $this
                ->useMessagesRelatedBySendToQuery()
                ->filterByPrimaryKeys($messages->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMessagesRelatedBySendTo() only accepts arguments of type Messages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MessagesRelatedBySendTo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function joinMessagesRelatedBySendTo($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MessagesRelatedBySendTo');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'MessagesRelatedBySendTo');
        }

        return $this;
    }

    /**
     * Use the MessagesRelatedBySendTo relation Messages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\MessagesQuery A secondary query class using the current class as primary query
     */
    public function useMessagesRelatedBySendToQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMessagesRelatedBySendTo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MessagesRelatedBySendTo', '\deploy\model\MessagesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Players $players Object to remove from the list of results
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function prune($players = null)
    {
        if ($players) {
            $this->addUsingAlias(PlayersPeer::PLAYER_ID, $players->getPlayerId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
