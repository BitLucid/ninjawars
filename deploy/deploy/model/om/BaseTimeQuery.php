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
use deploy\model\Time;
use deploy\model\TimePeer;
use deploy\model\TimeQuery;

/**
 * Base class that represents a query for the 'time' table.
 *
 *
 *
 * @method TimeQuery orderByTimeId($order = Criteria::ASC) Order by the time_id column
 * @method TimeQuery orderByTimeLabel($order = Criteria::ASC) Order by the time_label column
 * @method TimeQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 *
 * @method TimeQuery groupByTimeId() Group by the time_id column
 * @method TimeQuery groupByTimeLabel() Group by the time_label column
 * @method TimeQuery groupByAmount() Group by the amount column
 *
 * @method TimeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TimeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TimeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Time findOne(PropelPDO $con = null) Return the first Time matching the query
 * @method Time findOneOrCreate(PropelPDO $con = null) Return the first Time matching the query, or a new Time object populated from the query conditions when no match is found
 *
 * @method Time findOneByTimeLabel(string $time_label) Return the first Time filtered by the time_label column
 * @method Time findOneByAmount(int $amount) Return the first Time filtered by the amount column
 *
 * @method array findByTimeId(int $time_id) Return Time objects filtered by the time_id column
 * @method array findByTimeLabel(string $time_label) Return Time objects filtered by the time_label column
 * @method array findByAmount(int $amount) Return Time objects filtered by the amount column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseTimeQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTimeQuery object.
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
            $modelName = 'deploy\\model\\Time';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TimeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TimeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TimeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TimeQuery) {
            return $criteria;
        }
        $query = new TimeQuery(null, null, $modelAlias);

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
     * @return   Time|Time[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TimePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TimePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Time A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByTimeId($key, $con = null)
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
     * @return                 Time A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "time_id", "time_label", "amount" FROM "time" WHERE "time_id" = :p0';
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
            $obj = new Time();
            $obj->hydrate($row);
            TimePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Time|Time[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Time[]|mixed the list of results, formatted by the current formatter
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
     * @return TimeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TimePeer::TIME_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TimeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TimePeer::TIME_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the time_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeId(1234); // WHERE time_id = 1234
     * $query->filterByTimeId(array(12, 34)); // WHERE time_id IN (12, 34)
     * $query->filterByTimeId(array('min' => 12)); // WHERE time_id >= 12
     * $query->filterByTimeId(array('max' => 12)); // WHERE time_id <= 12
     * </code>
     *
     * @param     mixed $timeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TimeQuery The current query, for fluid interface
     */
    public function filterByTimeId($timeId = null, $comparison = null)
    {
        if (is_array($timeId)) {
            $useMinMax = false;
            if (isset($timeId['min'])) {
                $this->addUsingAlias(TimePeer::TIME_ID, $timeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeId['max'])) {
                $this->addUsingAlias(TimePeer::TIME_ID, $timeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TimePeer::TIME_ID, $timeId, $comparison);
    }

    /**
     * Filter the query on the time_label column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeLabel('fooValue');   // WHERE time_label = 'fooValue'
     * $query->filterByTimeLabel('%fooValue%'); // WHERE time_label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $timeLabel The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TimeQuery The current query, for fluid interface
     */
    public function filterByTimeLabel($timeLabel = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($timeLabel)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $timeLabel)) {
                $timeLabel = str_replace('*', '%', $timeLabel);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TimePeer::TIME_LABEL, $timeLabel, $comparison);
    }

    /**
     * Filter the query on the amount column
     *
     * Example usage:
     * <code>
     * $query->filterByAmount(1234); // WHERE amount = 1234
     * $query->filterByAmount(array(12, 34)); // WHERE amount IN (12, 34)
     * $query->filterByAmount(array('min' => 12)); // WHERE amount >= 12
     * $query->filterByAmount(array('max' => 12)); // WHERE amount <= 12
     * </code>
     *
     * @param     mixed $amount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TimeQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(TimePeer::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(TimePeer::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TimePeer::AMOUNT, $amount, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Time $time Object to remove from the list of results
     *
     * @return TimeQuery The current query, for fluid interface
     */
    public function prune($time = null)
    {
        if ($time) {
            $this->addUsingAlias(TimePeer::TIME_ID, $time->getTimeId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
