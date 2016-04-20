<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use \PDO;

/**
 * Display ninja & monsters to potentially pick fights with
 */
class ConsiderController extends AbstractController {
    const ALIVE       = false;
    const PRIV        = false;
    const ENEMY_LIMIT = 20;

    /**
     * Show the intial consider page
     */
    public function index() {
        return $this->render($this->configure());
    }

    /**
     * Search for enemies to remember.
     */
    public function search() {
        $enemy_match = RequestWrapper::getPostOrGet('enemy_match');
        $found_enemies = ($enemy_match ? $this->getEnemyMatches(SessionFactory::getSession()->get('player_id'), $enemy_match) : null);
        $parts = $this->configure();

        // Add some additional parts
        $parts = array_merge($parts, [
            'found_enemies' => $found_enemies,
            'enemy_match'   => $enemy_match,
        ]);

        return $this->render($parts);
    }

    /**
     * Add an enemy to pc's list if valid.
     */
    public function addEnemy() {
        $enemy = Player::find(RequestWrapper::getPostOrGet('add_enemy'));

        if ($enemy) {
            $this->addEnemyToPlayer(Player::find(SessionFactory::getSession()->get('player_id')), $enemy);
        }

        return new RedirectResponse('/enemies');
    }

    /**
     * Take an enemy off a pc's list.
     */
    public function deleteEnemy() {
        $enemy = Player::find(RequestWrapper::getPostOrGet('remove_enemy'));

        if ($enemy) {
            $this->removeEnemyFromPlayer(Player::find(SessionFactory::getSession()->get('player_id')), $enemy);
        }

        return new RedirectResponse('/enemies');
    }

    /**
     * @TODO Document me!
     */
    private function configure() {
        $char             = Player::find(SessionFactory::getSession()->get('player_id'));
        $peers            = ($char ? $this->getNearbyPeers($char->id()) : []);
        $active_ninjas    = Player::findActive(5, true);
        $char_info        = ($char ? $char->data() : []);
        $other_npcs       = NpcFactory::npcsData();
        $npcs             = NpcFactory::customNpcs();
        $enemy_list       = ($char ? $this->getCurrentEnemies($char->id()) : []);
        $recent_attackers = ($char ? $this->getRecentAttackers($char) : []);

        return [
            'logged_in'        => (bool)$char,
            'enemy_list'       => $enemy_list,
            'char_name'        => ($char ? $char->name() : ''),
            'npcs'             => $npcs,
            'other_npcs'       => $other_npcs,
            'char_info'        => $char_info,
            'active_ninjas'    => $active_ninjas,
            'recent_attackers' => $recent_attackers,
            'enemy_list'       => $enemy_list,
            'peers'            => $peers,
        ];
    }

    /**
     * Render the parts, since the template is always currently the same.
     */
    private function render($parts) {
        return new StreamedViewResponse('Fight', 'enemies.tpl', $parts, ['quickstat'=>false]);
    }

    /**
     * Search for enemies to add.
     *
     * @param int $p_playerId
     * @param string $p_pattern
     * @return array
     */
    private function getEnemyMatches($p_playerId, $p_pattern) {
        // Doesn't really cause any problems to allow like match characters to pass through here.
        $sel = "SELECT player_id, uname FROM players
            WHERE uname ilike :matchString || '%' AND active = 1 AND player_id != :user
            ORDER BY level LIMIT 11";

        $enemies = query_array(
            $sel,
            [
                ':matchString' => $p_pattern,
                ':user'        => $p_playerId,
            ]
        );

        return $enemies;
    }

    /**
     * Retrieve enemies for the player specified
     *
     * @param int $p_playerId
     * @return resulset
     */
    private function getCurrentEnemies($p_playerId) {
        $query = 'SELECT player_id, active, level, uname, health FROM players JOIN enemies ON _enemy_id = player_id AND _player_id = :pid
            WHERE active = 1 ORDER BY health DESC, level DESC';
        return query($query, [':pid'=>$p_playerId]);
    }

    /**
     * Add a certain enemy to the enemy list.
     *
     * @param Player $p_player
     * @param Player $p_enemy
     * @return void
     */
    private function addEnemyToPlayer(Player $p_player, Player $p_enemy) {
        $this->removeEnemyFromPlayer($p_player, $p_enemy);

        DatabaseConnection::getInstance();
        $query = 'INSERT INTO enemies (_player_id, _enemy_id) VALUES (:pid, :eid)';
        $statement = DatabaseConnection::$pdo->prepare($query);
        $statement->bindValue(':pid', $p_player->id());
        $statement->bindValue(':eid', $p_enemy->id());
        $statement->execute();
    }

    /**
     * Select characters right nearby in ranking score, up and down.
     *
     * @param int $p_playerId
     * @return array
     */
    private function getNearbyPeers($p_playerId) {
        $sel =
            "(SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id WHERE score >
            (SELECT score FROM player_rank WHERE _player_id = :char_id) AND active = 1 AND health > 0 ORDER BY score ASC LIMIT 5)
            UNION
            (SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id WHERE score <
            (SELECT score FROM player_rank WHERE _player_id = :char_id2) AND active = 1 AND health > 0 ORDER BY score DESC LIMIT 5)";

        $peers = query_array(
            $sel,
            [
                ':char_id'  => [$p_playerId, PDO::PARAM_INT],
                ':char_id2' => [$p_playerId, PDO::PARAM_INT],
            ]
        );

        if (!count($peers)) {
            // Get bottom 10 players if not yet ranked.
            $peers = query_array('SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id
                where active = 1 and health > 0
                order by rank_id desc limit 10');
        }

        return $peers;
    }

    /**
     * Pull the recent attackers from the event table.
     *
     * @param Player $p_player
     * @return PDOStatement
     */
    private function getRecentAttackers(Player $p_player) {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare(
            'SELECT DISTINCT player_id, send_from, uname, level, health FROM events JOIN players ON send_from = player_id WHERE send_to = :user AND active = 1 AND player_id != :id LIMIT 20');
        $statement->bindValue(':user', $p_player->id(), PDO::PARAM_INT);
        $statement->bindValue(':id', $p_player->id(), PDO::PARAM_INT);

        $statement->execute();

        return $statement;
    }

    /**
     * Drop a certain enemy from the list.
     *
     * @param Player $p_player
     * @param Player $p_enemy
     * @return void
     */
    private function removeEnemyFromPlayer(Player $p_player, Player $p_enemy) {
        DatabaseConnection::getInstance();
        $query = 'DELETE FROM enemies WHERE _player_id = :pid AND _enemy_id = :eid';
        $statement = DatabaseConnection::$pdo->prepare($query);
        $statement->bindValue(':pid', $p_player->id());
        $statement->bindValue(':eid', $p_enemy->id());
        $statement->execute();
    }
}
