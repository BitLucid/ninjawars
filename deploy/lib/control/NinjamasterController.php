<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\AdminViews;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Shop;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use InvalidArgumentException;
use NinjaWars\core\control\ShopController;

/**
 * The ninjamaster/admin info
 */
class NinjamasterController extends AbstractController {
    public const ALIVE = false;
    public const PRIV  = false;

    /**
     * Check user authentication as an admin before continuing.
     */
    private function checkAuth(Container $p_dependencies) {
        if (!$p_dependencies['current_player'] || !$p_dependencies['current_player']->isAdmin()) {
            return new RedirectResponse(WEB_ROOT);
        } else {
            return true;
        }
    }

    /**
     * Display the main admin area
     *
     * Includes player viewing, account duplicates checking, npc balacing
     */
    public function index(Container $p_dependencies): Response {
        $request = RequestWrapper::$request;
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }

        $error            = null;
        $char_infos        = null;
        $char_inventory    = null;
        $first_message     = null;
        $first_char       = null;
        $first_account     = null;
        $first_description = null;
        $dupes            = AdminViews::dupedIps();
        $stats            = AdminViews::highRollers();
        $usage           = AdminViews::recentUsage();
        $npcs             = NpcFactory::allNonTrivialNpcs();
        $trivial_npcs      = NpcFactory::allTrivialNpcs();

        $items = $this->items($p_dependencies);

        $initial_char_ids = $request->get('view');
        $char_ids  = $initial_char_ids ? preg_split("/[,\s]+/", $initial_char_ids) : null;
        $char_name = trim($request->get('char_name'));

        if ($char_name) { // View a target character
            $first_char = Player::findByName($char_name);
            if (null !== $first_char && null !== $first_char->id()) {
                $char_ids  = [$first_char->id()];
            }
        }

        if (is_array($char_ids)) {
            // Get a different first character if an array is specified
            $first_char = $first_char ? $first_char : Player::find((int) reset($char_ids));
            if ($first_char) {
                assert($first_char instanceof Player);
                $first_account     = Account::findByChar($first_char);
                $char_inventory    = AdminViews::charInventory($first_char);
                $first_message     = $first_char->messages;
                $first_description = $first_char->description;
            }
            // All the rest multi-character table view
            try {
                $char_infos        = is_numeric($char_ids) || is_array($char_ids) ? AdminViews::charInfos($char_ids) : [];
            } catch (InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }

        $parts = [
            'error'             => $error,
            'stats'             => $stats,
            'usage'             => $usage,
            'first_char'        => $first_char,
            'first_description' => $first_description,
            'first_message'     => $first_message,
            'first_account'     => $first_account,
            'char_infos'        => $char_infos,
            'dupes'             => $dupes,
            'char_inventory'    => $char_inventory,
            'char_name'         => $char_name,
            'npcs'              => $npcs,
            'items'             => $items,
            'trivial_npcs'      => $trivial_npcs,
        ];

        return new StreamedViewResponse('Admin Actions', 'ninjamaster.tpl', $parts);
    }

    /**
     * Pull the items for administrative review
     */
    public function items($p_dependencies) {
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }

        $item_costs = Shop::fullItems(true); // Show administrative entries.
        return $item_costs;
    }

    /**
     * Display the tools page
     *
     * @return Response
     */
    public function tools(Container $p_dependencies): Response {
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }
        return new StreamedViewResponse('Admin Tools', 'page.tools.tpl', [], [ 'private' => false ]);
    }

    /**
     * Display a list of characters ranked by score/difficulty.
     *
     * @return Response
     */
    public function player_tags(Container $p_dependencies): Response {
        $authed = $this->checkAuth($p_dependencies);
        if ($authed instanceof RedirectResponse) {
            return $authed;
        }
        $parts = [
            'player_size' => $this->playerSize(),
        ];
        return new StreamedViewResponse('Player Character Tags', 'character-tag-cloud.tpl', $parts, [ 'quickstat' => false ]);
    }

    /**
     * Get the tag of player activity/score
     */
    private function playerSize(): array {
        $res = [];
        $sel = "SELECT 
            (level - 3 - round(days/100)) AS sum, 
            round(stamina + strength + speed + greatest(health, 1) / 6 + level - status - greatest(days, 1) / 50  + active) as score, 
            player_id, 
            uname 
                FROM players 
                WHERE 
                    active = 1 AND 
                    health > 0 
                ORDER BY sum DESC";
        $player_info_list = query($sel); // Gets a resultset
        $max = 0;
        while ($player_info = $player_info_list->fetch()) {
            $max = max($player_info['sum'], $max);
            // make percentage of highest, multiply by 10 and round to give a 1-10 size
            $res[$player_info['uname']] = [
                'player_id' => $player_info['player_id'],
                'size'      => $this->calculatePlayerSize($player_info['sum'], $max),
                'score'     => $player_info['score']
            ];
        }

        return $res;
    }

    /**
     * @param int $p_rank
     * @param int $p_max
     * @return int
     */
    private function calculatePlayerSize($p_rank, $p_max): int {
        return (int) floor(((($p_rank-1 < 1 ? 0 : $p_rank-1)) / $p_max)*10)+1;
    }
}
