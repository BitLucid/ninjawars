<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\Filter;
use \PDO;
use PDOStatement;

/**
 * Model for manipulating enemies
 */
class Enemies
{
    const TABLE = 'enemies';
    const MAX_ENEMIES = 10;

    /**
     * Get all enemies for a given player
     *
     * @param Player $player
     * @return array
     */
    public static function getAllForPlayer(Player $player): array
    {
        return query_array(
            'SELECT * FROM ' . self::TABLE . ' WHERE player_id = :player_id',
            ['player_id' => $player->id()]
        );
    }

    /**
     * Retrieve enemies for the player specified
     *
     * @param Player $player
     * @return array
     */
    public static function getCurrentEnemies(Player $player): array
    {
        $query = 'SELECT player_id, active, level, uname, health FROM players LEFT JOIN enemies ON _enemy_id = player_id
            WHERE _player_id = :pid AND active = 1 ORDER BY health > 0 DESC, health ASC, level DESC';
        return query_array($query, [':pid' => [$player->id(), PDO::PARAM_INT]]);
    }

    /**
     * Count of enemies for the player
     */
    public static function count(Player $player): int
    {
        $query = 'SELECT COUNT(_enemy_id) FROM enemies WHERE _player_id = :pid';
        return query_item($query, [':pid' => [$player->id(), PDO::PARAM_INT]]);
    }

    /**
     * Get a specific enemy of a player
     *
     * @param int $player_id
     * @return array
     */
    public static function getAllForPlayerAndEnemy(Player $player, int $enemy_id)
    {
        return query_array(
            'SELECT * FROM ' . self::TABLE . ' WHERE _player_id = :player_id AND _enemy_id = :enemy_id',
            [':player_id' => [$player->id(), PDO::PARAM_INT], ':enemy_id' => [(int)$enemy_id, PDO::PARAM_INT]]
        );
    }

    /**
     * Add an enemy for a player
     * @note: skip inserting more if the max is already reached
     */
    public static function add(Player $player, $enemy_id): array
    {
        return static::count($player) >= self::MAX_ENEMIES ? [] : insert_query(
            'INSERT INTO ' . self::TABLE . ' (_player_id, _enemy_id) VALUES (:player_id, :enemy_id) ON CONFLICT DO NOTHING',
            [':player_id' => [$player->id(), PDO::PARAM_INT], ':enemy_id' => [$enemy_id, PDO::PARAM_INT]],
            false
        );
    }

    /**
     * Remove an enemy for a player
     */
    public static function remove(Player $player, int $enemy_id): array | \PDOStatement
    {
        return query(
            'DELETE FROM ' . self::TABLE . ' WHERE _player_id = :player_id AND _enemy_id = :enemy_id',
            [':player_id' => [$player->id(), PDO::PARAM_INT], ':enemy_id' => [$enemy_id, PDO::PARAM_INT]]
        );
    }

    /**
     * Search for enemies
     */
    public static function search(Player $player, string $search_term, int $limit = 11): array
    {
        // Doesn't really cause any problems to allow like match characters to pass through here.
        $sel = "SELECT player_id, uname FROM players
            WHERE uname ilike :matchString || '%' AND active = 1 AND player_id != :user
            ORDER BY level LIMIT :limit";
        $enemies = query_array(
            $sel,
            [
                ':matchString' => [$search_term, PDO::PARAM_STR],
                ':user'        => [$player->id(), PDO::PARAM_INT],
                ':limit'       => [$limit, PDO::PARAM_INT],
            ]
        );
        return $enemies;
    }
}
