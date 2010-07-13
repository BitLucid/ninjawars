<?php
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(DB_ROOT . "PlayerVO.class.php");
/* Player behavior object.
 *
 * This file should make use of a private PlayerVO.class.php and PlayerDAO.class.php
 * to propegate and save its data.
 *
 * @category    Template
 * @package     char
 * @subpackage	player
 * @author      Tchalvak <ninjawarsTchalvak@gmail.com>
 * @author
 * @link        http://someLinkToExampleUsage.com/
 * TODO: Create as_array() and as_vo() functions to convert the data.
*/

class Player
{
	public $player_id;
	public $vo;
	public $status;

	public function __construct($player_id_or_username) {
		if (!is_numeric($player_id_or_username)) {
			$sel = "select player_id from players where uname = :uname limit 1";
			$this->player_id = DatabaseConnection::$pdo->prepare($sel);
			$this->player_id->bindValue(':uname', $player_id_or_username);
			$this->player_id->execute();
			$this->player_id = $this->player_id->fetchColumn();
		} else {
			$this->player_id = $player_id_or_username;
		}

		$dao = new PlayerDAO();
		$this->vo = $dao->get($this->player_id);
	}

	// Save the Player state.
	public function save() {
		$dao = new PlayerDAO();
		$dao->save($this->vo);
	}

	public function getStatus() {
		return getStatus($this->vo->uname);
	}

	public function addStatus($status_constant) {
		addStatus($this->vo->uname, $status_constant);
	}

	public function subtractStatus($status_constant) {
		subtractStatus($this->vo->uname, $status_constant);
	}

	public function getStrength() {
		$str = $this->vo->strength;

		if ($this->hasStatus(STR_UP2)) {
			return $str+(ceil($str*.25));
		} elseif ($this->hasStatus(STR_UP1)) {
			return $str+(ceil($str*.12));
		} else {
			return $str;
		}
	}

	public function hasStatus($p_status) {
		return (bool)($this->vo->status&$p_status);
	}
	
	public function isActive(){
	    $activity_threshhold = 91;
	    return (bool) (($this->vo->days)<$activity_threshhold);
	}

	public function death() {
		$this->subtractStatus(STEALTH+POISON+FROZEN+CLASS_STATE+STR_UP1+STR_UP2);
	}

	public function as_vo() {
		return $this->vo;
	}

	public function as_array() {
		return (array) $this->vo;
	}
}
?>
