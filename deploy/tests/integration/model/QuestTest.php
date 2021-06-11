<?php
use NinjaWars\core\data\Quest;
use NinjaWars\core\data\Player;

class TestQuest extends NWTest {

    public function setUp():void {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();

        $this->data = [
            'title'       => 'Some QuEst TiTle HeRe',
            'description'     => 'What I want you to do, X, Y, and Z',
            '_player_id'      => $this->char->id(),
            'tags'            => 'shadow_quest,epic,ninjamaster_level',
            'karma'           => rand(5, 888),
            'proof'           => 'For proof, provide screenshots',
            'difficulty'      => 20,
        ];
        $this->quest = new Quest($this->data);
    }

    public function tearDown():void {
        TestAccountCreateAndDestroy::destroy();
        /*if ($this->quest) {
            //query('delete from quests where quest_id = :id and quest_id > 1', [':id'=>$this->quest->id()]);
        }*/
        parent::tearDown();
    }

    public function testQuestCanInstantiate() {
        $quest = new Quest();
        $this->assertTrue($quest instanceof Quest);
    }

    public function testQuestCanBeSaved(){
        $this->markTestIncomplete('Cannot yet save quest models');
        $id = $this->quest->id();
        $this->quest->save();
        $copied = Quest::find($id);
        Quest::delete($id);
        $this->assertEquals($copied->_player_id, $this->quest->_player_id);
        $this->assertEquals($copied->title, $this->quest->title);
        $this->assertEquals($copied->karma, $this->quest->karma);
    }

    public function testGettingAPlayerBackFromAQuest(){
        $this->markTestIncomplete('Cannot yet save quest models');
        $id = $this->quest->id();
        $this->quest->save();
        $copiedq = Quest::find($id);
        $this->assertInstanceOf('NinjaWars\core\data\Player', $copiedq->player());
    }

}
