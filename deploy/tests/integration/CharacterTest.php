<?php
use NinjaWars\core\data\Player;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Account;

class CharacterTest extends NWTest {
    private $previous_server_ip = '';
    private $char_id;

    public function setUp() {
        $this->previous_server_ip = @$_SERVER['REMOTE_ADDR'];
        $this->test_email = TestAccountCreateAndDestroy::$test_email; // Something@example.com
        $this->test_password = TestAccountCreateAndDestroy::$test_password;
        $this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
        $this->char_id = $char_id;
    }

    public function tearDown() {
        // Delete test user.
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
        $_SERVER['REMOTE_ADDR']=$this->previous_server_ip; // Reset remote addr to whatever it was before, just in case.
    }

    public function testCreatePlayerObject() {
        $char = Player::find($this->char_id);
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->id()));
    }

    public function testPlayerCanBeFoundStatically() {
        $char = Player::find($this->char_id);
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->id()));
        $this->assertTrue((bool)$char->name());
    }

    public function testFindPlayable(){
        $pcs_data = Player::findActive(1, false);
        $pc_data = reset($pcs_data);
        $pc = Player::find($pc_data['player_id']);
        $acc = Account::findByChar($pc);
        $pc2 = Player::findPlayable($acc->id());
        $this->assertEquals($pc->id(), $pc2->id());
    }

    public function testFindPlayableFromInitialChar(){
        $pc = Player::find($this->char_id);
        $acc = Account::findByChar($pc);
        $pc2 = Player::findPlayable($acc->id());
        $this->assertEquals($pc->id(), $pc2->id());
    }

    public function testFindByNamePositive() {
        $char = Player::findByName($this->test_ninja_name);
        $this->assertNotNull($char);
    }

    public function testFindByNameNegative() {
        $char = Player::findByName('BANANA_IS_FAKE$$$NOTREAL=;m"');
        $this->assertNull($char);
    }

    public function testNonexistentPlayerReturnsNullViaStaticFind() {
        $id = query_item('select max(player_id) from players');
        $bad_id = $id + 100;
        $char = Player::find($bad_id);
        $this->assertEquals(null, $char);
    }

    public function testCreatePlayerObjectHasUsefulInfo() {
        $char = Player::find($this->char_id);
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->health));
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->getSpeed()));
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->getStamina()));
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->getStrength()));
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->level));
        $this->assertNotEmpty($char->name());
        $this->assertTrue((bool)Filter::toNonNegativeInt($char->damage()));
    }

    public function testPCHasVariousAttributesAndCanSetSome() {
        $char = Player::find($this->char_id);
        $this->assertTrue(is_int($char->gold));
        $this->assertTrue(is_int($char->turns));
        $this->assertTrue(is_int($char->setGold(45)));
        $this->assertTrue(is_int($char->setTurns(32)));
        $this->assertEquals(444, $char->setBounty(444));
        $char->save();
        $char_dup = Player::find($this->char_id);
        $this->assertEquals(444, $char_dup->bounty);
        $this->assertEquals(32, $char_dup->turns);
        $this->assertEquals(45, $char_dup->gold);
    }

    public function testCharacterHasADifficultyRating() {
        $char = Player::find($this->char_id);
        $this->assertGreaterThan(0, $char->difficulty());
    }

    public function testCharacterHasAVerificationNumber() {
        $char = Player::find($this->char_id);
        $this->assertGreaterThan(0, $char->getVerificationNumber());
    }

    public function testPlayerStatusesChangeStatCalcs() {
        $char = Player::find($this->char_id);
        $str = $char->getStrength();
        $speed = $char->getSpeed();
        $stamina = $char->getStamina();
        $char->addStatus(SLOW);
        $this->assertNotEquals($char->getSpeed(), $speed, 'Speed should be different due to slow status.');
        $this->assertTrue($char->getSpeed() < $speed, 'Speed should be less due to slow status, but isn\'t.');
        $char->addStatus(POISON);
        $this->assertTrue($char->getStamina() < $stamina);
        $char->addStatus(WEAKENED);
        $this->assertTrue($char->getStrength() < $str);
        $char->resetStatus();
        $this->assertEquals($char->getStrength(), $str);
        $char->addStatus(STR_UP1);
        $this->assertTrue($char->getStrength() > $str);
        $char->addStatus(STR_UP2);
        $this->assertTrue($char->getStrength() > $str);
    }

    public function testRemoveStatus() {
        $char = Player::find($this->char_id);
        $char->resetStatus();
        $this->assertFalse($char->hasStatus(SLOW));
        $char->addStatus(SLOW);
        $this->assertTrue($char->hasStatus(SLOW));
        $char->subtractStatus(SLOW);
        $this->assertFalse($char->hasStatus(SLOW));
    }

    public function testPlayerObjectCanSaveDetails() {
        $bel = 'Believes in the mirror goddess.';
        $traits = 'Weird,Blue';
        $desc = 'Some description for testing';
        $goals = 'Test: to rule the world';
        $instincts = 'Kill Samurai';
        $ooc = 'I like cheese';
        $char = Player::find($this->char_id);
        $char->traits      = $traits;
        $char->beliefs     = $bel;
        $char->description = $desc;
        $char->goals       = $goals;
        $char->instincts   = $instincts;
        $char->messages    = $ooc;
        $char->save();
        $char = Player::find($this->char_id); // Create a new player copy.
        $this->assertEquals($desc, $char->description);
        $this->assertEquals($traits, $char->traits);
        $this->assertEquals($bel, $char->beliefs);
        $this->assertEquals($goals, $char->goals);
        $this->assertEquals($instincts, $char->instincts);
        $this->assertEquals($ooc, $char->messages);
    }

    public function testNegativeKiRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setKi(-643);
    }

    public function testNegativeTurnsRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setTurns(-345);
    }

    public function testNegativeStrengthRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setStrength(-6);
    }

    public function testNegativeSpeedRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setSpeed(-556);
    }

    public function testNegativeStaminaRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setStamina(-34);
    }

    public function testNegativeHealthRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setHealth(-6);
    }

    public function testFractionalHealthRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setHealth(6.45);
    }

    public function testNegativeGoldRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setGold(-45);
    }

    public function testFractionalGoldRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setGold(45.23);
    }

    public function testNegativeBountyRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setBounty(-45);
    }

    public function testFractionalBountyRejected() {
        $this->expectException('InvalidArgumentException');
        $char = Player::find($this->char_id);
        $char->setBounty(45.43);
    }

    public function testPlayerHealChangesHealth() {
        $char = Player::find($this->char_id);
        $half_health = $char->setHealth(floor($char->health/2));
        $char->save();

        $char = Player::find($this->char_id);
        $this->assertEquals($half_health, $char->health);
        $this->assertLessThan($char->getMaxHealth(), $char->health);

        $char->heal($char->getMaxHealth()); // Heal by max_health, so up to
        $char->save();

        $this->assertEquals($char->health, $char->getMaxHealth());
        $this->assertEquals($char->health, $char->getMaxHealth());
    }

    public function testPCCanObtainAGravatarUrl() {
        $char = Player::find($this->char_id);
        $this->assertNotEmpty($char->avatarUrl());
        $this->assertTrue(strpos($char->avatarUrl(), 'avatar') !== false);
    }

    public function testGravatarURLWithoutAvatarType() {
        $char = Player::find($this->char_id);
        $char->avatar_type = null;
        $this->assertEquals('', $char->avatarUrl());
    }

    public function testCreatePlayerObjectCanSaveChanges() {
        $char = Player::find($this->char_id);
        $ki = $char->ki;
        $char->setKi($ki+55);
        $char->setGold(343);
        $char->save();
        $char_copy = Player::find($this->char_id);
        $this->assertEquals($char_copy->ki, $ki+55);
        $this->assertEquals($char_copy->gold, 343);
    }

    public function testPlayerMaxHealthShouldMatchInitialHealthForTestPlayer(){
        $char = Player::find($this->char_id);
        $this->assertEquals($char->health, $char->getMaxHealth(), 'Test character created with an invalid max health!');
    }

    public function testPlayerObjectReportDamageCorrectly() {
        $char = Player::find($this->char_id);
        $damage = floor($char->health/2);
        $char->setHealth($char->health - $damage);
        $char->save();
        $char = Player::find($this->char_id);
        $this->assertEquals($damage, $char->is_hurt_by());
    }

    public function testPlayerObjectHarmWorksCorrectly() {
        $char = Player::find($this->char_id);
        $damage = floor($char->health/2);
        $char->harm($damage);
        $char->save();
        $char = Player::find($this->char_id);
        $this->assertEquals($damage, $char->is_hurt_by());
    }

    public function testKillCharByHarmingWithTheirFullHealth() {
        $char = Player::find($this->char_id);
        $char->harm($char->health);
        $this->assertEquals(0, $char->health);
    }

    public function testCauseDeath() {
        $char = Player::find($this->char_id);
        $char->death();
        $this->assertEquals(0, $char->health);
    }

    public function testNewPlayerSave() {
        $player = new Player();

        try {
            $player->save();
            $this->assertTrue(false, 'Player with no data saved successfully! Bad!');
        } catch (\PDOException $e) {
            $this->assertContains('Not null violation', $e->getMessage());
        }
    }

    /**
     * test that levelUp fails if not enough kills
     */
    public function testLevelUpKillsFail() {
        $char = Player::find($this->char_id);
        $char->kills = 0;
        $char->save();

        $this->assertFalse($char->levelUp());
    }

    /**
     * test that levelUp succeeds if enough kills
     */
    public function testLevelUpSucceeds() {
        $char = Player::find($this->char_id);
        $char->kills = 100;
        $char->save();

        $this->assertTrue($char->levelUp());
    }

    /**
     * test that levelUp changes player stats
     */
    public function testLevelUpChangesStats() {
        $original_char = Player::find($this->char_id);
        $original_char->kills = 100;
        $original_char->save();

        $char = Player::find($this->char_id);

        $char->levelUp();

        $this->assertGreaterThan($original_char->strength, $char->strength);
        $this->assertGreaterThan($original_char->health, $char->health);
        $this->assertGreaterThan($original_char->turns, $char->turns);
        $this->assertGreaterThan($original_char->stamina, $char->stamina);
        $this->assertGreaterThan($original_char->ki, $char->ki);
        $this->assertGreaterThan($original_char->speed, $char->speed);
        $this->assertGreaterThan($original_char->karma, $char->karma);
        $this->assertGreaterThan($original_char->level, $char->level);
    }

    /**
     * test that levelUp removes kill
     */
    public function testLevelUpRemovesKills() {
        $original_char = Player::find($this->char_id);
        $original_char->kills = 100;
        $original_char->save();

        $char = Player::find($this->char_id);

        $char->levelUp();

        $this->assertLessThan($original_char->kills, $char->kills);
    }

    public function testSetClassAndSave() {
        $char = Player::find($this->char_id);

        if (!in_array($char->identity, ['viper', 'dragon', 'crane'])) {
            $class = 'viper';
        } else {
            $class = 'tiger';
        }

        $char->setClass($class);
        $char->save();

        $updated_char = Player::find($this->char_id);
        $this->assertEquals($char->class_name, $updated_char->class_name, 'The class names for the character were not identical.');
        $this->assertEquals($char->identity, $updated_char->identity, 'The class identities pre/post update were not identical.');
    }

    public function testSetClassNegative() {
        $char = Player::find($this->char_id);
        $class = $char->getClassName();
        $char->setClass('BANANA');

        $this->assertEquals($class, $char->getClassName());
    }

    public function testSetClassChangesCurrentPCClass(){
        $char = Player::find($this->char_id);
        $class = $char->getClassName();
        $char->setClass('Viper');
        $this->assertEquals('Viper', $char->getClassName());
    }

    public function testClassStringValidationPositive() {
        $return = Player::validStatus('STEALTH');
        $this->assertInternalType('int', $return);
    }

    public function testClassStringValidationInvalidValue() {
        $return = Player::validStatus('BANANA_IS_FAKE');
        $this->assertEquals(0, $return);
    }

    public function testClassStringValidationInvalidType() {
        $return = Player::validStatus([]);
        $this->assertNull($return);
    }

    public function testFindActive() {
        $result = Player::findActive(5, false);
        $active = true;
        $count = 0;

        foreach ($result AS $record) {
            $active = $active && Player::find($record['player_id'])->active;
            $count++;
        }

        $this->assertTrue($active);
        $this->assertLessThan(6, $count);
    }

}
