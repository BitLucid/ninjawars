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
use deploy\model\PplOnline;
use deploy\model\PplOnlinePeer;
use deploy\model\PplOnlineQuery;

/**
 * Base class that represents a query for the 'ppl_online' table.
 *
 *
 *
 * @method PplOnlineQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 * @method PplOnlineQuery orderByActivity($order = Criteria::ASC) Order by the activity column
 * @method PplOnlineQuery orderByMember($order = Criteria::ASC) Order by the member column
 * @method PplOnlineQuery orderByIpAddress($order = Criteria::ASC) Order by the ip_address column
 * @method PplOnlineQuery orderByRefurl($order = Criteria::ASC) Order by the refurl column
 * @method PplOnlineQuery orderByUserAgent($order = Criteria::ASC) Order by the user_agent column
 *
 * @method PplOnlineQuery groupBySessionId() Group by the session_id column
 * @method PplOnlineQuery groupByActivity() Group by the activity column
 * @method PplOnlineQuery groupByMember() Group by the member column
 * @method PplOnlineQuery groupByIpAddress() Group by the ip_address column
 * @method PplOnlineQuery groupByRefurl() Group by the refurl column
 * @method PplOnlineQuery groupByUserAgent() Group by the user_agent column
 *
 * @method PplOnlineQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PplOnlineQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PplOnlineQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PplOnline findOne(PropelPDO $con = null) Return the first PplOnline matching the query
 * @method PplOnline findOneOrCreate(PropelPDO $con = null) Return the first PplOnline matching the query, or a new PplOnline object populated from the query conditions when no match is found
 *
 * @method PplOnline findOneByActivity(string $activity) Return the first PplOnline filtered by the activity column
 * @method PplOnline findOneByMember(boolean $member) Return the first PplOnline filtered by the member column
 * @method PplOnline findOneByIpAddress(string $ip_address) Return the first PplOnline filtered by the ip_address column
 * @method PplOnline findOneByRefurl(string $refurl) Return the first PplOnline filtered by the refurl column
 * @method PplOnline findOneByUserAgent(string $user_agent) Return the first PplOnline filtered by the user_agent column
 *
 * @method array findBySessionId(string $session_id) Return PplOnline objects filtered by the session_id column
 * @method array findByActivity(string $activity) Return PplOnline objects filtered by the activity column
 * @method array findByMember(boolean $member) Return PplOnline objects filtered by the member column
 * @method array findByIpAddress(string $ip_address) Return PplOnline objects filtered by the ip_address column
 * @method array findByRefurl(string $refurl) Return PplOnline objects filtered by the refurl column
 * @method array findByUserAgent(string $user_agent) Return PplOnline objects filtered by the user_agent column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePplOnlineQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePplOnlineQuery object.
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
            $modelName = 'deploy\\model\\PplOnline';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PplOnlineQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PplOnlineQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PplOnlineQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PplOnlineQuery) {
            return $criteria;
        }
        $query = new PplOnlineQuery(null, null, $modelAlias);

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
     * @return   PplOnline|PplOnline[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PplOnlinePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PplOnlinePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PplOnline A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneBySessionId($key, $con = null)
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
     * @return                 PplOnline A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "session_id", "activity", "member", "ip_address", "refurl", "user_agent" FROM "ppl_online" WHERE "session_id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new PplOnline();
            $obj->hydrate($row);
            PplOnlinePeer::addInstanceToPool($obj, (string) $key);
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
     * @return PplOnline|PplOnline[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PplOnline[]|mixed the list of results, formatted by the current formatter
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
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PplOnlinePeer::SESSION_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PplOnlinePeer::SESSION_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the session_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionId('fooValue');   // WHERE session_id = 'fooValue'
     * $query->filterBySessionId('%fooValue%'); // WHERE session_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessionId)) {
                $sessionId = str_replace('*', '%', $sessionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PplOnlinePeer::SESSION_ID, $sessionId, $comparison);
    }

    /**
     * Filter the query on the activity column
     *
     * Example usage:
     * <code>
     * $query->filterByActivity('2011-03-14'); // WHERE activity = '2011-03-14'
     * $query->filterByActivity('now'); // WHERE activity = '2011-03-14'
     * $query->filterByActivity(array('max' => 'yesterday')); // WHERE activity > '2011-03-13'
     * </code>
     *
     * @param     mixed $activity The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByActivity($activity = null, $comparison = null)
    {
        if (is_array($activity)) {
            $useMinMax = false;
            if (isset($activity['min'])) {
                $this->addUsingAlias(PplOnlinePeer::ACTIVITY, $activity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($activity['max'])) {
                $this->addUsingAlias(PplOnlinePeer::ACTIVITY, $activity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PplOnlinePeer::ACTIVITY, $activity, $comparison);
    }

    /**
     * Filter the query on the member column
     *
     * Example usage:
     * <code>
     * $query->filterByMember(true); // WHERE member = true
     * $query->filterByMember('yes'); // WHERE member = true
     * </code>
     *
     * @param     boolean|string $member The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByMember($member = null, $comparison = null)
    {
        if (is_string($member)) {
            $member = in_array(strtolower($member), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(PplOnlinePeer::MEMBER, $member, $comparison);
    }

    /**
     * Filter the query on the ip_address column
     *
     * Example usage:
     * <code>
     * $query->filterByIpAddress('fooValue');   // WHERE ip_address = 'fooValue'
     * $query->filterByIpAddress('%fooValue%'); // WHERE ip_address LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ipAddress The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByIpAddress($ipAddress = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ipAddress)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ipAddress)) {
                $ipAddress = str_replace('*', '%', $ipAddress);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PplOnlinePeer::IP_ADDRESS, $ipAddress, $comparison);
    }

    /**
     * Filter the query on the refurl column
     *
     * Example usage:
     * <code>
     * $query->filterByRefurl('fooValue');   // WHERE refurl = 'fooValue'
     * $query->filterByRefurl('%fooValue%'); // WHERE refurl LIKE '%fooValue%'
     * </code>
     *
     * @param     string $refurl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByRefurl($refurl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($refurl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $refurl)) {
                $refurl = str_replace('*', '%', $refurl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PplOnlinePeer::REFURL, $refurl, $comparison);
    }

    /**
     * Filter the query on the user_agent column
     *
     * Example usage:
     * <code>
     * $query->filterByUserAgent('fooValue');   // WHERE user_agent = 'fooValue'
     * $query->filterByUserAgent('%fooValue%'); // WHERE user_agent LIKE '%fooValue%'
     * </code>
     *
     * @param     string $userAgent The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function filterByUserAgent($userAgent = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($userAgent)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $userAgent)) {
                $userAgent = str_replace('*', '%', $userAgent);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PplOnlinePeer::USER_AGENT, $userAgent, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   PplOnline $pplOnline Object to remove from the list of results
     *
     * @return PplOnlineQuery The current query, for fluid interface
     */
    public function prune($pplOnline = null)
    {
        if ($pplOnline) {
            $this->addUsingAlias(PplOnlinePeer::SESSION_ID, $pplOnline->getSessionId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
