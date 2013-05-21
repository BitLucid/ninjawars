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
use deploy\model\AccountPlayers;
use deploy\model\Accounts;
use deploy\model\AccountsPeer;
use deploy\model\AccountsQuery;

/**
 * Base class that represents a query for the 'accounts' table.
 *
 *
 *
 * @method AccountsQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method AccountsQuery orderByAccountIdentity($order = Criteria::ASC) Order by the account_identity column
 * @method AccountsQuery orderByPhash($order = Criteria::ASC) Order by the phash column
 * @method AccountsQuery orderByActiveEmail($order = Criteria::ASC) Order by the active_email column
 * @method AccountsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method AccountsQuery orderByOperational($order = Criteria::ASC) Order by the operational column
 * @method AccountsQuery orderByCreatedDate($order = Criteria::ASC) Order by the created_date column
 * @method AccountsQuery orderByLastLogin($order = Criteria::ASC) Order by the last_login column
 * @method AccountsQuery orderByLastLoginFailure($order = Criteria::ASC) Order by the last_login_failure column
 * @method AccountsQuery orderByKarmaTotal($order = Criteria::ASC) Order by the karma_total column
 * @method AccountsQuery orderByLastIp($order = Criteria::ASC) Order by the last_ip column
 * @method AccountsQuery orderByConfirmed($order = Criteria::ASC) Order by the confirmed column
 * @method AccountsQuery orderByVerificationNumber($order = Criteria::ASC) Order by the verification_number column
 *
 * @method AccountsQuery groupByAccountId() Group by the account_id column
 * @method AccountsQuery groupByAccountIdentity() Group by the account_identity column
 * @method AccountsQuery groupByPhash() Group by the phash column
 * @method AccountsQuery groupByActiveEmail() Group by the active_email column
 * @method AccountsQuery groupByType() Group by the type column
 * @method AccountsQuery groupByOperational() Group by the operational column
 * @method AccountsQuery groupByCreatedDate() Group by the created_date column
 * @method AccountsQuery groupByLastLogin() Group by the last_login column
 * @method AccountsQuery groupByLastLoginFailure() Group by the last_login_failure column
 * @method AccountsQuery groupByKarmaTotal() Group by the karma_total column
 * @method AccountsQuery groupByLastIp() Group by the last_ip column
 * @method AccountsQuery groupByConfirmed() Group by the confirmed column
 * @method AccountsQuery groupByVerificationNumber() Group by the verification_number column
 *
 * @method AccountsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountsQuery leftJoinAccountPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountPlayers relation
 * @method AccountsQuery rightJoinAccountPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountPlayers relation
 * @method AccountsQuery innerJoinAccountPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountPlayers relation
 *
 * @method Accounts findOne(PropelPDO $con = null) Return the first Accounts matching the query
 * @method Accounts findOneOrCreate(PropelPDO $con = null) Return the first Accounts matching the query, or a new Accounts object populated from the query conditions when no match is found
 *
 * @method Accounts findOneByAccountIdentity(string $account_identity) Return the first Accounts filtered by the account_identity column
 * @method Accounts findOneByPhash(string $phash) Return the first Accounts filtered by the phash column
 * @method Accounts findOneByActiveEmail(string $active_email) Return the first Accounts filtered by the active_email column
 * @method Accounts findOneByType(int $type) Return the first Accounts filtered by the type column
 * @method Accounts findOneByOperational(boolean $operational) Return the first Accounts filtered by the operational column
 * @method Accounts findOneByCreatedDate(string $created_date) Return the first Accounts filtered by the created_date column
 * @method Accounts findOneByLastLogin(string $last_login) Return the first Accounts filtered by the last_login column
 * @method Accounts findOneByLastLoginFailure(string $last_login_failure) Return the first Accounts filtered by the last_login_failure column
 * @method Accounts findOneByKarmaTotal(int $karma_total) Return the first Accounts filtered by the karma_total column
 * @method Accounts findOneByLastIp(string $last_ip) Return the first Accounts filtered by the last_ip column
 * @method Accounts findOneByConfirmed(int $confirmed) Return the first Accounts filtered by the confirmed column
 * @method Accounts findOneByVerificationNumber(string $verification_number) Return the first Accounts filtered by the verification_number column
 *
 * @method array findByAccountId(int $account_id) Return Accounts objects filtered by the account_id column
 * @method array findByAccountIdentity(string $account_identity) Return Accounts objects filtered by the account_identity column
 * @method array findByPhash(string $phash) Return Accounts objects filtered by the phash column
 * @method array findByActiveEmail(string $active_email) Return Accounts objects filtered by the active_email column
 * @method array findByType(int $type) Return Accounts objects filtered by the type column
 * @method array findByOperational(boolean $operational) Return Accounts objects filtered by the operational column
 * @method array findByCreatedDate(string $created_date) Return Accounts objects filtered by the created_date column
 * @method array findByLastLogin(string $last_login) Return Accounts objects filtered by the last_login column
 * @method array findByLastLoginFailure(string $last_login_failure) Return Accounts objects filtered by the last_login_failure column
 * @method array findByKarmaTotal(int $karma_total) Return Accounts objects filtered by the karma_total column
 * @method array findByLastIp(string $last_ip) Return Accounts objects filtered by the last_ip column
 * @method array findByConfirmed(int $confirmed) Return Accounts objects filtered by the confirmed column
 * @method array findByVerificationNumber(string $verification_number) Return Accounts objects filtered by the verification_number column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseAccountsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountsQuery object.
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
            $modelName = 'deploy\\model\\Accounts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountsQuery) {
            return $criteria;
        }
        $query = new AccountsQuery(null, null, $modelAlias);

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
     * @return   Accounts|Accounts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Accounts A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountId($key, $con = null)
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
     * @return                 Accounts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "account_id", "account_identity", "phash", "active_email", "type", "operational", "created_date", "last_login", "last_login_failure", "karma_total", "last_ip", "confirmed", "verification_number" FROM "accounts" WHERE "account_id" = :p0';
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
            $obj = new Accounts();
            $obj->hydrate($row);
            AccountsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Accounts|Accounts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Accounts[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id >= 12
     * $query->filterByAccountId(array('max' => 12)); // WHERE account_id <= 12
     * </code>
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the account_identity column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountIdentity('fooValue');   // WHERE account_identity = 'fooValue'
     * $query->filterByAccountIdentity('%fooValue%'); // WHERE account_identity LIKE '%fooValue%'
     * </code>
     *
     * @param     string $accountIdentity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByAccountIdentity($accountIdentity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($accountIdentity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $accountIdentity)) {
                $accountIdentity = str_replace('*', '%', $accountIdentity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountsPeer::ACCOUNT_IDENTITY, $accountIdentity, $comparison);
    }

    /**
     * Filter the query on the phash column
     *
     * Example usage:
     * <code>
     * $query->filterByPhash('fooValue');   // WHERE phash = 'fooValue'
     * $query->filterByPhash('%fooValue%'); // WHERE phash LIKE '%fooValue%'
     * </code>
     *
     * @param     string $phash The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByPhash($phash = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($phash)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $phash)) {
                $phash = str_replace('*', '%', $phash);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountsPeer::PHASH, $phash, $comparison);
    }

    /**
     * Filter the query on the active_email column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveEmail('fooValue');   // WHERE active_email = 'fooValue'
     * $query->filterByActiveEmail('%fooValue%'); // WHERE active_email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $activeEmail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByActiveEmail($activeEmail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($activeEmail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $activeEmail)) {
                $activeEmail = str_replace('*', '%', $activeEmail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountsPeer::ACTIVE_EMAIL, $activeEmail, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type >= 12
     * $query->filterByType(array('max' => 12)); // WHERE type <= 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(AccountsPeer::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(AccountsPeer::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the operational column
     *
     * Example usage:
     * <code>
     * $query->filterByOperational(true); // WHERE operational = true
     * $query->filterByOperational('yes'); // WHERE operational = true
     * </code>
     *
     * @param     boolean|string $operational The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByOperational($operational = null, $comparison = null)
    {
        if (is_string($operational)) {
            $operational = in_array(strtolower($operational), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AccountsPeer::OPERATIONAL, $operational, $comparison);
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
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByCreatedDate($createdDate = null, $comparison = null)
    {
        if (is_array($createdDate)) {
            $useMinMax = false;
            if (isset($createdDate['min'])) {
                $this->addUsingAlias(AccountsPeer::CREATED_DATE, $createdDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdDate['max'])) {
                $this->addUsingAlias(AccountsPeer::CREATED_DATE, $createdDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::CREATED_DATE, $createdDate, $comparison);
    }

    /**
     * Filter the query on the last_login column
     *
     * Example usage:
     * <code>
     * $query->filterByLastLogin('2011-03-14'); // WHERE last_login = '2011-03-14'
     * $query->filterByLastLogin('now'); // WHERE last_login = '2011-03-14'
     * $query->filterByLastLogin(array('max' => 'yesterday')); // WHERE last_login > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastLogin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByLastLogin($lastLogin = null, $comparison = null)
    {
        if (is_array($lastLogin)) {
            $useMinMax = false;
            if (isset($lastLogin['min'])) {
                $this->addUsingAlias(AccountsPeer::LAST_LOGIN, $lastLogin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastLogin['max'])) {
                $this->addUsingAlias(AccountsPeer::LAST_LOGIN, $lastLogin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::LAST_LOGIN, $lastLogin, $comparison);
    }

    /**
     * Filter the query on the last_login_failure column
     *
     * Example usage:
     * <code>
     * $query->filterByLastLoginFailure('2011-03-14'); // WHERE last_login_failure = '2011-03-14'
     * $query->filterByLastLoginFailure('now'); // WHERE last_login_failure = '2011-03-14'
     * $query->filterByLastLoginFailure(array('max' => 'yesterday')); // WHERE last_login_failure > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastLoginFailure The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByLastLoginFailure($lastLoginFailure = null, $comparison = null)
    {
        if (is_array($lastLoginFailure)) {
            $useMinMax = false;
            if (isset($lastLoginFailure['min'])) {
                $this->addUsingAlias(AccountsPeer::LAST_LOGIN_FAILURE, $lastLoginFailure['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastLoginFailure['max'])) {
                $this->addUsingAlias(AccountsPeer::LAST_LOGIN_FAILURE, $lastLoginFailure['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::LAST_LOGIN_FAILURE, $lastLoginFailure, $comparison);
    }

    /**
     * Filter the query on the karma_total column
     *
     * Example usage:
     * <code>
     * $query->filterByKarmaTotal(1234); // WHERE karma_total = 1234
     * $query->filterByKarmaTotal(array(12, 34)); // WHERE karma_total IN (12, 34)
     * $query->filterByKarmaTotal(array('min' => 12)); // WHERE karma_total >= 12
     * $query->filterByKarmaTotal(array('max' => 12)); // WHERE karma_total <= 12
     * </code>
     *
     * @param     mixed $karmaTotal The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByKarmaTotal($karmaTotal = null, $comparison = null)
    {
        if (is_array($karmaTotal)) {
            $useMinMax = false;
            if (isset($karmaTotal['min'])) {
                $this->addUsingAlias(AccountsPeer::KARMA_TOTAL, $karmaTotal['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($karmaTotal['max'])) {
                $this->addUsingAlias(AccountsPeer::KARMA_TOTAL, $karmaTotal['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::KARMA_TOTAL, $karmaTotal, $comparison);
    }

    /**
     * Filter the query on the last_ip column
     *
     * Example usage:
     * <code>
     * $query->filterByLastIp('fooValue');   // WHERE last_ip = 'fooValue'
     * $query->filterByLastIp('%fooValue%'); // WHERE last_ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastIp The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByLastIp($lastIp = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastIp)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastIp)) {
                $lastIp = str_replace('*', '%', $lastIp);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountsPeer::LAST_IP, $lastIp, $comparison);
    }

    /**
     * Filter the query on the confirmed column
     *
     * Example usage:
     * <code>
     * $query->filterByConfirmed(1234); // WHERE confirmed = 1234
     * $query->filterByConfirmed(array(12, 34)); // WHERE confirmed IN (12, 34)
     * $query->filterByConfirmed(array('min' => 12)); // WHERE confirmed >= 12
     * $query->filterByConfirmed(array('max' => 12)); // WHERE confirmed <= 12
     * </code>
     *
     * @param     mixed $confirmed The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByConfirmed($confirmed = null, $comparison = null)
    {
        if (is_array($confirmed)) {
            $useMinMax = false;
            if (isset($confirmed['min'])) {
                $this->addUsingAlias(AccountsPeer::CONFIRMED, $confirmed['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($confirmed['max'])) {
                $this->addUsingAlias(AccountsPeer::CONFIRMED, $confirmed['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountsPeer::CONFIRMED, $confirmed, $comparison);
    }

    /**
     * Filter the query on the verification_number column
     *
     * Example usage:
     * <code>
     * $query->filterByVerificationNumber('fooValue');   // WHERE verification_number = 'fooValue'
     * $query->filterByVerificationNumber('%fooValue%'); // WHERE verification_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $verificationNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function filterByVerificationNumber($verificationNumber = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($verificationNumber)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $verificationNumber)) {
                $verificationNumber = str_replace('*', '%', $verificationNumber);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountsPeer::VERIFICATION_NUMBER, $verificationNumber, $comparison);
    }

    /**
     * Filter the query by a related AccountPlayers object
     *
     * @param   AccountPlayers|PropelObjectCollection $accountPlayers  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountPlayers($accountPlayers, $comparison = null)
    {
        if ($accountPlayers instanceof AccountPlayers) {
            return $this
                ->addUsingAlias(AccountsPeer::ACCOUNT_ID, $accountPlayers->getAccountId(), $comparison);
        } elseif ($accountPlayers instanceof PropelObjectCollection) {
            return $this
                ->useAccountPlayersQuery()
                ->filterByPrimaryKeys($accountPlayers->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountPlayers() only accepts arguments of type AccountPlayers or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountPlayers relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function joinAccountPlayers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountPlayers');

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
            $this->addJoinObject($join, 'AccountPlayers');
        }

        return $this;
    }

    /**
     * Use the AccountPlayers relation AccountPlayers object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\AccountPlayersQuery A secondary query class using the current class as primary query
     */
    public function useAccountPlayersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountPlayers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountPlayers', '\deploy\model\AccountPlayersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Accounts $accounts Object to remove from the list of results
     *
     * @return AccountsQuery The current query, for fluid interface
     */
    public function prune($accounts = null)
    {
        if ($accounts) {
            $this->addUsingAlias(AccountsPeer::ACCOUNT_ID, $accounts->getAccountId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
