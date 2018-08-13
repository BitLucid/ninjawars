<?php
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\CloneKill;

class CloneKillTest extends NWTest {
    /**
     *
     */
    function setUp() {
    }

    /**
     * Delete test user.
     */
    function tearDown() {
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    private function inactivate($char_id) {
        query('update players set active = 0 where player_id = :char_id', [':char_id'=>$char_id]);
    }

    private function syncIps($ip, $char_id, $char_id_2) {
        query('update accounts set last_ip = :ip '.
            'where account_id in (select account_id from accounts b '.
            'left join account_players on b.account_id = _account_id '.
            'where _player_id is not null and '.
            '(_player_id = :pid or _player_id = :pid2))',
            [
                ':pid'  => $char_id,
                ':pid2' => $char_id_2,
                ':ip'   => $ip,
            ]
        );
    }

    public function testYouCantCloneKillXAndX() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $this->assertFalse(CloneKill::canKill($char_id, $char_id));
    }

    public function testYouCantCloneKillEmpties() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $this->assertFalse(CloneKill::canKill(34534534, 234234235));
        $this->assertFalse(CloneKill::canKill(34534534, $char_id));
    }

    public function testSyncIpWorks() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $this->syncIps('222.222.222.250', $char_id, $char_id_2);
        $p1 = Player::find($char_id);
        $p2 = Player::find($char_id_2);
        $account1 = Account::findByChar($p1);
        $account2 = Account::findByChar($p2);
        $this->assertNotEmpty($account1->getLastIp());
        $this->assertEquals($account1->getLastIp(), $account2->getLastIp());
    }

    public function testCanSyncIpOfCharactersToBlanks() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $this->syncIps('', $char_id, $char_id_2);
        $p1 = Player::find($char_id);
        $p2 = Player::find($char_id_2);
        $account1 = Account::findByChar($p1);
        $account2 = Account::findByChar($p2);
        $this->assertEquals('', $account1->getLastIp());
        $this->assertEquals('', $account2->getLastIp());
    }

    public function testCantCloneKillSimilarCharactersEvenIfBothHaveIpOf127001() {
        $previous = $_SERVER['REMOTE_ADDR'];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.11';
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $_SERVER['REMOTE_ADDR'] = $previous;
        $this->syncIps('127.0.0.1', $char_id, $char_id_2); // Must be 127.0.0.1 for this test.
        $p1 = Player::find($char_id);
        $p2 = Player::find($char_id_2);
        $account1 = Account::findByChar($p1);
        $account2 = Account::findByChar($p2);
        $this->assertEquals('127.0.0.1', $account1->getLastIp());
        $this->assertEquals('127.0.0.1', $account2->getLastIp());
        $this->assertFalse(CloneKill::canKill($char_id, $char_id_2));
    }

    public function testCantCloneKillTwoSimilarCharactersBecauseOfTheirBlankIps() {
        $previous = $_SERVER['REMOTE_ADDR'];
        $_SERVER['REMOTE_ADDR'] = '';
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $this->syncIps('', $char_id, $char_id_2);
        $_SERVER['REMOTE_ADDR'] = $previous;
        $this->assertFalse(CloneKill::canKill($char_id, $char_id_2));
    }

    public function testYouCantCloneKillWithAnyNonActiveChar() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $this->syncIps('999.888.777.666', $char_id, $char_id_2);
        $this->inactivate($char_id);
        $this->assertFalse(CloneKill::canKill($char_id_2, $char_id));
    }

    public function testCloneKillOnCharsWithSameIp() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        // Will create characters with 127.0.0.1 ip, but that shouldn't be clone kill able.
        $this->assertFalse(CloneKill::canKill($char_id, $char_id_2));
        $this->syncIps('888.777.666.555', $char_id, $char_id_2);
        $this->assertTrue(CloneKill::canKill($char_id, $char_id_2), 'Should be able to clone kill similar and same ip characters!');
    }

    public function testCloneKillDoesNotAllowYouToCloneKillYourself() {
        $this->markTestIncomplete('Rejection of Clone killing self is implemented at the skills controller level currently.');
    }

    public function testYouCantCloneKillWithAnyNonConfirmedAccounts() {
        $this->markTestIncomplete();
    }

    public function testYouCantCloneKillWithAnyNonOperationalAccounts() {
        $this->markTestIncomplete();
    }

    public function testCloneKillOnCharsOfSameAccountSameIpWorks() {
        $this->markTestIncomplete();
    }

    public function testCloneKillOnActiveCharsOfSameAccountDifferentIpWorks() {
        $this->markTestIncomplete();
    }

    public function testCloneKillKillingWipesHealthAndTurns() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        $charObj = Player::find($char_id);
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $charObj_2 = Player::find($char_id_2);
        // Will create characters with 127.0.0.1 ip, but that shouldn't be clone kill able.
        $this->assertFalse(CloneKill::canKill($char_id, $char_id_2));
        $this->syncIps('555.66.77.88', $char_id, $char_id_2);
        $this->assertTrue(CloneKill::canKill($char_id, $char_id_2), 'Should be able to clone kill similar and same ip characters!');
        CloneKill::kill($charObj, $charObj, $charObj_2); // Obliterate them.
        $pc1 = Player::find($char_id);
        $pc2 = Player::find($char_id_2);
        $this->assertEquals(0, $pc1->health);
        $this->assertEquals(0, $pc2->health);
        $this->assertEquals(0, $pc1->turns);
        $this->assertEquals(0, $pc2->turns);
    }
}
