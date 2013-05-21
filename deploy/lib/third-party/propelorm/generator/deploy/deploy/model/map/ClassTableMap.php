<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'class' table.
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
class ClassTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ClassTableMap';

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
        $this->setName('class');
        $this->setPhpName('Class');
        $this->setClassname('deploy\\model\\Class');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('class_class_id_seq');
        // columns
        $this->addPrimaryKey('class_id', 'ClassId', 'INTEGER', true, null, null);
        $this->addColumn('class_name', 'ClassName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('class_active', 'ClassActive', 'BOOLEAN', false, null, true);
        $this->addColumn('class_note', 'ClassNote', 'LONGVARCHAR', false, null, null);
        $this->addColumn('class_tier', 'ClassTier', 'INTEGER', true, null, 1);
        $this->addColumn('class_desc', 'ClassDesc', 'LONGVARCHAR', false, null, null);
        $this->addColumn('class_icon', 'ClassIcon', 'LONGVARCHAR', false, null, null);
        $this->addColumn('theme', 'Theme', 'VARCHAR', false, 255, null);
        $this->addColumn('identity', 'Identity', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClassSkill', 'deploy\\model\\ClassSkill', RelationMap::ONE_TO_MANY, array('class_id' => '_class_id', ), 'CASCADE', 'CASCADE', 'ClassSkills');
        $this->addRelation('Players', 'deploy\\model\\Players', RelationMap::ONE_TO_MANY, array('class_id' => '_class_id', ), null, 'CASCADE', 'Playerss');
    } // buildRelations()

} // ClassTableMap
