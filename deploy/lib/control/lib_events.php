<?php
use NinjaWars\core\data\DatabaseConnection;

/**
 * Now only a wrapper for the send_event function
 *
 * @param String $from
 * @param String $to
 * @param String $msg
 * @return void
 */
function sendMessage($from, $to, $msg) {
    $from_id = (int) get_char_id($from);
    $to_id = get_char_id($to);
    send_event($from_id, $to_id, $msg);
}

/**
 * For events, attacks, kills, invites, etc, and no user-created messages
 *
 * @param int $fromId
 * @param int $toId
 * @param String $msg
 * @return void
 * @throws Exception
 */
function send_event($fromId, $toId, $msg) {
    if (!$toId) {
        $toId = self_char_id();
    }

    if (!is_numeric($fromId) || !is_numeric($toId)) {
        throw new \Exception('A player id wasn\'t sent in to the send_event function.');
    }

    DatabaseConnection::getInstance();
    $statement = DatabaseConnection::$pdo->prepare("INSERT INTO events (event_id, send_from, send_to, message, date)
        VALUES (default, :from, :to, :message, now())");
    $statement->bindValue(':from', $fromId);
    $statement->bindValue(':to', $toId);
    $statement->bindValue(':message', $msg);
    $statement->execute();
}

/**
 * Retrieve events by user
 *
 * @param int $userId
 * @param String $limit
 * @return Resultset
 */
function get_events($userId, $limit=null) {
    $params = [':to' => $userId];

    if ($limit) {
        $params[':limit'] = $limit;
    }

    return query("SELECT send_from, message, unread, date, uname AS from FROM events
        JOIN players ON send_from = player_id WHERE send_to = :to ORDER BY date DESC
        ".($limit ? "LIMIT :limit" : ''), $params);
}

/**
 * Mark events as read for a given user
 *
 * @param int $userId
 * @return void
 */
function read_events($userId) {
    DatabaseConnection::getInstance();
    $statement = DatabaseConnection::$pdo->prepare("UPDATE events SET unread = 0 WHERE send_to = :to");
    $statement->bindValue(':to', $userId);
    $statement->execute();
}
