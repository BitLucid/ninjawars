<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'inventory' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.deploy.model.map
 */
class InventoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.InventoryTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('inventory');
        $this->setPhpName('Inventory');
        $this->setClassname('deploy\\model\\Inventory');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('inventory_item_id_seq');
        // columns
        $this->addPrimaryKey('item_id', 'ItemId', 'INTEGER', true, null, null);
        $this->addColumn('amount', 'Amount', 'INTEGER', false, null, 1);
        $this->addForeignKey('owner', 'Owner', 'INTEGER', 'players', 'player_id', true, null, null);
        $this->addColumn('item_type', 'ItemType', 'INTEGER', false, null, null);
        $this->addColumn('item_type_string_backup', 'ItemTypeStringBackup', 'VARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Players', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('owner' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // InventoryTableMap
