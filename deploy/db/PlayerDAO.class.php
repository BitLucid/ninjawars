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
		$vo = $vo->getProperties();

		foreach ($vo AS $reflectionProperty)
		{
			$this->_vo_fields[] = $reflectionProperty->name;
		}

		$this->_id_field = 'player_id';
		$this->_table = 'players';
	}
}
?>
