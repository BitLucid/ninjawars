<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Message;
use Symfony\Component\HttpFoundation\Response;
use \PDO;

/**
 * This is a class that provides a jsonP get api via passing in a callback
 * It is not a REST api
 */
class ApiController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    /**
     * Determine which function to call to get the json for.
     *
     * @return Response
     */
    public function nw_json() {
        $request = RequestWrapper::$request;
        $type = $request->get('type');
        $dirty_jsoncallback = $request->get('jsoncallback');

        // Reject if non alphanumeric and _ chars
        $jsoncallback = (!preg_match('/[^a-z_0-9]/i', $dirty_jsoncallback) ? $dirty_jsoncallback : null);

        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Max-Age'       => '3628800',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
        ];

        if (!$jsoncallback) {
            $headers['Content-Type'] = 'application/json; charset=utf8';

            return new Response(json_encode(false), 200, $headers);
        }

        //  Whitelist of valid callbacks.
        $valid_type_map = [
            'player'         => 'jsonPlayer',
            'latest_event'   => 'jsonLatestEvent',
            'chats'          => 'jsonChats',
            'latest_message' => 'jsonLatestMessage',
            'index'          => 'jsonIndex',
            'latest_chat_id' => 'jsonLatestChatId',
            'inventory'      => 'jsonInventory',
            'new_chats'      => 'jsonNewChats',
            'send_chat'      => 'jsonSendChat',
            'char_search'    => 'jsonCharSearch',
            'deactivate_char'=> 'jsonDeactivateChar',
        ];

        $res = null;
        $data = $request->get('data');

        if (isset($valid_type_map[$type])) {
            if ($type == 'send_chat') {
                $result = $this->jsonSendChat($request->get('msg'));
            } elseif ($type == 'new_chats') {
                $chat_since = $request->get('since', null);
                $result = $this->jsonNewChats($chat_since);
            } elseif ($type == 'chats') {
                $chat_limit = $request->get('chat_limit', 20);
                $result = $this->jsonChats($chat_limit);
            } elseif ($type == 'char_search') {
                $result = $this->jsonCharSearch($request->get('term'), $request->get('limit'));
            } elseif (!empty($data)){ // If data param is present, pass data to the function
                $method = $valid_type_map[$type];
                $result = $this->$method($data);
            } else { // No data present, just call the function with no arguments.
                $method = $valid_type_map[$type];
                $result = $this->$method();
            }

            $res = "$jsoncallback(".json_encode($result).")";
        }

        $headers['Content-Type'] = 'text/javascript; charset=utf8';

        return new Response($res, 200, $headers);
    }

    /**
     * Deactivate a player character with a matching id
     */
    private function jsonDeactivateChar($data) {
        $char_id = $data;
        $user = Player::find(SessionFactory::getSession()->get('player_id'));
        if(!$user->isAdmin()){
            return [];
        } else {
            $res = query(
                "update players set active = 0 where player_id = :char_id and active != 0",
                [
                    ':char_id'  => $char_id,
                ]
            );
    
            return ['chars_deactivated' => $res->rowCount()];
        }
    }

    /**
     * Search through characters by text, returning multiple matches.
     */
    private function jsonCharSearch($term, $limit) {
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

    private function jsonLatestMessage() {
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

    private function jsonLatestEvent() {
        DatabaseConnection::getInstance();
        $user_id = (int) SessionFactory::getSession()->get('player_id');

        $statement = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender 
            FROM events JOIN players ON player_id = send_from 
            WHERE send_to = :userID and unread = 1 ORDER BY date DESC LIMIT 1");
        $statement->bindValue(':userID', $user_id);
        $statement->execute();

        return ['event' => $statement->fetch()];
    }

    private function jsonPlayer() {
        $player = Player::find(SessionFactory::getSession()->get('player_id'));
        return ['player' => ($player? $player->data() : null)];
    }

    private function jsonChats($limit = 20) {
        $limit = (int)$limit;
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("SELECT * FROM chat ORDER BY date DESC LIMIT :limit");
        $statement->bindValue(':limit', $limit);
        $statement->execute();
        $chats = $statement->fetchAll();

        return ['chats' => $chats];
    }

    private function jsonLatestChatId() {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->query("SELECT chat_id FROM chat ORDER BY date DESC LIMIT 1");

        return ['latest_chat_id' => $statement->fetch()];
    }

    private function jsonSendChat($msg) {
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
    private function jsonNewChats($since) {
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
    private function jsonMemberCount() {
        return $this->playerCount();
    }

    /**
     * Get the player's inventory list.
     */
    private function jsonInventory() {
        $char_id = SessionFactory::getSession()->get('player_id');
        $items = query_array(
            "SELECT item.item_display_name as item, amount 
                FROM inventory join item on inventory.item_type = item.item_id 
                WHERE owner = :char_id ORDER BY item_display_name",
            [':char_id'=> $char_id]
        );

        return ['inventory' => $items];
    }

    /**
     * Just count the number of active players
     * @return integer $player_count
     */
    private function playerCount() {
        return query_item(
            "SELECT count(player_id) from players where active = 1"
        );
    }

    private function jsonIndex() {
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
