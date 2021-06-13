<?php
namespace NinjaWars\core\control;

use Pimple\Container;
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
    const NW_VERSION = 'v1.12.2 2021.06.12';

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
    public function index(Container $p_dependencies) {
        return ($this->loggedIn ? $this->game($p_dependencies) : $this->splash());
    }

    /**
     * The standard homepage
     *
     * @return Response
     */
    private function game(Container $p_dependencies) {
        // Get the actual values of the vars.
        $ninja = $p_dependencies['current_player'] ?? new Player();
        $playerInfo = $ninja? $ninja->data() : [];
        $clan = $ninja? Clan::findByMember($ninja) : null;

        $unreadCount = Message::where([
            'send_to' => $ninja->id(),
            'unread'  => 1,
        ])->count();

        // Assign these vars to the template.
        $parts = [
            'main_src'             => '/intro',
            'body_classes'         => 'main-body',
            'version'              => self::NW_VERSION,
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
            'version'      => self::NW_VERSION,
        ];

        return new StreamedViewResponse('Live by the Shuriken', 'splash.tpl', $parts, [ 'is_index' => true ]);
    }
}