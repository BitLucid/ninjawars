<?php

namespace deploy\model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use deploy\model\Item;
use deploy\model\ItemPeer;
use deploy\model\map\ItemTableMap;

/**
 * Base static class for performing query and update operations on the 'item' table.
 *
 *
 *
 * @package propel.generator.deploy.model.om
 */
abstract class BaseItemPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'ninjawars';

    /** the table name for this class */
    const TABLE_NAME = 'item';

    /** the related Propel class for this table */
    const OM_CLASS = 'deploy\\model\\Item';

    /** the related TableMap class for this table */
    const TM_CLASS = 'ItemTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 16;

    /** the column name for the item_id field */
    const ITEM_ID = 'item.item_id';

    /** the column name for the item_internal_name field */
    const ITEM_INTERNAL_NAME = 'item.item_internal_name';

    /** the column name for the item_display_name field */
    const ITEM_DISPLAY_NAME = 'item.item_display_name';

    /** the column name for the item_cost field */
    const ITEM_COST = 'item.item_cost';

    /** the column name for the image field */
    const IMAGE = 'item.image';

    /** the column name for the for_sale field */
    const FOR_SALE = 'item.for_sale';

    /** the column name for the usage field */
    const USAGE = 'item.usage';

    /** the column name for the ignore_stealth field */
    const IGNORE_STEALTH = 'item.ignore_stealth';

    /** the column name for the covert field */
    const COVERT = 'item.covert';

    /** the column name for the turn_cost field */
    const TURN_COST = 'item.turn_cost';

    /** the column name for the target_damage field */
    const TARGET_DAMAGE = 'item.target_damage';

    /** the column name for the turn_change field */
    const TURN_CHANGE = 'item.turn_change';

    /** the column name for the self_use field */
    const SELF_USE = 'item.self_use';

    /** the column name for the plural field */
    const PLURAL = 'item.plural';

    /** the column name for the other_usable field */
    const OTHER_USABLE = 'item.other_usable';

    /** the column name for the traits field */
    const TRAITS = 'item.traits';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Item objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Item[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. ItemPeer::$fieldNames[ItemPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('ItemId', 'ItemInternalName', 'ItemDisplayName', 'ItemCost', 'Image', 'ForSale', 'Usage', 'IgnoreStealth', 'Covert', 'TurnCost', 'TargetDamage', 'TurnChange', 'SelfUse', 'Plural', 'OtherUsable', 'Traits', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('itemId', 'itemInternalName', 'itemDisplayName', 'itemCost', 'image', 'forSale', 'usage', 'ignoreStealth', 'covert', 'turnCost', 'targetDamage', 'turnChange', 'selfUse', 'plural', 'otherUsable', 'traits', ),
        BasePeer::TYPE_COLNAME => array (ItemPeer::ITEM_ID, ItemPeer::ITEM_INTERNAL_NAME, ItemPeer::ITEM_DISPLAY_NAME, ItemPeer::ITEM_COST, ItemPeer::IMAGE, ItemPeer::FOR_SALE, ItemPeer::USAGE, ItemPeer::IGNORE_STEALTH, ItemPeer::COVERT, ItemPeer::TURN_COST, ItemPeer::TARGET_DAMAGE, ItemPeer::TURN_CHANGE, ItemPeer::SELF_USE, ItemPeer::PLURAL, ItemPeer::OTHER_USABLE, ItemPeer::TRAITS, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ITEM_ID', 'ITEM_INTERNAL_NAME', 'ITEM_DISPLAY_NAME', 'ITEM_COST', 'IMAGE', 'FOR_SALE', 'USAGE', 'IGNORE_STEALTH', 'COVERT', 'TURN_COST', 'TARGET_DAMAGE', 'TURN_CHANGE', 'SELF_USE', 'PLURAL', 'OTHER_USABLE', 'TRAITS', ),
        BasePeer::TYPE_FIELDNAME => array ('item_id', 'item_internal_name', 'item_display_name', 'item_cost', 'image', 'for_sale', 'usage', 'ignore_stealth', 'covert', 'turn_cost', 'target_damage', 'turn_change', 'self_use', 'plural', 'other_usable', 'traits', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. ItemPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('ItemId' => 0, 'ItemInternalName' => 1, 'ItemDisplayName' => 2, 'ItemCost' => 3, 'Image' => 4, 'ForSale' => 5, 'Usage' => 6, 'IgnoreStealth' => 7, 'Covert' => 8, 'TurnCost' => 9, 'TargetDamage' => 10, 'TurnChange' => 11, 'SelfUse' => 12, 'Plural' => 13, 'OtherUsable' => 14, 'Traits' => 15, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('itemId' => 0, 'itemInternalName' => 1, 'itemDisplayName' => 2, 'itemCost' => 3, 'image' => 4, 'forSale' => 5, 'usage' => 6, 'ignoreStealth' => 7, 'covert' => 8, 'turnCost' => 9, 'targetDamage' => 10, 'turnChange' => 11, 'selfUse' => 12, 'plural' => 13, 'otherUsable' => 14, 'traits' => 15, ),
        BasePeer::TYPE_COLNAME => array (ItemPeer::ITEM_ID => 0, ItemPeer::ITEM_INTERNAL_NAME => 1, ItemPeer::ITEM_DISPLAY_NAME => 2, ItemPeer::ITEM_COST => 3, ItemPeer::IMAGE => 4, ItemPeer::FOR_SALE => 5, ItemPeer::USAGE => 6, ItemPeer::IGNORE_STEALTH => 7, ItemPeer::COVERT => 8, ItemPeer::TURN_COST => 9, ItemPeer::TARGET_DAMAGE => 10, ItemPeer::TURN_CHANGE => 11, ItemPeer::SELF_USE => 12, ItemPeer::PLURAL => 13, ItemPeer::OTHER_USABLE => 14, ItemPeer::TRAITS => 15, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ITEM_ID' => 0, 'ITEM_INTERNAL_NAME' => 1, 'ITEM_DISPLAY_NAME' => 2, 'ITEM_COST' => 3, 'IMAGE' => 4, 'FOR_SALE' => 5, 'USAGE' => 6, 'IGNORE_STEALTH' => 7, 'COVERT' => 8, 'TURN_COST' => 9, 'TARGET_DAMAGE' => 10, 'TURN_CHANGE' => 11, 'SELF_USE' => 12, 'PLURAL' => 13, 'OTHER_USABLE' => 14, 'TRAITS' => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('item_id' => 0, 'item_internal_name' => 1, 'item_display_name' => 2, 'item_cost' => 3, 'image' => 4, 'for_sale' => 5, 'usage' => 6, 'ignore_stealth' => 7, 'covert' => 8, 'turn_cost' => 9, 'target_damage' => 10, 'turn_change' => 11, 'self_use' => 12, 'plural' => 13, 'other_usable' => 14, 'traits' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = ItemPeer::getFieldNames($toType);
        $key = isset(ItemPeer::$fieldKeys[$fromType][$name]) ? ItemPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(ItemPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, ItemPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return ItemPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. ItemPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ItemPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ItemPeer::ITEM_ID);
            $criteria->addSelectColumn(ItemPeer::ITEM_INTERNAL_NAME);
            $criteria->addSelectColumn(ItemPeer::ITEM_DISPLAY_NAME);
            $criteria->addSelectColumn(ItemPeer::ITEM_COST);
            $criteria->addSelectColumn(ItemPeer::IMAGE);
            $criteria->addSelectColumn(ItemPeer::FOR_SALE);
            $criteria->addSelectColumn(ItemPeer::USAGE);
            $criteria->addSelectColumn(ItemPeer::IGNORE_STEALTH);
            $criteria->addSelectColumn(ItemPeer::COVERT);
            $criteria->addSelectColumn(ItemPeer::TURN_COST);
            $criteria->addSelectColumn(ItemPeer::TARGET_DAMAGE);
            $criteria->addSelectColumn(ItemPeer::TURN_CHANGE);
            $criteria->addSelectColumn(ItemPeer::SELF_USE);
            $criteria->addSelectColumn(ItemPeer::PLURAL);
            $criteria->addSelectColumn(ItemPeer::OTHER_USABLE);
            $criteria->addSelectColumn(ItemPeer::TRAITS);
        } else {
            $criteria->addSelectColumn($alias . '.item_id');
            $criteria->addSelectColumn($alias . '.item_internal_name');
            $criteria->addSelectColumn($alias . '.item_display_name');
            $criteria->addSelectColumn($alias . '.item_cost');
            $criteria->addSelectColumn($alias . '.image');
            $criteria->addSelectColumn($alias . '.for_sale');
            $criteria->addSelectColumn($alias . '.usage');
            $criteria->addSelectColumn($alias . '.ignore_stealth');
            $criteria->addSelectColumn($alias . '.covert');
            $criteria->addSelectColumn($alias . '.turn_cost');
            $criteria->addSelectColumn($alias . '.target_damage');
            $criteria->addSelectColumn($alias . '.turn_change');
            $criteria->addSelectColumn($alias . '.self_use');
            $criteria->addSelectColumn($alias . '.plural');
            $criteria->addSelectColumn($alias . '.other_usable');
            $criteria->addSelectColumn($alias . '.traits');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(ItemPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Item
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ItemPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return ItemPeer::populateObjects(ItemPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            ItemPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Item $obj A Item object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getItemId();
            } // if key === null
            ItemPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Item object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Item) {
                $key = (string) $value->getItemId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Item object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(ItemPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Item Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(ItemPeer::$instances[$key])) {
                return ItemPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (ItemPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        ItemPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to item
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = ItemPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = ItemPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ItemPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Item object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = ItemPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = ItemPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + ItemPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ItemPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            ItemPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(ItemPeer::DATABASE_NAME)->getTable(ItemPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseItemPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseItemPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new ItemTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return ItemPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Item or Criteria object.
     *
     * @param      mixed $values Criteria or Item object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Item object
        }

        if ($criteria->containsKey(ItemPeer::ITEM_ID) && $criteria->keyContainsValue(ItemPeer::ITEM_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ItemPeer::ITEM_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Item or Criteria object.
     *
     * @param      mixed $values Criteria or Item object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(ItemPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(ItemPeer::ITEM_ID);
            $value = $criteria->remove(ItemPeer::ITEM_ID);
            if ($value) {
                $selectCriteria->add(ItemPeer::ITEM_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(ItemPeer::TABLE_NAME);
            }

        } else { // $values is Item object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the item table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(ItemPeer::TABLE_NAME, $con, ItemPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ItemPeer::clearInstancePool();
            ItemPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Item or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Item object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            ItemPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Item) { // it's a model object
            // invalidate the cache for this single object
            ItemPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ItemPeer::DATABASE_NAME);
            $criteria->add(ItemPeer::ITEM_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                ItemPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            ItemPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Item object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Item $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ItemPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ItemPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(ItemPeer::DATABASE_NAME, ItemPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Item
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = ItemPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(ItemPeer::DATABASE_NAME);
        $criteria->add(ItemPeer::ITEM_ID, $pk);

        $v = ItemPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Item[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(ItemPeer::DATABASE_NAME);
            $criteria->add(ItemPeer::ITEM_ID, $pks, Criteria::IN);
            $objs = ItemPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseItemPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseItemPeer::buildTableMap();

