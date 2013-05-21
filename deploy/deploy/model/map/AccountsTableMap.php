<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'accounts' table.
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
class AccountsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.AccountsTableMap';

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
        $this->setName('accounts');
        $this->setPhpName('Accounts');
        $this->setClassname('deploy\\model\\Accounts');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('accounts_account_id_seq');
        // columns
        $this->addPrimaryKey('account_id', 'AccountId', 'INTEGER', true, null, null);
        $this->addColumn('account_identity', 'AccountIdentity', 'LONGVARCHAR', true, null, null);
        $this->addColumn('phash', 'Phash', 'LONGVARCHAR', false, null, null);
        $this->addColumn('active_email', 'ActiveEmail', 'LONGVARCHAR', true, null, null);
        $this->addColumn('type', 'Type', 'INTEGER', false, null, 0);
        $this->addColumn('operational', 'Operational', 'BOOLEAN', false, null, true);
        $this->addColumn('created_date', 'CreatedDate', 'TIMESTAMP', true, null, 'now()');
        $this->addColumn('last_login', 'LastLogin', 'TIMESTAMP', false, null, null);
        $this->addColumn('last_login_failure', 'LastLoginFailure', 'TIMESTAMP', false, null, null);
        $this->addColumn('karma_total', 'KarmaTotal', 'INTEGER', true, null, 0);
        $this->addColumn('last_ip', 'LastIp', 'VARCHAR', false, 100, null);
        $this->addColumn('confirmed', 'Confirmed', 'INTEGER', true, null, 0);
        $this->addColumn('verification_number', 'VerificationNumber', 'VARCHAR', false, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AccountPlayers', 'deploy\\model\\AccountPlayers', RelationMap::ONE_TO_MANY, array('account_id' => '_account_id', ), 'CASCADE', 'CASCADE', 'AccountPlayerss');
    } // buildRelations()

} // AccountsTableMap
