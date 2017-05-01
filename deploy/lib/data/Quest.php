<?php
namespace NinjaWars\core\data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 deleted_at  | timestamp with time zone | not null
 type        | integer                     | 
 difficulty  | integer 

 * @property int quest_id
 * @property Player player
 * @property int _player_id
 */
class Quest extends Model {
    use SoftDeletes;

    protected $primaryKey = 'quest_id'; // Anything other than id
    // The non-mass-fillable fields
    protected $guarded = ['quest_id', 'created_at', 'updated_at'];
    protected $dates   = ['created_at', 'updated_at', 'expires_at', 'deleted_at'];

    /**
     * Special case method to get the id regardless of what it's actually called in the database
     */
    public function id() {
        return $this->quest_id;
    }

    /**
     * Get the hydrated player from the quest's player_id
     * @return Player The quest originator/giver
     */
    public function player($id=null) {
        if(isset($this->player)){
            return $this->player;
        } else {
            return Player::find($id);
        }
    }

    /**
     * Override to get the custom _player_id attribute
     *
     */
    public function getPlayerIdAttribute($id) {
        return $this->attributes['_player_id'];
    }

    /**
     * Set the custom _player_id attribute
     *
     */
    public function setPlayerIdAttribute($id) {
        $this->attributes['_player_id'] = $id;
    }

    /**
     * Set the character who is the quest giver
     */
    public function setPlayer(Character $player) {
        $this->player = $player;
    }

    /**
     * Override save to add _player_id foreign key
     */
    public function save(array $options = []) {
        $player = $this->player($this->_player_id);
        if($player !== null){
            $this->_player_id = $player->id();
        }
        return parent::save($options);
    }

    /**
     * Get the quests from the database, undyrated
     * @return array Of quest data
     */
    public static function get_quests(){
        //$quests = Quest::where('active', 1)->orderBy('created_at', 'desc');
        $quests = [];
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
            $quest['char'] = Player::find($quest['player_id']);
            // Unfold the questers here.
            /**
            $quest['questers'] = Quest::get_questors($quest['quest_id']);
            if(DEBUG){
                $quest['questers']= ['10'=>'glassbox'];
            }**/
        }
        unset($quest);
        return $quests_data;
    }

    // TODO: Possibly implement a way to get quest-givers and possibly quest questors
}
