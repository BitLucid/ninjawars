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
use deploy\model\ClassSkill;
use deploy\model\Skill;
use deploy\model\SkillPeer;
use deploy\model\SkillQuery;

/**
 * Base class that represents a query for the 'skill' table.
 *
 *
 *
 * @method SkillQuery orderBySkillId($order = Criteria::ASC) Order by the skill_id column
 * @method SkillQuery orderBySkillLevel($order = Criteria::ASC) Order by the skill_level column
 * @method SkillQuery orderBySkillIsActive($order = Criteria::ASC) Order by the skill_is_active column
 * @method SkillQuery orderBySkillDisplayName($order = Criteria::ASC) Order by the skill_display_name column
 * @method SkillQuery orderBySkillInternalName($order = Criteria::ASC) Order by the skill_internal_name column
 * @method SkillQuery orderBySkillType($order = Criteria::ASC) Order by the skill_type column
 *
 * @method SkillQuery groupBySkillId() Group by the skill_id column
 * @method SkillQuery groupBySkillLevel() Group by the skill_level column
 * @method SkillQuery groupBySkillIsActive() Group by the skill_is_active column
 * @method SkillQuery groupBySkillDisplayName() Group by the skill_display_name column
 * @method SkillQuery groupBySkillInternalName() Group by the skill_internal_name column
 * @method SkillQuery groupBySkillType() Group by the skill_type column
 *
 * @method SkillQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SkillQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SkillQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method SkillQuery leftJoinClassSkill($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClassSkill relation
 * @method SkillQuery rightJoinClassSkill($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClassSkill relation
 * @method SkillQuery innerJoinClassSkill($relationAlias = null) Adds a INNER JOIN clause to the query using the ClassSkill relation
 *
 * @method Skill findOne(PropelPDO $con = null) Return the first Skill matching the query
 * @method Skill findOneOrCreate(PropelPDO $con = null) Return the first Skill matching the query, or a new Skill object populated from the query conditions when no match is found
 *
 * @method Skill findOneBySkillLevel(int $skill_level) Return the first Skill filtered by the skill_level column
 * @method Skill findOneBySkillIsActive(boolean $skill_is_active) Return the first Skill filtered by the skill_is_active column
 * @method Skill findOneBySkillDisplayName(string $skill_display_name) Return the first Skill filtered by the skill_display_name column
 * @method Skill findOneBySkillInternalName(string $skill_internal_name) Return the first Skill filtered by the skill_internal_name column
 * @method Skill findOneBySkillType(string $skill_type) Return the first Skill filtered by the skill_type column
 *
 * @method array findBySkillId(int $skill_id) Return Skill objects filtered by the skill_id column
 * @method array findBySkillLevel(int $skill_level) Return Skill objects filtered by the skill_level column
 * @method array findBySkillIsActive(boolean $skill_is_active) Return Skill objects filtered by the skill_is_active column
 * @method array findBySkillDisplayName(string $skill_display_name) Return Skill objects filtered by the skill_display_name column
 * @method array findBySkillInternalName(string $skill_internal_name) Return Skill objects filtered by the skill_internal_name column
 * @method array findBySkillType(string $skill_type) Return Skill objects filtered by the skill_type column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseSkillQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSkillQuery object.
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
            $modelName = 'deploy\\model\\Skill';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SkillQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SkillQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SkillQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SkillQuery) {
            return $criteria;
        }
        $query = new SkillQuery(null, null, $modelAlias);

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
     * @return   Skill|Skill[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SkillPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SkillPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Skill A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneBySkillId($key, $con = null)
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
     * @return                 Skill A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "skill_id", "skill_level", "skill_is_active", "skill_display_name", "skill_internal_name", "skill_type" FROM "skill" WHERE "skill_id" = :p0';
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
            $obj = new Skill();
            $obj->hydrate($row);
            SkillPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Skill|Skill[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Skill[]|mixed the list of results, formatted by the current formatter
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
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SkillPeer::SKILL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SkillPeer::SKILL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the skill_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillId(1234); // WHERE skill_id = 1234
     * $query->filterBySkillId(array(12, 34)); // WHERE skill_id IN (12, 34)
     * $query->filterBySkillId(array('min' => 12)); // WHERE skill_id >= 12
     * $query->filterBySkillId(array('max' => 12)); // WHERE skill_id <= 12
     * </code>
     *
     * @param     mixed $skillId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillId($skillId = null, $comparison = null)
    {
        if (is_array($skillId)) {
            $useMinMax = false;
            if (isset($skillId['min'])) {
                $this->addUsingAlias(SkillPeer::SKILL_ID, $skillId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($skillId['max'])) {
                $this->addUsingAlias(SkillPeer::SKILL_ID, $skillId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SkillPeer::SKILL_ID, $skillId, $comparison);
    }

    /**
     * Filter the query on the skill_level column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillLevel(1234); // WHERE skill_level = 1234
     * $query->filterBySkillLevel(array(12, 34)); // WHERE skill_level IN (12, 34)
     * $query->filterBySkillLevel(array('min' => 12)); // WHERE skill_level >= 12
     * $query->filterBySkillLevel(array('max' => 12)); // WHERE skill_level <= 12
     * </code>
     *
     * @param     mixed $skillLevel The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillLevel($skillLevel = null, $comparison = null)
    {
        if (is_array($skillLevel)) {
            $useMinMax = false;
            if (isset($skillLevel['min'])) {
                $this->addUsingAlias(SkillPeer::SKILL_LEVEL, $skillLevel['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($skillLevel['max'])) {
                $this->addUsingAlias(SkillPeer::SKILL_LEVEL, $skillLevel['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SkillPeer::SKILL_LEVEL, $skillLevel, $comparison);
    }

    /**
     * Filter the query on the skill_is_active column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillIsActive(true); // WHERE skill_is_active = true
     * $query->filterBySkillIsActive('yes'); // WHERE skill_is_active = true
     * </code>
     *
     * @param     boolean|string $skillIsActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillIsActive($skillIsActive = null, $comparison = null)
    {
        if (is_string($skillIsActive)) {
            $skillIsActive = in_array(strtolower($skillIsActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SkillPeer::SKILL_IS_ACTIVE, $skillIsActive, $comparison);
    }

    /**
     * Filter the query on the skill_display_name column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillDisplayName('fooValue');   // WHERE skill_display_name = 'fooValue'
     * $query->filterBySkillDisplayName('%fooValue%'); // WHERE skill_display_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $skillDisplayName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillDisplayName($skillDisplayName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($skillDisplayName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $skillDisplayName)) {
                $skillDisplayName = str_replace('*', '%', $skillDisplayName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SkillPeer::SKILL_DISPLAY_NAME, $skillDisplayName, $comparison);
    }

    /**
     * Filter the query on the skill_internal_name column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillInternalName('fooValue');   // WHERE skill_internal_name = 'fooValue'
     * $query->filterBySkillInternalName('%fooValue%'); // WHERE skill_internal_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $skillInternalName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillInternalName($skillInternalName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($skillInternalName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $skillInternalName)) {
                $skillInternalName = str_replace('*', '%', $skillInternalName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SkillPeer::SKILL_INTERNAL_NAME, $skillInternalName, $comparison);
    }

    /**
     * Filter the query on the skill_type column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillType('fooValue');   // WHERE skill_type = 'fooValue'
     * $query->filterBySkillType('%fooValue%'); // WHERE skill_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $skillType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function filterBySkillType($skillType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($skillType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $skillType)) {
                $skillType = str_replace('*', '%', $skillType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SkillPeer::SKILL_TYPE, $skillType, $comparison);
    }

    /**
     * Filter the query by a related ClassSkill object
     *
     * @param   ClassSkill|PropelObjectCollection $classSkill  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 SkillQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClassSkill($classSkill, $comparison = null)
    {
        if ($classSkill instanceof ClassSkill) {
            return $this
                ->addUsingAlias(SkillPeer::SKILL_ID, $classSkill->getSkillId(), $comparison);
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
     * @return SkillQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   Skill $skill Object to remove from the list of results
     *
     * @return SkillQuery The current query, for fluid interface
     */
    public function prune($skill = null)
    {
        if ($skill) {
            $this->addUsingAlias(SkillPeer::SKILL_ID, $skill->getSkillId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
