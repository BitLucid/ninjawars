<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'account_players' table.
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
class AccountPlayersTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.AccountPlayersTableMap';

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
        $this->setName('account_players');
        $this->setPhpName('AccountPlayers');
        $this->setClassname('deploy\\model\\AccountPlayers');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('_account_id', 'AccountId', 'INTEGER' , 'accounts', 'account_id', true, null, null);
        $this->addForeignPrimaryKey('_player_id', 'PlayerId', 'INTEGER' , 'players', 'player_id', true, null, null);
        $this->addColumn('last_login', 'LastLogin', 'TIMESTAMP', true, null, 'now()');
        $this->addColumn('created_date', 'CreatedDate', 'TIMESTAMP', true, null, 'now()');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Accounts', 'deploy\\model\\Accounts', RelationMap::MANY_TO_ONE, array('_account_id' => 'account_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Players', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('_player_id' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // AccountPlayersTableMap
