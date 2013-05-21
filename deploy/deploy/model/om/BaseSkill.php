<?php

namespace deploy\model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use deploy\model\ClassSkill;
use deploy\model\ClassSkillQuery;
use deploy\model\Skill;
use deploy\model\SkillPeer;
use deploy\model\SkillQuery;

/**
 * Base class that represents a row from the 'skill' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseSkill extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\SkillPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SkillPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the skill_id field.
     * @var        int
     */
    protected $skill_id;

    /**
     * The value for the skill_level field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $skill_level;

    /**
     * The value for the skill_is_active field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $skill_is_active;

    /**
     * The value for the skill_display_name field.
     * @var        string
     */
    protected $skill_display_name;

    /**
     * The value for the skill_internal_name field.
     * @var        string
     */
    protected $skill_internal_name;

    /**
     * The value for the skill_type field.
     * @var        string
     */
    protected $skill_type;

    /**
     * @var        PropelObjectCollection|ClassSkill[] Collection to store aggregation of ClassSkill objects.
     */
    protected $collClassSkills;
    protected $collClassSkillsPartial;

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
    protected $classSkillsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->skill_level = 1;
        $this->skill_is_active = true;
    }

    /**
     * Initializes internal state of BaseSkill object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [skill_id] column value.
     *
     * @return int
     */
    public function getSkillId()
    {

        return $this->skill_id;
    }

    /**
     * Get the [skill_level] column value.
     *
     * @return int
     */
    public function getSkillLevel()
    {

        return $this->skill_level;
    }

    /**
     * Get the [skill_is_active] column value.
     *
     * @return boolean
     */
    public function getSkillIsActive()
    {

        return $this->skill_is_active;
    }

    /**
     * Get the [skill_display_name] column value.
     *
     * @return string
     */
    public function getSkillDisplayName()
    {

        return $this->skill_display_name;
    }

    /**
     * Get the [skill_internal_name] column value.
     *
     * @return string
     */
    public function getSkillInternalName()
    {

        return $this->skill_internal_name;
    }

    /**
     * Get the [skill_type] column value.
     *
     * @return string
     */
    public function getSkillType()
    {

        return $this->skill_type;
    }

    /**
     * Set the value of [skill_id] column.
     *
     * @param  int $v new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->skill_id !== $v) {
            $this->skill_id = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_ID;
        }


        return $this;
    } // setSkillId()

    /**
     * Set the value of [skill_level] column.
     *
     * @param  int $v new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillLevel($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->skill_level !== $v) {
            $this->skill_level = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_LEVEL;
        }


        return $this;
    } // setSkillLevel()

    /**
     * Sets the value of the [skill_is_active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillIsActive($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->skill_is_active !== $v) {
            $this->skill_is_active = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_IS_ACTIVE;
        }


        return $this;
    } // setSkillIsActive()

    /**
     * Set the value of [skill_display_name] column.
     *
     * @param  string $v new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillDisplayName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->skill_display_name !== $v) {
            $this->skill_display_name = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_DISPLAY_NAME;
        }


        return $this;
    } // setSkillDisplayName()

    /**
     * Set the value of [skill_internal_name] column.
     *
     * @param  string $v new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillInternalName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->skill_internal_name !== $v) {
            $this->skill_internal_name = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_INTERNAL_NAME;
        }


        return $this;
    } // setSkillInternalName()

    /**
     * Set the value of [skill_type] column.
     *
     * @param  string $v new value
     * @return Skill The current object (for fluent API support)
     */
    public function setSkillType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->skill_type !== $v) {
            $this->skill_type = $v;
            $this->modifiedColumns[] = SkillPeer::SKILL_TYPE;
        }


        return $this;
    } // setSkillType()

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
            if ($this->skill_level !== 1) {
                return false;
            }

            if ($this->skill_is_active !== true) {
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

            $this->skill_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->skill_level = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->skill_is_active = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
            $this->skill_display_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->skill_internal_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->skill_type = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = SkillPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Skill object", $e);
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
            $con = Propel::getConnection(SkillPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = SkillPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collClassSkills = null;

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
            $con = Propel::getConnection(SkillPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = SkillQuery::create()
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
            $con = Propel::getConnection(SkillPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                SkillPeer::addInstanceToPool($this);
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

            if ($this->classSkillsScheduledForDeletion !== null) {
                if (!$this->classSkillsScheduledForDeletion->isEmpty()) {
                    ClassSkillQuery::create()
                        ->filterByPrimaryKeys($this->classSkillsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->classSkillsScheduledForDeletion = null;
                }
            }

            if ($this->collClassSkills !== null) {
                foreach ($this->collClassSkills as $referrerFK) {
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

        $this->modifiedColumns[] = SkillPeer::SKILL_ID;
        if (null !== $this->skill_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SkillPeer::SKILL_ID . ')');
        }
        if (null === $this->skill_id) {
            try {
                $stmt = $con->query("SELECT nextval('skill_skill_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->skill_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SkillPeer::SKILL_ID)) {
            $modifiedColumns[':p' . $index++]  = '"skill_id"';
        }
        if ($this->isColumnModified(SkillPeer::SKILL_LEVEL)) {
            $modifiedColumns[':p' . $index++]  = '"skill_level"';
        }
        if ($this->isColumnModified(SkillPeer::SKILL_IS_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '"skill_is_active"';
        }
        if ($this->isColumnModified(SkillPeer::SKILL_DISPLAY_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"skill_display_name"';
        }
        if ($this->isColumnModified(SkillPeer::SKILL_INTERNAL_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"skill_internal_name"';
        }
        if ($this->isColumnModified(SkillPeer::SKILL_TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"skill_type"';
        }

        $sql = sprintf(
            'INSERT INTO "skill" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"skill_id"':
                        $stmt->bindValue($identifier, $this->skill_id, PDO::PARAM_INT);
                        break;
                    case '"skill_level"':
                        $stmt->bindValue($identifier, $this->skill_level, PDO::PARAM_INT);
                        break;
                    case '"skill_is_active"':
                        $stmt->bindValue($identifier, $this->skill_is_active, PDO::PARAM_BOOL);
                        break;
                    case '"skill_display_name"':
                        $stmt->bindValue($identifier, $this->skill_display_name, PDO::PARAM_STR);
                        break;
                    case '"skill_internal_name"':
                        $stmt->bindValue($identifier, $this->skill_internal_name, PDO::PARAM_STR);
                        break;
                    case '"skill_type"':
                        $stmt->bindValue($identifier, $this->skill_type, PDO::PARAM_STR);
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


            if (($retval = SkillPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collClassSkills !== null) {
                    foreach ($this->collClassSkills as $referrerFK) {
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
        $pos = SkillPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSkillId();
                break;
            case 1:
                return $this->getSkillLevel();
                break;
            case 2:
                return $this->getSkillIsActive();
                break;
            case 3:
                return $this->getSkillDisplayName();
                break;
            case 4:
                return $this->getSkillInternalName();
                break;
            case 5:
                return $this->getSkillType();
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
        if (isset($alreadyDumpedObjects['Skill'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Skill'][$this->getPrimaryKey()] = true;
        $keys = SkillPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSkillId(),
            $keys[1] => $this->getSkillLevel(),
            $keys[2] => $this->getSkillIsActive(),
            $keys[3] => $this->getSkillDisplayName(),
            $keys[4] => $this->getSkillInternalName(),
            $keys[5] => $this->getSkillType(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collClassSkills) {
                $result['ClassSkills'] = $this->collClassSkills->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = SkillPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setSkillId($value);
                break;
            case 1:
                $this->setSkillLevel($value);
                break;
            case 2:
                $this->setSkillIsActive($value);
                break;
            case 3:
                $this->setSkillDisplayName($value);
                break;
            case 4:
                $this->setSkillInternalName($value);
                break;
            case 5:
                $this->setSkillType($value);
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
        $keys = SkillPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setSkillId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSkillLevel($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setSkillIsActive($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSkillDisplayName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setSkillInternalName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setSkillType($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SkillPeer::DATABASE_NAME);

        if ($this->isColumnModified(SkillPeer::SKILL_ID)) $criteria->add(SkillPeer::SKILL_ID, $this->skill_id);
        if ($this->isColumnModified(SkillPeer::SKILL_LEVEL)) $criteria->add(SkillPeer::SKILL_LEVEL, $this->skill_level);
        if ($this->isColumnModified(SkillPeer::SKILL_IS_ACTIVE)) $criteria->add(SkillPeer::SKILL_IS_ACTIVE, $this->skill_is_active);
        if ($this->isColumnModified(SkillPeer::SKILL_DISPLAY_NAME)) $criteria->add(SkillPeer::SKILL_DISPLAY_NAME, $this->skill_display_name);
        if ($this->isColumnModified(SkillPeer::SKILL_INTERNAL_NAME)) $criteria->add(SkillPeer::SKILL_INTERNAL_NAME, $this->skill_internal_name);
        if ($this->isColumnModified(SkillPeer::SKILL_TYPE)) $criteria->add(SkillPeer::SKILL_TYPE, $this->skill_type);

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
        $criteria = new Criteria(SkillPeer::DATABASE_NAME);
        $criteria->add(SkillPeer::SKILL_ID, $this->skill_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getSkillId();
    }

    /**
     * Generic method to set the primary key (skill_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setSkillId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getSkillId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Skill (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSkillLevel($this->getSkillLevel());
        $copyObj->setSkillIsActive($this->getSkillIsActive());
        $copyObj->setSkillDisplayName($this->getSkillDisplayName());
        $copyObj->setSkillInternalName($this->getSkillInternalName());
        $copyObj->setSkillType($this->getSkillType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getClassSkills() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClassSkill($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setSkillId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Skill Clone of current object.
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
     * @return SkillPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SkillPeer();
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
        if ('ClassSkill' == $relationName) {
            $this->initClassSkills();
        }
    }

    /**
     * Clears out the collClassSkills collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Skill The current object (for fluent API support)
     * @see        addClassSkills()
     */
    public function clearClassSkills()
    {
        $this->collClassSkills = null; // important to set this to null since that means it is uninitialized
        $this->collClassSkillsPartial = null;

        return $this;
    }

    /**
     * reset is the collClassSkills collection loaded partially
     *
     * @return void
     */
    public function resetPartialClassSkills($v = true)
    {
        $this->collClassSkillsPartial = $v;
    }

    /**
     * Initializes the collClassSkills collection.
     *
     * By default this just sets the collClassSkills collection to an empty array (like clearcollClassSkills());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClassSkills($overrideExisting = true)
    {
        if (null !== $this->collClassSkills && !$overrideExisting) {
            return;
        }
        $this->collClassSkills = new PropelObjectCollection();
        $this->collClassSkills->setModel('ClassSkill');
    }

    /**
     * Gets an array of ClassSkill objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Skill is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ClassSkill[] List of ClassSkill objects
     * @throws PropelException
     */
    public function getClassSkills($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collClassSkillsPartial && !$this->isNew();
        if (null === $this->collClassSkills || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClassSkills) {
                // return empty collection
                $this->initClassSkills();
            } else {
                $collClassSkills = ClassSkillQuery::create(null, $criteria)
                    ->filterBySkill($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collClassSkillsPartial && count($collClassSkills)) {
                      $this->initClassSkills(false);

                      foreach ($collClassSkills as $obj) {
                        if (false == $this->collClassSkills->contains($obj)) {
                          $this->collClassSkills->append($obj);
                        }
                      }

                      $this->collClassSkillsPartial = true;
                    }

                    $collClassSkills->getInternalIterator()->rewind();

                    return $collClassSkills;
                }

                if ($partial && $this->collClassSkills) {
                    foreach ($this->collClassSkills as $obj) {
                        if ($obj->isNew()) {
                            $collClassSkills[] = $obj;
                        }
                    }
                }

                $this->collClassSkills = $collClassSkills;
                $this->collClassSkillsPartial = false;
            }
        }

        return $this->collClassSkills;
    }

    /**
     * Sets a collection of ClassSkill objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $classSkills A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Skill The current object (for fluent API support)
     */
    public function setClassSkills(PropelCollection $classSkills, PropelPDO $con = null)
    {
        $classSkillsToDelete = $this->getClassSkills(new Criteria(), $con)->diff($classSkills);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->classSkillsScheduledForDeletion = clone $classSkillsToDelete;

        foreach ($classSkillsToDelete as $classSkillRemoved) {
            $classSkillRemoved->setSkill(null);
        }

        $this->collClassSkills = null;
        foreach ($classSkills as $classSkill) {
            $this->addClassSkill($classSkill);
        }

        $this->collClassSkills = $classSkills;
        $this->collClassSkillsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ClassSkill objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ClassSkill objects.
     * @throws PropelException
     */
    public function countClassSkills(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collClassSkillsPartial && !$this->isNew();
        if (null === $this->collClassSkills || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClassSkills) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClassSkills());
            }
            $query = ClassSkillQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySkill($this)
                ->count($con);
        }

        return count($this->collClassSkills);
    }

    /**
     * Method called to associate a ClassSkill object to this object
     * through the ClassSkill foreign key attribute.
     *
     * @param    ClassSkill $l ClassSkill
     * @return Skill The current object (for fluent API support)
     */
    public function addClassSkill(ClassSkill $l)
    {
        if ($this->collClassSkills === null) {
            $this->initClassSkills();
            $this->collClassSkillsPartial = true;
        }
        if (!in_array($l, $this->collClassSkills->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClassSkill($l);
        }

        return $this;
    }

    /**
     * @param	ClassSkill $classSkill The classSkill object to add.
     */
    protected function doAddClassSkill($classSkill)
    {
        $this->collClassSkills[]= $classSkill;
        $classSkill->setSkill($this);
    }

    /**
     * @param	ClassSkill $classSkill The classSkill object to remove.
     * @return Skill The current object (for fluent API support)
     */
    public function removeClassSkill($classSkill)
    {
        if ($this->getClassSkills()->contains($classSkill)) {
            $this->collClassSkills->remove($this->collClassSkills->search($classSkill));
            if (null === $this->classSkillsScheduledForDeletion) {
                $this->classSkillsScheduledForDeletion = clone $this->collClassSkills;
                $this->classSkillsScheduledForDeletion->clear();
            }
            $this->classSkillsScheduledForDeletion[]= clone $classSkill;
            $classSkill->setSkill(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Skill is new, it will return
     * an empty collection; or if this Skill has previously
     * been saved, it will retrieve related ClassSkills from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Skill.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ClassSkill[] List of ClassSkill objects
     */
    public function getClassSkillsJoinClass($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ClassSkillQuery::create(null, $criteria);
        $query->joinWith('Class', $join_behavior);

        return $this->getClassSkills($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->skill_id = null;
        $this->skill_level = null;
        $this->skill_is_active = null;
        $this->skill_display_name = null;
        $this->skill_internal_name = null;
        $this->skill_type = null;
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
            if ($this->collClassSkills) {
                foreach ($this->collClassSkills as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collClassSkills instanceof PropelCollection) {
            $this->collClassSkills->clearIterator();
        }
        $this->collClassSkills = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SkillPeer::DEFAULT_STRING_FORMAT);
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
