<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'item_effects' table.
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
class ItemEffectsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ItemEffectsTableMap';

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
        $this->setName('item_effects');
        $this->setPhpName('ItemEffects');
        $this->setClassname('deploy\\model\\ItemEffects');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('_item_id', 'ItemId', 'INTEGER' , 'item', 'item_id', true, null, null);
        $this->addForeignPrimaryKey('_effect_id', 'EffectId', 'INTEGER' , 'effects', 'effect_id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Effects', 'deploy\\model\\Effects', RelationMap::MANY_TO_ONE, array('_effect_id' => 'effect_id', ), 'RESTRICT', 'CASCADE');
        $this->addRelation('Item', 'deploy\\model\\Item', RelationMap::MANY_TO_ONE, array('_item_id' => 'item_id', ), 'RESTRICT', 'CASCADE');
    } // buildRelations()

} // ItemEffectsTableMap
