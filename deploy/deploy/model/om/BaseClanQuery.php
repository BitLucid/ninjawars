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
use deploy\model\Clan;
use deploy\model\ClanPeer;
use deploy\model\ClanPlayer;
use deploy\model\ClanQuery;

/**
 * Base class that represents a query for the 'clan' table.
 *
 *
 *
 * @method ClanQuery orderByClanId($order = Criteria::ASC) Order by the clan_id column
 * @method ClanQuery orderByClanName($order = Criteria::ASC) Order by the clan_name column
 * @method ClanQuery orderByClanCreatedDate($order = Criteria::ASC) Order by the clan_created_date column
 * @method ClanQuery orderByClanFounder($order = Criteria::ASC) Order by the clan_founder column
 * @method ClanQuery orderByClanAvatarUrl($order = Criteria::ASC) Order by the clan_avatar_url column
 * @method ClanQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method ClanQuery groupByClanId() Group by the clan_id column
 * @method ClanQuery groupByClanName() Group by the clan_name column
 * @method ClanQuery groupByClanCreatedDate() Group by the clan_created_date column
 * @method ClanQuery groupByClanFounder() Group by the clan_founder column
 * @method ClanQuery groupByClanAvatarUrl() Group by the clan_avatar_url column
 * @method ClanQuery groupByDescription() Group by the description column
 *
 * @method ClanQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ClanQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ClanQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ClanQuery leftJoinClanPlayer($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClanPlayer relation
 * @method ClanQuery rightJoinClanPlayer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClanPlayer relation
 * @method ClanQuery innerJoinClanPlayer($relationAlias = null) Adds a INNER JOIN clause to the query using the ClanPlayer relation
 *
 * @method Clan findOne(PropelPDO $con = null) Return the first Clan matching the query
 * @method Clan findOneOrCreate(PropelPDO $con = null) Return the first Clan matching the query, or a new Clan object populated from the query conditions when no match is found
 *
 * @method Clan findOneByClanName(string $clan_name) Return the first Clan filtered by the clan_name column
 * @method Clan findOneByClanCreatedDate(string $clan_created_date) Return the first Clan filtered by the clan_created_date column
 * @method Clan findOneByClanFounder(string $clan_founder) Return the first Clan filtered by the clan_founder column
 * @method Clan findOneByClanAvatarUrl(string $clan_avatar_url) Return the first Clan filtered by the clan_avatar_url column
 * @method Clan findOneByDescription(string $description) Return the first Clan filtered by the description column
 *
 * @method array findByClanId(int $clan_id) Return Clan objects filtered by the clan_id column
 * @method array findByClanName(string $clan_name) Return Clan objects filtered by the clan_name column
 * @method array findByClanCreatedDate(string $clan_created_date) Return Clan objects filtered by the clan_created_date column
 * @method array findByClanFounder(string $clan_founder) Return Clan objects filtered by the clan_founder column
 * @method array findByClanAvatarUrl(string $clan_avatar_url) Return Clan objects filtered by the clan_avatar_url column
 * @method array findByDescription(string $description) Return Clan objects filtered by the description column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseClanQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseClanQuery object.
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
            $modelName = 'deploy\\model\\Clan';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ClanQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ClanQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ClanQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ClanQuery) {
            return $criteria;
        }
        $query = new ClanQuery(null, null, $modelAlias);

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
     * @return   Clan|Clan[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClanPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ClanPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Clan A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByClanId($key, $con = null)
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
     * @return                 Clan A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "clan_id", "clan_name", "clan_created_date", "clan_founder", "clan_avatar_url", "description" FROM "clan" WHERE "clan_id" = :p0';
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
            $obj = new Clan();
            $obj->hydrate($row);
            ClanPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Clan|Clan[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Clan[]|mixed the list of results, formatted by the current formatter
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
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClanPeer::CLAN_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClanPeer::CLAN_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the clan_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClanId(1234); // WHERE clan_id = 1234
     * $query->filterByClanId(array(12, 34)); // WHERE clan_id IN (12, 34)
     * $query->filterByClanId(array('min' => 12)); // WHERE clan_id >= 12
     * $query->filterByClanId(array('max' => 12)); // WHERE clan_id <= 12
     * </code>
     *
     * @param     mixed $clanId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByClanId($clanId = null, $comparison = null)
    {
        if (is_array($clanId)) {
            $useMinMax = false;
            if (isset($clanId['min'])) {
                $this->addUsingAlias(ClanPeer::CLAN_ID, $clanId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clanId['max'])) {
                $this->addUsingAlias(ClanPeer::CLAN_ID, $clanId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClanPeer::CLAN_ID, $clanId, $comparison);
    }

    /**
     * Filter the query on the clan_name column
     *
     * Example usage:
     * <code>
     * $query->filterByClanName('fooValue');   // WHERE clan_name = 'fooValue'
     * $query->filterByClanName('%fooValue%'); // WHERE clan_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clanName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByClanName($clanName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clanName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clanName)) {
                $clanName = str_replace('*', '%', $clanName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClanPeer::CLAN_NAME, $clanName, $comparison);
    }

    /**
     * Filter the query on the clan_created_date column
     *
     * Example usage:
     * <code>
     * $query->filterByClanCreatedDate('2011-03-14'); // WHERE clan_created_date = '2011-03-14'
     * $query->filterByClanCreatedDate('now'); // WHERE clan_created_date = '2011-03-14'
     * $query->filterByClanCreatedDate(array('max' => 'yesterday')); // WHERE clan_created_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $clanCreatedDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByClanCreatedDate($clanCreatedDate = null, $comparison = null)
    {
        if (is_array($clanCreatedDate)) {
            $useMinMax = false;
            if (isset($clanCreatedDate['min'])) {
                $this->addUsingAlias(ClanPeer::CLAN_CREATED_DATE, $clanCreatedDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clanCreatedDate['max'])) {
                $this->addUsingAlias(ClanPeer::CLAN_CREATED_DATE, $clanCreatedDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClanPeer::CLAN_CREATED_DATE, $clanCreatedDate, $comparison);
    }

    /**
     * Filter the query on the clan_founder column
     *
     * Example usage:
     * <code>
     * $query->filterByClanFounder('fooValue');   // WHERE clan_founder = 'fooValue'
     * $query->filterByClanFounder('%fooValue%'); // WHERE clan_founder LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clanFounder The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByClanFounder($clanFounder = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clanFounder)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clanFounder)) {
                $clanFounder = str_replace('*', '%', $clanFounder);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClanPeer::CLAN_FOUNDER, $clanFounder, $comparison);
    }

    /**
     * Filter the query on the clan_avatar_url column
     *
     * Example usage:
     * <code>
     * $query->filterByClanAvatarUrl('fooValue');   // WHERE clan_avatar_url = 'fooValue'
     * $query->filterByClanAvatarUrl('%fooValue%'); // WHERE clan_avatar_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clanAvatarUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByClanAvatarUrl($clanAvatarUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clanAvatarUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clanAvatarUrl)) {
                $clanAvatarUrl = str_replace('*', '%', $clanAvatarUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClanPeer::CLAN_AVATAR_URL, $clanAvatarUrl, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClanPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related ClanPlayer object
     *
     * @param   ClanPlayer|PropelObjectCollection $clanPlayer  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClanQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClanPlayer($clanPlayer, $comparison = null)
    {
        if ($clanPlayer instanceof ClanPlayer) {
            return $this
                ->addUsingAlias(ClanPeer::CLAN_ID, $clanPlayer->getClanId(), $comparison);
        } elseif ($clanPlayer instanceof PropelObjectCollection) {
            return $this
                ->useClanPlayerQuery()
                ->filterByPrimaryKeys($clanPlayer->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByClanPlayer() only accepts arguments of type ClanPlayer or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClanPlayer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function joinClanPlayer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClanPlayer');

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
            $this->addJoinObject($join, 'ClanPlayer');
        }

        return $this;
    }

    /**
     * Use the ClanPlayer relation ClanPlayer object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClanPlayerQuery A secondary query class using the current class as primary query
     */
    public function useClanPlayerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClanPlayer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClanPlayer', '\deploy\model\ClanPlayerQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Clan $clan Object to remove from the list of results
     *
     * @return ClanQuery The current query, for fluid interface
     */
    public function prune($clan = null)
    {
        if ($clan) {
            $this->addUsingAlias(ClanPeer::CLAN_ID, $clan->getClanId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
