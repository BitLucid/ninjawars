<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "NPC Battle Status";

include "interface/header.php";
?>
  
<span class="brownHeading">Battle Status</span>

<hr />

<?php
$turn_cost = 1;
$attacked  = in('attacked');
$victim    = in('victim');

if(getTurns($username) > 0) {
  if ($attacked == 1) { // Invisible bit to expect that it comes from the form.
      echo "Commencing Attack<br /><br />\n";
      
      if (getStatus($username) && $status_array['Stealth']) {
    	  subtractStatus($username,STEALTH);
	  }
      
      $attacker_str    = getStrength($username);
      $attacker_health = getHealth($username);
      $attacker_gold   = getGold($username);
      
    if ($victim == "") {
	    echo "You attack the air.\n";
	} else if ($victim == "villager") {
	  echo "The villager sees you and prepares to defend!<br /><br />\n";
	  
	  $villager_attack = rand(0,10); // *** Villager Damage ***
	  
	  if (!subtractHealth($username,$villager_attack)) {
	      echo "The villager has slain you!<br />\n";
	      echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.\n";
	    } else {
	        $just_villager = rand(0,20);
	      $villager_gold = rand(0,20);    // *** Vilager Gold    ***
	      addGold($username,$villager_gold);

	      echo "The villager is no match for you!<br />\n";
	      echo "Villager does $villager_attack points of damage.<br />\n";
	      echo "You have gained $villager_gold gold.<br />\n";
	      // Bounty or no bounty
	      if (getLevel($username) > 5){
    		  if (getLevel($username) > 20)	{
    			  echo "You slay the villager easily, leaving no trace behind!<br />\n";
    			} else {
    			  $added_bounty = floor(getLevel($username)/3);
    			  echo "You have unjustly slain a commoner! A bounty of ".($added_bounty)." gold has been placed on your head!<br />\n";
    			  addBounty($username,($added_bounty));
    			}
    		} // End of if > 5
            if(!$just_villager){ // Something beyond just a villager, drop a shuriken.
                addItem($username,'Shuriken',$quantity=1);
            	echo "The villager dropped a Shuriken.\n"; 
            }
	    }
	} else if ($victim == "samurai") {
		  $turn_cost = 1;
		  echo "<img src=\"images/samurai.png\" border=\"0\" />\n";
		  if (getLevel($username)<6) {
			  echo "You are too weak to take on the Samurai.<br />\n";
			  $turn_cost=0;
		} else if (getKills($username)<1) {
			  echo "You are too exhausted to take on the Samurai.<br />\n";
			  $turn_cost=0;
		} else {
  	  echo "The Samurai was waiting for your attack.<br /><br />\n";
	  $ninja_str=getStrength($username);
	  $ninja_health=getHealth($username);
	  $samurai_damage_array[1]=rand(1,$ninja_str);
	  $samurai_damage_array[2]=rand(10,10+round($ninja_str*1.2));
	  $does_ninja_succeed = rand(1,2);
	  if ($does_ninja_succeed==1) {
		  $samurai_damage_array[3]=rand(30+round($ninja_str*0.2),30+round($ninja_str*1.7));
		} else {
    		$samurai_damage_array[3]=($ninja_health-$samurai_damage_array[1]-$samurai_damage_array[2]);  //Instant death.
		}
	  $samurai_attack[1]="The Samurai cuts you for ".$samurai_damage_array[1]." damage.<br />\n";
 	  $samurai_attack[2]="The Samurai slashes you mercilessly for ".$samurai_damage_array[2]." damage.<br />\n";
	  $samurai_attack[3]="The Samurai thrusts his katana into you for ".$samurai_damage_array[3]." damage.<br />\n";
	   for ($i=1;$i<4&&$ninja_health>0;++$i) {
            echo "$samurai_attack[$i]\n";
            $ninja_health = $ninja_health-$samurai_damage_array[$i];
            if ($ninja_health<1) {
                echo "<br />The Samurai has slain you!<br />\n";
                echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br />\n";
            }
	    }
		if ($ninja_health>0) {                                            //Ninja still has health after all three attacks.
			echo "You use an ancient ninja strike upon the Samurai, slaying him instantly!<br /><br />\n";
			$samurai_gold=rand(50,50+$samurai_damage_array[3]+$samurai_damage_array[2]);
  	        echo "You have gained $samurai_gold gold.<br />\n";
			addGold($username,$samurai_gold);
			echo "You gain a kill point.<br />\n";
			addKills($username,1);
			if ($samurai_damage_array[3] > 100){ // If samurai damage was over 100, but the ninja lived, give a speed scroll.
    			addItem($username,'Speed Scroll',$quantity=1);
    			echo "The Samurai had a speed scroll on him. You have a new Speed Scroll in your inventory.\n"; 
    		}
			if ($samurai_damage_array[3]==$ninja_str*3) {                   //If the final damage was the exact max damage...
				addItem($username,"Dim Mak",1);
				echo "You have gained a Dim Mak from the Samurai.<br />\n";
			}
			setHealth($username,$ninja_health);
		} else { // Cheaty trickery from the samurai kills the ninja.
			setHealth($username,0);
		} // End samurai trickery
		} // End valid turns and kills for the attack.
	} else if ($victim == "merchant") {
	  echo "Merchant sees you and prepares to defend!<br /><br />\n";
	  echo "<img src=\"images/merchant.png\" border=\"0\" />";
	  
	  $merchant_attack = rand(15,35);  // *** Merchant Damage ***
	  
	  if (!subtractHealth($username,$merchant_attack)) {
	      echo "The Merchant has slain you!<br />\n";
	      echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br />\n";
	    } else { // Ninja won the fight.
	      $merchant_gold   = rand(20,70);  // *** Merchant Gold   ***
	      addGold($username,$merchant_gold);
	      
	      echo "The merchant is defeated.<br />\n";
	      echo "The Merchant did $merchant_attack points of damage.<br />\n";
	      echo "You have gained $merchant_gold gold.<br />\n";
	      
	      if ($merchant_attack > 34) {
			addItem($username,'Fire Scroll',$quantity=1);
			echo "The Merchant has dropped a Fire Scroll. You have a new Fire Scroll in your inventory.\n"; 
    	  }

	      if (getLevel($username) > 10) {
    		  $added_bounty = floor((getLevel($username)-5)/3);
    		  addBounty($username,($added_bounty*5));
    		  echo "You have slain a member of the village!
    		   A bounty of ".($added_bounty*5)." gold has been placed on your head!<br />\n";
    	  }
	    } // End of if ninja won.
	} else if ($victim == "guard") {
	  echo "The Guard sees you and prepares to defend!<br /><br />\n";
	  echo "<img src=\"images/fighter.png\" border=\"0\" />\n";
	  
	  $guard_attack = rand(1,$attacker_str+10);  // *** Guard Damage ***
	  
	  if (!subtractHealth($username,$guard_attack)){
	      echo "The Guard has slain you!<br />\n";
	      echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br />\n";
	    } else {
	      $guard_gold   = rand(1,$attacker_str+40);           // *** Guard Gold   ***
	      addGold($username,$guard_gold);
	      
	      echo "The guard is defeated!<br />\n";
	      echo "Guard does $guard_attack points of damage.<br />\n";
	      echo "You have gained $guard_gold gold.<br />\n";

	    if (getLevel($username) > 15){
		  $added_bounty = floor((getLevel($username)-10)/5);
		  echo "You have slain a member of the military!
		   A bounty of ".($added_bounty*10)." gold has been placed on your head!<br />\n";
		  addBounty($username,($added_bounty*10));
		}

	    }
	}
      else if ($victim == "thief")
	{
	  echo "Thief sees you and prepares to defend!<br /><br />\n";
	  echo "<img src=\"images/thief.png\" border=\"0\" />\n";
	  
	  $thief_attack = rand(0,35);  // *** Thief Damage  ***
	  
	  if (!subtractHealth($username,$thief_attack))
	    {
	      echo "Thief has slain you!<br />\n";
	      echo "Go to the <a href=\"shrine.php\">shrine</a> to resurrect.<br />\n";
	    } else {
	      $thief_gold    = rand(0,40);  // *** Thief Gold ***
	      
	      if ($thief_attack > 30)
		{
		  echo "Thief escaped and stole $thief_gold pieces of your gold!\n";
		  subtractGold($username,$thief_gold);
		}
	      else if ($thief_attack < 30)
		{
		  echo "The Thief is injured!<br />\n";
		  echo "Thief does $thief_attack points of damage!<br />\n";
		  echo "You have gained $thief_gold gold.<br /> You have found a Shuriken on the thief!\n";
		  addGold($username,$thief_gold);
		  
		  addItem($username,'Shuriken',$quantity=1);
		}
	      echo "<br />\n";
	      echo "Beware the Ninja Thieves, they have entered this world to steal from all!<br />\n";
	    }
	  
	  if (!getHealth($username) || getHealth($username) <= 0)
	    {
	      sendMessage("SysMsg",$username,"DEATH: You have been killed by a non-player character at $today");
	    }
	}
	  
      subtractTurns($username,$turn_cost);
      
      echo "<hr />\n";
      echo "<a href=\"attack_npc.php?attacked=1&victim=$victim\">Attack $victim again</a>\n";
      echo "<br />\n";
      echo "<a href=\"attack_player.php\">Return to Combat</a>\n";
    }
}
else
{
  echo "You have no turns left today. Buy a speed scroll or wait for your turns to replenish.\n";
}

include "interface/footer.php";
?>
