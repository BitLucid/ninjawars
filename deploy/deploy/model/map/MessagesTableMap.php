<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'messages' table.
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
class MessagesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.MessagesTableMap';

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
        $this->setName('messages');
        $this->setPhpName('Messages');
        $this->setClassname('deploy\\model\\Messages');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('messages_message_id_seq');
        // columns
        $this->addPrimaryKey('message_id', 'MessageId', 'INTEGER', true, null, null);
        $this->addColumn('message', 'Message', 'LONGVARCHAR', true, null, null);
        $this->addColumn('date', 'Date', 'TIMESTAMP', true, null, 'now()');
        $this->addForeignKey('send_to', 'SendTo', 'INTEGER', 'players', 'player_id', false, null, null);
        $this->addForeignKey('send_from', 'SendFrom', 'INTEGER', 'players', 'player_id', false, null, null);
        $this->addColumn('unread', 'Unread', 'INTEGER', false, null, 1);
        $this->addColumn('type', 'Type', 'INTEGER', false, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PlayersRelatedBySendFrom', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('send_from' => 'player_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('PlayersRelatedBySendTo', 'deploy\\model\\Players', RelationMap::MANY_TO_ONE, array('send_to' => 'player_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // MessagesTableMap
