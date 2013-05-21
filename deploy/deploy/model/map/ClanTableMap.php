<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'clan' table.
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
class ClanTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ClanTableMap';

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
        $this->setName('clan');
        $this->setPhpName('Clan');
        $this->setClassname('deploy\\model\\Clan');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('clan_clan_id_seq');
        // columns
        $this->addPrimaryKey('clan_id', 'ClanId', 'INTEGER', true, null, null);
        $this->addColumn('clan_name', 'ClanName', 'VARCHAR', true, 255, null);
        $this->addColumn('clan_created_date', 'ClanCreatedDate', 'TIMESTAMP', true, null, 'now()');
        $this->addColumn('clan_founder', 'ClanFounder', 'LONGVARCHAR', false, null, null);
        $this->addColumn('clan_avatar_url', 'ClanAvatarUrl', 'LONGVARCHAR', false, null, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClanPlayer', 'deploy\\model\\ClanPlayer', RelationMap::ONE_TO_MANY, array('clan_id' => '_clan_id', ), 'CASCADE', 'CASCADE', 'ClanPlayers');
    } // buildRelations()

} // ClanTableMap
