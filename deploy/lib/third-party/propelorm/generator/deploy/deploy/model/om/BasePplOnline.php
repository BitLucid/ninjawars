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
use deploy\model\PplOnline;
use deploy\model\PplOnlinePeer;
use deploy\model\PplOnlineQuery;

/**
 * Base class that represents a row from the 'ppl_online' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BasePplOnline extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\PplOnlinePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PplOnlinePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the session_id field.
     * @var        string
     */
    protected $session_id;

    /**
     * The value for the activity field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $activity;

    /**
     * The value for the member field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $member;

    /**
     * The value for the ip_address field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $ip_address;

    /**
     * The value for the refurl field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $refurl;

    /**
     * The value for the user_agent field.
     * @var        string
     */
    protected $user_agent;

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
        $this->member = false;
        $this->ip_address = '';
        $this->refurl = '';
    }

    /**
     * Initializes internal state of BasePplOnline object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [session_id] column value.
     *
     * @return string
     */
    public function getSessionId()
    {

        return $this->session_id;
    }

    /**
     * Get the [optionally formatted] temporal [activity] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getActivity($format = 'Y-m-d H:i:s')
    {
        if ($this->activity === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->activity);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->activity, true), $x);
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
     * Get the [member] column value.
     *
     * @return boolean
     */
    public function getMember()
    {

        return $this->member;
    }

    /**
     * Get the [ip_address] column value.
     *
     * @return string
     */
    public function getIpAddress()
    {

        return $this->ip_address;
    }

    /**
     * Get the [refurl] column value.
     *
     * @return string
     */
    public function getRefurl()
    {

        return $this->refurl;
    }

    /**
     * Get the [user_agent] column value.
     *
     * @return string
     */
    public function getUserAgent()
    {

        return $this->user_agent;
    }

    /**
     * Set the value of [session_id] column.
     *
     * @param  string $v new value
     * @return PplOnline The current object (for fluent API support)
     */
    public function setSessionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->session_id !== $v) {
            $this->session_id = $v;
            $this->modifiedColumns[] = PplOnlinePeer::SESSION_ID;
        }


        return $this;
    } // setSessionId()

    /**
     * Sets the value of [activity] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return PplOnline The current object (for fluent API support)
     */
    public function setActivity($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->activity !== null || $dt !== null) {
            $currentDateAsString = ($this->activity !== null && $tmpDt = new DateTime($this->activity)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->activity = $newDateAsString;
                $this->modifiedColumns[] = PplOnlinePeer::ACTIVITY;
            }
        } // if either are not null


        return $this;
    } // setActivity()

    /**
     * Sets the value of the [member] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return PplOnline The current object (for fluent API support)
     */
    public function setMember($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->member !== $v) {
            $this->member = $v;
            $this->modifiedColumns[] = PplOnlinePeer::MEMBER;
        }


        return $this;
    } // setMember()

    /**
     * Set the value of [ip_address] column.
     *
     * @param  string $v new value
     * @return PplOnline The current object (for fluent API support)
     */
    public function setIpAddress($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->ip_address !== $v) {
            $this->ip_address = $v;
            $this->modifiedColumns[] = PplOnlinePeer::IP_ADDRESS;
        }


        return $this;
    } // setIpAddress()

    /**
     * Set the value of [refurl] column.
     *
     * @param  string $v new value
     * @return PplOnline The current object (for fluent API support)
     */
    public function setRefurl($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->refurl !== $v) {
            $this->refurl = $v;
            $this->modifiedColumns[] = PplOnlinePeer::REFURL;
        }


        return $this;
    } // setRefurl()

    /**
     * Set the value of [user_agent] column.
     *
     * @param  string $v new value
     * @return PplOnline The current object (for fluent API support)
     */
    public function setUserAgent($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->user_agent !== $v) {
            $this->user_agent = $v;
            $this->modifiedColumns[] = PplOnlinePeer::USER_AGENT;
        }


        return $this;
    } // setUserAgent()

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
            if ($this->member !== false) {
                return false;
            }

            if ($this->ip_address !== '') {
                return false;
            }

            if ($this->refurl !== '') {
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

            $this->session_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
            $this->activity = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->member = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
            $this->ip_address = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->refurl = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->user_agent = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = PplOnlinePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating PplOnline object", $e);
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
            $con = Propel::getConnection(PplOnlinePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PplOnlinePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

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
            $con = Propel::getConnection(PplOnlinePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PplOnlineQuery::create()
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
            $con = Propel::getConnection(PplOnlinePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PplOnlinePeer::addInstanceToPool($this);
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PplOnlinePeer::SESSION_ID)) {
            $modifiedColumns[':p' . $index++]  = '"session_id"';
        }
        if ($this->isColumnModified(PplOnlinePeer::ACTIVITY)) {
            $modifiedColumns[':p' . $index++]  = '"activity"';
        }
        if ($this->isColumnModified(PplOnlinePeer::MEMBER)) {
            $modifiedColumns[':p' . $index++]  = '"member"';
        }
        if ($this->isColumnModified(PplOnlinePeer::IP_ADDRESS)) {
            $modifiedColumns[':p' . $index++]  = '"ip_address"';
        }
        if ($this->isColumnModified(PplOnlinePeer::REFURL)) {
            $modifiedColumns[':p' . $index++]  = '"refurl"';
        }
        if ($this->isColumnModified(PplOnlinePeer::USER_AGENT)) {
            $modifiedColumns[':p' . $index++]  = '"user_agent"';
        }

        $sql = sprintf(
            'INSERT INTO "ppl_online" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"session_id"':
                        $stmt->bindValue($identifier, $this->session_id, PDO::PARAM_STR);
                        break;
                    case '"activity"':
                        $stmt->bindValue($identifier, $this->activity, PDO::PARAM_STR);
                        break;
                    case '"member"':
                        $stmt->bindValue($identifier, $this->member, PDO::PARAM_BOOL);
                        break;
                    case '"ip_address"':
                        $stmt->bindValue($identifier, $this->ip_address, PDO::PARAM_STR);
                        break;
                    case '"refurl"':
                        $stmt->bindValue($identifier, $this->refurl, PDO::PARAM_STR);
                        break;
                    case '"user_agent"':
                        $stmt->bindValue($identifier, $this->user_agent, PDO::PARAM_STR);
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


            if (($retval = PplOnlinePeer::doValidate($this, $columns)) !== true) {
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
        $pos = PplOnlinePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSessionId();
                break;
            case 1:
                return $this->getActivity();
                break;
            case 2:
                return $this->getMember();
                break;
            case 3:
                return $this->getIpAddress();
                break;
            case 4:
                return $this->getRefurl();
                break;
            case 5:
                return $this->getUserAgent();
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
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['PplOnline'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['PplOnline'][$this->getPrimaryKey()] = true;
        $keys = PplOnlinePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSessionId(),
            $keys[1] => $this->getActivity(),
            $keys[2] => $this->getMember(),
            $keys[3] => $this->getIpAddress(),
            $keys[4] => $this->getRefurl(),
            $keys[5] => $this->getUserAgent(),
        );

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
        $pos = PplOnlinePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setSessionId($value);
                break;
            case 1:
                $this->setActivity($value);
                break;
            case 2:
                $this->setMember($value);
                break;
            case 3:
                $this->setIpAddress($value);
                break;
            case 4:
                $this->setRefurl($value);
                break;
            case 5:
                $this->setUserAgent($value);
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
        $keys = PplOnlinePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setSessionId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setActivity($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setMember($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setIpAddress($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setRefurl($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUserAgent($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PplOnlinePeer::DATABASE_NAME);

        if ($this->isColumnModified(PplOnlinePeer::SESSION_ID)) $criteria->add(PplOnlinePeer::SESSION_ID, $this->session_id);
        if ($this->isColumnModified(PplOnlinePeer::ACTIVITY)) $criteria->add(PplOnlinePeer::ACTIVITY, $this->activity);
        if ($this->isColumnModified(PplOnlinePeer::MEMBER)) $criteria->add(PplOnlinePeer::MEMBER, $this->member);
        if ($this->isColumnModified(PplOnlinePeer::IP_ADDRESS)) $criteria->add(PplOnlinePeer::IP_ADDRESS, $this->ip_address);
        if ($this->isColumnModified(PplOnlinePeer::REFURL)) $criteria->add(PplOnlinePeer::REFURL, $this->refurl);
        if ($this->isColumnModified(PplOnlinePeer::USER_AGENT)) $criteria->add(PplOnlinePeer::USER_AGENT, $this->user_agent);

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
        $criteria = new Criteria(PplOnlinePeer::DATABASE_NAME);
        $criteria->add(PplOnlinePeer::SESSION_ID, $this->session_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->getSessionId();
    }

    /**
     * Generic method to set the primary key (session_id column).
     *
     * @param  string $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setSessionId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getSessionId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of PplOnline (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setActivity($this->getActivity());
        $copyObj->setMember($this->getMember());
        $copyObj->setIpAddress($this->getIpAddress());
        $copyObj->setRefurl($this->getRefurl());
        $copyObj->setUserAgent($this->getUserAgent());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setSessionId(NULL); // this is a auto-increment column, so set to default value
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
     * @return PplOnline Clone of current object.
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
     * @return PplOnlinePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PplOnlinePeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->session_id = null;
        $this->activity = null;
        $this->member = null;
        $this->ip_address = null;
        $this->refurl = null;
        $this->user_agent = null;
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

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PplOnlinePeer::DEFAULT_STRING_FORMAT);
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
