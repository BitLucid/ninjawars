<?php
use NinjaWars\core\control\Combat;
use NinjaWars\core\data\Player;

class CombatTest extends PHPUnit_Framework_TestCase {
    public function testKillPointCalculation() {
        $char_id = TestAccountCreateAndDestroy::create_testing_account();

        $this->assertInternalType(
            'int',
            Combat::killPointsFromDueling(
                Player::find($char_id),
                Player::find($char_id)
            )
        );
    }
}
