<?php

namespace deploy\model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelException;
use \PropelPDO;
use deploy\model\Enemies;
use deploy\model\EnemiesPeer;
use deploy\model\EnemiesQuery;
use deploy\model\Players;
use deploy\model\PlayersQuery;

/**
 * Base class that represents a row from the 'enemies' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseEnemies extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\EnemiesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        EnemiesPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the _player_id field.
     * @var        int
     */
    protected $_player_id;

    /**
     * The value for the _enemy_id field.
     * @var        int
     */
    protected $_enemy_id;

    /**
     * @var        Players
     */
    protected $aPlayersRelatedByEnemyId;

    /**
     * @var        Players
     */
    protected $aPlayersRelatedByPlayerId;

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
     * Get the [_player_id] column value.
     *
     * @return int
     */
    public function getPlayerId()
    {

        return $this->_player_id;
    }

    /**
     * Get the [_enemy_id] column value.
     *
     * @return int
     */
    public function getEnemyId()
    {

        return $this->_enemy_id;
    }

    /**
     * Set the value of [_player_id] column.
     *
     * @param  int $v new value
     * @return Enemies The current object (for fluent API support)
     */
    public function setPlayerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->_player_id !== $v) {
            $this->_player_id = $v;
            $this->modifiedColumns[] = EnemiesPeer::_PLAYER_ID;
        }

        if ($this->aPlayersRelatedByPlayerId !== null && $this->aPlayersRelatedByPlayerId->getPlayerId() !== $v) {
            $this->aPlayersRelatedByPlayerId = null;
        }


        return $this;
    } // setPlayerId()

    /**
     * Set the value of [_enemy_id] column.
     *
     * @param  int $v new value
     * @return Enemies The current object (for fluent API support)
     */
    public function setEnemyId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->_enemy_id !== $v) {
            $this->_enemy_id = $v;
            $this->modifiedColumns[] = EnemiesPeer::_ENEMY_ID;
        }

        if ($this->aPlayersRelatedByEnemyId !== null && $this->aPlayersRelatedByEnemyId->getPlayerId() !== $v) {
            $this->aPlayersRelatedByEnemyId = null;
        }


        return $this;
    } // setEnemyId()

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

            $this->_player_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->_enemy_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 2; // 2 = EnemiesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Enemies object", $e);
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

        if ($this->aPlayersRelatedByPlayerId !== null && $this->_player_id !== $this->aPlayersRelatedByPlayerId->getPlayerId()) {
            $this->aPlayersRelatedByPlayerId = null;
        }
        if ($this->aPlayersRelatedByEnemyId !== null && $this->_enemy_id !== $this->aPlayersRelatedByEnemyId->getPlayerId()) {
            $this->aPlayersRelatedByEnemyId = null;
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
            $con = Propel::getConnection(EnemiesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = EnemiesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPlayersRelatedByEnemyId = null;
            $this->aPlayersRelatedByPlayerId = null;
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
            $con = Propel::getConnection(EnemiesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = EnemiesQuery::create()
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
            $con = Propel::getConnection(EnemiesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                EnemiesPeer::addInstanceToPool($this);
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

            if ($this->aPlayersRelatedByEnemyId !== null) {
                if ($this->aPlayersRelatedByEnemyId->isModified() || $this->aPlayersRelatedByEnemyId->isNew()) {
                    $affectedRows += $this->aPlayersRelatedByEnemyId->save($con);
                }
                $this->setPlayersRelatedByEnemyId($this->aPlayersRelatedByEnemyId);
            }

            if ($this->aPlayersRelatedByPlayerId !== null) {
                if ($this->aPlayersRelatedByPlayerId->isModified() || $this->aPlayersRelatedByPlayerId->isNew()) {
                    $affectedRows += $this->aPlayersRelatedByPlayerId->save($con);
                }
                $this->setPlayersRelatedByPlayerId($this->aPlayersRelatedByPlayerId);
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(EnemiesPeer::_PLAYER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"_player_id"';
        }
        if ($this->isColumnModified(EnemiesPeer::_ENEMY_ID)) {
            $modifiedColumns[':p' . $index++]  = '"_enemy_id"';
        }

        $sql = sprintf(
            'INSERT INTO "enemies" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"_player_id"':
                        $stmt->bindValue($identifier, $this->_player_id, PDO::PARAM_INT);
                        break;
                    case '"_enemy_id"':
                        $stmt->bindValue($identifier, $this->_enemy_id, PDO::PARAM_INT);
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

            if ($this->aPlayersRelatedByEnemyId !== null) {
                if (!$this->aPlayersRelatedByEnemyId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayersRelatedByEnemyId->getValidationFailures());
                }
            }

            if ($this->aPlayersRelatedByPlayerId !== null) {
                if (!$this->aPlayersRelatedByPlayerId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayersRelatedByPlayerId->getValidationFailures());
                }
            }


            if (($retval = EnemiesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = EnemiesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPlayerId();
                break;
            case 1:
                return $this->getEnemyId();
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
        if (isset($alreadyDumpedObjects['Enemies'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Enemies'][serialize($this->getPrimaryKey())] = true;
        $keys = EnemiesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getPlayerId(),
            $keys[1] => $this->getEnemyId(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aPlayersRelatedByEnemyId) {
                $result['PlayersRelatedByEnemyId'] = $this->aPlayersRelatedByEnemyId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayersRelatedByPlayerId) {
                $result['PlayersRelatedByPlayerId'] = $this->aPlayersRelatedByPlayerId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = EnemiesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPlayerId($value);
                break;
            case 1:
                $this->setEnemyId($value);
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
        $keys = EnemiesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setPlayerId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setEnemyId($arr[$keys[1]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(EnemiesPeer::DATABASE_NAME);

        if ($this->isColumnModified(EnemiesPeer::_PLAYER_ID)) $criteria->add(EnemiesPeer::_PLAYER_ID, $this->_player_id);
        if ($this->isColumnModified(EnemiesPeer::_ENEMY_ID)) $criteria->add(EnemiesPeer::_ENEMY_ID, $this->_enemy_id);

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
        $criteria = new Criteria(EnemiesPeer::DATABASE_NAME);
        $criteria->add(EnemiesPeer::_PLAYER_ID, $this->_player_id);
        $criteria->add(EnemiesPeer::_ENEMY_ID, $this->_enemy_id);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return array
     */
    public function getPrimaryKey()
    {
        $pks = array();
        $pks[0] = $this->getPlayerId();
        $pks[1] = $this->getEnemyId();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param array $keys The elements of the composite key (order must match the order in XML file).
     * @return void
     */
    public function setPrimaryKey($keys)
    {
        $this->setPlayerId($keys[0]);
        $this->setEnemyId($keys[1]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getPlayerId()) && (null === $this->getEnemyId());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Enemies (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPlayerId($this->getPlayerId());
        $copyObj->setEnemyId($this->getEnemyId());

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
     * @return Enemies Clone of current object.
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
     * @return EnemiesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new EnemiesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Players object.
     *
     * @param                  Players $v
     * @return Enemies The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayersRelatedByEnemyId(Players $v = null)
    {
        if ($v === null) {
            $this->setEnemyId(NULL);
        } else {
            $this->setEnemyId($v->getPlayerId());
        }

        $this->aPlayersRelatedByEnemyId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Players object, it will not be re-added.
        if ($v !== null) {
            $v->addEnemiesRelatedByEnemyId($this);
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
    public function getPlayersRelatedByEnemyId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayersRelatedByEnemyId === null && ($this->_enemy_id !== null) && $doQuery) {
            $this->aPlayersRelatedByEnemyId = PlayersQuery::create()->findPk($this->_enemy_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayersRelatedByEnemyId->addEnemiessRelatedByEnemyId($this);
             */
        }

        return $this->aPlayersRelatedByEnemyId;
    }

    /**
     * Declares an association between this object and a Players object.
     *
     * @param                  Players $v
     * @return Enemies The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayersRelatedByPlayerId(Players $v = null)
    {
        if ($v === null) {
            $this->setPlayerId(NULL);
        } else {
            $this->setPlayerId($v->getPlayerId());
        }

        $this->aPlayersRelatedByPlayerId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Players object, it will not be re-added.
        if ($v !== null) {
            $v->addEnemiesRelatedByPlayerId($this);
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
    public function getPlayersRelatedByPlayerId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayersRelatedByPlayerId === null && ($this->_player_id !== null) && $doQuery) {
            $this->aPlayersRelatedByPlayerId = PlayersQuery::create()->findPk($this->_player_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayersRelatedByPlayerId->addEnemiessRelatedByPlayerId($this);
             */
        }

        return $this->aPlayersRelatedByPlayerId;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->_player_id = null;
        $this->_enemy_id = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
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
            if ($this->aPlayersRelatedByEnemyId instanceof Persistent) {
              $this->aPlayersRelatedByEnemyId->clearAllReferences($deep);
            }
            if ($this->aPlayersRelatedByPlayerId instanceof Persistent) {
              $this->aPlayersRelatedByPlayerId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aPlayersRelatedByEnemyId = null;
        $this->aPlayersRelatedByPlayerId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(EnemiesPeer::DEFAULT_STRING_FORMAT);
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
