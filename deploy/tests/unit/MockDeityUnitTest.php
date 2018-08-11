<?php
namespace NinjaWars\tests\unit;

use \PHPUnit_Framework_TestCase;
use NinjaWars\tests\MockDeity;
use NinjaWars\tests\MockGameLog;

class MockDeityUnitTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
    }

	protected function tearDown() {
    }

    public function testMockDeityFunctions(){
        $deity = new MockDeity(new MockGameLog());
        $this->assertTrue((bool)$deity->increaseki());
        $this->assertTrue((bool)$deity->rerank());
        $this->assertTrue((bool)$deity::DEFAULT_REGEN);
    }
}

