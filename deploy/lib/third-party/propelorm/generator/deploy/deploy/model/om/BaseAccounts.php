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
use deploy\model\AccountPlayers;
use deploy\model\AccountPlayersQuery;
use deploy\model\Accounts;
use deploy\model\AccountsPeer;
use deploy\model\AccountsQuery;

/**
 * Base class that represents a row from the 'accounts' table.
 *
 *
 *
 * @package    propel.generator.deploy.model.om
 */
abstract class BaseAccounts extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'deploy\\model\\AccountsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AccountsPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the account_id field.
     * @var        int
     */
    protected $account_id;

    /**
     * The value for the account_identity field.
     * @var        string
     */
    protected $account_identity;

    /**
     * The value for the phash field.
     * @var        string
     */
    protected $phash;

    /**
     * The value for the active_email field.
     * @var        string
     */
    protected $active_email;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $type;

    /**
     * The value for the operational field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $operational;

    /**
     * The value for the created_date field.
     * Note: this column has a database default value of: (expression) now()
     * @var        string
     */
    protected $created_date;

    /**
     * The value for the last_login field.
     * @var        string
     */
    protected $last_login;

    /**
     * The value for the last_login_failure field.
     * @var        string
     */
    protected $last_login_failure;

    /**
     * The value for the karma_total field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $karma_total;

    /**
     * The value for the last_ip field.
     * @var        string
     */
    protected $last_ip;

    /**
     * The value for the confirmed field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $confirmed;

    /**
     * The value for the verification_number field.
     * @var        string
     */
    protected $verification_number;

    /**
     * @var        PropelObjectCollection|AccountPlayers[] Collection to store aggregation of AccountPlayers objects.
     */
    protected $collAccountPlayerss;
    protected $collAccountPlayerssPartial;

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
    protected $accountPlayerssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->type = 0;
        $this->operational = true;
        $this->karma_total = 0;
        $this->confirmed = 0;
    }

    /**
     * Initializes internal state of BaseAccounts object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [account_id] column value.
     *
     * @return int
     */
    public function getAccountId()
    {

        return $this->account_id;
    }

    /**
     * Get the [account_identity] column value.
     *
     * @return string
     */
    public function getAccountIdentity()
    {

        return $this->account_identity;
    }

    /**
     * Get the [phash] column value.
     *
     * @return string
     */
    public function getPhash()
    {

        return $this->phash;
    }

    /**
     * Get the [active_email] column value.
     *
     * @return string
     */
    public function getActiveEmail()
    {

        return $this->active_email;
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
     * Get the [operational] column value.
     *
     * @return boolean
     */
    public function getOperational()
    {

        return $this->operational;
    }

    /**
     * Get the [optionally formatted] temporal [created_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedDate($format = 'Y-m-d H:i:s')
    {
        if ($this->created_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->created_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_date, true), $x);
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
     * Get the [optionally formatted] temporal [last_login] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastLogin($format = 'Y-m-d H:i:s')
    {
        if ($this->last_login === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_login);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_login, true), $x);
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
     * Get the [optionally formatted] temporal [last_login_failure] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastLoginFailure($format = 'Y-m-d H:i:s')
    {
        if ($this->last_login_failure === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_login_failure);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_login_failure, true), $x);
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
     * Get the [karma_total] column value.
     *
     * @return int
     */
    public function getKarmaTotal()
    {

        return $this->karma_total;
    }

    /**
     * Get the [last_ip] column value.
     *
     * @return string
     */
    public function getLastIp()
    {

        return $this->last_ip;
    }

    /**
     * Get the [confirmed] column value.
     *
     * @return int
     */
    public function getConfirmed()
    {

        return $this->confirmed;
    }

    /**
     * Get the [verification_number] column value.
     *
     * @return string
     */
    public function getVerificationNumber()
    {

        return $this->verification_number;
    }

    /**
     * Set the value of [account_id] column.
     *
     * @param  int $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[] = AccountsPeer::ACCOUNT_ID;
        }


        return $this;
    } // setAccountId()

    /**
     * Set the value of [account_identity] column.
     *
     * @param  string $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setAccountIdentity($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->account_identity !== $v) {
            $this->account_identity = $v;
            $this->modifiedColumns[] = AccountsPeer::ACCOUNT_IDENTITY;
        }


        return $this;
    } // setAccountIdentity()

    /**
     * Set the value of [phash] column.
     *
     * @param  string $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setPhash($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->phash !== $v) {
            $this->phash = $v;
            $this->modifiedColumns[] = AccountsPeer::PHASH;
        }


        return $this;
    } // setPhash()

    /**
     * Set the value of [active_email] column.
     *
     * @param  string $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setActiveEmail($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->active_email !== $v) {
            $this->active_email = $v;
            $this->modifiedColumns[] = AccountsPeer::ACTIVE_EMAIL;
        }


        return $this;
    } // setActiveEmail()

    /**
     * Set the value of [type] column.
     *
     * @param  int $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = AccountsPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Sets the value of the [operational] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setOperational($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->operational !== $v) {
            $this->operational = $v;
            $this->modifiedColumns[] = AccountsPeer::OPERATIONAL;
        }


        return $this;
    } // setOperational()

    /**
     * Sets the value of [created_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Accounts The current object (for fluent API support)
     */
    public function setCreatedDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_date !== null || $dt !== null) {
            $currentDateAsString = ($this->created_date !== null && $tmpDt = new DateTime($this->created_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_date = $newDateAsString;
                $this->modifiedColumns[] = AccountsPeer::CREATED_DATE;
            }
        } // if either are not null


        return $this;
    } // setCreatedDate()

    /**
     * Sets the value of [last_login] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Accounts The current object (for fluent API support)
     */
    public function setLastLogin($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_login !== null || $dt !== null) {
            $currentDateAsString = ($this->last_login !== null && $tmpDt = new DateTime($this->last_login)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_login = $newDateAsString;
                $this->modifiedColumns[] = AccountsPeer::LAST_LOGIN;
            }
        } // if either are not null


        return $this;
    } // setLastLogin()

    /**
     * Sets the value of [last_login_failure] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Accounts The current object (for fluent API support)
     */
    public function setLastLoginFailure($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_login_failure !== null || $dt !== null) {
            $currentDateAsString = ($this->last_login_failure !== null && $tmpDt = new DateTime($this->last_login_failure)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_login_failure = $newDateAsString;
                $this->modifiedColumns[] = AccountsPeer::LAST_LOGIN_FAILURE;
            }
        } // if either are not null


        return $this;
    } // setLastLoginFailure()

    /**
     * Set the value of [karma_total] column.
     *
     * @param  int $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setKarmaTotal($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->karma_total !== $v) {
            $this->karma_total = $v;
            $this->modifiedColumns[] = AccountsPeer::KARMA_TOTAL;
        }


        return $this;
    } // setKarmaTotal()

    /**
     * Set the value of [last_ip] column.
     *
     * @param  string $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setLastIp($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->last_ip !== $v) {
            $this->last_ip = $v;
            $this->modifiedColumns[] = AccountsPeer::LAST_IP;
        }


        return $this;
    } // setLastIp()

    /**
     * Set the value of [confirmed] column.
     *
     * @param  int $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setConfirmed($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->confirmed !== $v) {
            $this->confirmed = $v;
            $this->modifiedColumns[] = AccountsPeer::CONFIRMED;
        }


        return $this;
    } // setConfirmed()

    /**
     * Set the value of [verification_number] column.
     *
     * @param  string $v new value
     * @return Accounts The current object (for fluent API support)
     */
    public function setVerificationNumber($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->verification_number !== $v) {
            $this->verification_number = $v;
            $this->modifiedColumns[] = AccountsPeer::VERIFICATION_NUMBER;
        }


        return $this;
    } // setVerificationNumber()

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
            if ($this->type !== 0) {
                return false;
            }

            if ($this->operational !== true) {
                return false;
            }

            if ($this->karma_total !== 0) {
                return false;
            }

            if ($this->confirmed !== 0) {
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

            $this->account_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->account_identity = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->phash = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->active_email = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->type = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->operational = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->created_date = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->last_login = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->last_login_failure = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->karma_total = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->last_ip = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->confirmed = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->verification_number = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 13; // 13 = AccountsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Accounts object", $e);
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
            $con = Propel::getConnection(AccountsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AccountsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collAccountPlayerss = null;

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
            $con = Propel::getConnection(AccountsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AccountsQuery::create()
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
            $con = Propel::getConnection(AccountsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                AccountsPeer::addInstanceToPool($this);
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

            if ($this->accountPlayerssScheduledForDeletion !== null) {
                if (!$this->accountPlayerssScheduledForDeletion->isEmpty()) {
                    AccountPlayersQuery::create()
                        ->filterByPrimaryKeys($this->accountPlayerssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountPlayerssScheduledForDeletion = null;
                }
            }

            if ($this->collAccountPlayerss !== null) {
                foreach ($this->collAccountPlayerss as $referrerFK) {
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

        $this->modifiedColumns[] = AccountsPeer::ACCOUNT_ID;
        if (null !== $this->account_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AccountsPeer::ACCOUNT_ID . ')');
        }
        if (null === $this->account_id) {
            try {
                $stmt = $con->query("SELECT nextval('accounts_account_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->account_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AccountsPeer::ACCOUNT_ID)) {
            $modifiedColumns[':p' . $index++]  = '"account_id"';
        }
        if ($this->isColumnModified(AccountsPeer::ACCOUNT_IDENTITY)) {
            $modifiedColumns[':p' . $index++]  = '"account_identity"';
        }
        if ($this->isColumnModified(AccountsPeer::PHASH)) {
            $modifiedColumns[':p' . $index++]  = '"phash"';
        }
        if ($this->isColumnModified(AccountsPeer::ACTIVE_EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '"active_email"';
        }
        if ($this->isColumnModified(AccountsPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }
        if ($this->isColumnModified(AccountsPeer::OPERATIONAL)) {
            $modifiedColumns[':p' . $index++]  = '"operational"';
        }
        if ($this->isColumnModified(AccountsPeer::CREATED_DATE)) {
            $modifiedColumns[':p' . $index++]  = '"created_date"';
        }
        if ($this->isColumnModified(AccountsPeer::LAST_LOGIN)) {
            $modifiedColumns[':p' . $index++]  = '"last_login"';
        }
        if ($this->isColumnModified(AccountsPeer::LAST_LOGIN_FAILURE)) {
            $modifiedColumns[':p' . $index++]  = '"last_login_failure"';
        }
        if ($this->isColumnModified(AccountsPeer::KARMA_TOTAL)) {
            $modifiedColumns[':p' . $index++]  = '"karma_total"';
        }
        if ($this->isColumnModified(AccountsPeer::LAST_IP)) {
            $modifiedColumns[':p' . $index++]  = '"last_ip"';
        }
        if ($this->isColumnModified(AccountsPeer::CONFIRMED)) {
            $modifiedColumns[':p' . $index++]  = '"confirmed"';
        }
        if ($this->isColumnModified(AccountsPeer::VERIFICATION_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"verification_number"';
        }

        $sql = sprintf(
            'INSERT INTO "accounts" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"account_id"':
                        $stmt->bindValue($identifier, $this->account_id, PDO::PARAM_INT);
                        break;
                    case '"account_identity"':
                        $stmt->bindValue($identifier, $this->account_identity, PDO::PARAM_STR);
                        break;
                    case '"phash"':
                        $stmt->bindValue($identifier, $this->phash, PDO::PARAM_STR);
                        break;
                    case '"active_email"':
                        $stmt->bindValue($identifier, $this->active_email, PDO::PARAM_STR);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
                        break;
                    case '"operational"':
                        $stmt->bindValue($identifier, $this->operational, PDO::PARAM_BOOL);
                        break;
                    case '"created_date"':
                        $stmt->bindValue($identifier, $this->created_date, PDO::PARAM_STR);
                        break;
                    case '"last_login"':
                        $stmt->bindValue($identifier, $this->last_login, PDO::PARAM_STR);
                        break;
                    case '"last_login_failure"':
                        $stmt->bindValue($identifier, $this->last_login_failure, PDO::PARAM_STR);
                        break;
                    case '"karma_total"':
                        $stmt->bindValue($identifier, $this->karma_total, PDO::PARAM_INT);
                        break;
                    case '"last_ip"':
                        $stmt->bindValue($identifier, $this->last_ip, PDO::PARAM_STR);
                        break;
                    case '"confirmed"':
                        $stmt->bindValue($identifier, $this->confirmed, PDO::PARAM_INT);
                        break;
                    case '"verification_number"':
                        $stmt->bindValue($identifier, $this->verification_number, PDO::PARAM_STR);
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


            if (($retval = AccountsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAccountPlayerss !== null) {
                    foreach ($this->collAccountPlayerss as $referrerFK) {
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
        $pos = AccountsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAccountId();
                break;
            case 1:
                return $this->getAccountIdentity();
                break;
            case 2:
                return $this->getPhash();
                break;
            case 3:
                return $this->getActiveEmail();
                break;
            case 4:
                return $this->getType();
                break;
            case 5:
                return $this->getOperational();
                break;
            case 6:
                return $this->getCreatedDate();
                break;
            case 7:
                return $this->getLastLogin();
                break;
            case 8:
                return $this->getLastLoginFailure();
                break;
            case 9:
                return $this->getKarmaTotal();
                break;
            case 10:
                return $this->getLastIp();
                break;
            case 11:
                return $this->getConfirmed();
                break;
            case 12:
                return $this->getVerificationNumber();
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
        if (isset($alreadyDumpedObjects['Accounts'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Accounts'][$this->getPrimaryKey()] = true;
        $keys = AccountsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAccountId(),
            $keys[1] => $this->getAccountIdentity(),
            $keys[2] => $this->getPhash(),
            $keys[3] => $this->getActiveEmail(),
            $keys[4] => $this->getType(),
            $keys[5] => $this->getOperational(),
            $keys[6] => $this->getCreatedDate(),
            $keys[7] => $this->getLastLogin(),
            $keys[8] => $this->getLastLoginFailure(),
            $keys[9] => $this->getKarmaTotal(),
            $keys[10] => $this->getLastIp(),
            $keys[11] => $this->getConfirmed(),
            $keys[12] => $this->getVerificationNumber(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collAccountPlayerss) {
                $result['AccountPlayerss'] = $this->collAccountPlayerss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = AccountsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setAccountId($value);
                break;
            case 1:
                $this->setAccountIdentity($value);
                break;
            case 2:
                $this->setPhash($value);
                break;
            case 3:
                $this->setActiveEmail($value);
                break;
            case 4:
                $this->setType($value);
                break;
            case 5:
                $this->setOperational($value);
                break;
            case 6:
                $this->setCreatedDate($value);
                break;
            case 7:
                $this->setLastLogin($value);
                break;
            case 8:
                $this->setLastLoginFailure($value);
                break;
            case 9:
                $this->setKarmaTotal($value);
                break;
            case 10:
                $this->setLastIp($value);
                break;
            case 11:
                $this->setConfirmed($value);
                break;
            case 12:
                $this->setVerificationNumber($value);
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
        $keys = AccountsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setAccountId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setAccountIdentity($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPhash($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setActiveEmail($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setOperational($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedDate($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setLastLogin($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLastLoginFailure($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setKarmaTotal($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setLastIp($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setConfirmed($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setVerificationNumber($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AccountsPeer::DATABASE_NAME);

        if ($this->isColumnModified(AccountsPeer::ACCOUNT_ID)) $criteria->add(AccountsPeer::ACCOUNT_ID, $this->account_id);
        if ($this->isColumnModified(AccountsPeer::ACCOUNT_IDENTITY)) $criteria->add(AccountsPeer::ACCOUNT_IDENTITY, $this->account_identity);
        if ($this->isColumnModified(AccountsPeer::PHASH)) $criteria->add(AccountsPeer::PHASH, $this->phash);
        if ($this->isColumnModified(AccountsPeer::ACTIVE_EMAIL)) $criteria->add(AccountsPeer::ACTIVE_EMAIL, $this->active_email);
        if ($this->isColumnModified(AccountsPeer::TYPE)) $criteria->add(AccountsPeer::TYPE, $this->type);
        if ($this->isColumnModified(AccountsPeer::OPERATIONAL)) $criteria->add(AccountsPeer::OPERATIONAL, $this->operational);
        if ($this->isColumnModified(AccountsPeer::CREATED_DATE)) $criteria->add(AccountsPeer::CREATED_DATE, $this->created_date);
        if ($this->isColumnModified(AccountsPeer::LAST_LOGIN)) $criteria->add(AccountsPeer::LAST_LOGIN, $this->last_login);
        if ($this->isColumnModified(AccountsPeer::LAST_LOGIN_FAILURE)) $criteria->add(AccountsPeer::LAST_LOGIN_FAILURE, $this->last_login_failure);
        if ($this->isColumnModified(AccountsPeer::KARMA_TOTAL)) $criteria->add(AccountsPeer::KARMA_TOTAL, $this->karma_total);
        if ($this->isColumnModified(AccountsPeer::LAST_IP)) $criteria->add(AccountsPeer::LAST_IP, $this->last_ip);
        if ($this->isColumnModified(AccountsPeer::CONFIRMED)) $criteria->add(AccountsPeer::CONFIRMED, $this->confirmed);
        if ($this->isColumnModified(AccountsPeer::VERIFICATION_NUMBER)) $criteria->add(AccountsPeer::VERIFICATION_NUMBER, $this->verification_number);

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
        $criteria = new Criteria(AccountsPeer::DATABASE_NAME);
        $criteria->add(AccountsPeer::ACCOUNT_ID, $this->account_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getAccountId();
    }

    /**
     * Generic method to set the primary key (account_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setAccountId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getAccountId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Accounts (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setAccountIdentity($this->getAccountIdentity());
        $copyObj->setPhash($this->getPhash());
        $copyObj->setActiveEmail($this->getActiveEmail());
        $copyObj->setType($this->getType());
        $copyObj->setOperational($this->getOperational());
        $copyObj->setCreatedDate($this->getCreatedDate());
        $copyObj->setLastLogin($this->getLastLogin());
        $copyObj->setLastLoginFailure($this->getLastLoginFailure());
        $copyObj->setKarmaTotal($this->getKarmaTotal());
        $copyObj->setLastIp($this->getLastIp());
        $copyObj->setConfirmed($this->getConfirmed());
        $copyObj->setVerificationNumber($this->getVerificationNumber());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getAccountPlayerss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountPlayers($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setAccountId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Accounts Clone of current object.
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
     * @return AccountsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AccountsPeer();
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
        if ('AccountPlayers' == $relationName) {
            $this->initAccountPlayerss();
        }
    }

    /**
     * Clears out the collAccountPlayerss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Accounts The current object (for fluent API support)
     * @see        addAccountPlayerss()
     */
    public function clearAccountPlayerss()
    {
        $this->collAccountPlayerss = null; // important to set this to null since that means it is uninitialized
        $this->collAccountPlayerssPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountPlayerss collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountPlayerss($v = true)
    {
        $this->collAccountPlayerssPartial = $v;
    }

    /**
     * Initializes the collAccountPlayerss collection.
     *
     * By default this just sets the collAccountPlayerss collection to an empty array (like clearcollAccountPlayerss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountPlayerss($overrideExisting = true)
    {
        if (null !== $this->collAccountPlayerss && !$overrideExisting) {
            return;
        }
        $this->collAccountPlayerss = new PropelObjectCollection();
        $this->collAccountPlayerss->setModel('AccountPlayers');
    }

    /**
     * Gets an array of AccountPlayers objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Accounts is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AccountPlayers[] List of AccountPlayers objects
     * @throws PropelException
     */
    public function getAccountPlayerss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountPlayerssPartial && !$this->isNew();
        if (null === $this->collAccountPlayerss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountPlayerss) {
                // return empty collection
                $this->initAccountPlayerss();
            } else {
                $collAccountPlayerss = AccountPlayersQuery::create(null, $criteria)
                    ->filterByAccounts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountPlayerssPartial && count($collAccountPlayerss)) {
                      $this->initAccountPlayerss(false);

                      foreach ($collAccountPlayerss as $obj) {
                        if (false == $this->collAccountPlayerss->contains($obj)) {
                          $this->collAccountPlayerss->append($obj);
                        }
                      }

                      $this->collAccountPlayerssPartial = true;
                    }

                    $collAccountPlayerss->getInternalIterator()->rewind();

                    return $collAccountPlayerss;
                }

                if ($partial && $this->collAccountPlayerss) {
                    foreach ($this->collAccountPlayerss as $obj) {
                        if ($obj->isNew()) {
                            $collAccountPlayerss[] = $obj;
                        }
                    }
                }

                $this->collAccountPlayerss = $collAccountPlayerss;
                $this->collAccountPlayerssPartial = false;
            }
        }

        return $this->collAccountPlayerss;
    }

    /**
     * Sets a collection of AccountPlayers objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountPlayerss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Accounts The current object (for fluent API support)
     */
    public function setAccountPlayerss(PropelCollection $accountPlayerss, PropelPDO $con = null)
    {
        $accountPlayerssToDelete = $this->getAccountPlayerss(new Criteria(), $con)->diff($accountPlayerss);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->accountPlayerssScheduledForDeletion = clone $accountPlayerssToDelete;

        foreach ($accountPlayerssToDelete as $accountPlayersRemoved) {
            $accountPlayersRemoved->setAccounts(null);
        }

        $this->collAccountPlayerss = null;
        foreach ($accountPlayerss as $accountPlayers) {
            $this->addAccountPlayers($accountPlayers);
        }

        $this->collAccountPlayerss = $accountPlayerss;
        $this->collAccountPlayerssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountPlayers objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AccountPlayers objects.
     * @throws PropelException
     */
    public function countAccountPlayerss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountPlayerssPartial && !$this->isNew();
        if (null === $this->collAccountPlayerss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountPlayerss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountPlayerss());
            }
            $query = AccountPlayersQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccounts($this)
                ->count($con);
        }

        return count($this->collAccountPlayerss);
    }

    /**
     * Method called to associate a AccountPlayers object to this object
     * through the AccountPlayers foreign key attribute.
     *
     * @param    AccountPlayers $l AccountPlayers
     * @return Accounts The current object (for fluent API support)
     */
    public function addAccountPlayers(AccountPlayers $l)
    {
        if ($this->collAccountPlayerss === null) {
            $this->initAccountPlayerss();
            $this->collAccountPlayerssPartial = true;
        }
        if (!in_array($l, $this->collAccountPlayerss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountPlayers($l);
        }

        return $this;
    }

    /**
     * @param	AccountPlayers $accountPlayers The accountPlayers object to add.
     */
    protected function doAddAccountPlayers($accountPlayers)
    {
        $this->collAccountPlayerss[]= $accountPlayers;
        $accountPlayers->setAccounts($this);
    }

    /**
     * @param	AccountPlayers $accountPlayers The accountPlayers object to remove.
     * @return Accounts The current object (for fluent API support)
     */
    public function removeAccountPlayers($accountPlayers)
    {
        if ($this->getAccountPlayerss()->contains($accountPlayers)) {
            $this->collAccountPlayerss->remove($this->collAccountPlayerss->search($accountPlayers));
            if (null === $this->accountPlayerssScheduledForDeletion) {
                $this->accountPlayerssScheduledForDeletion = clone $this->collAccountPlayerss;
                $this->accountPlayerssScheduledForDeletion->clear();
            }
            $this->accountPlayerssScheduledForDeletion[]= clone $accountPlayers;
            $accountPlayers->setAccounts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Accounts is new, it will return
     * an empty collection; or if this Accounts has previously
     * been saved, it will retrieve related AccountPlayerss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Accounts.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AccountPlayers[] List of AccountPlayers objects
     */
    public function getAccountPlayerssJoinPlayers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountPlayersQuery::create(null, $criteria);
        $query->joinWith('Players', $join_behavior);

        return $this->getAccountPlayerss($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->account_id = null;
        $this->account_identity = null;
        $this->phash = null;
        $this->active_email = null;
        $this->type = null;
        $this->operational = null;
        $this->created_date = null;
        $this->last_login = null;
        $this->last_login_failure = null;
        $this->karma_total = null;
        $this->last_ip = null;
        $this->confirmed = null;
        $this->verification_number = null;
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
            if ($this->collAccountPlayerss) {
                foreach ($this->collAccountPlayerss as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAccountPlayerss instanceof PropelCollection) {
            $this->collAccountPlayerss->clearIterator();
        }
        $this->collAccountPlayerss = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AccountsPeer::DEFAULT_STRING_FORMAT);
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
