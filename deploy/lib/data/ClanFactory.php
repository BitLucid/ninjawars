<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Clan;
use \Player;

/**
 * Who/what/why/where
 *  Create a clan for leaders and members to manage membership and eventually clan structures.
 *
 */
class ClanFactory {
	// Returns a fleshed out clan object, or a mostly blank one if no existing data found
	public static function create($identity, $data=null) {
		$founder = ($data['founder'] ? $data['founder'] : null);
		$desc    = ($data['description'] ? $data['description'] : '');
		$url     = (isset($data['clan_avatar_url']) ? $data['clan_avatar_url'] : null);
		$name    = $identity;

		$new_clan_id = insert_query('insert into clan (clan_name, clan_avatar_url, clan_founder, description) values (:name, :url, :founder, :desc)',
			[':name'=>$name, ':url'=>$url, ':founder'=>$founder, ':desc'=>$desc], 'clan_clan_id_seq');

		if (!positive_int($new_clan_id)) {
			throw new \Exception('Clan not inserted into database properly!');
		}

		return ClanFactory::find($new_clan_id);
	}

	// Get a clan by identity.
	public static function find($identity) {
		$clan_info = query_row('select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan
				where clan_id = :id',
				[':id'=>$identity]);

		if (empty($clan_info)) {
			return null;
		} else {
			$clan = new Clan($clan_info['clan_id']);
			$clan->setFounder($clan_info['clan_founder']);
			$clan->setDescription($clan_info['description']);
			$clan->setName($clan_info['clan_name']);
			$clan->setAvatarUrl($clan_info['clan_avatar_url']);
			return $clan;
		}
	}

	/**
	 * Get the clan that a member has, if any
	 * @return Clan|null
	 **/
	public static function clanOfMember($pc_or_id){
		if ($pc_or_id instanceof Player) {
			$playerID = $pc_or_id->id();
		} else {
			$playerID = $pc_or_id;
		}

		$clan_info = query_row('select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan JOIN clan_player ON clan_id = _clan_id where _player_id = :pid', [':pid'=>$playerID]);

		if (empty($clan_info)) {
			return null;
		} else {
			$clan = new Clan($clan_info['clan_id']);
			$clan->setFounder($clan_info['clan_founder']);
			$clan->setDescription($clan_info['description']);
			$clan->setName($clan_info['clan_name']);
			$clan->setAvatarUrl($clan_info['clan_avatar_url']);
			return $clan;
		}
	}

	/**
	 * Create the flesh of an npc from it's data
	**/
	public static function fleshOutFromData($data, Clan $clan){
		$clan->setId($data['clan_id']);
		$clan->setName($data['clan_name']);
		$clan->setAvatarUrl($data['clan_avatar_url']);
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

	/**
	 * Save the data of an already created clan.
	 **/
	public static function save(Clan $clan){
		if(!$clan->id()){
			throw new \Exception('Clan cannot be saved as it does not yet have an id.');
		}
		$updated = update_query('update clan set clan_name = :name, clan_founder = :founder, clan_avatar_url = :avatar_url, description = :desc
				where clan_id = :id', [':name'=>$clan->getName(), ':founder'=>$clan->getFounder(), 
				':avatar_url'=>$clan->getAvatarUrl(), ':desc'=>$clan->getDescription(), ':id'=>$clan->id()]);
		return (bool)$updated;
	}
}
