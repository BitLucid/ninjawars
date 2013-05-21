<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'clan_player' table.
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
class ClanPlayerTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ClanPlayerTableMap';

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
        $this->setName('clan_player');
        $this->setPhpName('ClanPlayer');
        $this->setClassname('deploy\\model\\ClanPlayer');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('_clan_id', 'ClanId', 'INTEGER' , 'clan', 'clan_id', true, null, null);
        $this->addForeignPrimaryKey('_player_id', 'PlayerId', 'INTEGER' , 'players', 'player_id', true, null, null);
        $this->addColumn('member_level', 'MemberLevel', 'INTEGER', true, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Clan', 'deploy\\model\\Clan', RelationMap::MANY_TO_ONE, array('_clan_id' => 'clan_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Players', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('_player_id' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // ClanPlayerTableMap
