<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'duped_unames' table.
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
class DupedUnamesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.DupedUnamesTableMap';

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
        $this->setName('duped_unames');
        $this->setPhpName('DupedUnames');
        $this->setClassname('deploy\\model\\DupedUnames');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addColumn('uname', 'Uname', 'LONGVARCHAR', true, null, null);
        $this->addColumn('email', 'Email', 'LONGVARCHAR', true, null, null);
        $this->addColumn('created_date', 'CreatedDate', 'TIMESTAMP', true, null, null);
        $this->addColumn('relative_age', 'RelativeAge', 'SMALLINT', true, null, null);
        $this->addPrimaryKey('player_id', 'PlayerId', 'INTEGER', true, null, null);
        $this->addColumn('locked', 'Locked', 'BOOLEAN', true, null, false);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // DupedUnamesTableMap
