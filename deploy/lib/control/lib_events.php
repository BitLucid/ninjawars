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

