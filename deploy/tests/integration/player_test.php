<?php
class TestCharacter extends PHPUnit_Framework_TestCase {
	private $previous_server_ip = '';
	private $char_id;
    private $mock_ip = '127.0.0.199';


	/**
	 * group char
	**/
	public function setUp(){
		require_once(ROOT.'core/control/Player.class.php');
		$this->previous_server_ip = @$_SERVER['REMOTE_ADDR'];
		$_SERVER['REMOTE_ADDR']=$this->mock_ip;
		$this->test_email = TestAccountCreateAndDestroy::$test_email; // Something@example.com probably
		$this->test_password = TestAccountCreateAndDestroy::$test_password;
		$this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
		TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
		$char_id = TestAccountCreateAndDestroy::create_testing_account();
		$this->char_id = $char_id;
	}
	
	/**
	 * group char
	**/
	public function tearDown(){
		// Delete test user.
		TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
		$_SERVER['REMOTE_ADDR']=$this->previous_server_ip; // Reset remote addr to whatever it was before, just in case.
    }

	/**
	 * group char
	**/
    public function testCreatePlayerObject(){
    	$char = new Player($this->char_id);
    	$this->assertTrue((bool)positive_int($char->id()));
    }


    public function testPlayerCanBeFoundStatically() {
        $char = Player::find($this->char_id);
        $this->assertTrue((bool)positive_int($char->id()));
        $this->assertTrue((bool)$char->name());
    }


    public function testNonexistentPlayerReturnsNullViaStaticFind() {
        $id = query_item('select max(player_id) from players');
        $bad_id = $id + 100;
        $char = Player::find($bad_id);
        $this->assertEquals(null, $char);
    }


	/**
	 * group char
	**/
    public function testCreatePlayerObjectHasUsefulInfo(){
    	$char = new Player($this->char_id);
    	$this->assertTrue((bool)positive_int($char->health()));
		$this->assertTrue((bool)positive_int($char->speed()));
		$this->assertTrue((bool)positive_int($char->stamina()));
		$this->assertTrue((bool)positive_int($char->strength()));
		$this->assertTrue((bool)positive_int($char->level()));
		$this->assertNotEmpty($char->name());
		$this->assertTrue((bool)positive_int($char->damage()));
    }

    public function testPCHasVariousAttributesAndCanSetSome(){
        $char = new Player($this->char_id);
        $this->assertTrue(is_int($char->gold()));
        $this->assertTrue(is_int($char->turns()));
        $this->assertTrue(is_int($char->set_gold(45)));
        $this->assertTrue(is_int($char->set_turns(32)));
        $this->assertEquals(444, $char->set_bounty(444));
        $char->save();
        $char_dup = new Player($this->char_id);
        $this->assertEquals(444, $char_dup->bounty());
        $this->assertEquals(32, $char_dup->turns());
        $this->assertEquals(45, $char_dup->gold());
    }

    /**
     * group char
    **/
    public function testCharacterHasADifficultyRating(){
    	$char = new Player($this->char_id);
    	$this->assertGreaterThan(0, $char->difficulty());
    }

    public function testCharacterHasAVerificationNumber(){
        $char = new Player($this->char_id);
        $this->assertGreaterThan(0, $char->getVerificationNumber());
    }

	/**
	 * group char
	**/
    public function testPlayerStatusesChangeStatCalcs(){
    	$char = new Player($this->char_id);
    	$str = $char->strength();
    	$speed = $char->speed();
    	$stamina = $char->stamina();
    	$char->addStatus(SLOW);
    	$this->assertNotEquals($char->speed(), $speed, 'Speed should be different due to slow status.');
    	$this->assertTrue($char->speed() < $speed, 'Speed should be less due to slow status, but isn\'t.');
    	$char->addStatus(POISON);
    	$this->assertTrue($char->stamina() < $stamina);
    	$char->addStatus(WEAKENED);
    	$this->assertTrue($char->strength() < $str);
    	$char->resetStatus();
    	$this->assertEquals($char->strength(), $str);
    	$char->addStatus(STR_UP2);
    	$this->assertTrue($char->strength() > $str);
    }

    public function testPlayerObjectCAnReturnAnIPCorrectly(){
        $char = new Player($this->char_id);
        $this->assertEquals($this->mock_ip, $char->ip());
    }

    public function testPlayerObjectCanSaveDetails(){
        $bel = 'Believes in the mirror goddess.';
        $traits = 'Weird,Blue';
        $desc = 'Some description for testing';
        $goals = 'Test: to rule the world';
        $instincts = 'Kill Samurai';
        $char = new Player($this->char_id);
        $char->set_traits($traits);
        $char->set_beliefs($bel);
        $char->set_description($desc);
        $char->set_goals($goals);
        $char->set_instincts($instincts);
        $char->save();
        $char = new Player($this->char_id); // Create a new player copy.
        $this->assertEquals($desc, $char->description());
        $this->assertEquals($traits, $char->traits());
        $this->assertEquals($bel, $char->beliefs());
        $this->assertEquals($goals, $char->goals());
        $this->assertEquals($instincts, $char->instincts());
    }

    public function testNegativeKiRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->set_ki(-643);
    }

    public function testNegativeTurnsRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->set_turns(-345);
    }

    public function testNegativeStrengthRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->setStrength(-6);
    }

    public function testNegativeSpeedRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->setSpeed(-556);
    }

    public function testNegativeStaminaRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->setStamina(-34);
    }

    public function testNegativeHealthRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->set_health(-6);
    }

    public function testNegativeGoldRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->set_gold(-45);
    }

    public function testNegativeBountyRejected(){
        $this->setExpectedException('InvalidArgumentException');
        $char = new Player($this->char_id);
        $char->set_bounty(-45);
    }

    public function testPlayerHealChangesVOHealth(){
        $char = new Player($this->char_id);
        $half_health = floor($char->health()/2);
        $char->set_health($half_health);
        $char->save();
        $char = new Player($this->char_id);
        $this->assertEquals($half_health, $char->health());
        $this->assertLessThan($char->max_health(), $char->health());
        $char->heal($char->max_health()); // Heal by max_health, so up to 
        $char->save();
        $this->assertEquals($char->vo->health, $char->max_health());
        $this->assertEquals($char->health(), $char->max_health());
    }



    public function testPCCanObtainAGravatarUrl(){
        $char = new Player($this->char_id);
        $this->assertNotEmpty($char->avatarUrl());
        $this->assertTrue(strpos($char->avatarUrl(), 'avatar') !== false);
    }

    public function testCreatePlayerObjectCanSaveChanges(){
    	$char = new Player($this->char_id);
    	$ki = $char->ki();
    	$char->set_ki($ki+55);
        $char->set_gold(343);
        $char->save();
    	$char_copy = new Player($this->char_id);
    	$this->assertEquals($char_copy->ki(), $ki+55);
        $this->assertEquals($char_copy->gold(), 343);
    }

    public function testPlayerObjectReportDamageCorrectly(){
        $char = new Player($this->char_id);
        $damage = floor($char->health()/2);
        $char->set_health($char->health() - $damage);
        $char->save();
        $char = new Player($this->char_id);
        $this->assertEquals($damage, $char->is_hurt_by());
    }

    public function testPlayerObjectHarmWorksCorrectly(){
        $char = new Player($this->char_id);
        $damage = floor($char->health()/2);
        $char->harm($damage);
        //$char->save();
        $char = new Player($this->char_id);
        $this->assertEquals($damage, $char->is_hurt_by());
    }

    public function testKillCharByHarmingWithTheirFullHealth(){
        $char = new Player($this->char_id);
        $char->harm($char->health());
        $this->assertEquals(0, $char->vo->health);
        $this->assertEquals(0, $char->health());
    }

    public function testCauseDeath(){
        $char = new Player($this->char_id);
        $char->death();
        $this->assertEquals(0, $char->health());
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
        $this->assertFalse(level_up_if_possible($this->char_id));
    }

    /**
     * test that levelUp succeeds if enough kills
     */
    public function testLevelUpSucceeds() {
        $char = new Player($this->char_id);
        $char->vo->kills = 100;
        $char->save();

        $this->assertTrue(level_up_if_possible($char->id()));
    }

    /**
     * test that levelUp changes player stats
     */
    public function testLevelUpChangesStats() {
        $char = new Player($this->char_id);
        $char->vo->kills = 100;
        $char->save();

        level_up_if_possible($char->id());

        $levelled_char = new Player($this->char_id);

        $this->assertGreaterThan($char->strength, $levelled_char->strength);
        $this->assertGreaterThan($char->health, $levelled_char->health);
        $this->assertGreaterThan($char->turns, $levelled_char->turns);
        $this->assertGreaterThan($char->stamina, $levelled_char->stamina);
        $this->assertGreaterThan($char->ki, $levelled_char->ki);
        $this->assertGreaterThan($char->speed, $levelled_char->speed);
        $this->assertGreaterThan($char->karma, $levelled_char->karma);
        $this->assertGreaterThan($char->level, $levelled_char->level);
    }

    /**
     * test that levelUp removes kill
     */
    public function testLevelUpRemovesKills() {
        $char = new Player($this->char_id);
        $char->vo->kills = 100;
        $char->save();

        level_up_if_possible($char->id());

        $levelled_char = new Player($this->char_id);

        $this->assertLessThan($char->kills, $levelled_char->kills);
    }
}
