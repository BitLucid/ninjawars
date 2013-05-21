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
use deploy\model\ClanPlayer;
use deploy\model\ClanPlayerPeer;
use deploy\model\ClanPlayerQuery;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'clan_player' table.
 *
 *
 *
 * @method ClanPlayerQuery orderByClanId($order = Criteria::ASC) Order by the _clan_id column
 * @method ClanPlayerQuery orderByPlayerId($order = Criteria::ASC) Order by the _player_id column
 * @method ClanPlayerQuery orderByMemberLevel($order = Criteria::ASC) Order by the member_level column
 *
 * @method ClanPlayerQuery groupByClanId() Group by the _clan_id column
 * @method ClanPlayerQuery groupByPlayerId() Group by the _player_id column
 * @method ClanPlayerQuery groupByMemberLevel() Group by the member_level column
 *
 * @method ClanPlayerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ClanPlayerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ClanPlayerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ClanPlayerQuery leftJoinClan($relationAlias = null) Adds a LEFT JOIN clause to the query using the Clan relation
 * @method ClanPlayerQuery rightJoinClan($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Clan relation
 * @method ClanPlayerQuery innerJoinClan($relationAlias = null) Adds a INNER JOIN clause to the query using the Clan relation
 *
 * @method ClanPlayerQuery leftJoinPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Players relation
 * @method ClanPlayerQuery rightJoinPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Players relation
 * @method ClanPlayerQuery innerJoinPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the Players relation
 *
 * @method ClanPlayer findOne(PropelPDO $con = null) Return the first ClanPlayer matching the query
 * @method ClanPlayer findOneOrCreate(PropelPDO $con = null) Return the first ClanPlayer matching the query, or a new ClanPlayer object populated from the query conditions when no match is found
 *
 * @method ClanPlayer findOneByClanId(int $_clan_id) Return the first ClanPlayer filtered by the _clan_id column
 * @method ClanPlayer findOneByPlayerId(int $_player_id) Return the first ClanPlayer filtered by the _player_id column
 * @method ClanPlayer findOneByMemberLevel(int $member_level) Return the first ClanPlayer filtered by the member_level column
 *
 * @method array findByClanId(int $_clan_id) Return ClanPlayer objects filtered by the _clan_id column
 * @method array findByPlayerId(int $_player_id) Return ClanPlayer objects filtered by the _player_id column
 * @method array findByMemberLevel(int $member_level) Return ClanPlayer objects filtered by the member_level column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseClanPlayerQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseClanPlayerQuery object.
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
            $modelName = 'deploy\\model\\ClanPlayer';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ClanPlayerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ClanPlayerQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ClanPlayerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ClanPlayerQuery) {
            return $criteria;
        }
        $query = new ClanPlayerQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$_clan_id, $_player_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ClanPlayer|ClanPlayer[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClanPlayerPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ClanPlayerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ClanPlayer A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "_clan_id", "_player_id", "member_level" FROM "clan_player" WHERE "_clan_id" = :p0 AND "_player_id" = :p1';
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
            $obj = new ClanPlayer();
            $obj->hydrate($row);
            ClanPlayerPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ClanPlayer|ClanPlayer[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ClanPlayer[]|mixed the list of results, formatted by the current formatter
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
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ClanPlayerPeer::_CLAN_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ClanPlayerPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the _clan_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClanId(1234); // WHERE _clan_id = 1234
     * $query->filterByClanId(array(12, 34)); // WHERE _clan_id IN (12, 34)
     * $query->filterByClanId(array('min' => 12)); // WHERE _clan_id >= 12
     * $query->filterByClanId(array('max' => 12)); // WHERE _clan_id <= 12
     * </code>
     *
     * @see       filterByClan()
     *
     * @param     mixed $clanId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function filterByClanId($clanId = null, $comparison = null)
    {
        if (is_array($clanId)) {
            $useMinMax = false;
            if (isset($clanId['min'])) {
                $this->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $clanId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clanId['max'])) {
                $this->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $clanId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $clanId, $comparison);
    }

    /**
     * Filter the query on the _player_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerId(1234); // WHERE _player_id = 1234
     * $query->filterByPlayerId(array(12, 34)); // WHERE _player_id IN (12, 34)
     * $query->filterByPlayerId(array('min' => 12)); // WHERE _player_id >= 12
     * $query->filterByPlayerId(array('max' => 12)); // WHERE _player_id <= 12
     * </code>
     *
     * @see       filterByPlayers()
     *
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the member_level column
     *
     * Example usage:
     * <code>
     * $query->filterByMemberLevel(1234); // WHERE member_level = 1234
     * $query->filterByMemberLevel(array(12, 34)); // WHERE member_level IN (12, 34)
     * $query->filterByMemberLevel(array('min' => 12)); // WHERE member_level >= 12
     * $query->filterByMemberLevel(array('max' => 12)); // WHERE member_level <= 12
     * </code>
     *
     * @param     mixed $memberLevel The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function filterByMemberLevel($memberLevel = null, $comparison = null)
    {
        if (is_array($memberLevel)) {
            $useMinMax = false;
            if (isset($memberLevel['min'])) {
                $this->addUsingAlias(ClanPlayerPeer::MEMBER_LEVEL, $memberLevel['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($memberLevel['max'])) {
                $this->addUsingAlias(ClanPlayerPeer::MEMBER_LEVEL, $memberLevel['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClanPlayerPeer::MEMBER_LEVEL, $memberLevel, $comparison);
    }

    /**
     * Filter the query by a related Clan object
     *
     * @param   Clan|PropelObjectCollection $clan The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClanPlayerQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByClan($clan, $comparison = null)
    {
        if ($clan instanceof Clan) {
            return $this
                ->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $clan->getClanId(), $comparison);
        } elseif ($clan instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClanPlayerPeer::_CLAN_ID, $clan->toKeyValue('PrimaryKey', 'ClanId'), $comparison);
        } else {
            throw new PropelException('filterByClan() only accepts arguments of type Clan or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Clan relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function joinClan($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Clan');

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
            $this->addJoinObject($join, 'Clan');
        }

        return $this;
    }

    /**
     * Use the Clan relation Clan object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\ClanQuery A secondary query class using the current class as primary query
     */
    public function useClanQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClan($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Clan', '\deploy\model\ClanQuery');
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ClanPlayerQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayers($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClanPlayerPeer::_PLAYER_ID, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
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
     * @return ClanPlayerQuery The current query, for fluid interface
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
     * @param   ClanPlayer $clanPlayer Object to remove from the list of results
     *
     * @return ClanPlayerQuery The current query, for fluid interface
     */
    public function prune($clanPlayer = null)
    {
        if ($clanPlayer) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ClanPlayerPeer::_CLAN_ID), $clanPlayer->getClanId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ClanPlayerPeer::_PLAYER_ID), $clanPlayer->getPlayerId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
