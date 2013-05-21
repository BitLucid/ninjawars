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
use deploy\model\Item;
use deploy\model\ItemEffects;
use deploy\model\ItemEffectsQuery;
use deploy\model\ItemPeer;
use deploy\model\ItemQuery;

/**
 * Base class that represents a row from the 'item' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseItem extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\ItemPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ItemPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the item_id field.
     * @var        int
     */
    protected $item_id;

    /**
     * The value for the item_internal_name field.
     * @var        string
     */
    protected $item_internal_name;

    /**
     * The value for the item_display_name field.
     * @var        string
     */
    protected $item_display_name;

    /**
     * The value for the item_cost field.
     * @var        string
     */
    protected $item_cost;

    /**
     * The value for the image field.
     * @var        string
     */
    protected $image;

    /**
     * The value for the for_sale field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $for_sale;

    /**
     * The value for the usage field.
     * @var        string
     */
    protected $usage;

    /**
     * The value for the ignore_stealth field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $ignore_stealth;

    /**
     * The value for the covert field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $covert;

    /**
     * The value for the turn_cost field.
     * @var        int
     */
    protected $turn_cost;

    /**
     * The value for the target_damage field.
     * @var        int
     */
    protected $target_damage;

    /**
     * The value for the turn_change field.
     * @var        int
     */
    protected $turn_change;

    /**
     * The value for the self_use field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $self_use;

    /**
     * The value for the plural field.
     * @var        string
     */
    protected $plural;

    /**
     * The value for the other_usable field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $other_usable;

    /**
     * The value for the traits field.
     * @var        string
     */
    protected $traits;

    /**
     * @var        PropelObjectCollection|ItemEffects[] Collection to store aggregation of ItemEffects objects.
     */
    protected $collItemEffectss;
    protected $collItemEffectssPartial;

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
    protected $itemEffectssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->for_sale = false;
        $this->ignore_stealth = false;
        $this->covert = false;
        $this->self_use = false;
        $this->other_usable = false;
    }

    /**
     * Initializes internal state of BaseItem object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [item_id] column value.
     *
     * @return int
     */
    public function getItemId()
    {

        return $this->item_id;
    }

    /**
     * Get the [item_internal_name] column value.
     *
     * @return string
     */
    public function getItemInternalName()
    {

        return $this->item_internal_name;
    }

    /**
     * Get the [item_display_name] column value.
     *
     * @return string
     */
    public function getItemDisplayName()
    {

        return $this->item_display_name;
    }

    /**
     * Get the [item_cost] column value.
     *
     * @return string
     */
    public function getItemCost()
    {

        return $this->item_cost;
    }

    /**
     * Get the [image] column value.
     *
     * @return string
     */
    public function getImage()
    {

        return $this->image;
    }

    /**
     * Get the [for_sale] column value.
     *
     * @return boolean
     */
    public function getForSale()
    {

        return $this->for_sale;
    }

    /**
     * Get the [usage] column value.
     *
     * @return string
     */
    public function getUsage()
    {

        return $this->usage;
    }

    /**
     * Get the [ignore_stealth] column value.
     *
     * @return boolean
     */
    public function getIgnoreStealth()
    {

        return $this->ignore_stealth;
    }

    /**
     * Get the [covert] column value.
     *
     * @return boolean
     */
    public function getCovert()
    {

        return $this->covert;
    }

    /**
     * Get the [turn_cost] column value.
     *
     * @return int
     */
    public function getTurnCost()
    {

        return $this->turn_cost;
    }

    /**
     * Get the [target_damage] column value.
     *
     * @return int
     */
    public function getTargetDamage()
    {

        return $this->target_damage;
    }

    /**
     * Get the [turn_change] column value.
     *
     * @return int
     */
    public function getTurnChange()
    {

        return $this->turn_change;
    }

    /**
     * Get the [self_use] column value.
     *
     * @return boolean
     */
    public function getSelfUse()
    {

        return $this->self_use;
    }

    /**
     * Get the [plural] column value.
     *
     * @return string
     */
    public function getPlural()
    {

        return $this->plural;
    }

    /**
     * Get the [other_usable] column value.
     *
     * @return boolean
     */
    public function getOtherUsable()
    {

        return $this->other_usable;
    }

    /**
     * Get the [traits] column value.
     *
     * @return string
     */
    public function getTraits()
    {

        return $this->traits;
    }

    /**
     * Set the value of [item_id] column.
     *
     * @param  int $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setItemId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->item_id !== $v) {
            $this->item_id = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_ID;
        }


        return $this;
    } // setItemId()

    /**
     * Set the value of [item_internal_name] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setItemInternalName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->item_internal_name !== $v) {
            $this->item_internal_name = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_INTERNAL_NAME;
        }


        return $this;
    } // setItemInternalName()

    /**
     * Set the value of [item_display_name] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setItemDisplayName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->item_display_name !== $v) {
            $this->item_display_name = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_DISPLAY_NAME;
        }


        return $this;
    } // setItemDisplayName()

    /**
     * Set the value of [item_cost] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setItemCost($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->item_cost !== $v) {
            $this->item_cost = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_COST;
        }


        return $this;
    } // setItemCost()

    /**
     * Set the value of [image] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setImage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->image !== $v) {
            $this->image = $v;
            $this->modifiedColumns[] = ItemPeer::IMAGE;
        }


        return $this;
    } // setImage()

    /**
     * Sets the value of the [for_sale] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Item The current object (for fluent API support)
     */
    public function setForSale($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->for_sale !== $v) {
            $this->for_sale = $v;
            $this->modifiedColumns[] = ItemPeer::FOR_SALE;
        }


        return $this;
    } // setForSale()

    /**
     * Set the value of [usage] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setUsage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->usage !== $v) {
            $this->usage = $v;
            $this->modifiedColumns[] = ItemPeer::USAGE;
        }


        return $this;
    } // setUsage()

    /**
     * Sets the value of the [ignore_stealth] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Item The current object (for fluent API support)
     */
    public function setIgnoreStealth($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->ignore_stealth !== $v) {
            $this->ignore_stealth = $v;
            $this->modifiedColumns[] = ItemPeer::IGNORE_STEALTH;
        }


        return $this;
    } // setIgnoreStealth()

    /**
     * Sets the value of the [covert] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Item The current object (for fluent API support)
     */
    public function setCovert($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->covert !== $v) {
            $this->covert = $v;
            $this->modifiedColumns[] = ItemPeer::COVERT;
        }


        return $this;
    } // setCovert()

    /**
     * Set the value of [turn_cost] column.
     *
     * @param  int $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setTurnCost($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->turn_cost !== $v) {
            $this->turn_cost = $v;
            $this->modifiedColumns[] = ItemPeer::TURN_COST;
        }


        return $this;
    } // setTurnCost()

    /**
     * Set the value of [target_damage] column.
     *
     * @param  int $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setTargetDamage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->target_damage !== $v) {
            $this->target_damage = $v;
            $this->modifiedColumns[] = ItemPeer::TARGET_DAMAGE;
        }


        return $this;
    } // setTargetDamage()

    /**
     * Set the value of [turn_change] column.
     *
     * @param  int $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setTurnChange($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->turn_change !== $v) {
            $this->turn_change = $v;
            $this->modifiedColumns[] = ItemPeer::TURN_CHANGE;
        }


        return $this;
    } // setTurnChange()

    /**
     * Sets the value of the [self_use] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Item The current object (for fluent API support)
     */
    public function setSelfUse($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->self_use !== $v) {
            $this->self_use = $v;
            $this->modifiedColumns[] = ItemPeer::SELF_USE;
        }


        return $this;
    } // setSelfUse()

    /**
     * Set the value of [plural] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setPlural($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->plural !== $v) {
            $this->plural = $v;
            $this->modifiedColumns[] = ItemPeer::PLURAL;
        }


        return $this;
    } // setPlural()

    /**
     * Sets the value of the [other_usable] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Item The current object (for fluent API support)
     */
    public function setOtherUsable($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->other_usable !== $v) {
            $this->other_usable = $v;
            $this->modifiedColumns[] = ItemPeer::OTHER_USABLE;
        }


        return $this;
    } // setOtherUsable()

    /**
     * Set the value of [traits] column.
     *
     * @param  string $v new value
     * @return Item The current object (for fluent API support)
     */
    public function setTraits($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->traits !== $v) {
            $this->traits = $v;
            $this->modifiedColumns[] = ItemPeer::TRAITS;
        }


        return $this;
    } // setTraits()

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
            if ($this->for_sale !== false) {
                return false;
            }

            if ($this->ignore_stealth !== false) {
                return false;
            }

            if ($this->covert !== false) {
                return false;
            }

            if ($this->self_use !== false) {
                return false;
            }

            if ($this->other_usable !== false) {
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

            $this->item_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->item_internal_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->item_display_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->item_cost = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->image = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->for_sale = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->usage = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->ignore_stealth = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->covert = ($row[$startcol + 8] !== null) ? (boolean) $row[$startcol + 8] : null;
            $this->turn_cost = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->target_damage = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->turn_change = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->self_use = ($row[$startcol + 12] !== null) ? (boolean) $row[$startcol + 12] : null;
            $this->plural = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->other_usable = ($row[$startcol + 14] !== null) ? (boolean) $row[$startcol + 14] : null;
            $this->traits = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 16; // 16 = ItemPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Item object", $e);
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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ItemPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collItemEffectss = null;

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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ItemQuery::create()
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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ItemPeer::addInstanceToPool($this);
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

            if ($this->itemEffectssScheduledForDeletion !== null) {
                if (!$this->itemEffectssScheduledForDeletion->isEmpty()) {
                    ItemEffectsQuery::create()
                        ->filterByPrimaryKeys($this->itemEffectssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->itemEffectssScheduledForDeletion = null;
                }
            }

            if ($this->collItemEffectss !== null) {
                foreach ($this->collItemEffectss as $referrerFK) {
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

        $this->modifiedColumns[] = ItemPeer::ITEM_ID;
        if (null !== $this->item_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ItemPeer::ITEM_ID . ')');
        }
        if (null === $this->item_id) {
            try {
                $stmt = $con->query("SELECT nextval('item_item_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->item_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ItemPeer::ITEM_ID)) {
            $modifiedColumns[':p' . $index++]  = '"item_id"';
        }
        if ($this->isColumnModified(ItemPeer::ITEM_INTERNAL_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"item_internal_name"';
        }
        if ($this->isColumnModified(ItemPeer::ITEM_DISPLAY_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"item_display_name"';
        }
        if ($this->isColumnModified(ItemPeer::ITEM_COST)) {
            $modifiedColumns[':p' . $index++]  = '"item_cost"';
        }
        if ($this->isColumnModified(ItemPeer::IMAGE)) {
            $modifiedColumns[':p' . $index++]  = '"image"';
        }
        if ($this->isColumnModified(ItemPeer::FOR_SALE)) {
            $modifiedColumns[':p' . $index++]  = '"for_sale"';
        }
        if ($this->isColumnModified(ItemPeer::USAGE)) {
            $modifiedColumns[':p' . $index++]  = '"usage"';
        }
        if ($this->isColumnModified(ItemPeer::IGNORE_STEALTH)) {
            $modifiedColumns[':p' . $index++]  = '"ignore_stealth"';
        }
        if ($this->isColumnModified(ItemPeer::COVERT)) {
            $modifiedColumns[':p' . $index++]  = '"covert"';
        }
        if ($this->isColumnModified(ItemPeer::TURN_COST)) {
            $modifiedColumns[':p' . $index++]  = '"turn_cost"';
        }
        if ($this->isColumnModified(ItemPeer::TARGET_DAMAGE)) {
            $modifiedColumns[':p' . $index++]  = '"target_damage"';
        }
        if ($this->isColumnModified(ItemPeer::TURN_CHANGE)) {
            $modifiedColumns[':p' . $index++]  = '"turn_change"';
        }
        if ($this->isColumnModified(ItemPeer::SELF_USE)) {
            $modifiedColumns[':p' . $index++]  = '"self_use"';
        }
        if ($this->isColumnModified(ItemPeer::PLURAL)) {
            $modifiedColumns[':p' . $index++]  = '"plural"';
        }
        if ($this->isColumnModified(ItemPeer::OTHER_USABLE)) {
            $modifiedColumns[':p' . $index++]  = '"other_usable"';
        }
        if ($this->isColumnModified(ItemPeer::TRAITS)) {
            $modifiedColumns[':p' . $index++]  = '"traits"';
        }

        $sql = sprintf(
            'INSERT INTO "item" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"item_id"':
                        $stmt->bindValue($identifier, $this->item_id, PDO::PARAM_INT);
                        break;
                    case '"item_internal_name"':
                        $stmt->bindValue($identifier, $this->item_internal_name, PDO::PARAM_STR);
                        break;
                    case '"item_display_name"':
                        $stmt->bindValue($identifier, $this->item_display_name, PDO::PARAM_STR);
                        break;
                    case '"item_cost"':
                        $stmt->bindValue($identifier, $this->item_cost, PDO::PARAM_STR);
                        break;
                    case '"image"':
                        $stmt->bindValue($identifier, $this->image, PDO::PARAM_STR);
                        break;
                    case '"for_sale"':
                        $stmt->bindValue($identifier, $this->for_sale, PDO::PARAM_BOOL);
                        break;
                    case '"usage"':
                        $stmt->bindValue($identifier, $this->usage, PDO::PARAM_STR);
                        break;
                    case '"ignore_stealth"':
                        $stmt->bindValue($identifier, $this->ignore_stealth, PDO::PARAM_BOOL);
                        break;
                    case '"covert"':
                        $stmt->bindValue($identifier, $this->covert, PDO::PARAM_BOOL);
                        break;
                    case '"turn_cost"':
                        $stmt->bindValue($identifier, $this->turn_cost, PDO::PARAM_INT);
                        break;
                    case '"target_damage"':
                        $stmt->bindValue($identifier, $this->target_damage, PDO::PARAM_INT);
                        break;
                    case '"turn_change"':
                        $stmt->bindValue($identifier, $this->turn_change, PDO::PARAM_INT);
                        break;
                    case '"self_use"':
                        $stmt->bindValue($identifier, $this->self_use, PDO::PARAM_BOOL);
                        break;
                    case '"plural"':
                        $stmt->bindValue($identifier, $this->plural, PDO::PARAM_STR);
                        break;
                    case '"other_usable"':
                        $stmt->bindValue($identifier, $this->other_usable, PDO::PARAM_BOOL);
                        break;
                    case '"traits"':
                        $stmt->bindValue($identifier, $this->traits, PDO::PARAM_STR);
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


            if (($retval = ItemPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collItemEffectss !== null) {
                    foreach ($this->collItemEffectss as $referrerFK) {
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
        $pos = ItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getItemId();
                break;
            case 1:
                return $this->getItemInternalName();
                break;
            case 2:
                return $this->getItemDisplayName();
                break;
            case 3:
                return $this->getItemCost();
                break;
            case 4:
                return $this->getImage();
                break;
            case 5:
                return $this->getForSale();
                break;
            case 6:
                return $this->getUsage();
                break;
            case 7:
                return $this->getIgnoreStealth();
                break;
            case 8:
                return $this->getCovert();
                break;
            case 9:
                return $this->getTurnCost();
                break;
            case 10:
                return $this->getTargetDamage();
                break;
            case 11:
                return $this->getTurnChange();
                break;
            case 12:
                return $this->getSelfUse();
                break;
            case 13:
                return $this->getPlural();
                break;
            case 14:
                return $this->getOtherUsable();
                break;
            case 15:
                return $this->getTraits();
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
        if (isset($alreadyDumpedObjects['Item'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Item'][$this->getPrimaryKey()] = true;
        $keys = ItemPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getItemId(),
            $keys[1] => $this->getItemInternalName(),
            $keys[2] => $this->getItemDisplayName(),
            $keys[3] => $this->getItemCost(),
            $keys[4] => $this->getImage(),
            $keys[5] => $this->getForSale(),
            $keys[6] => $this->getUsage(),
            $keys[7] => $this->getIgnoreStealth(),
            $keys[8] => $this->getCovert(),
            $keys[9] => $this->getTurnCost(),
            $keys[10] => $this->getTargetDamage(),
            $keys[11] => $this->getTurnChange(),
            $keys[12] => $this->getSelfUse(),
            $keys[13] => $this->getPlural(),
            $keys[14] => $this->getOtherUsable(),
            $keys[15] => $this->getTraits(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collItemEffectss) {
                $result['ItemEffectss'] = $this->collItemEffectss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setItemId($value);
                break;
            case 1:
                $this->setItemInternalName($value);
                break;
            case 2:
                $this->setItemDisplayName($value);
                break;
            case 3:
                $this->setItemCost($value);
                break;
            case 4:
                $this->setImage($value);
                break;
            case 5:
                $this->setForSale($value);
                break;
            case 6:
                $this->setUsage($value);
                break;
            case 7:
                $this->setIgnoreStealth($value);
                break;
            case 8:
                $this->setCovert($value);
                break;
            case 9:
                $this->setTurnCost($value);
                break;
            case 10:
                $this->setTargetDamage($value);
                break;
            case 11:
                $this->setTurnChange($value);
                break;
            case 12:
                $this->setSelfUse($value);
                break;
            case 13:
                $this->setPlural($value);
                break;
            case 14:
                $this->setOtherUsable($value);
                break;
            case 15:
                $this->setTraits($value);
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
        $keys = ItemPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setItemId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setItemInternalName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setItemDisplayName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setItemCost($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setImage($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setForSale($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUsage($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setIgnoreStealth($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCovert($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setTurnCost($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setTargetDamage($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setTurnChange($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setSelfUse($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setPlural($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setOtherUsable($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setTraits($arr[$keys[15]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ItemPeer::DATABASE_NAME);

        if ($this->isColumnModified(ItemPeer::ITEM_ID)) $criteria->add(ItemPeer::ITEM_ID, $this->item_id);
        if ($this->isColumnModified(ItemPeer::ITEM_INTERNAL_NAME)) $criteria->add(ItemPeer::ITEM_INTERNAL_NAME, $this->item_internal_name);
        if ($this->isColumnModified(ItemPeer::ITEM_DISPLAY_NAME)) $criteria->add(ItemPeer::ITEM_DISPLAY_NAME, $this->item_display_name);
        if ($this->isColumnModified(ItemPeer::ITEM_COST)) $criteria->add(ItemPeer::ITEM_COST, $this->item_cost);
        if ($this->isColumnModified(ItemPeer::IMAGE)) $criteria->add(ItemPeer::IMAGE, $this->image);
        if ($this->isColumnModified(ItemPeer::FOR_SALE)) $criteria->add(ItemPeer::FOR_SALE, $this->for_sale);
        if ($this->isColumnModified(ItemPeer::USAGE)) $criteria->add(ItemPeer::USAGE, $this->usage);
        if ($this->isColumnModified(ItemPeer::IGNORE_STEALTH)) $criteria->add(ItemPeer::IGNORE_STEALTH, $this->ignore_stealth);
        if ($this->isColumnModified(ItemPeer::COVERT)) $criteria->add(ItemPeer::COVERT, $this->covert);
        if ($this->isColumnModified(ItemPeer::TURN_COST)) $criteria->add(ItemPeer::TURN_COST, $this->turn_cost);
        if ($this->isColumnModified(ItemPeer::TARGET_DAMAGE)) $criteria->add(ItemPeer::TARGET_DAMAGE, $this->target_damage);
        if ($this->isColumnModified(ItemPeer::TURN_CHANGE)) $criteria->add(ItemPeer::TURN_CHANGE, $this->turn_change);
        if ($this->isColumnModified(ItemPeer::SELF_USE)) $criteria->add(ItemPeer::SELF_USE, $this->self_use);
        if ($this->isColumnModified(ItemPeer::PLURAL)) $criteria->add(ItemPeer::PLURAL, $this->plural);
        if ($this->isColumnModified(ItemPeer::OTHER_USABLE)) $criteria->add(ItemPeer::OTHER_USABLE, $this->other_usable);
        if ($this->isColumnModified(ItemPeer::TRAITS)) $criteria->add(ItemPeer::TRAITS, $this->traits);

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
        $criteria = new Criteria(ItemPeer::DATABASE_NAME);
        $criteria->add(ItemPeer::ITEM_ID, $this->item_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getItemId();
    }

    /**
     * Generic method to set the primary key (item_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setItemId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getItemId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Item (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setItemInternalName($this->getItemInternalName());
        $copyObj->setItemDisplayName($this->getItemDisplayName());
        $copyObj->setItemCost($this->getItemCost());
        $copyObj->setImage($this->getImage());
        $copyObj->setForSale($this->getForSale());
        $copyObj->setUsage($this->getUsage());
        $copyObj->setIgnoreStealth($this->getIgnoreStealth());
        $copyObj->setCovert($this->getCovert());
        $copyObj->setTurnCost($this->getTurnCost());
        $copyObj->setTargetDamage($this->getTargetDamage());
        $copyObj->setTurnChange($this->getTurnChange());
        $copyObj->setSelfUse($this->getSelfUse());
        $copyObj->setPlural($this->getPlural());
        $copyObj->setOtherUsable($this->getOtherUsable());
        $copyObj->setTraits($this->getTraits());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getItemEffectss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addItemEffects($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setItemId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Item Clone of current object.
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
     * @return ItemPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ItemPeer();
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
        if ('ItemEffects' == $relationName) {
            $this->initItemEffectss();
        }
    }

    /**
     * Clears out the collItemEffectss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Item The current object (for fluent API support)
     * @see        addItemEffectss()
     */
    public function clearItemEffectss()
    {
        $this->collItemEffectss = null; // important to set this to null since that means it is uninitialized
        $this->collItemEffectssPartial = null;

        return $this;
    }

    /**
     * reset is the collItemEffectss collection loaded partially
     *
     * @return void
     */
    public function resetPartialItemEffectss($v = true)
    {
        $this->collItemEffectssPartial = $v;
    }

    /**
     * Initializes the collItemEffectss collection.
     *
     * By default this just sets the collItemEffectss collection to an empty array (like clearcollItemEffectss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initItemEffectss($overrideExisting = true)
    {
        if (null !== $this->collItemEffectss && !$overrideExisting) {
            return;
        }
        $this->collItemEffectss = new PropelObjectCollection();
        $this->collItemEffectss->setModel('ItemEffects');
    }

    /**
     * Gets an array of ItemEffects objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ItemEffects[] List of ItemEffects objects
     * @throws PropelException
     */
    public function getItemEffectss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collItemEffectssPartial && !$this->isNew();
        if (null === $this->collItemEffectss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collItemEffectss) {
                // return empty collection
                $this->initItemEffectss();
            } else {
                $collItemEffectss = ItemEffectsQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collItemEffectssPartial && count($collItemEffectss)) {
                      $this->initItemEffectss(false);

                      foreach ($collItemEffectss as $obj) {
                        if (false == $this->collItemEffectss->contains($obj)) {
                          $this->collItemEffectss->append($obj);
                        }
                      }

                      $this->collItemEffectssPartial = true;
                    }

                    $collItemEffectss->getInternalIterator()->rewind();

                    return $collItemEffectss;
                }

                if ($partial && $this->collItemEffectss) {
                    foreach ($this->collItemEffectss as $obj) {
                        if ($obj->isNew()) {
                            $collItemEffectss[] = $obj;
                        }
                    }
                }

                $this->collItemEffectss = $collItemEffectss;
                $this->collItemEffectssPartial = false;
            }
        }

        return $this->collItemEffectss;
    }

    /**
     * Sets a collection of ItemEffects objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $itemEffectss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Item The current object (for fluent API support)
     */
    public function setItemEffectss(PropelCollection $itemEffectss, PropelPDO $con = null)
    {
        $itemEffectssToDelete = $this->getItemEffectss(new Criteria(), $con)->diff($itemEffectss);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->itemEffectssScheduledForDeletion = clone $itemEffectssToDelete;

        foreach ($itemEffectssToDelete as $itemEffectsRemoved) {
            $itemEffectsRemoved->setItem(null);
        }

        $this->collItemEffectss = null;
        foreach ($itemEffectss as $itemEffects) {
            $this->addItemEffects($itemEffects);
        }

        $this->collItemEffectss = $itemEffectss;
        $this->collItemEffectssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ItemEffects objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ItemEffects objects.
     * @throws PropelException
     */
    public function countItemEffectss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collItemEffectssPartial && !$this->isNew();
        if (null === $this->collItemEffectss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collItemEffectss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getItemEffectss());
            }
            $query = ItemEffectsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByItem($this)
                ->count($con);
        }

        return count($this->collItemEffectss);
    }

    /**
     * Method called to associate a ItemEffects object to this object
     * through the ItemEffects foreign key attribute.
     *
     * @param    ItemEffects $l ItemEffects
     * @return Item The current object (for fluent API support)
     */
    public function addItemEffects(ItemEffects $l)
    {
        if ($this->collItemEffectss === null) {
            $this->initItemEffectss();
            $this->collItemEffectssPartial = true;
        }
        if (!in_array($l, $this->collItemEffectss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddItemEffects($l);
        }

        return $this;
    }

    /**
     * @param	ItemEffects $itemEffects The itemEffects object to add.
     */
    protected function doAddItemEffects($itemEffects)
    {
        $this->collItemEffectss[]= $itemEffects;
        $itemEffects->setItem($this);
    }

    /**
     * @param	ItemEffects $itemEffects The itemEffects object to remove.
     * @return Item The current object (for fluent API support)
     */
    public function removeItemEffects($itemEffects)
    {
        if ($this->getItemEffectss()->contains($itemEffects)) {
            $this->collItemEffectss->remove($this->collItemEffectss->search($itemEffects));
            if (null === $this->itemEffectssScheduledForDeletion) {
                $this->itemEffectssScheduledForDeletion = clone $this->collItemEffectss;
                $this->itemEffectssScheduledForDeletion->clear();
            }
            $this->itemEffectssScheduledForDeletion[]= clone $itemEffects;
            $itemEffects->setItem(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Item is new, it will return
     * an empty collection; or if this Item has previously
     * been saved, it will retrieve related ItemEffectss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Item.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ItemEffects[] List of ItemEffects objects
     */
    public function getItemEffectssJoinEffects($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ItemEffectsQuery::create(null, $criteria);
        $query->joinWith('Effects', $join_behavior);

        return $this->getItemEffectss($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->item_id = null;
        $this->item_internal_name = null;
        $this->item_display_name = null;
        $this->item_cost = null;
        $this->image = null;
        $this->for_sale = null;
        $this->usage = null;
        $this->ignore_stealth = null;
        $this->covert = null;
        $this->turn_cost = null;
        $this->target_damage = null;
        $this->turn_change = null;
        $this->self_use = null;
        $this->plural = null;
        $this->other_usable = null;
        $this->traits = null;
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
            if ($this->collItemEffectss) {
                foreach ($this->collItemEffectss as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collItemEffectss instanceof PropelCollection) {
            $this->collItemEffectss->clearIterator();
        }
        $this->collItemEffectss = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ItemPeer::DEFAULT_STRING_FORMAT);
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
