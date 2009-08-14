<?php
require_once(LIB_ROOT."specific/lib_inventory.php");
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

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Item Use</span>

<br><br>

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



if ($give == "on" || $give == "Give") {
  $turn_cost = 0;
  $using_item = false;
}

// Sets the page to link back to.
if ($target && $link_back == "") {$link_back = "<a href=\"player.php?player=$target\">Player Detail</a>"; }
else { $link_back = "<a href=\"inventory.php\">Inventory</a>"; }

$ignores_stealth = false;
if ($item  == "Dim Mak"  || $item == "Speed Scroll") { $ignores_stealth == true; } // TODO: Check this for problems.


// Attack Legal section
$attacker = $username;
$params = array('required_turns'=>$turn_cost, 'ignores_stealth'=>$ignores_stealth, 'self_use'=>$selfTarget);
assert(!!$selfTarget || $attacker != $target);
$AttackLegal = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error = $AttackLegal->getError();


// *** Any ERRORS prevent attacks happen here  ***
if(!$attack_allowed){ //Checks for error conditions before starting.
	echo "<div class='ninja-error centered'>$attack_error</div>"; // Display the reason the attack failed.
} else {
    if ($item == "" || $target == "")  {
        echo "You didn't choose an item/victim.\n";
    } else {   
	  $row = $sql->data;
	  if ($item_count < 1) {
	    echo "You do not have".($item? " a ".$item : ' that item').".\n";
	  } else {
            /**** MAIN SUCCESSFUL USE ****/
		      echo "Preparing to use item - <br>\n";
		      if ($give == "on" || $give == "Give") {
                  echo render_give_item($username, $target, $item);
                   
    			} else {
    			  $article = "a";
    			  
    			  // *** HP Altering ***
			  if ($item == "Fire Scroll") {
			      $target_damage = rand(20,getStrength($username)+20)+$near_level_power_increase;
			      $result        = "lose ".$target_damage." HP";
			      $victim_alive  = subtractHealth($target,$target_damage);
			    } else if ($item == "Shuriken") {
			      $target_damage = rand(1,getStrength($username))+$near_level_power_increase;
			      $result        = "lose ".$target_damage." HP";
			      $victim_alive  = subtractHealth($target,$target_damage);
			    } else if ($item == "Ice Scroll") {  
			        //Turn Altering
			      $article = "an";

			      $turns_decrease = ice_scroll_turns($targets_turns, $near_level_power_increase);
			      
			      if ($turns_decrease == 0){
			        echo 'You fail to take any turns from '.$target.'.';
			      }
			      
			      $result         = "lose ".$turns_decrease." turns";
			      subtractTurns($target,$turns_decrease);
			      $victim_alive = true;
			    } else if ($item == "Speed Scroll") {
			      $turns_increase = 6;
			      $result         = "gain $turns_increase turns";
			      changeTurns($target,$turns_increase);
			      $covert         = true;
			      $victim_alive = true;
			    } else if ($item == "Stealth Scroll") {
			      addStatus($target,STEALTH);
			      echo "<br>$target is now Stealthed.<br>\n";
			      $result = false;
			      $covert =  true;
			      $victim_alive = true;
			    } else if ($item == "Dim Mak") {
			      setHealth($target,0);
			      $covert = true;          //The Dim Mak is a covert weapon, allowing it to be used from Stealth.
			      $victim_alive = false;
			      $result = "be drained of your life-force and die!";
				  $gold_mod = 0.25;          //The Dim Mak takes away 25% of a targets' gold.
			    }
			}
		      
		      if ($result) {
			  // *** Message to display based on item type ***
			  if ($target_damage) {
			      echo "$target takes $target_damage damage from your attack!<br><br>\n";
			    } else if ($turns_decrease) {
			      echo "$target's turns reduced by $turns_decrease.<br>\n";
				  if (getTurns($target)<=0) { //Message when a target has no more turns to ice scroll away.
					  echo "$target no longer has any turns.<br>\n";
				    }
			    } else if ($turns_increase) {
			      echo "$target's turns increased by $turns_increase.<br>\n";
			    } else if ($item=="Dim Mak") {
			      echo "The life force drains from $target and they drop dead before your eyes!.<br>\n";
			    }

			  
			  if (!$victim_alive) { // Target was killed by the item.
                    if (getStatus($username) && ($target != $username) ) {   // *** SUCCESSFUL KILL ***
    				  $attacker_id = ($status_array['Stealth'] ? "A Stealthed Ninja" : $username);
    				  if (!$gold_mod) {
    					  $gold_mod = 0.15;
    				  }
    				  $loot     = round($gold_mod*getGold($target));
    				  subtractGold($target,$loot);
    				  addGold($username,$loot);
    				  addKills($username,1);
    				  echo "You have killed $target with $article $item!<br>\n";
    				  echo "You receive $loot gold from $target.<br>\n";
    				  runBountyExchange($username, $target);  //Rewards or increases bounty.
                    } else {
                      $loot = 0;
                      echo "You have comitted suicide!<br>\n";
                    }
    				
    				send_kill_mails($username, $target, $attacker_id, $article, $item, $today, $loot);
    			      
    			    } else {
    			      $attacker_id = $username;
    			    }
			  
                    if ($target != $username) {
                        $target_email_msg   = "$attacker_id has used $article $item on you at $today and caused you to $result.";
                        sendMessage($attacker_id,$target,$target_email_msg);
                    }
    			}
		      
		      $turns_to_take = 1;
		      
		      // *** remove Item ***
		      
		      echo "<br>Removing $item from your inventory.<br>\n";
		      
		      $sql->Update("UPDATE inventory set amount = amount-1 WHERE owner = '".$username."' AND item ='$item' AND amount>0"); 
		      // *** Decreases the item amount by 1.
		      
		      // Unstealth
            if (!isset($covert) && $give != "on" && $give != "Give" && getStatus($username) && $status_array['Stealth']) { //non-covert acts
                subtractStatus($username,STEALTH);
                echo "Your actions have revealed you. You are no longer stealthed.<br>\n";
            }
			if ($victim_alive == true && $using_item == true) {
			    $self_targetting = $selfTarget? '&selfTarget=1' : '';
				echo "<br><a href=\"inventory_mod.php?item=$item&target=$target{$self_targetting}\">Use $item again?</a><br>\n";  //Repeat Usage
			}
	    }
    }
}


// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take<1) {
	$turns_to_take = 1;
}
$ending_turns = subtractTurns($username, $turns_to_take);
assert($item == "Speed Scroll" || $ending_turns<$starting_turns || $starting_turns == 0);


?>

<br><br>

Return to <?echo $link_back;?>

<?php
include SERVER_ROOT."interface/footer.php";
?>
