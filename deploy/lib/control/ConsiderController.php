<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Enemies;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\NWLogger;
use NWError;
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
    public function index(Container $p_dependencies): StreamedViewResponse {
        return $this->render($this->configure());
    }

    /**
     * Search for enemies to remember.
     */
    public function search(Container $p_dependencies): StreamedViewResponse {
        $enemy_match = RequestWrapper::getPostOrGet('enemy_match');
        $current_player = $p_dependencies['current_player'];
        $found_enemies = ($enemy_match ? Enemies::search($current_player, $enemy_match) : []);
        $parts = $this->configure();

        // Add some additional parts
        $parts = array_merge($parts, [
            'found_enemies' => $found_enemies,
            'enemy_match'   => $enemy_match,
        ]);

        return $this->render($parts);
    }

    /**
     * Display just the next ninja to attack
     */
    public function nextEnemy(Container $p_dependencies): StreamedViewResponse {
        $char             = Player::find(SessionFactory::getSession()->get('player_id'));
        $shift = max(0, min(300, (int) RequestWrapper::get('shift')));
        $char_info        = ($char ? $char->data() : []);
        $next_enemy = $char ? $this->getNextEnemy($char, $shift) : null;

        $inventory = $char ? new Inventory($char) : null;
        $items     = $inventory ? $inventory->counts() : null;

        $skillDAO = new SkillDAO();

        // Set up combat and single-use skills
        if(!$char){
            $combat_skills = null;
            $targeted_skills = null;
        }elseif (!$char->isAdmin()) {
            // PCs get what is appropriate for their class
            $combat_skills   = $skillDAO->getSkillsByTypeAndClass($char->_class_id, 'combat', $char->level);
            $targeted_skills = $skillDAO->getSkillsByTypeAndClass($char->_class_id, 'targeted', $char->level);
        } else {
            // Admins get all skills
            $combat_skills   = $skillDAO->all('combat');
            $targeted_skills = $skillDAO->all('targeted');
        }
        if($combat_skills instanceof \PDOStatement){
            // Unwrap combat skills
            $combat_skills = $combat_skills->fetchAll(\PDO::FETCH_ASSOC);
        }

        $parts = [
            'logged_in'        => (bool)$char && $char->id(),
            'char'             => $char,
            'char_name'        => ($char ? $char->name() : ''),
            'char_info'        => $char_info,
            'enemy'            => $next_enemy,
            'shift'           => $shift,
            'inventory'        => $inventory,
            'items'            => $items,
            'combat_skills'    => $combat_skills,
            'targeted_skills'  => $targeted_skills,
        ];
        return new StreamedViewResponse('Fight Next Enemy', 'enemies.attack-next.tpl', $parts, ['quickstat'=>false]);
    }

    /**
     * Add an enemy to pc's list if valid.
     */
    public function addEnemy(Container $p_dependencies): RedirectResponse {
        $enemy_id = (int) RequestWrapper::getPostOrGet('add_enemy');
        if ($enemy_id) {
            Enemies::add($p_dependencies['current_player'], $enemy_id);
        } else {
            NWLogger::log('Request 3n3m4 for invalid enemy id: ' . $enemy_id);
        }
        return new RedirectResponse('/enemies');
    }

    /**
     * Take an enemy off a pc's list.
     */
    public function deleteEnemy(Container $p_dependencies): RedirectResponse {
        Enemies::remove($p_dependencies['current_player'], RequestWrapper::getPostOrGet('remove_enemy'));
        return new RedirectResponse('/enemies');
    }

    /**
     * Bring together all the parts for the main display
     */
    private function configure(): array {
        $shift = max(0, min(300, (int) RequestWrapper::get('shift')));
        $char             = Player::find(SessionFactory::getSession()->get('player_id'));
        $peers            = ($char ? $this->getNearbyPeers($char->id()) : []);
        $active_ninjas    = Player::findActive(5, true);
        $char_info        = ($char ? $char->data() : []);
        $other_npcs       = NpcFactory::npcsData();
        $npcs             = NpcFactory::customNpcs();
        $enemy_list       = ($char ? Enemies::getCurrent($char) : []);
        $recent_attackers = ($char ? $this->getRecentAttackers($char) : []);
        $next_enemy = $char ? $this->getNextEnemy($char, $shift) : null;

        $inventory = $char ? new Inventory($char) : null;
        $items     = $inventory ? $inventory->counts() : null;

        $skillDAO = new SkillDAO();

        // Set up combat and single-use skills
        if ($char && !$char->isAdmin()) {
            // PCs get what is appropriate for their class
            $combat_skills   = $skillDAO->getSkillsByTypeAndClass($char->_class_id, 'combat', $char->level);
            $targeted_skills = $skillDAO->getSkillsByTypeAndClass($char->_class_id, 'targeted', $char->level);
        } else {
            // Admins get all skills
            $combat_skills   = $skillDAO->all('combat');
            $targeted_skills = $skillDAO->all('targeted');
        }
        if($combat_skills instanceof \PDOStatement){
            // Unwrap combat skills
            $combat_skills = $combat_skills->fetchAll(\PDO::FETCH_ASSOC);
        }

        return [
            'logged_in'        => (bool)$char && $char->id(),
            'enemy_list'       => $enemy_list,
            'char'             => $char,
            'char_name'        => ($char ? $char->name() : ''),
            'npcs'             => $npcs,
            'other_npcs'       => $other_npcs,
            'char_info'        => $char_info,
            'active_ninjas'    => $active_ninjas,
            'recent_attackers' => $recent_attackers,
            'enemy_list'       => $enemy_list,
            'next_enemy'       => $next_enemy,
            'shift'           => $shift,
            'inventory'        => $inventory,
            'items'            => $items,
            'combat_skills' => $combat_skills,
            'targeted_skills' => $targeted_skills,
            'peers'            => $peers,
        ];
    }

    /**
     * Render the parts, since the template is always currently the same.
     */
    private function render(array $parts): StreamedViewResponse {
        return new StreamedViewResponse('Fight', 'enemies.tpl', $parts, ['quickstat'=>false]);
    }

    /**
     * Search for enemies to add.
     *
     * @param int    $p_playerId
     * @param string $p_pattern
     * @return array
     */
    private function getEnemyMatches(int $p_playerId, string $p_pattern): array | bool
    {
        $char = Player::find($p_playerId);
        return $char && Enemies::search($char, $p_pattern) || [];
    }

    /**
     * Select characters right nearby in ranking score, up and down.
     *
     * @param int $p_playerId
     * @return array
     */
    private function getNearbyPeers(int $p_playerId): array {
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
     * Select nearest character down in rank
     *
     * @param Player $char
     * @return Player
     */
    private function getNextEnemy(Player $char, int $shift=0): ?Player {
        $sel = '
            SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id 
            WHERE score < (SELECT score FROM player_rank WHERE _player_id = :char_id) 
                AND active = 1 AND level <= (5 + :char_level) AND health > 0 
                AND player_id != :char_id2
            ORDER BY score DESC LIMIT 1 OFFSET greatest(0, :off)';
        $enemies = query_array(
            $sel,
            [
                ':char_id'  => [$char->id(), PDO::PARAM_INT],
                ':char_id2'  => [$char->id(), PDO::PARAM_INT],
                ':char_level'=> [$char->level, PDO::PARAM_INT],
                ':off'      => [$shift, PDO::PARAM_INT],
            ]
        );
        if (!count($enemies)) {
            // Get bottom 10 players if not yet ranked.
            $enemies = query_array(
                '
                SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id
            WHERE 
                active = 1 AND health > 0 AND level <= (5 + :char_level)
                AND player_id != :char_id2
            ORDER BY rank_id ASC limit 10 OFFSET greatest(0, :off)',
                [
                ':char_id2'  => [$char->id(), PDO::PARAM_INT],
                ':char_level' => [$char->level, PDO::PARAM_INT],
                ':off'      => [$shift, PDO::PARAM_INT],
                ]
            );
        }
        return !empty($enemies) ? Player::find(reset($enemies)['player_id']) : null;
    }

    /**
     * Pull the recent attackers from the event table.
     *
     * @param Player $p_player
     * @return \PDOStatement
     */
    private function getRecentAttackers(Player $p_player, int $limit = 7): \PDOStatement
    {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare(
            'SELECT DISTINCT player_id, send_from, uname, level, health FROM events JOIN players ON send_from = player_id WHERE send_to = :user AND active = 1 AND player_id != :id LIMIT :limit'
        );
        $statement->bindValue(':user', $p_player->id(), PDO::PARAM_INT);
        $statement->bindValue(':id', $p_player->id(), PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);

        $statement->execute();

        return $statement;
    }
}
