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
use deploy\model\Item;
use deploy\model\ItemEffects;
use deploy\model\ItemPeer;
use deploy\model\ItemQuery;

/**
 * Base class that represents a query for the 'item' table.
 *
 *
 *
 * @method ItemQuery orderByItemId($order = Criteria::ASC) Order by the item_id column
 * @method ItemQuery orderByItemInternalName($order = Criteria::ASC) Order by the item_internal_name column
 * @method ItemQuery orderByItemDisplayName($order = Criteria::ASC) Order by the item_display_name column
 * @method ItemQuery orderByItemCost($order = Criteria::ASC) Order by the item_cost column
 * @method ItemQuery orderByImage($order = Criteria::ASC) Order by the image column
 * @method ItemQuery orderByForSale($order = Criteria::ASC) Order by the for_sale column
 * @method ItemQuery orderByUsage($order = Criteria::ASC) Order by the usage column
 * @method ItemQuery orderByIgnoreStealth($order = Criteria::ASC) Order by the ignore_stealth column
 * @method ItemQuery orderByCovert($order = Criteria::ASC) Order by the covert column
 * @method ItemQuery orderByTurnCost($order = Criteria::ASC) Order by the turn_cost column
 * @method ItemQuery orderByTargetDamage($order = Criteria::ASC) Order by the target_damage column
 * @method ItemQuery orderByTurnChange($order = Criteria::ASC) Order by the turn_change column
 * @method ItemQuery orderBySelfUse($order = Criteria::ASC) Order by the self_use column
 * @method ItemQuery orderByPlural($order = Criteria::ASC) Order by the plural column
 * @method ItemQuery orderByOtherUsable($order = Criteria::ASC) Order by the other_usable column
 * @method ItemQuery orderByTraits($order = Criteria::ASC) Order by the traits column
 *
 * @method ItemQuery groupByItemId() Group by the item_id column
 * @method ItemQuery groupByItemInternalName() Group by the item_internal_name column
 * @method ItemQuery groupByItemDisplayName() Group by the item_display_name column
 * @method ItemQuery groupByItemCost() Group by the item_cost column
 * @method ItemQuery groupByImage() Group by the image column
 * @method ItemQuery groupByForSale() Group by the for_sale column
 * @method ItemQuery groupByUsage() Group by the usage column
 * @method ItemQuery groupByIgnoreStealth() Group by the ignore_stealth column
 * @method ItemQuery groupByCovert() Group by the covert column
 * @method ItemQuery groupByTurnCost() Group by the turn_cost column
 * @method ItemQuery groupByTargetDamage() Group by the target_damage column
 * @method ItemQuery groupByTurnChange() Group by the turn_change column
 * @method ItemQuery groupBySelfUse() Group by the self_use column
 * @method ItemQuery groupByPlural() Group by the plural column
 * @method ItemQuery groupByOtherUsable() Group by the other_usable column
 * @method ItemQuery groupByTraits() Group by the traits column
 *
 * @method ItemQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ItemQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ItemQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ItemQuery leftJoinItemEffects($relationAlias = null) Adds a LEFT JOIN clause to the query using the ItemEffects relation
 * @method ItemQuery rightJoinItemEffects($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ItemEffects relation
 * @method ItemQuery innerJoinItemEffects($relationAlias = null) Adds a INNER JOIN clause to the query using the ItemEffects relation
 *
 * @method Item findOne(PropelPDO $con = null) Return the first Item matching the query
 * @method Item findOneOrCreate(PropelPDO $con = null) Return the first Item matching the query, or a new Item object populated from the query conditions when no match is found
 *
 * @method Item findOneByItemInternalName(string $item_internal_name) Return the first Item filtered by the item_internal_name column
 * @method Item findOneByItemDisplayName(string $item_display_name) Return the first Item filtered by the item_display_name column
 * @method Item findOneByItemCost(string $item_cost) Return the first Item filtered by the item_cost column
 * @method Item findOneByImage(string $image) Return the first Item filtered by the image column
 * @method Item findOneByForSale(boolean $for_sale) Return the first Item filtered by the for_sale column
 * @method Item findOneByUsage(string $usage) Return the first Item filtered by the usage column
 * @method Item findOneByIgnoreStealth(boolean $ignore_stealth) Return the first Item filtered by the ignore_stealth column
 * @method Item findOneByCovert(boolean $covert) Return the first Item filtered by the covert column
 * @method Item findOneByTurnCost(int $turn_cost) Return the first Item filtered by the turn_cost column
 * @method Item findOneByTargetDamage(int $target_damage) Return the first Item filtered by the target_damage column
 * @method Item findOneByTurnChange(int $turn_change) Return the first Item filtered by the turn_change column
 * @method Item findOneBySelfUse(boolean $self_use) Return the first Item filtered by the self_use column
 * @method Item findOneByPlural(string $plural) Return the first Item filtered by the plural column
 * @method Item findOneByOtherUsable(boolean $other_usable) Return the first Item filtered by the other_usable column
 * @method Item findOneByTraits(string $traits) Return the first Item filtered by the traits column
 *
 * @method array findByItemId(int $item_id) Return Item objects filtered by the item_id column
 * @method array findByItemInternalName(string $item_internal_name) Return Item objects filtered by the item_internal_name column
 * @method array findByItemDisplayName(string $item_display_name) Return Item objects filtered by the item_display_name column
 * @method array findByItemCost(string $item_cost) Return Item objects filtered by the item_cost column
 * @method array findByImage(string $image) Return Item objects filtered by the image column
 * @method array findByForSale(boolean $for_sale) Return Item objects filtered by the for_sale column
 * @method array findByUsage(string $usage) Return Item objects filtered by the usage column
 * @method array findByIgnoreStealth(boolean $ignore_stealth) Return Item objects filtered by the ignore_stealth column
 * @method array findByCovert(boolean $covert) Return Item objects filtered by the covert column
 * @method array findByTurnCost(int $turn_cost) Return Item objects filtered by the turn_cost column
 * @method array findByTargetDamage(int $target_damage) Return Item objects filtered by the target_damage column
 * @method array findByTurnChange(int $turn_change) Return Item objects filtered by the turn_change column
 * @method array findBySelfUse(boolean $self_use) Return Item objects filtered by the self_use column
 * @method array findByPlural(string $plural) Return Item objects filtered by the plural column
 * @method array findByOtherUsable(boolean $other_usable) Return Item objects filtered by the other_usable column
 * @method array findByTraits(string $traits) Return Item objects filtered by the traits column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseItemQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseItemQuery object.
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
            $modelName = 'deploy\\model\\Item';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ItemQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ItemQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ItemQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ItemQuery) {
            return $criteria;
        }
        $query = new ItemQuery(null, null, $modelAlias);

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
     * @return   Item|Item[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ItemPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Item A model object, or null if the key is not found
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
     * @return                 Item A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "item_id", "item_internal_name", "item_display_name", "item_cost", "image", "for_sale", "usage", "ignore_stealth", "covert", "turn_cost", "target_damage", "turn_change", "self_use", "plural", "other_usable", "traits" FROM "item" WHERE "item_id" = :p0';
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
            $obj = new Item();
            $obj->hydrate($row);
            ItemPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Item|Item[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Item[]|mixed the list of results, formatted by the current formatter
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
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ItemPeer::ITEM_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ItemPeer::ITEM_ID, $keys, Criteria::IN);
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
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId)) {
            $useMinMax = false;
            if (isset($itemId['min'])) {
                $this->addUsingAlias(ItemPeer::ITEM_ID, $itemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemId['max'])) {
                $this->addUsingAlias(ItemPeer::ITEM_ID, $itemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_ID, $itemId, $comparison);
    }

    /**
     * Filter the query on the item_internal_name column
     *
     * Example usage:
     * <code>
     * $query->filterByItemInternalName('fooValue');   // WHERE item_internal_name = 'fooValue'
     * $query->filterByItemInternalName('%fooValue%'); // WHERE item_internal_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $itemInternalName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemInternalName($itemInternalName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($itemInternalName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $itemInternalName)) {
                $itemInternalName = str_replace('*', '%', $itemInternalName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_INTERNAL_NAME, $itemInternalName, $comparison);
    }

    /**
     * Filter the query on the item_display_name column
     *
     * Example usage:
     * <code>
     * $query->filterByItemDisplayName('fooValue');   // WHERE item_display_name = 'fooValue'
     * $query->filterByItemDisplayName('%fooValue%'); // WHERE item_display_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $itemDisplayName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemDisplayName($itemDisplayName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($itemDisplayName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $itemDisplayName)) {
                $itemDisplayName = str_replace('*', '%', $itemDisplayName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_DISPLAY_NAME, $itemDisplayName, $comparison);
    }

    /**
     * Filter the query on the item_cost column
     *
     * Example usage:
     * <code>
     * $query->filterByItemCost(1234); // WHERE item_cost = 1234
     * $query->filterByItemCost(array(12, 34)); // WHERE item_cost IN (12, 34)
     * $query->filterByItemCost(array('min' => 12)); // WHERE item_cost >= 12
     * $query->filterByItemCost(array('max' => 12)); // WHERE item_cost <= 12
     * </code>
     *
     * @param     mixed $itemCost The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemCost($itemCost = null, $comparison = null)
    {
        if (is_array($itemCost)) {
            $useMinMax = false;
            if (isset($itemCost['min'])) {
                $this->addUsingAlias(ItemPeer::ITEM_COST, $itemCost['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemCost['max'])) {
                $this->addUsingAlias(ItemPeer::ITEM_COST, $itemCost['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_COST, $itemCost, $comparison);
    }

    /**
     * Filter the query on the image column
     *
     * Example usage:
     * <code>
     * $query->filterByImage('fooValue');   // WHERE image = 'fooValue'
     * $query->filterByImage('%fooValue%'); // WHERE image LIKE '%fooValue%'
     * </code>
     *
     * @param     string $image The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByImage($image = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($image)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $image)) {
                $image = str_replace('*', '%', $image);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::IMAGE, $image, $comparison);
    }

    /**
     * Filter the query on the for_sale column
     *
     * Example usage:
     * <code>
     * $query->filterByForSale(true); // WHERE for_sale = true
     * $query->filterByForSale('yes'); // WHERE for_sale = true
     * </code>
     *
     * @param     boolean|string $forSale The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByForSale($forSale = null, $comparison = null)
    {
        if (is_string($forSale)) {
            $forSale = in_array(strtolower($forSale), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ItemPeer::FOR_SALE, $forSale, $comparison);
    }

    /**
     * Filter the query on the usage column
     *
     * Example usage:
     * <code>
     * $query->filterByUsage('fooValue');   // WHERE usage = 'fooValue'
     * $query->filterByUsage('%fooValue%'); // WHERE usage LIKE '%fooValue%'
     * </code>
     *
     * @param     string $usage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByUsage($usage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($usage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $usage)) {
                $usage = str_replace('*', '%', $usage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::USAGE, $usage, $comparison);
    }

    /**
     * Filter the query on the ignore_stealth column
     *
     * Example usage:
     * <code>
     * $query->filterByIgnoreStealth(true); // WHERE ignore_stealth = true
     * $query->filterByIgnoreStealth('yes'); // WHERE ignore_stealth = true
     * </code>
     *
     * @param     boolean|string $ignoreStealth The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByIgnoreStealth($ignoreStealth = null, $comparison = null)
    {
        if (is_string($ignoreStealth)) {
            $ignoreStealth = in_array(strtolower($ignoreStealth), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ItemPeer::IGNORE_STEALTH, $ignoreStealth, $comparison);
    }

    /**
     * Filter the query on the covert column
     *
     * Example usage:
     * <code>
     * $query->filterByCovert(true); // WHERE covert = true
     * $query->filterByCovert('yes'); // WHERE covert = true
     * </code>
     *
     * @param     boolean|string $covert The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByCovert($covert = null, $comparison = null)
    {
        if (is_string($covert)) {
            $covert = in_array(strtolower($covert), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ItemPeer::COVERT, $covert, $comparison);
    }

    /**
     * Filter the query on the turn_cost column
     *
     * Example usage:
     * <code>
     * $query->filterByTurnCost(1234); // WHERE turn_cost = 1234
     * $query->filterByTurnCost(array(12, 34)); // WHERE turn_cost IN (12, 34)
     * $query->filterByTurnCost(array('min' => 12)); // WHERE turn_cost >= 12
     * $query->filterByTurnCost(array('max' => 12)); // WHERE turn_cost <= 12
     * </code>
     *
     * @param     mixed $turnCost The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByTurnCost($turnCost = null, $comparison = null)
    {
        if (is_array($turnCost)) {
            $useMinMax = false;
            if (isset($turnCost['min'])) {
                $this->addUsingAlias(ItemPeer::TURN_COST, $turnCost['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($turnCost['max'])) {
                $this->addUsingAlias(ItemPeer::TURN_COST, $turnCost['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::TURN_COST, $turnCost, $comparison);
    }

    /**
     * Filter the query on the target_damage column
     *
     * Example usage:
     * <code>
     * $query->filterByTargetDamage(1234); // WHERE target_damage = 1234
     * $query->filterByTargetDamage(array(12, 34)); // WHERE target_damage IN (12, 34)
     * $query->filterByTargetDamage(array('min' => 12)); // WHERE target_damage >= 12
     * $query->filterByTargetDamage(array('max' => 12)); // WHERE target_damage <= 12
     * </code>
     *
     * @param     mixed $targetDamage The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByTargetDamage($targetDamage = null, $comparison = null)
    {
        if (is_array($targetDamage)) {
            $useMinMax = false;
            if (isset($targetDamage['min'])) {
                $this->addUsingAlias(ItemPeer::TARGET_DAMAGE, $targetDamage['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($targetDamage['max'])) {
                $this->addUsingAlias(ItemPeer::TARGET_DAMAGE, $targetDamage['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::TARGET_DAMAGE, $targetDamage, $comparison);
    }

    /**
     * Filter the query on the turn_change column
     *
     * Example usage:
     * <code>
     * $query->filterByTurnChange(1234); // WHERE turn_change = 1234
     * $query->filterByTurnChange(array(12, 34)); // WHERE turn_change IN (12, 34)
     * $query->filterByTurnChange(array('min' => 12)); // WHERE turn_change >= 12
     * $query->filterByTurnChange(array('max' => 12)); // WHERE turn_change <= 12
     * </code>
     *
     * @param     mixed $turnChange The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByTurnChange($turnChange = null, $comparison = null)
    {
        if (is_array($turnChange)) {
            $useMinMax = false;
            if (isset($turnChange['min'])) {
                $this->addUsingAlias(ItemPeer::TURN_CHANGE, $turnChange['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($turnChange['max'])) {
                $this->addUsingAlias(ItemPeer::TURN_CHANGE, $turnChange['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::TURN_CHANGE, $turnChange, $comparison);
    }

    /**
     * Filter the query on the self_use column
     *
     * Example usage:
     * <code>
     * $query->filterBySelfUse(true); // WHERE self_use = true
     * $query->filterBySelfUse('yes'); // WHERE self_use = true
     * </code>
     *
     * @param     boolean|string $selfUse The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterBySelfUse($selfUse = null, $comparison = null)
    {
        if (is_string($selfUse)) {
            $selfUse = in_array(strtolower($selfUse), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ItemPeer::SELF_USE, $selfUse, $comparison);
    }

    /**
     * Filter the query on the plural column
     *
     * Example usage:
     * <code>
     * $query->filterByPlural('fooValue');   // WHERE plural = 'fooValue'
     * $query->filterByPlural('%fooValue%'); // WHERE plural LIKE '%fooValue%'
     * </code>
     *
     * @param     string $plural The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByPlural($plural = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($plural)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $plural)) {
                $plural = str_replace('*', '%', $plural);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::PLURAL, $plural, $comparison);
    }

    /**
     * Filter the query on the other_usable column
     *
     * Example usage:
     * <code>
     * $query->filterByOtherUsable(true); // WHERE other_usable = true
     * $query->filterByOtherUsable('yes'); // WHERE other_usable = true
     * </code>
     *
     * @param     boolean|string $otherUsable The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByOtherUsable($otherUsable = null, $comparison = null)
    {
        if (is_string($otherUsable)) {
            $otherUsable = in_array(strtolower($otherUsable), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ItemPeer::OTHER_USABLE, $otherUsable, $comparison);
    }

    /**
     * Filter the query on the traits column
     *
     * Example usage:
     * <code>
     * $query->filterByTraits('fooValue');   // WHERE traits = 'fooValue'
     * $query->filterByTraits('%fooValue%'); // WHERE traits LIKE '%fooValue%'
     * </code>
     *
     * @param     string $traits The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByTraits($traits = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($traits)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $traits)) {
                $traits = str_replace('*', '%', $traits);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::TRAITS, $traits, $comparison);
    }

    /**
     * Filter the query by a related ItemEffects object
     *
     * @param   ItemEffects|PropelObjectCollection $itemEffects  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByItemEffects($itemEffects, $comparison = null)
    {
        if ($itemEffects instanceof ItemEffects) {
            return $this
                ->addUsingAlias(ItemPeer::ITEM_ID, $itemEffects->getItemId(), $comparison);
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
     * @return ItemQuery The current query, for fluid interface
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
     * @param   Item $item Object to remove from the list of results
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function prune($item = null)
    {
        if ($item) {
            $this->addUsingAlias(ItemPeer::ITEM_ID, $item->getItemId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
