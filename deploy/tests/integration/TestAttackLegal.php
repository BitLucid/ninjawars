<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use NinjaWars\core\control\AttackLegal;
use NinjaWars\core\data\Player;

/**
 * @TODO: Need to be able to mock ips and ensure ability to attack even when both players have the server ip, somehow.
 */
class TestAttackLegal extends NWTest {
    /**
     * group char
     */
    function setUp() {
    }

    /**
     * group char
     */
    function tearDown() {
        // Delete test user.
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    private function oldify_character_last_attack($char_id) {
        query("update players set last_started_attack = (now() - INTERVAL '20 days') where player_id = :char_id", [':char_id'=>$char_id]);
    }

    /**
     * Test that you can't self-attack.
     */
    public function testAttackLegalCantAttackSelf() {
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
        $this->oldify_character_last_attack($char_id);
        $legal = new AttackLegal(Player::find($char_id), Player::find($char_id), ['required_turns'=>1, 'ignores_stealth'=>true]);
        $this->assertFalse($legal->check(false));
    }

    public function testAttackLegalCantAttackSelfEvenIfUsingSelfIdVsSelfUsername() {
        //$this->setExpectedException('InvalidArgumentException');
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
        $this->oldify_character_last_attack($char_id);
        $player = Player::find($char_id);
        $info = $player->data();
        $this->assertTrue((bool)$info['uname'], 'Character uname not found to check attacklegal with');
        $legal = new AttackLegal($player, Player::findByName($info['uname']), ['required_turns'=>1, 'ignores_stealth'=>true]);
        $this->assertFalse($legal->check(false));
    }

    /**
     * Test that you can attack as two separate characters.
     */
    public function testCanAttackAsTwoSeparateCharacters() {
        $confirm = true;
        $char_id = TestAccountCreateAndDestroy::create_testing_account($confirm);
        $this->oldify_character_last_attack($char_id);
        $char_2_id = TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
        $this->oldify_character_last_attack($char_2_id);
        $legal = new AttackLegal(Player::find($char_id), Player::find($char_2_id), ['required_turns'=>1, 'ignores_stealth'=>true]);
        $checked = $legal->check(false);
        $this->assertEquals(null, $legal->getError(), 'There was an attack error message when there shouldn\'t be one.');
        $this->assertTrue($checked);
    }

    /**
     * Test that you can't attack if an excessive amount of turns is required
     */
    public function testCantAttackIfExcessiveAmountOfTurnsIsRequired() {
        $confirm = true;
        $char_id = TestAccountCreateAndDestroy::create_testing_account($confirm);
        $this->oldify_character_last_attack($char_id);
        $char_2_id = TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
        $this->oldify_character_last_attack($char_2_id);
        $char = Player::find($char_2_id);
        $legal = new AttackLegal(Player::find($char_id), $char, ['required_turns'=>4000000000, 'ignores_stealth'=>true]);
        $this->assertFalse($legal->check(false));
    }
}
