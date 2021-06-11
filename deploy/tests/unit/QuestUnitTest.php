<?php
use NinjaWars\core\data\Quest;

class QuestUnitTest extends NWTest {
    private $quest;
    private $data;

	public function setUp():void {
        parent::setUp();
        $this->data = [
            'title'       => 'Some QuEst TiTle HeRe',
            'description'     => 'What I want you to do, X, Y, and Z',
            'tags'            => 'shadow_quest,epic,ninjamaster_level',
            'karma'           => 5,
            'proof'           => 'For proof, provide screenshots',
            'difficulty'      => 20,
        ];
        $this->quest = new Quest($this->data);
    }

	public function tearDown():void {
        $this->quest = null;
        parent::tearDown();
    }

    public function testQuestConstructor() {
        $this->assertInstanceOf('NinjaWars\core\data\Quest', $this->quest);
    }

    public function testConstructEmptyQuest(){
        $q = new Quest();
        $this->assertInstanceOf('NinjaWars\core\data\Quest', $q);
    }

    public function testGetDescription() {
        $this->assertEquals($this->quest->description, $this->data['description']);
    }

}
