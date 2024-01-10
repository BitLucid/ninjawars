<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Shop;
use NinjaWars\core\data\Npc;
use NinjaWars\core\data\Item;
use NinjaWars\core\control\Combat;
use model\News as News;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * Epic Controller for UI Stories
 */
class EpicsController extends AbstractController
{
    public const ALIVE = false;
    public const PRIV  = false;

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

    private function filterNpcData($npcs, $npc_id)
    {
        foreach ($npcs as $npc) {
            if ($npc['name'] == $npc_id) {
                return $npc;
            }
        }
        return null;
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
        $npco             = new Npc($this->filterNpcData($other_npcs, 'Oni')); // Construct the npc object.
        $item             = Item::findByIdentity('dimmak');
        $npc_damage_class = Combat::determineDamageClass(9999, 10);
        $transientClass = new \stdclass();
        $transientClass->enteredName = 'CoolNameGuy';
        $transientClass->enteredEmail = 'coolNameGuy@example.com';
        $transientClass->enteredPass = 'dragon';
        $transientClass->enteredCPass = 'dragon';
        $transientClass->enteredClass = 'dragon';
        $signupRequest2    = $transientClass;
        $news = new News();
        $all_news = $news->all();

        $error            = null;
        $static_nodes = include(ROOT . 'lib/data/raw/nodes.php');

        $parts = [
            'nodes'             => $static_nodes,
            'npcs'              => $npcs,
            'npco'              => $npco,
            'item'              => $item,
            'npc_damage_class'  => $npc_damage_class,
            'other_npcs'        => $other_npcs,
            'error'             => $error,
            'char'              => $char,
            'ninja'             => $char,
            'clan'              => $clan,
            'myClan'            => $clan,
            'item_costs'        => Shop::itemForSaleCosts(),
            'full_item_costs'   => Shop::fullItems(true),
            'clans'             => Clan::rankings(),
            'signupRequest2'    => $signupRequest2,
            'all_news'          => $all_news,
        ];

        return new StreamedViewResponse('UI Epics', 'epics.tpl', $parts);
    }
}
