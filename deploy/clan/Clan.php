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

	public __constructor($clanID=NULL)
	{
		// *** Should use the clanID to create the clan, or else use something else to create the clan.
	}

	public getID()
	{
		$res = NULL;
		if (is_int($id))
		{
			$res = (int) $id;
		}
		return $res;
	}

	public getClanName()
	{
		return $this->clanName;
	}

	public getLeaderName()
	{
		return $this->leaderName();
	}

	public getLeaderID()
	{
		return $this->leaderID;
		// *** This should get that player object and then set the leaderID and leaderName from that.
	}

	public messageMembers()
	{
	}

	public messageLeaders()
	{
	}
}

?>
