<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\DataAccessObject;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\ValueObject;

class UnableToSaveException extends \Exception{
}

/**
 * Creates the player value objects.
 * Essentially it acts as the model (creator) if Model-View-Controller were in play.
 */
class PlayerDAO extends DataAccessObject {

	/**
	 * Assigns and holds the connection to the db.
	 */
	public function __construct() {
		$this->m_dbconn = DatabaseConnection::getInstance();
		$this->_vo_obj_name = 'PlayerVO';
		$this->_vo_fields = array();
		$vo = new \ReflectionClass(new PlayerVO());

		foreach ($vo->getProperties() AS $reflectionProperty) {
            $this->_vo_fields[] = $reflectionProperty->name;
		}

		$this->_id_field = 'player_id';
		$this->_table = 'players JOIN class ON class_id = _class_id';
		$this->_table_for_saving = 'players';
		$this->setReadOnlyFields(['identity', 'class_name', 'theme']);
	}

	/**
	 * Save the changes made to the data to the database.
	 */
	public function save(ValueObject $vo) {
		if (empty($vo)) {
			throw new \UnableToSaveException('Uninitialized character is unable to be saved.');

		}

		$vo2 = clone $vo; // Make cloned copy of the vo

		return parent::save($vo2);
	}
}
