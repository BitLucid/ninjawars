<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use Symfony\Component\HttpFoundation\Request; // Just for request created below.
use app\environment\RequestWrapper;


class TestInput extends PHPUnit_Framework_TestCase {
	protected function setUp() {
		$get = [
			'id'         => 7,
			'ninja_name' => 5
		];

		$post = [
			'hidden_post'     => 1,
			'some_post_field' => 'Bob'
		];

        $request = new Request($get, $post);

        RequestWrapper::inject($request); // Pass a request to be used by tests
	}

	protected function tearDown() {
        RequestWrapper::destroy();
    }

    public function testInputWithinEnvironment() {
        $id = in('id');
        $this->assertEquals(7, $id);
        $default_result = in('doesnotexist', 5);
        $this->assertEquals(5, $default_result);
    }

	public function testInputWithFilter() {
		$this->assertEquals(in('some_post_field', null, "base64_encode"), base64_encode('Bob'));
	}

    public function testPostWithinMockedEnvironment() {
        $posted = post('some_post_field');
        $this->assertEquals('Bob', $posted);
        $default = post('blah_doesnt_exist', 7777);
        $this->assertEquals(7777, $default);
    }

	public function testNonNegativeInt() {
		$this->assertEquals(4, non_negative_int(4));
		$this->assertEquals(0, non_negative_int(-4));
		$this->assertEquals(0, non_negative_int(4.1));
		$this->assertEquals(0, non_negative_int(4.9));
		$this->assertEquals(0, non_negative_int(0));
		$this->assertEquals(0, non_negative_int('somestring'));
		$this->assertEquals(0, non_negative_int([]));
	}

	public function testPositiveInt() {
		$this->assertEquals(4, positive_int(4));
		$this->assertEquals(0, positive_int(-4));
		$this->assertEquals(0, positive_int(4.1));
		$this->assertEquals(0, positive_int(4.9));
		$this->assertEquals(0, positive_int(0));
		$this->assertEquals(0, positive_int('somestring'));
		$this->assertEquals(0, positive_int([]));
	}

	/**
	 * @todo review expected behavior of sanitize_to_int on strings
	 */
	public function testSanitizeToInt() {
		$this->assertEquals(4, sanitize_to_int(4));
		$this->assertEquals(-4, sanitize_to_int(-4));
		$this->assertNull(sanitize_to_int(4.1));
		$this->assertNull(sanitize_to_int(4.9));
		$this->assertEquals(0, sanitize_to_int('somestring'));
		$this->assertNull(sanitize_to_int([]));
		$this->assertEquals(0, sanitize_to_int(0));
	}

	public function testToInt() {
		$this->assertEquals(4, toInt(4));
		$this->assertEquals(-4, toInt(-4));
		$this->assertNull(toInt(4.1));
		$this->assertNull(toInt(4.9));
		$this->assertEquals(0, toInt('somestring'));
		$this->assertNull(toInt([]));
		$this->assertEquals(0, toInt(0));
	}

	public function testSanitizeToEmail() {
		$email = "bob@example.com";
		$this->assertEquals($email, sanitize_to_email($email));
		$this->assertEquals('notanemail', sanitize_to_email("not an email"));
	}

	public function testSanitizeToText() {
		$clean = 'abcdeghijklmnopqrstuvwxyz';
		$dirty = 'abcdegh*ijklmnopqrstuvwxyz';
		$this->assertEquals($clean, sanitize_to_text($clean));
		$this->assertNotEquals($dirty, sanitize_to_text($dirty));
	}

	public function testRestrictToPositive() {
		$this->assertEquals('bob', restrict_to('bob',['bob','joe','john','jake'],'grace'));
	}

	public function testRestrictToDefault() {
		$this->assertEquals('grace', restrict_to('bob',['joe','john','jake'],'grace'));
	}
}
