<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\DatabaseAccessObject;
use NinjaWars\core\data\SkillVO;

/*
 * Creates the skill value objects.
 * Essentially it acts as the model (creator) if Model-View-Controller were in play.
 */
class SkillDAO extends DataAccessObject {

	/*
	 * Assigns and holds the connection to the db.
	 */
	public function __construct() {
		$this->m_dbconn = DatabaseConnection::getInstance();
		$this->_vo_obj_name = 'SkillVO';
		$this->_vo_fields = array();
		$vo = new \ReflectionClass(new SkillVO());
		$vo = $vo->getProperties();

		foreach ($vo AS $reflectionProperty)
		{
			$this->_vo_fields[] = $reflectionProperty->name;
		}

		$this->_id_field = 'skill_id';
		$this->_table = 'skill';
	}

	/** 
	 * Return skills for a PC
	 * @param int $p_classID the integer representing the class with skills
	 * @param int $p_level   The level of the user to check fulfillment of level requirements
	 */
	public function getSkillsByClass($p_classID, $p_level = 0) {
		$query = 'SELECT skill_id, skill_display_name, skill_internal_name, skill_type FROM skill LEFT JOIN class_skill ON skill_id = _skill_id WHERE COALESCE(_class_id, :classID1) = :classID2 AND skill_is_active ';

		if ($p_level > 0) {
			$query .= ' AND COALESCE(class_skill_level, skill_level) <= :level2';
		}

		$statement = DatabaseConnection::$pdo->prepare($query.' ORDER BY _class_id');
		$statement->bindValue(':classID1', $p_classID);
		$statement->bindValue(':classID2', $p_classID);

		if ($p_level > 0) {
			$statement->bindValue(':level1', $p_level);
			$statement->bindValue(':level2', $p_level);
		}

		$statement->execute();

		return $statement;
	}

	/** 
	 * Return skills for a PC, also filtered by skill type
	 * @param string $p_type    The tag type of the skill, e.g. combat, targetted
	 * @param int    $p_classID the integer representing the class with skills
	 * @param int    $p_level   The level of the user to check fulfillment of level requirements
	 */
	public function getSkillsByTypeAndClass($p_classID, $p_type, $p_level=0) {
		$query = 'SELECT skill_id, skill_display_name, skill_internal_name, skill_type FROM skill LEFT JOIN class_skill ON skill_id = _skill_id WHERE COALESCE(_class_id, :classID1) = :classID2 AND skill_is_active AND skill_type = :type ';

		if ($p_level > 0) {
			$query .= ' AND COALESCE(class_skill_level, skill_level) <= :level';
		}

		$statement = DatabaseConnection::$pdo->prepare($query.' ORDER BY _class_id, skill_display_name');
		$statement->bindValue(':classID1', $p_classID);
		$statement->bindValue(':classID2', $p_classID);
		$statement->bindValue(':type', $p_type);

		if ($p_level > 0) {
			$statement->bindValue(':level', $p_level);
		}

		$statement->execute();

		return $statement;
	}

	/**
	 * Get all skills for a listing
	 */
	public function all($type=null){
		$params = [];
		$type_where = '';
		if($type !== null){
			$type_where = 'AND skill_type = :type';
			$params[':type'] = $type;
		}
		$query = 'SELECT skill_id, skill_display_name, skill_internal_name, skill_type 
			FROM skill WHERE skill_is_active '.$type_where.' 
			ORDER BY skill_type, skill_display_name';
		return query($query, $params);
	}
}
