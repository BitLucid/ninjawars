<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\NpcFactory;
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
        $request = RequestWrapper::$request;
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }

        $other_npcs       = NpcFactory::npcsData();
        $npcs             = NpcFactory::customNpcs();

        $error            = null;
        $static_nodes = include(ROOT . 'lib/data/raw/nodes.php');

        $parts = [
            'nodes' => $static_nodes,
            'npcs' => $npcs,
            'other_npcs' => $other_npcs,
            'error'             => $error,
        ];

        return new StreamedViewResponse('UI Epics', 'epics.tpl', $parts);
    }
}
