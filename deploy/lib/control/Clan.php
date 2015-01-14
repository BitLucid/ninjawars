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

	public function __construct($p_id, $p_name=null) {
		$this->setID($p_id);
		if(!$p_name){
			$p_name = $this->name_from_id($p_id);
		}
		$this->setName($p_name);
	}

	public function getID()
	{ return $this->m_id; }

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
	
	public function name_from_id($id){
		return query_item('select clan_name from clan where clan_id = :id', array(':id'=>$id));
	}

	public function getLeaderInfo() {
		return get_clan_leader_info($this->getID());
	}

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

		$statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan WHERE clan_id = :clan");
		$statement->bindValue(':clan', $this->getID());
		$statement->execute();
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
	}

	public function promoteMember($p_playerID) {
	}
}
