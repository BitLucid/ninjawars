<?php
namespace NinjaWars\core\control;

use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Message;
use \PDO;

class ApiController {
    public function sendHeaders() {
        // Json P headers
        header('Content-Type: text/javascript; charset=utf8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 3628800');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    }

    /**
     * Determine which function to call to get the json for.
     */
    public function nw_json($type, $dirty_jsoncallback) {
        // Reject if non alphanumeric and _ chars
        $jsoncallback = (!preg_match('/[^a-z_0-9]/i', $dirty_jsoncallback) ? $dirty_jsoncallback : null);

        if (!$jsoncallback) {
            return json_encode(false);
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
        ];

        $res = null;
        $data = in('data');

        if (isset($valid_type_map[$type])) {
            if ($type == 'send_chat') {
                $result = $this->jsonSendChat(in('msg'));
            } else if ($type == 'new_chats') {
                $chat_since = in('since', null);
                $chat_limit = in('chat_limit', 100);
                $result = $this->jsonNewChats($chat_since, $chat_limit);
            } elseif ($type == 'chats') {
                $chat_limit = in('chat_limit', 20);
                $result = $this->jsonChats($chat_limit);
            } elseif ($type == 'char_search') {
                $result = $this->jsonCharSearch(in('term'), in('limit'));
            } elseif (!empty($data)){ // If data param is present, pass data to the function
                $result = $this->$valid_type_map[$type]($data);
            } else { // No data present, just call the function with no arguments.
                $result = $this->$valid_type_map[$type]();
            }

            $res = "$jsoncallback(".json_encode($result).")";
        }

        return $res;
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

        $statement = DatabaseConnection::$pdo->prepare("SELECT message_id, message, date, send_to, send_from, unread, uname AS sender FROM messages JOIN players ON player_id = send_from WHERE send_to = :userID1 AND send_from != :userID2 and unread = 1 ORDER BY date DESC LIMIT 1");
        $statement->bindValue(':userID1', $user_id);
        $statement->bindValue(':userID2', $user_id);
        $statement->execute();

        // Skips message sent by self, i.e. clan send messages.
        return ['message' => $statement->fetch()];
    }

    private function jsonLatestEvent() {
        DatabaseConnection::getInstance();
        $user_id = (int) SessionFactory::getSession()->get('player_id');

        $statement = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender FROM events JOIN players ON player_id = send_from WHERE send_to = :userID and unread = 1 ORDER BY date DESC LIMIT 1");
        $statement->bindValue(':userID', $user_id);
        $statement->execute();

        return ['event' => $statement->fetch()];
    }

    private function jsonPlayer() {
        $player = Player::find(SessionFactory::getSession()->get('player_id'));
        return ['player' => $player->dataWithClan()];
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
        if (is_logged_in()) {
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

    private function jsonMemberCount() {
        return $this->memberCounts();
    }

    private function jsonInventory() {
        $items = query_array(
            "SELECT item.item_display_name as item, amount FROM inventory join item on inventory.item_type = item.item_id WHERE owner = :char_id ORDER BY item_display_name",
            [':char_id'=> self_id()]
        );

        return ['inventory' => $items];
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
            'member_counts'         => $this->memberCounts(),
            'unread_messages_count' => $unread_messages,
            'message'               => (!empty($messages) ? $messages->fetch() : null),
            'inventory'             => [
                'inv'   => 1,
                'items' => $items,
                'hash'  => md5(strtotime("now")),
            ],
            'unread_events_count'   => $unread_events,
            'event'                 => (!empty($events) ? $events->fetch() : null),
        ];
    }

    /**
     * Pull an array of different activity counts.
     */
    private function memberCounts() {
        $counts = query_array("(SELECT count(session_id) FROM ppl_online WHERE member AND activity > (now() - CAST('30 minutes' AS interval)))
            UNION ALL (SELECT count(session_id) FROM ppl_online WHERE member)
            UNION ALL (select count(player_id) from players where active = 1)");
        $active_row = array_shift($counts);
        $online_row = array_shift($counts);
        $total_row = array_shift($counts);
        return array('active'=>reset($active_row), 'online'=>reset($online_row), 'total'=>end($total_row));
    }
}
