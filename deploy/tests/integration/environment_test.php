<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use Symfony\Component\HttpFoundation\Request; // Just for request created below.
use app\environment\RequestWrapper;


class TestInput extends PHPUnit_Framework_TestCase {

	function setUp(){
        $get = ['id'=>7,'ninja_name'=>5];
        $post = ['hidden_post'=>1, 'some_post_field'=>'Bob'];
        $request = new Request($get, $post);
        RequestWrapper::inject($request); // Pass a request to be used by the in() function!
	}
	
	function tearDown(){
        RequestWrapper::destroy();
    }

    public function testInputWithinEnvironment(){
        $id = in('id');
        $this->assertEquals(7, $id);
        $default_result = in('doesnotexist', 5);
        $this->assertEquals(5, $default_result);
    }

    public function testPostWithinMockedEnvironment(){
        $posted = post('some_post_field');
        $this->assertEquals('Bob', $posted);
        $default = post('blah_doesnt_exist', 7777);
        $this->assertEquals(7777, $default);
    }

}

