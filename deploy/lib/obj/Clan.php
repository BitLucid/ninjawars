<?php
class Clan
{
	private $m_id;
	private $m_name;

	public function __construct($p_id, $p_name) {
		$this->setID($p_id);
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

	public function getLeader() {
		return get_clan_leader_id($this->getID());
	}

	public function disband() {
		DatabaseConnection::getInstance();
		$leader = $this->getLeader();

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
}
?>
