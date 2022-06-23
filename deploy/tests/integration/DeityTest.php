<?php

use NinjaWars\core\data\Deity;
use NinjaWars\core\data\GameLog;

class DeityTest extends NWTest
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testDeityReranking()
    {
        $logger = new GameLog();
        // rerank the deity rankings list
        $deity = new Deity($logger);
        $deity->rerank();
        // Expect no exceptions
        $this->assertTrue(true); // As long as it gets to here.
    }
}
