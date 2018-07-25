<?php
namespace model;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use \model\BaseModel;
use \PDO;


/**
 * Status model for items and effects
 *
 * @property int id
 * @property string name
 * @property string expiration_datetime
 * @property int secs_duration
 * @property integer _player_id
 */
class Status extends BaseModel {
	const FIELDS = ['status_id as id', 'name', 'expiry_datetime', '_player_id'];

	/**
	 * Load any additional data to a model class
	 */
	public function load(){
		return true;
	}

	/**
	 * Save the model data to the database
	 */
	public function save(): int {
		if(!isset($this->id) || null === $this->id){
			// Set the new id on insert here.
			if(!isset($this->secs_duration) || $this->secs_duration < 1){
				throw new \InvalidArgumentException('When saving a status effect initially, need a duration in seconds.');
			}
			$this->id = self::addStatusEffect($this->name, Player::find($this->_player_id), $this->secs_duration);
		} else {
			throw new \BadMethodCallException('Save update of existing Statuses not yet implemented;');
		}
		return $this->id;
	}

	/**
	 * Get a status by id
	 */
	public static function find(int $id): \stdClass {
		return (object) query_row('select '.implode(', ', static::FIELDS).' from statuses where status_id = :id',
			[':id'=>[$id, PDO::PARAM_INT]]);
	}

	/**
	 * Get all the statuses a ninja has active
	 */
	public static function findStatusesByNinja(int $ninja_id): array {
		$status_res = query_array('select '.implode(', ', static::FIELDS).' from statuses where _player_id = :id',
			[':id'=>[$ninja_id, PDO::PARAM_INT]]);
		$statuses = array_map(function($sta){
			return (object) $sta;
		}, $status_res);
		return $statuses;
	}



    /**
     * Check whether an effect is active on the character
     */
    public static function queryStatusEffect(string $status_name, Player $char): int {
		$res = query_item('select status_id from statuses 
			where _player_id = :id and lower(name) = lower(:name) and expiry_datetime > now()', [
            ':id'=>[$char->id(), PDO::PARAM_INT],
            ':name'=>$status_name,
		]);
        return $res;
    }

	/**
	 * Add a status effect to a character
	 * return int the id of the status effect
	 */
    public static function addStatusEffect(string $status_name, Player $char, int $secs_duration) {
		// Run an update/insert 
		$name = strtolower($status_name); // Normalize the status name
		$secs = (string)(int) $secs_duration;
		$res = insert_query('insert into statuses (name, expiry_datetime, _player_id)
			values (:name, now() + interval \''.$secs.' seconds\', :char_id)
			ON CONFLICT ON CONSTRAINT effect_per_player DO UPDATE
			SET expiry_datetime = (now() + interval \''.$secs.' seconds\')
			returning status_id',
			[
				':name'=>$name,
				':char_id'=>[$char->id(), PDO::PARAM_INT],
			]);
		$first = $res->fetch(PDO::FETCH_ASSOC);
		return $first['status_id'];
	}
	
	/**
	 * Add a status effect to a character, refreshing if requested, or returning false
	 * return int|bool false if it can't refresh, or else the id of the status effect
	 */
    public static function refreshStatusEffect(string $status_name, Player $char, int $secs_duration, bool $refresh=false) {
		// Run an update/insert 
		if(!$refresh){
			$exists = static::queryStatusEffect($status_name, $char);
			if($exists){
				return false;
			}
		} else {
			return static::addStatusEffect($status_name, $char, $secs_duration);
		}
    }

}