<?php
namespace NinjaWars\tests\unit;

use NinjaWars\core\control\TickController;
use NinjaWars\tests\MockGameLog;
use NinjaWars\tests\MockDeity;

class TickControllerUnitTest extends \NWTest {

	public function setUp() {
        parent::setUp();
    }

	public function tearDown() {
        parent::tearDown();
    }

    function testTickControllerInstantiates() {
        $tick = new TickController(new MockGameLog(), new MockDeity());
        $this->assertTrue($tick instanceof TickController);
    }

    function testTickRunsVariousTicksWithoutErrors() {

        $logger = new MockGameLog();

        $tick = new TickController($logger, new MockDeity());
        $tick->atomic();
        $tick->tiny();
        $tick->minor();
        $tick->major();
        $tick->nightly();
        $this->assertTrue(true); // Just has to be reached in terms of a sanity check
    }
}

