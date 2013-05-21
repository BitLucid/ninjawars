<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'ppl_online' table.
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
class PplOnlineTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.PplOnlineTableMap';

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
        $this->setName('ppl_online');
        $this->setPhpName('PplOnline');
        $this->setClassname('deploy\\model\\PplOnline');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('session_id', 'SessionId', 'VARCHAR', true, 255, null);
        $this->addColumn('activity', 'Activity', 'TIMESTAMP', true, null, 'now()');
        $this->addColumn('member', 'Member', 'BOOLEAN', true, null, false);
        $this->addColumn('ip_address', 'IpAddress', 'VARCHAR', true, 255, '');
        $this->addColumn('refurl', 'Refurl', 'VARCHAR', true, 255, '');
        $this->addColumn('user_agent', 'UserAgent', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // PplOnlineTableMap
