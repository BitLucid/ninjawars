<?php namespace model;

/**
 * Base model class
 *
 * @package NinjaWars
 * @category Model
 * @author Taufan Aditya<toopay@taufanaditya.com>
 */

use \Propel;
use \BaseObject;
use \ModelCriteria;

class Base {

	/**
	 * @var bool init state
	 */
	protected static $init = false;

	/**
	 * Check init flag
	 *
	 * @return bool
	 */
	public static function isInitialized()
	{
		return self::$init;
	}

	/**
	 * Factory method to manufacturing the ORM entities
	 *
	 * @param string entity name
	 * @return BaseObject 
	 */
	public static function create($entity = '')
	{
		$entityObject = '\\model\\orm\\'.$entity;

		return new $entityObject;
	}

	/**
	 * Factory method to manufacturing the entity model criteria
	 *
	 * @param string entity name
	 * @return ModelCriteria 
	 */
	public static function query($entity = '')
	{
		$modelCriteria = '\\model\\orm\\'.$entity.'Query';

		return new $modelCriteria;
	}


	/**
	 * Constructor
	 *
	 * Initialize the propel connection 
	 */
	public function __construct()
	{
		if ( ! self::$init) {
			// Setup propel
			Propel::init(CONF_ROOT . 'connection.php');

			// flag init state
			self::$init = true;
		}
	}
}