<?php
use model\Base as ModelBase;

class BaseTest extends PHPUnit_Framework_TestCase {

	public function testContructor() 
	{

		$model = new ModelBase();

		$this->assertInstanceOf('\model\Base', $model);

		$this->assertTrue(ModelBase::isInitialized());
	}

	public function testCreate()
	{
		$accountsObject = ModelBase::create('Accounts');

		$this->assertInstanceOf('\BaseObject', $accountsObject);
	}

	public function testQuery()
	{
		$accountsCriteria = ModelBase::query('Accounts');

		$this->assertInstanceOf('\ModelCriteria', $accountsCriteria);
	}
	
	public function testIsObject()
	{
		$model = new ModelBase();

		$this->assertFalse($model->isObject(null));

		$account = ModelBase::create('News');

		$this->assertTrue($model->isObject($account));
	}

	public function testCollectionAndisCollection()
	{
		$model = new ModelBase();

		$this->assertFalse($model->isCollection(null));

		$someCollection = $model->collection(array(1,2,3));

		$this->assertTrue($model->isCollection($someCollection));
	}
}
