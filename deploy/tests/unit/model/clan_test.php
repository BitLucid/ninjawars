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
        $this->char_id = TestAccountCreateAndDestroy::char_id();
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2();
	}

    private function deleteClan($id){
        query('delete from clan where clan_id = :id', [':id'=>$id]);
    }
	
	function tearDown(){
        $this->deleteClan($this->clan_id);
        TestAccountCreateAndDestroy::purge_test_accounts();
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

    function testClanAddMembers(){
        $p1 = new Player($this->char_id); 
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $this->assertTrue($clan->addMember($p2 = new Player($this->char_id_2), $p1));
    }

    function testClanGetMembers(){
        $p1 = new Player($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $this->assertTrue($clan->addMember($p2 = new Player($this->char_id_2), $p1));
        $member_ids = $clan->getMemberIds();
        $this->assertEquals(2, rco($member_ids));
        $this->assertTrue($clan->hasMember($p1->id()));
        $this->assertTrue($clan->hasMember($p2->id()));
    }

    function testGetClanForANinjaThatDoesntHaveAClanAtAllShouldYieldNull(){
        $p1 = new Player($this->char_id);
        $clan_final = ClanFactory::clanOfMember($p1);
        $this->assertEmpty($clan_final);
    }

    function testGetClanThatAMemberBelongsTo(){
        $p1 = new Player($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $clan_final = ClanFactory::clanOfMember($p1);
        $this->assertTrue($clan_final->hasMember($p1->id()));
    }

    function testKickClanMember(){
        $p1 = new Player($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $this->assertTrue($clan->addMember($p2 = new Player($this->char_id_2), $p1));
        $this->assertTrue($clan->hasMember($p2->id()));
        $this->assertTrue($clan->hasMember($p1->id()));
        $clan->kickMember($p1->id(), $p2);
        $this->assertFalse($clan->hasMember($p1->id()));
    }

    function testPromoteClanMember(){
        $p1 = new Player($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $this->assertTrue($clan->promoteMember($p1->id()));
    }

    function testInviteCharacterToYourClan(){
        $this->markTestIncomplete('Inviting a ninja from the clan object is not yet implemented.');
    }

    function testGetClanObjectNumericRating(){
        $this->markTestIncomplete('Clan rating is not yet implemented');
        $p1 = new Player($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($p1, $p1));
        $this->assertTrue($clan->addMember($p2 = new Player($this->char_id_2), $p1));
        $this->assertTrue($clan->rating());
    }

}

