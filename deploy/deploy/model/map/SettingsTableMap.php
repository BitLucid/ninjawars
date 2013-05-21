<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'settings' table.
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
class SettingsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.SettingsTableMap';

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
        $this->setName('settings');
        $this->setPhpName('Settings');
        $this->setClassname('deploy\\model\\Settings');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('settings_setting_id_seq');
        // columns
        $this->addPrimaryKey('setting_id', 'SettingId', 'INTEGER', true, null, null);
        $this->addPrimaryKey('player_id', 'PlayerId', 'INTEGER', true, null, null);
        $this->addColumn('settings_store', 'SettingsStore', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SettingsTableMap
