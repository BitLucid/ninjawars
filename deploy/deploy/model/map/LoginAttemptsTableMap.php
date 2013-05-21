<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'login_attempts' table.
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
class LoginAttemptsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.LoginAttemptsTableMap';

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
        $this->setName('login_attempts');
        $this->setPhpName('LoginAttempts');
        $this->setClassname('deploy\\model\\LoginAttempts');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('login_attempts_attempt_id_seq');
        // columns
        $this->addPrimaryKey('attempt_id', 'AttemptId', 'INTEGER', true, null, null);
        $this->addColumn('username', 'Username', 'LONGVARCHAR', false, null, null);
        $this->addColumn('ua_string', 'UaString', 'LONGVARCHAR', false, null, null);
        $this->addColumn('ip', 'Ip', 'LONGVARCHAR', false, null, null);
        $this->addColumn('successful', 'Successful', 'INTEGER', false, null, null);
        $this->addColumn('additional_info', 'AdditionalInfo', 'LONGVARCHAR', false, null, null);
        $this->addColumn('attempt_date', 'AttemptDate', 'TIMESTAMP', false, null, 'now()');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // LoginAttemptsTableMap
