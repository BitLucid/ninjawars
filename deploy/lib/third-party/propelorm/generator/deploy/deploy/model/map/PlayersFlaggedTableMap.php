<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'players_flagged' table.
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
class PlayersFlaggedTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.PlayersFlaggedTableMap';

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
        $this->setName('players_flagged');
        $this->setPhpName('PlayersFlagged');
        $this->setClassname('deploy\\model\\PlayersFlagged');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('players_flagged_players_flagged_id_seq');
        // columns
        $this->addPrimaryKey('players_flagged_id', 'PlayersFlaggedId', 'INTEGER', true, null, null);
        $this->addColumn('player_id', 'PlayerId', 'INTEGER', false, null, null);
        $this->addColumn('flag_id', 'FlagId', 'INTEGER', false, null, null);
        $this->addColumn('timestamp', 'Timestamp', 'DATE', false, null, 'now()');
        $this->addColumn('originating_page', 'OriginatingPage', 'VARCHAR', false, 50, null);
        $this->addColumn('extra_notes', 'ExtraNotes', 'VARCHAR', false, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // PlayersFlaggedTableMap
