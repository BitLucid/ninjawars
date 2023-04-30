<?php

namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\extensions\StreamedViewResponse;

/**
 * Handles the rumors and info displayed by the bathhouse.
 */
class RumorController extends AbstractController
{
    public const ALIVE = false;
    public const PRIV  = false;

    private function notableDuels($limit=50, $simple_limit=5): array
    {
        return query_array("(SELECT dueling_log.*, attackers.player_id AS attacker_id, defenders.player_id AS defender_id FROM dueling_log JOIN players AS attackers ON attackers.uname = attacker JOIN players AS defenders ON defender = defenders.uname 
        where (dueling_log.killpoints != 1 OR dueling_log.won is not true) LIMIT :limit)
        UNION
(SELECT dueling_log.*, attackers.player_id AS attacker_id, defenders.player_id AS defender_id FROM dueling_log JOIN players AS attackers ON attackers.uname = attacker JOIN players AS defenders ON defender = defenders.uname 
        where (dueling_log.killpoints = 1 AND dueling_log.won is true) ORDER BY id DESC LIMIT :simple_limit);", [':limit'=>$limit, ':simple_limit'=>$simple_limit]);
    }

    public function index(Container $p_dependencies)
    {
        $stats           = $this->stats();
        $parts           = [
            'stats'          => $stats,
            'vicious_killer' => $stats['vicious_killer'],
            'duels'          => $this->notableDuels(),
        ];

        return new StreamedViewResponse('Bath House', 'duel.tpl', $parts, ['quickstat'=>false]);
    }

    /**
     * Stats on recent activity and other aggregate counts/information.
     *
     * @return array
     */
    private function stats()
    {
        $stats = [];
        $stats['vicious_killer'] = query_item('SELECT stat_result from past_stats where id = 4');

        $stats['player_count'] = query_item("SELECT count(player_id) FROM players WHERE active = 1");

        // Give just an approximation of some high gold amount
        $stats['rich_haul'] = query_item("SELECT floor(max(gold)/1000)*1000 FROM players WHERE active = 1");

        return $stats;
    }
}
