<?php

use NinjaWars\core\data\NinjaMeta;
use NinjaWars\core\data\Player;

class NinjaMetaTest extends NWTest {
    public function setUp(): void {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();
    }

    public function tearDown(): void {
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::tearDown();
    }

    public function testCanDeactivateChar() {
        $ninja_meta = new NinjaMeta($this->char);
        $ninja_meta->deactivate();
        $final_char = Player::find($this->char->id());
        $this->assertFalse($final_char->isActive(), 'NinjaMeta::deactivate() failed to change operational status');
    }

    public function testCanReactivateChar() {
        $ninja_meta = new NinjaMeta($this->char);
        $ninja_meta->deactivate();
        $p_char = Player::find($this->char->id());
        $this->assertFalse($p_char->isActive(), 'NinjaMeta::deactivate() failed to change operational status');
        $ninja_meta->reactivate();
        $final_char = Player::find($this->char->id());
        $this->assertTrue($final_char->isActive(), 'NinjaMeta::reactivate() failed to change operational status');
    }
}
