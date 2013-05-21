<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'players' table.
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
class PlayersTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.PlayersTableMap';

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
        $this->setName('players');
        $this->setPhpName('Players');
        $this->setClassname('deploy\\model\\Players');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('players_player_id_seq');
        // columns
        $this->addPrimaryKey('player_id', 'PlayerId', 'INTEGER', true, null, null);
        $this->addColumn('uname', 'Uname', 'VARCHAR', true, 100, null);
        $this->addColumn('pname_backup', 'PnameBackup', 'VARCHAR', false, 100, null);
        $this->addColumn('health', 'Health', 'INTEGER', true, null, 0);
        $this->addColumn('strength', 'Strength', 'INTEGER', true, null, 0);
        $this->addColumn('gold', 'Gold', 'INTEGER', true, null, 0);
        $this->addColumn('messages', 'Messages', 'LONGVARCHAR', true, null, '');
        $this->addColumn('kills', 'Kills', 'INTEGER', true, null, 0);
        $this->addColumn('turns', 'Turns', 'INTEGER', true, null, 0);
        $this->addColumn('verification_number', 'VerificationNumber', 'INTEGER', true, null, 0);
        $this->addColumn('active', 'Active', 'INTEGER', true, null, 0);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 100, '');
        $this->addColumn('level', 'Level', 'INTEGER', true, null, 0);
        $this->addColumn('status', 'Status', 'INTEGER', true, null, 0);
        $this->addColumn('member', 'Member', 'INTEGER', true, null, 0);
        $this->addColumn('days', 'Days', 'INTEGER', true, null, 0);
        $this->addColumn('ip', 'Ip', 'VARCHAR', true, 100, '');
        $this->addColumn('bounty', 'Bounty', 'INTEGER', true, null, 0);
        $this->addColumn('created_date', 'CreatedDate', 'TIMESTAMP', false, null, 'now()');
        $this->addColumn('resurrection_time', 'ResurrectionTime', 'INTEGER', true, null, 0);
        $this->addColumn('last_started_attack', 'LastStartedAttack', 'TIMESTAMP', false, null, 'now()');
        $this->addColumn('energy', 'Energy', 'INTEGER', true, null, 0);
        $this->addColumn('avatar_type', 'AvatarType', 'INTEGER', true, null, 1);
        $this->addForeignKey('_class_id', 'ClassId', 'INTEGER', 'class', 'class_id', true, null, null);
        $this->addColumn('ki', 'Ki', 'INTEGER', true, null, 0);
        $this->addColumn('stamina', 'Stamina', 'INTEGER', true, null, 0);
        $this->addColumn('speed', 'Speed', 'INTEGER', true, null, 0);
        $this->addColumn('karma', 'Karma', 'INTEGER', true, null, 0);
        $this->addColumn('kills_gained', 'KillsGained', 'INTEGER', true, null, 0);
        $this->addColumn('kills_used', 'KillsUsed', 'INTEGER', true, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Class', 'deploy\\model\\Class', RelationMap::MANY_TO_ONE, array('_class_id' => 'class_id', ), null, 'CASCADE');
        $this->addRelation('AccountPlayers', 'deploy\\model\\AccountPlayers', RelationMap::ONE_TO_MANY, array('player_id' => '_player_id', ), 'CASCADE', 'CASCADE', 'AccountPlayerss');
        $this->addRelation('ClanPlayer', 'deploy\\model\\ClanPlayer', RelationMap::ONE_TO_MANY, array('player_id' => '_player_id', ), 'CASCADE', 'CASCADE', 'ClanPlayers');
        $this->addRelation('EnemiesRelatedByEnemyId', 'deploy\\model\\Enemies', RelationMap::ONE_TO_MANY, array('player_id' => '_enemy_id', ), 'CASCADE', 'CASCADE', 'EnemiessRelatedByEnemyId');
        $this->addRelation('EnemiesRelatedByPlayerId', 'deploy\\model\\Enemies', RelationMap::ONE_TO_MANY, array('player_id' => '_player_id', ), 'CASCADE', 'CASCADE', 'EnemiessRelatedByPlayerId');
        $this->addRelation('Inventory', 'deploy\\model\\Inventory', RelationMap::ONE_TO_MANY, array('player_id' => 'owner', ), 'CASCADE', 'CASCADE', 'Inventorys');
        $this->addRelation('LevellingLog', 'deploy\\model\\LevellingLog', RelationMap::ONE_TO_MANY, array('player_id' => '_player_id', ), 'CASCADE', 'CASCADE', 'LevellingLogs');
        $this->addRelation('MessagesRelatedBySendFrom', 'deploy\\model\\Messages', RelationMap::ONE_TO_MANY, array('player_id' => 'send_from', ), 'CASCADE', 'CASCADE', 'MessagessRelatedBySendFrom');
        $this->addRelation('MessagesRelatedBySendTo', 'deploy\\model\\Messages', RelationMap::ONE_TO_MANY, array('player_id' => 'send_to', ), 'CASCADE', 'CASCADE', 'MessagessRelatedBySendTo');
    } // buildRelations()

} // PlayersTableMap
