<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use Symfony\Component\HttpFoundation\Request; // Just for request created below.
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\Filter;

class TestInput extends NWTest {
	public function setUp():void {
		parent::setUp();
		$get = [
			'id'         => 7,
			'ninja_name' => 5,
            'some_negative_int'=> -444,
            'some_int'         => 66,
            'garbage_field'    => 'Robert\'); drop table students; --'
		];

		$post = [
			'hidden_post'      => 1,
			'post_post_field'  => 'Bob',
            'post_negative_int'=> -234,
            'post_some_int'         => 34,
            'post_garbage_field'    => 'Robert\'); drop table students; --'
		];

        $request = new Request($get, $post);

        RequestWrapper::inject($request); // Pass a request to be used by tests
	}

	public function tearDown():void {
        RequestWrapper::destroy();
		parent::tearDown();
    }

    public function testInputWithinEnvironment() {
        $id = RequestWrapper::getPostOrGet('id');
        $this->assertEquals(7, $id);
        $default_result = RequestWrapper::getPostOrGet('doesnotexist', 5);
        $this->assertEquals(5, $default_result);
    }

    public function testPostWithinMockedEnvironment() {
        $posted = RequestWrapper::getPost('post_post_field', 'Bob');
        $this->assertEquals('Bob', $posted);
        $default = RequestWrapper::getPost('blah_doesnt_exist', 7777);
        $this->assertEquals(7777, $default);
    }

	public function testNonNegativeInt() {
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
	public function testSanitizeToInt() {
		$this->assertEquals(4, Filter::toInt(4));
		$this->assertEquals(-4, Filter::toInt(-4));
		$this->assertNull(Filter::toInt(4.1));
		$this->assertNull(Filter::toInt(4.9));
		$this->assertEquals(0, Filter::toInt('somestring'));
		$this->assertNull(Filter::toInt([]));
		$this->assertEquals(0, Filter::toInt(0));
	}

	public function testToInt() {
		$this->assertEquals(4, Filter::toInt(4));
		$this->assertEquals(-4, Filter::toInt(-4));
		$this->assertNull(Filter::toInt(4.1));
		$this->assertNull(Filter::toInt(4.9));
		$this->assertEquals(0, Filter::toInt('somestring'));
		$this->assertNull(Filter::toInt([]));
		$this->assertEquals(0, Filter::toInt(0));
	}

    public function testFilterToSimple(){
        $this->assertEquals('boba', Filter::toSimple("bob\0aä\x80"));
        $this->assertEquals("!@#^&()_+--", Filter::toSimple("!@#^&()_+'''\"\"''--"));
    }

}
