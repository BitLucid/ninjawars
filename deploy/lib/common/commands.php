<?php

// ********************* STATUS DEFINES MOVED TO STATUS_DEFINES.PHP FILE ******** //


// ********************* CLASS FUNCTIONS MOVED TO PLAYER OBJECT ******* //


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

/**
 * Returns the state of the player from the database,
 * uses a user_id if one is present, otherwise
 * defaults to the currently logged in player, but can act on any player
 * if another username is passed in.
 * @param $user user_id or username
 * @param @password Unless true, wipe the password.
**/
function get_player_info($p_id = null, $p_password = false) {
	require_once(LIB_ROOT."specific/lib_status.php");
	$id = whichever($p_id, SESSION::get('player_id')); // *** Default to current player. ***
	$player = new Player($id); // Constructor uses DAO to get player object.

	$player_data = array();

	if ($player) {
	    // Turn the player data vo into a simple array.
	    $player_data = (array) $player->vo;
		if (!$p_password) {
			unset($player_data['pname']);
		}
		
		$player_data['clan_id'] = $player->getClan()? $player->getClan()->getID() : null;

    	$player_data['hp_percent'] = min(100, round(($player_data['health']/max_health_by_level($player_data['level']))*100));
    	$player_data['exp_percent'] = min(100, round(($player_data['kills']/(($player_data['level']+1)*5))*100));
    	$player_data['status_list'] = implode(', ', get_status_list($p_id));

    	$player_data['hash'] = md5(implode($player_data));

	}


	return $player_data;
}

?>
