<?php
namespace NinjaWars\tests\unit;

use NinjaWars\core\control\TickController;
use NinjaWars\tests\MockGameLog;
use NinjaWars\tests\MockDeity;

class TickControllerUnitTest extends \NWTest {

	public function setUp():void {
        parent::setUp();
    }

	public function tearDown():void {
        parent::tearDown();
    }

    function testTickControllerInstantiates() {
        $logger = new MockGameLog();
        $tick = new TickController(new MockGameLog(), new MockDeity($logger));
        $this->assertTrue($tick instanceof TickController);
    }

    function testTickRunsVariousTicksWithoutErrors() {

        $logger = new MockGameLog();

        $tick = new TickController($logger, new MockDeity($logger));
        $tick->atomic();
        $tick->tiny();
        $tick->minor();
        $tick->major();
        $tick->nightly();
        $this->assertTrue(true); // Just has to be reached in terms of a sanity check
    }
}

