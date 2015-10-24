<?php
namespace app\data;
require_once(CORE.'data/database.php');

use Illuminate\Database\Eloquent\Model;
use \Player;

class Message extends Model{
	protected $primaryKey = 'message_id'; // Anything other than id
    public $timestamps = false;
    // The non-mass-fillable fields
    protected $guarded = ['message_id', 'date'];
    /**
    Currently:
    message_id | serial
    message | text
    date | timestamp
    send_to | foreign key
    send_from | foreign key
    unread | integer default 1
    type | integer default 0
    */

    /**
     * Custom initialization of `date` field, since this model only keeps one
    **/
    public static function boot(){
        static::creating(function($model){
            $model->date = $model->freshTimestamp();
        });
    }


    /**
     * Special case method to get the id regardless of what it's actually called in the database
    **/
    public function id(){
    	return $this->message_id;
    }

    /**
     * Send a message of a certain type, to a target, essentially just wraps ::create
     * Does not fully instantiate a target object to avoid the overhead.
    **/
    public static function send(Player $sender, $target_id, $message, $type){
        Message::create(['message'=>$message, 'send_to'=>$target_id, 
                'send_from'=>$sender->id(), 'type'=>$type]);
        return true;
    }

    /**
     * Send the message to a group of target ids
    **/
    public static function sendToGroup(Player $sender, $groupTargets, $message, $type){
        foreach($groupTargets as $target_id){
            Message::create(['message'=>$message, 'send_to'=>$target_id, 
                    'send_from'=>$sender->id(), 'type'=>$type]);
        }
        return true;
    }

    /**
     * Get messages to a receiver.
    **/ 
    public static function findByReceiver(Player $char, $type=0, $limit=null, $offset=null){
        return Message::where(['send_to'=>$char->id(), 'type'=>$type])->leftJoin('players', function($join) {
            $join->on('messages.send_from', '=', 'players.player_id');
        })->orderBy('date', 'DESC')->limit($limit)->offset($offset)->get(['players.uname as sender', 'messages.type', 'messages.send_to', 
            'messages.send_from', 'messages.message', 'messages.unread', 'messages.date']);
    }

    /**
     * Get a count of the messages to a receiver.
    **/ 
    public static function countByReceiver(Player $char, $type=0){
        return Message::where(['send_to'=>$char->id(), 'type'=>$type])->count();
    }

    /**
     * Delete personal messages to a receiver.
    **/
    public static function deleteByReceiver(Player $char, $type){
        return Message::where(['send_to'=>$char->id(), 'type'=>$type])->delete();
    }

    /**
     * mark
    **/
    public static function markAsRead(Player $char, $type){
        return Message::where(['send_to'=>$char->id(), 'type'=>$type])->update(['unread' => 0]);
    }
}