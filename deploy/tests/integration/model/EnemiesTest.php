<?php

use NinjaWars\core\data\Enemies;
use NinjaWars\core\data\Player;

class EnemiesTest extends NWTest
{
    var $char;
    var $char_id;
    var $char_id_2;

    public function setUp(): void
    {
        parent::setUp();
        TestAccountCreateAndDestroy::destroy();
        $this->char_id   = TestAccountCreateAndDestroy::char_id();
        $this->char = Player::find($this->char_id);
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2();
    }

    public function tearDown(): void
    {
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::tearDown();
    }

    public function testAddEnemy()
    {
        Enemies::add($this->char, $this->char_id_2);
        $this->assertEquals(1, Enemies::count($this->char));
    }

    public function testAddMultipleEnemies()
    {
        Enemies::add($this->char, $this->char_id_2);
        Enemies::add($this->char, $this->char_id_2);
        $this->assertEquals(1, Enemies::count($this->char)); // Multiple adds should just result in the same one
    }

    public function testAddEnemyWithoutPlayerShouldFail()
    {
        $this->expectException(TypeError::class);
        Enemies::add(null, $this->char_id_2); // Intentional null
        $this->assertEquals(true, false); // Should never get reached
    }

    public function testRemoveEnemy()
    {
        Enemies::add($this->char, $this->char_id_2);
        $this->assertEquals(1, Enemies::count($this->char));
        Enemies::remove($this->char, $this->char_id_2);
        $this->assertEquals(0, Enemies::count($this->char));
    }

    public function testGetCurrentEnemies()
    {
        Enemies::add($this->char, $this->char_id_2);
        $this->assertEquals(1, count(Enemies::getCurrent($this->char)));
    }

    public function testGetAllEnemies()
    {
        Enemies::add($this->char, $this->char_id_2);
        $found = Enemies::getAllForPlayerAndEnemy($this->char, $this->char_id_2);
        $this->assertEquals(1, count($found));
    }

    public function testSearchEnemiesCannotFindInactive()
    {
        $char_2 = Player::find($this->char_id_2);
        $char_2->active = 0;
        $char_2->save();
        Enemies::add($this->char, $this->char_id_2);
        $this->assertEquals(0, count(Enemies::search($this->char, $this->char_id_2)));
    }

    public function testSearchEnemiesCanFindActive()
    {
        $this->assertEquals(1, count(Enemies::search($this->char, Player::find($this->char_id_2)->name())));
    }
}
