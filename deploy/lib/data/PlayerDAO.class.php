<?php
require_once(DB_ROOT . "DataAccessObject.class.php");
require_once(DB_ROOT . "PlayerVO.class.php");

/*
 * Creates the player value objects.
 * Essentially it acts as the model (creator) if Model-View-Controller were in play.
 */
class PlayerDAO extends DataAccessObject {

	/*
	 * Assigns and holds the connection to the db.
	 */
	public function __construct() {
		$this->m_dbconn = DatabaseConnection::getInstance();
		$this->_vo_obj_name = 'PlayerVO';
		$this->_vo_fields = array();
		$vo = new PlayerVO();
		$vo = new ReflectionClass(new PlayerVO());

		foreach ($vo->getProperties() AS $reflectionProperty){
			$this->_vo_fields[] = $reflectionProperty->name;
		}

		$this->_id_field = 'player_id';
		$this->_table = 'players JOIN class ON class_id = _class_id';
		$this->_table_for_saving = 'players';
	}

	public function get($id) {
		$vo = parent::get($id);

		if (is_object($vo)) {
			$vo->class = $vo->class_name;
		}

		return $vo;
	}

	/*
	 * Save the changes made to the data to the database.
	 */
	public function save(ValueObject $vo) {
		if(empty($vo)){
			return false;
		}
		$vo2 = clone $vo; // Make cloned copy of the vo
		// Have to unset joined class data, though not the _foreign keys, I guess.
		unset($vo2->identity);
		unset($vo2->class_name);
		unset($vo2->theme);
		parent::save($vo2);
	}
}
