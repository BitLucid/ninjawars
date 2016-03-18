<?php
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class ClanTest extends PHPUnit_Framework_TestCase {
    private $clan_id;
    private $char_id;
    private $char_id_2;

	function setUp() {
        $clan_identity = 'randomNewTestClan';

        $id_already_exists = query_item(
            'select clan_id from clan where clan_name = :name',
            [':name' => $clan_identity]
        );

        if ($id_already_exists) {
            $this->deleteClan($id_already_exists);
        }

        $this->clan = ClanFactory::create(
            $clan_identity,
            [
                'founder'     => 'phpunittest',
                'description' => 'Some clan description'
            ]
        );

        $this->clan_id   = $this->clan->getId();
        $this->char_id   = TestAccountCreateAndDestroy::char_id();
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2();
	}

    /**
     * @todo remove this function in favor of static method on Clan model
     */
    private function deleteClan($clanId) {
        query('delete from clan where clan_id = :id', [':id'=>$clanId]);
    }

    /**
     * @todo remove this function in favor of static method on Clan model
     */
    private function deleteClanByIdentity($identity) {
        query('delete from clan where clan_name = :identity', [':identity'=>$identity]);
    }

	function tearDown() {
        $this->deleteClan($this->clan_id);
        $this->deleteClanByIdentity('someTestClan');
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

    function testClanAddMembers(){
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->addMember(Player::find($this->char_id_2), $player1));
    }

    function testClanGetMembers(){
        $player1 = Player::find($this->char_id);
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->addMember($player2 = Player::find($this->char_id_2), $player1));
        $member_ids = $clan->getMemberIds();
        $this->assertEquals(2, rco($member_ids));
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
        $clan = ClanFactory::find($this->clan_id);
        $this->assertTrue($clan->addMember($player1, $player1));
        $this->assertTrue($clan->addMember($player2 = Player::find($this->char_id_2), $player1));
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
        $this->assertEquals(1, rco($clan->getMembers()));
    }

    function testGetTheClanAvatarUrl(){
        $clan = ClanFactory::find($this->clan_id);
        $clan->setAvatarUrl($g = 'http://google.com/someimage.jpg');
        $this->assertEquals($g, $clan->getAvatarUrl());
    }

    function testSavingTheClanViaTheFactory(){
        $clan = ClanFactory::create('someTestClan', ['founder'=>'noone', 'clan_avatar_url'=>'http://example.com/img.png', 'description'=>'SomeDesc']);
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
        $clan = ClanFactory::create('someTestClan', ['founder'=>'noone', 'clan_avatar_url'=>'http://example.com/img.png', 'description'=>'SomeDesc']);
        $error = $clan->invite(Player::find($this->char_id), Player::find($this->char_id_2));
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
