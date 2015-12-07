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
	private $avatarUrl;
	private $description;
	private $founder;

    public function __construct($p_id=null, $p_name=null, $data=null) {
        $this->setID($p_id);
        if(!$p_name){
            $p_name = $this->name_from_id($p_id);
        }
        $this->setName($p_name);
        if($data){
        	$this->setAvatarUrl($data['clan_avatar_url']);
        	$this->setDescription($data['description']);
        	$this->setFounder($data['clan_founder']);
        }
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

    /**
     * @return int
    **/
    public function getLeaderID() {
        $leader_info = $this->getLeaderInfo();
        return $leader_info['player_id'];
    }

    /**
     * @return string
    **/
    public function getFounder(){
        if(!$this->founder){
            $this->founder = query_item('select clan_founder from clan where clan_id = :id', [':id'=>$this->getId()]);
        }
        return $this->founder;
    }

    public function setDescription($desc){
    	$this->description = (string) $desc;
    }

    public function getDescription(){
    	return $this->description;
    }

    public function setFounder($founder){
        $this->founder = $founder;
    }

    /**
     * @return string
    **/
    public function getAvatarUrl(){
    	return $this->avatarUrl;
    }

    public function setAvatarUrl($url){
    	$this->avatarUrl = $url;
    }

    // End of getters and setters.

    public function name_from_id($id){
        return query_item('select clan_name from clan where clan_id = :id', [':id'=>[$id, PDO::PARAM_INT]]);
    }

    public function getLeaderInfo() {
        return get_clan_leader_info($this->getID());
    }

    public function addMember(Player $ninja, Player $adder){
        if($this->hasMember($ninja->id())){
            return 'That ninja is already a member of the clan.';
        }
    	// Not an insert_query because there is no sequence involved or needed.
    	query('insert into clan_player (_clan_id, _player_id) values (:c, :p)', [':c'=>$this->id(), ':p'=>$ninja->id()]);
    	query('update players set verification_number = :new_num where player_id = :id', [':new_num'=>rand(1, 999999), ':id'=>$ninja->id()]);
    	send_message($adder->id(), $ninja->id(),"CLAN: You have been accepted into ".$this->getName());
    	return true;
    }

    /**
     * Passively invite a character to a clan with a message and link.
     * @return string
    **/
    public function invite(Player $p_target, Player $p_inviter) {
        $failure_reason = null;

        if (!$p_target || empty($p_target)) {
            return $failure_reason = 'No such ninja.';
        }

        $active = $p_target->isActive();

        if (!$active) {
            $failure_error = 'That ninja is not active.';
        } else {
            $invite_msg = $p_inviter->name().' has invited you into their clan, '.$this->getName().'. '
            .'To accept, choose their clan '.$this->getName().' on the '
            .message_url('clan.php?command=view&clan_id='.$this->getID(), 'clan joining page').'.';

            send_message($p_inviter->id(), $p_target->id(), $invite_msg);
            $failure_error = null;
        }
        return $failure_error;
    }

    /**
     * For when a player chooses to leave their clan
     * of their own volition.
    **/
    public function leave(Player $ninja){
    	$this->kickMember($ninja->id(), $ninja, $self_leave=true);
    }

    /**
     * When a leader removes a member without choice.
    **/
    public function kickMember($p_playerID, Player $kicker, $self_leave=false) {
        $today = date("F j, Y, g:i a");
        query("DELETE FROM clan_player WHERE _player_id = :player AND _clan_id = :clan",
        	[':player'=>$p_playerID, ':clan'=>$this->getID()]);
        if($self_leave){
        	$msg = "You have been kicked out of ".$this->getName()." by ".$kicker->name()." on $today.";
        } else {
        	$msg = "You have left clan ".$this->getName()." on $today.";
        }
        send_message($kicker->id(), $p_playerID, $msg);

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

    public function promoteMember($ninja_id) {
    	$updated = update_query('update clan_player set member_level = (member_level + 1) where _player_id = :pid', [':pid'=>$ninja_id]);
    	return (bool)$updated;
    }

    /**
     * Get the members of a clan, 
     **/
    public function getMembers(){
    	$members_array = query_array(
	    		'SELECT uname, accounts.active_email as email, clan_name, level, days, clan_founder, player_id, member_level
				FROM clan JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id JOIN players ON player_id = clan_player._player_id 
				join account_players on player_id = account_players._player_id join accounts on account_id = _account_id 
				AND active = 1 ORDER BY level, health DESC',
				[':clan_id'=>$this->id()]
			);

		$max = query_item('SELECT max(level) AS max
			FROM clan
			JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id
			JOIN players ON player_id = _player_id AND active = 1',
			[':clan_id'=>$this->id()]);

		// Modify the members by reference
		foreach ($members_array as &$member) {
			$member['leader'] = false;
			$member['size'] = floor( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) / $max) * 2) + 1;

			// Calc the member display size based on their level relative to the max.
			if ($member['member_level'] >= 1) {
			    $member['leader'] = true;
				$member['size'] = max($member['size'] + 2, 3);
			}
			$member['gravatar_url'] = generate_gravatar_url($member['player_id']);
		}

		return $members_array;
    }
}
