<?php
use NinjaWars\core\control\Clan;

class ClanUnitTest extends PHPUnit_Framework_TestCase {
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

	protected function setUp() {
        $this->clan = new Clan($this->data['clan_id'], $this->data['clan_name'], $this->data);
    }


	protected function tearDown() {
    }

    public function testClanConstructor() {
        $this->assertInstanceOf('NinjaWars\core\control\Clan', $this->clan);
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
