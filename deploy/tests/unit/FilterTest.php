<?php

// Note that the file has to have a file ending of ...test.php to be run by phpunit

use NinjaWars\core\Filter;

class FilterTest extends NWTest
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testNonNegativeInt()
    {
        $this->assertEquals(4, Filter::toNonNegativeInt(4));
        $this->assertEquals(0, Filter::toNonNegativeInt(-4));
        $this->assertEquals(0, Filter::toNonNegativeInt(4.1));
        $this->assertEquals(0, Filter::toNonNegativeInt(4.9));
        $this->assertEquals(0, Filter::toNonNegativeInt(0));
        $this->assertEquals(0, Filter::toNonNegativeInt('somestring'));
        $this->assertEquals(0, Filter::toNonNegativeInt([]));
    }

    /**
     *
     */
    public function testSanitizeToInt()
    {
        $this->assertEquals(4, Filter::toInt(4));
        $this->assertEquals(-4, Filter::toInt(-4));
        $this->assertNull(Filter::toInt(4.1));
        $this->assertNull(Filter::toInt(4.9));
        $this->assertEquals(0, Filter::toInt('somestring'));
        $this->assertNull(Filter::toInt([]));
        $this->assertEquals(0, Filter::toInt(0));
    }

    public function testToInt()
    {
        $this->assertEquals(4, Filter::toInt(4));
        $this->assertEquals(-4, Filter::toInt(-4));
        $this->assertNull(Filter::toInt(4.1));
        $this->assertNull(Filter::toInt(4.9));
        $this->assertEquals(0, Filter::toInt('somestring'));
        $this->assertNull(Filter::toInt([]));
        $this->assertEquals(0, Filter::toInt(0));
    }

    public function testFilterToSimple()
    {
        $this->assertEquals('boba', Filter::toSimple("bob\0aä\x80"));
        $this->assertEquals("!@#^&()_+--", Filter::toSimple("!@#^&()_+'''\"\"''--"));
    }
}
