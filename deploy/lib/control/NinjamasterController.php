<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT.'control/lib_player_list.php');
require_once(LIB_ROOT.'control/lib_player.php'); // For player tags

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\AccountFactory;
use NinjaWars\core\data\AdminViews;

/**
 * The ninjamaster/admin info
 */
class NinjamasterController {
    const ALIVE = false;
    const PRIV  = false;
    protected $charId = null;
    protected $self = null;

    public function __construct() {
        $this->charId = self_char_id();
        $this->self = Player::find($this->charId);
    }

    /**
     * If the player isn't logged in, or isn't admin, return a redirect
     *
     * @return RedirectResponse|boolean
     */
    private function requireAdmin($player) {
        if ($player === null || !$player instanceof Player || !$player->isAdmin()) {
            // Redirect to the root site.
            return new RedirectResponse(WEB_ROOT);
        } else {
            return true;
        }
    }

    /**
     * Display the main admin area
     *
     * Includes player viewing, account duplicates checking, npc balacing
     *
     * @return ViewSpec|RedirectResponse
     */
    public function index() {
        $result = $this->requireAdmin($this->self);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        $viewChar = null;

        // View a target non-self character
        $charName = in('char_name');
        if (is_string($charName) && trim($charName)) {
            $viewChar = get_char_id($charName);
        }

        // If a request is made to view a character's info, show it.
        $viewChar = first_value($viewChar, in('view'));

        $dupes = AdminViews::duped_ips();
        $stats = AdminViews::high_rollers();

        $npcs        = NpcFactory::allNonTrivialNpcs();
        $trivialNpcs = NpcFactory::allTrivialNpcs();

        $charInfos        = null;
        $charInventory    = null;
        $firstMessage     = null;
        $firstChar        = null;
        $firstAccount     = null;
        $firstDescription = null;

        if ($viewChar) {
            $ids              = explode(',', $viewChar);
            $firstChar        = new Player(reset($ids));
            $firstAccount     = AccountFactory::findByChar($firstChar);
            $charInfos        = AdminViews::split_char_infos($viewChar);
            $charInventory    = AdminViews::char_inventory($viewChar);
            $firstMessage     = $firstChar->message();
            $firstDescription = $firstChar->description();
        }

        $parts = [
            'stats'             => $stats,
            'first_char'        => $firstChar,
            'first_description' => $firstDescription,
            'first_message'     => $firstMessage,
            'first_account'     => $firstAccount,
            'char_infos'        => $charInfos,
            'dupes'             => $dupes,
            'char_inventory'    => $charInventory,
            'char_name'         => $charName,
            'npcs'              => $npcs,
            'trivial_npcs'      => $trivialNpcs,
        ];

        return [
            'title'    => 'Admin Actions',
            'template' => 'ninjamaster.tpl',
            'parts'    => $parts,
            'options'  => null,
        ];
    }

    /**
     * Display the tools page
     *
     * @return ViewSpec|RedirectResponse
     */
    public function tools() {
        $result = $this->requireAdmin($this->self);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return [
            'title'    => 'Admin Tools',
            'template' => 'page.tools.tpl',
            'parts'    => [],
            'options'  => [ 'private' => false ],
        ];
    }

    /**
     * Display a list of characters ranked by score/difficulty.
     *
     * @return ViewSpec|RedirectResponse
     */
    public function player_tags() {
        $result = $this->requireAdmin($this->self);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return [
            'title'    => 'Player Character Tags',
            'template' => 'player-tags.tpl',
            'parts'    => [ 'player_size' => player_size() ],
            'options'  => [ 'quickstat' => false ],
        ];
    }

}
