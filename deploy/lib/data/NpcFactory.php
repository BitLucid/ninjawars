<?php


class InvalidNpcException extends Exception{}

/**
 * Who/what/why/where
 *  Create npcs with static methods.
 *
**/
class NpcFactory{
	// Returns a fleshed out npc object
	public static function create($identity){
        $identity = mb_strtolower($identity);
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
			throw new InvalidNpcException('No such npc ['.$identity.'] found to create!');
		}
	}

	/**
	 * Create the meat of an npc from it's data
	**/
	public static function fleshOutFromData($data, $npc){
        $npc->setData($data);
        $npc->name = @$data['name'];
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

	// Pull all the npcs, currently from the get_npcs() function as a standing for
	// the database eventually.
	public static function npcsData(){
		return get_npcs();
	}

	// Pull all the npcs from data source.
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