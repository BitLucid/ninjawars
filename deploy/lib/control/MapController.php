<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\StreamedViewResponse;

class MapController extends AbstractController {
    const PRIV  = false;
    const ALIVE = false;

    /**
     * Get the nodes and assign them to current.
     */
    public function __construct(){
        $this->setNodes();
    }

    private function setNodes($nodes=null){
        // Here is where the node locations are defined, and their order is allocated.
        $this->nodes = $nodes !== null? $nodes : array(
            array( // Row
                array('name'=>'Shrine', 'type'=>'shrine building', 'url'=>'shrine', 'image'=>'shrine.png', 'tile_image'=>'concentric_shrine.png', 'xcoord'=>0, 'ycoord'=>0, 'id'=>1)
                , array('name'=>'', 'type'=>'rice-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>12)
                , array('name'=>'Road', 'type'=>'north-south-road', 'url'=>'', 'tile_image'=>'north-south-road.png', 'xcoord'=>2, 'ycoord'=>0, 'id'=>3)
                , array('name'=>'', 'type'=>'rice-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>12)
                , array('name'=>'Doshin', 'type'=>'doshin building', 'url'=>'doshin', 'image'=>'doshin.png', 'tile_image'=>'doshin_building.png', 'xcoord'=>1, 'ycoord'=>0, 'id'=>2)

            ),

            array( // Row
                array('name'=>'', 'type'=>'wheat-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>15)
                , array('name'=>'Dojo', 'type'=>'dojo building',  'url'=>'dojo', 'tile_image'=>'concentric_leaf.png', 'xcoord'=>1, 'ycoord'=>1, 'id'=>7)
                , array('name'=>'Road', 'type'=>'north-south-road', 'url'=>'', 'tile_image'=>'north-south-road.png', 'xcoord'=>2, 'ycoord'=>0, 'id'=>3)
                , array('name'=>'Shop', 'type'=>'weapons-shop building',  'url'=>'shop', 'tile_image'=>'concentric_star.png', 'xcoord'=>0, 'ycoord'=>1, 'id'=>6)
                , array('name'=>'', 'type'=>'rice-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>12)

            ),

            array(// Row
                array('name'=>'Rice Paddy', 'type'=>'wheat-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>10)
                , array('name'=>'Road', 'type'=>'east-west-road', 'url'=>'', 'xcoord'=>2, 'ycoord'=>1, 'id'=>8)
                , array('name'=>'Village Square', 'type'=>'village-square', 'url'=>'village', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>12)
                , array('name'=>'Road', 'type'=>'east-west-road', 'url'=>'', 'xcoord'=>2, 'ycoord'=>1, 'id'=>8)
                , array('name'=>'Fields', 'type'=>'rice-field', 'url'=>'work', 'tile_image'=>'concentric_field.png', 'xcoord'=>2, 'ycoord'=>14, 'id'=>14)
                // Unnamed node.
            ),

            array(// Row
                array('name'=>'', 'type'=>'wheat-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>15)
                , array('name'=>'Casino', 'type'=>'casino building', 'url'=>'casino', 'tile_image'=>'elemental_coin.png', 'xcoord'=>0, 'ycoord'=>2, 'id'=>11)
                , array('name'=>'Road', 'type'=>'north-south-road', 'url'=>'', 'tile_image'=>'north-south-road.png', 'xcoord'=>2, 'ycoord'=>1, 'id'=>17)
                , array('name'=>'Bath House', 'type'=>'bath-house building', 'url'=>'duel', 'tile_image'=>'concentric_star.png', 'xcoord'=>2, 'ycoord'=>13, 'id'=>19)
                , array('name'=>'Fields', 'type'=>'rice-field', 'url'=>'work', 'tile_image'=>'concentric_field.png', 'xcoord'=>2, 'ycoord'=>14, 'id'=>14)
            ),

            array(// Row
                array('name'=>'Rice Paddy', 'type'=>'wheat-field', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>10)
                , array('name'=>'Grassy Knoll', 'type'=>'grass', 'url'=>'', 'tile_image'=>null, 'xcoord'=>0, 'ycoord'=>2, 'id'=>11)
                , array('name'=>'Road', 'type'=>'north-south-road', 'url'=>'', 'tile_image'=>'north-south-road.png', 'xcoord'=>2, 'ycoord'=>1, 'id'=>17)
                , array('name'=>'Fields', 'type'=>'rice-field', 'url'=>'work', 'tile_image'=>'concentric_field.png', 'xcoord'=>2, 'ycoord'=>13, 'id'=>13)
                , array('name'=>'Fields', 'type'=>'rice-field', 'url'=>'work', 'tile_image'=>'concentric_field.png', 'xcoord'=>2, 'ycoord'=>14, 'id'=>14)
            )
        );
    }

    /**
     * Get the various nodes of the map and pass them to the template
     *
     * @return Response
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
