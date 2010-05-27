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

/* *** Currently unused consider removing ***
function setGold($who, $new_gold) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET gold = :gold WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':gold', $new_gold);
	$statement->execute();

	return $new_gold;
}
*/

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

/* *** Currently unused consider removing ***
function setTurns($who, $new_turns) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = :turns WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':turns', $new_turns);
	$statement->execute();

	if ($who == get_username()) {
		$_SESSION['turns'] = $new_turns;
	}

	return $new_turns;
}
*/

function getTurns($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT turns FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	$turns = $statement->fetchColumn();

	if ($who == get_username()) {
		$_SESSION['turns'] = $turns;
	}

	return $turns;
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

/* *** Currently unused consider removing ***
function setKills($who, $new_kills) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET kills = $new_kills WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();

	if ($who == get_username()) {
		$_SESSION['kills'] = $new_kills;
	}

	return $new_kills;
}
*/

function getKills($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT kills FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$kills = $statement->fetchColumn();

	if ($who == get_username()) {
		$_SESSION['kills'] = $kills;
	}

	return $kills;
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

/* *** Currently unused consider removing ***
function getKillPointsAmount($attacker, $defender) {
	DatabaseConnection::getInstance();
	DatabaseConnection::$pdo->query("INSERT INTO 'dueling_log' values ('','$attacker', '$defender', '$won', '$killpoints', now())");                            //Log of Dueling information.
}
*/


/* *** Currently unused consider removing ***
function kill($killer, $victim, $how, $what) {  //This is a kill replacement function that may not yet be in use.
	echo "$killer has killed $victim!<br>\n";

	global $today;

	setHealth($victim,0);
	subtractStatus($victim,STEALTH+POISON+FROZEN+CLASS_STATE);

	$kill_point = 1;
	$_killer    = $killer;

	if ($how == "combat") {
		$msg = "$killer has killed you in combat on $today";
		$gold_mod  = .2;

		if ($what == "duel") {
			$msg = "$killer has killed you in a duel on $today";
			$gold_mod = .25;
		} else if  ($what == "stealth") {
			$msg = "A stealthed player has killed you in combat on $today";
			$gold_mod = .1;
			$kill_point = 0;
			$_killer = "A stealthed player";
		}
	} else if ($how == "item") {
		$msg  = "$killer has killed you using $what on $today";
		$gold_mod = .15;
	} else if ($how == "skill") {
		$msg  = "$killer has killed you using $what on $today";
		$gold_mod = .15;
	}

	$gold_won = takeGold($victim,$killer,$gold_mod);
	$msg.=" and taken $gold_won gold.";

	if ($kill_point) {
		addKills($killer, $kill_point);
	}

	sendMessage($_killer, $victim,$msg);
	sendMessage($victim, $killer,str_replace($_killer." has","You have",str_replace("you",$victim,$msg)));
}
*/

// ************************************
// ************************************



// ************************************
// ********** LEVEL FUNCTIONS *********
// ************************************

/* *** Currently unused consider removing ***
function setLevel($who, $new_level) {
	DatabaseConnection::getInstance();

	DatabaseConnection::$pdo->query("UPDATE players SET level = $new_level WHERE uname = '$who'");

	if ($who == get_username())
	{
		$_SESSION['level'] = $new_level;
	}

	return $new_level;
}
*/

function getLevel($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT level FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$level = $statement->fetchColumn();

	if ($who == get_username()) {
		$_SESSION['level'] = $level;
	}

	return $level;
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

/* *** Currently unused consider removing ***
function subtractLevel($who, $amount) {
	return changeLevel($who, ((-1)*$amount));
}
*/

// ************************************
// ************************************



// ************************************
// ********* STATUS FUNCTIONS *********
// ************************************
// TODO: These must be moved to a more visible place,
//and the global status_array as well.
define("STEALTH",     1);
define("POISON",      2);
define("FROZEN",      4);
define("CLASS_STATE", 8);
define("SKILL_1",     16);
define("SKILL_2",     32);
define("INVITED",     64);
$status_array;

/* *** Currently unused consider removing ***
function setStatus($who, $what) {
	//if (!is_numeric($what)) { // *** If the status being sent in isn't a number...
	//  (isset($status_array[$what])? '
	//}

	DatabaseConnection::getInstance();
	DatabaseConnection::$pdo->query("UPDATE players SET status = $what WHERE uname = '$who'");
	if ($who == get_username()) {
	    $_SESSION['status'] = $what;
	    if ($what == 0)	{
			echo "<br>You have returned to normal.<br>\n";
		} else if ($what == 1) {
			echo "<br>You have been poisoned.<br>\n";
		} else if ($what == 2) {
			echo "<br>You are now stealthed.<br>\n";
		}
	}

	return $what;
}
*/

function getStatus($who) {
	global $status_array;

	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT status FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$status = $statement->fetchColumn();

	if ($who == SESSION::get('username')) {
  		$_SESSION['status'] = $status;
	}

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

	    if ($who == get_username()) {
			$_SESSION['status'] += $what;
		}
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

		if ($who == get_username()) {
		  $_SESSION['status']-=($status&$what);
		}
	}

	return getStatus($who);
}

// ************************************
// ************************************


// ************************************
// ********* STRENGTH FUNCTIONS *******
// ************************************

/* *** Currently unused consider removing ***
function setStrength($who, $new_strength) {
	DatabaseConnection::getInstance();

	DatabaseConnection::$pdo->query>("UPDATE players SET strength = $new_strength WHERE uname = '$who'");

	if ($who == get_username()) {
		$_SESSION['strength'] = $new_strength;
	}

	return $new_strength;
}
*/

function getStrength($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT strength FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$strength = $statement->fetchColumn();

	if ($who == get_username()) {
		$_SESSION['strength'] = $strength;
	}

	return $strength;
}

function changeStrength($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET strength = strength+:amount WHERE uname = :player");
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':player', $who);
		$statement->execute();
	}

	return getStrength($who);
}

function addStrength($who,$amount) {
	return changeStrength($who, $amount);
}

/* Currently unused consider removing ***
function subtractStrength($who, $amount) {
	return changeStrength($who, ((-1) * $amount));
}
*/

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

	if ($who == get_username()) {
		$_SESSION['bounty'] = $new_bounty;
	}

	return $new_bounty;
}

function getBounty($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT bounty FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	$bounty = $statement->fetchColumn();

	if ($who == get_username()) {
		$_SESSION['bounty'] = $bounty;
	}

	return $bounty;
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

	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan (clan_id, clan_name, _creator_player_id) VALUES (:clanID, :clanName, :leader)");
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

/* *** Currently unused consider removing ***
function get_clan_by_id($p_clanID) {
	DatabaseConnection::getInstance();
	$result = DatabaseConnection::$pdo->query('SELECT clan_id, clan_name FROM clan WHERE clan_id = '.$p_clanID);

	if ($data = $result->fetch()) {
		$clan = new Clan($data['clan_id'], $data['clan_name']);
		return $clan;
	} else {
		return null;
	}
}
*/

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

function getPlayerName($p_playerID) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT uname FROM players WHERE player_id = :player");
	$statement->bindValue(':player', $p_playerID);
	$statement->execute();

	return $statement->fetchColumn();
}

function kick($p_playerID) {
	global $today;

	$clan_long_name = get_clan_by_player_id($p_playerID)->getName();

	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan_player WHERE _player_id = :player");
	$statement->bindValue(':player', $p_playerID);
	$statement->execute();

	$msg = "You have been kicked out of $clan_long_name by ".get_username()." on $today.";

	send_message(get_user_id(), $p_playerID, $msg);
}

function disbandClan($p_clanID) {
	DatabaseConnection::getInstance();

	$message = "Your leader has disbanded your clan. You are alone again.";
	$leader = get_clan_leader_id($p_clanID);

	$statement = DatabaseConnection::$pdo->prepare("SELECT _player_id FROM clan_player WHERE _clan_id = :clan");
	$statement->bindValue(':clan', $p_clanID);
	$statement->execute();

	while ($data = $statement->fetch()) {
		$member_id = $data[0];

		send_message($leader, $member_id, $message);
	}

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan WHERE clan_id = :clan");
	$statement->bindValue(':clan', $p_clanID);
	$statement->execute();
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

	if (!$current_clan && $player_is_confirmed == 1 && !$status_array['Invited']) {
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

/* *** Currently unused consider removing ***
function removeItem($who, $item, $quantity=1) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = amount - :quantity WHERE owner = :user AND lower(item) = lower(:item) AND amount > 1");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':item', $item);
	$statement->bindValue(':quantity', $quantity);
	$statement->execute();
}
*/


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


// ************************************
// ******** FLAGGING FUNCTIONS *******
// ************************************

/* *** Currently unused consider removing ***
function flagPlayer($player, $flag, $note, $originatingPage) {
	$dbconn = DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT flag_ID FROM flags WHERE flag = :flag");
	$statement->bindValue(':flag', $flag);
	$statement->execute();
	$flagID = $statement->fetchColumn();

	$user_id = get_user_id($player);

	$statement = DatabaseConnection::$pdo->prepare("INSERT IGNORE INTO players_flagged 
        (flag_ID, player_ID, extra_notes, originating_page, timestamp) 
        VALUES (:flagID, :playerID, :note, :originatingPage, now())");
	$statement->bindValue(':flagID', $flagID);
	$statement->bindValue(':playerID', $user_id);
	$statement->bindValue(':note', $note);
	$statement->bindValue(':originatingPage', $originatingPage);
	$statement->execute();
}
*/

// ************************************
// ******** MESSAGE FUNCTIONS *********
// ************************************

// event/message functions are in lib_events.

// user message functions are in lib_message now.



// ************************************
// ******** ACCOUNT FUNCTIONS *********
// ************************************

function pauseAccount($who) {
	$dbconn = DatabaseConnection::getInstance();

	$user_id = get_user_id($who);

	if (($clan = get_clan_by_player_id($user_id)) && get_clan_leader_id($clan->getID()) == $user_id) {
		disbandClan($clan->getID());
	}

	$statement = DatabaseConnection::$pdo->prepare("SELECT email FROM players WHERE player_id = :user");
	$statement->bindValue(':user', $user_id);
	$statement->execute();

	$quickemail = $email."PAUSED";
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET confirmed = 0, email = :pausedEmail WHERE player_id = :user");
	$statement->bindValue(':pausedEmail', $quickemail);
	$statement->bindValue(':user', $user_id);
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM inventory WHERE owner = :user");
	$statement->bindValue(':user', get_user_id($who));
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM mail WHERE send_to = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	session_destroy();

	echo "Your account has been removed from Ninja Wars.
	If you wish to sign back up you may do so, though your previous ninja name will be unavailable. <br>
	If you don't plan on creating another account, we would be glad to receive an email telling us why you choose to leave the game,<br>
	Thank you.<br><br>".ADMIN_EMAIL."\n";
}

/* *** Currently unused. Consider removing ***
function deleteAccount($who) {
	$dbconn = DatabaseConnection::getInstance();

	$user_id = get_user_id($who);

	if (($clan = get_clan_by_player_id($user_id)) && get_clan_leader_id($clan->getID()) == $user_id) {
		disbandClan($clan->getID());
	}

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM inventory WHERE owner = :user");
	$statement->bindValue(':user', get_user_id($who));
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM mail WHERE send_to = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("DELETE FROM players WHERE player_id = :user");
	$statement->bindValue(':user', $user_id);
	$statement->execute();

	session_destroy();

	echo "Your account has been removed from Ninja Wars. If you wish to sign back up you may do so. <br>If not, we would be glad to receive an email telling us why you choose to leave the game,<br>Thank you.<br><br>".ADMIN_EMAIL."\n";
}
*/

// *** NEED CLAN FUNCTIONS (invite,msg,view) ***
// *** NEED SIGNUP/LOGIN FUNCTIONS ***
// *** NEED WORK FUNCTIONS ***
// *** NEED CASINO FUNCTIONS ***
// *** NEED SHRINE FUNCTIONS ***
// *** NEED COMBAT FUNCTIONS ***
// *** NEED INVENTORY FUNCTIONS ***
// *** NEED MAIL FUNCTIONS ***
// *** NEED DOJO FUNCTIONS ***


function takeGold($from, $to, $mod) {
	$victim_gold = getGold($from);
	$gold_change = round($victim_gold * $mod);
	$gold_change = ($gold_change < 0 ? 0 : $gold_change);

	addGold($to, $gold_change);
	subtractGold($from, $gold_change);

	echo "$to has acquired $gold_change gold from $from.<br>\n";

	return $gold_change;
}

?>
