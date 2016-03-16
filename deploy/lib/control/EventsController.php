<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Player;

/**
 * Handle the listing of events
 */
class EventsController {
    const ALIVE          = false;
    const PRIV           = true;

    /**
     * Display the combat/action events and mark them as read when displayed.
     */
    public function index(){
    	$char = new Player(self_char_id());
		$events = $this->getEvents($char->id(), 300);

		// Check for clan to use it in the nav tabs.
		$has_clan  = (bool)ClanFactory::clanOfMember($char);

		$this->readEvents($char->id()); // mark events as viewed.

		$template = 'events.tpl';
		$title = 'Events';
		$parts = ['events'=>$events, 'has_clan'=>$has_clan, 'char'=>$char];
		$options = ['quickstat' => 'player'];
		return [
			'title'=>$title,
			'template'=>$template,
			'parts'=>$parts,
			'options'=>$options
			];
    }

    /**
     * Retrieve events by user
     *
     * @param int $user_id
     * @param String $limit
     * @return Resultset
     */
    private function getEvents($user_id, $limit=null) {
        $params = [':to' => $user_id];

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
     * @param int $user_id
     * @return void
     */
    private function readEvents($user_id) {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("UPDATE events SET unread = 0 WHERE send_to = :to");
        $statement->bindValue(':to', $user_id);
        $statement->execute();
    }
}
