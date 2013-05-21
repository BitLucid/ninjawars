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
use deploy\model\Settings;
use deploy\model\SettingsPeer;
use deploy\model\SettingsQuery;

/**
 * Base class that represents a query for the 'settings' table.
 *
 *
 *
 * @method SettingsQuery orderBySettingId($order = Criteria::ASC) Order by the setting_id column
 * @method SettingsQuery orderByPlayerId($order = Criteria::ASC) Order by the player_id column
 * @method SettingsQuery orderBySettingsStore($order = Criteria::ASC) Order by the settings_store column
 *
 * @method SettingsQuery groupBySettingId() Group by the setting_id column
 * @method SettingsQuery groupByPlayerId() Group by the player_id column
 * @method SettingsQuery groupBySettingsStore() Group by the settings_store column
 *
 * @method SettingsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SettingsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SettingsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Settings findOne(PropelPDO $con = null) Return the first Settings matching the query
 * @method Settings findOneOrCreate(PropelPDO $con = null) Return the first Settings matching the query, or a new Settings object populated from the query conditions when no match is found
 *
 * @method Settings findOneBySettingId(int $setting_id) Return the first Settings filtered by the setting_id column
 * @method Settings findOneByPlayerId(int $player_id) Return the first Settings filtered by the player_id column
 * @method Settings findOneBySettingsStore(string $settings_store) Return the first Settings filtered by the settings_store column
 *
 * @method array findBySettingId(int $setting_id) Return Settings objects filtered by the setting_id column
 * @method array findByPlayerId(int $player_id) Return Settings objects filtered by the player_id column
 * @method array findBySettingsStore(string $settings_store) Return Settings objects filtered by the settings_store column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseSettingsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSettingsQuery object.
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
            $modelName = 'deploy\\model\\Settings';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SettingsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SettingsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SettingsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SettingsQuery) {
            return $criteria;
        }
        $query = new SettingsQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$setting_id, $player_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Settings|Settings[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SettingsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Settings A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "setting_id", "player_id", "settings_store" FROM "settings" WHERE "setting_id" = :p0 AND "player_id" = :p1';
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
            $obj = new Settings();
            $obj->hydrate($row);
            SettingsPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return Settings|Settings[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Settings[]|mixed the list of results, formatted by the current formatter
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
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SettingsPeer::SETTING_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SettingsPeer::PLAYER_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SettingsPeer::SETTING_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SettingsPeer::PLAYER_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the setting_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySettingId(1234); // WHERE setting_id = 1234
     * $query->filterBySettingId(array(12, 34)); // WHERE setting_id IN (12, 34)
     * $query->filterBySettingId(array('min' => 12)); // WHERE setting_id >= 12
     * $query->filterBySettingId(array('max' => 12)); // WHERE setting_id <= 12
     * </code>
     *
     * @param     mixed $settingId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterBySettingId($settingId = null, $comparison = null)
    {
        if (is_array($settingId)) {
            $useMinMax = false;
            if (isset($settingId['min'])) {
                $this->addUsingAlias(SettingsPeer::SETTING_ID, $settingId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($settingId['max'])) {
                $this->addUsingAlias(SettingsPeer::SETTING_ID, $settingId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsPeer::SETTING_ID, $settingId, $comparison);
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
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(SettingsPeer::PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(SettingsPeer::PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsPeer::PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the settings_store column
     *
     * Example usage:
     * <code>
     * $query->filterBySettingsStore('fooValue');   // WHERE settings_store = 'fooValue'
     * $query->filterBySettingsStore('%fooValue%'); // WHERE settings_store LIKE '%fooValue%'
     * </code>
     *
     * @param     string $settingsStore The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterBySettingsStore($settingsStore = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($settingsStore)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $settingsStore)) {
                $settingsStore = str_replace('*', '%', $settingsStore);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsPeer::SETTINGS_STORE, $settingsStore, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Settings $settings Object to remove from the list of results
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function prune($settings = null)
    {
        if ($settings) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SettingsPeer::SETTING_ID), $settings->getSettingId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SettingsPeer::PLAYER_ID), $settings->getPlayerId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
