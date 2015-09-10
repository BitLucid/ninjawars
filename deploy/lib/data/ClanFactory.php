<?php

/**
 * Who/what/why/where
 *  Create a clan for leaders and members to manage membership and eventually clan structures.
 *
**/
class ClanFactory{
	// Returns a fleshed out clan object, or a mostly blank one if no existing data found
	public static function create($identity, $data=null){
		$founder = $data['founder']? $data['founder'] : null;
		$desc = $data['description']? $data['description'] : null;
		$name = $identity;
		$new_clan_id = insert_query('insert into clan (clan_name, clan_founder, description) values (:name, :founder, :desc)',
			[':name'=>$name, ':founder'=>$founder, ':desc'=>$desc], 'clan_clan_id_seq');
		if(!positive_int($new_clan_id)){
			throw new Exception('Clan not inserted into database properly!');
		}
		return new Clan($new_clan_id);
	}

	// Get a clan by identity.
	public static function find($identity){
		$clan_info = query_row('select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan
				where clan_id = :id',
				[':id'=>$identity]);
		if(empty($clan_info)){
			return null;
		} else {
			$clan = new Clan($clan_info['clan_id']);
			$clan->setFounder($clan_info['clan_founder']);
			$clan->setDescription($clan_info['description']);
			$clan->setName($clan_info['clan_name']);
			return $clan;
		}
	}

	/**
	 * Create the flesh of an npc from it's data
	**/
	public static function fleshOutFromData($data, Clan $clan){
		$clan->setId($data['clan_id']);
		$clan->setName($data['clan_name']);
	}

	// Pull all the clans from data source.
	public static function clans(){
		return self::all();
	}

	// Alternate alias for the npcs static function.
	public static function all(){
		$data = ClanFactory::allData();
		$clans = array();
		foreach($data as $clan_info){
			$clans[] = self::fleshOutFromData($clan_info, new Clan());
		}
		return $clans;
	}

	public static function allData(){
		return query_array('select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan');
	}


}