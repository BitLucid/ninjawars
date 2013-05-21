<?php

namespace deploy\model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use deploy\model\Messages;
use deploy\model\MessagesPeer;
use deploy\model\MessagesQuery;
use deploy\model\Players;
use deploy\model\PlayersQuery;

/**
 * Base class that represents a row from the 'messages' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseMessages extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\MessagesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MessagesPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the message_id field.
     * @var        int
     */
    protected $message_id;

    /**
     * The value for the message field.
     * @var        string
     */
    protected $message;

    /**
     * The value for the date field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $date;

    /**
     * The value for the send_to field.
     * @var        int
     */
    protected $send_to;

    /**
     * The value for the send_from field.
     * @var        int
     */
    protected $send_from;

    /**
     * The value for the unread field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $unread;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $type;

    /**
     * @var        Players
     */
    protected $aPlayersRelatedBySendFrom;

    /**
     * @var        Players
     */
    protected $aPlayersRelatedBySendTo;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->unread = 1;
        $this->type = 0;
    }

    /**
     * Initializes internal state of BaseMessages object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [message_id] column value.
     *
     * @return int
     */
    public function getMessageId()
    {

        return $this->message_id;
    }

    /**
     * Get the [message] column value.
     *
     * @return string
     */
    public function getMessage()
    {

        return $this->message;
    }

    /**
     * Get the [optionally formatted] temporal [date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDate($format = 'Y-m-d H:i:s')
    {
        if ($this->date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [send_to] column value.
     *
     * @return int
     */
    public function getSendTo()
    {

        return $this->send_to;
    }

    /**
     * Get the [send_from] column value.
     *
     * @return int
     */
    public function getSendFrom()
    {

        return $this->send_from;
    }

    /**
     * Get the [unread] column value.
     *
     * @return int
     */
    public function getUnread()
    {

        return $this->unread;
    }

    /**
     * Get the [type] column value.
     *
     * @return int
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Set the value of [message_id] column.
     *
     * @param  int $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setMessageId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->message_id !== $v) {
            $this->message_id = $v;
            $this->modifiedColumns[] = MessagesPeer::MESSAGE_ID;
        }


        return $this;
    } // setMessageId()

    /**
     * Set the value of [message] column.
     *
     * @param  string $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setMessage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->message !== $v) {
            $this->message = $v;
            $this->modifiedColumns[] = MessagesPeer::MESSAGE;
        }


        return $this;
    } // setMessage()

    /**
     * Sets the value of [date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Messages The current object (for fluent API support)
     */
    public function setDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->date !== null || $dt !== null) {
            $currentDateAsString = ($this->date !== null && $tmpDt = new DateTime($this->date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->date = $newDateAsString;
                $this->modifiedColumns[] = MessagesPeer::DATE;
            }
        } // if either are not null


        return $this;
    } // setDate()

    /**
     * Set the value of [send_to] column.
     *
     * @param  int $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setSendTo($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->send_to !== $v) {
            $this->send_to = $v;
            $this->modifiedColumns[] = MessagesPeer::SEND_TO;
        }

        if ($this->aPlayersRelatedBySendTo !== null && $this->aPlayersRelatedBySendTo->getPlayerId() !== $v) {
            $this->aPlayersRelatedBySendTo = null;
        }


        return $this;
    } // setSendTo()

    /**
     * Set the value of [send_from] column.
     *
     * @param  int $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setSendFrom($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->send_from !== $v) {
            $this->send_from = $v;
            $this->modifiedColumns[] = MessagesPeer::SEND_FROM;
        }

        if ($this->aPlayersRelatedBySendFrom !== null && $this->aPlayersRelatedBySendFrom->getPlayerId() !== $v) {
            $this->aPlayersRelatedBySendFrom = null;
        }


        return $this;
    } // setSendFrom()

    /**
     * Set the value of [unread] column.
     *
     * @param  int $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setUnread($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->unread !== $v) {
            $this->unread = $v;
            $this->modifiedColumns[] = MessagesPeer::UNREAD;
        }


        return $this;
    } // setUnread()

    /**
     * Set the value of [type] column.
     *
     * @param  int $v new value
     * @return Messages The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = MessagesPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->unread !== 1) {
                return false;
            }

            if ($this->type !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->message_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->message = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->date = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->send_to = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->send_from = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->unread = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->type = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = MessagesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Messages object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aPlayersRelatedBySendTo !== null && $this->send_to !== $this->aPlayersRelatedBySendTo->getPlayerId()) {
            $this->aPlayersRelatedBySendTo = null;
        }
        if ($this->aPlayersRelatedBySendFrom !== null && $this->send_from !== $this->aPlayersRelatedBySendFrom->getPlayerId()) {
            $this->aPlayersRelatedBySendFrom = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(MessagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = MessagesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPlayersRelatedBySendFrom = null;
            $this->aPlayersRelatedBySendTo = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(MessagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = MessagesQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(MessagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                MessagesPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPlayersRelatedBySendFrom !== null) {
                if ($this->aPlayersRelatedBySendFrom->isModified() || $this->aPlayersRelatedBySendFrom->isNew()) {
                    $affectedRows += $this->aPlayersRelatedBySendFrom->save($con);
                }
                $this->setPlayersRelatedBySendFrom($this->aPlayersRelatedBySendFrom);
            }

            if ($this->aPlayersRelatedBySendTo !== null) {
                if ($this->aPlayersRelatedBySendTo->isModified() || $this->aPlayersRelatedBySendTo->isNew()) {
                    $affectedRows += $this->aPlayersRelatedBySendTo->save($con);
                }
                $this->setPlayersRelatedBySendTo($this->aPlayersRelatedBySendTo);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = MessagesPeer::MESSAGE_ID;
        if (null !== $this->message_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . MessagesPeer::MESSAGE_ID . ')');
        }
        if (null === $this->message_id) {
            try {
                $stmt = $con->query("SELECT nextval('messages_message_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->message_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(MessagesPeer::MESSAGE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"message_id"';
        }
        if ($this->isColumnModified(MessagesPeer::MESSAGE)) {
            $modifiedColumns[':p' . $index++]  = '"message"';
        }
        if ($this->isColumnModified(MessagesPeer::DATE)) {
            $modifiedColumns[':p' . $index++]  = '"date"';
        }
        if ($this->isColumnModified(MessagesPeer::SEND_TO)) {
            $modifiedColumns[':p' . $index++]  = '"send_to"';
        }
        if ($this->isColumnModified(MessagesPeer::SEND_FROM)) {
            $modifiedColumns[':p' . $index++]  = '"send_from"';
        }
        if ($this->isColumnModified(MessagesPeer::UNREAD)) {
            $modifiedColumns[':p' . $index++]  = '"unread"';
        }
        if ($this->isColumnModified(MessagesPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }

        $sql = sprintf(
            'INSERT INTO "messages" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"message_id"':
                        $stmt->bindValue($identifier, $this->message_id, PDO::PARAM_INT);
                        break;
                    case '"message"':
                        $stmt->bindValue($identifier, $this->message, PDO::PARAM_STR);
                        break;
                    case '"date"':
                        $stmt->bindValue($identifier, $this->date, PDO::PARAM_STR);
                        break;
                    case '"send_to"':
                        $stmt->bindValue($identifier, $this->send_to, PDO::PARAM_INT);
                        break;
                    case '"send_from"':
                        $stmt->bindValue($identifier, $this->send_from, PDO::PARAM_INT);
                        break;
                    case '"unread"':
                        $stmt->bindValue($identifier, $this->unread, PDO::PARAM_INT);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPlayersRelatedBySendFrom !== null) {
                if (!$this->aPlayersRelatedBySendFrom->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayersRelatedBySendFrom->getValidationFailures());
                }
            }

            if ($this->aPlayersRelatedBySendTo !== null) {
                if (!$this->aPlayersRelatedBySendTo->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayersRelatedBySendTo->getValidationFailures());
                }
            }


            if (($retval = MessagesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = MessagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getMessageId();
                break;
            case 1:
                return $this->getMessage();
                break;
            case 2:
                return $this->getDate();
                break;
            case 3:
                return $this->getSendTo();
                break;
            case 4:
                return $this->getSendFrom();
                break;
            case 5:
                return $this->getUnread();
                break;
            case 6:
                return $this->getType();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Messages'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Messages'][$this->getPrimaryKey()] = true;
        $keys = MessagesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getMessageId(),
            $keys[1] => $this->getMessage(),
            $keys[2] => $this->getDate(),
            $keys[3] => $this->getSendTo(),
            $keys[4] => $this->getSendFrom(),
            $keys[5] => $this->getUnread(),
            $keys[6] => $this->getType(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aPlayersRelatedBySendFrom) {
                $result['PlayersRelatedBySendFrom'] = $this->aPlayersRelatedBySendFrom->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayersRelatedBySendTo) {
                $result['PlayersRelatedBySendTo'] = $this->aPlayersRelatedBySendTo->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = MessagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setMessageId($value);
                break;
            case 1:
                $this->setMessage($value);
                break;
            case 2:
                $this->setDate($value);
                break;
            case 3:
                $this->setSendTo($value);
                break;
            case 4:
                $this->setSendFrom($value);
                break;
            case 5:
                $this->setUnread($value);
                break;
            case 6:
                $this->setType($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = MessagesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setMessageId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setMessage($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDate($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSendTo($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setSendFrom($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUnread($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setType($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MessagesPeer::DATABASE_NAME);

        if ($this->isColumnModified(MessagesPeer::MESSAGE_ID)) $criteria->add(MessagesPeer::MESSAGE_ID, $this->message_id);
        if ($this->isColumnModified(MessagesPeer::MESSAGE)) $criteria->add(MessagesPeer::MESSAGE, $this->message);
        if ($this->isColumnModified(MessagesPeer::DATE)) $criteria->add(MessagesPeer::DATE, $this->date);
        if ($this->isColumnModified(MessagesPeer::SEND_TO)) $criteria->add(MessagesPeer::SEND_TO, $this->send_to);
        if ($this->isColumnModified(MessagesPeer::SEND_FROM)) $criteria->add(MessagesPeer::SEND_FROM, $this->send_from);
        if ($this->isColumnModified(MessagesPeer::UNREAD)) $criteria->add(MessagesPeer::UNREAD, $this->unread);
        if ($this->isColumnModified(MessagesPeer::TYPE)) $criteria->add(MessagesPeer::TYPE, $this->type);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(MessagesPeer::DATABASE_NAME);
        $criteria->add(MessagesPeer::MESSAGE_ID, $this->message_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getMessageId();
    }

    /**
     * Generic method to set the primary key (message_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setMessageId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getMessageId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Messages (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setMessage($this->getMessage());
        $copyObj->setDate($this->getDate());
        $copyObj->setSendTo($this->getSendTo());
        $copyObj->setSendFrom($this->getSendFrom());
        $copyObj->setUnread($this->getUnread());
        $copyObj->setType($this->getType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setMessageId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Messages Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return MessagesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MessagesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Players object.
     *
     * @param                  Players $v
     * @return Messages The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayersRelatedBySendFrom(Players $v = null)
    {
        if ($v === null) {
            $this->setSendFrom(NULL);
        } else {
            $this->setSendFrom($v->getPlayerId());
        }

        $this->aPlayersRelatedBySendFrom = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Players object, it will not be re-added.
        if ($v !== null) {
            $v->addMessagesRelatedBySendFrom($this);
        }


        return $this;
    }


    /**
     * Get the associated Players object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Players The associated Players object.
     * @throws PropelException
     */
    public function getPlayersRelatedBySendFrom(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayersRelatedBySendFrom === null && ($this->send_from !== null) && $doQuery) {
            $this->aPlayersRelatedBySendFrom = PlayersQuery::create()->findPk($this->send_from, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayersRelatedBySendFrom->addMessagessRelatedBySendFrom($this);
             */
        }

        return $this->aPlayersRelatedBySendFrom;
    }

    /**
     * Declares an association between this object and a Players object.
     *
     * @param                  Players $v
     * @return Messages The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayersRelatedBySendTo(Players $v = null)
    {
        if ($v === null) {
            $this->setSendTo(NULL);
        } else {
            $this->setSendTo($v->getPlayerId());
        }

        $this->aPlayersRelatedBySendTo = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Players object, it will not be re-added.
        if ($v !== null) {
            $v->addMessagesRelatedBySendTo($this);
        }


        return $this;
    }


    /**
     * Get the associated Players object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Players The associated Players object.
     * @throws PropelException
     */
    public function getPlayersRelatedBySendTo(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayersRelatedBySendTo === null && ($this->send_to !== null) && $doQuery) {
            $this->aPlayersRelatedBySendTo = PlayersQuery::create()->findPk($this->send_to, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayersRelatedBySendTo->addMessagessRelatedBySendTo($this);
             */
        }

        return $this->aPlayersRelatedBySendTo;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->message_id = null;
        $this->message = null;
        $this->date = null;
        $this->send_to = null;
        $this->send_from = null;
        $this->unread = null;
        $this->type = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->aPlayersRelatedBySendFrom instanceof Persistent) {
              $this->aPlayersRelatedBySendFrom->clearAllReferences($deep);
            }
            if ($this->aPlayersRelatedBySendTo instanceof Persistent) {
              $this->aPlayersRelatedBySendTo->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aPlayersRelatedBySendFrom = null;
        $this->aPlayersRelatedBySendTo = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MessagesPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
