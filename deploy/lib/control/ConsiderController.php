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
        $found_enemies = ($enemy_match && $current_player ? Enemies::search($current_player, $enemy_match) : []);
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
        $next_enemy = $char ? Enemies::nextTarget($char, $shift) : null;

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
        $peers            = ($char ? Enemies::getNearbyPeers($char->id()) : []);
        $active_ninjas    = Player::findActive(5, true);
        $char_info        = ($char ? $char->data() : []);
        $other_npcs       = NpcFactory::npcsData();
        $npcs             = NpcFactory::customNpcs();
        $enemy_list       = ($char ? Enemies::getCurrent($char) : []);
        $next_enemy = $char ? Enemies::nextTarget($char, $shift) : null;

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
        return new StreamedViewResponse('Fight', 'fight.tpl', $parts, ['quickstat' => false]);
    }

}
