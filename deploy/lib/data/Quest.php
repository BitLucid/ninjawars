<?php
namespace NinjaWars\core\data;

use Illuminate\Database\Eloquent\Model;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Character;

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

    /**
     * Get the hydrated player from the quest's player_id
     *
     */
    public function player() {
        $player = isset($this->player)? $this->player : Player::find($this->_player_id);
        return $player;
    }

    public function getPlayerIdAttribute($id) {
        return $this->attributes['_player_id'];
    }

    public function setPlayerIdAttribute($id) {
        $this->attributes['_player_id'] = $id;
    }

    public function setPlayer(Character $player) {
        $this->player = $player;
    }

    /**
     * Override save to add _player_id foreign key
     */
    public function save(array $options = []) {
        $player = $this->player();
        if($player !== null){
            $this->_player_id = $player->id();
        }
        return parent::save($options);
    }

    /**
     * Get the quests from the database.
     *
     */
    public static function get_quests($quest_id=null){
        /*$many_or_one = $quest_id? "WHERE quest_id= :quest_id" : '';
        // quest_id, title, player_id, tags, description, rewards, obstacles, expiration, proof
        $query = "SELECT quest_id, uname as giver, player_id, title, tags, description, rewards, obstacles, expiration, proof FROM quests q join players p on p.player_id = q._player_id".$many_or_one;*/
        $quests = [];
        /*
        if($quest_id){
            //$quests = query($query);        
        } else {
            //$quests = query($query, array(':quest_id'=>array($quest_id, PDO::PARAM_INT)));
        }
        */
        if(DEBUG){ // While debugging, mock a single quest
            $quests = [
                [
                'quest_id'=>1, 'giver'=>'glassbox', 'player_id'=>10, 'title'=>'some quest', 'tags'=>'fake bob jim',
            'description'=>'A description', 'rewards'=>'gold:30,kills:7,karma:35', 'obstacles'=>'wall, enemy , some guy,monster',
            'expiration'=>'10/20/30.12.14.96', 'proof' => 'have to show a screenshot'
                ],
            ];
        }
        return $quests;
    }

    /**
     *
     * Decorate the array with additional data
     * @return array $quests_data Replaced with decoded values
     */
    public static function hydrate_quests(array $quests_data){
        foreach($quests_data as &$quest){
            $quest['rewards'] = json_decode($quest['rewards']);
            // Eventually linkify the tags here.
            $quest['obstacles'] = json_decode($quest['obstacles']);
            // Unfold the questers here.
            $quest['questers'] = Quest::get_questors($quest['quest_id']);
            $quest['char'] = Player::find($quest['player_id']);
            if(DEBUG){
                $quest['questers']= ['10'=>'glassbox'];
            }
        }
        unset($quest);
        return $quests_data;
    }

    // Get the questors 
    public static function get_questors($quest_id){
        $questers = null;
        /*$sel = "SELECT p.player_id, p.uname 
            from players p join questers q on p.player_id = q._player_id 
            where quest_id = :quest_id";
        */
        //$questers = query($sel, array(":quest_id"=>array($quest_id, PDO::PARAM_INT)));
        if(DEBUG){
            $questers = [
                ['player_id'=>10, 'uname'=>'glassbox']
            ];
        }
        return $questers;
    }

}
