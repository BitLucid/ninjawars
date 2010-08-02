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
		$vo = new ReflectionClass(new PlayerVO());

		foreach ($vo->getProperties() AS $reflectionProperty)
		{
			$this->_vo_fields[] = $reflectionProperty->name;
		}

		$this->_id_field = 'player_id';
		$this->_table = 'players JOIN class ON class_id = _class_id';
	}

	public function get($id) {
		$vo = parent::get($id);

		if (is_object($vo)) {
			$vo->class = $vo->class_name;
		}

		return $vo;
	}
}
?>
