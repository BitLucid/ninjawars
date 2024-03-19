<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\NWTemplate;
use Nmail;
use PDOStatement;

/**
 * Class for generating communications to the account about internal activity
 */

class Communication
{
    public static function sendEvents($data)
    {
        ['char' => $char] = $data;
        if (!($char instanceof Player)) {
            throw new \InvalidArgumentException('Communication::sendEvents requires a player object');
        }


        $events = self::getEvents($char->id(), 300);
        self::mailEvents($char, $events);
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
     * Mark events as read for a given user
     *
     * @param int $user_id
     * @return void
     */
    public static function readEvents(int $user_id)
    {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("UPDATE events SET unread = 0 WHERE send_to = :to");
        $statement->bindValue(':to', $user_id);
        $statement->execute();
        return true;
    }

    public static function mailEvents(Player $char, $events)
    {
        // account by the char
        $account = Account::findByChar($char);

        $parts    = [
            'events'   => $events,
            'has_clan' => (bool)Clan::findByMember($char),
            'char'     => $char,
        ];

        $template = new NWTemplate();

        $rendered_messages = $template->simpleRender('email.messages.tpl', $parts);
        $rendered_events = $template->simpleRender('events.tpl', $parts);
        $email = $account->active_email;
        $subject = 'NinjaWars.net: Your Recent Events';
        $body = $rendered_messages . $rendered_events;
        $from = [SYSTEM_EMAIL => SYSTEM_EMAIL_NAME];
        $nmail = new Nmail($email, $subject, $body, $from);
        $nmail->setReplyTo([SUPPORT_EMAIL => SUPPORT_EMAIL_NAME]);
        $nmail->send();
    }
}
