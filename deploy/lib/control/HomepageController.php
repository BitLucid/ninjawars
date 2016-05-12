<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\data\Clan;

/**
 * display the standard homepage, and maybe eventually the splash page
 */
class HomepageController extends AbstractController {
    const PRIV      = false;
    const ALIVE     = false;
    private $loggedIn = false;

    /**
     * Stores logged-in status of user in member variable for use later
     */
    public function __construct() {
        $this->loggedIn = (bool) SessionFactory::getSession()->get('player_id');
    }

    /**
     * Parse whether to display the splash page or the logged-in homepage.
     *
     * @return Response
     */
    public function index() {
        return ($this->loggedIn ? $this->game() : $this->splash());
    }

    /**
     * The standard homepage
     *
     * @return Response
     */
    private function game() {
        // Get the actual values of the vars.
        $ninja = Player::find(SessionFactory::getSession()->get('player_id'));
        $playerInfo = $ninja->data();
        $clan = Clan::findByMember($ninja);

        $unreadCount = Message::where([
            'send_to' => $ninja->id(),
            'unread'  => 1,
        ])->count();

        // Assign these vars to the template.
        $parts = [
            'main_src'             => '/intro',
            'body_classes'         => 'main-body',
            'version'              => 'NW Version 1.7.5 2010.12.05',
            'ninja'                => $ninja,
            'player_info'          => $playerInfo,
            'clan'                 => $clan,
            'unread_message_count' => $unreadCount,
        ];

        return new StreamedViewResponse('Live by the Shuriken', 'index.tpl', $parts, [ 'is_index' => true ]);
    }

    /**
     * The main starting splash homepage (for logged-out user)
     *
     * @todo Make version dynamic based on actual version of app
     * @return Response
     */
    private function splash() {
        // Assign these vars to the template.
        $parts = [
            'main_src'     => '/intro',
            'body_classes' => 'main-body splash',
            'version'      => 'NW Version 1.8.0 2014.06.30',
        ];

        return new StreamedViewResponse('Live by the Shuriken', 'splash.tpl', $parts, [ 'is_index' => true ]);
    }
}
