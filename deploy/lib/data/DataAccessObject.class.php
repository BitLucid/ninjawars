<?php
use NinjaWars\core\data\DatabaseConnection;

abstract class DataAccessObject {
	protected $m_dbconn; // *** DatabaseConnection object.

	protected $_vo_obj_name;
	protected $_vo_fields;

	protected $_id_field;
	protected $_table;
	protected $_last_saved_or_updated = '';

	/*
	 * Assigns and holds the connection to the db.
	 */
	public function __construct() {
		$this->m_dbconn = DatabaseConnection::getInstance();
	}


	/*
	 * Save the changes made to the data to the database.
	 */
	public function save(ValueObject $vo) {
		// *** Check the ID of the value object to see whether it was pre-existing.
		if ($vo->{$this->_id_field} === null) {
			$outcome = $this->_insert($vo);
			$this->_last_saved_or_updated = 'saved';
		} else {
			$outcome = (bool) $this->_update($vo);
			$this->_last_saved_or_updated = 'updated';
		}
		return $outcome;
	}

	public function get($id) {
		if (!is_numeric($id)) {
			return false;
		}

		// execute select statement
		$sel = "SELECT ".implode(", ", $this->_vo_fields)." FROM ".$this->_table." WHERE ".$this->_id_field." = :id";

		// both of these asserts are kind of assinine
		assert(isset($id));
		assert(isset($sel));

		$statement = DatabaseConnection::$pdo->prepare($sel);
		$statement->bindValue(':id', intval($id));
		$statement->execute();

		if ( ! ($data = $statement->fetch()) ) {
			return null;
		}

		// create new vo and call getFromResult
		$vo = new $this->_vo_obj_name();
		assert(isset($vo));

		$this->_getFromResult($vo, $data);

		// return fleshed out vo.
		return $vo;
	}

	public function delete(ValueObject $vo) {
		$statement = DatabaseConnection::$pdo->prepare("DELETE FROM ".$this->_table." WHERE ".$this->_id_field." = :id");
		$statement->bindValue(':id', intval($vo->{$this->_id_field}));

		// execute delete statement
		$success = $statement->execute();
		// set id on vo to 0
		$vo->{$this->_id_field} = 0;
		return $success;
	}

	// *** private functions

	private function _getFromResult($vo, $data) {
		// fill vo from the database result set
		foreach ($this->_vo_fields AS $loopField) { // *** use fields dynamically from list.
			$vo->$loopField = $data[$loopField];
		}
	}

	private function _update($vo) {
		// execute update statement here
		$up = "UPDATE ".$this->_table_for_saving." SET ";

		$excludes = ['identity', 'class_name', 'theme'];
		foreach ($this->_vo_fields AS $loopField) { // Put in values from vo.
			if(in_array($loopField, $excludes)){
				continue;
			}
			$up .= "$loopField = :$loopField, ";
		}

		$up = rtrim($up, ', '); // *** Remove that final comma.
		$up .= " WHERE ".$this->_id_field." = :id";

		$statement = DatabaseConnection::$pdo->prepare($up);
		foreach ($this->_vo_fields AS $loopField) { // Put in values from vo.
			if(in_array($loopField, $excludes)){
				continue;
			}
			$statement->bindValue(":$loopField", $vo->$loopField);
		}

		$statement->bindValue(':id', intval($vo->{$this->_id_field}));
		$statement->execute();
		return $statement->rowCount();
	}

	private function _insert($vo) {
		// generate id using sequence
		$new_id = $this->m_dbconn->nextSequenceValue($this->_id_field, $this->_table);
		assert(is_numeric($new_id));

		// *** Make insert statement.
		$in = "INSERT INTO ".$this->_table_for_saving." (".implode(", ", $this->_vo_fields).") VALUES (";

		foreach ($this->_vo_fields AS $loopField) {
			$in .= " :$loopField,";
		}

		$in = rtrim($in, ', '); // *** Remove that final comma.
		$in .= ")"; // *** Final closing of the parentheses.

		// insert record into db
		$statement = DatabaseConnection::$pdo->prepare($in);

		foreach ($this->_vo_fields AS $loopField) {
			if ($loopField == $this->_id_field) {
				$statement->bindValue(":$loopField", $new_id); // *** Insert ID as new_id.
			} else {
				$statement->bindValue(":$loopField", $vo->$loopField);
			}
		}

		$statement->execute();

		// The new id is set at the beginning of the function.
		// set id on vo
		$vo->{$this->_id_field} = $new_id;
		return $new_id;
	}
}
