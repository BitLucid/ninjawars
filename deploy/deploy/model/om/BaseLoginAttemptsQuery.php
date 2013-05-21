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
use deploy\model\LoginAttempts;
use deploy\model\LoginAttemptsPeer;
use deploy\model\LoginAttemptsQuery;

/**
 * Base class that represents a query for the 'login_attempts' table.
 *
 *
 *
 * @method LoginAttemptsQuery orderByAttemptId($order = Criteria::ASC) Order by the attempt_id column
 * @method LoginAttemptsQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method LoginAttemptsQuery orderByUaString($order = Criteria::ASC) Order by the ua_string column
 * @method LoginAttemptsQuery orderByIp($order = Criteria::ASC) Order by the ip column
 * @method LoginAttemptsQuery orderBySuccessful($order = Criteria::ASC) Order by the successful column
 * @method LoginAttemptsQuery orderByAdditionalInfo($order = Criteria::ASC) Order by the additional_info column
 * @method LoginAttemptsQuery orderByAttemptDate($order = Criteria::ASC) Order by the attempt_date column
 *
 * @method LoginAttemptsQuery groupByAttemptId() Group by the attempt_id column
 * @method LoginAttemptsQuery groupByUsername() Group by the username column
 * @method LoginAttemptsQuery groupByUaString() Group by the ua_string column
 * @method LoginAttemptsQuery groupByIp() Group by the ip column
 * @method LoginAttemptsQuery groupBySuccessful() Group by the successful column
 * @method LoginAttemptsQuery groupByAdditionalInfo() Group by the additional_info column
 * @method LoginAttemptsQuery groupByAttemptDate() Group by the attempt_date column
 *
 * @method LoginAttemptsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method LoginAttemptsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method LoginAttemptsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method LoginAttempts findOne(PropelPDO $con = null) Return the first LoginAttempts matching the query
 * @method LoginAttempts findOneOrCreate(PropelPDO $con = null) Return the first LoginAttempts matching the query, or a new LoginAttempts object populated from the query conditions when no match is found
 *
 * @method LoginAttempts findOneByUsername(string $username) Return the first LoginAttempts filtered by the username column
 * @method LoginAttempts findOneByUaString(string $ua_string) Return the first LoginAttempts filtered by the ua_string column
 * @method LoginAttempts findOneByIp(string $ip) Return the first LoginAttempts filtered by the ip column
 * @method LoginAttempts findOneBySuccessful(int $successful) Return the first LoginAttempts filtered by the successful column
 * @method LoginAttempts findOneByAdditionalInfo(string $additional_info) Return the first LoginAttempts filtered by the additional_info column
 * @method LoginAttempts findOneByAttemptDate(string $attempt_date) Return the first LoginAttempts filtered by the attempt_date column
 *
 * @method array findByAttemptId(int $attempt_id) Return LoginAttempts objects filtered by the attempt_id column
 * @method array findByUsername(string $username) Return LoginAttempts objects filtered by the username column
 * @method array findByUaString(string $ua_string) Return LoginAttempts objects filtered by the ua_string column
 * @method array findByIp(string $ip) Return LoginAttempts objects filtered by the ip column
 * @method array findBySuccessful(int $successful) Return LoginAttempts objects filtered by the successful column
 * @method array findByAdditionalInfo(string $additional_info) Return LoginAttempts objects filtered by the additional_info column
 * @method array findByAttemptDate(string $attempt_date) Return LoginAttempts objects filtered by the attempt_date column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseLoginAttemptsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseLoginAttemptsQuery object.
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
            $modelName = 'deploy\\model\\LoginAttempts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LoginAttemptsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   LoginAttemptsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LoginAttemptsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LoginAttemptsQuery) {
            return $criteria;
        }
        $query = new LoginAttemptsQuery(null, null, $modelAlias);

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
     * @return   LoginAttempts|LoginAttempts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LoginAttemptsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LoginAttemptsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 LoginAttempts A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAttemptId($key, $con = null)
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
     * @return                 LoginAttempts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "attempt_id", "username", "ua_string", "ip", "successful", "additional_info", "attempt_date" FROM "login_attempts" WHERE "attempt_id" = :p0';
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
            $obj = new LoginAttempts();
            $obj->hydrate($row);
            LoginAttemptsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return LoginAttempts|LoginAttempts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|LoginAttempts[]|mixed the list of results, formatted by the current formatter
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
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the attempt_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttemptId(1234); // WHERE attempt_id = 1234
     * $query->filterByAttemptId(array(12, 34)); // WHERE attempt_id IN (12, 34)
     * $query->filterByAttemptId(array('min' => 12)); // WHERE attempt_id >= 12
     * $query->filterByAttemptId(array('max' => 12)); // WHERE attempt_id <= 12
     * </code>
     *
     * @param     mixed $attemptId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByAttemptId($attemptId = null, $comparison = null)
    {
        if (is_array($attemptId)) {
            $useMinMax = false;
            if (isset($attemptId['min'])) {
                $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $attemptId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attemptId['max'])) {
                $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $attemptId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $attemptId, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the ua_string column
     *
     * Example usage:
     * <code>
     * $query->filterByUaString('fooValue');   // WHERE ua_string = 'fooValue'
     * $query->filterByUaString('%fooValue%'); // WHERE ua_string LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uaString The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByUaString($uaString = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uaString)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $uaString)) {
                $uaString = str_replace('*', '%', $uaString);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::UA_STRING, $uaString, $comparison);
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
     * @return LoginAttemptsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LoginAttemptsPeer::IP, $ip, $comparison);
    }

    /**
     * Filter the query on the successful column
     *
     * Example usage:
     * <code>
     * $query->filterBySuccessful(1234); // WHERE successful = 1234
     * $query->filterBySuccessful(array(12, 34)); // WHERE successful IN (12, 34)
     * $query->filterBySuccessful(array('min' => 12)); // WHERE successful >= 12
     * $query->filterBySuccessful(array('max' => 12)); // WHERE successful <= 12
     * </code>
     *
     * @param     mixed $successful The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterBySuccessful($successful = null, $comparison = null)
    {
        if (is_array($successful)) {
            $useMinMax = false;
            if (isset($successful['min'])) {
                $this->addUsingAlias(LoginAttemptsPeer::SUCCESSFUL, $successful['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($successful['max'])) {
                $this->addUsingAlias(LoginAttemptsPeer::SUCCESSFUL, $successful['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::SUCCESSFUL, $successful, $comparison);
    }

    /**
     * Filter the query on the additional_info column
     *
     * Example usage:
     * <code>
     * $query->filterByAdditionalInfo('fooValue');   // WHERE additional_info = 'fooValue'
     * $query->filterByAdditionalInfo('%fooValue%'); // WHERE additional_info LIKE '%fooValue%'
     * </code>
     *
     * @param     string $additionalInfo The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByAdditionalInfo($additionalInfo = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($additionalInfo)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $additionalInfo)) {
                $additionalInfo = str_replace('*', '%', $additionalInfo);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::ADDITIONAL_INFO, $additionalInfo, $comparison);
    }

    /**
     * Filter the query on the attempt_date column
     *
     * Example usage:
     * <code>
     * $query->filterByAttemptDate('2011-03-14'); // WHERE attempt_date = '2011-03-14'
     * $query->filterByAttemptDate('now'); // WHERE attempt_date = '2011-03-14'
     * $query->filterByAttemptDate(array('max' => 'yesterday')); // WHERE attempt_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $attemptDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByAttemptDate($attemptDate = null, $comparison = null)
    {
        if (is_array($attemptDate)) {
            $useMinMax = false;
            if (isset($attemptDate['min'])) {
                $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_DATE, $attemptDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attemptDate['max'])) {
                $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_DATE, $attemptDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_DATE, $attemptDate, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   LoginAttempts $loginAttempts Object to remove from the list of results
     *
     * @return LoginAttemptsQuery The current query, for fluid interface
     */
    public function prune($loginAttempts = null)
    {
        if ($loginAttempts) {
            $this->addUsingAlias(LoginAttemptsPeer::ATTEMPT_ID, $loginAttempts->getAttemptId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
