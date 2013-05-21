<?php

namespace deploy\model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use deploy\model\PlayersFlagged;
use deploy\model\PlayersFlaggedPeer;
use deploy\model\PlayersFlaggedQuery;

/**
 * Base class that represents a query for the 'players_flagged' table.
 *
 *
 *
 * @method PlayersFlaggedQuery orderByPlayersFlaggedId($order = Criteria::ASC) Order by the players_flagged_id column
 * @method PlayersFlaggedQuery orderByPlayerId($order = Criteria::ASC) Order by the player_id column
 * @method PlayersFlaggedQuery orderByFlagId($order = Criteria::ASC) Order by the flag_id column
 * @method PlayersFlaggedQuery orderByTimestamp($order = Criteria::ASC) Order by the timestamp column
 * @method PlayersFlaggedQuery orderByOriginatingPage($order = Criteria::ASC) Order by the originating_page column
 * @method PlayersFlaggedQuery orderByExtraNotes($order = Criteria::ASC) Order by the extra_notes column
 *
 * @method PlayersFlaggedQuery groupByPlayersFlaggedId() Group by the players_flagged_id column
 * @method PlayersFlaggedQuery groupByPlayerId() Group by the player_id column
 * @method PlayersFlaggedQuery groupByFlagId() Group by the flag_id column
 * @method PlayersFlaggedQuery groupByTimestamp() Group by the timestamp column
 * @method PlayersFlaggedQuery groupByOriginatingPage() Group by the originating_page column
 * @method PlayersFlaggedQuery groupByExtraNotes() Group by the extra_notes column
 *
 * @method PlayersFlaggedQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlayersFlaggedQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlayersFlaggedQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlayersFlagged findOne(PropelPDO $con = null) Return the first PlayersFlagged matching the query
 * @method PlayersFlagged findOneOrCreate(PropelPDO $con = null) Return the first PlayersFlagged matching the query, or a new PlayersFlagged object populated from the query conditions when no match is found
 *
 * @method PlayersFlagged findOneByPlayerId(int $player_id) Return the first PlayersFlagged filtered by the player_id column
 * @method PlayersFlagged findOneByFlagId(int $flag_id) Return the first PlayersFlagged filtered by the flag_id column
 * @method PlayersFlagged findOneByTimestamp(string $timestamp) Return the first PlayersFlagged filtered by the timestamp column
 * @method PlayersFlagged findOneByOriginatingPage(string $originating_page) Return the first PlayersFlagged filtered by the originating_page column
 * @method PlayersFlagged findOneByExtraNotes(string $extra_notes) Return the first PlayersFlagged filtered by the extra_notes column
 *
 * @method array findByPlayersFlaggedId(int $players_flagged_id) Return PlayersFlagged objects filtered by the players_flagged_id column
 * @method array findByPlayerId(int $player_id) Return PlayersFlagged objects filtered by the player_id column
 * @method array findByFlagId(int $flag_id) Return PlayersFlagged objects filtered by the flag_id column
 * @method array findByTimestamp(string $timestamp) Return PlayersFlagged objects filtered by the timestamp column
 * @method array findByOriginatingPage(string $originating_page) Return PlayersFlagged objects filtered by the originating_page column
 * @method array findByExtraNotes(string $extra_notes) Return PlayersFlagged objects filtered by the extra_notes column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePlayersFlaggedQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlayersFlaggedQuery object.
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
            $modelName = 'deploy\\model\\PlayersFlagged';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlayersFlaggedQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlayersFlaggedQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlayersFlaggedQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlayersFlaggedQuery) {
            return $criteria;
        }
        $query = new PlayersFlaggedQuery(null, null, $modelAlias);

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
     * @return   PlayersFlagged|PlayersFlagged[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlayersFlaggedPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlayersFlaggedPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PlayersFlagged A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPlayersFlaggedId($key, $con = null)
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
     * @return                 PlayersFlagged A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "players_flagged_id", "player_id", "flag_id", "timestamp", "originating_page", "extra_notes" FROM "players_flagged" WHERE "players_flagged_id" = :p0';
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
            $obj = new PlayersFlagged();
            $obj->hydrate($row);
            PlayersFlaggedPeer::addInstanceToPool($obj, (string) $key);
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
     * @return PlayersFlagged|PlayersFlagged[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PlayersFlagged[]|mixed the list of results, formatted by the current formatter
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
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the players_flagged_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayersFlaggedId(1234); // WHERE players_flagged_id = 1234
     * $query->filterByPlayersFlaggedId(array(12, 34)); // WHERE players_flagged_id IN (12, 34)
     * $query->filterByPlayersFlaggedId(array('min' => 12)); // WHERE players_flagged_id >= 12
     * $query->filterByPlayersFlaggedId(array('max' => 12)); // WHERE players_flagged_id <= 12
     * </code>
     *
     * @param     mixed $playersFlaggedId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByPlayersFlaggedId($playersFlaggedId = null, $comparison = null)
    {
        if (is_array($playersFlaggedId)) {
            $useMinMax = false;
            if (isset($playersFlaggedId['min'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $playersFlaggedId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playersFlaggedId['max'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $playersFlaggedId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $playersFlaggedId, $comparison);
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
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the flag_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFlagId(1234); // WHERE flag_id = 1234
     * $query->filterByFlagId(array(12, 34)); // WHERE flag_id IN (12, 34)
     * $query->filterByFlagId(array('min' => 12)); // WHERE flag_id >= 12
     * $query->filterByFlagId(array('max' => 12)); // WHERE flag_id <= 12
     * </code>
     *
     * @param     mixed $flagId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByFlagId($flagId = null, $comparison = null)
    {
        if (is_array($flagId)) {
            $useMinMax = false;
            if (isset($flagId['min'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::FLAG_ID, $flagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($flagId['max'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::FLAG_ID, $flagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::FLAG_ID, $flagId, $comparison);
    }

    /**
     * Filter the query on the timestamp column
     *
     * Example usage:
     * <code>
     * $query->filterByTimestamp('2011-03-14'); // WHERE timestamp = '2011-03-14'
     * $query->filterByTimestamp('now'); // WHERE timestamp = '2011-03-14'
     * $query->filterByTimestamp(array('max' => 'yesterday')); // WHERE timestamp > '2011-03-13'
     * </code>
     *
     * @param     mixed $timestamp The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByTimestamp($timestamp = null, $comparison = null)
    {
        if (is_array($timestamp)) {
            $useMinMax = false;
            if (isset($timestamp['min'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::TIMESTAMP, $timestamp['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timestamp['max'])) {
                $this->addUsingAlias(PlayersFlaggedPeer::TIMESTAMP, $timestamp['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::TIMESTAMP, $timestamp, $comparison);
    }

    /**
     * Filter the query on the originating_page column
     *
     * Example usage:
     * <code>
     * $query->filterByOriginatingPage('fooValue');   // WHERE originating_page = 'fooValue'
     * $query->filterByOriginatingPage('%fooValue%'); // WHERE originating_page LIKE '%fooValue%'
     * </code>
     *
     * @param     string $originatingPage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByOriginatingPage($originatingPage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($originatingPage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $originatingPage)) {
                $originatingPage = str_replace('*', '%', $originatingPage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::ORIGINATING_PAGE, $originatingPage, $comparison);
    }

    /**
     * Filter the query on the extra_notes column
     *
     * Example usage:
     * <code>
     * $query->filterByExtraNotes('fooValue');   // WHERE extra_notes = 'fooValue'
     * $query->filterByExtraNotes('%fooValue%'); // WHERE extra_notes LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extraNotes The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function filterByExtraNotes($extraNotes = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extraNotes)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extraNotes)) {
                $extraNotes = str_replace('*', '%', $extraNotes);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersFlaggedPeer::EXTRA_NOTES, $extraNotes, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   PlayersFlagged $playersFlagged Object to remove from the list of results
     *
     * @return PlayersFlaggedQuery The current query, for fluid interface
     */
    public function prune($playersFlagged = null)
    {
        if ($playersFlagged) {
            $this->addUsingAlias(PlayersFlaggedPeer::PLAYERS_FLAGGED_ID, $playersFlagged->getPlayersFlaggedId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
