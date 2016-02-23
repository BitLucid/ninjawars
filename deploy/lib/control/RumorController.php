<?php
namespace NinjaWars\core\control;

/**
 * Handles the rumors and info displayed by the bathhouse.
 */
class RumorController {
    const ALIVE          = false;
    const PRIV           = false;

    /**
     * 
     */
    public function __construct() {
    }

    private function duels(){
        return query_array("SELECT dueling_log.*, attackers.player_id AS attacker_id, defenders.player_id AS defender_id FROM dueling_log JOIN players AS attackers ON attackers.uname = attacker JOIN players AS defenders ON defender = defenders.uname ORDER BY id DESC LIMIT 500");
    }

    private function stats(){
        return membership_and_combat_stats();
    }

    public function index(){
        $stats          = $this->stats();
        $vicious_killer = $stats['vicious_killer'];
        $duels = $this->duels();




        return [
            'template'=>'duel.tpl',
            'title'   =>'Bath House',
            'parts'   =>[
                    'stats'=>$stats,
                    'vicious_killer'=>$vicious_killer,
                    'duels'=>$duels,
                ],
            'options' =>['quickstat'=>false]
        ];
    }


}
