<?php

namespace deploy\model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'effects' table.
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
class EffectsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'deploy.model.map.EffectsTableMap';

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
        $this->setName('effects');
        $this->setPhpName('Effects');
        $this->setClassname('deploy\\model\\Effects');
        $this->setPackage('deploy.model');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('effects_effect_id_seq');
        // columns
        $this->addPrimaryKey('effect_id', 'EffectId', 'INTEGER', true, null, null);
        $this->addColumn('effect_identity', 'EffectIdentity', 'VARCHAR', true, 500, null);
        $this->addColumn('effect_name', 'EffectName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('effect_verb', 'EffectVerb', 'LONGVARCHAR', true, null, null);
        $this->addColumn('effect_self', 'EffectSelf', 'BOOLEAN', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ItemEffects', 'deploy\\model\\ItemEffects', RelationMap::ONE_TO_MANY, array('effect_id' => '_effect_id', ), 'RESTRICT', 'CASCADE', 'ItemEffectss');
    } // buildRelations()

} // EffectsTableMap
