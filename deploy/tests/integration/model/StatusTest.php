<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use \model\Status;
use NinjaWars\core\data\Player;

class TestStatus extends NWTest {

    public function setUp() {
        parent::setUp();
        $this->char_id = TestAccountCreateAndDestroy::char_id();
        $status = new Status();
        $status->_player_id = $this->char_id;
        $status->name = 'test_effect';
        $status->secs_duration = 100;
        $status->save();
        $this->assertGreaterThan(0, $status->id);
    }

    public function tearDown() {
        parent::tearDown();
        // Delete testing news.
        //query('delete from statuses where _player_id = :id', [':id'=>$this->char_id]);
        //TestAccountCreateAndDestroy::destroy();
        //unset($this->char_id);
    }

    public function testStatusCanInstantiate(){
        $obj = new Status();
        $this->assertTrue($obj instanceof Status);
    }

    public function testStatusClassHasASaveMethod(){
        $this->assertTrue(is_callable('Status', 'save'), 'No save method found on object!');
    }

    public function testCanAddStatusViaStaticMethod(){
        $id = Status::refreshStatusEffect('weakenedt', Player::find($this->char_id), 434, true); // Allow refresh
        $this->assertGreaterThan(0, $id);
    }
    public function testCanAddStatusViaStaticMethodAndStaticCheckhasTextStatusAfter(){
        $name = 'weakenedt';
        $char = Player::find($this->char_id);
        $id = Status::refreshStatusEffect($name, $char, 10, true); // Allow refresh
        $this->assertGreaterThan(0, $id);
        $this->assertGreaterThan(0, (Status::find($id))->id);
        $found = Status::queryStatusEffect($name, $char);
        $this->assertGreaterThan(0, $found, 'Status was not found after being created.');
    }

    public function testStatusCanBeFound(){
        $status_id = Status::refreshStatusEffect('weakenedt', Player::find($this->char_id), 434, true); // Allow refresh
        $status = Status::find($status_id);
        $this->assertGreaterThan(0, $status->id);
        $this->assertGreaterThan(0, mb_strlen($status->name));
    }

    public function testStatusCanBeFoundByCharacter(){
        $char = Player::find($this->char_id);
        $char->addTextStatus('unit_test', 10, true);
        $statuses = Status::findStatusesByNinja($this->char_id);
        $status = null;
        foreach($statuses as $check_status){
            if($check_status->name === 'unit_test'){
                $status = $check_status;
            }
        }
        $this->assertGreaterThan(0, $status->id);
        $this->assertGreaterThan(0, mb_strlen($status->name));
        $this->assertEquals($this->char_id, $status->_player_id);
        $this->assertEquals('unit_test', $status->name);
    }

    public function testAddAnArbitraryStatusToACharacter(){
        $char = Player::find($this->char_id);
        $char->addTextStatus('poison', 300, true); // Refresh if necessary
        $this->assertTrue($char->hasTextStatus('poison'));
    }

    public function testCharacterHasStatus(){
        $char = Player::find($this->char_id);
        $char->addTextStatus('teste', 555, true); // Refresh if necessary
        $this->assertTrue($char->hasTextStatus('teste'));
        $this->assertTrue($char->hasTextStatus('TESTE'));
    }

}
