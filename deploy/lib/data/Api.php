<?php

namespace NinjaWars\core\data;

use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Enemies;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Message;
use \PDO;

/**
 * Api calls
 */
class Api
{
    /**
     * Find the next enemy target for the player.
     */
    public function nextTarget($data)
    {
        $offset = $data;
        $char = Player::find(SessionFactory::getSession()->get('player_id'));
        $target = $char ? Enemies::nextTarget($char, (int) $offset) : null;
        return $char && $target ? $target->publicData() : false;
    }

    /**
     * Deactivate a player character and account with a matching id
     * @note Admin only
     */
    public function deactivateChar($data, $auth_override = false)
    {
        $accounts_deactivated = 0;
        $chars_deactivated = 0;
        $char_id = $data;
        $user = Player::find(SessionFactory::getSession()->get('player_id'));
        if (!$user || !$user->isAdmin()) {
            return ['error' => 'Unable to proceed further.'];
        } else {
            $char = Player::find($char_id);
            if ($char) {
                $chars_deactivated = Account::deactivateSingleCharacter($char);
                $accounts_deactivated = Account::deactivateByCharacter($char);
            }
            return ['chars_deactivated' => $chars_deactivated, 'accounts_deactivated' => $accounts_deactivated];
        }
    }

    /**
     * Reactivate a player character and account with a matching id
     * @note Admin only
     */
    public function reactivateChar($data)
    {
        $chars_reactivated = 0;
        $accounts_reactivated = 0;
        $char_id = $data;
        $user = Player::find(SessionFactory::getSession()->get('player_id'));
        if (!$user || !$user->isAdmin()) {
            return ['error' => 'Unable to proceed further.'];
        } else {
            $char = Player::find($char_id);
            if ($char) {
                $chars_reactivated = Account::reactivateSingleCharacter($char);
                $accounts_reactivated = Account::reactivateByCharacter($char);
            }
            return ['chars_reactivated' => $chars_reactivated, 'accounts_reactivated' => $accounts_reactivated];
        }
    }

    /**
     * Search through characters by text, returning multiple matches.
     */
    public function charSearch($term, $limit)
    {
        if (!is_numeric($limit)) {
            $limit = 10;
        }
        // Should be fine for this to allow regex characters here if it happens.
        $res = query(
            "select player_id, uname from players where uname ilike :term || '%' and active=1 order by level desc limit :limit",
            [
                ':term'  => $term,
                ':limit' => [$limit, PDO::PARAM_INT]
            ]
        );
        return ['char_matches' => $res->fetchAll(PDO::FETCH_ASSOC)];
    }

    /**
     * Last message sent to current player
     */
    public function latestMessage()
    {
        DatabaseConnection::getInstance();
        $user_id = (int) SessionFactory::getSession()->get('player_id');

        $statement = DatabaseConnection::$pdo->prepare("SELECT message_id, message, date, send_to, send_from, unread, uname AS sender 
            FROM messages JOIN players ON player_id = send_from 
            WHERE send_to = :userID1 AND send_from != :userID2 and unread = 1 ORDER BY date DESC LIMIT 1");
        $statement->bindValue(':userID1', $user_id);
        $statement->bindValue(':userID2', $user_id);
        $statement->execute();

        // Skips message sent by self, i.e. clan send messages.
        return ['message' => $statement->fetch()];
    }

    public function latestEvent()
    {
        DatabaseConnection::getInstance();
        $user_id = (int) SessionFactory::getSession()->get('player_id');

        $statement = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender 
            FROM events JOIN players ON player_id = send_from 
            WHERE send_to = :userID and unread = 1 ORDER BY date DESC LIMIT 1");
        $statement->bindValue(':userID', $user_id);
        $statement->execute();

        return ['event' => $statement->fetch()];
    }

    public function player()
    {
        $player = Player::find(SessionFactory::getSession()->get('player_id'));
        return ['player' => ($player ? $player->data() : null)];
    }

    public function chats($limit = 20)
    {
        $limit = (int)$limit;
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("SELECT * FROM chat ORDER BY date DESC LIMIT :limit");
        $statement->bindValue(':limit', $limit);
        $statement->execute();
        $chats = $statement->fetchAll();

        return ['chats' => $chats];
    }

    public function latestChatId()
    {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->query("SELECT chat_id FROM chat ORDER BY date DESC LIMIT 1");

        return ['latest_chat_id' => $statement->fetch()];
    }

    public function sendChat($msg)
    {
        if (SessionFactory::getSession()->get('authenticated', false)) {
            $msg     = trim($msg);
            $player  = Player::find(SessionFactory::getSession()->get('player_id'));
            $success = Message::sendChat($player->id(), $msg);

            if (!$success) {
                return false;
            } else {
                return [
                    'message'   => $msg,
                    'sender_id' => $player->id(),
                    'uname'     => $player->name(),
                ];
            }
        }
    }

    /**
     * Get the newest chats for the mini-chat area.
     */
    public function newChats($since)
    {
        $since = ($since ? (float)$since : null); // Since is a float?  Weird
        $now = microtime(true);
        DatabaseConnection::getInstance();

        if ($since) {
            $statement = DatabaseConnection::$pdo->prepare("SELECT chat.*, uname FROM chat LEFT JOIN players ON player_id = sender_id WHERE EXTRACT(EPOCH FROM date) > :since ORDER BY date ASC");
            $statement->bindValue(':since', $since);
        } else {
            $statement = DatabaseConnection::$pdo->prepare("SELECT chat.*, uname FROM chat LEFT JOIN players ON player_id = sender_id ORDER BY date ASC");
        }

        $statement->execute();
        $chats = $statement->fetchAll();

        return [
            'new_chats' => [
                'datetime'  => $now,
                'new_count' => count($chats),
                'chats'     => $chats,
            ],
        ];
    }

    /** 
     * Just count the number of currently active players.
     */
    public function memberCount()
    {
        return $this->playerCount();
    }

    /**
     * Get the player's inventory list.
     */
    public function inventory()
    {
        $char_id = SessionFactory::getSession()->get('player_id');
        $items = query_array(
            "SELECT item.item_display_name as item, amount 
                FROM inventory join item on inventory.item_type = item.item_id 
                WHERE owner = :char_id ORDER BY item_display_name",
            [':char_id' => $char_id]
        );

        return ['inventory' => $items];
    }

    /**
     * Just count the number of active players
     * @return integer $player_count
     */
    private function playerCount()
    {
        return query_item(
            "SELECT count(player_id) from players where active = 1"
        );
    }

    public function index()
    {
        DatabaseConnection::getInstance();

        $player          = Player::find(SessionFactory::getSession()->get('player_id'));
        $events          = [];
        $messages        = [];
        $unread_messages = null;
        $unread_events   = null;
        $items = null;

        if ($player) {
            $events = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender FROM events JOIN players ON player_id = send_from WHERE send_to = :userID and unread = 1 ORDER BY date DESC");
            $events->bindValue(':userID', $player->id());
            $events->execute();

            $unread_events = $events->rowCount();

            $messages = DatabaseConnection::$pdo->prepare("SELECT message_id, message, date, send_to, send_from, unread, uname AS sender FROM messages JOIN players ON player_id = send_from WHERE send_to = :userID1 AND send_from != :userID2 and unread = 1 ORDER BY date DESC");
            $messages->bindValue(':userID1', $player->id());
            $messages->bindValue(':userID2', $player->id());
            $messages->execute();

            $unread_messages = $messages->rowCount();

            $items = query_array(
                'SELECT item.item_display_name as item, amount FROM inventory join item on inventory.item_type = item.item_id WHERE owner = :user_id ORDER BY item_display_name',
                [':user_id' => $player->id()]
            );
        }

        return [
            'player'                => ($player ? $player->publicData() : []),
            'unread_messages_count' => $unread_messages,
            'message'               => (!empty($messages) ? $messages->fetch() : null),
            'inventory'             => [
                'inv'   => 1,
                'items' => $items,
                'hash'  => md5(strtotime("now")),
            ],
            'unread_events_count'   => $unread_events,
            'event'                 => (!empty($events) ? $events->fetch() : null),
            'member_counts'         => $this->playerCount(),
        ];
    }
}
