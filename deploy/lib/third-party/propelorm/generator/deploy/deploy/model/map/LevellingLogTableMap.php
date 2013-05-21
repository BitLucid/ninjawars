<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'levelling_log' table.
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
class LevellingLogTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.LevellingLogTableMap';

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
        $this->setName('levelling_log');
        $this->setPhpName('LevellingLog');
        $this->setClassname('deploy\\model\\LevellingLog');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('levelling_log_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('killpoints', 'Killpoints', 'INTEGER', true, null, 0);
        $this->addColumn('levelling', 'Levelling', 'INTEGER', true, null, 0);
        $this->addColumn('killsdate', 'Killsdate', 'DATE', true, null, null);
        $this->addForeignKey('_player_id', 'PlayerId', 'INTEGER', 'players', 'player_id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Players', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('_player_id' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // LevellingLogTableMap
