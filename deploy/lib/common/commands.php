<?php

// ************************************
// ********** CLASS FUNCTIONS *********
// ************************************

function setClass($who,$new_class)
{
  global $sql;

  $sql->Update("UPDATE players SET class = '$new_class' WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['class'] = $new_class;
    }

  return $new_class;
}

function getClass($who)
{
  global $sql;

  $class = $sql->QueryItem("SELECT class FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['class'] = $class;
    }

  return $class;
}

// ************************************
// ************************************



// ************************************
// ********* HEALTH FUNCTIONS *********
// ************************************

function setHealth($who,$new_health)
{
  global $sql;

  $sql->Update("UPDATE players SET health = '$new_health' WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['health'] = $new_health;
    }

  return $new_health;
}

function getHealth($who)
{
  global $sql;

  $health = $sql->QueryItem("SELECT health FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['health'] = $health;
    }

  return $health;
}

function changeHealth($who,$amount)
{
  if (abs($amount)>0)
    {
      global $sql;

      $sql->Update("UPDATE players SET health = health + ".
		   "CASE WHEN health+$amount < 0 THEN health*(-1) ELSE $amount END ".
		   "WHERE uname  = '$who'");
      $new_health = getHealth($who);

      return $new_health;
    }
  else
    {
      return getHealth($who);
    }
}

function addHealth($who,$amount)
{
  return changeHealth($who,$amount);
}

function subtractHealth($who,$amount)
{
  return changeHealth($who,((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** GOLD FUNCTIONS **********
// ************************************

function setGold($who,$new_gold)
{
  global $sql;

  $sql->Update("UPDATE players SET gold = $new_gold WHERE uname = '$who'");

  return $new_gold;
}

function getGold($who)
{
  global $sql;

  $gold = $sql->QueryItem("SELECT gold FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['gold'] = $gold;
    }

  return $gold;
}

function changeGold($who,$amount)
{
  if (abs($amount) >  0)
    {
      global $sql;

      $sql->Update("UPDATE players SET gold = gold + ".
		   "CASE WHEN gold+$amount < 0 THEN gold*(-1) ELSE $amount END ".
		   "WHERE uname = '$who'");

      $new_gold = getGold($who);

      return $new_gold;
    }
  else
    {
      return getGold($who);
    }
}

function addGold($who,$amount)
{
  return changeGold($who,$amount);
}

function subtractGold($who,$amount)
{
  return changeGold($who,((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** TURNS FUNCTIONS *********
// ************************************

function setTurns($who,$new_turns)
{
  global $sql;

  $sql->Update("UPDATE players SET turns = $new_turns WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['turns'] = $new_turns;
    }

  return $new_turns;
}

function getTurns($who)
{
  global $sql;

  $turns = $sql->QueryItem("SELECT turns FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['turns'] = $turns;
    }

  return $turns;
}

function changeTurns($who,$amount)
{
  if (abs($amount) > 0)
    {
      global $sql;

      $sql->Update("UPDATE players SET turns = turns + ".
		   "CASE WHEN turns+$amount < 0 THEN turns*(-1) ELSE $amount END ".
		   "WHERE uname  = '$who'");

      $new_turns = getTurns($who);

      return $new_turns;
    }
  else
    {
      return getTurns($who);
    }
}

function addTurns($who,$amount)
{
  return changeTurns($who,$amount);
}

function subtractTurns($who,$amount)
{
  return changeTurns($who,((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** KILLS FUNCTIONS *********
// ************************************

function setKills($who,$new_kills)
{
  global $sql;

  $sql->Update("UPDATE players SET kills = $new_kills WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['kills'] = $new_kills;
    }

  return $new_kills;
}

function getKills($who)
{
  global $sql;

  $kills = $sql->QueryItem("SELECT kills FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['kills'] = $kills;
    }

  return $kills;
}

function changeKills($who,$amount)
{
  if (abs($amount) > 0)
    {
      global $sql;

      $sql->Update("UPDATE players SET kills = kills + ".
		   "CASE WHEN kills+$amount < 0 THEN kills*(-1) ELSE $amount END ".
		   "WHERE uname  = '$who'");

      $new_kills = getKills($who);

      return $new_kills;
    }
  else
    {
      return getKills($who);
    }
}

function addKills($who,$amount)
{
		  // *** UPDATE THE KILLS INCREASE LOG *** //
  global $sql;

	  $alreadyThere = $sql->Query("SELECT * FROM levelling_log WHERE uname='$who' AND killsdate = now() AND killpoints>0 LIMIT 1");  //Check for record.

	  $notYetANewDay=$sql->rows;  //positive if todays record already exists
	  if ($notYetANewDay != NULL)  // if record exists
		{
		  $sql->Query("UPDATE levelling_log SET killpoints=killpoints + $amount WHERE uname='$who' AND killsdate=now() AND killpoints>0");  //increase killpoints
		}
		else
		{
			$sql->Query("INSERT INTO levelling_log ( uname, killpoints, levelling, killsdate) VALUES ('$who', '$amount', '0', now())");  //create a new record for today
		}

  return changeKills($who,$amount);
}

function subtractKills($who,$amount)
{
  global $sql;
		  // *** UPDATE THE KILLS INCREASE LOG (with a negative entry) *** //

	  $alreadyThere = $sql->query("SELECT * FROM levelling_log WHERE uname='$who' AND killsdate=now() AND killpoints<0 LIMIT 1"); //check for record

	  $notYetANewDay=$sql->rows;  //positive if todays record already exists
	  if ($notYetANewDay != NULL)  // if record exists
		{
		  $sql->Query("UPDATE levelling_log SET killpoints=killpoints - $amount WHERE uname='$who' AND killsdate=now() AND killpoints<0 LIMIT 1");  //increase killpoints
		}
		else
		{
			$sql->Query("INSERT INTO levelling_log ( uname, killpoints, levelling, killsdate) VALUES ('$who', '-$amount', '0', now())");  //create a new record for today
		}


  return changeKills($who,((-1)*$amount));
}


function getKillPointsAmount($attacker,$defender)
{
  global $sql;
  $sql->Query("INSERT INTO 'dueling_log' values ('','$attacker', '$defender', '$won', '$killpoints', now())");                            //Log of Dueling information.
}


function kill($killer,$victim,$how,$what)  //This is a kill replacement function that may not yet be in use.
{
  echo "$killer has killed $victim!<br>\n";

  global $sql,$today;

  setHealth($victim,0);
  subtractStatus($victim,STEALTH+POISON+FROZEN+CLASS_STATE);

  $kill_point = 1;
  $_killer    = $killer;

  if ($how == "combat")
    {
      $msg = "$killer has killed you in combat on $today";
      $gold_mod  = .2;

      if ($what == "duel")
	{
	  $msg = "$killer has killed you in a duel on $today";
	  $gold_mod = .25;
	}
      else if  ($what == "stealth")
	{
	  $msg = "A stealthed player has killed you in combat on $today";
	  $gold_mod = .1;
	  $kill_point = 0;
	  $_killer = "A stealthed player";
	}
    }
  else if ($how == "item")
    {
      $msg  = "$killer has killed you using $what on $today";
      $gold_mod = .15;
    }
  else if ($how == "skill")
    {
      $msg  = "$killer has killed you using $what on $today";
      $gold_mod = .15;
    }

  $gold_won = takeGold($victim,$killer,$gold_mod);
  $msg.=" and taken $gold_won gold.";

  if ($kill_point){addKills($killer,$kill_point);}

  sendMessage($_killer,$victim,$msg);
  sendMessage($victim,$killer,str_replace($_killer." has","You have",str_replace("you",$victim,$msg)));
}

// ************************************
// ************************************



// ************************************
// ********** LEVEL FUNCTIONS *********
// ************************************

function setLevel($who,$new_level)
{
  global $sql;

  $sql->Update("UPDATE players SET level = $new_level WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['level'] = $new_level;
    }

  return $new_level;
}

function getLevel($who)
{
  global $sql;

  $level = $sql->QueryItem("SELECT level FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['level'] = $level;
    }

  return $level;
}

function changeLevel($who,$amount)
{
  if (abs($amount) > 0)
    {
      global $sql;

      $sql->Update("UPDATE players SET level = level+$amount WHERE uname = '$who'");

      $new_level = getLevel($who);

	  // *** UPDATE THE LEVEL INCREASE LOG *** //

	  $alreadyThere = $sql->Query("SELECT * FROM levelling_log WHERE uname='$who' AND killsdate=now()");
	  $notYetANewDay=$sql->rows;  //Throws back a row result if there is a pre-existing record.
	  if ($notYetANewDay != NULL) //if record already exists.
		{
		  $sql->Query("UPDATE levelling_log SET levelling=levelling + $amount WHERE uname='$who' AND killsdate=now() LIMIT 1");
		}
		else   //if no prior record exists, create a new one.
		{
			$sql->Query("INSERT INTO levelling_log ( uname, killpoints, levelling, killsdate) VALUES ('$who', '0', '$amount', now())");  //inserts all except the autoincrement ones
		}

      return $new_level;
    }
  else
    {
      return getLevel($who);
    }
}

function addLevel($who,$amount)
{
  return changeLevel($who,$amount);
}

function subtractLevel($who,$amount)
{
  return changeLevel($who,((-1)*$amount));
}

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

function setStatus($who,$what)
{
	//if (!is_numeric($what)) // *** If the status being sent in isn't a number...
	//{
	//  (isset($status_array[$what])? '
	//}


	global $sql;
	if (!$sql) { $sql = new DBAccess(); }
	$sql->Update("UPDATE players SET status = $what WHERE uname = '$who'");
	if ($who == $_SESSION['username'])   {
	    $_SESSION['status'] = $what;
	    if ($what == 0)	{
			echo "<br>You have returned to normal.<br>\n";
		}
		else if ($what == 1) {
			echo "<br>You have been poisoned.<br>\n";
		}
	    else if ($what == 2) {
			echo "<br>You are now stealthed.<br>\n";
		}
	}
	return $what;
}

function getStatus($who)
{
	global $sql, $status_array;
	if(!$sql){
		$sql = new DBAccess();
	}
	$status = $sql->QueryItem("SELECT status FROM players WHERE uname = '$who'");
	if (isset($_SESSION) && $who == $_SESSION['username']) {
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

function addStatus($who,$what)   //Takes in the Status in the ALL_CAPS_WORD format seen above for each.
{
	global $sql;

	$status = $sql->QueryItem("SELECT status FROM players WHERE uname = '$who'");

	if ($what < 0) {
	    return subtractStatus($who,abs($what));
	}

	if (!($status&$what)) {
	    $sql->Update("UPDATE players SET status = status+$what WHERE uname = '$who'");

	    if ($who == $_SESSION['username']) {
			$_SESSION['status']+=$what;
		}
	}
	return getStatus($who);
}

function subtractStatus($who,$what)     //Takes in the Status in the ALL_CAPS_WORD format seen above for each.
{
	global $sql;

	$status = $sql->QueryItem("SELECT status FROM players WHERE uname = '$who'");

	if ($status&$what) {
		$sql->Update("UPDATE players SET status = status-($status&$what) WHERE uname = '$who'");

		if ($who == $_SESSION['username']) {
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

function setStrength($who,$new_strength)
{
  global $sql;

  $sql->Update("UPDATE players SET strength = $new_strength WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['strength'] = $new_strength;
    }

  return $new_strength;
}

function getStrength($who)
{
  global $sql;

  $strength = $sql->QueryItem("SELECT strength FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['strength'] = $strength;
    }
  return $strength;
}

function changeStrength($who,$amount)
{
  if (abs($amount) > 0)
    {
      global $sql;

      $sql->Update("UPDATE players SET strength = strength+$amount WHERE uname = '$who'");

      $new_strength = getStrength($who);

      return $new_strength;
    }
  else
    {
      return getStrength($who);
    }
}

function addStrength($who,$amount)
{
  return changeStrength($who,$amount);
}

function subtractStrength($who,$amount)
{
  return changeStrength($who,((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********* BOUNTY FUNCTIONS *********
// ************************************

function setBounty($who,$new_bounty)
{
  global $sql;

  $sql->Update("UPDATE players SET bounty = $new_bounty WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['bounty'] = $new_bounty;
    }

  return $new_bounty;
}

function getBounty($who)
{
  global $sql;

  $bounty = $sql->QueryItem("SELECT bounty FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username'])
    {
      $_SESSION['bounty'] = $bounty;
    }

  return $bounty;
}

function changeBounty($who,$amount)
{
  if (abs($amount) > 0)
    {
      global $sql;

      $sql->Update("UPDATE players SET bounty = bounty+".
		   "CASE WHEN bounty+$amount < 0 THEN bounty*(-1) ".
		   "WHEN bounty+$amount > 5000 THEN (5000 - bounty) ".
		   "ELSE $amount END ".
		   "WHERE uname  = '$who'");

      $new_bounty = getBounty($who);

      return $new_bounty;
    }
  else
    {
      return getBounty($who);
    }
}

function addBounty($who,$amount)
{
  return changeBounty($who,$amount);
}

function subtractBounty($who,$amount)
{
  return changeBounty($who,((-1)*$amount));
}

function rewardBounty($bounty_to,$bounty_on)
{
  global $sql;

  $bounty = getBounty($bounty_on);

  setBounty($bounty_on,0);  //Sets bounty to zero.

  addGold($bounty_to,$bounty);

  return $bounty;
}

function runBountyExchange ($username, $defender)  //  *** BOUNTY EQUATION ***
{
      //  Bounty Increase equation: attacker'slevel-defender'slevel/5,roundeddown,times25goldperpoint
	$levelRatio=floor( ( getLevel($username)-getLevel($defender) )/5 );
	 if ($levelRatio>0) $bountyIncrease=$levelRatio*25;  //Avoids negative increases.
	 else $bountyIncrease=0;

	 $bountyForAttacker = rewardBounty($username,$defender); //returns a value if bounty rewarded.
	  if($bountyForAttacker)  //Reward bounty whenever available.
		 {
		  echo "You have received the $bountyForAttacker gold bounty on $defender's head for your deeds!<br>\n";
		  $bounty_msg = "You have valiantly slain the wanted criminal, $defender! For your efforts, you have been awarded $bountyForAttacker gold!";
		  sendMessage("Village Doshin",$username,$bounty_msg);
		 }
	  else if ($bountyIncrease>0)  // If Defender has no bounty and there was a level difference.
	    {
	      addBounty($username,$bountyIncrease);
	      echo "Your victim was much weaker than you. The townsfolk are angered. A bounty of ".$bountyIncrease." gold has been placed on your head!<br>\n";
	    }
}


// ************************************
// ************************************



// ************************************
// ********** CLAN FUNCTIONS **********
// ************************************

function setClan($who,$clan_name) {
  global $sql;

  $clan_long_name = $sql->QueryItem("SELECT clan_long_name FROM players WHERE clan = '$clan_name' LIMIT 1");

  $sql->Update("UPDATE players SET clan = '$clan_name', clan_long_name = '$clan_long_name' WHERE uname = '$who'");

  if ($who == $_SESSION['username']) {
      $_SESSION['clan'] = $clan_name;
    }

  return $clan_name;
}

function setClanLongName($who,$clan_long_name)
{
  global $sql;

  $sql->Update("UPDATE players SET clan_long_name = '$clan_long_name' WHERE uname = '$who'");

  return $clan_long_name;
}

function getClan($who) {
  global $sql,$status_array;

  $clan = $sql->QueryItem("SELECT clan FROM players WHERE uname = '$who'");

  if ($who == $_SESSION['username']) {
      $_SESSION['clan'] = $clan;
    }
/* //Commented out to prevent the invite status from allowing return of clan's name
  if (getStatus($who) && !$status_array['Invited'])
    {
      return $clan;
    }
  else
    {
      return "";
    }
*/
	return $clan;
}

function getClanLongName($who) {
  global $sql, $status_array;

  $clan_long_name = $sql->QueryItem("SELECT clan_long_name FROM players WHERE uname = '$who'");

  if (getStatus($who)){
      return $clan_long_name;
    } else {
      return "";
    }
}

function kick($who){
  global $today;

  $clan_long_name = getClanLongName($who);
  if (!$clan_long_name) {
      $clan_long_name = getClan($who)."'s clan";
    }

  setClan($who,"");
  setClanLongName($who,"");

  $msg = "You have been kicked out of $clan_long_name by ".$_SESSION['username']." on $today.";

  sendMessage($_SESSION['username'],$who,$msg);
}

function disbandClan($clan_name) {
  global $sql;

  setClan($clan_name,"");
  $sql->Query("SELECT uname FROM players WHERE clan = '$clan_name'");

  $message = "Your leader has disbanded your clan. You are alone again.";

	while ($data = $sql->Fetch()) {
      $name = $data[0];

      sendMessage($clan_name,$name,$message);
    }

  $sql->Update("UPDATE players SET clan = '', clan_long_name = '' WHERE clan = '$clan_name'");
}

function renameClan($clan,$new_name) {
  global $sql;

  $sql->Update("UPDATE players SET clan_long_name = '$new_name' WHERE clan = '$clan'");

  return $new_name;
}

function invitePlayer($who,$clan) {
	global $sql,$status_array;

  $current_clan = $sql->QueryItem("SELECT clan FROM players WHERE uname = '$who'");
  $player_is_confirmed = $sql->QueryItem("SELECT confirmed FROM players WHERE uname = '$who'");

	if ($current_clan == "" && $player_is_confirmed == 1 && !$status_array['Invited']) {
		$invite_msg = "$clan has invited you into their clan.  To accept, choose their clan <b>".getClanLongName($clan)."</b> on the <a href=\"clan.php?command=join\">clan joining page.</a>";
		sendMessage($clan,$who,$invite_msg);
		addStatus($who,INVITED);
		$failure_reason = "None.";
		return $failure_reason;
	}
    else if ($player_is_confirmed != 1)
	{
		$failure_reason = "That player name does not exist.";
		return $failure_reason;
	}
	else if ($current_clan != "")
	{
		$failure_reason = "That player is already in a clan.";
		return $failure_reason;
	}
	else if ($status_array['Invited'])
	{
	  $failure_reason = "That player has already been Invited into a Clan.";
	  return $failure_reason;
	}
	else
	{
		$failure_reason = "Report invitePlayer Code Error: That Player cannot be invited.";
		return $failure_reason;
	}
}

/*
function invite($who,$clan)
{
  global $sql,$status_array;

  if (getStatus($who) && !$status_array['Invited'] && getClan($who) == "")
    {
      addStatus($who,INVITED);
      setClan($who,$clan);
      setClanLongName($who,getClanLongName($clan));

      $invite_msg = "$clan has invited you into his/her clan. Accept / Decline";
      sendMessage($clan,$who,$invite_msg);
    }
  else
    {
      return false;
    }
}

function acceptInvite($who)
{
  subtractStatus($who,INVITED);

  $leader     = getClan($who);
  $accept_msg = "$who has accepted your invitation to join your clan.";
  sendMessage($who,$leader,$accept_msg);
}

function declineInvite($who)
{
  $leader      = getClan($who);
  $decline_msg = "$who has declined your invitation to join your clan.";
  sendMessage($who,$leader,$decline_msg);

  subtractStatus($who,INVITED);
  setClan($who,"");
  setClanLongName($who,"");
}
*/
// ************************************
// ************************************



// ************************************
// ******** INVENTORY FUNCTIONS *******
// ************************************

function addItem($who,$item,$quantity=1)
{
  global $sql;
  if ($quantity<0)
  	{$quantity=0;}

  $sql->Update("Update inventory set amount = amount + ".$quantity." WHERE owner = '$who' AND lower(item) =lower('$item')");
  $rows = $sql->getRowCount();
  if (!$rows)
  {
  		$sql->Insert("INSERT INTO inventory (owner, item, amount) VALUES ('$who','$item', '$quantity')");
  }
}

function removeItem($who,$item,$quantity=1)
{
  global $sql;

  $sql->Update("Update inventory set amount = amount - ".$quantity." WHERE owner = '$who' AND lower(item) =lower('$item') AND amount>1");
}


// ************************************
// ******** LOGGING FUNCTIONS *******
// ************************************


function sendLogOfDuel($attacker,$defender,$won,$killpoints)
{
  global $sql;
  $sql->Insert("INSERT INTO dueling_log values (default,'$attacker', '$defender', '$won', '$killpoints', now())");                            //Log of Dueling information.
}


// ************************************
// ******** FLAGGING FUNCTIONS *******
// ************************************

function flagPlayer($player, $flag, $note, $originatingPage)
{
  global $sql;
  $flagDetectionQuery = "SELECT flag_ID FROM flags WHERE flag='".$flag."'";
  $flagResult=$sql->Query($flagDetectionQuery);
  $flagIDArray=$sql->Fetch;
  $playerDetectionQuery = "SELECT player_ID FROM players WHERE uname='".$player."'";
  $playerResult=$sql->Query($playerDetectionQuery);
  $playerArray=$sql->Fetch();
  $addToPlayersFlagged = "INSERT IGNORE INTO players_flagged (flag_ID, player_ID, extra_notes, originating_page, timestamp) VALUES ('$flagIDArray[0]', '$playerArray[0]', '$note', '$originatingPage', now())";
  $result=$sql->Query($addToPlayersFlagged);
}


// ************************************
// ******** MESSAGE FUNCTIONS *********
// ************************************

// 
function sendMessage($from,$to,$msg,$filter=false) {
  global $sql;
  if ($filter){
  	$msg = strip_tags($msg);
  }
  $sql->Insert("INSERT INTO mail VALUES (default,'$from','$to','".sql($msg)."',now())");
}

// For true user-to-user or user-to-clan messages as opposed to events.
function sendUserMessage($from_id,$to_id,$msg) {
  global $sql;
  $sql->Insert("INSERT INTO messages VALUES (default,".sql($from_id).",".sql($to_id).",'".sql($msg)."',now())");
}


// ************************************
// ******** CHAT FUNCTIONS ************
// ************************************

function sendChat($from,$to,$msg) {
  global $sql;
  $sql->Insert("INSERT INTO chat (id, send_from, send_to, message, time) VALUES (default,'$from','$to','".sql($msg)."',now())");
}



// ************************************
// ******** ACCOUNT FUNCTIONS *********
// ************************************

function pauseAccount($who) {
	global $sql;

	if (getClan($who) == $who) {
	    disbandClan($who);
	}

	$selectStatement="SELECT email FROM players WHERE uname = '$who'";
	$results=$sql->Query($selectStatement); //Calls the select statement, returns a resource for the statements that return.

	$row=$sql->Fetch();  //Turns the first line of the resource into an array.
	$quickemail = $row[0]."PAUSED";
	$sql->Update("UPDATE players SET confirmed = 0, email= '$quickemail' WHERE uname = '$who'");
	$sql->Delete("DELETE FROM inventory WHERE owner = '$who'");
	$sql->Delete("DELETE FROM mail WHERE send_to = '$who'");
	$_SESSION['username'] = false;
	session_destroy();

	echo "Your account has been removed from Ninja Wars.
	If you wish to sign back up you may do so, though your previous ninja name will be unavailable. <br>
	If you don't plan on creating another account, we would be glad to receive an email telling us why you choose to leave the game,<br>
	Thank you.<br><br>".ADMIN_EMAIL."\n";
}

function deleteAccount($who) {
	global $sql;

	if (getClan($who) == $who) {
	    disbandClan($who);
	}

	$sql->Delete("DELETE FROM players WHERE uname = '$who'");
	$sql->Delete("DELETE FROM inventory WHERE owner = '$who'");
	$sql->Delete("DELETE FROM mail WHERE send_to = '$who'");
	$_SESSION['username'] = false;
	session_destroy();

	echo "Your account has been removed from Ninja Wars. If you wish to sign back up you may do so. <br>If not, we would be glad to receive an email telling us why you choose to leave the game,<br>Thank you.<br><br>Admin@NinjaWars.net\n";
}

// *** NEED CLAN FUNCTIONS (invite,msg,view) ***
// *** NEED SIGNUP/LOGIN FUNCTIONS ***
// *** NEED WORK FUNCTIONS ***
// *** NEED CASINO FUNCTIONS ***
// *** NEED SHRINE FUNCTIONS ***
// *** NEED COMBAT FUNCTIONS ***
// *** NEED INVENTORY FUNCTIONS ***
// *** NEED MAIL FUNCTIONS ***
// *** NEED DOJO FUNCTIONS ***


function takeGold($from,$to,$mod)
{
  global $sql;

  $victim_gold = getGold($from);
  $gold_change = round($victim_gold * $mod);
  $gold_change = ($gold_change < 0 ? 0 : $gold_change);

  addGold($to,$gold_change);
  subtractGold($from,$gold_change);

  echo "$to has acquired $gold_change gold from $from.<br>\n";

  return $gold_change;
}

?>
