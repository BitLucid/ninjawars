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
use deploy\model\Events;
use deploy\model\EventsPeer;
use deploy\model\EventsQuery;

/**
 * Base class that represents a query for the 'events' table.
 *
 *
 *
 * @method EventsQuery orderByEventId($order = Criteria::ASC) Order by the event_id column
 * @method EventsQuery orderBySendTo($order = Criteria::ASC) Order by the send_to column
 * @method EventsQuery orderBySendFrom($order = Criteria::ASC) Order by the send_from column
 * @method EventsQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method EventsQuery orderByUnread($order = Criteria::ASC) Order by the unread column
 * @method EventsQuery orderByDate($order = Criteria::ASC) Order by the date column
 *
 * @method EventsQuery groupByEventId() Group by the event_id column
 * @method EventsQuery groupBySendTo() Group by the send_to column
 * @method EventsQuery groupBySendFrom() Group by the send_from column
 * @method EventsQuery groupByMessage() Group by the message column
 * @method EventsQuery groupByUnread() Group by the unread column
 * @method EventsQuery groupByDate() Group by the date column
 *
 * @method EventsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EventsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EventsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Events findOne(PropelPDO $con = null) Return the first Events matching the query
 * @method Events findOneOrCreate(PropelPDO $con = null) Return the first Events matching the query, or a new Events object populated from the query conditions when no match is found
 *
 * @method Events findOneBySendTo(int $send_to) Return the first Events filtered by the send_to column
 * @method Events findOneBySendFrom(int $send_from) Return the first Events filtered by the send_from column
 * @method Events findOneByMessage(string $message) Return the first Events filtered by the message column
 * @method Events findOneByUnread(int $unread) Return the first Events filtered by the unread column
 * @method Events findOneByDate(string $date) Return the first Events filtered by the date column
 *
 * @method array findByEventId(int $event_id) Return Events objects filtered by the event_id column
 * @method array findBySendTo(int $send_to) Return Events objects filtered by the send_to column
 * @method array findBySendFrom(int $send_from) Return Events objects filtered by the send_from column
 * @method array findByMessage(string $message) Return Events objects filtered by the message column
 * @method array findByUnread(int $unread) Return Events objects filtered by the unread column
 * @method array findByDate(string $date) Return Events objects filtered by the date column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseEventsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEventsQuery object.
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
            $modelName = 'deploy\\model\\Events';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new EventsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   EventsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EventsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EventsQuery) {
            return $criteria;
        }
        $query = new EventsQuery(null, null, $modelAlias);

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
     * @return   Events|Events[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EventsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Events A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByEventId($key, $con = null)
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
     * @return                 Events A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "event_id", "send_to", "send_from", "message", "unread", "date" FROM "events" WHERE "event_id" = :p0';
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
            $obj = new Events();
            $obj->hydrate($row);
            EventsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Events|Events[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Events[]|mixed the list of results, formatted by the current formatter
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EventsPeer::EVENT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EventsPeer::EVENT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the event_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEventId(1234); // WHERE event_id = 1234
     * $query->filterByEventId(array(12, 34)); // WHERE event_id IN (12, 34)
     * $query->filterByEventId(array('min' => 12)); // WHERE event_id >= 12
     * $query->filterByEventId(array('max' => 12)); // WHERE event_id <= 12
     * </code>
     *
     * @param     mixed $eventId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByEventId($eventId = null, $comparison = null)
    {
        if (is_array($eventId)) {
            $useMinMax = false;
            if (isset($eventId['min'])) {
                $this->addUsingAlias(EventsPeer::EVENT_ID, $eventId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($eventId['max'])) {
                $this->addUsingAlias(EventsPeer::EVENT_ID, $eventId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::EVENT_ID, $eventId, $comparison);
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
     * @param     mixed $sendTo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterBySendTo($sendTo = null, $comparison = null)
    {
        if (is_array($sendTo)) {
            $useMinMax = false;
            if (isset($sendTo['min'])) {
                $this->addUsingAlias(EventsPeer::SEND_TO, $sendTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sendTo['max'])) {
                $this->addUsingAlias(EventsPeer::SEND_TO, $sendTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::SEND_TO, $sendTo, $comparison);
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
     * @param     mixed $sendFrom The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterBySendFrom($sendFrom = null, $comparison = null)
    {
        if (is_array($sendFrom)) {
            $useMinMax = false;
            if (isset($sendFrom['min'])) {
                $this->addUsingAlias(EventsPeer::SEND_FROM, $sendFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sendFrom['max'])) {
                $this->addUsingAlias(EventsPeer::SEND_FROM, $sendFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::SEND_FROM, $sendFrom, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsPeer::MESSAGE, $message, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByUnread($unread = null, $comparison = null)
    {
        if (is_array($unread)) {
            $useMinMax = false;
            if (isset($unread['min'])) {
                $this->addUsingAlias(EventsPeer::UNREAD, $unread['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unread['max'])) {
                $this->addUsingAlias(EventsPeer::UNREAD, $unread['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::UNREAD, $unread, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByDate($date = null, $comparison = null)
    {
        if (is_array($date)) {
            $useMinMax = false;
            if (isset($date['min'])) {
                $this->addUsingAlias(EventsPeer::DATE, $date['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($date['max'])) {
                $this->addUsingAlias(EventsPeer::DATE, $date['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::DATE, $date, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Events $events Object to remove from the list of results
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function prune($events = null)
    {
        if ($events) {
            $this->addUsingAlias(EventsPeer::EVENT_ID, $events->getEventId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
