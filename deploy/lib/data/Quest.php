<?php
namespace NinjaWars\core\data;

use Illuminate\Database\Eloquent\Model;
use \Player;

    /**
    Currently:
 quest_id    | integer                     | not null default nextval('quests_quest_id_seq'::regclass)
 title       | character varying(100)      | not null default ''::character varying
 description | text                        | not null default ''::text
 _player_id  | integer                     | not null
 tags        | text                        | default ''::text
 karma       | integer                     | not null default 0
 rewards     | text                        | not null default ''::text
 obstacles   | text                        | not null default ''::text
 proof       | text                        | not null default ''::text
 expires_at  | timestamp with time zone | not null
 created_at  | timestamp with time zone | not null default now()
 updated_at  | timestamp with time zone | not null default now()
 type        | integer                     | 
 difficulty  | integer 

     */
class Quest extends Model {
    protected $primaryKey = 'quest_id'; // Anything other than id
    // The non-mass-fillable fields
    protected $guarded = ['quest_id', 'created_at', 'updated_at'];
    protected $dates   = ['created_at', 'updated_at', 'expires_at'];

    /**
     * Special case method to get the id regardless of what it's actually called in the database
     */
    public function id() {
        return $this->quest_id;
    }

    public function player() {
        $player = isset($this->player)? $this->player : Player::find($this->_player_id);
        return $player;
    }

    public function getPlayerIdAttribute($id) {
        return Player::find($id);
    }

    public function setPlayerIdAttribute($id) {
        $this->attributes['_player_id'] = $id;
    }

    public function setPlayer(Player $player) {
        $this->player = $player;
    }

    /**
     * Override save to add _player_id foreign key
     */
    public function save(array $options = array()) {
        $player = $this->player();
        if($player !== null){
            $this->_player_id = $player->id();
        }
        return parent::save($options);
    }

}
