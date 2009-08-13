<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'www/')))."lib/base.inc.php"); // *** Absolute path include of everything.
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
	var $player_id;
	var $vo;
	var $status;
	
	
	public function __construct($player_id_or_username) {
		$sql = new DBAccess();
		if (!is_numeric($player_id_or_username)){
			$sel = "select player_id from players where uname = '".$player_id_or_username."' limit 1";
			$this->player_id = $sql->QueryItem($sel);
		} else {
			$this->player_id = $player_id_or_username;
		}
		$dao = new PlayerDAO($sql);
		$this->vo = $dao->get($this->player_id);
	}
	
	// Save the Player state.
	public function save(){
		$sql = new DBAccess();
		$dao = new PlayerDAO($sql);
		$dao->save($this->vo);
	}
	
	public function getStatus(){
		return getStatus($this->vo->uname);
	}
	
	public function addStatus($status_in_caps){
		addStatus($status_in_caps);
	}
	
	public function subtractStatus($status_in_caps){
		subtractStatus($status_in_caps);
	}
	
	// Magic overloading method.
	/*public function __call($method_name, $arguments){
	}*/
	
	public function as_vo(){
		return $this->vo;
	}
	
	public function as_array(){
		return (array) $this->vo;
	}
	
}


?>
