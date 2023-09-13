<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
// use Illuminate\Database\Eloquent\Model;
use NinjaWars\core\data\Player;

class Message extends NWQuery
{
    protected static $primaryKey = 'message_id'; // if anything other than id
    protected static $table = 'messages';
    protected $message_id;
    protected $message;
    protected $date;
    protected $send_to;
    protected $send_from;
    protected $unread;
    protected $type;
    public $timestamps = false;
    // The non-mass-fillable fields
    protected $guarded = ['message_id', 'date'];
    /**
    Messages Currently:
    message_id | serial
    message | text
    date | timestamp
    send_to | foreign key
    send_from | foreign key
    unread | integer default 1
    type | integer default 0
     */

    /**
     * Special case method to get the id regardless of what it's actually called in the database
     */
    public function id()
    {
        return $this->message_id;
    }

    /**
     * Send the message to a group of target ids
     */
    public static function sendToGroup(Player $sender, array $groupTargets, $message, $type)
    {
        if (!$sender || !$sender->id()) {
            throw new \Exception('Error: Message sender not set.');
        }

        $id = $sender->id();

        foreach ($groupTargets as $target_id) {
            self::create([
                'message'   => $message,
                'send_to'   => $target_id,
                'send_from' => $id,
                'type'      => $type
            ]);
        }

        return true;
    }

    /**
     * Get messages for a receiver.
     */
    public static function findByReceiver(Player $sender, $type = 0, $limit = null, $offset = null)
    {
        if ($limit === null || $offset ===  null) {
            $limit = 1000;
            $offset = 0;
        }
        $res = query(['select p.uname as sender, m.* from messages m left join players p on p.player_id = m.send_from
            where m.send_to = :send_to and m.type = :type
         order by date desc limit :limit offset :offset', [
            ':send_to' => $sender->id(),
            ':type'    => $type,
            ':limit' => $limit,
            ':offset' => $offset
        ]]);
        return $res;
    }

    /**
     * Get a count of the messages to a receiver.
     */
    public static function countByReceiver(Player $char, $type = 0)
    {
        $res = static::query_resultset(['select count(*) from messages where send_to = :send_to and type = :type', [
            ':send_to' => $char->id(),
            ':type'    => $type
        ]]);
        return $res->rowCount();
    }

    /**
     * Delete personal messages to a receiver.
     */
    public static function deleteByReceiver(Player $char, $type)
    {
        $resultset = query('delete from messages where send_to is not null and send_to = :send_to and type = :type', [
            ':send_to' => $char->id(),
            ':type'    => $type
        ]);
        return $resultset->rowCount();
    }

    /**
     * mark all messages of a type for a ninja as read
     */
    public static function markAsRead(Player $char, $type)
    {
        $resultset = query('update messages set unread = 0 where send_to = :send_to and type = :type', [
            ':send_to' => $char->id(),
            ':type'    => $type
        ]);
        return $resultset->rowCount();
    }

    public static function sendChat($user_id, $msg)
    {
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
    public static function deleteOldMessages()
    {
        $statement = query("delete from messages where date < ( now() - '3 months'::interval)");
        return $statement->rowCount();
    }

    /**
     * Deletes old chat messages.
     */
    public static function shortenChat($message_limit = 800)
    {
        DatabaseConnection::getInstance();
        // Find the latest 800 messages and delete all the rest;
        $deleted = DatabaseConnection::$pdo->prepare("DELETE FROM chat WHERE chat_id NOT IN (SELECT chat_id FROM chat ORDER BY date DESC LIMIT :msg_limit)");
        $deleted->bindValue(':msg_limit', $message_limit);
        $deleted->execute();

        return (int) $deleted->rowCount();
    }

    public function save()
    {
        $this->date = static::freshTimestamp();
        query('insert into messages (message, date, send_to, send_from, unread, type) values (:message, :date, :send_to, :send_from, :unread, :type)', [
            ':message'   => $this->message,
            ':date'      => $this->date,
            ':send_to'   => $this->send_to,
            ':send_from' => $this->send_from,
            ':unread'    => $this->unread,
            ':type'      => $this->type
        ]);
        return $this;
    }

    /**
     * @return bool success
     */
    public function delete(): bool
    {
        return (query('delete from messages where message_id = :id', [':id' => $this->id()])->rowCount()) > 0;
    }
}
