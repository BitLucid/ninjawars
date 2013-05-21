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
use deploy\model\Flags;
use deploy\model\FlagsPeer;
use deploy\model\FlagsQuery;

/**
 * Base class that represents a query for the 'flags' table.
 *
 *
 *
 * @method FlagsQuery orderByFlagId($order = Criteria::ASC) Order by the flag_id column
 * @method FlagsQuery orderByFlag($order = Criteria::ASC) Order by the flag column
 * @method FlagsQuery orderByFlagType($order = Criteria::ASC) Order by the flag_type column
 *
 * @method FlagsQuery groupByFlagId() Group by the flag_id column
 * @method FlagsQuery groupByFlag() Group by the flag column
 * @method FlagsQuery groupByFlagType() Group by the flag_type column
 *
 * @method FlagsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FlagsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FlagsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Flags findOne(PropelPDO $con = null) Return the first Flags matching the query
 * @method Flags findOneOrCreate(PropelPDO $con = null) Return the first Flags matching the query, or a new Flags object populated from the query conditions when no match is found
 *
 * @method Flags findOneByFlag(string $flag) Return the first Flags filtered by the flag column
 * @method Flags findOneByFlagType(int $flag_type) Return the first Flags filtered by the flag_type column
 *
 * @method array findByFlagId(int $flag_id) Return Flags objects filtered by the flag_id column
 * @method array findByFlag(string $flag) Return Flags objects filtered by the flag column
 * @method array findByFlagType(int $flag_type) Return Flags objects filtered by the flag_type column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseFlagsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFlagsQuery object.
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
            $modelName = 'deploy\\model\\Flags';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FlagsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FlagsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FlagsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FlagsQuery) {
            return $criteria;
        }
        $query = new FlagsQuery(null, null, $modelAlias);

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
     * @return   Flags|Flags[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FlagsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FlagsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Flags A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByFlagId($key, $con = null)
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
     * @return                 Flags A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "flag_id", "flag", "flag_type" FROM "flags" WHERE "flag_id" = :p0';
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
            $obj = new Flags();
            $obj->hydrate($row);
            FlagsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Flags|Flags[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Flags[]|mixed the list of results, formatted by the current formatter
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
     * @return FlagsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FlagsPeer::FLAG_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FlagsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FlagsPeer::FLAG_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the flag_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFlagId(1234); // WHERE flag_id = 1234
     * $query->filterByFlagId(array(12, 34)); // WHERE flag_id IN (12, 34)
     * $query->filterByFlagId(array('min' => 12)); // WHERE flag_id >= 12
     * $query->filterByFlagId(array('max' => 12)); // WHERE flag_id <= 12
     * </code>
     *
     * @param     mixed $flagId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FlagsQuery The current query, for fluid interface
     */
    public function filterByFlagId($flagId = null, $comparison = null)
    {
        if (is_array($flagId)) {
            $useMinMax = false;
            if (isset($flagId['min'])) {
                $this->addUsingAlias(FlagsPeer::FLAG_ID, $flagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($flagId['max'])) {
                $this->addUsingAlias(FlagsPeer::FLAG_ID, $flagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FlagsPeer::FLAG_ID, $flagId, $comparison);
    }

    /**
     * Filter the query on the flag column
     *
     * Example usage:
     * <code>
     * $query->filterByFlag('fooValue');   // WHERE flag = 'fooValue'
     * $query->filterByFlag('%fooValue%'); // WHERE flag LIKE '%fooValue%'
     * </code>
     *
     * @param     string $flag The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FlagsQuery The current query, for fluid interface
     */
    public function filterByFlag($flag = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($flag)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $flag)) {
                $flag = str_replace('*', '%', $flag);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FlagsPeer::FLAG, $flag, $comparison);
    }

    /**
     * Filter the query on the flag_type column
     *
     * Example usage:
     * <code>
     * $query->filterByFlagType(1234); // WHERE flag_type = 1234
     * $query->filterByFlagType(array(12, 34)); // WHERE flag_type IN (12, 34)
     * $query->filterByFlagType(array('min' => 12)); // WHERE flag_type >= 12
     * $query->filterByFlagType(array('max' => 12)); // WHERE flag_type <= 12
     * </code>
     *
     * @param     mixed $flagType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FlagsQuery The current query, for fluid interface
     */
    public function filterByFlagType($flagType = null, $comparison = null)
    {
        if (is_array($flagType)) {
            $useMinMax = false;
            if (isset($flagType['min'])) {
                $this->addUsingAlias(FlagsPeer::FLAG_TYPE, $flagType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($flagType['max'])) {
                $this->addUsingAlias(FlagsPeer::FLAG_TYPE, $flagType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FlagsPeer::FLAG_TYPE, $flagType, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Flags $flags Object to remove from the list of results
     *
     * @return FlagsQuery The current query, for fluid interface
     */
    public function prune($flags = null)
    {
        if ($flags) {
            $this->addUsingAlias(FlagsPeer::FLAG_ID, $flags->getFlagId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
