<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;

class Event {
    /**
     * For events, attacks, kills, invites, etc, and no user-created messages
     *
     * @param int $from_id
     * @param int $to_id
     * @param String $message
     * @return void
     * @throws Exception
     */
    public static function create($from_id, $to_id, $message) {
        if (!is_numeric($from_id) || !is_numeric($to_id)) {
            throw new \Exception('A player id wasn\'t sent in to the Event::create function.');
        }

        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("INSERT INTO events (event_id, send_from, send_to, message, date)
            VALUES (default, :from, :to, :message, now())");
        $statement->bindValue(':from', $from_id);
        $statement->bindValue(':to', $to_id);
        $statement->bindValue(':message', $message);
        $statement->execute();
    }

    /**
     * @return int Number of rows deleted
     */
    public static function deleteOldEvents() {
        $statement = query("delete from events where date < ( now() - '31 days'::interval)");
        return $statement->rowCount();
    }
}
