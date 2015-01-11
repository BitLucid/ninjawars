<?php

require_once ROOT.'lib/data/lib_npc.php'; // Temporarily for the database mocking info.
// TODO: Abstract all the unique npc behaviors into the generic system.

/**
 *   who/what/why/where The various generic npcs that can be fought or interacted with
 *  villager npcs could have bounties
 *  npcs can have shared traits that provide special abilities
 *  Generally they are interacted with from the /enemies page
**/
class Npc{
    private $data;
    function __construct($content){
    	if(is_string($content) && $content){
    		NpcFactory::fleshOut($content, $this);
    	} else {
    		NpcFactory::fleshOutFromData($content, $this);
    	}
    }

    // Calculcate the max damage of an npc.  Needed for effectiveness calc.
    function max_damage(){
        return ((1+ ($this->strength * 2)) + $this->damage);
    }

    // Calculate the initial naive damage from npcs.
    function damage(){
        return rand(0, $this->max_damage());
    }

    // Calculate difficulty, naively at the moment.
    function difficulty(){
        // Just add together all the points of the mob, so to speak.
        $has_bounty = (int) isset($this->data['bounty']);
        $armored = $this->has_trait('armored')? 1 : 0;
        return 10 + $this->strength * 2 + $this->damage + $has_bounty + $armored * 5;
    }

    // Check for specific traits.
    function has_trait($trait){
        if(!isset($this->traits_array) && isset($this->traits)){
            // Initialize traits as an array at this point.
            $this->traits_array = $this->traits? explode(',', $this->traits) : array();
        }
        return count($this->traits_array) && in_array($trait, $this->traits_array);
    }

    function speed(){
        return $this->speed;
    }
    function strength(){
        return $this->strength;
    }
    function stamina(){
        return $this->stamina;
    }
    function ki(){
        return $this->ki;
    }
    
    function health(){
    	$armored = $this->has_trait('armored')? 1 : 0;
    	return $this->stamina * 5 + $this->stamina * 2 * $armored;
	}
    
    // Calculate this npc's inventory from initial chances.
    function inventory(){
    	if(!isset($this->inventory) && isset($this->inventory_chances) && $this->inventory_chances){
    		$inv = array();
    		foreach($this->inventory_chances as $item=>$chance){
    			if(rand(1, 1000) < (int) ceil((float)$chance * 1000)){ // Calculate success from a decimal/float.
    				// Add the item.
    				$inv[$item] = true;
    			}
    		}
    		$this->inventory = $inv;
    	}
    	return $this->inventory;
    }
    
    // Get the npcs inventory and return true if there is an instance of the item in it.
    function has_item($item){
    	return isset($this->inventory[$item]);
    }

    // Get the race of the npc.
    function race(){

    }

    function bounty(){
    }
}

class InvalidNpcException extends Exception{}

/**
 * Who/what/why/where
 *  Create npcs with static methods.
 *
**/
class NpcFactory{
	// Returns a fleshed out npc object
	public static function create($identity){
		$npcs = NpcFactory::npcsData();
		$npc = null;
		if($identity && in_array($identity, $npcs)){
			$npc = new Npc($npcs[$identity]);
		}
		return $npc;
	}

	/**
	 * Pass the npc in and use the reference to flesh out it's data, from an identity if nothing else
	**/
	public static function fleshOut($identity, $npc){
		$npcs_data = NpcFactory::npcsData();
		if(in_array($identity, $npcs_data) && !empty($npcs_data[$identity])){
			NpcFactory::fleshOutFromData($npcs_data[$identity]);
		} else {
			throw new InvalidNpcException('No such npc found to create!');
		}
	}

	/**
	 * Create the meat of an npc from it's data
	**/
	public static function fleshOutFromData($data, $npc){
        $npc->data = $data;
        $npc->inventory_chances = @$data['inventory'];
        $npc->traits = @$data['traits'];
        $npc->strength = (int) @$data['strength'];
        $npc->speed = (int) @$data['speed'];
        $npc->stamina = (int) @$data['stamina'];
        $npc->damage = (int) @$data['damage'];
        $npc->ki = (int) @$data['ki'];
        $npc->race = @$data['race'];
        $npc->traits_array = null;
        $npc->inventory = null; // Initially just null;
	}

	public static function npcsData(){
		return get_npcs();
	}

	public static function npcs(){
		$npcs_data = NpcFactory::npcsData();
		$npcs = array();
		foreach($npcs_data as $identity=>$npc_data){
			assert((bool)$identity);
			$npcs[$identity] = new Npc($npc_data);
		}
		return $npcs;
	}
}