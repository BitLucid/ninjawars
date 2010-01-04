<?php

class Clan
{
	private $id;
	private $clanName;
	private $leaderID;
	private $leaderName;
	//private $otherLeaderIDs[];
	//private $memberCount;
	//private $memberIDList[];
	//Clan password?
	//memberJoin method. Using ids.
	//addRequest method. Using ids.
	//removeRequest method.
	//recruit method. Using ids.

	public function __constructor($clanID=NULL)
	{
		// *** Should use the clanID to create the clan, or else use something else to create the clan.
	}

	public function getID()
	{
		$res = NULL;
		if (is_int($id))
		{
			$res = (int) $id;
		}
		return $res;
	}

	public function getClanName()
	{
		return $this->clanName;
	}

	public function getLeaderName()
	{
		return $this->leaderName();
	}

	public function getLeaderID()
	{
		return $this->leaderID;
		// *** This should get that player object and then set the leaderID and leaderName from that.
	}

	public function messageMembers()
	{
	}

	public function messageLeaders()
	{
	}
}
?>
