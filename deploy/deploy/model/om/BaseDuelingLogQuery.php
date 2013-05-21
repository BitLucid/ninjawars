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
use deploy\model\DuelingLog;
use deploy\model\DuelingLogPeer;
use deploy\model\DuelingLogQuery;

/**
 * Base class that represents a query for the 'dueling_log' table.
 *
 *
 *
 * @method DuelingLogQuery orderById($order = Criteria::ASC) Order by the id column
 * @method DuelingLogQuery orderByAttacker($order = Criteria::ASC) Order by the attacker column
 * @method DuelingLogQuery orderByDefender($order = Criteria::ASC) Order by the defender column
 * @method DuelingLogQuery orderByWon($order = Criteria::ASC) Order by the won column
 * @method DuelingLogQuery orderByKillpoints($order = Criteria::ASC) Order by the killpoints column
 * @method DuelingLogQuery orderByDate($order = Criteria::ASC) Order by the date column
 *
 * @method DuelingLogQuery groupById() Group by the id column
 * @method DuelingLogQuery groupByAttacker() Group by the attacker column
 * @method DuelingLogQuery groupByDefender() Group by the defender column
 * @method DuelingLogQuery groupByWon() Group by the won column
 * @method DuelingLogQuery groupByKillpoints() Group by the killpoints column
 * @method DuelingLogQuery groupByDate() Group by the date column
 *
 * @method DuelingLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DuelingLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DuelingLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method DuelingLog findOne(PropelPDO $con = null) Return the first DuelingLog matching the query
 * @method DuelingLog findOneOrCreate(PropelPDO $con = null) Return the first DuelingLog matching the query, or a new DuelingLog object populated from the query conditions when no match is found
 *
 * @method DuelingLog findOneByAttacker(string $attacker) Return the first DuelingLog filtered by the attacker column
 * @method DuelingLog findOneByDefender(string $defender) Return the first DuelingLog filtered by the defender column
 * @method DuelingLog findOneByWon(boolean $won) Return the first DuelingLog filtered by the won column
 * @method DuelingLog findOneByKillpoints(int $killpoints) Return the first DuelingLog filtered by the killpoints column
 * @method DuelingLog findOneByDate(string $date) Return the first DuelingLog filtered by the date column
 *
 * @method array findById(int $id) Return DuelingLog objects filtered by the id column
 * @method array findByAttacker(string $attacker) Return DuelingLog objects filtered by the attacker column
 * @method array findByDefender(string $defender) Return DuelingLog objects filtered by the defender column
 * @method array findByWon(boolean $won) Return DuelingLog objects filtered by the won column
 * @method array findByKillpoints(int $killpoints) Return DuelingLog objects filtered by the killpoints column
 * @method array findByDate(string $date) Return DuelingLog objects filtered by the date column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseDuelingLogQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDuelingLogQuery object.
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
            $modelName = 'deploy\\model\\DuelingLog';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DuelingLogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   DuelingLogQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DuelingLogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DuelingLogQuery) {
            return $criteria;
        }
        $query = new DuelingLogQuery(null, null, $modelAlias);

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
     * @return   DuelingLog|DuelingLog[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DuelingLogPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DuelingLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 DuelingLog A model object, or null if the key is not found
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
     * @return                 DuelingLog A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "attacker", "defender", "won", "killpoints", "date" FROM "dueling_log" WHERE "id" = :p0';
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
            $obj = new DuelingLog();
            $obj->hydrate($row);
            DuelingLogPeer::addInstanceToPool($obj, (string) $key);
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
     * @return DuelingLog|DuelingLog[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|DuelingLog[]|mixed the list of results, formatted by the current formatter
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
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DuelingLogPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DuelingLogPeer::ID, $keys, Criteria::IN);
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
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DuelingLogPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DuelingLogPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DuelingLogPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the attacker column
     *
     * Example usage:
     * <code>
     * $query->filterByAttacker('fooValue');   // WHERE attacker = 'fooValue'
     * $query->filterByAttacker('%fooValue%'); // WHERE attacker LIKE '%fooValue%'
     * </code>
     *
     * @param     string $attacker The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByAttacker($attacker = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($attacker)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $attacker)) {
                $attacker = str_replace('*', '%', $attacker);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DuelingLogPeer::ATTACKER, $attacker, $comparison);
    }

    /**
     * Filter the query on the defender column
     *
     * Example usage:
     * <code>
     * $query->filterByDefender('fooValue');   // WHERE defender = 'fooValue'
     * $query->filterByDefender('%fooValue%'); // WHERE defender LIKE '%fooValue%'
     * </code>
     *
     * @param     string $defender The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByDefender($defender = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($defender)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $defender)) {
                $defender = str_replace('*', '%', $defender);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DuelingLogPeer::DEFENDER, $defender, $comparison);
    }

    /**
     * Filter the query on the won column
     *
     * Example usage:
     * <code>
     * $query->filterByWon(true); // WHERE won = true
     * $query->filterByWon('yes'); // WHERE won = true
     * </code>
     *
     * @param     boolean|string $won The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByWon($won = null, $comparison = null)
    {
        if (is_string($won)) {
            $won = in_array(strtolower($won), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(DuelingLogPeer::WON, $won, $comparison);
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
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByKillpoints($killpoints = null, $comparison = null)
    {
        if (is_array($killpoints)) {
            $useMinMax = false;
            if (isset($killpoints['min'])) {
                $this->addUsingAlias(DuelingLogPeer::KILLPOINTS, $killpoints['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($killpoints['max'])) {
                $this->addUsingAlias(DuelingLogPeer::KILLPOINTS, $killpoints['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DuelingLogPeer::KILLPOINTS, $killpoints, $comparison);
    }

    /**
     * Filter the query on the date column
     *
     * Example usage:
     * <code>
     * $query->filterByDate('2011-03-14'); // WHERE date = '2011-03-14'
     * $query->filterByDate('now'); // WHERE date = '2011-03-14'
     * $query->filterByDate(array('max' => 'yesterday')); // WHERE date > '2011-03-13'
     * </code>
     *
     * @param     mixed $date The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function filterByDate($date = null, $comparison = null)
    {
        if (is_array($date)) {
            $useMinMax = false;
            if (isset($date['min'])) {
                $this->addUsingAlias(DuelingLogPeer::DATE, $date['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($date['max'])) {
                $this->addUsingAlias(DuelingLogPeer::DATE, $date['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DuelingLogPeer::DATE, $date, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   DuelingLog $duelingLog Object to remove from the list of results
     *
     * @return DuelingLogQuery The current query, for fluid interface
     */
    public function prune($duelingLog = null)
    {
        if ($duelingLog) {
            $this->addUsingAlias(DuelingLogPeer::ID, $duelingLog->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
