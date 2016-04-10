<?php
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class ClanTest extends PHPUnit_Framework_TestCase {
    private $clan_id;
    private $char_id;
    private $char_id_2;
    private $clan_identity = 'phpunit_test_clan';

	function setUp() {
        $this->char_id   = TestAccountCreateAndDestroy::char_id();
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $this->clan = Clan::create(Player::find($this->char_id_2), $this->clan_identity);
        $this->clan_id   = $this->clan->getId();
	}

	function tearDown() {
        query('delete from clan where clan_id = :id', [':id'=>$this->clan->id()]);
        query('delete from clan_player where _clan_id = :id', [':id'=>$this->clan->id()]);
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    function testSetClanInfo() {
        $clan = new Clan();
        $clan->setFounder('randomFounder');
        $clan->setName('AwesomeClan');
        $this->assertEquals('randomFounder', $clan->getFounder());
        $this->assertEquals('AwesomeClan', $clan->getName());
    }

    function testFindClanObject() {
        $clan = ClanFactory::find($this->clan_id);
        $this->assertInstanceOf('NinjaWars\core\data\Clan', $clan);
        $this->assertEquals($this->clan_id, $clan->getId());
    }

    function testClanAddMember(){
        $player = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player, $player));
    }

    function testClanGetMembers() {
        $player1 = Player::find($this->char_id);
        $player2 = Player::find($this->char_id_2);
        $clan = $this->clan;
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertEquals(2, $clan->getMemberCount());
        $this->assertTrue($clan->hasMember($player1->id()));
        $this->assertTrue($clan->hasMember($player2->id()));
    }

    function testGetClanForANinjaThatDoesntHaveAClanAtAllShouldYieldNull(){
        $player1 = Player::find($this->char_id);
        $clan_final = ClanFactory::clanOfMember($player1);
        $this->assertEmpty($clan_final);
    }

    function testGetClanThatAMemberBelongsTo(){
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $clan_final = ClanFactory::clanOfMember($player1);
        $this->assertTrue($clan_final->hasMember($player1->id()));
    }

    function testKickClanMember(){
        $player1 = Player::find($this->char_id);
        $player2 = Player::find($this->char_id_2);
        $clan = $this->clan;
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->hasMember($player2->id()));
        $this->assertTrue($clan->hasMember($player1->id()));
        $clan->kickMember($player1->id(), $player2);
        $this->assertFalse($clan->hasMember($player1->id()));
    }

    function testPromoteClanMember(){
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->promoteMember($player1->id()));
    }

    function testGetRankedClanMembersOfAClan(){
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertEquals(2, count($clan->getMembers()));
    }

    function testGetTheClanAvatarUrl(){
        $clan = ClanFactory::find($this->clan_id);
        $clan->setAvatarUrl($g = 'http://google.com/someimage.jpg');
        $this->assertEquals($g, $clan->getAvatarUrl());
    }

    function testSavingTheClanViaTheFactory(){
        $clan = $this->clan;
        $clan->setDescription($d = 'a new description');
        $clan->setFounder($f = 'newFounder');
        $clan->setAvatarUrl($url = 'http://example.com/avatar.png');
        $was_saved = ClanFactory::save($clan);
        $this->assertTrue($was_saved);
        $saved = ClanFactory::find($clan->id());
        $this->assertEquals($d, $saved->getDescription());
        $this->assertEquals($f, $saved->getFounder());
        $this->assertEquals($url, $saved->getAvatarUrl());
    }

    function testInviteCharacterToYourClan(){
        $error = $this->clan->invite(Player::find($this->char_id), Player::find($this->char_id_2));
        $this->assertFalse((bool)$error);
    }

    function testGetClanObjectNumericRating(){
        $this->markTestIncomplete('Clan rating is not yet implemented');
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->addMember(Player::find($this->char_id_2), $player1));
        $this->assertTrue($clan->rating());
    }

	function testClanNamePositiveValidation() {
		$clanName = 'Clan Beagle';
		$this->assertTrue((boolean)Clan::isValidClanName($clanName));
	}

	function testClanNameNegativeValidation() {
		$clanName = 'Ù�Ø³ Ø§Ù�Ù�Ø·Ø';
		$this->assertFalse((boolean)Clan::isValidClanName($clanName));
	}
}
