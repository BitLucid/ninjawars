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
use deploy\model\LevellingLog;
use deploy\model\LevellingLogPeer;
use deploy\model\LevellingLogQuery;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'levelling_log' table.
 *
 *
 *
 * @method LevellingLogQuery orderById($order = Criteria::ASC) Order by the id column
 * @method LevellingLogQuery orderByKillpoints($order = Criteria::ASC) Order by the killpoints column
 * @method LevellingLogQuery orderByLevelling($order = Criteria::ASC) Order by the levelling column
 * @method LevellingLogQuery orderByKillsdate($order = Criteria::ASC) Order by the killsdate column
 * @method LevellingLogQuery orderByPlayerId($order = Criteria::ASC) Order by the _player_id column
 *
 * @method LevellingLogQuery groupById() Group by the id column
 * @method LevellingLogQuery groupByKillpoints() Group by the killpoints column
 * @method LevellingLogQuery groupByLevelling() Group by the levelling column
 * @method LevellingLogQuery groupByKillsdate() Group by the killsdate column
 * @method LevellingLogQuery groupByPlayerId() Group by the _player_id column
 *
 * @method LevellingLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method LevellingLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method LevellingLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method LevellingLogQuery leftJoinPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Players relation
 * @method LevellingLogQuery rightJoinPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Players relation
 * @method LevellingLogQuery innerJoinPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the Players relation
 *
 * @method LevellingLog findOne(PropelPDO $con = null) Return the first LevellingLog matching the query
 * @method LevellingLog findOneOrCreate(PropelPDO $con = null) Return the first LevellingLog matching the query, or a new LevellingLog object populated from the query conditions when no match is found
 *
 * @method LevellingLog findOneByKillpoints(int $killpoints) Return the first LevellingLog filtered by the killpoints column
 * @method LevellingLog findOneByLevelling(int $levelling) Return the first LevellingLog filtered by the levelling column
 * @method LevellingLog findOneByKillsdate(string $killsdate) Return the first LevellingLog filtered by the killsdate column
 * @method LevellingLog findOneByPlayerId(int $_player_id) Return the first LevellingLog filtered by the _player_id column
 *
 * @method array findById(int $id) Return LevellingLog objects filtered by the id column
 * @method array findByKillpoints(int $killpoints) Return LevellingLog objects filtered by the killpoints column
 * @method array findByLevelling(int $levelling) Return LevellingLog objects filtered by the levelling column
 * @method array findByKillsdate(string $killsdate) Return LevellingLog objects filtered by the killsdate column
 * @method array findByPlayerId(int $_player_id) Return LevellingLog objects filtered by the _player_id column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseLevellingLogQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseLevellingLogQuery object.
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
            $modelName = 'deploy\\model\\LevellingLog';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LevellingLogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   LevellingLogQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LevellingLogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LevellingLogQuery) {
            return $criteria;
        }
        $query = new LevellingLogQuery(null, null, $modelAlias);

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
     * @return   LevellingLog|LevellingLog[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LevellingLogPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LevellingLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 LevellingLog A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
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
     * @return                 LevellingLog A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "killpoints", "levelling", "killsdate", "_player_id" FROM "levelling_log" WHERE "id" = :p0';
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
            $obj = new LevellingLog();
            $obj->hydrate($row);
            LevellingLogPeer::addInstanceToPool($obj, (string) $key);
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
     * @return LevellingLog|LevellingLog[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|LevellingLog[]|mixed the list of results, formatted by the current formatter
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
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LevellingLogPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LevellingLogPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LevellingLogPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LevellingLogPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LevellingLogPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the killpoints column
     *
     * Example usage:
     * <code>
     * $query->filterByKillpoints(1234); // WHERE killpoints = 1234
     * $query->filterByKillpoints(array(12, 34)); // WHERE killpoints IN (12, 34)
     * $query->filterByKillpoints(array('min' => 12)); // WHERE killpoints >= 12
     * $query->filterByKillpoints(array('max' => 12)); // WHERE killpoints <= 12
     * </code>
     *
     * @param     mixed $killpoints The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByKillpoints($killpoints = null, $comparison = null)
    {
        if (is_array($killpoints)) {
            $useMinMax = false;
            if (isset($killpoints['min'])) {
                $this->addUsingAlias(LevellingLogPeer::KILLPOINTS, $killpoints['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($killpoints['max'])) {
                $this->addUsingAlias(LevellingLogPeer::KILLPOINTS, $killpoints['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LevellingLogPeer::KILLPOINTS, $killpoints, $comparison);
    }

    /**
     * Filter the query on the levelling column
     *
     * Example usage:
     * <code>
     * $query->filterByLevelling(1234); // WHERE levelling = 1234
     * $query->filterByLevelling(array(12, 34)); // WHERE levelling IN (12, 34)
     * $query->filterByLevelling(array('min' => 12)); // WHERE levelling >= 12
     * $query->filterByLevelling(array('max' => 12)); // WHERE levelling <= 12
     * </code>
     *
     * @param     mixed $levelling The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByLevelling($levelling = null, $comparison = null)
    {
        if (is_array($levelling)) {
            $useMinMax = false;
            if (isset($levelling['min'])) {
                $this->addUsingAlias(LevellingLogPeer::LEVELLING, $levelling['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($levelling['max'])) {
                $this->addUsingAlias(LevellingLogPeer::LEVELLING, $levelling['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LevellingLogPeer::LEVELLING, $levelling, $comparison);
    }

    /**
     * Filter the query on the killsdate column
     *
     * Example usage:
     * <code>
     * $query->filterByKillsdate('2011-03-14'); // WHERE killsdate = '2011-03-14'
     * $query->filterByKillsdate('now'); // WHERE killsdate = '2011-03-14'
     * $query->filterByKillsdate(array('max' => 'yesterday')); // WHERE killsdate > '2011-03-13'
     * </code>
     *
     * @param     mixed $killsdate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByKillsdate($killsdate = null, $comparison = null)
    {
        if (is_array($killsdate)) {
            $useMinMax = false;
            if (isset($killsdate['min'])) {
                $this->addUsingAlias(LevellingLogPeer::KILLSDATE, $killsdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($killsdate['max'])) {
                $this->addUsingAlias(LevellingLogPeer::KILLSDATE, $killsdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LevellingLogPeer::KILLSDATE, $killsdate, $comparison);
    }

    /**
     * Filter the query on the _player_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerId(1234); // WHERE _player_id = 1234
     * $query->filterByPlayerId(array(12, 34)); // WHERE _player_id IN (12, 34)
     * $query->filterByPlayerId(array('min' => 12)); // WHERE _player_id >= 12
     * $query->filterByPlayerId(array('max' => 12)); // WHERE _player_id <= 12
     * </code>
     *
     * @see       filterByPlayers()
     *
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(LevellingLogPeer::_PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(LevellingLogPeer::_PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LevellingLogPeer::_PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 LevellingLogQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayers($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(LevellingLogPeer::_PLAYER_ID, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LevellingLogPeer::_PLAYER_ID, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayers() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Players relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function joinPlayers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Players');

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
            $this->addJoinObject($join, 'Players');
        }

        return $this;
    }

    /**
     * Use the Players relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Players', '\deploy\model\PlayersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   LevellingLog $levellingLog Object to remove from the list of results
     *
     * @return LevellingLogQuery The current query, for fluid interface
     */
    public function prune($levellingLog = null)
    {
        if ($levellingLog) {
            $this->addUsingAlias(LevellingLogPeer::ID, $levellingLog->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
