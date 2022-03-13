<?php

use NinjaWars\core\data\Map;
use NinjaWars\core\data\Player;

class MapUnitTest extends NWTest
{
    public function testNearbyNpcs()
    {
        $npcs = Map::nearbyNpcs(1, 1);
        $this->assertEquals(1, count($npcs));
        $this->assertEquals(1, $npcs[0]->difficulty());
    }

    public function testNearbyNinja()
    {
        $char = Player::find(SessionFactory::getSession()->get('player_id'));
        $ninjas = Map::nearbyNinja($char, 1);
        $this->assertEquals(1, count($ninjas));
        $this->assertEquals($char->id(), $ninjas[0]->id());
    }
}
