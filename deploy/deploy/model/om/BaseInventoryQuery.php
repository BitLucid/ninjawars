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
use deploy\model\Inventory;
use deploy\model\InventoryPeer;
use deploy\model\InventoryQuery;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'inventory' table.
 *
 *
 *
 * @method InventoryQuery orderByItemId($order = Criteria::ASC) Order by the item_id column
 * @method InventoryQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method InventoryQuery orderByOwner($order = Criteria::ASC) Order by the owner column
 * @method InventoryQuery orderByItemType($order = Criteria::ASC) Order by the item_type column
 * @method InventoryQuery orderByItemTypeStringBackup($order = Criteria::ASC) Order by the item_type_string_backup column
 *
 * @method InventoryQuery groupByItemId() Group by the item_id column
 * @method InventoryQuery groupByAmount() Group by the amount column
 * @method InventoryQuery groupByOwner() Group by the owner column
 * @method InventoryQuery groupByItemType() Group by the item_type column
 * @method InventoryQuery groupByItemTypeStringBackup() Group by the item_type_string_backup column
 *
 * @method InventoryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method InventoryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method InventoryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method InventoryQuery leftJoinPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Players relation
 * @method InventoryQuery rightJoinPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Players relation
 * @method InventoryQuery innerJoinPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the Players relation
 *
 * @method Inventory findOne(PropelPDO $con = null) Return the first Inventory matching the query
 * @method Inventory findOneOrCreate(PropelPDO $con = null) Return the first Inventory matching the query, or a new Inventory object populated from the query conditions when no match is found
 *
 * @method Inventory findOneByAmount(int $amount) Return the first Inventory filtered by the amount column
 * @method Inventory findOneByOwner(int $owner) Return the first Inventory filtered by the owner column
 * @method Inventory findOneByItemType(int $item_type) Return the first Inventory filtered by the item_type column
 * @method Inventory findOneByItemTypeStringBackup(string $item_type_string_backup) Return the first Inventory filtered by the item_type_string_backup column
 *
 * @method array findByItemId(int $item_id) Return Inventory objects filtered by the item_id column
 * @method array findByAmount(int $amount) Return Inventory objects filtered by the amount column
 * @method array findByOwner(int $owner) Return Inventory objects filtered by the owner column
 * @method array findByItemType(int $item_type) Return Inventory objects filtered by the item_type column
 * @method array findByItemTypeStringBackup(string $item_type_string_backup) Return Inventory objects filtered by the item_type_string_backup column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseInventoryQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseInventoryQuery object.
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
            $modelName = 'deploy\\model\\Inventory';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new InventoryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   InventoryQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return InventoryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof InventoryQuery) {
            return $criteria;
        }
        $query = new InventoryQuery(null, null, $modelAlias);

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
     * @return   Inventory|Inventory[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = InventoryPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(InventoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Inventory A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByItemId($key, $con = null)
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
     * @return                 Inventory A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "item_id", "amount", "owner", "item_type", "item_type_string_backup" FROM "inventory" WHERE "item_id" = :p0';
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
            $obj = new Inventory();
            $obj->hydrate($row);
            InventoryPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Inventory|Inventory[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Inventory[]|mixed the list of results, formatted by the current formatter
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
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(InventoryPeer::ITEM_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(InventoryPeer::ITEM_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the item_id column
     *
     * Example usage:
     * <code>
     * $query->filterByItemId(1234); // WHERE item_id = 1234
     * $query->filterByItemId(array(12, 34)); // WHERE item_id IN (12, 34)
     * $query->filterByItemId(array('min' => 12)); // WHERE item_id >= 12
     * $query->filterByItemId(array('max' => 12)); // WHERE item_id <= 12
     * </code>
     *
     * @param     mixed $itemId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId)) {
            $useMinMax = false;
            if (isset($itemId['min'])) {
                $this->addUsingAlias(InventoryPeer::ITEM_ID, $itemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemId['max'])) {
                $this->addUsingAlias(InventoryPeer::ITEM_ID, $itemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InventoryPeer::ITEM_ID, $itemId, $comparison);
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
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(InventoryPeer::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(InventoryPeer::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InventoryPeer::AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query on the owner column
     *
     * Example usage:
     * <code>
     * $query->filterByOwner(1234); // WHERE owner = 1234
     * $query->filterByOwner(array(12, 34)); // WHERE owner IN (12, 34)
     * $query->filterByOwner(array('min' => 12)); // WHERE owner >= 12
     * $query->filterByOwner(array('max' => 12)); // WHERE owner <= 12
     * </code>
     *
     * @see       filterByPlayers()
     *
     * @param     mixed $owner The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByOwner($owner = null, $comparison = null)
    {
        if (is_array($owner)) {
            $useMinMax = false;
            if (isset($owner['min'])) {
                $this->addUsingAlias(InventoryPeer::OWNER, $owner['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($owner['max'])) {
                $this->addUsingAlias(InventoryPeer::OWNER, $owner['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InventoryPeer::OWNER, $owner, $comparison);
    }

    /**
     * Filter the query on the item_type column
     *
     * Example usage:
     * <code>
     * $query->filterByItemType(1234); // WHERE item_type = 1234
     * $query->filterByItemType(array(12, 34)); // WHERE item_type IN (12, 34)
     * $query->filterByItemType(array('min' => 12)); // WHERE item_type >= 12
     * $query->filterByItemType(array('max' => 12)); // WHERE item_type <= 12
     * </code>
     *
     * @param     mixed $itemType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByItemType($itemType = null, $comparison = null)
    {
        if (is_array($itemType)) {
            $useMinMax = false;
            if (isset($itemType['min'])) {
                $this->addUsingAlias(InventoryPeer::ITEM_TYPE, $itemType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemType['max'])) {
                $this->addUsingAlias(InventoryPeer::ITEM_TYPE, $itemType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InventoryPeer::ITEM_TYPE, $itemType, $comparison);
    }

    /**
     * Filter the query on the item_type_string_backup column
     *
     * Example usage:
     * <code>
     * $query->filterByItemTypeStringBackup('fooValue');   // WHERE item_type_string_backup = 'fooValue'
     * $query->filterByItemTypeStringBackup('%fooValue%'); // WHERE item_type_string_backup LIKE '%fooValue%'
     * </code>
     *
     * @param     string $itemTypeStringBackup The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function filterByItemTypeStringBackup($itemTypeStringBackup = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($itemTypeStringBackup)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $itemTypeStringBackup)) {
                $itemTypeStringBackup = str_replace('*', '%', $itemTypeStringBackup);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(InventoryPeer::ITEM_TYPE_STRING_BACKUP, $itemTypeStringBackup, $comparison);
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 InventoryQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayers($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(InventoryPeer::OWNER, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(InventoryPeer::OWNER, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayers() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Players relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function joinPlayers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Players');

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
            $this->addJoinObject($join, 'Players');
        }

        return $this;
    }

    /**
     * Use the Players relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Players', '\deploy\model\PlayersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Inventory $inventory Object to remove from the list of results
     *
     * @return InventoryQuery The current query, for fluid interface
     */
    public function prune($inventory = null)
    {
        if ($inventory) {
            $this->addUsingAlias(InventoryPeer::ITEM_ID, $inventory->getItemId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
