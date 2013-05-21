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
use deploy\model\Class;
use deploy\model\ClassPeer;
use deploy\model\ClassQuery;
use deploy\model\ClassSkill;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'class' table.
 *
 *
 *
 * @method ClassQuery orderByClassId($order = Criteria::ASC) Order by the class_id column
 * @method ClassQuery orderByClassName($order = Criteria::ASC) Order by the class_name column
 * @method ClassQuery orderByClassActive($order = Criteria::ASC) Order by the class_active column
 * @method ClassQuery orderByClassNote($order = Criteria::ASC) Order by the class_note column
 * @method ClassQuery orderByClassTier($order = Criteria::ASC) Order by the class_tier column
 * @method ClassQuery orderByClassDesc($order = Criteria::ASC) Order by the class_desc column
 * @method ClassQuery orderByClassIcon($order = Criteria::ASC) Order by the class_icon column
 * @method ClassQuery orderByTheme($order = Criteria::ASC) Order by the theme column
 * @method ClassQuery orderByIdentity($order = Criteria::ASC) Order by the identity column
 *
 * @method ClassQuery groupByClassId() Group by the class_id column
 * @method ClassQuery groupByClassName() Group by the class_name column
 * @method ClassQuery groupByClassActive() Group by the class_active column
 * @method ClassQuery groupByClassNote() Group by the class_note column
 * @method ClassQuery groupByClassTier() Group by the class_tier column
 * @method ClassQuery groupByClassDesc() Group by the class_desc column
 * @method ClassQuery groupByClassIcon() Group by the class_icon column
 * @method ClassQuery groupByTheme() Group by the theme column
 * @method ClassQuery groupByIdentity() Group by the identity column
 *
 * @method ClassQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ClassQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ClassQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ClassQuery leftJoinClassSkill($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClassSkill relation
 * @method ClassQuery rightJoinClassSkill($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClassSkill relation
 * @method ClassQuery innerJoinClassSkill($relationAlias = null) Adds a INNER JOIN clause to the query using the ClassSkill relation
 *
 * @method ClassQuery leftJoinPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Players relation
 * @method ClassQuery rightJoinPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Players relation
 * @method ClassQuery innerJoinPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the Players relation
 *
 * @method Class findOne(PropelPDO $con = null) Return the first Class matching the query
 * @method Class findOneOrCreate(PropelPDO $con = null) Return the first Class matching the query, or a new Class object populated from the query conditions when no match is found
 *
 * @method Class findOneByClassName(string $class_name) Return the first Class filtered by the class_name column
 * @method Class findOneByClassActive(boolean $class_active) Return the first Class filtered by the class_active column
 * @method Class findOneByClassNote(string $class_note) Return the first Class filtered by the class_note column
 * @method Class findOneByClassTier(int $class_tier) Return the first Class filtered by the class_tier column
 * @method Class findOneByClassDesc(string $class_desc) Return the first Class filtered by the class_desc column
 * @method Class findOneByClassIcon(string $class_icon) Return the first Class filtered by the class_icon column
 * @method Class findOneByTheme(string $theme) Return the first Class filtered by the theme column
 * @method Class findOneByIdentity(string $identity) Return the first Class filtered by the identity column
 *
 * @method array findByClassId(int $class_id) Return Class objects filtered by the class_id column
 * @method array findByClassName(string $class_name) Return Class objects filtered by the class_name column
 * @method array findByClassActive(boolean $class_active) Return Class objects filtered by the class_active column
 * @method array findByClassNote(string $class_note) Return Class objects filtered by the class_note column
 * @method array findByClassTier(int $class_tier) Return Class objects filtered by the class_tier column
 * @method array findByClassDesc(string $class_desc) Return Class objects filtered by the class_desc column
 * @method array findByClassIcon(string $class_icon) Return Class objects filtered by the class_icon column
 * @method array findByTheme(string $theme) Return Class objects filtered by the theme column
 * @method array findByIdentity(string $identity) Return Class objects filtered by the identity column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseClassQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseClassQuery object.
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
            $modelName = 'deploy\\model\\Class';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ClassQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ClassQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ClassQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ClassQuery) {
            return $criteria;
        }
        $query = new ClassQuery(null, null, $modelAlias);

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
     * @return   Class|Class[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClassPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ClassPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Class A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByClassId($key, $con = null)
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
     * @return                 Class A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "class_id", "class_name", "class_active", "class_note", "class_tier", "class_desc", "class_icon", "theme", "identity" FROM "class" WHERE "class_id" = :p0';
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
            $obj = new Class();
            $obj->hydrate($row);
            ClassPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Class|Class[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Class[]|mixed the list of results, formatted by the current formatter
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
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClassPeer::CLASS_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClassPeer::CLASS_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the class_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassId(1234); // WHERE class_id = 1234
     * $query->filterByClassId(array(12, 34)); // WHERE class_id IN (12, 34)
     * $query->filterByClassId(array('min' => 12)); // WHERE class_id >= 12
     * $query->filterByClassId(array('max' => 12)); // WHERE class_id <= 12
     * </code>
     *
     * @param     mixed $classId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassId($classId = null, $comparison = null)
    {
        if (is_array($classId)) {
            $useMinMax = false;
            if (isset($classId['min'])) {
                $this->addUsingAlias(ClassPeer::CLASS_ID, $classId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classId['max'])) {
                $this->addUsingAlias(ClassPeer::CLASS_ID, $classId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_ID, $classId, $comparison);
    }

    /**
     * Filter the query on the class_name column
     *
     * Example usage:
     * <code>
     * $query->filterByClassName('fooValue');   // WHERE class_name = 'fooValue'
     * $query->filterByClassName('%fooValue%'); // WHERE class_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $className The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassName($className = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($className)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $className)) {
                $className = str_replace('*', '%', $className);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_NAME, $className, $comparison);
    }

    /**
     * Filter the query on the class_active column
     *
     * Example usage:
     * <code>
     * $query->filterByClassActive(true); // WHERE class_active = true
     * $query->filterByClassActive('yes'); // WHERE class_active = true
     * </code>
     *
     * @param     boolean|string $classActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassActive($classActive = null, $comparison = null)
    {
        if (is_string($classActive)) {
            $classActive = in_array(strtolower($classActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ClassPeer::CLASS_ACTIVE, $classActive, $comparison);
    }

    /**
     * Filter the query on the class_note column
     *
     * Example usage:
     * <code>
     * $query->filterByClassNote('fooValue');   // WHERE class_note = 'fooValue'
     * $query->filterByClassNote('%fooValue%'); // WHERE class_note LIKE '%fooValue%'
     * </code>
     *
     * @param     string $classNote The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassNote($classNote = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($classNote)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $classNote)) {
                $classNote = str_replace('*', '%', $classNote);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_NOTE, $classNote, $comparison);
    }

    /**
     * Filter the query on the class_tier column
     *
     * Example usage:
     * <code>
     * $query->filterByClassTier(1234); // WHERE class_tier = 1234
     * $query->filterByClassTier(array(12, 34)); // WHERE class_tier IN (12, 34)
     * $query->filterByClassTier(array('min' => 12)); // WHERE class_tier >= 12
     * $query->filterByClassTier(array('max' => 12)); // WHERE class_tier <= 12
     * </code>
     *
     * @param     mixed $classTier The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassTier($classTier = null, $comparison = null)
    {
        if (is_array($classTier)) {
            $useMinMax = false;
            if (isset($classTier['min'])) {
                $this->addUsingAlias(ClassPeer::CLASS_TIER, $classTier['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classTier['max'])) {
                $this->addUsingAlias(ClassPeer::CLASS_TIER, $classTier['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_TIER, $classTier, $comparison);
    }

    /**
     * Filter the query on the class_desc column
     *
     * Example usage:
     * <code>
     * $query->filterByClassDesc('fooValue');   // WHERE class_desc = 'fooValue'
     * $query->filterByClassDesc('%fooValue%'); // WHERE class_desc LIKE '%fooValue%'
     * </code>
     *
     * @param     string $classDesc The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassDesc($classDesc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($classDesc)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $classDesc)) {
                $classDesc = str_replace('*', '%', $classDesc);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_DESC, $classDesc, $comparison);
    }

    /**
     * Filter the query on the class_icon column
     *
     * Example usage:
     * <code>
     * $query->filterByClassIcon('fooValue');   // WHERE class_icon = 'fooValue'
     * $query->filterByClassIcon('%fooValue%'); // WHERE class_icon LIKE '%fooValue%'
     * </code>
     *
     * @param     string $classIcon The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByClassIcon($classIcon = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($classIcon)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $classIcon)) {
                $classIcon = str_replace('*', '%', $classIcon);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::CLASS_ICON, $classIcon, $comparison);
    }

    /**
     * Filter the query on the theme column
     *
     * Example usage:
     * <code>
     * $query->filterByTheme('fooValue');   // WHERE theme = 'fooValue'
     * $query->filterByTheme('%fooValue%'); // WHERE theme LIKE '%fooValue%'
     * </code>
     *
     * @param     string $theme The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByTheme($theme = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($theme)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $theme)) {
                $theme = str_replace('*', '%', $theme);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::THEME, $theme, $comparison);
    }

    /**
     * Filter the query on the identity column
     *
     * Example usage:
     * <code>
     * $query->filterByIdentity('fooValue');   // WHERE identity = 'fooValue'
     * $query->filterByIdentity('%fooValue%'); // WHERE identity LIKE '%fooValue%'
     * </code>
     *
     * @param     string $identity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function filterByIdentity($identity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($identity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $identity)) {
                $identity = str_replace('*', '%', $identity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClassPeer::IDENTITY, $identity, $comparison);
    }

    /**
     * Filter the query by a related ClassSkill object
     *
     * @param   ClassSkill|PropelObjectCollection $classSkill  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClassQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClassSkill($classSkill, $comparison = null)
    {
        if ($classSkill instanceof ClassSkill) {
            return $this
                ->addUsingAlias(ClassPeer::CLASS_ID, $classSkill->getClassId(), $comparison);
        } elseif ($classSkill instanceof PropelObjectCollection) {
            return $this
                ->useClassSkillQuery()
                ->filterByPrimaryKeys($classSkill->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByClassSkill() only accepts arguments of type ClassSkill or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClassSkill relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function joinClassSkill($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClassSkill');

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
            $this->addJoinObject($join, 'ClassSkill');
        }

        return $this;
    }

    /**
     * Use the ClassSkill relation ClassSkill object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClassSkillQuery A secondary query class using the current class as primary query
     */
    public function useClassSkillQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClassSkill($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClassSkill', '\deploy\model\ClassSkillQuery');
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClassQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayers($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(ClassPeer::CLASS_ID, $players->getClassId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            return $this
                ->usePlayersQuery()
                ->filterByPrimaryKeys($players->getPrimaryKeys())
                ->endUse();
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
     * @return ClassQuery The current query, for fluid interface
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
     * @param   Class $class Object to remove from the list of results
     *
     * @return ClassQuery The current query, for fluid interface
     */
    public function prune($class = null)
    {
        if ($class) {
            $this->addUsingAlias(ClassPeer::CLASS_ID, $class->getClassId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
