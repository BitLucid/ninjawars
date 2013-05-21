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
use deploy\model\ClassSkill;
use deploy\model\ClassSkillPeer;
use deploy\model\ClassSkillQuery;
use deploy\model\Skill;

/**
 * Base class that represents a query for the 'class_skill' table.
 *
 *
 *
 * @method ClassSkillQuery orderByClassId($order = Criteria::ASC) Order by the _class_id column
 * @method ClassSkillQuery orderBySkillId($order = Criteria::ASC) Order by the _skill_id column
 * @method ClassSkillQuery orderByClassSkillLevel($order = Criteria::ASC) Order by the class_skill_level column
 *
 * @method ClassSkillQuery groupByClassId() Group by the _class_id column
 * @method ClassSkillQuery groupBySkillId() Group by the _skill_id column
 * @method ClassSkillQuery groupByClassSkillLevel() Group by the class_skill_level column
 *
 * @method ClassSkillQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ClassSkillQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ClassSkillQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ClassSkillQuery leftJoinClass($relationAlias = null) Adds a LEFT JOIN clause to the query using the Class relation
 * @method ClassSkillQuery rightJoinClass($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Class relation
 * @method ClassSkillQuery innerJoinClass($relationAlias = null) Adds a INNER JOIN clause to the query using the Class relation
 *
 * @method ClassSkillQuery leftJoinSkill($relationAlias = null) Adds a LEFT JOIN clause to the query using the Skill relation
 * @method ClassSkillQuery rightJoinSkill($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Skill relation
 * @method ClassSkillQuery innerJoinSkill($relationAlias = null) Adds a INNER JOIN clause to the query using the Skill relation
 *
 * @method ClassSkill findOne(PropelPDO $con = null) Return the first ClassSkill matching the query
 * @method ClassSkill findOneOrCreate(PropelPDO $con = null) Return the first ClassSkill matching the query, or a new ClassSkill object populated from the query conditions when no match is found
 *
 * @method ClassSkill findOneByClassId(int $_class_id) Return the first ClassSkill filtered by the _class_id column
 * @method ClassSkill findOneBySkillId(int $_skill_id) Return the first ClassSkill filtered by the _skill_id column
 * @method ClassSkill findOneByClassSkillLevel(int $class_skill_level) Return the first ClassSkill filtered by the class_skill_level column
 *
 * @method array findByClassId(int $_class_id) Return ClassSkill objects filtered by the _class_id column
 * @method array findBySkillId(int $_skill_id) Return ClassSkill objects filtered by the _skill_id column
 * @method array findByClassSkillLevel(int $class_skill_level) Return ClassSkill objects filtered by the class_skill_level column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseClassSkillQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseClassSkillQuery object.
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
            $modelName = 'deploy\\model\\ClassSkill';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ClassSkillQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ClassSkillQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ClassSkillQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ClassSkillQuery) {
            return $criteria;
        }
        $query = new ClassSkillQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$_class_id, $_skill_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ClassSkill|ClassSkill[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClassSkillPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ClassSkillPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ClassSkill A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "_class_id", "_skill_id", "class_skill_level" FROM "class_skill" WHERE "_class_id" = :p0 AND "_skill_id" = :p1';
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
            $obj = new ClassSkill();
            $obj->hydrate($row);
            ClassSkillPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ClassSkill|ClassSkill[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ClassSkill[]|mixed the list of results, formatted by the current formatter
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
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ClassSkillPeer::_CLASS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ClassSkillPeer::_SKILL_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ClassSkillPeer::_CLASS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ClassSkillPeer::_SKILL_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the _class_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassId(1234); // WHERE _class_id = 1234
     * $query->filterByClassId(array(12, 34)); // WHERE _class_id IN (12, 34)
     * $query->filterByClassId(array('min' => 12)); // WHERE _class_id >= 12
     * $query->filterByClassId(array('max' => 12)); // WHERE _class_id <= 12
     * </code>
     *
     * @see       filterByClass()
     *
     * @param     mixed $classId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function filterByClassId($classId = null, $comparison = null)
    {
        if (is_array($classId)) {
            $useMinMax = false;
            if (isset($classId['min'])) {
                $this->addUsingAlias(ClassSkillPeer::_CLASS_ID, $classId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classId['max'])) {
                $this->addUsingAlias(ClassSkillPeer::_CLASS_ID, $classId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassSkillPeer::_CLASS_ID, $classId, $comparison);
    }

    /**
     * Filter the query on the _skill_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySkillId(1234); // WHERE _skill_id = 1234
     * $query->filterBySkillId(array(12, 34)); // WHERE _skill_id IN (12, 34)
     * $query->filterBySkillId(array('min' => 12)); // WHERE _skill_id >= 12
     * $query->filterBySkillId(array('max' => 12)); // WHERE _skill_id <= 12
     * </code>
     *
     * @see       filterBySkill()
     *
     * @param     mixed $skillId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function filterBySkillId($skillId = null, $comparison = null)
    {
        if (is_array($skillId)) {
            $useMinMax = false;
            if (isset($skillId['min'])) {
                $this->addUsingAlias(ClassSkillPeer::_SKILL_ID, $skillId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($skillId['max'])) {
                $this->addUsingAlias(ClassSkillPeer::_SKILL_ID, $skillId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassSkillPeer::_SKILL_ID, $skillId, $comparison);
    }

    /**
     * Filter the query on the class_skill_level column
     *
     * Example usage:
     * <code>
     * $query->filterByClassSkillLevel(1234); // WHERE class_skill_level = 1234
     * $query->filterByClassSkillLevel(array(12, 34)); // WHERE class_skill_level IN (12, 34)
     * $query->filterByClassSkillLevel(array('min' => 12)); // WHERE class_skill_level >= 12
     * $query->filterByClassSkillLevel(array('max' => 12)); // WHERE class_skill_level <= 12
     * </code>
     *
     * @param     mixed $classSkillLevel The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function filterByClassSkillLevel($classSkillLevel = null, $comparison = null)
    {
        if (is_array($classSkillLevel)) {
            $useMinMax = false;
            if (isset($classSkillLevel['min'])) {
                $this->addUsingAlias(ClassSkillPeer::CLASS_SKILL_LEVEL, $classSkillLevel['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classSkillLevel['max'])) {
                $this->addUsingAlias(ClassSkillPeer::CLASS_SKILL_LEVEL, $classSkillLevel['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassSkillPeer::CLASS_SKILL_LEVEL, $classSkillLevel, $comparison);
    }

    /**
     * Filter the query by a related Class object
     *
     * @param   Class|PropelObjectCollection $class The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClassSkillQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClass($class, $comparison = null)
    {
        if ($class instanceof Class) {
            return $this
                ->addUsingAlias(ClassSkillPeer::_CLASS_ID, $class->getClassId(), $comparison);
        } elseif ($class instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClassSkillPeer::_CLASS_ID, $class->toKeyValue('PrimaryKey', 'ClassId'), $comparison);
        } else {
            throw new PropelException('filterByClass() only accepts arguments of type Class or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Class relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function joinClass($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Class');

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
            $this->addJoinObject($join, 'Class');
        }

        return $this;
    }

    /**
     * Use the Class relation Class object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClassQuery A secondary query class using the current class as primary query
     */
    public function useClassQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClass($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Class', '\deploy\model\ClassQuery');
    }

    /**
     * Filter the query by a related Skill object
     *
     * @param   Skill|PropelObjectCollection $skill The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClassSkillQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySkill($skill, $comparison = null)
    {
        if ($skill instanceof Skill) {
            return $this
                ->addUsingAlias(ClassSkillPeer::_SKILL_ID, $skill->getSkillId(), $comparison);
        } elseif ($skill instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClassSkillPeer::_SKILL_ID, $skill->toKeyValue('PrimaryKey', 'SkillId'), $comparison);
        } else {
            throw new PropelException('filterBySkill() only accepts arguments of type Skill or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Skill relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function joinSkill($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Skill');

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
            $this->addJoinObject($join, 'Skill');
        }

        return $this;
    }

    /**
     * Use the Skill relation Skill object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\SkillQuery A secondary query class using the current class as primary query
     */
    public function useSkillQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSkill($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Skill', '\deploy\model\SkillQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ClassSkill $classSkill Object to remove from the list of results
     *
     * @return ClassSkillQuery The current query, for fluid interface
     */
    public function prune($classSkill = null)
    {
        if ($classSkill) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ClassSkillPeer::_CLASS_ID), $classSkill->getClassId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ClassSkillPeer::_SKILL_ID), $classSkill->getSkillId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
