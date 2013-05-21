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
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use deploy\model\Clan;
use deploy\model\ClanPeer;
use deploy\model\ClanPlayer;
use deploy\model\ClanPlayerQuery;
use deploy\model\ClanQuery;

/**
 * Base class that represents a row from the 'clan' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseClan extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\ClanPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ClanPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the clan_id field.
     * @var        int
     */
    protected $clan_id;

    /**
     * The value for the clan_name field.
     * @var        string
     */
    protected $clan_name;

    /**
     * The value for the clan_created_date field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $clan_created_date;

    /**
     * The value for the clan_founder field.
     * @var        string
     */
    protected $clan_founder;

    /**
     * The value for the clan_avatar_url field.
     * @var        string
     */
    protected $clan_avatar_url;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * @var        PropelObjectCollection|ClanPlayer[] Collection to store aggregation of ClanPlayer objects.
     */
    protected $collClanPlayers;
    protected $collClanPlayersPartial;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $clanPlayersScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
    }

    /**
     * Initializes internal state of BaseClan object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [clan_id] column value.
     *
     * @return int
     */
    public function getClanId()
    {

        return $this->clan_id;
    }

    /**
     * Get the [clan_name] column value.
     *
     * @return string
     */
    public function getClanName()
    {

        return $this->clan_name;
    }

    /**
     * Get the [optionally formatted] temporal [clan_created_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getClanCreatedDate($format = 'Y-m-d H:i:s')
    {
        if ($this->clan_created_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->clan_created_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->clan_created_date, true), $x);
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
     * Get the [clan_founder] column value.
     *
     * @return string
     */
    public function getClanFounder()
    {

        return $this->clan_founder;
    }

    /**
     * Get the [clan_avatar_url] column value.
     *
     * @return string
     */
    public function getClanAvatarUrl()
    {

        return $this->clan_avatar_url;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Set the value of [clan_id] column.
     *
     * @param  int $v new value
     * @return Clan The current object (for fluent API support)
     */
    public function setClanId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->clan_id !== $v) {
            $this->clan_id = $v;
            $this->modifiedColumns[] = ClanPeer::CLAN_ID;
        }


        return $this;
    } // setClanId()

    /**
     * Set the value of [clan_name] column.
     *
     * @param  string $v new value
     * @return Clan The current object (for fluent API support)
     */
    public function setClanName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->clan_name !== $v) {
            $this->clan_name = $v;
            $this->modifiedColumns[] = ClanPeer::CLAN_NAME;
        }


        return $this;
    } // setClanName()

    /**
     * Sets the value of [clan_created_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Clan The current object (for fluent API support)
     */
    public function setClanCreatedDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->clan_created_date !== null || $dt !== null) {
            $currentDateAsString = ($this->clan_created_date !== null && $tmpDt = new DateTime($this->clan_created_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->clan_created_date = $newDateAsString;
                $this->modifiedColumns[] = ClanPeer::CLAN_CREATED_DATE;
            }
        } // if either are not null


        return $this;
    } // setClanCreatedDate()

    /**
     * Set the value of [clan_founder] column.
     *
     * @param  string $v new value
     * @return Clan The current object (for fluent API support)
     */
    public function setClanFounder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->clan_founder !== $v) {
            $this->clan_founder = $v;
            $this->modifiedColumns[] = ClanPeer::CLAN_FOUNDER;
        }


        return $this;
    } // setClanFounder()

    /**
     * Set the value of [clan_avatar_url] column.
     *
     * @param  string $v new value
     * @return Clan The current object (for fluent API support)
     */
    public function setClanAvatarUrl($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->clan_avatar_url !== $v) {
            $this->clan_avatar_url = $v;
            $this->modifiedColumns[] = ClanPeer::CLAN_AVATAR_URL;
        }


        return $this;
    } // setClanAvatarUrl()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return Clan The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = ClanPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

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

            $this->clan_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->clan_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->clan_created_date = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->clan_founder = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->clan_avatar_url = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->description = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = ClanPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Clan object", $e);
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
            $con = Propel::getConnection(ClanPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ClanPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collClanPlayers = null;

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
            $con = Propel::getConnection(ClanPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ClanQuery::create()
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
            $con = Propel::getConnection(ClanPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ClanPeer::addInstanceToPool($this);
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

            if ($this->clanPlayersScheduledForDeletion !== null) {
                if (!$this->clanPlayersScheduledForDeletion->isEmpty()) {
                    ClanPlayerQuery::create()
                        ->filterByPrimaryKeys($this->clanPlayersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->clanPlayersScheduledForDeletion = null;
                }
            }

            if ($this->collClanPlayers !== null) {
                foreach ($this->collClanPlayers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

        $this->modifiedColumns[] = ClanPeer::CLAN_ID;
        if (null !== $this->clan_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ClanPeer::CLAN_ID . ')');
        }
        if (null === $this->clan_id) {
            try {
                $stmt = $con->query("SELECT nextval('clan_clan_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->clan_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ClanPeer::CLAN_ID)) {
            $modifiedColumns[':p' . $index++]  = '"clan_id"';
        }
        if ($this->isColumnModified(ClanPeer::CLAN_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"clan_name"';
        }
        if ($this->isColumnModified(ClanPeer::CLAN_CREATED_DATE)) {
            $modifiedColumns[':p' . $index++]  = '"clan_created_date"';
        }
        if ($this->isColumnModified(ClanPeer::CLAN_FOUNDER)) {
            $modifiedColumns[':p' . $index++]  = '"clan_founder"';
        }
        if ($this->isColumnModified(ClanPeer::CLAN_AVATAR_URL)) {
            $modifiedColumns[':p' . $index++]  = '"clan_avatar_url"';
        }
        if ($this->isColumnModified(ClanPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }

        $sql = sprintf(
            'INSERT INTO "clan" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"clan_id"':
                        $stmt->bindValue($identifier, $this->clan_id, PDO::PARAM_INT);
                        break;
                    case '"clan_name"':
                        $stmt->bindValue($identifier, $this->clan_name, PDO::PARAM_STR);
                        break;
                    case '"clan_created_date"':
                        $stmt->bindValue($identifier, $this->clan_created_date, PDO::PARAM_STR);
                        break;
                    case '"clan_founder"':
                        $stmt->bindValue($identifier, $this->clan_founder, PDO::PARAM_STR);
                        break;
                    case '"clan_avatar_url"':
                        $stmt->bindValue($identifier, $this->clan_avatar_url, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
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


            if (($retval = ClanPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collClanPlayers !== null) {
                    foreach ($this->collClanPlayers as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
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
        $pos = ClanPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getClanId();
                break;
            case 1:
                return $this->getClanName();
                break;
            case 2:
                return $this->getClanCreatedDate();
                break;
            case 3:
                return $this->getClanFounder();
                break;
            case 4:
                return $this->getClanAvatarUrl();
                break;
            case 5:
                return $this->getDescription();
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
        if (isset($alreadyDumpedObjects['Clan'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Clan'][$this->getPrimaryKey()] = true;
        $keys = ClanPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getClanId(),
            $keys[1] => $this->getClanName(),
            $keys[2] => $this->getClanCreatedDate(),
            $keys[3] => $this->getClanFounder(),
            $keys[4] => $this->getClanAvatarUrl(),
            $keys[5] => $this->getDescription(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collClanPlayers) {
                $result['ClanPlayers'] = $this->collClanPlayers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ClanPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setClanId($value);
                break;
            case 1:
                $this->setClanName($value);
                break;
            case 2:
                $this->setClanCreatedDate($value);
                break;
            case 3:
                $this->setClanFounder($value);
                break;
            case 4:
                $this->setClanAvatarUrl($value);
                break;
            case 5:
                $this->setDescription($value);
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
        $keys = ClanPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setClanId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setClanName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setClanCreatedDate($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setClanFounder($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setClanAvatarUrl($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ClanPeer::DATABASE_NAME);

        if ($this->isColumnModified(ClanPeer::CLAN_ID)) $criteria->add(ClanPeer::CLAN_ID, $this->clan_id);
        if ($this->isColumnModified(ClanPeer::CLAN_NAME)) $criteria->add(ClanPeer::CLAN_NAME, $this->clan_name);
        if ($this->isColumnModified(ClanPeer::CLAN_CREATED_DATE)) $criteria->add(ClanPeer::CLAN_CREATED_DATE, $this->clan_created_date);
        if ($this->isColumnModified(ClanPeer::CLAN_FOUNDER)) $criteria->add(ClanPeer::CLAN_FOUNDER, $this->clan_founder);
        if ($this->isColumnModified(ClanPeer::CLAN_AVATAR_URL)) $criteria->add(ClanPeer::CLAN_AVATAR_URL, $this->clan_avatar_url);
        if ($this->isColumnModified(ClanPeer::DESCRIPTION)) $criteria->add(ClanPeer::DESCRIPTION, $this->description);

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
        $criteria = new Criteria(ClanPeer::DATABASE_NAME);
        $criteria->add(ClanPeer::CLAN_ID, $this->clan_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getClanId();
    }

    /**
     * Generic method to set the primary key (clan_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setClanId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getClanId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Clan (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setClanName($this->getClanName());
        $copyObj->setClanCreatedDate($this->getClanCreatedDate());
        $copyObj->setClanFounder($this->getClanFounder());
        $copyObj->setClanAvatarUrl($this->getClanAvatarUrl());
        $copyObj->setDescription($this->getDescription());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getClanPlayers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClanPlayer($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setClanId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Clan Clone of current object.
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
     * @return ClanPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ClanPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('ClanPlayer' == $relationName) {
            $this->initClanPlayers();
        }
    }

    /**
     * Clears out the collClanPlayers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Clan The current object (for fluent API support)
     * @see        addClanPlayers()
     */
    public function clearClanPlayers()
    {
        $this->collClanPlayers = null; // important to set this to null since that means it is uninitialized
        $this->collClanPlayersPartial = null;

        return $this;
    }

    /**
     * reset is the collClanPlayers collection loaded partially
     *
     * @return void
     */
    public function resetPartialClanPlayers($v = true)
    {
        $this->collClanPlayersPartial = $v;
    }

    /**
     * Initializes the collClanPlayers collection.
     *
     * By default this just sets the collClanPlayers collection to an empty array (like clearcollClanPlayers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClanPlayers($overrideExisting = true)
    {
        if (null !== $this->collClanPlayers && !$overrideExisting) {
            return;
        }
        $this->collClanPlayers = new PropelObjectCollection();
        $this->collClanPlayers->setModel('ClanPlayer');
    }

    /**
     * Gets an array of ClanPlayer objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Clan is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ClanPlayer[] List of ClanPlayer objects
     * @throws PropelException
     */
    public function getClanPlayers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collClanPlayersPartial && !$this->isNew();
        if (null === $this->collClanPlayers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClanPlayers) {
                // return empty collection
                $this->initClanPlayers();
            } else {
                $collClanPlayers = ClanPlayerQuery::create(null, $criteria)
                    ->filterByClan($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collClanPlayersPartial && count($collClanPlayers)) {
                      $this->initClanPlayers(false);

                      foreach ($collClanPlayers as $obj) {
                        if (false == $this->collClanPlayers->contains($obj)) {
                          $this->collClanPlayers->append($obj);
                        }
                      }

                      $this->collClanPlayersPartial = true;
                    }

                    $collClanPlayers->getInternalIterator()->rewind();

                    return $collClanPlayers;
                }

                if ($partial && $this->collClanPlayers) {
                    foreach ($this->collClanPlayers as $obj) {
                        if ($obj->isNew()) {
                            $collClanPlayers[] = $obj;
                        }
                    }
                }

                $this->collClanPlayers = $collClanPlayers;
                $this->collClanPlayersPartial = false;
            }
        }

        return $this->collClanPlayers;
    }

    /**
     * Sets a collection of ClanPlayer objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $clanPlayers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Clan The current object (for fluent API support)
     */
    public function setClanPlayers(PropelCollection $clanPlayers, PropelPDO $con = null)
    {
        $clanPlayersToDelete = $this->getClanPlayers(new Criteria(), $con)->diff($clanPlayers);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->clanPlayersScheduledForDeletion = clone $clanPlayersToDelete;

        foreach ($clanPlayersToDelete as $clanPlayerRemoved) {
            $clanPlayerRemoved->setClan(null);
        }

        $this->collClanPlayers = null;
        foreach ($clanPlayers as $clanPlayer) {
            $this->addClanPlayer($clanPlayer);
        }

        $this->collClanPlayers = $clanPlayers;
        $this->collClanPlayersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ClanPlayer objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ClanPlayer objects.
     * @throws PropelException
     */
    public function countClanPlayers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collClanPlayersPartial && !$this->isNew();
        if (null === $this->collClanPlayers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClanPlayers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClanPlayers());
            }
            $query = ClanPlayerQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClan($this)
                ->count($con);
        }

        return count($this->collClanPlayers);
    }

    /**
     * Method called to associate a ClanPlayer object to this object
     * through the ClanPlayer foreign key attribute.
     *
     * @param    ClanPlayer $l ClanPlayer
     * @return Clan The current object (for fluent API support)
     */
    public function addClanPlayer(ClanPlayer $l)
    {
        if ($this->collClanPlayers === null) {
            $this->initClanPlayers();
            $this->collClanPlayersPartial = true;
        }
        if (!in_array($l, $this->collClanPlayers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClanPlayer($l);
        }

        return $this;
    }

    /**
     * @param	ClanPlayer $clanPlayer The clanPlayer object to add.
     */
    protected function doAddClanPlayer($clanPlayer)
    {
        $this->collClanPlayers[]= $clanPlayer;
        $clanPlayer->setClan($this);
    }

    /**
     * @param	ClanPlayer $clanPlayer The clanPlayer object to remove.
     * @return Clan The current object (for fluent API support)
     */
    public function removeClanPlayer($clanPlayer)
    {
        if ($this->getClanPlayers()->contains($clanPlayer)) {
            $this->collClanPlayers->remove($this->collClanPlayers->search($clanPlayer));
            if (null === $this->clanPlayersScheduledForDeletion) {
                $this->clanPlayersScheduledForDeletion = clone $this->collClanPlayers;
                $this->clanPlayersScheduledForDeletion->clear();
            }
            $this->clanPlayersScheduledForDeletion[]= clone $clanPlayer;
            $clanPlayer->setClan(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Clan is new, it will return
     * an empty collection; or if this Clan has previously
     * been saved, it will retrieve related ClanPlayers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Clan.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ClanPlayer[] List of ClanPlayer objects
     */
    public function getClanPlayersJoinPlayers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ClanPlayerQuery::create(null, $criteria);
        $query->joinWith('Players', $join_behavior);

        return $this->getClanPlayers($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->clan_id = null;
        $this->clan_name = null;
        $this->clan_created_date = null;
        $this->clan_founder = null;
        $this->clan_avatar_url = null;
        $this->description = null;
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
            if ($this->collClanPlayers) {
                foreach ($this->collClanPlayers as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collClanPlayers instanceof PropelCollection) {
            $this->collClanPlayers->clearIterator();
        }
        $this->collClanPlayers = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ClanPeer::DEFAULT_STRING_FORMAT);
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
