<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\StreamedViewResponse;

class MapController extends AbstractController {
    const PRIV  = false;
    const ALIVE = false;
    public $nodes = [];

    /**
     * Get the nodes and assign them to current.
     * This will just be a standin for the database for now
     */
    public function __construct(){
        $this->setNodes();
    }

    private function setNodes($nodes=null){
        // Here is where the node locations are defined, and their order is allocated.
        $static_nodes = include(ROOT.'lib/data/raw/nodes.php'); // Mocking database data for later
        $this->nodes = $nodes !== null? $nodes : $static_nodes;
    }

    /**
     * Get the various nodes of the map and pass them to the template
     *
     * @return StreamedViewResponse
     */
    public function index(Container $p_dependencies) {
        $parts = [
            'nodes'   => $this->nodes,
            'show_ad' => rand(1, 20) // show the ad in the village 10% of the time
        ];

        $options = ['quickstat'=>'player'];

        return new StreamedViewResponse('Map', 'map.tpl', $parts, $options);
    }
}
