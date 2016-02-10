<?php

use NinjaWars\core\data\NpcFactory;

require_once(ROOT . "core/control/Character.php");
require_once ROOT.'lib/data/lib_npc.php'; // Temporarily for the database mocking info.
// TODO: Abstract all the unique npc behaviors into the generic system.

/**
 *  who/what/why/where The various generic npcs that can be fought or interacted with
 *  villager npcs could have bounties
 *  npcs can have shared traits that provide special abilities
 *  Generally they are interacted with from the /enemies page
**/
class Npc implements Character{
    private $data;

    public function __construct($content){
    	if(is_string($content) && trim($content)){
    		NpcFactory::fleshOut($content, $this);
    	} else {
    		NpcFactory::fleshOutFromData($content, $this);
    	}
    }

    public function name(){
        return $this->name;
    }

    public function identity(){
        return $this->name;
    }

    public function image(){
        return $this->image;
    }

    public function shortDesc(){
        return $this->short_desc;
    }

    // Calculcate the max damage of an npc.  Needed for effectiveness calc.
    public function max_damage(Character $enemy=null){
        $dam = ((1+ ($this->strength * 2)) + $this->damage);
        // Mirror some of their enemy's strength
        if($this->has_trait('partial_match_strength') && $enemy instanceof Character){
            $add = max(0, floor($enemy->strength() / 3)); // Enemy str/3 or at minimum 0
            $dam = $dam + $add;
        }
        return $dam;
    }

    // Calculate the initial naive damage from npcs.
    public function damage(Character $char = null){
        return rand(0, $this->max_damage($char));
    }

    // Calculate difficulty, naively at the moment.
    public function difficulty(){
        // Just add together all the points of the mob, so to speak.
        $has_bounty = (int) isset($this->data['bounty']);
        $armored = $this->has_trait('armored')? 1 : 0;
        $complex = count($this->traits());
        $matches_strength = $this->has_trait('partial_match_strength')? 1 : 0;
        return 0
            + $this->strength * 2 
            + $this->damage 
            + floor($this->max_health() / 10)
            + (int) ($this->max_health() > 1) // Have more than 1 health, so not totally devoid of content
            + $has_bounty 
            + $armored * 5
            + $complex * 3
			+ $matches_strength * 5
            ;
    }

    // Check for specific traits.

    /**
     * @param string $trait
     */
    public function has_trait($trait){
        if(!isset($this->traits_array) && isset($this->traits)){
            // Initialize traits as an array at this point.
            $this->traits_array = $this->traits? explode(',', $this->traits) : array();
        }
        return count($this->traits_array) && in_array($trait, $this->traits_array);
    }

    public function traits(){
        return $this->traits_array;
    }

    public function speed(){
        return $this->speed;
    }
    public function strength(){
        return $this->strength;
    }
    public function stamina(){
        return $this->stamina;
    }
    public function ki(){
        return $this->ki;
    }

    public function health(){
        return $this->max_health(); // For now, since there aren't npc instances currently.
    }
    
    // Get their starting health, minimum of 1.
    public function max_health(){
    	$armored = $this->has_trait('armored')? 1 : 0;
    	return 1 + ($this->stamina * 5) + ($this->stamina * 2 * $armored);
	}
    
    // Instantiate a random chance of the inventory item being present.
    private function inventory_present($chance){
        return rand(1, 1000) < (int) ceil((float)$chance * 1000);
    }

    // Calculate this npc's inventory from initial chances.
    public function inventory(){
    	if(!isset($this->inventory) && isset($this->inventory_chances) && $this->inventory_chances){
    		$inv = array();
    		foreach($this->inventory_chances as $item=>$chance){
    			if($this->inventory_present($chance)){ // Calculate success from a decimal/float.
    				// Add the item.
    				$inv[$item] = true;
    			}
    		}
    		$this->inventory = $inv;
    	}
    	return $this->inventory;
    }
    
    // Get the npcs inventory and return true if there is an instance of the item in it.
    public function has_item($item){
    	return isset($this->inventory[$item]);
    }

    // Get the race of the npc.
    public function race(){
        if(!$this->race){
            return 'creature';
        } else {
            return $this->race;
        }
    }

    /**
     * Technically, this is the MAX bounty.
    **/
    public function bounty(){
        return $this->bounty;
    }

    public function dynamicBounty(Player $char){
        if($char->level() <= 2){
            return 0;
        } else {
            return $this->bounty();
        }
    }

    /**
     * Presumably this is modified gold.
    **/
    public function gold(){
        return $this->gold;
    }
}
