<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;

/**
 * Handle the listing of events
 */
class EventsController extends AbstractController {
    const ALIVE = false;
    const PRIV  = true;

    /**
     * Display the combat/action events and mark them as read when displayed.
     *
     * @return Response
     */
    public function index() {
    	$char   = Player::find(SessionFactory::getSession()->get('player_id'));
		$events = $this->getEvents($char->id(), 300);

		$this->readEvents($char->id()); // mark events as viewed.

        $parts    = [
            'events'   => $events,
            'has_clan' => (bool)Clan::findByMember($char),
            'char'     => $char,
        ];

        return new StreamedViewResponse('Events', 'events.tpl', $parts, ['quickstat' => 'player']);
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

        return query("SELECT coalesce(send_from, 0) AS send_from, message, unread, date, uname AS from FROM events
            LEFT JOIN players ON send_from = player_id WHERE send_to = :to ORDER BY date DESC
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
