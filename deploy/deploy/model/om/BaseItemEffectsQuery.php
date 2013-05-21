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
use deploy\model\Item;
use deploy\model\ItemEffects;
use deploy\model\ItemEffectsPeer;
use deploy\model\ItemEffectsQuery;

/**
 * Base class that represents a query for the 'item_effects' table.
 *
 *
 *
 * @method ItemEffectsQuery orderByItemId($order = Criteria::ASC) Order by the _item_id column
 * @method ItemEffectsQuery orderByEffectId($order = Criteria::ASC) Order by the _effect_id column
 *
 * @method ItemEffectsQuery groupByItemId() Group by the _item_id column
 * @method ItemEffectsQuery groupByEffectId() Group by the _effect_id column
 *
 * @method ItemEffectsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ItemEffectsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ItemEffectsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ItemEffectsQuery leftJoinEffects($relationAlias = null) Adds a LEFT JOIN clause to the query using the Effects relation
 * @method ItemEffectsQuery rightJoinEffects($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Effects relation
 * @method ItemEffectsQuery innerJoinEffects($relationAlias = null) Adds a INNER JOIN clause to the query using the Effects relation
 *
 * @method ItemEffectsQuery leftJoinItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the Item relation
 * @method ItemEffectsQuery rightJoinItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Item relation
 * @method ItemEffectsQuery innerJoinItem($relationAlias = null) Adds a INNER JOIN clause to the query using the Item relation
 *
 * @method ItemEffects findOne(PropelPDO $con = null) Return the first ItemEffects matching the query
 * @method ItemEffects findOneOrCreate(PropelPDO $con = null) Return the first ItemEffects matching the query, or a new ItemEffects object populated from the query conditions when no match is found
 *
 * @method ItemEffects findOneByItemId(int $_item_id) Return the first ItemEffects filtered by the _item_id column
 * @method ItemEffects findOneByEffectId(int $_effect_id) Return the first ItemEffects filtered by the _effect_id column
 *
 * @method array findByItemId(int $_item_id) Return ItemEffects objects filtered by the _item_id column
 * @method array findByEffectId(int $_effect_id) Return ItemEffects objects filtered by the _effect_id column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseItemEffectsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseItemEffectsQuery object.
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
            $modelName = 'deploy\\model\\ItemEffects';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ItemEffectsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ItemEffectsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ItemEffectsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ItemEffectsQuery) {
            return $criteria;
        }
        $query = new ItemEffectsQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$_item_id, $_effect_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ItemEffects|ItemEffects[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ItemEffectsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ItemEffectsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ItemEffects A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "_item_id", "_effect_id" FROM "item_effects" WHERE "_item_id" = :p0 AND "_effect_id" = :p1';
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
            $obj = new ItemEffects();
            $obj->hydrate($row);
            ItemEffectsPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ItemEffects|ItemEffects[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ItemEffects[]|mixed the list of results, formatted by the current formatter
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
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ItemEffectsPeer::_ITEM_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ItemEffectsPeer::_EFFECT_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the _item_id column
     *
     * Example usage:
     * <code>
     * $query->filterByItemId(1234); // WHERE _item_id = 1234
     * $query->filterByItemId(array(12, 34)); // WHERE _item_id IN (12, 34)
     * $query->filterByItemId(array('min' => 12)); // WHERE _item_id >= 12
     * $query->filterByItemId(array('max' => 12)); // WHERE _item_id <= 12
     * </code>
     *
     * @see       filterByItem()
     *
     * @param     mixed $itemId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId)) {
            $useMinMax = false;
            if (isset($itemId['min'])) {
                $this->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $itemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemId['max'])) {
                $this->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $itemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $itemId, $comparison);
    }

    /**
     * Filter the query on the _effect_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEffectId(1234); // WHERE _effect_id = 1234
     * $query->filterByEffectId(array(12, 34)); // WHERE _effect_id IN (12, 34)
     * $query->filterByEffectId(array('min' => 12)); // WHERE _effect_id >= 12
     * $query->filterByEffectId(array('max' => 12)); // WHERE _effect_id <= 12
     * </code>
     *
     * @see       filterByEffects()
     *
     * @param     mixed $effectId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function filterByEffectId($effectId = null, $comparison = null)
    {
        if (is_array($effectId)) {
            $useMinMax = false;
            if (isset($effectId['min'])) {
                $this->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $effectId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($effectId['max'])) {
                $this->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $effectId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $effectId, $comparison);
    }

    /**
     * Filter the query by a related Effects object
     *
     * @param   Effects|PropelObjectCollection $effects The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ItemEffectsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByEffects($effects, $comparison = null)
    {
        if ($effects instanceof Effects) {
            return $this
                ->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $effects->getEffectId(), $comparison);
        } elseif ($effects instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemEffectsPeer::_EFFECT_ID, $effects->toKeyValue('PrimaryKey', 'EffectId'), $comparison);
        } else {
            throw new PropelException('filterByEffects() only accepts arguments of type Effects or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Effects relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function joinEffects($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Effects');

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
            $this->addJoinObject($join, 'Effects');
        }

        return $this;
    }

    /**
     * Use the Effects relation Effects object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\EffectsQuery A secondary query class using the current class as primary query
     */
    public function useEffectsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEffects($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Effects', '\deploy\model\EffectsQuery');
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ItemEffectsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $item->getItemId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemEffectsPeer::_ITEM_ID, $item->toKeyValue('PrimaryKey', 'ItemId'), $comparison);
        } else {
            throw new PropelException('filterByItem() only accepts arguments of type Item or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Item relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function joinItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Item');

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
            $this->addJoinObject($join, 'Item');
        }

        return $this;
    }

    /**
     * Use the Item relation Item object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ItemQuery A secondary query class using the current class as primary query
     */
    public function useItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Item', '\deploy\model\ItemQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ItemEffects $itemEffects Object to remove from the list of results
     *
     * @return ItemEffectsQuery The current query, for fluid interface
     */
    public function prune($itemEffects = null)
    {
        if ($itemEffects) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ItemEffectsPeer::_ITEM_ID), $itemEffects->getItemId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ItemEffectsPeer::_EFFECT_ID), $itemEffects->getEffectId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
