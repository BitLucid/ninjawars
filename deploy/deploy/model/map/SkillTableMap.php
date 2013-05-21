<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'skill' table.
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
class SkillTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.SkillTableMap';

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
        $this->setName('skill');
        $this->setPhpName('Skill');
        $this->setClassname('deploy\\model\\Skill');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('skill_skill_id_seq');
        // columns
        $this->addPrimaryKey('skill_id', 'SkillId', 'INTEGER', true, null, null);
        $this->addColumn('skill_level', 'SkillLevel', 'INTEGER', true, null, 1);
        $this->addColumn('skill_is_active', 'SkillIsActive', 'BOOLEAN', false, null, true);
        $this->addColumn('skill_display_name', 'SkillDisplayName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('skill_internal_name', 'SkillInternalName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('skill_type', 'SkillType', 'VARCHAR', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClassSkill', 'deploy\\model\\ClassSkill', RelationMap::ONE_TO_MANY, array('skill_id' => '_skill_id', ), 'CASCADE', 'CASCADE', 'ClassSkills');
    } // buildRelations()

} // SkillTableMap
