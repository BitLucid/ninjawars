<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_grouping.php");
require_once(LIB_ROOT."data/lib_npc.php");

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use \NpcFactory;

/**
 * Display ninja & monsters to potentially pick fights with
 */
class ConsiderController {
    const ALIVE       = false;
    const PRIV        = false;
    const ENEMY_LIMIT = 20;

    public function index() {
        return $this->render($this->configure());
    }

    private function configure() {
        $char = new Player(self_char_id());
        // Array that simulates database display information for switching out for an npc database solution.
        $npcs = [
            ['name'=>'Peasant',  'identity'=>'peasant',  'image'=>'fighter.png'],
            ['name'=>'Thief',    'identity'=>'thief',    'image'=>'thief.png'],
            ['name'=>'Merchant', 'identity'=>'merchant', 'image'=>'merchant.png'],
            ['name'=>'Guard',    'identity'=>'guard',    'image'=>'guard.png'],
            ['name'=>'Samurai',  'identity'=>'samurai',  'image'=>'samurai.png'],
        ];

        $peers = nearby_peers($char->id());

        $active_ninjas = get_active_players(5, true); // Get the currently active ninjas

        $char_info = self_info();

        // Generic/abstracted npcs
        $other_npcs = NpcFactory::npcsData();

        $enemy_list = get_current_enemies();
        $enemy_count = rco($enemy_list);
        $recent_attackers = get_recent_attackers($char);

        return [
            'logged_in'        => (bool)$char->id(),
            'enemy_list'       => $enemy_list,
            'enemy_count'      => $enemy_count,
            'char_name'        => $char->name(),
            'npcs'             => $npcs,
            'other_npcs'       => $other_npcs,
            'char_info'        => $char_info,
            'active_ninjas'    => $active_ninjas,
            'recent_attackers' => $recent_attackers,
            'enemy_list'       => $enemy_list,
            'peers'            => $peers,
            'max_enemies'      => (self::ENEMY_LIMIT <= $enemy_count),
        ];
    }

    /**
     * Search for enemies to remember.
     */
    public function search() {
        $enemy_match = in('enemy_match');
        $found_enemies = ($enemy_match ? get_enemy_matches($enemy_match) : null);
        $parts = $this->configure();

        // Add some additional parts
        $parts = array_merge($parts, [
            'found_enemies' => $found_enemies,
            'enemy_match'   => $enemy_match,
        ]);

        return $this->render($parts);
    }

    /**
     * Render the parts, since the template is always currently the same.
     */
    private function render($parts) {
        return [
            'template' => 'enemies.tpl',
            'title'    => 'Fight',
            'parts'    => $parts,
            'options'  => ['quickstat'=>false],
        ];
    }

    /**
     * Add an enemy to pc's list if valid.
     */
    public function addEnemy() {
        $add_enemy = in('add_enemy', null, 'toInt');
        if (is_numeric($add_enemy) && $add_enemy != 0) {
            add_enemy($add_enemy);
        }

        return new RedirectResponse('enemies.php');
    }

    /**
     * Take an enemy off a pc's list.
     */
    public function deleteEnemy() {
        $remove_enemy = in('remove_enemy', null, 'toInt');

        if (is_numeric($remove_enemy) && $remove_enemy != 0) {
            remove_enemy($remove_enemy);
        }

        return new RedirectResponse('enemies.php');
    }
}
