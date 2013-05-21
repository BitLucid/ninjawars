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
use deploy\model\DupedUnames;
use deploy\model\DupedUnamesPeer;
use deploy\model\DupedUnamesQuery;

/**
 * Base class that represents a query for the 'duped_unames' table.
 *
 *
 *
 * @method DupedUnamesQuery orderByUname($order = Criteria::ASC) Order by the uname column
 * @method DupedUnamesQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method DupedUnamesQuery orderByCreatedDate($order = Criteria::ASC) Order by the created_date column
 * @method DupedUnamesQuery orderByRelativeAge($order = Criteria::ASC) Order by the relative_age column
 * @method DupedUnamesQuery orderByPlayerId($order = Criteria::ASC) Order by the player_id column
 * @method DupedUnamesQuery orderByLocked($order = Criteria::ASC) Order by the locked column
 *
 * @method DupedUnamesQuery groupByUname() Group by the uname column
 * @method DupedUnamesQuery groupByEmail() Group by the email column
 * @method DupedUnamesQuery groupByCreatedDate() Group by the created_date column
 * @method DupedUnamesQuery groupByRelativeAge() Group by the relative_age column
 * @method DupedUnamesQuery groupByPlayerId() Group by the player_id column
 * @method DupedUnamesQuery groupByLocked() Group by the locked column
 *
 * @method DupedUnamesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DupedUnamesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DupedUnamesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method DupedUnames findOne(PropelPDO $con = null) Return the first DupedUnames matching the query
 * @method DupedUnames findOneOrCreate(PropelPDO $con = null) Return the first DupedUnames matching the query, or a new DupedUnames object populated from the query conditions when no match is found
 *
 * @method DupedUnames findOneByUname(string $uname) Return the first DupedUnames filtered by the uname column
 * @method DupedUnames findOneByEmail(string $email) Return the first DupedUnames filtered by the email column
 * @method DupedUnames findOneByCreatedDate(string $created_date) Return the first DupedUnames filtered by the created_date column
 * @method DupedUnames findOneByRelativeAge(int $relative_age) Return the first DupedUnames filtered by the relative_age column
 * @method DupedUnames findOneByLocked(boolean $locked) Return the first DupedUnames filtered by the locked column
 *
 * @method array findByUname(string $uname) Return DupedUnames objects filtered by the uname column
 * @method array findByEmail(string $email) Return DupedUnames objects filtered by the email column
 * @method array findByCreatedDate(string $created_date) Return DupedUnames objects filtered by the created_date column
 * @method array findByRelativeAge(int $relative_age) Return DupedUnames objects filtered by the relative_age column
 * @method array findByPlayerId(int $player_id) Return DupedUnames objects filtered by the player_id column
 * @method array findByLocked(boolean $locked) Return DupedUnames objects filtered by the locked column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseDupedUnamesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDupedUnamesQuery object.
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
            $modelName = 'deploy\\model\\DupedUnames';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DupedUnamesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   DupedUnamesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DupedUnamesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DupedUnamesQuery) {
            return $criteria;
        }
        $query = new DupedUnamesQuery(null, null, $modelAlias);

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
     * @return   DupedUnames|DupedUnames[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DupedUnamesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DupedUnamesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 DupedUnames A model object, or null if the key is not found
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
     * @return                 DupedUnames A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "uname", "email", "created_date", "relative_age", "player_id", "locked" FROM "duped_unames" WHERE "player_id" = :p0';
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
            $obj = new DupedUnames();
            $obj->hydrate($row);
            DupedUnamesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return DupedUnames|DupedUnames[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|DupedUnames[]|mixed the list of results, formatted by the current formatter
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
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $keys, Criteria::IN);
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
     * @return DupedUnamesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DupedUnamesPeer::UNAME, $uname, $comparison);
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
     * @return DupedUnamesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DupedUnamesPeer::EMAIL, $email, $comparison);
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
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByCreatedDate($createdDate = null, $comparison = null)
    {
        if (is_array($createdDate)) {
            $useMinMax = false;
            if (isset($createdDate['min'])) {
                $this->addUsingAlias(DupedUnamesPeer::CREATED_DATE, $createdDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdDate['max'])) {
                $this->addUsingAlias(DupedUnamesPeer::CREATED_DATE, $createdDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DupedUnamesPeer::CREATED_DATE, $createdDate, $comparison);
    }

    /**
     * Filter the query on the relative_age column
     *
     * Example usage:
     * <code>
     * $query->filterByRelativeAge(1234); // WHERE relative_age = 1234
     * $query->filterByRelativeAge(array(12, 34)); // WHERE relative_age IN (12, 34)
     * $query->filterByRelativeAge(array('min' => 12)); // WHERE relative_age >= 12
     * $query->filterByRelativeAge(array('max' => 12)); // WHERE relative_age <= 12
     * </code>
     *
     * @param     mixed $relativeAge The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByRelativeAge($relativeAge = null, $comparison = null)
    {
        if (is_array($relativeAge)) {
            $useMinMax = false;
            if (isset($relativeAge['min'])) {
                $this->addUsingAlias(DupedUnamesPeer::RELATIVE_AGE, $relativeAge['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($relativeAge['max'])) {
                $this->addUsingAlias(DupedUnamesPeer::RELATIVE_AGE, $relativeAge['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DupedUnamesPeer::RELATIVE_AGE, $relativeAge, $comparison);
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
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the locked column
     *
     * Example usage:
     * <code>
     * $query->filterByLocked(true); // WHERE locked = true
     * $query->filterByLocked('yes'); // WHERE locked = true
     * </code>
     *
     * @param     boolean|string $locked The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function filterByLocked($locked = null, $comparison = null)
    {
        if (is_string($locked)) {
            $locked = in_array(strtolower($locked), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(DupedUnamesPeer::LOCKED, $locked, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   DupedUnames $dupedUnames Object to remove from the list of results
     *
     * @return DupedUnamesQuery The current query, for fluid interface
     */
    public function prune($dupedUnames = null)
    {
        if ($dupedUnames) {
            $this->addUsingAlias(DupedUnamesPeer::PLAYER_ID, $dupedUnames->getPlayerId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
