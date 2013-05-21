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
use deploy\model\Enemies;
use deploy\model\EnemiesPeer;
use deploy\model\EnemiesQuery;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'enemies' table.
 *
 *
 *
 * @method EnemiesQuery orderByPlayerId($order = Criteria::ASC) Order by the _player_id column
 * @method EnemiesQuery orderByEnemyId($order = Criteria::ASC) Order by the _enemy_id column
 *
 * @method EnemiesQuery groupByPlayerId() Group by the _player_id column
 * @method EnemiesQuery groupByEnemyId() Group by the _enemy_id column
 *
 * @method EnemiesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EnemiesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EnemiesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method EnemiesQuery leftJoinPlayersRelatedByEnemyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayersRelatedByEnemyId relation
 * @method EnemiesQuery rightJoinPlayersRelatedByEnemyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayersRelatedByEnemyId relation
 * @method EnemiesQuery innerJoinPlayersRelatedByEnemyId($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayersRelatedByEnemyId relation
 *
 * @method EnemiesQuery leftJoinPlayersRelatedByPlayerId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayersRelatedByPlayerId relation
 * @method EnemiesQuery rightJoinPlayersRelatedByPlayerId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayersRelatedByPlayerId relation
 * @method EnemiesQuery innerJoinPlayersRelatedByPlayerId($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayersRelatedByPlayerId relation
 *
 * @method Enemies findOne(PropelPDO $con = null) Return the first Enemies matching the query
 * @method Enemies findOneOrCreate(PropelPDO $con = null) Return the first Enemies matching the query, or a new Enemies object populated from the query conditions when no match is found
 *
 * @method Enemies findOneByPlayerId(int $_player_id) Return the first Enemies filtered by the _player_id column
 * @method Enemies findOneByEnemyId(int $_enemy_id) Return the first Enemies filtered by the _enemy_id column
 *
 * @method array findByPlayerId(int $_player_id) Return Enemies objects filtered by the _player_id column
 * @method array findByEnemyId(int $_enemy_id) Return Enemies objects filtered by the _enemy_id column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseEnemiesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEnemiesQuery object.
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
            $modelName = 'deploy\\model\\Enemies';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new EnemiesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   EnemiesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EnemiesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EnemiesQuery) {
            return $criteria;
        }
        $query = new EnemiesQuery(null, null, $modelAlias);

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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$_player_id, $_enemy_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Enemies|Enemies[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EnemiesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EnemiesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Enemies A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "_player_id", "_enemy_id" FROM "enemies" WHERE "_player_id" = :p0 AND "_enemy_id" = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Enemies();
            $obj->hydrate($row);
            EnemiesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return Enemies|Enemies[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Enemies[]|mixed the list of results, formatted by the current formatter
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
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(EnemiesPeer::_PLAYER_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(EnemiesPeer::_ENEMY_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(EnemiesPeer::_PLAYER_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(EnemiesPeer::_ENEMY_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByPlayersRelatedByPlayerId()
     *
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(EnemiesPeer::_PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(EnemiesPeer::_PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnemiesPeer::_PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the _enemy_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEnemyId(1234); // WHERE _enemy_id = 1234
     * $query->filterByEnemyId(array(12, 34)); // WHERE _enemy_id IN (12, 34)
     * $query->filterByEnemyId(array('min' => 12)); // WHERE _enemy_id >= 12
     * $query->filterByEnemyId(array('max' => 12)); // WHERE _enemy_id <= 12
     * </code>
     *
     * @see       filterByPlayersRelatedByEnemyId()
     *
     * @param     mixed $enemyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function filterByEnemyId($enemyId = null, $comparison = null)
    {
        if (is_array($enemyId)) {
            $useMinMax = false;
            if (isset($enemyId['min'])) {
                $this->addUsingAlias(EnemiesPeer::_ENEMY_ID, $enemyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enemyId['max'])) {
                $this->addUsingAlias(EnemiesPeer::_ENEMY_ID, $enemyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnemiesPeer::_ENEMY_ID, $enemyId, $comparison);
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 EnemiesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayersRelatedByEnemyId($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(EnemiesPeer::_ENEMY_ID, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EnemiesPeer::_ENEMY_ID, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayersRelatedByEnemyId() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayersRelatedByEnemyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function joinPlayersRelatedByEnemyId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayersRelatedByEnemyId');

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
            $this->addJoinObject($join, 'PlayersRelatedByEnemyId');
        }

        return $this;
    }

    /**
     * Use the PlayersRelatedByEnemyId relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersRelatedByEnemyIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayersRelatedByEnemyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayersRelatedByEnemyId', '\deploy\model\PlayersQuery');
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 EnemiesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayersRelatedByPlayerId($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(EnemiesPeer::_PLAYER_ID, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EnemiesPeer::_PLAYER_ID, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayersRelatedByPlayerId() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayersRelatedByPlayerId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function joinPlayersRelatedByPlayerId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayersRelatedByPlayerId');

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
            $this->addJoinObject($join, 'PlayersRelatedByPlayerId');
        }

        return $this;
    }

    /**
     * Use the PlayersRelatedByPlayerId relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersRelatedByPlayerIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayersRelatedByPlayerId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayersRelatedByPlayerId', '\deploy\model\PlayersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Enemies $enemies Object to remove from the list of results
     *
     * @return EnemiesQuery The current query, for fluid interface
     */
    public function prune($enemies = null)
    {
        if ($enemies) {
            $this->addCond('pruneCond0', $this->getAliasedColName(EnemiesPeer::_PLAYER_ID), $enemies->getPlayerId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(EnemiesPeer::_ENEMY_ID), $enemies->getEnemyId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
