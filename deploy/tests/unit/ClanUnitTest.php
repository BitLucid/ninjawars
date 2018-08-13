<?php
use NinjaWars\core\data\Clan;

class ClanUnitTest extends NWTest {
    private $clan;
    private $data;

    public function __construct() {
        $this->data = [
            'clan_name'       => 'Clan Name',
            'clan_avatar_url' => 'http://localhost/',
            'clan_founder'    => 'Founder',
            'description'     => 'Clan description',
            'clan_id'         => 1,
        ];
    }

	public function setUp() {
        parent::setUp();
        $this->clan = new Clan($this->data['clan_id'], $this->data['clan_name'], $this->data);
    }


	public function tearDown() {
        parent::tearDown();
    }

    public function testClanConstructor() {
        $this->assertInstanceOf('NinjaWars\core\data\Clan', $this->clan);
    }

    public function testGetFounder() {
        $this->assertEquals($this->clan->getFounder(), $this->data['clan_founder']);
    }

    public function testGetDescription() {
        $this->assertEquals($this->clan->getDescription(), $this->data['description']);
    }

    public function testGetAvatarUrl() {
        $this->assertEquals($this->clan->getAvatarUrl(), $this->data['clan_avatar_url']);
    }
}
