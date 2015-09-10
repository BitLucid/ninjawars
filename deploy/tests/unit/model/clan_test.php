<?php
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');
require_once(LIB_ROOT.'data/ClanFactory.php');

// Note that the file has to have a file ending of ...test.php to be run by phpunit


class TestClan extends PHPUnit_Framework_TestCase {

	function setUp(){
        $clan_identity = 'randomNewTestClan';
        $id_already_exists = query_item('select clan_id from clan where clan_name = :name',
            [':name'=>$clan_identity]);
        if($id_already_exists){
            $this->deleteClan($id_already_exists);
        }
        $this->clan = ClanFactory::create($clan_identity, ['founder'=>'phpunittest', 'description'=>'Some clan description']);
        //var_dump($this->clan);
        $this->clan_id = $this->clan->getId();
	}

    private function deleteClan($id){
        query('delete from clan where clan_id = :id', [':id'=>$id]);
    }
	
	function tearDown(){
        $this->deleteClan($this->clan_id);
    }

    function testSetClanInfo(){
        $clan = new Clan();
        $clan->setFounder('randomFounder');
        $clan->setName('AwesomeClan');
        $this->assertEquals('randomFounder', $clan->getFounder());
        $this->assertEquals('AwesomeClan', $clan->getName());
    }

    function testFindClanObject(){
        $clan = ClanFactory::find($this->clan_id);
        $this->assertInstanceOf('Clan', $clan);
        $this->assertEquals($this->clan_id, $clan->getId());
    }

    // TODO: Clan get ranking
    // TODO: Clan get members
    // TODO: Clan addMember
    // TODO: Clan kickMember

}

