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
use deploy\model\Messages;
use deploy\model\MessagesPeer;
use deploy\model\MessagesQuery;
use deploy\model\Players;

/**
 * Base class that represents a query for the 'messages' table.
 *
 *
 *
 * @method MessagesQuery orderByMessageId($order = Criteria::ASC) Order by the message_id column
 * @method MessagesQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method MessagesQuery orderByDate($order = Criteria::ASC) Order by the date column
 * @method MessagesQuery orderBySendTo($order = Criteria::ASC) Order by the send_to column
 * @method MessagesQuery orderBySendFrom($order = Criteria::ASC) Order by the send_from column
 * @method MessagesQuery orderByUnread($order = Criteria::ASC) Order by the unread column
 * @method MessagesQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method MessagesQuery groupByMessageId() Group by the message_id column
 * @method MessagesQuery groupByMessage() Group by the message column
 * @method MessagesQuery groupByDate() Group by the date column
 * @method MessagesQuery groupBySendTo() Group by the send_to column
 * @method MessagesQuery groupBySendFrom() Group by the send_from column
 * @method MessagesQuery groupByUnread() Group by the unread column
 * @method MessagesQuery groupByType() Group by the type column
 *
 * @method MessagesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method MessagesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method MessagesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method MessagesQuery leftJoinPlayersRelatedBySendFrom($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayersRelatedBySendFrom relation
 * @method MessagesQuery rightJoinPlayersRelatedBySendFrom($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayersRelatedBySendFrom relation
 * @method MessagesQuery innerJoinPlayersRelatedBySendFrom($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayersRelatedBySendFrom relation
 *
 * @method MessagesQuery leftJoinPlayersRelatedBySendTo($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayersRelatedBySendTo relation
 * @method MessagesQuery rightJoinPlayersRelatedBySendTo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayersRelatedBySendTo relation
 * @method MessagesQuery innerJoinPlayersRelatedBySendTo($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayersRelatedBySendTo relation
 *
 * @method Messages findOne(PropelPDO $con = null) Return the first Messages matching the query
 * @method Messages findOneOrCreate(PropelPDO $con = null) Return the first Messages matching the query, or a new Messages object populated from the query conditions when no match is found
 *
 * @method Messages findOneByMessage(string $message) Return the first Messages filtered by the message column
 * @method Messages findOneByDate(string $date) Return the first Messages filtered by the date column
 * @method Messages findOneBySendTo(int $send_to) Return the first Messages filtered by the send_to column
 * @method Messages findOneBySendFrom(int $send_from) Return the first Messages filtered by the send_from column
 * @method Messages findOneByUnread(int $unread) Return the first Messages filtered by the unread column
 * @method Messages findOneByType(int $type) Return the first Messages filtered by the type column
 *
 * @method array findByMessageId(int $message_id) Return Messages objects filtered by the message_id column
 * @method array findByMessage(string $message) Return Messages objects filtered by the message column
 * @method array findByDate(string $date) Return Messages objects filtered by the date column
 * @method array findBySendTo(int $send_to) Return Messages objects filtered by the send_to column
 * @method array findBySendFrom(int $send_from) Return Messages objects filtered by the send_from column
 * @method array findByUnread(int $unread) Return Messages objects filtered by the unread column
 * @method array findByType(int $type) Return Messages objects filtered by the type column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseMessagesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseMessagesQuery object.
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
            $modelName = 'deploy\\model\\Messages';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new MessagesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   MessagesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return MessagesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof MessagesQuery) {
            return $criteria;
        }
        $query = new MessagesQuery(null, null, $modelAlias);

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
     * @return   Messages|Messages[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MessagesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(MessagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Messages A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByMessageId($key, $con = null)
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
     * @return                 Messages A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "message_id", "message", "date", "send_to", "send_from", "unread", "type" FROM "messages" WHERE "message_id" = :p0';
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
            $obj = new Messages();
            $obj->hydrate($row);
            MessagesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Messages|Messages[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Messages[]|mixed the list of results, formatted by the current formatter
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
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the message_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMessageId(1234); // WHERE message_id = 1234
     * $query->filterByMessageId(array(12, 34)); // WHERE message_id IN (12, 34)
     * $query->filterByMessageId(array('min' => 12)); // WHERE message_id >= 12
     * $query->filterByMessageId(array('max' => 12)); // WHERE message_id <= 12
     * </code>
     *
     * @param     mixed $messageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByMessageId($messageId = null, $comparison = null)
    {
        if (is_array($messageId)) {
            $useMinMax = false;
            if (isset($messageId['min'])) {
                $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $messageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($messageId['max'])) {
                $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $messageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $messageId, $comparison);
    }

    /**
     * Filter the query on the message column
     *
     * Example usage:
     * <code>
     * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
     * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $message The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByMessage($message = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($message)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $message)) {
                $message = str_replace('*', '%', $message);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MessagesPeer::MESSAGE, $message, $comparison);
    }

    /**
     * Filter the query on the date column
     *
     * Example usage:
     * <code>
     * $query->filterByDate('2011-03-14'); // WHERE date = '2011-03-14'
     * $query->filterByDate('now'); // WHERE date = '2011-03-14'
     * $query->filterByDate(array('max' => 'yesterday')); // WHERE date > '2011-03-13'
     * </code>
     *
     * @param     mixed $date The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByDate($date = null, $comparison = null)
    {
        if (is_array($date)) {
            $useMinMax = false;
            if (isset($date['min'])) {
                $this->addUsingAlias(MessagesPeer::DATE, $date['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($date['max'])) {
                $this->addUsingAlias(MessagesPeer::DATE, $date['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::DATE, $date, $comparison);
    }

    /**
     * Filter the query on the send_to column
     *
     * Example usage:
     * <code>
     * $query->filterBySendTo(1234); // WHERE send_to = 1234
     * $query->filterBySendTo(array(12, 34)); // WHERE send_to IN (12, 34)
     * $query->filterBySendTo(array('min' => 12)); // WHERE send_to >= 12
     * $query->filterBySendTo(array('max' => 12)); // WHERE send_to <= 12
     * </code>
     *
     * @see       filterByPlayersRelatedBySendTo()
     *
     * @param     mixed $sendTo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterBySendTo($sendTo = null, $comparison = null)
    {
        if (is_array($sendTo)) {
            $useMinMax = false;
            if (isset($sendTo['min'])) {
                $this->addUsingAlias(MessagesPeer::SEND_TO, $sendTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sendTo['max'])) {
                $this->addUsingAlias(MessagesPeer::SEND_TO, $sendTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::SEND_TO, $sendTo, $comparison);
    }

    /**
     * Filter the query on the send_from column
     *
     * Example usage:
     * <code>
     * $query->filterBySendFrom(1234); // WHERE send_from = 1234
     * $query->filterBySendFrom(array(12, 34)); // WHERE send_from IN (12, 34)
     * $query->filterBySendFrom(array('min' => 12)); // WHERE send_from >= 12
     * $query->filterBySendFrom(array('max' => 12)); // WHERE send_from <= 12
     * </code>
     *
     * @see       filterByPlayersRelatedBySendFrom()
     *
     * @param     mixed $sendFrom The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterBySendFrom($sendFrom = null, $comparison = null)
    {
        if (is_array($sendFrom)) {
            $useMinMax = false;
            if (isset($sendFrom['min'])) {
                $this->addUsingAlias(MessagesPeer::SEND_FROM, $sendFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sendFrom['max'])) {
                $this->addUsingAlias(MessagesPeer::SEND_FROM, $sendFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::SEND_FROM, $sendFrom, $comparison);
    }

    /**
     * Filter the query on the unread column
     *
     * Example usage:
     * <code>
     * $query->filterByUnread(1234); // WHERE unread = 1234
     * $query->filterByUnread(array(12, 34)); // WHERE unread IN (12, 34)
     * $query->filterByUnread(array('min' => 12)); // WHERE unread >= 12
     * $query->filterByUnread(array('max' => 12)); // WHERE unread <= 12
     * </code>
     *
     * @param     mixed $unread The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByUnread($unread = null, $comparison = null)
    {
        if (is_array($unread)) {
            $useMinMax = false;
            if (isset($unread['min'])) {
                $this->addUsingAlias(MessagesPeer::UNREAD, $unread['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unread['max'])) {
                $this->addUsingAlias(MessagesPeer::UNREAD, $unread['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::UNREAD, $unread, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type >= 12
     * $query->filterByType(array('max' => 12)); // WHERE type <= 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(MessagesPeer::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(MessagesPeer::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagesPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MessagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayersRelatedBySendFrom($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(MessagesPeer::SEND_FROM, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MessagesPeer::SEND_FROM, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayersRelatedBySendFrom() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayersRelatedBySendFrom relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function joinPlayersRelatedBySendFrom($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayersRelatedBySendFrom');

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
            $this->addJoinObject($join, 'PlayersRelatedBySendFrom');
        }

        return $this;
    }

    /**
     * Use the PlayersRelatedBySendFrom relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersRelatedBySendFromQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPlayersRelatedBySendFrom($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayersRelatedBySendFrom', '\deploy\model\PlayersQuery');
    }

    /**
     * Filter the query by a related Players object
     *
     * @param   Players|PropelObjectCollection $players The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MessagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayersRelatedBySendTo($players, $comparison = null)
    {
        if ($players instanceof Players) {
            return $this
                ->addUsingAlias(MessagesPeer::SEND_TO, $players->getPlayerId(), $comparison);
        } elseif ($players instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MessagesPeer::SEND_TO, $players->toKeyValue('PrimaryKey', 'PlayerId'), $comparison);
        } else {
            throw new PropelException('filterByPlayersRelatedBySendTo() only accepts arguments of type Players or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayersRelatedBySendTo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function joinPlayersRelatedBySendTo($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayersRelatedBySendTo');

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
            $this->addJoinObject($join, 'PlayersRelatedBySendTo');
        }

        return $this;
    }

    /**
     * Use the PlayersRelatedBySendTo relation Players object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \deploy\model\PlayersQuery A secondary query class using the current class as primary query
     */
    public function usePlayersRelatedBySendToQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPlayersRelatedBySendTo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayersRelatedBySendTo', '\deploy\model\PlayersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Messages $messages Object to remove from the list of results
     *
     * @return MessagesQuery The current query, for fluid interface
     */
    public function prune($messages = null)
    {
        if ($messages) {
            $this->addUsingAlias(MessagesPeer::MESSAGE_ID, $messages->getMessageId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
