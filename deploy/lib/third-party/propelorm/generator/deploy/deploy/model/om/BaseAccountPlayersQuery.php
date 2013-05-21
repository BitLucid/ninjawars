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
use deploy\model\AccountPlayers;
use deploy\model\AccountPlayersPeer;
use deploy\model\AccountPlayersQuery;
use deploy\model\Accounts;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'account_players' table.
 *
 *
 *
 * @method AccountPlayersQuery orderByAccountId($order = Criteria::ASC) Order by the _account_id column
 * @method AccountPlayersQuery orderByPlayerId($order = Criteria::ASC) Order by the _player_id column
 * @method AccountPlayersQuery orderByLastLogin($order = Criteria::ASC) Order by the last_login column
 * @method AccountPlayersQuery orderByCreatedDate($order = Criteria::ASC) Order by the created_date column
 *
 * @method AccountPlayersQuery groupByAccountId() Group by the _account_id column
 * @method AccountPlayersQuery groupByPlayerId() Group by the _player_id column
 * @method AccountPlayersQuery groupByLastLogin() Group by the last_login column
 * @method AccountPlayersQuery groupByCreatedDate() Group by the created_date column
 *
 * @method AccountPlayersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountPlayersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountPlayersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountPlayersQuery leftJoinAccounts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Accounts relation
 * @method AccountPlayersQuery rightJoinAccounts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Accounts relation
 * @method AccountPlayersQuery innerJoinAccounts($relationAlias = null) Adds a INNER JOIN clause to the query using the Accounts relation
 *
 * @method AccountPlayersQuery leftJoinPlayers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Players relation
 * @method AccountPlayersQuery rightJoinPlayers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Players relation
 * @method AccountPlayersQuery innerJoinPlayers($relationAlias = null) Adds a INNER JOIN clause to the query using the Players relation
 *
 * @method AccountPlayers findOne(PropelPDO $con = null) Return the first AccountPlayers matching the query
 * @method AccountPlayers findOneOrCreate(PropelPDO $con = null) Return the first AccountPlayers matching the query, or a new AccountPlayers object populated from the query conditions when no match is found
 *
 * @method AccountPlayers findOneByAccountId(int $_account_id) Return the first AccountPlayers filtered by the _account_id column
 * @method AccountPlayers findOneByPlayerId(int $_player_id) Return the first AccountPlayers filtered by the _player_id column
 * @method AccountPlayers findOneByLastLogin(string $last_login) Return the first AccountPlayers filtered by the last_login column
 * @method AccountPlayers findOneByCreatedDate(string $created_date) Return the first AccountPlayers filtered by the created_date column
 *
 * @method array findByAccountId(int $_account_id) Return AccountPlayers objects filtered by the _account_id column
 * @method array findByPlayerId(int $_player_id) Return AccountPlayers objects filtered by the _player_id column
 * @method array findByLastLogin(string $last_login) Return AccountPlayers objects filtered by the last_login column
 * @method array findByCreatedDate(string $created_date) Return AccountPlayers objects filtered by the created_date column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseAccountPlayersQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountPlayersQuery object.
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
            $modelName = 'deploy\\model\\AccountPlayers';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountPlayersQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountPlayersQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountPlayersQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountPlayersQuery) {
            return $criteria;
        }
        $query = new AccountPlayersQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$_account_id, $_player_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   AccountPlayers|AccountPlayers[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountPlayersPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountPlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountPlayers A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "_account_id", "_player_id", "last_login", "created_date" FROM "account_players" WHERE "_account_id" = :p0 AND "_player_id" = :p1';
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
            $obj = new AccountPlayers();
            $obj->hydrate($row);
            AccountPlayersPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return AccountPlayers|AccountPlayers[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountPlayers[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(AccountPlayersPeer::_ACCOUNT_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(AccountPlayersPeer::_PLAYER_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the _account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE _account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE _account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE _account_id >= 12
     * $query->filterByAccountId(array('max' => 12)); // WHERE _account_id <= 12
     * </code>
     *
     * @see       filterByAccounts()
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $accountId, $comparison);
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
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the last_login column
     *
     * Example usage:
     * <code>
     * $query->filterByLastLogin('2011-03-14'); // WHERE last_login = '2011-03-14'
     * $query->filterByLastLogin('now'); // WHERE last_login = '2011-03-14'
     * $query->filterByLastLogin(array('max' => 'yesterday')); // WHERE last_login > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastLogin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByLastLogin($lastLogin = null, $comparison = null)
    {
        if (is_array($lastLogin)) {
            $useMinMax = false;
            if (isset($lastLogin['min'])) {
                $this->addUsingAlias(AccountPlayersPeer::LAST_LOGIN, $lastLogin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastLogin['max'])) {
                $this->addUsingAlias(AccountPlayersPeer::LAST_LOGIN, $lastLogin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPlayersPeer::LAST_LOGIN, $lastLogin, $comparison);
    }

    /**
     * Filter the query on the created_date column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedDate('2011-03-14'); // WHERE created_date = '2011-03-14'
     * $query->filterByCreatedDate('now'); // WHERE created_date = '2011-03-14'
     * $query->filterByCreatedDate(array('max' => 'yesterday')); // WHERE created_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function filterByCreatedDate($createdDate = null, $comparison = null)
    {
        if (is_array($createdDate)) {
            $useMinMax = false;
            if (isset($createdDate['min'])) {
                $this->addUsingAlias(AccountPlayersPeer::CREATED_DATE, $createdDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdDate['max'])) {
                $this->addUsingAlias(AccountPlayersPeer::CREATED_DATE, $createdDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPlayersPeer::CREATED_DATE, $createdDate, $comparison);
    }

    /**
     * Filter the query by a related Accounts object
     *
     * @param   Accounts|PropelObjectCollection $accounts The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountPlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccounts($accounts, $comparison = null)
    {
        if ($accounts instanceof Accounts) {
            return $this
                ->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $accounts->getAccountId(), $comparison);
        } elseif ($accounts instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountPlayersPeer::_ACCOUNT_ID, $accounts->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccounts() only accepts arguments of type Accounts or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Accounts relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function joinAccounts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Accounts');

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
            $this->addJoinObject($join, 'Accounts');
        }

        return $this;
    }

    /**
     * Use the Accounts relation Accounts object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\AccountsQuery A secondary query class using the current class as primary query
     */
    public function useAccountsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccounts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Accounts', '\deploy\model\AccountsQuery');
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountPlayersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayers($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountPlayersPeer::_PLAYER_ID, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
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
     * @return AccountPlayersQuery The current query, for fluid interface
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
     * @param   AccountPlayers $accountPlayers Object to remove from the list of results
     *
     * @return AccountPlayersQuery The current query, for fluid interface
     */
    public function prune($accountPlayers = null)
    {
        if ($accountPlayers) {
            $this->addCond('pruneCond0', $this->getAliasedColName(AccountPlayersPeer::_ACCOUNT_ID), $accountPlayers->getAccountId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(AccountPlayersPeer::_PLAYER_ID), $accountPlayers->getPlayerId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
