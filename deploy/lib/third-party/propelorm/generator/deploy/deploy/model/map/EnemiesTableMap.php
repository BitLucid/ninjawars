<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'enemies' table.
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
class EnemiesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.EnemiesTableMap';

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
        $this->setName('enemies');
        $this->setPhpName('Enemies');
        $this->setClassname('deploy\\model\\Enemies');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('_player_id', 'PlayerId', 'INTEGER' , 'players', 'player_id', true, null, null);
        $this->addForeignPrimaryKey('_enemy_id', 'EnemyId', 'INTEGER' , 'players', 'player_id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PlayersRelatedByEnemyId', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('_enemy_id' => 'player_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('PlayersRelatedByPlayerId', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('_player_id' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // EnemiesTableMap
