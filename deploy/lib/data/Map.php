<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data;
use PDO;

/**
 * Use for obtaining targets/npcs from the map
 */
class Map
{
    /**
     * Retrieve the first $limit npcs close to a difficulty
     * Should be deterministic
     */
    public static function nearbyNpcs($current_difficulty, $limit)
    {
        if ($limit < 1) {
            return [];
        } else {
            $sort = function ($a, $b) {
                return $b->difficulty() - $a->difficulty();
            };
            $npcs = NpcFactory::npcs($sort);
        }
        // Return the first $limit npcs that are at least $difficulty

        return $npcs;
    }

    /**
     * Get a list of ninja
     */
    public static function nearbyNinja(Player $char, int $limit = 11): array
    {
        $sel =
            '
            SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id 
            WHERE active = 1 AND level <= (5 + :char_level) AND health > 0 
                AND player_id != :char_id2
            ORDER BY score < (SELECT score FROM player_rank WHERE _player_id = :char_id) desc, score DESC LIMIT :limit';
        $targets = query_array(
            $sel,
            [
                ':char_id'  => [$char->id(), PDO::PARAM_INT],
                ':char_id2'  => [$char->id(), PDO::PARAM_INT],
                ':char_level' => [$char->level, PDO::PARAM_INT],
                ':limit'      => [$limit, PDO::PARAM_INT],
            ]
        );
        return $targets;
    }

    /**
     * Get a list of ninja and npcs
     */
    public static function nearbyList(Player $char, int $limit = 11): array
    {
        $ninja = self::nearbyNinja($char, $limit);
        $npcs = self::nearbyNpcs($char->difficulty(), $limit - count($ninja));
        return array_merge($ninja, $npcs);
    }
}
