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
use deploy\model\PastStats;
use deploy\model\PastStatsPeer;
use deploy\model\PastStatsQuery;

/**
 * Base class that represents a query for the 'past_stats' table.
 *
 *
 *
 * @method PastStatsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PastStatsQuery orderByStatType($order = Criteria::ASC) Order by the stat_type column
 * @method PastStatsQuery orderByStatResult($order = Criteria::ASC) Order by the stat_result column
 *
 * @method PastStatsQuery groupById() Group by the id column
 * @method PastStatsQuery groupByStatType() Group by the stat_type column
 * @method PastStatsQuery groupByStatResult() Group by the stat_result column
 *
 * @method PastStatsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PastStatsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PastStatsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PastStats findOne(PropelPDO $con = null) Return the first PastStats matching the query
 * @method PastStats findOneOrCreate(PropelPDO $con = null) Return the first PastStats matching the query, or a new PastStats object populated from the query conditions when no match is found
 *
 * @method PastStats findOneByStatType(string $stat_type) Return the first PastStats filtered by the stat_type column
 * @method PastStats findOneByStatResult(string $stat_result) Return the first PastStats filtered by the stat_result column
 *
 * @method array findById(int $id) Return PastStats objects filtered by the id column
 * @method array findByStatType(string $stat_type) Return PastStats objects filtered by the stat_type column
 * @method array findByStatResult(string $stat_result) Return PastStats objects filtered by the stat_result column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePastStatsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePastStatsQuery object.
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
            $modelName = 'deploy\\model\\PastStats';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PastStatsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PastStatsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PastStatsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PastStatsQuery) {
            return $criteria;
        }
        $query = new PastStatsQuery(null, null, $modelAlias);

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
     * @return   PastStats|PastStats[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PastStatsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PastStatsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PastStats A model object, or null if the key is not found
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
     * @return                 PastStats A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "stat_type", "stat_result" FROM "past_stats" WHERE "id" = :p0';
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
            $obj = new PastStats();
            $obj->hydrate($row);
            PastStatsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return PastStats|PastStats[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PastStats[]|mixed the list of results, formatted by the current formatter
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
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PastStatsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PastStatsPeer::ID, $keys, Criteria::IN);
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
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PastStatsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PastStatsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PastStatsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the stat_type column
     *
     * Example usage:
     * <code>
     * $query->filterByStatType('fooValue');   // WHERE stat_type = 'fooValue'
     * $query->filterByStatType('%fooValue%'); // WHERE stat_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $statType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function filterByStatType($statType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($statType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $statType)) {
                $statType = str_replace('*', '%', $statType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PastStatsPeer::STAT_TYPE, $statType, $comparison);
    }

    /**
     * Filter the query on the stat_result column
     *
     * Example usage:
     * <code>
     * $query->filterByStatResult('fooValue');   // WHERE stat_result = 'fooValue'
     * $query->filterByStatResult('%fooValue%'); // WHERE stat_result LIKE '%fooValue%'
     * </code>
     *
     * @param     string $statResult The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function filterByStatResult($statResult = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($statResult)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $statResult)) {
                $statResult = str_replace('*', '%', $statResult);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PastStatsPeer::STAT_RESULT, $statResult, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   PastStats $pastStats Object to remove from the list of results
     *
     * @return PastStatsQuery The current query, for fluid interface
     */
    public function prune($pastStats = null)
    {
        if ($pastStats) {
            $this->addUsingAlias(PastStatsPeer::ID, $pastStats->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
