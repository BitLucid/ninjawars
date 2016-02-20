<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Message;
use \Player as Player;

/**
 * display the standard homepage, and maybe eventually the splash page
 */
class HomepageController {
    const PRIV      = false;
    const ALIVE     = false;
    private $loggedIn = false;

    /**
     * Stores logged-in status of user in member variable for use later
     */
    public function __construct() {
        $this->loggedIn = (bool) self_char_id();
    }

    /**
     * Parse whether to display the splash page or the logged-in homepage.
     *
     * @return ViewSpec
     */
    public function index() {
        return ($this->loggedIn ? $this->game() : $this->splash());
    }

    /**
     * The standard homepage
     *
     * @return ViewSpec
     */
    private function game() {
        // Get the actual values of the vars.
        $playerInfo = self_info();
        $ninja = new Player(self_char_id());

        $unreadCount = Message::where([
            'send_to' => $ninja->id(),
            'unread'  => 1,
        ])->count();

        $memberCounts = member_counts();

        // Assign these vars to the template.
        $parts = [
            'main_src'             => '/intro',
            'body_classes'         => 'main-body',
            'version'              => 'NW Version 1.7.5 2010.12.05',
            'ninja'                => $ninja,
            'player_info'          => $playerInfo,
            'unread_message_count' => $unreadCount,
            'members'              => $memberCounts['active'],
            'membersTotal'         => $memberCounts['total'],
        ];

        return [
            'template' => 'index.tpl',
            'title'    => 'Live by the Shuriken',
            'parts'    => $parts,
            'options'  => [ 'is_index' => true ],
        ];
    }

    /**
     * The main starting splash homepage (for logged-out user)
     *
     * @todo Make version dynamic based on actual version of app
     * @return ViewSpec
     */
    private function splash() {
        $memberCounts = member_counts();

        // Assign these vars to the template.
        $parts = [
            'main_src'     => '/intro',
            'body_classes' => 'main-body splash',
            'version'      => 'NW Version 1.8.0 2014.06.30',
            'members'      => $memberCounts['active'],
            'membersTotal' => $memberCounts['total'],
        ];

        return [
            'template' => 'splash.tpl',
            'title'    => 'Live by the Shuriken',
            'parts'    => $parts,
            'options'  => [ 'is_index' => true ],
        ];
    }
}
