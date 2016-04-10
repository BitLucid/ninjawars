<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use Illuminate\Database\Eloquent\Model;
use NinjaWars\core\data\Player;

class Message extends Model {
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
     */
    public static function boot() {
        static::creating(function($model) {
            $model->date = $model->freshTimestamp();
        });
    }

    /**
     * Special case method to get the id regardless of what it's actually called in the database
     */
    public function id() {
        return $this->message_id;
    }

    /**
     * Send the message to a group of target ids
     */
    public static function sendToGroup(Player $sender, array $groupTargets, $message, $type) {
        if (!$sender || !$sender->id()) {
            throw new \Exception('Error: Message sender not set.');
        }

        $id = $sender->id();

        foreach ($groupTargets as $target_id) {
            Message::create([
                'message'   => $message,
                'send_to'   => $target_id,
                'send_from' => $id,
                'type'      => $type
            ]);
        }

        return true;
    }

    /**
     * Get messages to a receiver.
     */
    public static function findByReceiver(Player $sender, $type=0, $limit=null, $offset=null) {
        if ($limit !== null && $offset !== null) {
            return Message::where([
                'send_to' => $sender->id(),
                'type'    => $type
            ])
            ->leftJoin('players', function($join) {
                $join->on('messages.send_from', '=', 'players.player_id');
            })
            ->orderBy('date', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get([
                'players.uname as sender',
                'messages.type',
                'messages.send_to',
                'messages.send_from',
                'messages.message',
                'messages.unread',
                'messages.date'
            ]);
        } else {
            return Message::where([
                'send_to' => $sender->id(),
                'type'    => $type
            ])
            ->leftJoin('players', function($join) {
                $join->on('messages.send_from', '=', 'players.player_id');
            })
            ->orderBy('date', 'DESC')
            ->get([
                'players.uname as sender',
                'messages.type',
                'messages.send_to',
                'messages.send_from',
                'messages.message',
                'messages.unread',
                'messages.date'
            ]);
        }
    }

    /**
     * Get a count of the messages to a receiver.
     */
    public static function countByReceiver(Player $char, $type=0) {
        return Message::where([
            'send_to' => $char->id(),
            'type'    => $type
        ])->count();
    }

    /**
     * Delete personal messages to a receiver.
     */
    public static function deleteByReceiver(Player $char, $type) {
        return Message::where([
            'send_to' => $char->id(),
            'type'    => $type
        ])->delete();
    }

    /**
     * mark all messages of a type for a ninja as read
     */
    public static function markAsRead(Player $char, $type) {
        return Message::where([
            'send_to' => $char->id(),
            'type'    => $type
        ])->update([
            'unread' => 0
        ]);
    }

    public static function sendChat($user_id, $msg) {
        DatabaseConnection::getInstance();

        $msg = trim($msg);

        if ($msg) {
            $statement = DatabaseConnection::$pdo->prepare("SELECT message FROM chat WHERE sender_id = :sender ORDER BY date DESC LIMIT 1");
            $statement->bindValue(':sender', $user_id);
            $statement->execute();
            $prevMsg = trim($statement->fetchColumn());

            if ($prevMsg != $msg) {
                $statement = DatabaseConnection::$pdo->prepare("INSERT INTO chat (chat_id, sender_id, message, date) VALUES (default, :sender, :message, now())");
                $statement->bindValue(':sender', $user_id);
                $statement->bindValue(':message', $msg);
                $statement->execute();

                return true;
            }
        }

        return false;
    }

    /**
     * @return int Number of rows deleted
     */
    public static function deleteOldMessages() {
        $statement = query("delete from messages where date < ( now() - '3 months'::interval)");
        return $statement->rowCount();
    }

    /**
     * Deletes old chat messages.
     */
    public static function shortenChat($message_limit=800) {
        DatabaseConnection::getInstance();
        // Find the latest 800 messages and delete all the rest;
        $deleted = DatabaseConnection::$pdo->prepare("DELETE FROM chat WHERE chat_id NOT IN (SELECT chat_id FROM chat ORDER BY date DESC LIMIT :msg_limit)");
        $deleted->bindValue(':msg_limit', $message_limit);
        $deleted->execute();

        return (int) $deleted->rowCount();
    }
}
