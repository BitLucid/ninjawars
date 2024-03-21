<?php

namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Communication;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\NWTemplate;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use Nmail;

/**
 * Handle the listing of game event messages for a player character
 */
class EventsController extends AbstractController
{
    public const ALIVE = false;
    public const PRIV  = true;

    /**
     * Display the combat/action events and mark them as read when displayed.
     *
     * @return Response
     */
    public function index()
    {
        $char   = Player::find(SessionFactory::getSession()->get('player_id'));
        $events = Communication::getEvents($char->id(), 300);

        Communication::readEvents($char->id()); // mark events as viewed.

        $parts    = [
            'events'   => $events,
            'has_clan' => (bool)Clan::findByMember($char),
            'char'     => $char,
        ];

        return new StreamedViewResponse('Events', 'events.tpl', $parts, ['quickstat' => 'player']);
    }
}
