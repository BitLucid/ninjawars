<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'item' table.
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
class ItemTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ItemTableMap';

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
        $this->setName('item');
        $this->setPhpName('Item');
        $this->setClassname('deploy\\model\\Item');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('item_item_id_seq');
        // columns
        $this->addPrimaryKey('item_id', 'ItemId', 'INTEGER', true, null, null);
        $this->addColumn('item_internal_name', 'ItemInternalName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('item_display_name', 'ItemDisplayName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('item_cost', 'ItemCost', 'DECIMAL', true, null, null);
        $this->addColumn('image', 'Image', 'VARCHAR', false, 250, null);
        $this->addColumn('for_sale', 'ForSale', 'BOOLEAN', false, null, false);
        $this->addColumn('usage', 'Usage', 'LONGVARCHAR', false, null, null);
        $this->addColumn('ignore_stealth', 'IgnoreStealth', 'BOOLEAN', false, null, false);
        $this->addColumn('covert', 'Covert', 'BOOLEAN', false, null, false);
        $this->addColumn('turn_cost', 'TurnCost', 'INTEGER', false, null, null);
        $this->addColumn('target_damage', 'TargetDamage', 'INTEGER', false, null, null);
        $this->addColumn('turn_change', 'TurnChange', 'INTEGER', false, null, null);
        $this->addColumn('self_use', 'SelfUse', 'BOOLEAN', false, null, false);
        $this->addColumn('plural', 'Plural', 'VARCHAR', false, 20, null);
        $this->addColumn('other_usable', 'OtherUsable', 'BOOLEAN', false, null, false);
        $this->addColumn('traits', 'Traits', 'VARCHAR', false, 250, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ItemEffects', 'deploy\\model\\ItemEffects', RelationMap::ONE_TO_MANY, array('item_id' => '_item_id', ), 'RESTRICT', 'CASCADE', 'ItemEffectss');
    } // buildRelations()

} // ItemTableMap
