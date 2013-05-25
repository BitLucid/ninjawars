<?php

class Index_Test extends PHPUnit_Extensions_Selenium2TestCase {

	public function setUp()
	{
		$this->setBrowser('firefox');
		
		// Change below url to your servername, eg : $this->url('http://nw.local/');
		$this->setBrowserUrl('http://nw.local/');
	}
	
	public function testTitle()
	{
		// Change below url to your servername, eg : $this->url('http://nw.local/');
		$this->url('http://nw.local/');

		$this->assertEquals('Live by the Sword - Ninja Wars Web Game', $this->title());
	}
}