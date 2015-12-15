<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/Template.php');

use app\data\Template;


class TestTemplate extends PHPUnit_Framework_TestCase {


	function setUp(){
	}
	
	function tearDown(){
    }

    public function testTemplateCanInstantiate(){
        $template = new Template();
        $this->assertTrue($template instanceof Template);
    }


}

