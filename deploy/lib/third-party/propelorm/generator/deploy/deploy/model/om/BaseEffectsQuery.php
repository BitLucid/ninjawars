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
use deploy\model\Effects;
use deploy\model\EffectsPeer;
use deploy\model\EffectsQuery;
use deploy\model\ItemEffects;

/**
 * Base class that represents a query for the 'effects' table.
 *
 *
 *
 * @method EffectsQuery orderByEffectId($order = Criteria::ASC) Order by the effect_id column
 * @method EffectsQuery orderByEffectIdentity($order = Criteria::ASC) Order by the effect_identity column
 * @method EffectsQuery orderByEffectName($order = Criteria::ASC) Order by the effect_name column
 * @method EffectsQuery orderByEffectVerb($order = Criteria::ASC) Order by the effect_verb column
 * @method EffectsQuery orderByEffectSelf($order = Criteria::ASC) Order by the effect_self column
 *
 * @method EffectsQuery groupByEffectId() Group by the effect_id column
 * @method EffectsQuery groupByEffectIdentity() Group by the effect_identity column
 * @method EffectsQuery groupByEffectName() Group by the effect_name column
 * @method EffectsQuery groupByEffectVerb() Group by the effect_verb column
 * @method EffectsQuery groupByEffectSelf() Group by the effect_self column
 *
 * @method EffectsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EffectsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EffectsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method EffectsQuery leftJoinItemEffects($relationAlias = null) Adds a LEFT JOIN clause to the query using the ItemEffects relation
 * @method EffectsQuery rightJoinItemEffects($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ItemEffects relation
 * @method EffectsQuery innerJoinItemEffects($relationAlias = null) Adds a INNER JOIN clause to the query using the ItemEffects relation
 *
 * @method Effects findOne(PropelPDO $con = null) Return the first Effects matching the query
 * @method Effects findOneOrCreate(PropelPDO $con = null) Return the first Effects matching the query, or a new Effects object populated from the query conditions when no match is found
 *
 * @method Effects findOneByEffectIdentity(string $effect_identity) Return the first Effects filtered by the effect_identity column
 * @method Effects findOneByEffectName(string $effect_name) Return the first Effects filtered by the effect_name column
 * @method Effects findOneByEffectVerb(string $effect_verb) Return the first Effects filtered by the effect_verb column
 * @method Effects findOneByEffectSelf(boolean $effect_self) Return the first Effects filtered by the effect_self column
 *
 * @method array findByEffectId(int $effect_id) Return Effects objects filtered by the effect_id column
 * @method array findByEffectIdentity(string $effect_identity) Return Effects objects filtered by the effect_identity column
 * @method array findByEffectName(string $effect_name) Return Effects objects filtered by the effect_name column
 * @method array findByEffectVerb(string $effect_verb) Return Effects objects filtered by the effect_verb column
 * @method array findByEffectSelf(boolean $effect_self) Return Effects objects filtered by the effect_self column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseEffectsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEffectsQuery object.
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
            $modelName = 'deploy\\model\\Effects';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new EffectsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   EffectsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EffectsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EffectsQuery) {
            return $criteria;
        }
        $query = new EffectsQuery(null, null, $modelAlias);

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
     * @return   Effects|Effects[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EffectsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EffectsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Effects A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByEffectId($key, $con = null)
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
     * @return                 Effects A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "effect_id", "effect_identity", "effect_name", "effect_verb", "effect_self" FROM "effects" WHERE "effect_id" = :p0';
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
            $obj = new Effects();
            $obj->hydrate($row);
            EffectsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Effects|Effects[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Effects[]|mixed the list of results, formatted by the current formatter
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
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EffectsPeer::EFFECT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EffectsPeer::EFFECT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the effect_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectId(1234); // WHERE effect_id = 1234
     * $query->filterByEffectId(array(12, 34)); // WHERE effect_id IN (12, 34)
     * $query->filterByEffectId(array('min' => 12)); // WHERE effect_id >= 12
     * $query->filterByEffectId(array('max' => 12)); // WHERE effect_id <= 12
     * </code>
     *
     * @param     mixed $effectId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByEffectId($effectId = null, $comparison = null)
    {
        if (is_array($effectId)) {
            $useMinMax = false;
            if (isset($effectId['min'])) {
                $this->addUsingAlias(EffectsPeer::EFFECT_ID, $effectId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($effectId['max'])) {
                $this->addUsingAlias(EffectsPeer::EFFECT_ID, $effectId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EffectsPeer::EFFECT_ID, $effectId, $comparison);
    }

    /**
     * Filter the query on the effect_identity column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectIdentity('fooValue');   // WHERE effect_identity = 'fooValue'
     * $query->filterByEffectIdentity('%fooValue%'); // WHERE effect_identity LIKE '%fooValue%'
     * </code>
     *
     * @param     string $effectIdentity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByEffectIdentity($effectIdentity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($effectIdentity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $effectIdentity)) {
                $effectIdentity = str_replace('*', '%', $effectIdentity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EffectsPeer::EFFECT_IDENTITY, $effectIdentity, $comparison);
    }

    /**
     * Filter the query on the effect_name column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectName('fooValue');   // WHERE effect_name = 'fooValue'
     * $query->filterByEffectName('%fooValue%'); // WHERE effect_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $effectName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByEffectName($effectName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($effectName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $effectName)) {
                $effectName = str_replace('*', '%', $effectName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EffectsPeer::EFFECT_NAME, $effectName, $comparison);
    }

    /**
     * Filter the query on the effect_verb column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectVerb('fooValue');   // WHERE effect_verb = 'fooValue'
     * $query->filterByEffectVerb('%fooValue%'); // WHERE effect_verb LIKE '%fooValue%'
     * </code>
     *
     * @param     string $effectVerb The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByEffectVerb($effectVerb = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($effectVerb)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $effectVerb)) {
                $effectVerb = str_replace('*', '%', $effectVerb);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EffectsPeer::EFFECT_VERB, $effectVerb, $comparison);
    }

    /**
     * Filter the query on the effect_self column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectSelf(true); // WHERE effect_self = true
     * $query->filterByEffectSelf('yes'); // WHERE effect_self = true
     * </code>
     *
     * @param     boolean|string $effectSelf The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function filterByEffectSelf($effectSelf = null, $comparison = null)
    {
        if (is_string($effectSelf)) {
            $effectSelf = in_array(strtolower($effectSelf), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EffectsPeer::EFFECT_SELF, $effectSelf, $comparison);
    }

    /**
     * Filter the query by a related ItemEffects object
     *
     * @param   ItemEffects|PropelObjectCollection $itemEffects  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 EffectsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByItemEffects($itemEffects, $comparison = null)
    {
        if ($itemEffects instanceof ItemEffects) {
            return $this
                ->addUsingAlias(EffectsPeer::EFFECT_ID, $itemEffects->getEffectId(), $comparison);
        } elseif ($itemEffects instanceof PropelObjectCollection) {
            return $this
                ->useItemEffectsQuery()
                ->filterByPrimaryKeys($itemEffects->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByItemEffects() only accepts arguments of type ItemEffects or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ItemEffects relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function joinItemEffects($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ItemEffects');

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
            $this->addJoinObject($join, 'ItemEffects');
        }

        return $this;
    }

    /**
     * Use the ItemEffects relation ItemEffects object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ItemEffectsQuery A secondary query class using the current class as primary query
     */
    public function useItemEffectsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinItemEffects($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ItemEffects', '\deploy\model\ItemEffectsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Effects $effects Object to remove from the list of results
     *
     * @return EffectsQuery The current query, for fluid interface
     */
    public function prune($effects = null)
    {
        if ($effects) {
            $this->addUsingAlias(EffectsPeer::EFFECT_ID, $effects->getEffectId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
