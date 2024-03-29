<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\NWTemplate;
use Nmail;
use PDOStatement;
use NinjaWars\core\UnauthorizedException;

/**
 * Class for generating communications to the account about internal activity
 */

class Communication
{
    public static function sendEvents($data): array
    {
        ['char' => $char] = $data;
        if (!($char instanceof Player)) {
            throw new UnauthorizedException('Communication::sendEvents requires a player object', 401);
        }


        $events = self::getEvents($char->id(), 300);

        $messages = self::getMessages($char->id(), 300);
        self::mailEvents($char, $events, $messages);
        self::readEvents($char->id()); // mark events as viewed.


        return ['events' => $events];
    }

    /**
     * Retrieve events by user
     *
     * @param int    $user_id
     * @param String $limit
     * @return array
     */
    public static function getEvents(int $user_id, $limit = null): PDOStatement
    {
        $params = [':to' => $user_id];

        $add_limit = '';
        if ($limit !== null) {
            $params[':limit'] = $limit;
            $add_limit = 'LIMIT :limit';
        }

        return query("SELECT coalesce(send_from, 0) AS send_from, message, unread, date, uname AS from FROM events
            LEFT JOIN players ON send_from = player_id WHERE send_to = :to ORDER BY date DESC
            " . $add_limit, $params);
    }

    /**
     * Retrieve messages sent to user
     *
     * @param int    $user_id
     * @param String $limit
     * @return array
     */
    public static function getMessages(int $user_id, $limit = null, $offset = null, $type = null): PDOStatement
    {
        $params = [':to' => $user_id];

        $add_limits = '';
        $add_type = '';
        if ($limit !== null) {
            $params[':limit'] = $limit;
            $add_limits = 'LIMIT :limit';
        }
        if ($offset !== null) {
            $params[':offset'] = $offset;
            $add_limits .= ' OFFSET :offset';
        }
        if ($type !== null) {
            $params[':type'] = $type;
            $add_type .= ' AND type = :type';
        }

        // Note that sender is added as the "from" originator
        return query(
            "SELECT coalesce(send_from, 0) AS send_from, uname as sender, message, unread, date, uname AS from FROM messages
            LEFT JOIN players ON send_from = player_id WHERE send_to = :to " . $add_type . "  ORDER BY date DESC
            " . $add_limits,
            $params
        );
    }

    /**
     * Format messages as a simple class for backwards compatibility
     */
    public static function formatMessages(PDOStatement|array $messages): array
    {
        $formatted = [];
        foreach ($messages as $message) {
            $formatted[] = (object)[
                ...$message,
            ];
        }
        return $formatted;
    }

    public static function createMessage($data): bool
    {
        ['send_from' => $send_from, 'send_to' => $send_to, 'message' => $message, 'type' => $type] = $data;
        $count = insert_query(
            "INSERT INTO messages (send_from, send_to, message, type) VALUES (:from, :to, :message, :type)",
            [':from' => $send_from, ':to' => $send_to, ':message' => $message, ':type' => $type]
        );
        return $count > 0;
    }

    public static function sendToGroup(Player $sender, array $target_id_list, string $message, int $type): bool
    {
        $count = 0;
        foreach ($target_id_list as $target_id) {
            static::createMessage(['send_from' => $sender->id(), 'send_to' => $target_id, 'message' => $message, 'type' => $type]);
            $count += 1;
        }
        return $count > 0;
    }

    /**
     * Mark events as read for a given user
     *
     * @param int $user_id
     * @return void
     */
    public static function readEvents(int $user_id): bool
    {
        $bindings = [':to' => $user_id];
        $count = update_query("UPDATE events SET unread = 0 WHERE send_to = :to", $bindings);
        return $count > 0;
    }

    /**
     * Mark some type of messages as read for a given user
     *
     * @param int $user_id
     * @return void
     */
    public static function readMessages(int $user_id, $type = null): bool
    {
        $bindings = [':to' => $user_id];
        $add_type = '';
        if ($type !== null) {
            $add_type = ' AND type = :type';
            $bindings[':type'] = $type;
        }
        $count = update_query("UPDATE messages SET unread = 0 WHERE send_to = :to" . $add_type, $bindings);
        return $count > 0;
    }

    public static function unreadCommunications(int $user_id): array
    {
        $events = query_item("SELECT count(event_id) FROM events WHERE send_to = :to AND unread = 1", [':to' => $user_id]);
        $messages = query_item("SELECT count(message_id) FROM messages WHERE send_to = :to AND unread = 1", [':to' => $user_id]);
        return ['events' => $events, 'messages' => $messages];
    }

    public static function countByReceiver(int $ninja_id, int $type): int
    {
        return query_item("SELECT count(message_id) FROM messages WHERE send_to = :to AND type = :type", [':to' => $ninja_id, ':type' => $type]);
    }

    public static function deleteByReceiver(int $ninja_id, int $type): int
    {
        return update_query("DELETE FROM messages WHERE send_to = :to AND type = :type", [':to' => $ninja_id, ':type' => $type]);
    }

    /**
     * @see https://localhost:8765/api?type=sendCommunications&json=1
     */
    public static function mailEvents(Player $char, $events, $messages): bool
    {
        // account by the char
        $account = Account::findByChar($char);

        $parts    = [
            'events'   => $events,
            'messages' => $messages,
            'has_clan' => (bool)Clan::findByMember($char),
            'char'     => $char,
        ];

        $template = new NWTemplate();

        $rendered_messages = $template->simpleRender('email.messages.tpl', $parts);
        $rendered_events = $template->simpleRender('events.tpl', $parts);
        $email = $account->active_email;
        $subject = 'NinjaWars.net: Your Recent Game Events';
        $body = $rendered_messages . $rendered_events;
        $from = [SYSTEM_EMAIL => SYSTEM_EMAIL_NAME];
        $nmail = new Nmail($email, $subject, $body, $from);
        $nmail->setReplyTo([SUPPORT_EMAIL => SUPPORT_EMAIL_NAME]);
        $sent = $nmail->send();
        return $sent;
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
}
