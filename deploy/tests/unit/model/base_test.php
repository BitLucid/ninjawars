<?php

class Base_Test extends PHPUnit_Framework_TestCase {

	public function testContructor() 
	{
		$this->assertFalse(model\Base::isInitialized());

		$model = new model\Base();

		$this->assertInstanceOf('\model\Base', $model);

		$this->assertTrue(model\Base::isInitialized());
	}

	public function testCreate()
	{
		$accountsObject = model\Base::create('Accounts');

		$this->assertInstanceOf('\BaseObject', $accountsObject);
	}

	public function testQuery()
	{
		$accountsCriteria = model\Base::query('Accounts');

		$this->assertInstanceOf('\ModelCriteria', $accountsCriteria);
	}
	
}