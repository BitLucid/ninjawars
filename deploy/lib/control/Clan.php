<?php
/**
 * who/what/why/where
 * Ninja clans with their various members
 *
 *
 **/


class Clan
{
    private $m_id;
    private $m_name;

    public function __construct($p_id=null, $p_name=null) {
        $this->setID($p_id);
        if(!$p_name){
            $p_name = $this->name_from_id($p_id);
        }
        $this->setName($p_name);
    }

    public function getID()
    { 
        return $this->m_id; 
    }

    public function id(){
        return $this->getID();
    }

    public function getName()
    { return $this->m_name; }

    public function setID($p_id)
    { $this->m_id = (int)$p_id; }

    public function setName($p_name)
    { $this->m_name = trim($p_name); }

    public function getLeaderID() {
        $leader_info = $this->getLeaderInfo();
        return $leader_info['player_id'];
    }

    public function getFounder(){
        if(!$this->founder){
            $this->founder = query_item('select clan_founder from clan where clan_id = :id', [':id'=>$this->getId()]);
        }
        return $this->founder;
    }

    public function setDescription($desc){
    	$this->description = $desc;
    }

    public function getDescription(){
    	return $this->description;
    }

    public function setFounder($founder){
        $this->founder = $founder;
    }

    public function name_from_id($id){
        return query_item('select clan_name from clan where clan_id = :id', array(':id'=>[$id, PDO::PARAM_INT]));
    }

    public function getLeaderInfo() {
        return get_clan_leader_info($this->getID());
    }

    public function addMember(Player $player){
    	// Not an insert_query because there is no sequence involved or needed.
    	query('insert into clan_player (_clan_id, _player_id) values (:c, :p)', [':c'=>$this->id(), ':p'=>$player->id()]);
    	return true;
    }


    public function kickMember($p_playerID) {
        global $today;

        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan_player WHERE _player_id = :player AND _clan_id = :clan");
        $statement->bindValue(':player', $p_playerID);
        $statement->bindValue(':clan', $this->getID());
        $statement->execute();

        $msg = "You have been kicked out of ".$this->getName()." by ".get_username()." on $today.";

        send_message(get_user_id(), $p_playerID, $msg);
        return true;
    }

    public function hasMember($player_id){
    	$found = query_item('select _player_id from clan_player where _player_id = :pid and _clan_id = :clan_id',
    		[':pid'=>$player_id, ':clan_id'=>$this->id()]);
    	return (bool) $found;
    }

    /**
     * @returns array(int, int, ...)
    **/
    public function getMemberIds(){
    	$player_rows = query_array('select player_id from players left join clan_player on _player_id = player_id where _clan_id = :cid',
    		[':cid'=>$this->id()]);
    	$ids = array();
    	foreach($player_rows as $row){
    		$ids[] = $row['player_id'];
    	}
    	return $ids;
    }

    /**
     * Delete a clan after sending a message to all clan members.
     **/
    public function disband() {
        DatabaseConnection::getInstance();
        $leader = $this->getLeaderID();

        $message = "Your leader has disbanded your clan. You are alone again.";

        $statement = DatabaseConnection::$pdo->prepare("SELECT _player_id FROM clan_player WHERE _clan_id = :clan");
        $statement->bindValue(':clan', $this->getID());
        $statement->execute();

        while ($data = $statement->fetch()) {
            $member_id = $data[0];

            send_message($leader, $member_id, $message);
        }
        // Deletion of the clan_player connections should cascade from the deletion of the clan, at least ideally.

        $statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan WHERE clan_id = :clan");
        $statement->bindValue(':clan', $this->getID());
        $statement->execute();
    }

    /*public function promoteMember($p_playerID) {
    }*/
}
