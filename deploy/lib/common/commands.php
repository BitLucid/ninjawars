<?php

// ************************************
// ********** CLASS FUNCTIONS *********
// ************************************

function setClass($who, $new_class) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET _class_id = (select class_id FROM class WHERE class_name = :class) WHERE uname = :user");
	$statement->bindValue(':class', $new_class);
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $new_class;
}

function getClass($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT class_name FROM players JOIN class ON class_id = _class_id WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

// ************************************
// ************************************



// ************************************
// ********* HEALTH FUNCTIONS *********
// ************************************

function setHealth($who, $new_health) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET health = :health WHERE uname = :user");
	$statement->bindValue(':health', $new_health);
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $new_health;
}

function getHealth($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT health FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

function changeHealth($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		$dbconn = DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET health = health + ".
		   "CASE WHEN health + :amount < 0 THEN health*(-1) ELSE :amount2 END ".
		   "WHERE uname  = :user");
		$statement->bindValue(':user', $who);
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->execute();
	}

	return getHealth($who);
}

function addHealth($who, $amount) {
	return changeHealth($who, $amount);
}

function subtractHealth($who, $amount) {
	return changeHealth($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** GOLD FUNCTIONS **********
// ************************************

function getGold($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT gold FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

function changeGold($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) >  0) {
		$dbconn = DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET gold = gold + ".
		   "CASE WHEN gold + :amount < 0 THEN gold*(-1) ELSE :amount2 END ".
		   "WHERE uname = :user");
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->bindValue(':user', $who);
		$statement->execute();
	}

	return getGold($who);
}

function addGold($who, $amount) {
	return changeGold($who, $amount);
}

function subtractGold($who, $amount) {
	return changeGold($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** TURNS FUNCTIONS *********
// ************************************

function getTurns($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT turns FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

function changeTurns($who, $amount) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		$dbconn = DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns + ".
		   "CASE WHEN turns + :amount < 0 THEN turns*(-1) ELSE :amount2 END ".
		   "WHERE uname  = :user");
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->bindValue(':user', $who);
		$statement->execute();
    }

	return getTurns($who);
}

function addTurns($who, $amount) {
	return changeTurns($who, $amount);
}

function subtractTurns($who, $amount) {
	return changeTurns($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** KILLS FUNCTIONS *********
// ************************************

function getKills($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT kills FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	return $statement->fetchColumn();
}

function changeKills($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$update = DatabaseConnection::$pdo->prepare("UPDATE players SET kills = kills + 
		   CASE WHEN kills + :amount1 < 0 THEN kills*(-1) ELSE :amount2 END
		   WHERE uname = :user");
		$update->bindValue(':amount1', $amount);
		$update->bindValue(':amount2', $amount);
		$update->bindValue(':user', $who);
		$update->execute();
	}

	return getKills($who);
}

function addKills($who, $amount) {
	DatabaseConnection::getInstance();
	$amount = (int)$amount;
	// *** UPDATE THE KILLS INCREASE LOG *** //

	$statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE uname = :player AND killsdate = now() AND killpoints > 0 LIMIT 1");  //Check for record.
	$statement->bindValue(':player', $who);
	$statement->execute();

	$notYetANewDay = $statement->fetch();  //positive if todays record already exists

	if ($notYetANewDay != NULL) {
		// if record exists
		$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET killpoints = killpoints + :amount WHERE uname = :player AND killsdate = now() AND killpoints > 0");  //increase killpoints
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':player', $who);
	} else {
		$statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (uname, killpoints, levelling, killsdate) VALUES (:player, :amount, '0', now())");  //create a new record for today
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':player', $who);
	}

	$statement->execute();
	return changeKills($who, $amount);
}

function subtractKills($who,$amount) {
	DatabaseConnection::getInstance();
	$amount = (int)$amount;

	// *** UPDATE THE KILLS INCREASE LOG (with a negative entry) *** //

	$statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE uname = :player AND killsdate = now() AND killpoints > 0 LIMIT 1");  //Check for record.
	$statement->bindValue(':player', $who);
	$statement->execute();

	$notYetANewDay = $statement->fetch();  //positive if todays record already exists

	if ($notYetANewDay != NULL) {
		// if record exists
		$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET killpoints = killpoints - :amount WHERE uname = :player AND killsdate = now() AND killpoints < 0");  //increase killpoints
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':player', $who);
	} else {
		$statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (uname, killpoints, levelling, killsdate) VALUES (:player, :amount, '0', now())");  //create a new record for today
		$statement->bindValue(':amount', $amount*-1);
		$statement->bindValue(':player', $who);
	}

	$statement->execute();
	return changeKills($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** LEVEL FUNCTIONS *********
// ************************************

function getLevel($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT level FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	return $statement->fetchColumn();
}

function changeLevel($who, $amount) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET level = level+:amount WHERE uname = :player");
		$statement->bindValue(':player', $who);
		$statement->bindValue(':amount', $amount);
		$statement->execute();

		// *** UPDATE THE LEVEL INCREASE LOG *** //

		$statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE uname = :player AND killsdate = now()");
		$statement->bindValue(':player', $who);
		$statement->execute();

		$notYetANewDay = $statement->fetch();  //Throws back a row result if there is a pre-existing record.

		if ($notYetANewDay != NULL) {
			//if record already exists.
			$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET levelling=levelling + :amount WHERE uname = :player AND killsdate=now() LIMIT 1");
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		} else {	// if no prior record exists, create a new one.
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (uname, killpoints, levelling, killsdate) VALUES (:player, '0', :amount, now())");  //inserts all except the autoincrement ones
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		}

		$statement->execute();
	}

	return getLevel($who);
}

function addLevel($who, $amount) {
	return changeLevel($who, $amount);
}

// ************************************
// ************************************



// ************************************
// ********* STATUS FUNCTIONS *********
// ************************************
// TODO: These must be moved to a more visible place,
//and the global status_array as well.
define('STEALTH',     1);
define('POISON',      1<<1);
define('FROZEN',      1<<2);
define('CLASS_STATE', 1<<3);
define('SKILL_1',     1<<4);
define('SKILL_2',     1<<5);
define('INVITED',     1<<6);
define('STR_UP1',     1<<7);
define('STR_UP2',     1<<8);
$status_array;

function getStatus($who) {
	global $status_array;

	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT status FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$status = $statement->fetchColumn();

	$status_array['Stealth']    = ($status&STEALTH     ? 1 : 0);
	$status_array['Poison']     = ($status&POISON      ? 1 : 0);
	$status_array['Frozen']     = ($status&FROZEN      ? 1 : 0);
	$status_array['ClassState'] = ($status&CLASS_STATE ? 1 : 0);
	$status_array['Skill1']     = ($status&SKILL_1     ? 1 : 0);
	$status_array['Skill2']     = ($status&SKILL_2     ? 1 : 0);
	$status_array['Invited']    = ($status&INVITED     ? 1 : 0);
	return $status_array;
}

function addStatus($who, $what) {   //Takes in the Status in the ALL_CAPS_WORD format seen above for each.
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT status FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();

	$status = $statement->fetchColumn();

	if ($what < 0) {
	    return subtractStatus($who, abs($what));
	}

	if (!($status & $what)) {
	    $statement = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status+:what WHERE uname = :player");
		$statement->bindValue(':player', $who);
		$statement->bindValue(':what', $what);
		$statement->execute();
	}

	return getStatus($who);
}

function subtractStatus($who, $what) {     //Takes in the Status in the ALL_CAPS_WORD format seen above for each.
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT status FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$status = $statement->fetchColumn();

	if ($status&$what) {
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-(:status::int&:what::int) WHERE uname = :player");
		$statement->bindValue(':player', $who);
		$statement->bindValue(':status', $status, PDO::PARAM_INT);
		$statement->bindValue(':what', $what, PDO::PARAM_INT);
		$statement->execute();
	}

	return getStatus($who);
}

// ************************************
// ************************************


// ************************************
// ********* STRENGTH FUNCTIONS *******
// ************************************

function changeStrength($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET strength = strength+:amount WHERE uname = :player");
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':player', $who);
		$statement->execute();
	}

	$player = new Player($who);

	return $player->getStrength();
}

function addStrength($who,$amount) {
	return changeStrength($who, $amount);
}

// ************************************
// ************************************

// ************************************
// ********* BOUNTY FUNCTIONS *********
// ************************************

function setBounty($who, $new_bounty) {
	$new_bounty = (int)$new_bounty;
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET bounty = :bounty WHERE uname = :player");
	$statement->bindValue(':bounty', $new_bounty);
	$statement->bindValue(':player', $who);
	$statement->execute();

	return $new_bounty;
}

function getBounty($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT bounty FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	return $statement->fetchColumn();
}

function changeBounty($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET bounty = bounty+".
			"CASE WHEN bounty+:amount1 < 0 THEN bounty*(-1) ".
			"WHEN bounty+:amount2 > 5000 THEN (5000 - bounty) ".
			"ELSE :amount3 END ".
			"WHERE lower(uname) = :player");
		$statement->bindValue(':player', strtolower($who));
		$statement->bindValue(':amount1', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->bindValue(':amount3', $amount);
		$statement->execute();
	}

	return getBounty($who);
}

function addBounty($who, $amount) {
	return changeBounty($who, $amount);
}

function subtractBounty($who, $amount) {
	return changeBounty($who, ((-1)*$amount));
}

function rewardBounty($bounty_to, $bounty_on) {
	$bounty = getBounty($bounty_on);

	setBounty($bounty_on, 0);  //Sets bounty to zero.

	addGold($bounty_to, $bounty);

	return $bounty;
}

function runBountyExchange($username, $defender) {  //  *** BOUNTY EQUATION ***
	// *** Bounty Increase equation: (attacker's level - defender's level) / 5, rounded down, times 25 gold per point ***
	$levelRatio     = floor((getLevel($username) - getLevel($defender)) / 5);

	$bountyIncrease = ($levelRatio > 0 ? $levelRatio * 25 : 0);	//Avoids negative increases.

	$bountyForAttacker = rewardBounty($username, $defender); //returns a value if bounty rewarded.
	if ($bountyForAttacker) {
		// *** Reward bounty whenever available. ***
		echo "You have received the $bountyForAttacker gold bounty on $defender's head for your deeds!<br>\n";
		$bounty_msg = "You have valiantly slain the wanted criminal, $defender! For your efforts, you have been awarded $bountyForAttacker gold!";
		sendMessage("Village Doshin",$username,$bounty_msg);
	} else if ($bountyIncrease > 0) {
		// *** If Defender has no bounty and there was a level difference. ***
		addBounty($username, $bountyIncrease);
		echo "Your victim was much weaker than you. The townsfolk are angered. A bounty of ", $bountyIncrease, " gold has been placed on your head!<br>\n";
	}
}

// ************************************
// ************************************


// ************************************
// ********** CLAN FUNCTIONS **********
// ************************************

function createClan($p_leaderID, $p_clanName) {
	DatabaseConnection::getInstance();

	$p_clanName = trim($p_clanName);

	$result = DatabaseConnection::$pdo->query("SELECT nextval('clan_clan_id_seq')");
	$newClanID = $result->fetchColumn();

	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan (clan_id, clan_name, clan_founder) VALUES (:clanID, :clanName, :leader)");
	$statement->bindValue(':clanID', $newClanID);
	$statement->bindValue(':clanName', $p_clanName);
	$statement->bindValue(':leader', $p_leaderID);
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan_player (_player_id, _clan_id, member_level) VALUES (:leader, :clanID, 1)");
	$statement->bindValue(':clanID', $newClanID);
	$statement->bindValue(':leader', $p_leaderID);
	$statement->execute();

	return new Clan($newClanID, $p_clanName);
}

function get_clan_by_player_id($p_playerID) {
	DatabaseConnection::getInstance();
	$id = (int) $p_playerID;
	$statement = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name 
	    FROM clan 
	    JOIN clan_player ON clan_id = _clan_id 
	    WHERE _player_id = :player");
	$statement->bindValue(':player', $id);
	$statement->execute();

	if ($data = $statement->fetch()) {
		$clan = new Clan($data['clan_id'], $data['clan_name']);
		return $clan;
	} else {
		return null;
	}
}

function renameClan($p_clanID, $p_newName) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("UPDATE clan SET clan_name = :name WHERE clan_id = :clan");
	$statement->bindValue(':name', $p_newName);
	$statement->bindValue(':clan', $p_clanID);
	$statement->execute();

	return $p_newName;
}

function invitePlayer($who, $p_clanID) {
	global $status_array;
	DatabaseConnection::getInstance();

	$target_id = get_user_id($who);
	$target    = new Player($target_id);

	if (!$target_id) {
		return $failure_reason = 'No such ninja.';
	}

	$statement = DatabaseConnection::$pdo->prepare("SELECT confirmed, _clan_id FROM players LEFT JOIN clan_player ON player_id = _player_id WHERE player_id = :target");
	$statement->bindValue(':target', $target_id);
	$statement->execute();
	$data = $statement->fetch();

	$current_clan        = $data['_clan_id'];
	$player_is_confirmed = $data['confirmed'];

    $leader_info = get_clan_leader_info($p_clanID);
    $clan_name   = $leader_info['clan_name'];
    $clan_id     = $leader_info['clan_id'];
    $leader_id   = $leader_info['player_id'];
    $leader_name = $leader_info['uname'];

	if (!$current_clan && $player_is_confirmed == 1 && !$target->hasStatus(INVITED)) {
		$invite_msg = "$leader_name has invited you into their clan.  
		To accept, choose their clan $clan_name on the "
		.message_url('clan.php?command=join&clan_id='.$p_clanID, 'clan joining page').".";
		send_message($leader_id, $target_id, $invite_msg);
		addStatus($who, INVITED);
		$failure_reason = "None.";
	} else if ($player_is_confirmed != 1) {
		$failure_reason = "That player name does not exist.";
	} else if ($current_clan != "") {
		$failure_reason = "That player is already in a clan.";
	} else if ($status_array['Invited']) {
		$failure_reason = "That player has already been Invited into a Clan.";
	} else {
		$failure_reason = "Report invitePlayer Code Error: That Player cannot be invited.";
	}

	return $failure_reason;
}
// ************************************
// ************************************


// ************************************
// ******** INVENTORY FUNCTIONS *******
// ************************************

function addItem($who, $item, $quantity = 1) {
	$quantity = (int)$quantity;

	if ($quantity > 0 && !empty($item)) {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = amount + :quantity WHERE owner = :who AND lower(item) = lower(:item)");
		$statement->bindValue(':quantity', $quantity);
		$statement->bindValue(':who', get_user_id($who));
		$statement->bindValue(':item', $item);
		$statement->execute();

		$rows = $statement->rowCount();

		if (!$rows) {
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO inventory (owner, item, amount) VALUES (:user, :item, :quantity)");
			$statement->bindValue(':user', get_user_id($who));
			$statement->bindValue(':item', $item);
			$statement->bindValue(':quantity', $quantity);
			$statement->execute();
		}
	}
}

function removeItem($who, $item, $quantity=1) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = amount - :quantity WHERE owner = :user AND lower(item) = lower(:item) AND amount > 0");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':item', $item);
	$statement->bindValue(':quantity', $quantity);
	$statement->execute();
}

// ************************************
// ******** LOGGING FUNCTIONS *******
// ************************************


function sendLogOfDuel($attacker, $defender, $won, $killpoints) {
	$killpoints = (int)$killpoints;

	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO dueling_log values 
        (default, :attacker, :defender, :won, :killpoints, now())");
        //Log of Dueling information.
	$statement->bindValue(':attacker', $attacker);
	$statement->bindValue(':defender', $defender);
	$statement->bindValue(':won', $won);
	$statement->bindValue(':killpoints', $killpoints);
	$statement->execute();
}
?>
