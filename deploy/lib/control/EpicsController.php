<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Shop;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * Epic Controller for UI Stories
 */
class EpicsController extends AbstractController
{
    const ALIVE = false;
    const PRIV  = false;

    /**
     * Check user authentication as an admin before continuing.
     */
    private function checkAuth(Container $p_dependencies)
    {
        if (!$p_dependencies['current_player'] || !$p_dependencies['current_player']->isAdmin()) {
            return new RedirectResponse(WEB_ROOT);
        } else {
            return true;
        }
    }

    /**
     * Epics for raw ui stories
     */
    public function index(Container $p_dependencies): Response
    {
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }

        $char             = Player::find(SessionFactory::getSession()->get('player_id'));
        $clan             = $char->getClan();
        $other_npcs       = NpcFactory::npcsData();
        $npcs             = NpcFactory::customNpcs();

        $error            = null;
        $static_nodes = include(ROOT . 'lib/data/raw/nodes.php');

        $parts = [
            'nodes'             => $static_nodes,
            'npcs'              => $npcs,
            'other_npcs'        => $other_npcs,
            'error'             => $error,
            'char'              => $char,
            'ninja'             => $char,
            'clan'              => $clan,
            'myClan'            => $clan,
            'item_costs'        => Shop::itemForSaleCosts(),
            'clans'     => Clan::rankings(),
        ];

        return new StreamedViewResponse('UI Epics', 'epics.tpl', $parts);
    }
}
