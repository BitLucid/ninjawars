<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'class_skill' table.
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
class ClassSkillTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.ClassSkillTableMap';

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
        $this->setName('class_skill');
        $this->setPhpName('ClassSkill');
        $this->setClassname('deploy\\model\\ClassSkill');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('_class_id', 'ClassId', 'INTEGER' , 'class', 'class_id', true, null, null);
        $this->addForeignPrimaryKey('_skill_id', 'SkillId', 'INTEGER' , 'skill', 'skill_id', true, null, null);
        $this->addColumn('class_skill_level', 'ClassSkillLevel', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Class', 'deploy\\model\\Class', RelationMap::MANY_TO_ONE, array('_class_id' => 'class_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Skill', 'deploy\\model\\Skill', RelationMap::MANY_TO_ONE, array('_skill_id' => 'skill_id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // ClassSkillTableMap
