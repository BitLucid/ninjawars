<?php
/*
 * Submission page from inventory.php to process results of item use.
 * 
 * @package combat
 * @subpackage skill
 */
/***********************************************
 File: inventory_mod.php
 Author: John K. Facey (NinjaLord)
 Date: Unknown
 Description: Submission page from inventory.php
              to process results of item use
 ***********************************************/


$quickstat  = "viewinv";
$private    = true;
$alive      = true;
$page_title = "Item Usage";

include "interface/header.php";
?>

<span class="brownHeading">Item Use</span>

<br /><br />

<?php
$link_back = in('link_back');
$target = in('target');
$selfTarget = in('selfTarget');
$item = in('item');
$give = in('give');

$turn_cost     = 1;
$victim_alive  = true;
$using_item    = true;
$starting_turns = getTurns($username);
$username_turns = $starting_turns;
$username_level = getLevel($username);
$item_count = $sql->QueryItem("SELECT sum(amount) FROM inventory WHERE owner = '$username' AND lower(item)=lower('$item')");
$ending_turns = null;

if ($selfTarget) {
	$target = $username;
}
$targets_turns = ($target? getTurns($target) : false);
$targets_level = ($target? getLevel($target) : NULL);
$target_hp = $sql->QueryItem("SELECT health FROM players WHERE uname = '$target'");
$target_status = getStatus($target);
$target_ip = $sql->QueryItem("SELECT ip FROM players WHERE uname = '$target'");

$target_damage = false;
$turns_decrease = false;
$turns_increase = false;

$covert			= NULL;
$gold_mod		= NULL;
$result			= NULL;
$max_power_increase = 10;
$level_difference = $targets_level - $players_level;
$level_check = $username_level - $targets_level;
$near_level_power_increase = nearLevelPowerIncrease($level_difference, $max_power_increase);

$turns_to_take = null;   // *** Take at least one turn away even on failure.

function nearLevelPowerIncrease($level_difference, $max_increase) {
	$res = 0;
	$coeff = abs($level_difference);
	if ($coeff<$max_increase)
	{
		$res = $max_increase-$coeff;
	}
	return $res;
}

if ($give == "on" || $give == "Give") {
  $turn_cost = 0;
  $using_item = false;
}

// Sets the page to link back to.
if ($target && $link_back == "") {$link_back = "<a href=\"player.php?player=$target\">Player Detail</a>";}
else {$link_back = "<a href=\"inventory.php\">Inventory</a>";}  //Results from using an item on yourself.

if (($_SESSION['ip'] != '127.0.0.1' && $_SESSION['ip'] == $target_ip) && $username!='Tchalvak' && $target!=$username)
{
	echo "You can not attack or give items to a ninja from the same domain.<br />\n";
	echo "Start your combat <a href=\"list_all_players.php\">here.</a>\n";
}
else if ($target_status && $target_status['Stealth'] && $item!="Dim Mak" && $target!=$username)
{
	echo "You are unable to attack $target because they are hidden in the darkness.";
}
else if ($username_turns >= $turn_cost)
{
  if ($item != "" || $target != "")  {   
   if ($target_hp > 0)	{
	  $row = $sql->data;
	  
	  if ($item_count > 0)
	    {
		      echo "Preparing to use item - <br />\n";
		      
		      if ($give == "on" || $give == "Give")
			{
			  addItem($target,$item,1);

			  $give_msg = "You have been given a $item by $username.";
			  sendMessage($username,$target,$give_msg);
			  
			  echo "$target will receive your $item.<br />\n";	 
			}
		      else
			{
			  $article = "a";
			  
			  // *** HP Altering ***
			  
			  if ($item == "Fire Scroll")
			    {
			      $target_damage = rand(20,getStrength($username)+20)+$near_level_power_increase;
			      $result        = "lose ".$target_damage." HP";
			      $victim_alive  = subtractHealth($target,$target_damage);
			    }
			  else if ($item == "Shuriken")
			    {
			      $target_damage = rand(1,getStrength($username))+$near_level_power_increase;
			      $result        = "lose ".$target_damage." HP";
			      $victim_alive  = subtractHealth($target,$target_damage);
			    } 			  //Turn Altering
			  else if ($item == "Ice Scroll")
			    {
			      $article = "an";
			      if ($targets_turns>50)
			      {
			      	$turns_decrease = rand(1,11)+$near_level_power_increase; // *** 1-11 + 0-10
			      }
			      elseif ($targets_turns>10)
			      {
			      	$turns_decrease = rand(1, 5)+$near_level_power_increase;
			      }
			      elseif ($targets_turns>2)
			      {
			      	$turns_decrease = rand(1, 2)+($near_level_power_increase? 1 : 0);
			      }
			      else // *** Players are always left with 1 or two turns.
			      {
					echo 'You fail to take any turns from '.$target.'.';
			      	$turns_decrease = '0';
			      }
			      
			      $result         = "lose ".$turns_decrease." turns";
			      subtractTurns($target,$turns_decrease);
			      $victim_alive = true;
			    }
			  else if ($item == "Speed Scroll")
			    {
			      $turns_increase = 6;
			      $result         = "gain $turns_increase turns";
			      changeTurns($target,$turns_increase);
			      $covert         = true;
			      $victim_alive = true;
			    }
			  else if ($item == "Stealth Scroll")
			    {
			      addStatus($target,STEALTH);
			      echo "<br />$target is now Stealthed.<br />\n";
			      $result = false;
			      $covert =  true;
			      $victim_alive = true;
			    }
			  else if ($item == "Dim Mak")  
			    {
			      setHealth($target,0);
			      $covert = true;          //The Dim Mak is a covert weapon, allowing it to be used from Stealth.
			      $victim_alive = false;
			      $result = "be drained of your life-force and die!";
				  $gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
			    }
			}
		      
		      if ($result)
			{
			  // *** Message to display based on item type ***
			  if ($target_damage)
			    {
			      echo "$target's HP reduced by $target_damage.<br /><br />\n";
			    }
			  else if ($turns_decrease)
			    {
			      echo "$target's turns reduced by $turns_decrease.<br />\n";
				  if (getTurns($target)<=0)  //Message when a target has no more turns to ice scroll away.
					{
					  echo "$target no longer has any turns.<br />\n";
					}
			    }
			  else if ($turns_increase)
			    {
			      echo "$target's turns increased by $turns_increase.<br />\n";
			    }
			  else if ($item=="Dim Mak")
			    {
			      echo "The life force drains from $target and they drop dead before your eyes!.<br />\n";
			    }
			  
			  if (!$victim_alive)
			    {
			      if (getStatus($username) && ($target != $username) )   // *** SUCCESSFUL ATTACK ***
				{
				  $attacker_id = ($status_array['Stealth'] ? "A Stealthed Ninja" : $username);

				  if (!$gold_mod)
					{
					  $gold_mod = 0.15;
					}
				  $loot     = round($gold_mod*getGold($target));
				  subtractGold($target,$loot);
				  addGold($username,$loot);
				  addKills($username,1);
				  echo "You have killed $target with $article $item!<br />\n";
				  echo "You receive $loot gold from $target.<br />\n";
				  runBountyExchange($username, $target);  //Rewards or increases bounty.
				}
			      else
				{
				  $loot = 0;
				  echo "You have comitted suicide!<br />\n";
				}
			      
			      $target_email_msg   = "You have been killed by $attacker_id with $article $item at $today and lost $loot gold.";
			      sendMessage($attacker_id,$target,$target_email_msg);
			      
			      $user_email_msg     = "You have killed $target with $article $item at $today and received $loot gold.";
			      sendMessage($target,$username,$user_email_msg);
			    }
			  else
			    {
			      $attacker_id = $username;
			    }
			  
			  if ($target != $username)
			    {
			      $target_email_msg   = "$attacker_id has used $article $item on you at $today and caused you to $result.";
			      sendMessage($attacker_id,$target,$target_email_msg);
			    }
			}
		      
		      $turns_to_take = 1;
		      
		      // *** remove Item ***
		      
		      echo "<br />Removing $item from your inventory.<br />\n";
		      
		      $sql->Update("UPDATE inventory set amount = amount-1 WHERE owner = '".$username."' AND item ='$item' AND amount>0"); // *** Decreases the amount by 1.
		      
		      // Unstealth
		      if (!isset($covert) && $give != "on" && $give != "Give" && getStatus($username) && $status_array['Stealth']) //non-covert acts
			{
			  subtractStatus($username,STEALTH);
			  echo "Your actions have revealed you. You are no longer stealthed.<br />\n";
			}
			if ($victim_alive == true && $using_item == true)
				{
				echo "<br /><a href=\"inventory_mod.php?item=$item&target=$target\">Use $item again?</a><br />\n";  //Repeat Usage
				}
	    }
	  else
	    {
	      echo "You do not have".($item? " a ".$item : ' that item').".\n";
	    }
	}
      else
	{
	  echo "$target is among the deceased.\n";
	}
    }
  else
    {
	  echo "You didn't choose an item/victim.\n";
    }
}
else
{
  echo "You have no turns. You must wait for your turns to replenish.\n";
}

// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take<1) {
	$turns_to_take = 1;
}
$ending_turns = subtractTurns($username, $turns_to_take);
assert($item == "Speed Scroll" || $ending_turns<$starting_turns || $starting_turns == 0);


?>

<br /><br />

Return to <?echo $link_back;?>

<?php
include "interface/footer.php";
?>
