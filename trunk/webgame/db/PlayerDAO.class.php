<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
require_once(DB_ROOT . "PlayerVO.class.php");


/*
 * Creates the player value objects.
 * Essentially it acts as the model (creator) if Model-View-Controller were in play.
 */
class PlayerDAO {
	var $_sql; // *** DBAccess object.
	
	var $_vo_obj_name = 'PlayerVO';
	var $_vo_fields = array(
		'player_id', 'uname', 'pname', 'health', 'strength', 'gold',
	  	'messages', 'kills', 'turns', 'confirm', 'confirmed', 'email',
	  	'class', 'level', 'status', 'member', 'days', 'ip', 'bounty', 'clan',
	  	'clan_long_name', 'created_date', 'last_started_attack', 'energy',
	  	'avatar_type'
		);
		
	var $_id_field = 'player_id';
	var $_table = 'players';
	var $_last_saved_or_updated = '';
	
	/*
	 * Assigns and holds the connection to the db.
	 */
	function __construct(DBAccess &$sql) {
	    $this->_sql = $sql;// *** Not sure whether this should be by reference or not.
	}
	
	
	/*
	 * Save the changes made to the Player data to the database.
	 */
	function save(PlayerVO &$vo) {
		// *** Check the ID of the value object to see whether it was pre-existing.
	    if ($vo->{$this->_id_field} == 0) {
	    	$this->_insert($vo);
	    	$this->_last_saved_or_updated = 'saved';
	    } else {
	    	$this->_update($vo);
	    	$this->_last_saved_or_updated = 'updated';
	    }
	}


	function get($id) {
		$vo = null;
		if (!is_numeric($id)){
			return false;
		}
		assert(isset($id));
		// execute select statement
		$sel = "select ".implode(", ", $this->_vo_fields)." from ".$this->_table." where ".$this->_id_field." = '".intval($id)."'";
		assert(isset($sel));
		//var_dump($sel);
		$data = $this->_sql->QueryRow($sel);
		if(!$data){
			return null;
		}
	    // create new vo and call getFromResult
	    $vo = new $this->_vo_obj_name();
	    assert(isset($vo));
	    //var_dump($data);
	    $this->_getFromResult($vo, $data);
	    //var_dump($vo->player_id);
	    assert(isset($vo->player_id));
	    //if(DEBUG && !isset($vo->player_id)){ var_dump($vo); }
	    // return fleshed out vo.
	    return $vo;
	}

	function delete(PlayerVO &$vo) {
		$success = null;
	    // execute delete statement
		$success = $this->_sql->Delete("delete from ".$this->_table." where ".$this->_id_field." = '".intval($vo->player_id)."'");
	    // set id on vo to 0
	    $vo->{$this->_id_field} = 0;
	    return $success;
	}


	// *** private functions

	private function _getFromResult(&$vo, $data) {
	    #fill vo from the database result set
	    foreach ($this->_vo_fields AS $loopField) { // *** use fields dynamically from list.
	    	$vo->$loopField = $data[$loopField];
	    }
	}

	private function _update(&$vo) {
	    #execute update statement here
	    $up = "update ".$this->_table." set ";
	    foreach ($this->_vo_fields AS $loopField) { // Put in values from vo.
	    	$up .= $loopField." = '".$vo->$loopField."',";
	    }
	    $up = substr($up, 0, -1); // *** Remove that final comma.
	    $up .= " where ".$this->_id_field." = '".intval($vo->{$this->_id_field})."'";
	    
	    $this->_sql->Update($up);
	}

	private function _insert(&$vo) {
		//echo "Insert saving new vo...";
	    #generate id using sequence
	    $new_id = $this->_sql->nextSequenceValue($this->_id_field, $this->_table, $full=null);
	    assert(is_numeric($new_id));
	    // *** Make insert statement.
	    $in = "insert into ".$this->_table." (".implode(", ", $this->_vo_fields).") values (";
	    foreach ($this->_vo_fields AS $loopField){
	    	if ($loopField == $this->_id_field){
	    		$in .= " ".$new_id.","; // *** Insert ID as new_id.
	    	} else {
	    		$in .= " '".$vo->$loopField."',";
	    	}
	    }

	    $in = substr($in, 0, -1); // *** Remove that final comma.
	    $in .= ")"; // *** Final closing of the parentheses.
	    
	    #insert record into db
	    $this->_sql->Insert($in);
	    // The new id is set at the beginning of the function.
	    #set id on vo
	    $vo->{$this->_id_field} = $new_id;
	}
}


?>
