<?php

use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class ClanTest extends NWTest {
    private $clan_id;
    private $char_id;
    private $char_id_2;
    private $clan;
    private $clan_identity = 'phpunit_test_clan';

    public function destroyClan() {
        query('delete from clan where clan_id = :id or clan_name = :clan_name', [':id'=>$this->clan_id, ':clan_name'=>$this->clan_identity]);
    }

    public function setUp(): void {
        parent::setUp();
        TestAccountCreateAndDestroy::destroy();
        $this->destroyClan();
        $this->char_id   = TestAccountCreateAndDestroy::char_id();
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $this->clan = Clan::create(Player::find($this->char_id_2), $this->clan_identity);
        $this->clan_id   = $this->clan->id;
    }

    public function tearDown(): void {
        $this->destroyClan();
        TestAccountCreateAndDestroy::destroy();
        parent::tearDown();
    }

    public function testSetClanInfo() {
        $clan = new Clan();
        $clan->setFounder('randomFounder');
        $clan->setName('AwesomeClan');
        $this->assertEquals('randomFounder', $clan->getFounder());
        $this->assertEquals('AwesomeClan', $clan->getName());
    }

    public function testFindClanObject() {
        $clan = Clan::find($this->clan_id);
        $this->assertInstanceOf('NinjaWars\core\data\Clan', $clan);
        $this->assertEquals($this->clan_id, $clan->id);
    }

    public function testClanAddMember() {
        $player = Player::find($this->char_id);
        $clan = Clan::find($this->clan_id);
        $this->assertTrue($clan->addMember($player, $player));
    }

    public function testClanGetMembers() {
        $player1 = Player::find($this->char_id);
        $player2 = Player::find($this->char_id_2);
        $clan = $this->clan;
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertEquals(2, $clan->getMemberCount());
        $this->assertTrue($clan->hasMember($player1->id()));
        $this->assertTrue($clan->hasMember($player2->id()));
    }

    public function testGetClanForANinjaThatDoesntHaveAClanAtAllShouldYieldNull() {
        $player1 = Player::find($this->char_id);
        $clan_final = Clan::findByMember($player1);
        $this->assertEmpty($clan_final);
    }

    public function testGetClanThatAMemberBelongsTo() {
        $player1 = Player::find($this->char_id);
        $clan = Clan::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $clan_final = Clan::findByMember($player1);
        $this->assertTrue($clan_final->hasMember($player1->id()));
    }

    public function testKickClanMember() {
        $player1 = Player::find($this->char_id);
        $player2 = Player::find($this->char_id_2);
        $clan = $this->clan;
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->hasMember($player2->id()));
        $this->assertTrue($clan->hasMember($player1->id()));
        $clan->kickMember($player1->id(), $player2);
        $this->assertFalse($clan->hasMember($player1->id()));
    }

    public function testPromoteClanMember() {
        $player1 = Player::find($this->char_id);
        $clan = Clan::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->promoteMember($player1->id()));
    }

    public function testGetRankedClanMembersOfAClan() {
        $player1 = Player::find($this->char_id);
        $clan = Clan::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertEquals(2, count($clan->getMembers()));
    }

    public function testGetTheClanAvatarUrl() {
        $clan = Clan::find($this->clan_id);
        $clan->setAvatarUrl($g = 'https://google.com/someimage.jpg');
        $this->assertEquals($g, $clan->getAvatarUrl());
    }

    public function testSavingTheClan() {
        $clan = $this->clan;
        $clan->setDescription($d = 'a new description');
        $clan->setFounder($f = 'newFounder');
        $clan->setAvatarUrl($url = 'https://example.com/avatar.png');
        $was_saved = $clan->save();
        $this->assertTrue($was_saved);
        $saved = Clan::find($clan->id);
        $this->assertEquals($d, $saved->getDescription());
        $this->assertEquals($f, $saved->getFounder());
        $this->assertEquals($url, $saved->getAvatarUrl());
    }

    public function testInviteCharacterToYourClan() {
        $error = $this->clan->invite(Player::find($this->char_id), Player::find($this->char_id_2));
        $this->assertFalse((bool)$error);
    }

    public function testGetClanObjectNumericRating() {
        $this->markTestIncomplete('Clan rating is not yet implemented');
        $player1 = Player::find($this->char_id);
        $clan = Clan::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->addMember(Player::find($this->char_id_2), $player1));
        $this->assertTrue($clan->rating());
    }

    public function testClanNamePositiveValidation() {
        $clanName = 'Clan Beagle';
        $this->assertTrue((bool)Clan::isValidClanName($clanName));
    }

    public function testClanNameNegativeValidation() {
        $clanName = 'Ù�Ø³ Ø§Ù�Ù�Ø·Ø';
        $this->assertFalse((bool)Clan::isValidClanName($clanName));
    }
}
