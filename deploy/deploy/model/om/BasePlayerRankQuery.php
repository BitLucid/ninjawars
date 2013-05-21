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
use deploy\model\PlayerRank;
use deploy\model\PlayerRankPeer;
use deploy\model\PlayerRankQuery;

/**
 * Base class that represents a query for the 'player_rank' table.
 *
 *
 *
 * @method PlayerRankQuery orderByRankId($order = Criteria::ASC) Order by the rank_id column
 * @method PlayerRankQuery orderByPlayerId($order = Criteria::ASC) Order by the _player_id column
 * @method PlayerRankQuery orderByScore($order = Criteria::ASC) Order by the score column
 *
 * @method PlayerRankQuery groupByRankId() Group by the rank_id column
 * @method PlayerRankQuery groupByPlayerId() Group by the _player_id column
 * @method PlayerRankQuery groupByScore() Group by the score column
 *
 * @method PlayerRankQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlayerRankQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlayerRankQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlayerRank findOne(PropelPDO $con = null) Return the first PlayerRank matching the query
 * @method PlayerRank findOneOrCreate(PropelPDO $con = null) Return the first PlayerRank matching the query, or a new PlayerRank object populated from the query conditions when no match is found
 *
 * @method PlayerRank findOneByRankId(int $rank_id) Return the first PlayerRank filtered by the rank_id column
 * @method PlayerRank findOneByPlayerId(int $_player_id) Return the first PlayerRank filtered by the _player_id column
 * @method PlayerRank findOneByScore(int $score) Return the first PlayerRank filtered by the score column
 *
 * @method array findByRankId(int $rank_id) Return PlayerRank objects filtered by the rank_id column
 * @method array findByPlayerId(int $_player_id) Return PlayerRank objects filtered by the _player_id column
 * @method array findByScore(int $score) Return PlayerRank objects filtered by the score column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePlayerRankQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlayerRankQuery object.
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
            $modelName = 'deploy\\model\\PlayerRank';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlayerRankQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlayerRankQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlayerRankQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlayerRankQuery) {
            return $criteria;
        }
        $query = new PlayerRankQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$rank_id, $_player_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   PlayerRank|PlayerRank[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlayerRankPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlayerRankPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PlayerRank A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "rank_id", "_player_id", "score" FROM "player_rank" WHERE "rank_id" = :p0 AND "_player_id" = :p1';
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
            $obj = new PlayerRank();
            $obj->hydrate($row);
            PlayerRankPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return PlayerRank|PlayerRank[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PlayerRank[]|mixed the list of results, formatted by the current formatter
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
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(PlayerRankPeer::RANK_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(PlayerRankPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(PlayerRankPeer::RANK_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(PlayerRankPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the rank_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRankId(1234); // WHERE rank_id = 1234
     * $query->filterByRankId(array(12, 34)); // WHERE rank_id IN (12, 34)
     * $query->filterByRankId(array('min' => 12)); // WHERE rank_id >= 12
     * $query->filterByRankId(array('max' => 12)); // WHERE rank_id <= 12
     * </code>
     *
     * @param     mixed $rankId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function filterByRankId($rankId = null, $comparison = null)
    {
        if (is_array($rankId)) {
            $useMinMax = false;
            if (isset($rankId['min'])) {
                $this->addUsingAlias(PlayerRankPeer::RANK_ID, $rankId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rankId['max'])) {
                $this->addUsingAlias(PlayerRankPeer::RANK_ID, $rankId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayerRankPeer::RANK_ID, $rankId, $comparison);
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
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(PlayerRankPeer::_PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(PlayerRankPeer::_PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayerRankPeer::_PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the score column
     *
     * Example usage:
     * <code>
     * $query->filterByScore(1234); // WHERE score = 1234
     * $query->filterByScore(array(12, 34)); // WHERE score IN (12, 34)
     * $query->filterByScore(array('min' => 12)); // WHERE score >= 12
     * $query->filterByScore(array('max' => 12)); // WHERE score <= 12
     * </code>
     *
     * @param     mixed $score The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function filterByScore($score = null, $comparison = null)
    {
        if (is_array($score)) {
            $useMinMax = false;
            if (isset($score['min'])) {
                $this->addUsingAlias(PlayerRankPeer::SCORE, $score['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($score['max'])) {
                $this->addUsingAlias(PlayerRankPeer::SCORE, $score['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayerRankPeer::SCORE, $score, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   PlayerRank $playerRank Object to remove from the list of results
     *
     * @return PlayerRankQuery The current query, for fluid interface
     */
    public function prune($playerRank = null)
    {
        if ($playerRank) {
            $this->addCond('pruneCond0', $this->getAliasedColName(PlayerRankPeer::RANK_ID), $playerRank->getRankId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(PlayerRankPeer::_PLAYER_ID), $playerRank->getPlayerId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
