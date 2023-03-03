<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\DataAccessObject;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\ValueObject;
use NinjaWars\core\UnableToSaveException;

/**
 * Creates the player value objects.
 * Essentially it acts as the model (creator) if Model-View-Controller were in play.
 */
#[\AllowDynamicProperties]
class PlayerDAO extends DataAccessObject {
    /**
     * Assigns and holds the connection to the db.
     */
    public function __construct() {
        $this->m_dbconn = DatabaseConnection::getInstance();
        $this->_vo_obj_name = 'PlayerVO';
        $this->_vo_fields = [];
        $vo = new \ReflectionClass(new PlayerVO());

        foreach ($vo->getProperties() as $reflectionProperty) {
            $this->_vo_fields[] = $reflectionProperty->name;
        }

        $this->_id_field = 'player_id';
        $this->_table = 'players JOIN class ON class_id = _class_id';
        $this->_table_for_saving = 'players';
        $this->setReadOnlyFields(['identity', 'class_name', 'theme']);
    }
}
