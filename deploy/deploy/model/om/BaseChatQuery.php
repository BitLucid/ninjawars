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
use deploy\model\Chat;
use deploy\model\ChatPeer;
use deploy\model\ChatQuery;

/**
 * Base class that represents a query for the 'chat' table.
 *
 *
 *
 * @method ChatQuery orderByChatId($order = Criteria::ASC) Order by the chat_id column
 * @method ChatQuery orderBySenderId($order = Criteria::ASC) Order by the sender_id column
 * @method ChatQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method ChatQuery orderByDate($order = Criteria::ASC) Order by the date column
 *
 * @method ChatQuery groupByChatId() Group by the chat_id column
 * @method ChatQuery groupBySenderId() Group by the sender_id column
 * @method ChatQuery groupByMessage() Group by the message column
 * @method ChatQuery groupByDate() Group by the date column
 *
 * @method ChatQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ChatQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ChatQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Chat findOne(PropelPDO $con = null) Return the first Chat matching the query
 * @method Chat findOneOrCreate(PropelPDO $con = null) Return the first Chat matching the query, or a new Chat object populated from the query conditions when no match is found
 *
 * @method Chat findOneBySenderId(int $sender_id) Return the first Chat filtered by the sender_id column
 * @method Chat findOneByMessage(string $message) Return the first Chat filtered by the message column
 * @method Chat findOneByDate(string $date) Return the first Chat filtered by the date column
 *
 * @method array findByChatId(int $chat_id) Return Chat objects filtered by the chat_id column
 * @method array findBySenderId(int $sender_id) Return Chat objects filtered by the sender_id column
 * @method array findByMessage(string $message) Return Chat objects filtered by the message column
 * @method array findByDate(string $date) Return Chat objects filtered by the date column
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseChatQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseChatQuery object.
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
            $modelName = 'deploy\\model\\Chat';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChatQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ChatQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChatQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ChatQuery) {
            return $criteria;
        }
        $query = new ChatQuery(null, null, $modelAlias);

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
     * @return   Chat|Chat[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ChatPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ChatPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Chat A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByChatId($key, $con = null)
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
     * @return                 Chat A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "chat_id", "sender_id", "message", "date" FROM "chat" WHERE "chat_id" = :p0';
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
            $obj = new Chat();
            $obj->hydrate($row);
            ChatPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Chat|Chat[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Chat[]|mixed the list of results, formatted by the current formatter
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
     * @return ChatQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ChatPeer::CHAT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChatQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ChatPeer::CHAT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the chat_id column
     *
     * Example usage:
     * <code>
     * $query->filterByChatId(1234); // WHERE chat_id = 1234
     * $query->filterByChatId(array(12, 34)); // WHERE chat_id IN (12, 34)
     * $query->filterByChatId(array('min' => 12)); // WHERE chat_id >= 12
     * $query->filterByChatId(array('max' => 12)); // WHERE chat_id <= 12
     * </code>
     *
     * @param     mixed $chatId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChatQuery The current query, for fluid interface
     */
    public function filterByChatId($chatId = null, $comparison = null)
    {
        if (is_array($chatId)) {
            $useMinMax = false;
            if (isset($chatId['min'])) {
                $this->addUsingAlias(ChatPeer::CHAT_ID, $chatId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($chatId['max'])) {
                $this->addUsingAlias(ChatPeer::CHAT_ID, $chatId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChatPeer::CHAT_ID, $chatId, $comparison);
    }

    /**
     * Filter the query on the sender_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySenderId(1234); // WHERE sender_id = 1234
     * $query->filterBySenderId(array(12, 34)); // WHERE sender_id IN (12, 34)
     * $query->filterBySenderId(array('min' => 12)); // WHERE sender_id >= 12
     * $query->filterBySenderId(array('max' => 12)); // WHERE sender_id <= 12
     * </code>
     *
     * @param     mixed $senderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChatQuery The current query, for fluid interface
     */
    public function filterBySenderId($senderId = null, $comparison = null)
    {
        if (is_array($senderId)) {
            $useMinMax = false;
            if (isset($senderId['min'])) {
                $this->addUsingAlias(ChatPeer::SENDER_ID, $senderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($senderId['max'])) {
                $this->addUsingAlias(ChatPeer::SENDER_ID, $senderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChatPeer::SENDER_ID, $senderId, $comparison);
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
     * @return ChatQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ChatPeer::MESSAGE, $message, $comparison);
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
     * @return ChatQuery The current query, for fluid interface
     */
    public function filterByDate($date = null, $comparison = null)
    {
        if (is_array($date)) {
            $useMinMax = false;
            if (isset($date['min'])) {
                $this->addUsingAlias(ChatPeer::DATE, $date['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($date['max'])) {
                $this->addUsingAlias(ChatPeer::DATE, $date['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChatPeer::DATE, $date, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Chat $chat Object to remove from the list of results
     *
     * @return ChatQuery The current query, for fluid interface
     */
    public function prune($chat = null)
    {
        if ($chat) {
            $this->addUsingAlias(ChatPeer::CHAT_ID, $chat->getChatId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
