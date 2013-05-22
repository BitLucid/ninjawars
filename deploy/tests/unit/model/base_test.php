<?php

class Base_Test extends PHPUnit_Framework_TestCase {

	public function testContructor() {
		$this->assertFalse(model\Base::isInitialized());

		$model = new model\Base();

		$this->assertInstanceOf('\model\Base', $model);
		
		$this->assertTrue(model\Base::isInitialized());
	}
	
}