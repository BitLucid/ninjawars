<?php

class Default_Test extends PHPUnit_Extensions_Selenium2TestCase {

	public function setUp()
	{
		$this->setBrowser('firefox');
		
		$this->setBrowserUrl('http://127.0.0.1/');
	}
	
	public function testTitle()
	{
		$this->url('http://127.0.0.1/');

		$pageTitle = $this->byCssSelector('h1');
        $this->assertEquals('It works!', $pageTitle->text());
	}
}