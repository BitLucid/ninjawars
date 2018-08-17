<?php
namespace NinjaWars\tests\unit;

use NinjaWars\tests\MockDeity;
use NinjaWars\tests\MockGameLog;
use \NWTest;

class MockDeityUnitTest extends NWTest {
    public function testMockDeityFunctions(){
        $deity = new MockDeity(new MockGameLog());
        $this->assertTrue((bool)$deity->increaseki());
        $this->assertTrue((bool)$deity->rerank());
        $this->assertTrue((bool)$deity::DEFAULT_REGEN);
    }
}

