<?php

namespace NinjaWars\tests\unit;

use NinjaWars\tests\MockDeity;
use NinjaWars\tests\MockGameLog;
use NWTest;

class MockDeityUnitTest extends NWTest
{
    public function testMockDeityFunctions()
    {
        $logger = new MockGameLog();
        $deity = new MockDeity($logger);
        $increase_ki = $deity->increaseki();
        $rerank = $deity->rerank();
        $this->assertTrue((bool)$increase_ki, 'Increase ki was:'.$increase_ki);
        $this->assertTrue((bool)$rerank, 'Rerank was not truthy, it was:'.$rerank);
        $this->assertTrue((bool)$deity::DEFAULT_REGEN);
    }
}
